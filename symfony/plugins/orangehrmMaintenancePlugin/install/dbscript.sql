-- Add module to `ohrm_module` table
INSERT INTO ohrm_module (name, status) VALUES
('maintenance', '1');
set @module_id := (SELECT id FROM ohrm_module WHERE name = 'maintenance');
set @admin_role_id := (SELECT id FROM ohrm_user_role WHERE name = 'Admin');

-- Add screens to `ohrm_screen` table
INSERT INTO ohrm_screen (name, module_id, action_url) VALUES
('Purge Employee Records', @module_id , 'purgeEmployee'),
('Purge Candidate Records', @module_id , 'purgeCandidateData'),
('Access Employee Records', @module_id , 'accessEmployeeData');

set @purge_employee_screen_id := (SELECT id FROM ohrm_screen WHERE action_url = 'purgeEmployee');
set @purge_candidate_screen_id := (SELECT id FROM ohrm_screen WHERE action_url = 'purgeCandidateData');
set @access_employee_records_screen_id := (SELECT id FROM ohrm_screen WHERE action_url = 'accessEmployeeData');

-- Add menu items to `ohrm_menu_items` which are showing in UI left menu
INSERT INTO ohrm_menu_item (menu_title, screen_id, parent_id, level, order_hint, url_extras, status) VALUES
('Maintenance', @purge_employee_screen_id , NULL, '1', '1200', NULL, '1');

set @parent_menu_id := (SELECT id FROM ohrm_menu_item WHERE menu_title = 'Maintenance');

INSERT INTO ohrm_menu_item (menu_title, screen_id, parent_id, level, order_hint, url_extras, status) VALUES
('Purge Records', NULL , @parent_menu_id, 2, '100', null, 1),
('Access Records', @access_employee_records_screen_id, @parent_menu_id, 2, '200', null, 1);

set @parent_menu_id_level_2:= (SELECT id FROM ohrm_menu_item WHERE menu_title = 'Purge Records');
INSERT INTO ohrm_menu_item (menu_title, screen_id, parent_id, level, order_hint, url_extras, status) VALUES
('Employee Records', @purge_employee_screen_id, @parent_menu_id_level_2, 3, '100', null, 1),
('Candidate Records', @purge_candidate_screen_id, @parent_menu_id_level_2, 3, '200', null, 1);


-- Task view permissions
INSERT INTO ohrm_user_role_screen (user_role_id,screen_id, can_read) VALUES
(@admin_role_id, @purge_employee_screen_id, 1),
(@admin_role_id, @purge_candidate_screen_id, 1),
(@admin_role_id, @access_employee_records_screen_id, 1);

-- Task adding permissions
INSERT INTO ohrm_user_role_screen (user_role_id,screen_id, can_read, can_create, can_update, can_delete) VALUES
(@admin_role_id, @access_employee_records_screen_id, 1,1,1,1);

-- Add data group permissions
INSERT INTO ohrm_data_group (name, description, can_read, can_create, can_update, can_delete) VALUES
  ('GDPR Employee', 'Employee Records purge or Extract- Tasks', 1, 1, 1, 1);

  SET @data_group_id := (SELECT id FROM ohrm_data_group WHERE name = 'GDPR Employee');

INSERT INTO ohrm_data_group_screen (data_group_id, screen_id, permission) VALUES
  (@data_group_id, @purge_employee_screen_id, 1),
  (@data_group_id, @access_employee_records_screen_id, 1);

INSERT INTO ohrm_user_role_data_group (user_role_id, data_group_id, can_read, can_create, can_update, can_delete, self) VALUES
  (@admin_role_id, @data_group_id, 1, 1, 1, 1, 1);
