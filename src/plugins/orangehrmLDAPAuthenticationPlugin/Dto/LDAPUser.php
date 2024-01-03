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

use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Core\Api\V2\Validator\Rules\Email;
use OrangeHRM\Core\Traits\Service\TextHelperTrait;
use OrangeHRM\LDAP\Exception\LDAPSyncException;
use OrangeHRM\LDAP\Traits\LDAPLoggerTrait;
use OrangeHRM\Pim\Service\EmployeeService;
use Symfony\Component\Ldap\Entry;

class LDAPUser
{
    use TextHelperTrait;
    use LDAPLoggerTrait;

    public const USER_DN_MAX_LENGTH = 255;
    public const USER_UNIQUE_ID_MAX_LENGTH = 255;

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
        if ($this->getTextHelper()->strLength($userDN) > self::USER_DN_MAX_LENGTH) {
            throw LDAPSyncException::userDNMaxLength();
        }
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
        $usernameLength = $this->getTextHelper()->strLength($username);
        if ($usernameLength < UserService::USERNAME_MIN_LENGTH) {
            throw LDAPSyncException::usernameMinLength();
        }
        if ($usernameLength > UserService::USERNAME_MAX_LENGTH) {
            throw LDAPSyncException::usernameMaxLength();
        }
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
        if ($userUniqueId === null) {
            $this->userUniqueId = $userUniqueId;
            return $this;
        }
        if ($this->getTextHelper()->strLength($userUniqueId) > self::USER_UNIQUE_ID_MAX_LENGTH) {
            $this->getLogger()->warning(
                'User unique id length should not exceed ' . self::USER_UNIQUE_ID_MAX_LENGTH . ' characters'
            );
            $this->userUniqueId = null;
            return $this;
        }
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
        if ($this->getTextHelper()->strLength($firstName) > EmployeeService::FIRST_NAME_MAX_LENGTH) {
            throw LDAPSyncException::firstNameMaxLength();
        }
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
        if ($this->getTextHelper()->strLength($middleName) > EmployeeService::MIDDLE_NAME_MAX_LENGTH) {
            throw LDAPSyncException::middleNameMaxLength();
        }
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
        if ($this->getTextHelper()->strLength($lastName) > EmployeeService::LAST_NAME_MAX_LENGTH) {
            throw LDAPSyncException::lastNameMaxLength();
        }
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
        if ($employeeId === null) {
            $this->employeeId = $employeeId;
            return $this;
        }
        if ($this->getTextHelper()->strLength($employeeId) > EmployeeService::EMPLOYEE_ID_MAX_LENGTH) {
            throw LDAPSyncException::employeeIdMaxLength();
        }
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
        if ($workEmail === null) {
            $this->workEmail = $workEmail;
            return $this;
        }
        if ($this->getTextHelper()->strLength($workEmail) > EmployeeService::WORK_EMAIL_MAX_LENGTH) {
            throw LDAPSyncException::employeeWorkEmailMaxLength();
        }
        $rule = new Email();
        if (!$rule->validate($workEmail)) {
            throw LDAPSyncException::invalidEmployeeWorkEmail();
        }
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

    /**
     * @return array
     */
    public function getLogInfo(): array
    {
        return [$this->entry->getDn(), $this->entry->getAttributes()];
    }
}
