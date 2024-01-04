<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\LDAP\Service;

use OrangeHRM\Admin\Traits\Service\UserServiceTrait;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Core\Traits\EventDispatcherTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\UserAuthProvider;
use OrangeHRM\LDAP\Dao\LDAPDao;
use OrangeHRM\LDAP\Dto\EntryCollection;
use OrangeHRM\LDAP\Dto\EntryCollectionLookupSettingPair;
use OrangeHRM\LDAP\Dto\LDAPEmployeeSearchFilterParams;
use OrangeHRM\LDAP\Dto\LDAPSetting;
use OrangeHRM\LDAP\Dto\LDAPUser;
use OrangeHRM\LDAP\Dto\LDAPUserCollection;
use OrangeHRM\LDAP\Dto\LDAPUserLookupSetting;
use OrangeHRM\LDAP\Dto\PartialEmployee;
use OrangeHRM\LDAP\Exception\LDAPSettingException;
use OrangeHRM\LDAP\Exception\LDAPSyncException;
use OrangeHRM\LDAP\Traits\LDAPLoggerTrait;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\Pim\Event\EmployeeEvents;
use OrangeHRM\Pim\Event\EmployeeSavedEvent;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;
use Symfony\Component\Ldap\Adapter\CollectionInterface;
use Symfony\Component\Ldap\Entry;
use Throwable;

use function md5;
use function serialize;

class LDAPSyncService
{
    use ConfigServiceTrait;
    use EntityManagerHelperTrait;
    use UserServiceTrait;
    use DateTimeHelperTrait;
    use EmployeeServiceTrait;
    use LDAPLoggerTrait;
    use EventDispatcherTrait;

    protected ?LDAPService $ldapService = null;
    protected ?LDAPSetting $ldapSetting = null;
    private LDAPDao $ldapDao;
    private array $empNumbersWhoHaveManyUsers;
    private array $entryCollectionCache = [];

    /**
     * @return LDAPDao
     */
    public function getLDAPDao(): LDAPDao
    {
        return $this->ldapDao ??= new LDAPDao();
    }

    /**
     * @return LDAPService
     */
    protected function getLDAPService(): LDAPService
    {
        if (!$this->ldapService instanceof LDAPService) {
            $this->ldapService = new LDAPService();
            $bindCredentials = new UserCredential();
            if (!$this->getLDAPSetting()->isBindAnonymously()) {
                $bindCredentials->setUsername($this->getLDAPSetting()->getBindUserDN());
                $bindCredentials->setPassword($this->getLDAPSetting()->getBindUserPassword());
            }
            $this->ldapService->bind($bindCredentials);
        }
        return $this->ldapService;
    }

    /**
     * @return LDAPSetting
     */
    protected function getLDAPSetting(): LDAPSetting
    {
        if (!$this->ldapSetting instanceof LDAPSetting) {
            $this->ldapSetting = $this->getConfigService()->getLDAPSetting();
            if ($this->ldapSetting === null) {
                throw new LDAPSettingException('LDAP settings not configured');
            }
        }
        return $this->ldapSetting;
    }

    /**
     * @return int[]
     */
    public function getEmpNumbersWhoHaveManyUsers(): array
    {
        return $this->empNumbersWhoHaveManyUsers ??= $this->getLDAPDao()->getEmpNumbersWhoHaveManyUsers();
    }

    /**
     * @return LDAPUserCollection
     */
    public function fetchAllLDAPUsers(): LDAPUserCollection
    {
        $ldapUsers = [];
        $duplicateUsernames = [];
        $usersOfDuplicateUsernames = [];
        $failedUsers = [];
        foreach ($this->fetchEntryCollections()->getCollections() as $entryCollectionLookupSettingPair) {
            foreach ($entryCollectionLookupSettingPair->getCollection()->toArray() as $entry) {
                $ldapUser = $this->prepareLDAPUserFromEntry(
                    $entry,
                    $entryCollectionLookupSettingPair->getLookupSetting()
                );
                if (is_null($ldapUser)) {
                    $failedUsers[] = $entry->getDn();
                    continue;
                }
                $username = $ldapUser->getUsername();
                if (isset($ldapUsers[$username])) {
                    $duplicateUsernames[] = $username;
                    $usersOfDuplicateUsernames[$username][] = $ldapUser;
                    $this->getLogger()->warning('Duplicate username', $ldapUser->getLogInfo());
                } else {
                    $ldapUsers[$username] = $ldapUser;
                }
            }
        }
        foreach ($duplicateUsernames as $duplicateUsername) {
            $usersOfDuplicateUsernames[$duplicateUsername][] = $ldapUsers[$duplicateUsername];
            $this->getLogger()->warning('Duplicate username', $ldapUsers[$duplicateUsername]->getLogInfo());
            unset($ldapUsers[$duplicateUsername]);
        }

        return new LDAPUserCollection($ldapUsers, $duplicateUsernames, $usersOfDuplicateUsernames, $failedUsers);
    }

    /**
     * @param LDAPUser $ldapUser
     * @return string
     */
    private function getHashOfLDAPUser(LDAPUser $ldapUser): string
    {
        return md5(serialize($ldapUser));
    }

    /**
     * @param LDAPUser[] $ldapUsers
     */
    public function createSystemUsers(array $ldapUsers): void
    {
        $defaultUserRole = $this->getUserService()->getUserRole('ESS');
        foreach ($ldapUsers as $ldapUser) {
            $user = $this->getLDAPDao()->getUserByUserName($ldapUser->getUsername());

            if ($user instanceof User) {
                $ldapAuthProvider = $this->filterLDAPAuthProvider($user->getAuthProviders());
                if ($ldapAuthProvider instanceof UserAuthProvider) {
                    if ($ldapAuthProvider->getLDAPUserHash() === $this->getHashOfLDAPUser($ldapUser)) {
                        continue;
                    }

                    $employee = $user->getEmployee();
                    $clonedEmployee = PartialEmployee::createFromEmployee($employee);
                    $employee->setFirstName($ldapUser->getFirstName());
                    $employee->setLastName($ldapUser->getLastName());
                    $employee->setMiddleName($ldapUser->getMiddleName());
                    $employee->setEmployeeId($ldapUser->getEmployeeId());
                    $employee->setWorkEmail($ldapUser->getWorkEmail());
                    $user->setStatus($ldapUser->isUserEnabled());
                    $user->setDateModified($this->getDateTimeHelper()->getNow());

                    // Change user data
                    $ldapAuthProvider->setLDAPUserDN($ldapUser->getUserDN());
                    $ldapAuthProvider->setLDAPUserUniqueId($ldapUser->getUserUniqueId());
                    $ldapAuthProvider->setLDAPUserHash($this->getHashOfLDAPUser($ldapUser));

                    if ($this->trySaveEmployee($employee, $clonedEmployee, $ldapUser) === null) {
                        continue;
                    }
                    $this->getEntityManager()->persist($user);
                    $this->getEntityManager()->persist($ldapAuthProvider);
                    $this->getEntityManager()->flush();
                    $this->warnMultiUserEmployee($employee);
                } elseif ($this->getLDAPSetting()->shouldMergeLDAPUsersWithExistingSystemUsers()) {
                    $user->setStatus($ldapUser->isUserEnabled());
                    $user->setDateModified($this->getDateTimeHelper()->getNow());

                    $employee = $user->getEmployee();
                    $clonedEmployee = PartialEmployee::createFromEmployee($employee);
                    $employee->setFirstName($ldapUser->getFirstName());
                    $employee->setLastName($ldapUser->getLastName());
                    $employee->setMiddleName($ldapUser->getMiddleName());
                    $employee->setEmployeeId($ldapUser->getEmployeeId());
                    $employee->setWorkEmail($ldapUser->getWorkEmail());

                    $authProvider = new UserAuthProvider();
                    $authProvider->setUser($user);
                    $authProvider->setType(UserAuthProvider::TYPE_LDAP);
                    $authProvider->setLDAPUserDN($ldapUser->getUserDN());
                    $authProvider->setLDAPUserUniqueId($ldapUser->getUserUniqueId());
                    $authProvider->setLDAPUserHash($this->getHashOfLDAPUser($ldapUser));

                    if ($this->trySaveEmployee($employee, $clonedEmployee, $ldapUser) === null) {
                        continue;
                    }
                    $this->getEntityManager()->persist($user);
                    $this->getEntityManager()->persist($authProvider);
                    $this->getEntityManager()->flush();
                    $this->warnMultiUserEmployee($employee);
                } else {
                    $this->getLogger()->warning('Username already exists', $ldapUser->getLogInfo());
                }
            } else {
                // try to find a user who have user unique id
                if ($ldapUser->getUserUniqueId() !== null) {
                    $ldapAuthProvider = $this->getLDAPDao()
                        ->getAuthProviderByUserUniqueId($ldapUser->getUserUniqueId());
                    if ($ldapAuthProvider instanceof UserAuthProvider) {
                        $user = $ldapAuthProvider->getUser();
                        $user->setUserName($ldapUser->getUsername());
                        $user->setStatus($ldapUser->isUserEnabled());
                        $user->setDateModified($this->getDateTimeHelper()->getNow());

                        $employee = $user->getEmployee();
                        $clonedEmployee = PartialEmployee::createFromEmployee($employee);
                        $employee->setFirstName($ldapUser->getFirstName());
                        $employee->setLastName($ldapUser->getLastName());
                        $employee->setMiddleName($ldapUser->getMiddleName());
                        $employee->setEmployeeId($ldapUser->getEmployeeId());
                        $employee->setWorkEmail($ldapUser->getWorkEmail());

                        // Change user data
                        $ldapAuthProvider->setLDAPUserDN($ldapUser->getUserDN());
                        $ldapAuthProvider->setLDAPUserHash($this->getHashOfLDAPUser($ldapUser));

                        if ($this->trySaveEmployee($employee, $clonedEmployee, $ldapUser) === null) {
                            continue;
                        }
                        $this->getEntityManager()->persist($user);
                        $this->getEntityManager()->persist($ldapAuthProvider);
                        $this->getEntityManager()->flush();
                        $this->warnMultiUserEmployee($employee);

                        continue;
                    }
                }

                $employee = null;
                $employeeSearchFilterParams = $ldapUser->getEmployeeSearchFilterParams();
                if ($employeeSearchFilterParams instanceof LDAPEmployeeSearchFilterParams) {
                    $employee = $this->getLDAPDao()->getEmployee($employeeSearchFilterParams);
                }

                // Create a new user if not found the employee for given mapping configurations
                $employee = $employee ?? new Employee();
                $clonedEmployee = PartialEmployee::createFromEmployee($employee);
                $employee->setFirstName($ldapUser->getFirstName());
                $employee->setLastName($ldapUser->getLastName());
                $employee->setMiddleName($ldapUser->getMiddleName());
                $employee->setEmployeeId($ldapUser->getEmployeeId());
                $employee->setWorkEmail($ldapUser->getWorkEmail());

                $user = new User();
                $user->setUserName($ldapUser->getUsername());
                $user->setStatus($ldapUser->isUserEnabled());
                $user->setEmployee($employee);
                $user->setDateEntered($this->getDateTimeHelper()->getNow());
                $user->setUserRole($defaultUserRole);

                $authProvider = new UserAuthProvider();
                $authProvider->setUser($user);
                $authProvider->setType(UserAuthProvider::TYPE_LDAP);
                $authProvider->setLDAPUserDN($ldapUser->getUserDN());
                $authProvider->setLDAPUserUniqueId($ldapUser->getUserUniqueId());
                $authProvider->setLDAPUserHash($this->getHashOfLDAPUser($ldapUser));

                if ($this->trySaveEmployee($employee, $clonedEmployee, $ldapUser) === null) {
                    continue;
                }
                $this->getEntityManager()->persist($user);
                $this->getEntityManager()->persist($authProvider);
                $this->getEntityManager()->flush();
                $this->warnMultiUserEmployee($employee);
            }
        }
    }

    /**
     * @param Employee $employee
     * @param PartialEmployee $clonedEmployee
     * @param LDAPUser $ldapUser
     * @return Employee|null
     */
    private function trySaveEmployee(Employee $employee, PartialEmployee $clonedEmployee, LDAPUser $ldapUser): ?Employee
    {
        try {
            $this->saveEmployee($employee, $clonedEmployee);
            $this->getEventDispatcher()->dispatch(new EmployeeSavedEvent($employee), EmployeeEvents::EMPLOYEE_SAVED);
            return $employee;
        } catch (LDAPSyncException $e) {
            $this->getLogger()->error($e->getMessage(), $ldapUser->getLogInfo());
            return null;
        }
    }

    /**
     * @param Employee $employee
     */
    private function warnMultiUserEmployee(Employee $employee): void
    {
        $empNumber = $employee->getEmpNumber();
        if (in_array($empNumber, $this->getEmpNumbersWhoHaveManyUsers())) {
            $this->getLogger()->warning("Employee Number: `$empNumber`. This employee has many users");
        }
    }

    /**
     * @param Employee $employee
     * @param PartialEmployee $clonedEmployee
     * @throws LDAPSyncException
     */
    private function saveEmployee(Employee $employee, PartialEmployee $clonedEmployee): void
    {
        if ($employee->getWorkEmail() !== null
            && !$this->getEmployeeService()
                ->isUniqueEmail($employee->getWorkEmail(), $clonedEmployee->getWorkEmail())) {
            throw LDAPSyncException::nonUniqueWorkEmail();
        }
        if ($employee->getEmployeeId() !== null
            && !$this->getEmployeeService()
                ->isUniqueEmployeeId($employee->getEmployeeId(), $clonedEmployee->getEmployeeId())) {
            throw LDAPSyncException::nonUniqueEmployeeId();
        }

        $this->getEntityManager()->persist($employee);
    }

    /**
     * @param UserAuthProvider[] $authProviders
     */
    public function filterLDAPAuthProvider(iterable $authProviders): ?UserAuthProvider
    {
        foreach ($authProviders as $authProvider) {
            if ($authProvider->getType() === UserAuthProvider::TYPE_LDAP) {
                return $authProvider;
            }
        }
        return null;
    }

    public function sync(): void
    {
        $this->beginTransaction();
        try {
            $ldapUserCollection = $this->fetchAllLDAPUsers();
            $ldapUsers = $ldapUserCollection->getLDAPUsers();
            $this->createSystemUsers(array_values($ldapUsers));
            $this->deleteLocalUsersWhoRemovedFromLDAPServer($ldapUserCollection);
            $this->commitTransaction();
        } catch (Throwable $e) {
            $this->rollBackTransaction();
            $this->getLogger()->error($e->getMessage());
            $this->getLogger()->error($e->getTraceAsString());
            throw new TransactionException($e);
        }
    }

    /**
     * @return EntryCollection
     */
    public function fetchEntryCollections(): EntryCollection
    {
        $entryCollectionLookupSettingPairArray = [];
        foreach ($this->getLDAPSetting()->getUserLookupSettings() as $lookupSetting) {
            $entryCollectionLookupSettingPairArray[] = new EntryCollectionLookupSettingPair(
                $this->fetchEntryCollection($lookupSetting),
                $lookupSetting
            );
        }
        return new EntryCollection(...$entryCollectionLookupSettingPairArray);
    }

    /**
     * @param LDAPUserLookupSetting $lookupSetting
     * @return CollectionInterface
     */
    private function fetchEntryCollection(LDAPUserLookupSetting $lookupSetting): CollectionInterface
    {
        $hashKey = md5(serialize($lookupSetting));
        if (isset($this->entryCollectionCache[$hashKey])) {
            return $this->entryCollectionCache[$hashKey];
        }

        $options = [
            'scope' => $lookupSetting->getSearchScope(),
            'filter' => $this->getSearchAttributes($lookupSetting),
        ];
        $q = $this->getLDAPService()->query(
            $lookupSetting->getBaseDN(),
            $lookupSetting->getUserSearchFilter(),
            $options,
        );
        return $this->entryCollectionCache[$hashKey] = $q->execute();
    }

    /**
     * @param Entry $entry
     * @param LDAPUserLookupSetting $lookupSetting
     * @return LDAPUser|null
     */
    private function prepareLDAPUserFromEntry(
        Entry $entry,
        LDAPUserLookupSetting $lookupSetting
    ): ?LDAPUser {
        $dataMapping = $this->getLDAPSetting()->getDataMapping();
        try {
            $username = $this->getAttribute($entry, $lookupSetting->getUserNameAttribute());
            if ($username === null) {
                throw LDAPSyncException::usernameEmpty($lookupSetting->getUserNameAttribute());
            }
            $firstName = $this->getAttribute($entry, $dataMapping->getFirstNameAttribute());
            if ($firstName === null) {
                throw LDAPSyncException::firstNameEmpty($dataMapping->getFirstNameAttribute());
            }
            $lastName = $this->getAttribute($entry, $dataMapping->getLastNameAttribute());
            if ($lastName === null) {
                throw LDAPSyncException::lastNameEmpty($dataMapping->getLastNameAttribute());
            }
            return (new LDAPUser())
                ->setUserDN($entry->getDn())
                ->setUsername($username)
                ->setUserUniqueId($this->getAttribute($entry, $lookupSetting->getUserUniqueIdAttribute()))
                ->setUserEnabled($this->getAttribute($entry, $dataMapping->getUserStatusAttribute()) ?? true)
                ->setFirstName($firstName)
                ->setMiddleName($this->getAttribute($entry, $dataMapping->getMiddleNameAttribute()) ?? '')
                ->setLastName($lastName)
                ->setEmployeeId($this->getAttribute($entry, $dataMapping->getEmployeeIdAttribute()))
                ->setWorkEmail($this->getAttribute($entry, $dataMapping->getWorkEmailAttribute()))
                ->setUserLookupSetting($lookupSetting)
                ->setEntry($entry);
        } catch (Throwable $e) {
            $this->getLogger()->error($e->getMessage(), [$entry->getDn(), $entry->getAttributes()]);
            return null;
        }
    }

    /**
     * @param Entry $entry
     * @param string|null $attributeName
     * @return string|null
     */
    private function getAttribute(Entry $entry, ?string $attributeName): ?string
    {
        if (empty($attributeName)) {
            return null;
        }
        return $entry->getAttribute($attributeName)[0] ?? null;
    }

    /**
     * @param LDAPUserLookupSetting $lookupSetting
     * @return string[]
     */
    private function getSearchAttributes(LDAPUserLookupSetting $lookupSetting): array
    {
        $dataMapping = $this->getLDAPSetting()->getDataMapping();
        $attributes = array_values(array_filter($dataMapping->toArray()));
        $attributes[] = $lookupSetting->getUserNameAttribute();
        if (!empty($lookupSetting->getUserUniqueIdAttribute())) {
            $attributes[] = $lookupSetting->getUserUniqueIdAttribute();
        }
        $attributes = array_merge($attributes, $lookupSetting->getEmployeeSelectorMapping()->getAllAttributeNames());
        return array_values(array_unique($attributes));
    }

    /**
     * @param LDAPUserCollection $ldapUserCollection
     */
    public function deleteLocalUsersWhoRemovedFromLDAPServer(LDAPUserCollection $ldapUserCollection): void
    {
        $ldapAuthProviders = [];
        foreach ($this->getLDAPDao()->getAllLDAPAuthProviders() as $authProvider) {
            $ldapAuthProviders[$authProvider->getUserDN()] = $authProvider->getUserId();
        }

        foreach ($ldapUserCollection->getLDAPUsers() as $ldapUser) {
            unset($ldapAuthProviders[$ldapUser->getUserDN()]);
        }
        foreach ($ldapUserCollection->getFailedUsers() as $failedUserDNs) {
            unset($ldapAuthProviders[$failedUserDNs]);
        }
        foreach ($ldapUserCollection->getUsersOfDuplicateUsernames() as $duplicateUsers) {
            foreach ($duplicateUsers as $ldapUser) {
                unset($ldapAuthProviders[$ldapUser->getUserDN()]);
            }
        }

        $this->getUserService()
            ->geUserDao()
            ->deleteSystemUsers(array_values($ldapAuthProviders));
    }
}
