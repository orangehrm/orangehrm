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

class LDAPUserDataMapping
{
    private string $firstNameAttribute = 'givenName';
    private ?string $middleNameAttribute = null;
    private string $lastNameAttribute = 'sn';
    private ?string $employeeIdAttribute = null;
    private ?string $workEmailAttribute = null;
    private ?string $userStatusAttribute = null;

    /**
     * @return string
     */
    public function getFirstNameAttribute(): string
    {
        return $this->firstNameAttribute;
    }

    /**
     * @param string $firstNameAttribute
     * @return LDAPUserDataMapping
     */
    public function setFirstNameAttribute(string $firstNameAttribute): LDAPUserDataMapping
    {
        $this->firstNameAttribute = $firstNameAttribute;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMiddleNameAttribute(): ?string
    {
        return $this->middleNameAttribute;
    }

    /**
     * @param string|null $middleNameAttribute
     * @return LDAPUserDataMapping
     */
    public function setMiddleNameAttribute(?string $middleNameAttribute): LDAPUserDataMapping
    {
        $this->middleNameAttribute = $middleNameAttribute;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastNameAttribute(): string
    {
        return $this->lastNameAttribute;
    }

    /**
     * @param string $lastNameAttribute
     * @return LDAPUserDataMapping
     */
    public function setLastNameAttribute(string $lastNameAttribute): LDAPUserDataMapping
    {
        $this->lastNameAttribute = $lastNameAttribute;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmployeeIdAttribute(): ?string
    {
        return $this->employeeIdAttribute;
    }

    /**
     * @param string|null $employeeIdAttribute
     * @return LDAPUserDataMapping
     */
    public function setEmployeeIdAttribute(?string $employeeIdAttribute): LDAPUserDataMapping
    {
        $this->employeeIdAttribute = $employeeIdAttribute;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getWorkEmailAttribute(): ?string
    {
        return $this->workEmailAttribute;
    }

    /**
     * @param string|null $workEmailAttribute
     * @return LDAPUserDataMapping
     */
    public function setWorkEmailAttribute(?string $workEmailAttribute): LDAPUserDataMapping
    {
        $this->workEmailAttribute = $workEmailAttribute;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUserStatusAttribute(): ?string
    {
        return $this->userStatusAttribute;
    }

    /**
     * @param string|null $userStatusAttribute
     * @return LDAPUserDataMapping
     */
    public function setUserStatusAttribute(?string $userStatusAttribute): LDAPUserDataMapping
    {
        $this->userStatusAttribute = $userStatusAttribute;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'firstName' => $this->getFirstNameAttribute(),
            'middleName' => $this->getMiddleNameAttribute(),
            'lastName' => $this->getLastNameAttribute(),
            'workEmail' => $this->getWorkEmailAttribute(),
            'employeeId' => $this->getEmployeeIdAttribute(),
            'userStatus' => $this->getUserStatusAttribute(),
        ];
    }

    /**
     * @param array $dataMapping
     */
    public function setAttributeNames(array $dataMapping): void
    {
        $this->setFirstNameAttribute($dataMapping['firstName']);
        $this->setMiddleNameAttribute($dataMapping['middleName'] ?? null);
        $this->setLastNameAttribute($dataMapping['lastName']);
        $this->setWorkEmailAttribute($dataMapping['workEmail'] ?? null);
        $this->setEmployeeIdAttribute($dataMapping['employeeId'] ?? null);
        $this->setUserStatusAttribute($dataMapping['userStatus'] ?? null);
    }
}
