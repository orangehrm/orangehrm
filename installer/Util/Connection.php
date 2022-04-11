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

use Doctrine\DBAL\DriverManager;

class Connection
{
    /**
     * @var DriverManager|null
     */
    private static ?DriverManager $driverManager = null;

    private function __construct()
    {
        //ToDo
        $connectionParams = [
            'dbname' => 'orangehrm5x',
            'user' => 'root',
            'password' => 'root',
            'host' => 'mariadb104',
            'port' => 3306,
            'driver' => 'pdo_mysql',
            'charset' => 'utf8mb4'
        ];
        self::$driverManager = DriverManager::getConnection($connectionParams);
    }

    /**
     * @return DriverManager
     */
    public static function getDriverManager(): DriverManager
    {
        if (is_null(self::$driverManager)) {
            new self();
        }
        return self::$driverManager;
    }
}
