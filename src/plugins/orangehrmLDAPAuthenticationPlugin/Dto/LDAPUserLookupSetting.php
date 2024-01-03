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

namespace OrangeHRM\LDAP\Dto;

use InvalidArgumentException;
use Symfony\Component\Ldap\Adapter\QueryInterface;

class LDAPUserLookupSetting
{
    private string $baseDN;
    private string $searchScope = QueryInterface::SCOPE_SUB;

    private string $userNameAttribute = 'cn';
    private ?string $userUniqueIdAttribute = 'entryUUID';
    private string $userSearchFilter = '(objectClass=inetOrgPerson)';
    private LDAPEmployeeSelectorMapping $employeeSelectorMapping;

    /**
     * @param string $baseDN
     */
    public function __construct(string $baseDN)
    {
        $this->baseDN = $baseDN;
        $this->employeeSelectorMapping = new LDAPEmployeeSelectorMapping();
    }

    /**
     * @return string
     */
    public function getBaseDN(): string
    {
        return $this->baseDN;
    }

    /**
     * @return string
     */
    public function getSearchScope(): string
    {
        return $this->searchScope;
    }

    /**
     * @param string $searchScope
     * @return LDAPUserLookupSetting
     */
    public function setSearchScope(string $searchScope): LDAPUserLookupSetting
    {
        if (!in_array($searchScope, [QueryInterface::SCOPE_SUB, QueryInterface::SCOPE_ONE])) {
            throw new InvalidArgumentException("Invalid search scope: `$searchScope`");
        }
        $this->searchScope = $searchScope;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserNameAttribute(): string
    {
        return $this->userNameAttribute;
    }

    /**
     * @param string $userNameAttribute
     * @return LDAPUserLookupSetting
     */
    public function setUserNameAttribute(string $userNameAttribute): LDAPUserLookupSetting
    {
        $this->userNameAttribute = $userNameAttribute;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUserUniqueIdAttribute(): ?string
    {
        return $this->userUniqueIdAttribute;
    }

    /**
     * @param string|null $userUniqueIdAttribute
     * @return LDAPUserLookupSetting
     */
    public function setUserUniqueIdAttribute(?string $userUniqueIdAttribute): LDAPUserLookupSetting
    {
        $this->userUniqueIdAttribute = $userUniqueIdAttribute;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserSearchFilter(): string
    {
        return $this->userSearchFilter;
    }

    /**
     * @param string $userSearchFilter
     * @return LDAPUserLookupSetting
     */
    public function setUserSearchFilter(string $userSearchFilter): LDAPUserLookupSetting
    {
        $this->userSearchFilter = $userSearchFilter;
        return $this;
    }

    /**
     * @return LDAPEmployeeSelectorMapping
     */
    public function getEmployeeSelectorMapping(): LDAPEmployeeSelectorMapping
    {
        return $this->employeeSelectorMapping;
    }

    /**
     * @param LDAPEmployeeSelectorMapping $employeeSelectorMapping
     * @return LDAPUserLookupSetting
     */
    public function setEmployeeSelectorMapping(
        LDAPEmployeeSelectorMapping $employeeSelectorMapping
    ): LDAPUserLookupSetting {
        $this->employeeSelectorMapping = $employeeSelectorMapping;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'baseDN' => $this->getBaseDN(),
            'searchScope' => $this->getSearchScope(),
            'userNameAttribute' => $this->getUserNameAttribute(),
            'userUniqueIdAttribute' => $this->getUserUniqueIdAttribute(),
            'userSearchFilter' => $this->getUserSearchFilter(),
            'employeeSelectorMapping' => $this->getEmployeeSelectorMapping()->toArray(),
        ];
    }

    /**
     * @param array $userLookupSetting
     * @return static
     */
    public static function createFromArray(array $userLookupSetting): self
    {
        $lookupSetting = new LDAPUserLookupSetting($userLookupSetting['baseDN']);
        $lookupSetting->setSearchScope($userLookupSetting['searchScope'] ?? QueryInterface::SCOPE_SUB);
        $lookupSetting->setUserNameAttribute($userLookupSetting['userNameAttribute'] ?? 'cn');
        $lookupSetting->setUserUniqueIdAttribute($userLookupSetting['userUniqueIdAttribute'] ?? null);
        $lookupSetting->setUserSearchFilter($userLookupSetting['userSearchFilter'] ?? '(objectClass=inetOrgPerson)');
        $lookupSetting->setEmployeeSelectorMapping(
            LDAPEmployeeSelectorMapping::createFromArray($userLookupSetting['employeeSelectorMapping'] ?? [])
        );
        return $lookupSetting;
    }
}
