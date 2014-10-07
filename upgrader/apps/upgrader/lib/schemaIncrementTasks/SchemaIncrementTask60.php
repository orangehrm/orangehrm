<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once sfConfig::get('sf_root_dir') . '/../symfony/lib/vendor/phpseclib/Crypt/Random.php';

/**
 * Upgrader changes to support bcrypt() based hash and storying csrf key in hs_hr_config
 *
 */
class SchemaIncrementTask60 extends SchemaIncrementTask {

    public function execute() {
        $this->incrementNumber = 60;
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
        $sql[] = "CREATE TABLE `ohrm_datapoint_type` (
    `id` INT AUTO_INCREMENT, 
    `name` VARCHAR(100) NOT NULL, 
    `action_class` VARCHAR(100) NOT NULL, 
    PRIMARY KEY(id)
) ENGINE = INNODB DEFAULT CHARSET=utf8;
;";
        $sql[] = "CREATE TABLE `ohrm_datapoint` (
    `id` INT AUTO_INCREMENT, 
    `name` VARCHAR(100), 
    `datapoint_type_id` INT NOT NULL, 
    `definition` LONGTEXT NOT NULL, 
    PRIMARY KEY(`id`),
    FOREIGN KEY (`datapoint_type_id`) REFERENCES `ohrm_datapoint_type` (`id`) ON DELETE CASCADE
) ENGINE = INNODB DEFAULT CHARSET=utf8;";
        $sql[] = "CREATE TABLE `ohrm_beacon_notification` (
    `id` INT AUTO_INCREMENT, 
    `name` VARCHAR(100) NOT NULL, 
    `expiry_date` TIMESTAMP NOT NULL, 
    `definition` LONGTEXT NOT NULL, PRIMARY KEY(id)
) ENGINE = INNODB DEFAULT CHARSET=utf8;";
        $sql[] = "CREATE TABLE `ohrm_login` (
    `id` INT AUTO_INCREMENT, 
    `user_id` BIGINT NOT NULL, 
    `user_name` VARCHAR(255), 
    `user_role_name` TEXT NOT NULL, 
    `user_role_predefined` TINYINT(1) NOT NULL, 
    `login_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id)
) ENGINE = INNODB DEFAULT CHARSET=utf8;";

        $sql[] = "INSERT INTO `hs_hr_config`(`key`,`value`) VALUES 
            ('beacon.activation_acceptance_status','off'),
('beacon.company_name',''),
('beacon.activiation_status','off'),
('beacon.uuid',0),
('beacon.next_flash_time','0'),
('beacon.lock','unlocked'),
('beacon.flash_period','120'),
('admin.product_type','os');";
        $sql[] = "INSERT INTO `ohrm_module` (`name`,`status`) VALUES
('communication',1);";
        $sql[] = "INSERT INTO `ohrm_datapoint_type`(`id`,`name`,`action_class`)  VALUES 
(1,'config','configDatapointProcessor'),
(2,'count','countDatapointProcessor'),
(3, 'session', 'sessionDatapointProcessor'),
(4,'organization','OrganizationDataProcessor');";
        // Create and insert csrf hash
        $csrfKey = $this->createCsrfKey();
        $sql[] = "UPDATE `hs_hr_config` SET `value`='{$csrfKey}' WHERE `key` = 'csrf_secret';";

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
