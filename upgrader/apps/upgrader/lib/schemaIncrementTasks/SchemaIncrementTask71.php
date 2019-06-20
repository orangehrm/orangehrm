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
 * Class SchemaIncrementTask71
 */
class SchemaIncrementTask71  extends SchemaIncrementTask
{

    public function loadSql()
    {
        $sql = array(
            "SET @old_path := (SELECT `sendmail_path` FROM `ohrm_email_configuration` WHERE `mail_type`='sendmail' AND `sendmail_path` IS NOT NULL AND `sendmail_path` != '');",
            "INSERT INTO hs_hr_config(`key`, `value`) VALUES ('email_config.sendmail_path','/usr/sbin/sendmail -bs');",
            'UPDATE `hs_hr_config`
            SET `value` = @old_path
            WHERE `key`= "email_config.sendmail_path" AND @old_path!="";',
            'ALTER TABLE `ohrm_email_configuration` DROP COLUMN `sendmail_path`;',
            'ALTER TABLE `ohrm_marketplace_addon` ADD `type` ENUM(\'free\',\'paid\') DEFAULT \'free\';'
        );
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
        $this->incrementNumber = 71;
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