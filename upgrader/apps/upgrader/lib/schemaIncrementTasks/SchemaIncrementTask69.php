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
 * Class SchemaIncrementTask69
 */
class SchemaIncrementTask69 extends SchemaIncrementTask
{

    public function loadSql()
    {
        $sql = array();
        $sql[] = 'INSERT INTO ohrm_module (name, status) VALUES (\'marketPlace\', \'1\');';
        $sql[] = 'set @module_id := (SELECT id FROM ohrm_module WHERE name = \'marketPlace\');';
        $sql[] = 'INSERT INTO ohrm_screen (
                  name, 
                  module_id, 
                  action_url) VALUES
                  (\'Marcket Place Home Page\', @module_id , \'ohrmAddons\');';
        $sql[] = 'set @MP_home_screen_id := (SELECT id FROM ohrm_screen WHERE action_url = \'ohrmAddons\');';
        $sql[] = 'set @admin_role_id := (SELECT id FROM ohrm_user_role WHERE name = \'Admin\');';
        $sql[] = 'set @ESS_role_id := (SELECT id FROM ohrm_user_role WHERE name = \'ESS\');';
        $sql[] = 'set @Supervisor_role_id := (SELECT id FROM ohrm_user_role WHERE name = \'Supervisor\');';
        $sql[] = 'INSERT INTO ohrm_user_role_screen (
                  user_role_id,screen_id, 
                  can_read,
                  can_create, 
                  can_update, 
                  can_delete) VALUES
                  (@admin_role_id, @MP_home_screen_id, 1,0,0,0),
                  (@ESS_role_id, @MP_home_screen_id, 1,0,0,0),
                  (@Supervisor_role_id, @MP_home_screen_id, 1,0,0,0);';
        $sql[] = 'INSERT INTO `hs_hr_config` (`key`, `value`) VALUES
                  (\'base_url\', \'https://marketplace.orangehrm.com\');';
        $sql[] = 'INSERT INTO ohrm_data_group (
                  name,
                  description,
                  can_read,
                  can_create, 
                  can_update, 
                  can_delete) VALUES
                  (\'Marketplace\', \'Install or Uninstall addons- Tasks\', 1, 1, 0, 1);';
        $sql[] = 'SET @data_group_id := (SELECT id FROM ohrm_data_group WHERE name = \'Marketplace\');';
        $sql[] = 'INSERT INTO ohrm_data_group_screen (data_group_id, screen_id, permission) VALUES
                  (@data_group_id, @MP_home_screen_id, 1);';
        $sql[] = 'INSERT INTO ohrm_user_role_data_group (
                  user_role_id, data_group_id,
                  can_read, can_create,
                  can_update,
                  can_delete,
                  self) VALUES
                  (@admin_role_id, @data_group_id, 1, 1, 0, 1, 0),
                  (@ESS_role_id, @data_group_id, 1, 0, 0, 0, 0),
                  (@Supervisor_role_id, @data_group_id, 1, 0, 0, 0, 0);';
        $sql[] = 'CREATE TABLE `ohrm_marketplace_addon` (
                  `addon_id` INT(11),
                  `title` VARCHAR(100),
                  `date` TIMESTAMP,
                  `status` VARCHAR(30),
                  `version` VARCHAR(100),
                  `plugin_name` VARCHAR(255),
                  PRIMARY KEY(`addon_id`)
                  ) engine=innodb default charset=utf8;';
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
        $this->incrementNumber = 69;
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
