<?php
/**
 * *
 *  * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 *  * all the essential functionalities required for any enterprise.
 *  * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *  *
 *  * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 *  * the GNU General Public License as published by the Free Software Foundation; either
 *  * version 2 of the License, or (at your option) any later version.
 *  *
 *  * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 *  * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *  * See the GNU General Public License for more details.
 *  *
 *  * You should have received a copy of the GNU General Public License along with this program;
 *  * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 *  * Boston, MA  02110-1301, USA
 *
 */

namespace OrangeHRM\Installer\Controller\Upgrader\Traits;

use mysqli;

trait UpgraderUtilityTrait
{
    private $dbConnection = null;

    /***
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $dbname
     * @param int|null $port
     * @return bool
     */
    public function checkDatabaseConnection(
        string $host,
        string $username,
        string $password,
        string $dbname,
        ?int $port
    ): bool {
        $this->dbConnection = @new mysqli($host, $username, $password, $dbname, $port);
        return !$this->dbConnection->connect_error;
    }

    /**
     * @return bool
     */
    public function checkDatabaseStatus(): bool
    {
        $query = "SHOW TABLES LIKE 'ohrm_upgrade_status'";
        $result = $this->executeSql($query);
        return $result->num_rows > 0;
    }

    /**
     * @param string $query
     * @return mixed
     */
    private function executeSql(string $query)
    {
//        UpgradeLogger::writeLogMessage('Executing SQL:' . $query);
//
//        if (!$result) {
//            $logMessage = 'MySQL Error: ' . mysqli_error($this->dbConnection) . ". \nQuery: $query\n";
//            UpgradeLogger::writeErrorMessage($logMessage, true);
//        }

        return $this->dbConnection->query($query);
    }
}
