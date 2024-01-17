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

namespace OrangeHRM\Authentication\Exception;

use Exception;
use OrangeHRM\I18N\Traits\Service\I18NHelperTrait;

class AuthenticationException extends Exception
{
    use I18NHelperTrait;
    public const EMPLOYEE_NOT_ASSIGNED = 'employee_not_assigned';
    public const EMPLOYEE_TERMINATED = 'employee_terminated';
    public const USER_DISABLED = 'user_disabled';
    public const INVALID_CREDENTIALS = 'invalid_credentials';
    public const INVALID_CSRF_TOKEN = 'invalid_csrf_token';
    public const UNEXPECT_ERROR = 'unexpected_error';
    public const PASSWORD_NOT_STRONG = 'password_not_strong';
    public const INVALID_RESET_CODE = 'invalid_password_reset_code';
    public const NO_USER_FOUND = 'no_user_found';
    public const MULTIPLE_USER_RETURNED = 'multiple_user_returned';

    /**
     * @var string
     */
    private string $name;

    /**
     * @param string $name
     * @param string $message
     */
    protected function __construct(string $name, string $message)
    {
        $this->name = $name;
        parent::__construct($message);
    }

    /**
     * @return array
     */
    public function normalize(): array
    {
        return [
            'error' => $this->name,
            'message' => $this->getI18NHelper()->transBySource($this->message),
        ];
    }

    /**
     * @return static
     */
    public static function employeeNotAssigned(): self
    {
        return new self(self::EMPLOYEE_NOT_ASSIGNED, 'Employee not assigned');
    }

    /**
     * @return static
     */
    public static function employeeTerminated(): self
    {
        return new self(self::EMPLOYEE_TERMINATED, 'Employee is terminated');
    }

    /**
     * @return static
     */
    public static function userDisabled(): self
    {
        return new self(self::USER_DISABLED, 'Account disabled');
    }

    /**
     * @return static
     */
    public static function invalidCredentials(): self
    {
        return new self(self::INVALID_CREDENTIALS, 'Invalid credentials');
    }

    /**
     * @return static
     */
    public static function invalidCsrfToken(): self
    {
        return new self(self::INVALID_CSRF_TOKEN, 'CSRF token validation failed');
    }

    /**
     * @return static
     */
    public static function noUserFound(): self
    {
        return new self(self::NO_USER_FOUND, 'No User Found');
    }

    /**
     * @return static
     */
    public static function multipleUserReturned(): self
    {
        return new self(self::MULTIPLE_USER_RETURNED, 'Multiple User Returned');
    }
}
