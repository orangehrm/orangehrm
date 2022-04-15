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

namespace OrangeHRM\Installer\Util;

use OrangeHRM\Framework\Http\Session\Session;
use OrangeHRM\Framework\ServiceContainer;
use OrangeHRM\Framework\Services;

class StateContainer
{
    public const DB_PORT = 'dbPort';
    public const DB_USER = 'dbUser';
    public const DB_NAME = 'dbName';
    public const DB_HOST = 'dbHost';
    public const DB_PASSWORD = 'dbPass';

    /**
     * @var null|self
     */
    protected static ?self $instance = null;

    /**
     * @return Session
     */
    protected function getSession(): Session
    {
        return ServiceContainer::getContainer()->get(Services::SESSION);
    }

    private function __construct()
    {
    }

    /**
     * @return self
     */
    public static function getInstance(): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
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
     * @param string $dbHost
     * @param string $dbPort
     * @param string $dbUser
     * @param string $dbPassword
     * @param string $dbName
     */
    public function storeDbInfo(
        string $dbHost,
        string $dbPort,
        string $dbUser,
        string $dbPassword,
        string $dbName
    ): void {
        $this->getSession()->set(self::DB_NAME, $dbName);
        $this->getSession()->set(self::DB_USER, $dbUser);
        $this->getSession()->set(self::DB_PASSWORD, $dbPassword);
        $this->getSession()->set(self::DB_HOST, $dbHost);
        $this->getSession()->set(self::DB_PORT, $dbPort);
    }

    /***
     * @return array
     */
    public function getDbInfo(): array
    {
        return [
            self::DB_NAME => $this->getSession()->get(self::DB_NAME),
            self::DB_USER => $this->getSession()->get(self::DB_USER),
            self::DB_PASSWORD => $this->getSession()->get(self::DB_PASSWORD),
            self::DB_HOST => $this->getSession()->get(self::DB_HOST),
            self::DB_PORT => $this->getSession()->get(self::DB_PORT),
        ];
    }
}
