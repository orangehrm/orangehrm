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

namespace OrangeHRM\LDAP\Exception;

use Exception;
use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\LDAP\Dto\LDAPUser;
use OrangeHRM\Pim\Service\EmployeeService;

class LDAPSyncException extends Exception
{
    /**
     * @return static
     */
    public static function nonUniqueWorkEmail(): self
    {
        return new self('Employee work email is not a unique email');
    }

    /**
     * @return static
     */
    public static function nonUniqueEmployeeId(): self
    {
        return new self('Employee ID is not a unique ID');
    }

    /**
     * @return static
     */
    public static function userDNMaxLength(): self
    {
        return new self('User DN length should not exceed ' . LDAPUser::USER_DN_MAX_LENGTH . ' characters');
    }

    /**
     * @return static
     */
    public static function usernameMinLength(): self
    {
        return new self('Username length should be at least ' . UserService::USERNAME_MIN_LENGTH . ' characters');
    }

    /**
     * @return static
     */
    public static function usernameMaxLength(): self
    {
        return new self('Username length should not exceed ' . UserService::USERNAME_MAX_LENGTH . ' characters');
    }

    /**
     * @return static
     */
    public static function usernameEmpty(string $usernameAttribute): self
    {
        return new self("Username attribute `$usernameAttribute` not exist");
    }

    /**
     * @return static
     */
    public static function firstNameEmpty(string $firstNameAttribute): self
    {
        return new self("First name attribute `$firstNameAttribute` not exist");
    }

    /**
     * @return static
     */
    public static function firstNameMaxLength(): self
    {
        return new self(
            'First name length should not exceed ' . EmployeeService::FIRST_NAME_MAX_LENGTH . ' characters'
        );
    }

    /**
     * @return static
     */
    public static function middleNameMaxLength(): self
    {
        return new self(
            'Middle name length should not exceed ' . EmployeeService::MIDDLE_NAME_MAX_LENGTH . ' characters'
        );
    }

    /**
     * @return static
     */
    public static function lastNameEmpty(string $lastNameAttribute): self
    {
        return new self("Last name attribute `$lastNameAttribute` not exist");
    }

    /**
     * @return static
     */
    public static function lastNameMaxLength(): self
    {
        return new self('Last name length should not exceed ' . EmployeeService::LAST_NAME_MAX_LENGTH . ' characters');
    }

    /**
     * @return static
     */
    public static function employeeIdMaxLength(): self
    {
        return new self(
            'Employee Id length should not exceed ' . EmployeeService::EMPLOYEE_ID_MAX_LENGTH . ' characters'
        );
    }

    /**
     * @return static
     */
    public static function employeeWorkEmailMaxLength(): self
    {
        return new self(
            'Employee work email length should not exceed ' . EmployeeService::WORK_EMAIL_MAX_LENGTH . ' characters'
        );
    }

    /**
     * @return static
     */
    public static function invalidEmployeeWorkEmail(): self
    {
        return new self('Invalid employee work email');
    }
}
