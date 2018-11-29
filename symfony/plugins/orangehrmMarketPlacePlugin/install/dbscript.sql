-- Add module to `ohrm_module` table
INSERT INTO ohrm_module (name, status) VALUES
('marketPlace', '1');
set @module_id := (SELECT id FROM ohrm_module WHERE name = 'marketPlace');
set @admin_role_id := (SELECT id FROM ohrm_user_role WHERE name = 'Admin');
set @ESS_role_id := (SELECT id FROM ohrm_user_role WHERE name = 'ESS');
set @Supervisor_role_id := (SELECT id FROM ohrm_user_role WHERE name = 'Supervisor');

-- Add screens to `ohrm_screen` table
INSERT INTO ohrm_screen (name, module_id, action_url) VALUES
('Marcket Place Home Page', @module_id , 'ohrmAddons');

set @MP_home_screen_id := (SELECT id FROM ohrm_screen WHERE action_url = 'ohrmAddons');

-- Add menu items to `ohrm_menu_items` which are showing in UI left menu
INSERT INTO ohrm_menu_item (menu_title, screen_id, parent_id, level, order_hint, url_extras, status) VALUES
('Marketplace', @MP_home_screen_id , NULL, '1', '1200', NULL, '1');

set @parent_menu_id := (SELECT id FROM ohrm_menu_item WHERE menu_title = 'Marketplace');

INSERT INTO ohrm_menu_item (menu_title, screen_id, parent_id, level, order_hint, url_extras, status) VALUES
('Addons', NULL , @parent_menu_id, 2, '100', null, 1);

-- Task adding permissions
INSERT INTO ohrm_user_role_screen (user_role_id,screen_id, can_read, can_create, can_update, can_delete) VALUES
(@admin_role_id, @MP_home_screen_id, 1,0,0,0),
(@ESS_role_id, @MP_home_screen_id, 1,0,0,0),
(@Supervisor_role_id, @MP_home_screen_id, 1,0,0,0);

-- Add data group permissions
INSERT INTO ohrm_data_group (name, description, can_read, can_create, can_update, can_delete) VALUES
  ('Marketplace', 'Install or Uninstall addons- Tasks', 1, 0, 0, 0);

  SET @data_group_id := (SELECT id FROM ohrm_data_group WHERE name = 'Marketplace');

INSERT INTO ohrm_data_group_screen (data_group_id, screen_id, permission) VALUES
  (@data_group_id, @MP_home_screen_id, 1);

INSERT INTO ohrm_user_role_data_group (user_role_id, data_group_id, can_read, can_create, can_update, can_delete, self) VALUES
  (@admin_role_id, @data_group_id, 1, 1, 1, 1, 1);

INSERT INTO `hs_hr_config` (`key`, `value`) VALUES
  ('client_id', '1_5dk5bp84p0wskowswcs8kw48osw8c8cwwso0wo0w4ck0w8kkw0'),
  ('client_secret', '2tnry9y53v40c8so4cggk0ogsgogg8wogoosk4wo4kww8gs8g8'),
  ('base_url', 'https://demo-marketplace.orangehrm.com');
