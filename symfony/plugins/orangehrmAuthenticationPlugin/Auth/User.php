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
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Authentication\Auth;

use OrangeHRM\Framework\Http\Session\Session;
use OrangeHRM\Framework\ServiceContainer;
use OrangeHRM\Framework\Services;

class User
{
    public const IS_AUTHENTICATED = 'user.is_authenticated';
    public const USER_ID = 'user.user_id';
    public const USER_ROLE_ID = 'user.user_role_id';
    public const USER_ROLE_NAME = 'user.user_role_name';
    public const USER_EMPLOYEE_NUMBER = 'user.user_employee_number';

    /**
     * @var null|self
     */
    protected static ?User $instance = null;

    /**
     * @var Session
     */
    protected $session = null;

    private function __construct()
    {
        /** @var Session $session */
        $this->session = ServiceContainer::getContainer()->get(Services::SESSION);
    }

    /**
     * @return static
     */
    public static function getInstance(): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @return Session
     */
    protected function getSession(): Session
    {
        return $this->session;
    }

    /**
     * @param string $name
     * @param $value
     */
    public function setAttribute(string $name, $value): void
    {
        $this->getSession()->set($name, $value);
    }

    /**
     * @param string $name
     * @param null $default
     * @return mixed
     */
    public function getAttribute(string $name, $default = null)
    {
        return $this->getSession()->get($name, $default);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasAttribute(string $name): bool
    {
        return $this->getSession()->has($name);
    }

    /**
     * @return array
     */
    public function getAllAttributes(): array
    {
        return $this->getSession()->all();
    }

    /**
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return $this->getAttribute(self::IS_AUTHENTICATED, false);
    }

    /**
     * @param bool $status
     */
    public function setIsAuthenticated(bool $status = true): void
    {
        $this->setAttribute(self::IS_AUTHENTICATED, $status);
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->getAttribute(self::USER_ID);
    }

    /**
     * @param int|null $userId
     */
    public function setUserId(?int $userId): void
    {
        $this->setAttribute(self::USER_ID, $userId);
    }

    /**
     * @return int|null
     */
    public function getUserRoleId(): ?int
    {
        return $this->getAttribute(self::USER_ROLE_ID);
    }

    /**
     * @param int|null $userRoleId
     */
    public function setUserRoleId(?int $userRoleId): void
    {
        $this->setAttribute(self::USER_ROLE_ID, $userRoleId);
    }

    /**
     * @return string|null
     */
    public function getUserRoleName(): ?string
    {
        return $this->getAttribute(self::USER_ROLE_NAME);
    }

    /**
     * @param string|null $userRoleName
     */
    public function setUserRoleName(?string $userRoleName): void
    {
        $this->setAttribute(self::USER_ROLE_NAME, $userRoleName);
    }

    /**
     * @return int|null
     */
    public function getEmpNumber(): ?int
    {
        return $this->getAttribute(self::USER_EMPLOYEE_NUMBER);
    }

    /**
     * @param int|null $empNumber
     */
    public function setEmpNumber(?int $empNumber): void
    {
        $this->setAttribute(self::USER_EMPLOYEE_NUMBER, $empNumber);
    }
}
