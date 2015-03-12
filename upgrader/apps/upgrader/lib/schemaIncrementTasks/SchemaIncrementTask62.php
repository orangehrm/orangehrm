<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SchemaIncrementTask62
 *
 * @author nirmal
 */
class SchemaIncrementTask62 extends SchemaIncrementTask {

    public function getNotes() {
        
    }

    public function getUserInputWidgets() {
        
    }

    public function loadSql() {
        $sql = array();

        $sql[] = 'CREATE TABLE `ohrm_ws_consumer` (`app_id` INT UNSIGNED AUTO_INCREMENT, `app_token` VARCHAR(10) NOT NULL, `app_name` VARCHAR(50) DEFAULT NULL, `status` TINYINT NOT NULL DEFAULT 1, PRIMARY KEY(`app_id`));';

        $sql[] = 'CREATE TABLE ohrm_oauth_client ( client_id VARCHAR(80) NOT NULL, client_secret VARCHAR(80) NOT NULL, redirect_uri VARCHAR(2000)  NOT NULL, CONSTRAINT client_id_pk PRIMARY KEY (client_id));';

        $sql[] = 'CREATE TABLE ohrm_oauth_access_token (access_token VARCHAR(40) NOT NULL, client_id VARCHAR(80) NOT NULL, user_id VARCHAR(255), expires TIMESTAMP NOT NULL,scope VARCHAR(2000), CONSTRAINT access_token_pk PRIMARY KEY (access_token));';

        $sql[] = 'CREATE TABLE ohrm_oauth_authorization_code (authorization_code VARCHAR(40) NOT NULL, client_id VARCHAR(80) NOT NULL, user_id VARCHAR(255), redirect_uri VARCHAR(2000) NOT NULL, expires TIMESTAMP NOT NULL, scope VARCHAR(2000), CONSTRAINT auth_code_pk PRIMARY KEY (authorization_code));';

        $sql[] = 'CREATE TABLE ohrm_oauth_refresh_token ( refresh_token VARCHAR(40) NOT NULL, client_id VARCHAR(80) NOT NULL, user_id VARCHAR(255), expires TIMESTAMP NOT NULL, scope VARCHAR(2000), CONSTRAINT refresh_token_pk PRIMARY KEY (refresh_token));';

        $sql[] = 'CREATE TABLE ohrm_oauth_user (username VARCHAR(255) NOT NULL, password VARCHAR(2000), first_name VARCHAR(255), last_name VARCHAR(255), CONSTRAINT username_pk PRIMARY KEY (username));';

        $sql[] = 'INSERT INTO `ohrm_module` (`name`, `status`) VALUES(\'directory\', 1);';

        $sql[] = 'SET @module_id := (SELECT LAST_INSERT_ID());';

        $sql[] = 'INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES(\'Directory\', @module_id, \'viewDirectory\');';

        $sql[] = 'SET @directory_configuration_screen_id := (SELECT LAST_INSERT_ID());';

        $sql[] = 'INSERT INTO ohrm_menu_item (`menu_title`, `screen_id`, `parent_id`, `level`, `order_hint`, `url_extras`, `status`) VALUES(\'Directory\', @directory_configuration_screen_id, null, 1, 1000, \'/reset/1\', 1);';

        $sql[] = 'SET @admin_role_id := (SELECT `id` FROM ohrm_user_role WHERE `name` = \'Admin\');';

        $sql[] = 'SET @ess_role_id := (SELECT `id` FROM ohrm_user_role WHERE `name` = \'ESS\');';

        $sql[] = 'INSERT INTO ohrm_user_role_screen (user_role_id, screen_id, can_read, can_create, can_update, can_delete) VALUES  
                    (@admin_role_id, @directory_configuration_screen_id, 1, 1, 1, 1),
                    (@ess_role_id, @directory_configuration_screen_id, 1, 1, 1, 1);';

        $this->sql = $sql;
    }

    public function setUserInputs() {
        
    }

    public function execute() {
        $this->incrementNumber = 62;
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
