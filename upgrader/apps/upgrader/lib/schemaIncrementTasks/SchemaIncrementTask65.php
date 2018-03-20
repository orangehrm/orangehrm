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

class SchemaIncrementTask65 extends SchemaIncrementTask {

    public function getNotes() {
        
    }

    public function getUserInputWidgets() {
        
    }

    public function loadSql() {
        $sql = array();

        $sql[] = 'ALTER TABLE `hs_hr_config`
                  CHANGE COLUMN `value` `value` TEXT NOT NULL;';


        $sql[] = 'INSERT INTO hs_hr_config (`key`, `value`) VALUES
(\'open_source_integrations\',\'<xml>
<integrations>
</integrations>
</xml>
\');';

        $sql[] = 'INSERT INTO `hs_hr_config` (`key`, `value`) VALUES 
(\'authentication.status\', \'Enable\'), 
(\'authentication.enforce_password_strength\', \'on\'), 
(\'authentication.default_required_password_strength\', \'strong\');';

        $this->sql = $sql;
    }

    public function setUserInputs() {
        
    }

    public function execute() {
        $this->incrementNumber = 65;
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

}
