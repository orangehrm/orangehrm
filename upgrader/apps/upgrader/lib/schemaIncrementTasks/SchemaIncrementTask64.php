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

class SchemaIncrementTask64 extends SchemaIncrementTask {

    public function getNotes() {
        
    }

    public function getUserInputWidgets() {
        
    }

    public function loadSql() {
        $sql = array();

        $sql[] = 'CREATE TABLE `ohrm_employee_event` (
                 `event_id` int(7) NOT NULL AUTO_INCREMENT,
                  `employee_id` int(7) NOT NULL DEFAULT \'0\',
                  `type` varchar(45) DEFAULT NULL,
                  `event` varchar(45) DEFAULT NULL,
                  `note` varchar(150) DEFAULT NULL,
                  `created_date` datetime DEFAULT NULL,
                  `created_by` varchar(45) DEFAULT NULL,
                  PRIMARY KEY (`event_id`)
                ) ENGINE=InnoDB;';


         $sql[] = 'INSERT INTO ohrm_screen ( `name`, `module_id`, `action_url`) VALUES ( \'Register OAuth Client\', 2, \'registerOAuthClient\');';

        $sql[] = 'SET @oauth_client_screen_id := (SELECT LAST_INSERT_ID());';

        $sql[] = 'SET @admin_menu_id := (SELECT `id` FROM ohrm_menu_item WHERE `menu_title` = \'Admin\' AND `level` = 1);';

        $sql[] = 'SET @configuration_id := (SELECT `id` FROM ohrm_menu_item WHERE `menu_title` = \'Configuration\' AND `level` = 2 AND parent_id = @admin_menu_id);';

        $sql[] = 'SET @max_order := (SELECT MAX(`order_hint`) FROM ohrm_menu_item WHERE parent_id = @configuration_id);';

        $sql[] = 'INSERT INTO ohrm_menu_item ( `menu_title`, `screen_id`, `parent_id`, `level`, `order_hint`, `url_extras`, `status`) VALUES 
                     (\'Register OAuth Client\', @oauth_client_screen_id, @configuration_id, 3, @max_order+100, NULL, 1);';

        $sql[] = 'INSERT INTO ohrm_user_role_screen (user_role_id, screen_id, can_read, can_create, can_update, can_delete) VALUES  
                      (1, @oauth_client_screen_id, 1, 1, 1, 0);';

        $this->sql = $sql;
    }

    public function setUserInputs() {
        
    }

    public function execute() {
        $this->incrementNumber = 64;
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
