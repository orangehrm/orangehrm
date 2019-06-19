-- Add module to `ohrm_module` table
INSERT INTO ohrm_module (name, status) VALUES
('marketPlace', '1');
set @module_id := (SELECT id FROM ohrm_module WHERE name = 'marketPlace');

-- Add screens to `ohrm_screen` table
INSERT INTO ohrm_screen (name, module_id, action_url) VALUES
('Marcket Place Home Page', @module_id , 'ohrmAddons');

set @MP_home_screen_id := (SELECT id FROM ohrm_screen WHERE action_url = 'ohrmAddons');
set @admin_role_id := (SELECT id FROM ohrm_user_role WHERE name = 'Admin');
set @ESS_role_id := (SELECT id FROM ohrm_user_role WHERE name = 'ESS');
set @Supervisor_role_id := (SELECT id FROM ohrm_user_role WHERE name = 'Supervisor');

-- Task adding permissions
INSERT INTO ohrm_user_role_screen (user_role_id,screen_id, can_read, can_create, can_update, can_delete) VALUES
(@admin_role_id, @MP_home_screen_id, 1,0,0,0),
(@ESS_role_id, @MP_home_screen_id, 1,0,0,0),
(@Supervisor_role_id, @MP_home_screen_id, 1,0,0,0);

INSERT INTO `hs_hr_config` (`key`, `value`) VALUES
('base_url', 'https://marketplace.orangehrm.com');

-- Add data group permissions
INSERT INTO ohrm_data_group (name, description, can_read, can_create, can_update, can_delete) VALUES
('Marketplace', 'Install or Uninstall addons- Tasks', 1, 1, 0, 1);

SET @data_group_id := (SELECT id FROM ohrm_data_group WHERE name = 'Marketplace');

INSERT INTO ohrm_data_group_screen (data_group_id, screen_id, permission) VALUES
(@data_group_id, @MP_home_screen_id, 1);

-- Add default data group permission
INSERT INTO ohrm_user_role_data_group (user_role_id, data_group_id, can_read, can_create, can_update, can_delete, self) VALUES
(@admin_role_id, @data_group_id, 1, 1, 0, 1, 0),
(@ESS_role_id, @data_group_id, 1, 0, 0, 0, 0),
(@Supervisor_role_id, @data_group_id, 1, 0, 0, 0, 0);

CREATE TABLE `ohrm_marketplace_addon` (
    `addon_id` INT(11),
    `title` VARCHAR(100),
    `date` TIMESTAMP,
    `status` VARCHAR(30),
    `version` VARCHAR(100),
    `plugin_name` VARCHAR(255),
    `type` ENUM('paid','free') DEFAULT 'free',
    PRIMARY KEY(`addon_id`)
) engine=innodb default charset=utf8;
