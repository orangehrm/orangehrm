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

use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\DriverManager;
use OrangeHRM\Framework\Http\Session\Session;
use OrangeHRM\Framework\ServiceContainer;
use OrangeHRM\Framework\Services;

class Connection
{
    /**
     * @var DBALConnection|null
     */
    private static ?DBALConnection $connection = null;

    private function __construct()
    {
        /** @var Session $session */
        $session = ServiceContainer::getContainer()->get(Services::SESSION);
        $connectionParams = [
            'dbname' => $session->get(Constant::DB_NAME),
            'user' => $session->get(Constant::DB_USER),
            'password' => $session->get(Constant::DB_PASSWORD),
            'host' => $session->get(Constant::DB_HOST),
            'port' => $session->get(Constant::DB_PORT),
            'driver' => 'pdo_mysql',
            'charset' => 'utf8mb4'
        ];
        self::$connection = DriverManager::getConnection($connectionParams);
    }

    /**
     * @return DBALConnection
     */
    public static function getConnection(): DBALConnection
    {
        if (is_null(self::$connection)) {
            new self();
        }
        return self::$connection;
    }
}
