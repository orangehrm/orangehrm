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
 * Class SchemaIncrementTask76
 */
class SchemaIncrementTask76 extends SchemaIncrementTask
{

    public function loadSql()
    {
        $sql[] = "CREATE TABLE `ohrm_oauth_scope` (
  `scope` text,
  `is_default` boolean not null default false
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $sql[] = "ALTER TABLE `ohrm_oauth_client` ADD `grant_types` VARCHAR(80) NULL DEFAULT NULL;";
        $sql[] = "ALTER TABLE `ohrm_oauth_client` ADD `scope` VARCHAR(4000) NULL DEFAULT NULL;";
        // Update existing client records as `client_credentials` grant type
        $sql[] = "UPDATE `ohrm_oauth_client` SET `grant_types`='client_credentials',`scope`='admin';";
        $sql[] = "INSERT IGNORE INTO `ohrm_oauth_client`(`client_id`, `client_secret`, `redirect_uri`, `grant_types`, `scope`) VALUES
('orangehrm_mobile_app','','','password refresh_token','user');";
        $sql[] = "CREATE TABLE `ohrm_rest_api_usage` (
    `id` INT NOT NULL AUTO_INCREMENT ,
    `client_id` VARCHAR(255) NULL DEFAULT NULL ,
    `user_id` VARCHAR(255) NULL DEFAULT NULL ,
    `scope` VARCHAR(20) NULL DEFAULT NULL,
    `method` VARCHAR(20) NULL DEFAULT NULL ,
    `module` VARCHAR(20) NULL DEFAULT NULL ,
    `action` VARCHAR(50) NULL DEFAULT NULL ,
    `path` VARCHAR(255) NULL DEFAULT NULL ,
    `parameters` TINYTEXT NULL DEFAULT NULL ,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;";
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
        $this->incrementNumber = 76;
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
