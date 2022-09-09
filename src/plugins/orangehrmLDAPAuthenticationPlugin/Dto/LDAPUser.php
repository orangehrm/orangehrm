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

namespace OrangeHRM\LDAP\Dto;

use Symfony\Component\Ldap\Entry;

class LDAPUser
{
    private string $userDN;
    private string $username;
    private ?string $userUniqueId = null;
    private bool $userEnabled = true;
    private string $firstName;
    private string $middleName = '';
    private string $lastName;
    private ?string $employeeId = null;
    private ?string $workEmail = null;

    private LDAPUserLookupSetting $userLookupSetting;
    private Entry $entry;

    /**
     * @return string
     */
    public function getUserDN(): string
    {
        return $this->userDN;
    }

    /**
     * @param string $userDN
     * @return LDAPUser
     */
    public function setUserDN(string $userDN): LDAPUser
    {
        $this->userDN = $userDN;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return LDAPUser
     */
    public function setUsername(string $username): LDAPUser
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUserUniqueId(): ?string
    {
        return $this->userUniqueId;
    }

    /**
     * @param string|null $userUniqueId
     * @return LDAPUser
     */
    public function setUserUniqueId(?string $userUniqueId): LDAPUser
    {
        $this->userUniqueId = $userUniqueId;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUserEnabled(): bool
    {
        return $this->userEnabled;
    }

    /**
     * @param bool $userEnabled
     * @return LDAPUser
     */
    public function setUserEnabled(bool $userEnabled): LDAPUser
    {
        $this->userEnabled = $userEnabled;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return LDAPUser
     */
    public function setFirstName(string $firstName): LDAPUser
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getMiddleName(): string
    {
        return $this->middleName;
    }

    /**
     * @param string $middleName
     * @return LDAPUser
     */
    public function setMiddleName(string $middleName): LDAPUser
    {
        $this->middleName = $middleName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return LDAPUser
     */
    public function setLastName(string $lastName): LDAPUser
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmployeeId(): ?string
    {
        return $this->employeeId;
    }

    /**
     * @param string|null $employeeId
     * @return LDAPUser
     */
    public function setEmployeeId(?string $employeeId): LDAPUser
    {
        $this->employeeId = $employeeId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getWorkEmail(): ?string
    {
        return $this->workEmail;
    }

    /**
     * @param string|null $workEmail
     * @return LDAPUser
     */
    public function setWorkEmail(?string $workEmail): LDAPUser
    {
        $this->workEmail = $workEmail;
        return $this;
    }

    /**
     * @param LDAPUserLookupSetting $userLookupSetting
     * @return LDAPUser
     */
    public function setUserLookupSetting(LDAPUserLookupSetting $userLookupSetting): LDAPUser
    {
        $this->userLookupSetting = $userLookupSetting;
        return $this;
    }

    /**
     * @param Entry $entry
     * @return LDAPUser
     */
    public function setEntry(Entry $entry): LDAPUser
    {
        $this->entry = $entry;
        return $this;
    }

    /**
     * @return LDAPEmployeeSearchFilterParams|null
     */
    public function getEmployeeSearchFilterParams(): ?LDAPEmployeeSearchFilterParams
    {
        return $this->userLookupSetting
            ->getEmployeeSelectorMapping()
            ->extractAttributeValuesToSearchFilterParam($this->entry);
    }
}
