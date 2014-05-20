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

require_once sfConfig::get('sf_root_dir') . '/../symfony/lib/vendor/phpseclib/Crypt/Random.php';

/**
 * Upgrader changes to support bcrypt() based hash and storying csrf key in hs_hr_config
 *
 */
class SchemaIncrementTask59 extends SchemaIncrementTask {

    public function execute() {
        $this->incrementNumber = 59;
        parent::execute();

        $result = array();

        foreach ($this->sql as $sql) {
            $result[] = $this->upgradeUtility->executeSql($sql);
        }

        $this->checkTransactionComplete($result);
        $this->updateOhrmUpgradeInfo($this->transactionComplete, $this->incrementNumber);
        $this->upgradeUtility->finalizeTransaction($this->transactionComplete);
        $this->upgradeUtility->closeDbConnection();
    }

    public function getNotes() {
        
    }

    public function getUserInputWidgets() {
        
    }

    public function loadSql() {

        $sql = array();
        
        // Increase field width to support new bcrypt hashes
        $sql[] = "ALTER TABLE ohrm_user MODIFY COLUMN user_password varchar(255) DEFAULT NULL;";

        // Create and insert csrf hash
        $csrfKey = $this->createCsrfKey();
        $sql[] = "INSERT INTO `hs_hr_config` ( `key`, `value`) VALUES ('csrf_secret', '{$csrfKey}');";

        $this->sql = $sql;
    }

    public function setUserInputs() {
        
    }

    public function createCsrfKey() {
        $csrfKey = '';

        while (strlen($csrfKey) <= 50) {
            $csrfKey .= base_convert(crypt_random(), 10, 32);
        }

        return $csrfKey;
    }

}
