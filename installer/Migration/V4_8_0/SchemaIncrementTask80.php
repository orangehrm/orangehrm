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

/**
 * Class SchemaIncrementTask80
 */
class SchemaIncrementTask80 extends SchemaIncrementTask
{

    public function loadSql()
    {
        $sql[] = "INSERT INTO `hs_hr_config` (`key` ,`value`) VALUES ('help.url',  'https://opensourcehelp.orangehrm.com');";

        $sql[] = "INSERT INTO `hs_hr_config` (`key` ,`value`) VALUES ('help.processorClass',  'ZendeskHelpProcessor');";

        $sql[] = "UPDATE `hs_hr_config` SET `value` = '4.8' WHERE `hs_hr_config`.`key` = 'instance.version';";

        $sql[] = "UPDATE `hs_hr_config` SET `value` = '" . $this->incrementNumber . "' WHERE `hs_hr_config`.`key` = 'instance.increment_number';";

        $sql[] = "INSERT INTO `ohrm_i18n_group` (`name`,`title`) VALUES ('help','Help')";
        
        $this->sql = $sql;
    }

    public function getUserInputWidgets()
    {
    }

    public function setUserInputs()
    {
    }

    public function getNotes()
    {
    }

    public function execute()
    {
        $this->incrementNumber = 80;
        parent::execute();
        $result = [];
        foreach ($this->sql as $sql) {
            $result[] = $this->upgradeUtility->executeSql($sql);
        }
        $this->checkTransactionComplete($result);
        $this->updateOhrmUpgradeInfo($this->transactionComplete, $this->incrementNumber);
        $this->upgradeUtility->finalizeTransaction($this->transactionComplete);
        $this->upgradeUtility->closeDbConnection();
    }
}
