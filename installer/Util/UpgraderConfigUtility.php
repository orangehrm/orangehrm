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
use OrangeHRM\Installer\Exception\SystemCheckException;

class UpgraderConfigUtility
{
    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function connectToDatabase(): void
    {
        try {
            $connection = $this->getConnection();
            $connection->connect();
        } catch (Exception $e) {
            Logger::getLogger()->error($e->getMessage());
            Logger::getLogger()->error($e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * @return bool
     */
    public function checkDatabaseStatus(): bool
    {
        return $this->getSchemaManager()->tablesExist(['ohrm_upgrade_status']);
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    private function getConnection(): \Doctrine\DBAL\Connection
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

    /**
     * @throws SystemCheckException
     */
    public function checkDatabaseConnection(): void
    {
        $systemCheck = new SystemCheck();
        if (!$systemCheck->checkPDOExtensionEnabled()) {
            throw new SystemCheckException('Please Enable `PDO` Extension To Proceed');
        }

        if (!$systemCheck->checkPDOMySqlExtensionEnabled()) {
            throw new SystemCheckException('Please Enable `pdo_mysql` Extension To Proceed');
        }

        try {
            $this->connectToDatabase();
        } catch (\Doctrine\DBAL\Exception $e) {
            $dbInfo = StateContainer::getInstance()->getDbInfo();
            $dbHost = $dbInfo[StateContainer::DB_HOST];
            $dbPort = $dbInfo[StateContainer::DB_PORT];

            $appSetupUtility = new AppSetupUtility();
            $message = $appSetupUtility->getExistingDBConnectionErrorMessage($e, $dbHost, $dbPort);
            throw new SystemCheckException($message);
        }

        if ($this->checkDatabaseStatus()) {
            throw new SystemCheckException('Failed to Proceed: Interrupted Database');
        }
    }
}
