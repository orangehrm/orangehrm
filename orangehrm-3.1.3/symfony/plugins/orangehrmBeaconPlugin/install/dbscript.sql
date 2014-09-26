INSERT INTO `hs_hr_config`(`key`,`value`) VALUES 
('beacon.activiation_status','off'),
('beacon.uuid',0),
('beacon.next_flash_time','0000-00-00'),
('beacon.lock','unlocked'),
('beacon.flash_period','120');

INSERT INTO `ohrm_module` (`name`,`status`) VALUES
('communication',1);

SET @admin_module_id := (SELECT `id` FROM `ohrm_module` WHERE `name` = 'admin');

INSERT INTO `ohrm_screen` (`name`,`module_id`,`action_url`) VALUES
('Beacon Registration',@admin_module_id,'beaconRegistration');

SET @beacon_screen_id := (SELECT LAST_INSERT_ID());

SET @admin_menu_id := (SELECT `id` FROM `ohrm_menu_item` WHERE `menu_title` = 'Admin');

INSERT INTO ohrm_menu_item (`menu_title`, `screen_id`, `parent_id`, `level`, `order_hint`, `url_extras`, `status`) VALUES
('Beacon', @beacon_screen_id, @admin_menu_id, 2, 1000, NULL, 1);

SET @admin_role_id := (SELECT `id` FROM ohrm_user_role WHERE `name` = 'Admin');

INSERT INTO ohrm_user_role_screen (user_role_id, screen_id, can_read, can_create, can_update, can_delete) VALUES
(@admin_role_id, @beacon_screen_id, 1, 1, 1, 1);

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

INSERT INTO `ohrm_datapoint_type`(`id`,`name`,`action_class`)  VALUES 
(1,'config','configDatapointProcessor'),
(2,'count','countDatapointProcessor'),
(3, 'session', 'sessionDatapointProcessor'),
(4,'organization','OrganizationDataProcessor');

-- INSERT INTO `ohrm_datapoint` (`name`,`datapoint_type_id`,`definition`) VALUES
-- ('employee_count',2,'
-- 
-- <datapoint type = "count">
--     <settings>
--         <name>employee_count</name>
--     </settings>
--     <parameters>
--         <table>hs_hr_employee</table>
--     </parameters>
-- </datapoint>
-- '),
-- ('user_count',2,'
-- 
-- <datapoint type = "count">
-- <settings>
--         <name>user_count</name>
--     </settings>
--     <parameters>
--         <table>ohrm_user</table>
--     </parameters>
-- </datapoint>
-- '),
-- ('login_count',1,'
-- 
-- <datapoint type = "config">
-- <settings>
--         <name>login_count</name>
--     </settings>
--     <parameters>
--         <key>auth.logins</key>
--     </parameters>
-- </datapoint>
-- ');