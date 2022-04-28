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

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Exception;

class UpgraderConfigUtility
{
    /**
     * @return bool|Exception
     */
    public function checkDatabaseConnection()
    {
        try {
            $connection = $this->getConnection();
            $connection->connect();
            return true;
        } catch (Exception $exception) {
            Logger::getLogger()->error($exception->getMessage());
            Logger::getLogger()->error($exception->getTraceAsString());
            return $exception;
        }
    }

    /**
     * @return bool
     * @throws \Doctrine\DBAL\Exception
     */
    public function checkDatabaseStatus(): bool
    {
        $connection = $this->getConnection();
        return $this->getSchemaManager()->tablesExist(['ohrm_upgrade_status']);
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection(): \Doctrine\DBAL\Connection
    {
        return Connection::getConnection();
    }

    /**
     * @return AbstractSchemaManager
     * @throws \Doctrine\DBAL\Exception
     */
    private function getSchemaManager(): AbstractSchemaManager
    {
        return $this->getConnection()->createSchemaManager();
    }
}
