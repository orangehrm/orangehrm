CREATE TABLE `ohrm_datapoint_type` (
    `id` INT AUTO_INCREMENT, 
    `name` VARCHAR(100) NOT NULL, 
    `action_class` VARCHAR(100) NOT NULL, 
    PRIMARY KEY(id)
) ENGINE = INNODB DEFAULT CHARSET=utf8;


CREATE TABLE `ohrm_datapoint` (
    `id` INT AUTO_INCREMENT, 
    `name` VARCHAR(100), 
    `datapoint_type_id` INT NOT NULL, 
    `definition` LONGTEXT NOT NULL, 
    PRIMARY KEY(`id`),
    FOREIGN KEY (`datapoint_type_id`) REFERENCES `ohrm_datapoint_type` (`id`) ON DELETE CASCADE
) ENGINE = INNODB DEFAULT CHARSET=utf8;

CREATE TABLE `ohrm_beacon_notification` (
    `id` INT AUTO_INCREMENT, 
    `name` VARCHAR(100) NOT NULL, 
    `expiry_date` TIMESTAMP NOT NULL, 
    `definition` LONGTEXT NOT NULL, PRIMARY KEY(id)
) ENGINE = INNODB DEFAULT CHARSET=utf8;

CREATE TABLE `ohrm_login` (
    `id` INT AUTO_INCREMENT, 
    `user_id` BIGINT NOT NULL, 
    `user_name` VARCHAR(255), 
    `user_role_name` TEXT NOT NULL, 
    `user_role_predefined` TINYINT(1) NOT NULL, 
    `login_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id)
) ENGINE = INNODB DEFAULT CHARSET=utf8;


-- INSERT INTO `hs_hr_config`(`key`,`value`) VALUES 
-- ('beacon.activation_acceptance_status','off'),
-- ('beacon.company_name',''),
-- ('beacon.activiation_status','off'),
-- ('beacon.uuid',0),
-- ('beacon.next_flash_time','0000-00-00'),
-- ('beacon.lock','unlocked'),
-- ('beacon.flash_period','120'),
-- ('admin.product_type','os');



INSERT INTO `ohrm_module` (`name`,`status`) VALUES
('communication',1);
INSERT INTO `ohrm_datapoint_type`(`id`,`name`,`action_class`)  VALUES 
(1,'config','configDatapointProcessor'),
(2,'count','countDatapointProcessor'),
(3, 'session', 'sessionDatapointProcessor'),
(4,'organization','OrganizationDataProcessor');

