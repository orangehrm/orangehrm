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
 * Class SchemaIncrementTask70
 */
class SchemaIncrementTask70  extends SchemaIncrementTask
{

    public function loadSql()
    {
        $sql = array();

        $sql[] = 'DROP TABLE IF EXISTS `ohrm_reset_password`;';
        $sql[] = 'CREATE  TABLE `ohrm_reset_password` (
                 `id` BIGINT UNSIGNED AUTO_INCREMENT,
                  `reset_email` VARCHAR(60) NOT NULL,
                  `reset_request_date` TIMESTAMP NOT NULL ,
                  `reset_code` VARCHAR(200) NOT NULL ,
                  PRIMARY KEY(`id`))
                  ENGINE = InnoDB DEFAULT CHARSET=UTF8;';

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
        $this->incrementNumber = 70;
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