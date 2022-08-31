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

use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Core\Traits\LoggerTrait;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\LDAP\Dto\EntryCollection;
use OrangeHRM\LDAP\Dto\EntryCollectionLookupSettingPair;
use OrangeHRM\LDAP\Dto\LDAPSetting;
use OrangeHRM\LDAP\Dto\LDAPUser;
use OrangeHRM\LDAP\Dto\LDAPUserCollection;
use OrangeHRM\LDAP\Dto\LDAPUserLookupSetting;
use Symfony\Component\Ldap\Adapter\CollectionInterface;
use Symfony\Component\Ldap\Entry;
use Throwable;

class LDAPSyncService
{
    use ConfigServiceTrait;
    use LoggerTrait;

    private string $ldapServiceClass;
    private ?LDAPService $ldapService = null;
    private ?LDAPSetting $ldapSetting = null;

    /**
     * @param string $ldapServiceClass
     */
    public function __construct(string $ldapServiceClass = LDAPService::class)
    {
        $this->ldapServiceClass = $ldapServiceClass;
    }

    /**
     * @return LDAPService
     */
    private function getLdapService(): LDAPService
    {
        if (!$this->ldapService instanceof $this->ldapServiceClass) {
            $this->ldapService = new $this->ldapServiceClass();
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
                ->setWorkEmail($this->getAttribute($entry, $dataMapping->getWorkEmailAttribute()));
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
        return $attributes;
    }
}
