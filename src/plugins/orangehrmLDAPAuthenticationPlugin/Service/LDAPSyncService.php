<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA 02110-1301, USA
 */

namespace OrangeHRM\LDAP\Service;

use OrangeHRM\Admin\Traits\Service\UserServiceTrait;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Core\Traits\LoggerTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerTrait;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
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
use Symfony\Component\Ldap\Adapter\CollectionInterface;
use Symfony\Component\Ldap\Entry;
use Throwable;

use function md5;
use function serialize;

class LDAPSyncService
{
    use ConfigServiceTrait;
    use LoggerTrait;
    use EntityManagerTrait;
    use UserServiceTrait;

    private ?LDAPService $ldapService = null;
    private ?LDAPSetting $ldapSetting = null;
    private LDAPDao $ldapDao;

    /**
     * @return LDAPDao
     */
    protected function getLdapDao(): LDAPDao
    {
        return $this->ldapDao ??= new LDAPDao();
    }

    /**
     * @return LDAPService
     */
    protected function getLdapService(): LDAPService
    {
        if (!$this->ldapService instanceof LDAPService) {
            $this->ldapService = new LDAPService();
            $bindCredentials = new UserCredential();
            if (!$this->getLdapSetting()->isBindAnonymously()) {
                $bindCredentials->setUsername($this->getLdapSetting()->getBindUserDN());
                $bindCredentials->setPassword($this->getLdapSetting()->getBindUserPassword());
            }
            $this->ldapService->bind($bindCredentials);
        }
        return $this->ldapService;
    }

    /**
     * @return LDAPSetting
     */
    protected function getLdapSetting(): LDAPSetting
    {
        if (!$this->ldapSetting instanceof LDAPSetting) {
            $this->ldapSetting = $this->getConfigService()->getLDAPSetting();
        }
        return $this->ldapSetting;
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
                } else {
                    $ldapUsers[$username] = $ldapUser;
                }
            }
        }
        foreach ($duplicateUsernames as $duplicateUsername) {
            $usersOfDuplicateUsernames[$duplicateUsername][] = $ldapUsers[$duplicateUsername];
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
     * @todo refactor
     */
    public function createSystemUsers(array $ldapUsers): void
    {
        $defaultUserRole = $this->getUserService()->getUserRole('ESS');
        foreach ($ldapUsers as $ldapUser) {
            $user = $this->getLdapDao()->getUserByUserName($ldapUser->getUsername());

            if ($user instanceof User) {
                $ldapAuthProvider = $this->getLDAPAuthProvider($user->getAuthProviders());
                if ($ldapAuthProvider instanceof UserAuthProvider) {
                    if ($ldapAuthProvider->getLdapUserHash() === $this->getHashOfLDAPUser($ldapUser)) {
                        continue;
                    }

                    $employee = $user->getEmployee();
                    $employee->setFirstName($ldapUser->getFirstName());
                    $employee->setLastName($ldapUser->getLastName());
                    $employee->setMiddleName($ldapUser->getMiddleName());
                    $employee->setEmployeeId($ldapUser->getEmployeeId());
                    $employee->setWorkEmail($ldapUser->getWorkEmail()); // TODO:: check unique email
                    $user->setStatus($ldapUser->isUserEnabled());
                    //$user->setDateModified(); TODO
                    //$user->setModifiedUserId();

                    // Change user data
                    $ldapAuthProvider->setLdapUserDN($ldapUser->getUserDN());
                    $ldapAuthProvider->setLdapUserUniqueId($ldapUser->getUserUniqueId());
                    $ldapAuthProvider->setLdapUserHash($this->getHashOfLDAPUser($ldapUser));

                    // TODO:: save $user
                    $this->getEntityManager()->persist($user);
                    // TODO:: save $employee
                    $this->getEntityManager()->persist($employee);
                    // TODO:: save $ldapAuthProvider
                    $this->getEntityManager()->persist($ldapAuthProvider);
                    $this->getEntityManager()->flush();
                } else { // TODO:: elseif check setting to link ldap user
                    // TODO:: check employees who have multiple users
                    // local auth, may be skipped
                    $user->setStatus($ldapUser->isUserEnabled());
                    //$user->setDateModified(); TODO
                    //$user->setModifiedUserId();

                    $employee = $user->getEmployee();
                    $employee->setFirstName($ldapUser->getFirstName());
                    $employee->setLastName($ldapUser->getLastName());
                    $employee->setMiddleName($ldapUser->getMiddleName());
                    $employee->setEmployeeId($ldapUser->getEmployeeId());
                    $employee->setWorkEmail($ldapUser->getWorkEmail()); // TODO:: check unique email

                    $authProvider = new UserAuthProvider();
                    $authProvider->setUser($user);
                    $authProvider->setType(UserAuthProvider::TYPE_LDAP);
                    $authProvider->setLdapUserDN($ldapUser->getUserDN());
                    $authProvider->setLdapUserUniqueId($ldapUser->getUserUniqueId());
                    $authProvider->setLdapUserHash($this->getHashOfLDAPUser($ldapUser));

                    // TODO:: save $user
                    $this->getEntityManager()->persist($user);
                    // TODO:: save $employee
                    $this->getEntityManager()->persist($employee);
                    // TODO:: save $ldapAuthProvider
                    $this->getEntityManager()->persist($ldapAuthProvider);
                    $this->getEntityManager()->flush();
                    // TODO:: check/handle empty $user->getUserPassword()
                }
            } else {
                // try to find a user who have user unique id
                if ($ldapUser->getUserUniqueId() !== null) {
                    $ldapAuthProvider = $this->getLdapDao()
                        ->getAuthProviderByUserUniqueId($ldapUser->getUserUniqueId());
                    if ($ldapAuthProvider instanceof UserAuthProvider) {
                        $user = $ldapAuthProvider->getUser();
                        $user->setUserName($ldapUser->getUsername());
                        $user->setStatus($ldapUser->isUserEnabled());
                        //$user->setDateModified(); TODO
                        //$user->setModifiedUserId();

                        $employee = $user->getEmployee();
                        $employee->setFirstName($ldapUser->getFirstName());
                        $employee->setLastName($ldapUser->getLastName());
                        $employee->setMiddleName($ldapUser->getMiddleName());
                        $employee->setEmployeeId($ldapUser->getEmployeeId());
                        $employee->setWorkEmail($ldapUser->getWorkEmail()); // TODO:: check unique email

                        // Change user data
                        $ldapAuthProvider->setLdapUserDN($ldapUser->getUserDN());
                        $ldapAuthProvider->setLdapUserHash($this->getHashOfLDAPUser($ldapUser));

                        // TODO:: save $user
                        $this->getEntityManager()->persist($user);
                        // TODO:: save $employee
                        $this->getEntityManager()->persist($employee);
                        // TODO:: save $ldapAuthProvider
                        $this->getEntityManager()->persist($ldapAuthProvider);
                        $this->getEntityManager()->flush();

                        continue;
                    }
                }

                $employee = null;
                $employeeSearchFilterParams = $ldapUser->getEmployeeSearchFilterParams();
                if ($employeeSearchFilterParams instanceof LDAPEmployeeSearchFilterParams) {
                    $employee = $this->getLdapDao()->getEmployee($employeeSearchFilterParams);
                }

                // Create a new user if not found the employee for given mapping configurations
                $employee = $employee ?? new Employee(); // TODO:: get employee using mapper
                $employee->setFirstName($ldapUser->getFirstName());
                $employee->setLastName($ldapUser->getLastName());
                $employee->setMiddleName($ldapUser->getMiddleName());
                $employee->setEmployeeId($ldapUser->getEmployeeId());
                $employee->setWorkEmail($ldapUser->getWorkEmail()); // TODO:: check unique email

                $user = new User();
                $user->setUserName($ldapUser->getUsername());
                $user->setStatus($ldapUser->isUserEnabled());
                $user->setEmployee($employee);
                //$user->setDateEntered(); TODO
                //$user->setCreatedBy();
                $user->setUserRole($defaultUserRole);

                $authProvider = new UserAuthProvider();
                $authProvider->setUser($user);
                $authProvider->setType(UserAuthProvider::TYPE_LDAP);
                $authProvider->setLdapUserDN($ldapUser->getUserDN());
                $authProvider->setLdapUserUniqueId($ldapUser->getUserUniqueId());
                $authProvider->setLdapUserHash($this->getHashOfLDAPUser($ldapUser));

                // TODO:: save $authProvider
                $this->getEntityManager()->persist($employee);
                $this->getEntityManager()->persist($user);
                $this->getEntityManager()->persist($authProvider);
                $this->getEntityManager()->flush();
            }
        }
        // TODO:: soft delete LDAP users who removed from the server
    }

    /**
     * @param UserAuthProvider[] $authProviders
     */
    public function getLDAPAuthProvider(iterable $authProviders): ?UserAuthProvider
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
        // begin transaction

        // commit transaction
    }

    /**
     * @return EntryCollection
     */
    public function fetchEntryCollections(): EntryCollection
    {
        $entryCollectionLookupSettingPairArray = [];
        foreach ($this->getLdapSetting()->getUserLookupSettings() as $lookupSetting) {
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
        $options = [
            'scope' => $lookupSetting->getSearchScope(),
            'filter' => $this->getSearchAttributes($lookupSetting),
        ];
        $q = $this->getLdapService()->query(
            $lookupSetting->getBaseDN(),
            $lookupSetting->getUserSearchFilter(),
            $options,
        );
        return $q->execute();
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
        $dataMapping = $this->getLdapSetting()->getDataMapping();
        try {
            $username = $this->getAttribute($entry, $lookupSetting->getUserNameAttribute());
            return (new LDAPUser())
                ->setUserDN($entry->getDn())
                ->setUsername($username)
                ->setUserUniqueId($this->getAttribute($entry, $lookupSetting->getUserUniqueIdAttribute()))
                ->setUserEnabled($this->getAttribute($entry, $dataMapping->getUserStatusAttribute()) ?? true)
                ->setFirstName($this->getAttribute($entry, $dataMapping->getFirstNameAttribute()))
                ->setMiddleName($this->getAttribute($entry, $dataMapping->getMiddleNameAttribute()) ?? '')
                ->setLastName($this->getAttribute($entry, $dataMapping->getLastNameAttribute()))
                ->setEmployeeId($this->getAttribute($entry, $dataMapping->getEmployeeIdAttribute()))
                ->setWorkEmail($this->getAttribute($entry, $dataMapping->getWorkEmailAttribute()))
                ->setUserLookupSetting($lookupSetting)
                ->setEntry($entry);
        } catch (Throwable $e) {
            // TODO
            $this->getLogger()->warning($e->getMessage());
            $this->getLogger()->warning($e->getTraceAsString());
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
        $dataMapping = $this->getLdapSetting()->getDataMapping();
        $attributes = array_values(array_filter($dataMapping->toArray()));
        $attributes[] = $lookupSetting->getUserNameAttribute();
        if (!empty($lookupSetting->getUserUniqueIdAttribute())) {
            $attributes[] = $lookupSetting->getUserUniqueIdAttribute();
        }
        $attributes = array_merge($attributes, $lookupSetting->getEmployeeSelectorMapping()->getAllAttributeNames());
        return array_unique($attributes);
    }
}
