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

use OrangeHRM\Framework\ServiceContainer;
use OrangeHRM\Framework\Services;
use Symfony\Component\HttpFoundation\Session\Session;

class User
{
    public const IS_AUTHENTICATED = 'user.is_authenticated';
    /**
     * @var null|self
     */
    protected static $userInstance = null;

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
        if (is_null(self::$userInstance)) {
            self::$userInstance = new self();
        }
        return self::$userInstance;
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
}
