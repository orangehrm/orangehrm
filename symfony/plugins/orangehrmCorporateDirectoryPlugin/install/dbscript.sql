INSERT INTO `ohrm_module` (`name`, `status`) VALUES ('directory', 1);  

SET @module_id := (SELECT LAST_INSERT_ID());  
  
INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES  
('Directory', @module_id, 'viewDirectory'); 

SET @directory_configuration_screen_id := (SELECT LAST_INSERT_ID());
  
INSERT INTO ohrm_menu_item (`menu_title`, `screen_id`, `parent_id`, `level`, `order_hint`, `url_extras`, `status`) VALUES  
('Directory', @directory_configuration_screen_id, null, 1, 1000, '/reset/1', 1);  
  
SET @admin_role_id := (SELECT `id` FROM ohrm_user_role WHERE `name` = 'Admin'); 

SET @ess_role_id := (SELECT `id` FROM ohrm_user_role WHERE `name` = 'ESS'); 

INSERT INTO ohrm_user_role_screen (user_role_id, screen_id, can_read, can_create, can_update, can_delete) VALUES  
(@admin_role_id, @directory_configuration_screen_id, 1, 1, 1, 1),
(@ess_role_id, @directory_configuration_screen_id, 1, 1, 1, 1);  

