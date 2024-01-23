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

namespace OrangeHRM\Authentication\Auth;

use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Http\Session\Session;
use OrangeHRM\Framework\Services;

class User
{
    use ServiceContainerTrait;

    public const SESSION_TIMEOUT_REDIRECT_URL = 'redirect_uri';
    public const IS_AUTHENTICATED = 'user.is_authenticated';
    public const HAS_ADMIN_ACCESS = 'user.has_admin_access';
    public const ADMIN_ACCESS_FORWARD_URL = 'admin_access.forward_url';
    public const ADMIN_ACCESS_BACK_URL = 'admin_access.back_url';
    public const USER_ID = 'user.user_id';
    public const USER_ROLE_ID = 'user.user_role_id';
    public const USER_ROLE_NAME = 'user.user_role_name';
    public const USER_EMPLOYEE_NUMBER = 'user.user_employee_number';
    public const OPENID_PROVIDER_ID = 'openid.provider_id';

    public const FLASH_LOGIN_ERROR = 'flash.login_error';
    public const FLASH_PASSWORD_ENFORCE_ERROR = 'flash.password_enforce_error';
    public const FLASH_VERIFY_ERROR = 'flash.admin_access.verify_error';

    /**
     * @var null|self
     */
    protected static ?User $instance = null;

    private function __construct()
    {
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
        return $this->getContainer()->get(Services::SESSION);
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
     * @param string $name
     * @return mixed
     */
    public function removeAttribute(string $name)
    {
        return $this->getSession()->remove($name);
    }

    /**
     * @return array
     */
    public function getAllAttributes(): array
    {
        return $this->getSession()->all();
    }

    /**
     * @param string $type
     * @param mixed $message
     */
    public function addFlash(string $type, $message): void
    {
        $this->getSession()->getFlashBag()->add($type, $message);
    }

    /**
     * @param string $type
     * @param array $default
     * @return array
     */
    public function getFlash(string $type, array $default = []): array
    {
        return $this->getSession()->getFlashBag()->get($type, $default);
    }

    /**
     * @param string $type
     * @return bool
     */
    public function hasFlash(string $type): bool
    {
        return $this->getSession()->getFlashBag()->has($type);
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
     * @internal
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
     * @internal
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
     * @internal
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
     * @internal
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
     * @internal
     */
    public function setEmpNumber(?int $empNumber): void
    {
        $this->setAttribute(self::USER_EMPLOYEE_NUMBER, $empNumber);
    }

    /**
     * @return bool
     */
    public function getHasAdminAccess(): bool
    {
        return $this->getAttribute(self::HAS_ADMIN_ACCESS, false);
    }

    /**
     * @param bool $status
     */
    public function setHasAdminAccess(bool $status = true): void
    {
        $this->setAttribute(self::HAS_ADMIN_ACCESS, $status);
    }
}
