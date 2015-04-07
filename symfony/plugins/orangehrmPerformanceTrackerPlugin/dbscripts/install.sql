CREATE TABLE `ohrm_performance_track` ( 
  `id` int(11) NOT NULL AUTO_INCREMENT, 
  `emp_number` int(7) NOT NULL, 
  `tracker_name` varchar(200) NOT NULL,
  `added_date` timestamp NULL DEFAULT NULL, 
  `added_by` int(11) DEFAULT NULL, 
  `status` int(11) DEFAULT NULL, 
  `modified_date` timestamp NULL DEFAULT NULL, 
  PRIMARY KEY (`id`), 
  KEY `ohrm_performance_track_fk1_idx` (`emp_number`), 
  KEY `ohrm_performance_track_fk2_idx` (`added_by`), 
  CONSTRAINT `ohrm_performance_track_fk1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE NO ACTION ON UPDATE NO ACTION, 
  CONSTRAINT `ohrm_performance_track_fk2` FOREIGN KEY (`added_by`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE NO ACTION ON UPDATE NO ACTION 
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE `ohrm_performance_tracker_log` ( 
  `id` int(11) NOT NULL AUTO_INCREMENT, 
  `performance_track_id` int(11) DEFAULT NULL, 
  `log` varchar(150) DEFAULT NULL, 
  `comment` varchar(3000) DEFAULT NULL, 
  `status` int(11) DEFAULT NULL, 
  `added_date` timestamp NULL DEFAULT NULL, 
  `modified_date` timestamp NULL DEFAULT NULL, 
  `reviewer_id` int(7) DEFAULT NULL, 
  `achievement` varchar(45) DEFAULT NULL, 
  `user_id` int(10) DEFAULT NULL, 
  PRIMARY KEY (`id`), 
  KEY `ohrm_performance_tracker_log_fk1_idx` (`performance_track_id`), 
  KEY `ohrm_performance_tracker_log_fk2_idx` (`reviewer_id`), 
  KEY `fk_ohrm_performance_tracker_log_1` (`user_id`), 
  CONSTRAINT `fk_ohrm_performance_tracker_log_1` FOREIGN KEY (`user_id`) REFERENCES `ohrm_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION, 
  CONSTRAINT `ohrm_performance_tracker_log_fk1` FOREIGN KEY (`performance_track_id`) REFERENCES `ohrm_performance_track` (`id`) ON DELETE CASCADE ON UPDATE CASCADE, 
  CONSTRAINT `ohrm_performance_tracker_log_fk2` FOREIGN KEY (`reviewer_id`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE ON UPDATE NO ACTION 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `ohrm_performance_tracker_reviewer` ( 
  `id` int(11) NOT NULL AUTO_INCREMENT, 
  `performance_track_id` int(11) NOT NULL, 
  `reviewer_id` int(7) NOT NULL, 
  `added_date` timestamp NULL DEFAULT NULL, 
  `status` int(2) DEFAULT NULL, 
  PRIMARY KEY (`id`), 
  KEY `ohrm_performance_tracker_reviewer_fk1_idx` (`performance_track_id`), 
  KEY `ohrm_performance_tracker_reviewer_fk2_idx` (`reviewer_id`), 
  CONSTRAINT `ohrm_performance_tracker_reviewer_fk1` FOREIGN KEY (`performance_track_id`) REFERENCES `ohrm_performance_track` (`id`) ON DELETE CASCADE ON UPDATE CASCADE, 
  CONSTRAINT `ohrm_performance_tracker_reviewer_fk2` FOREIGN KEY (`reviewer_id`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE ON UPDATE NO ACTION 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET @admin_user_role := (SELECT id FROM ohrm_user_role WHERE name = 'Admin');
SET @ess_user_role := (SELECT id FROM ohrm_user_role WHERE name = 'ESS');

SET @performance_module_id:= (SELECT `id` FROM `ohrm_module` WHERE `name` = 'performance');

-- Admin Section. Manage Trackers.
INSERT INTO `ohrm_screen` (`name`, `module_id`, `action_url`) VALUES
( 'Manage_Trackers', @performance_module_id, 'addPerformanceTracker');
SET @manage_performance_trackers_screen_id :=  (SELECT LAST_INSERT_ID());

SET @performance_menu_id:= (SELECT id FROM ohrm_menu_item where menu_title = 'Performance');
INSERT INTO ohrm_menu_item (`menu_title`, `screen_id`, `parent_id`, `level`, `order_hint`, `url_extras`, `status`) VALUES
('Manage Trackers', @manage_performance_trackers_screen_id, @performance_menu_id, 2, 700, NULL, 1);

INSERT INTO ohrm_user_role_screen (user_role_id, screen_id, can_read, can_create, can_update, can_delete) VALUES
(@admin_user_role, @manage_performance_trackers_screen_id, 1, 1, 1, 1),
(@ess_user_role, @manage_performance_trackers_screen_id, 0, 0, 0, 0);

-- Admin/ ESS Section Employee Trackers.
INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES
( 'Employee_Trackers', @performance_module_id, 'viewEmployeePerformanceTrackerList');
SET @employee_trackers_screen_id :=  (SELECT LAST_INSERT_ID());

INSERT INTO ohrm_menu_item (`menu_title`, `screen_id`, `parent_id`, `level`, `order_hint`, `url_extras`, `status`) VALUES
('Employee Trackers', @employee_trackers_screen_id, @performance_menu_id, 2, 800, NULL, 1);

INSERT INTO ohrm_user_role_screen (user_role_id, screen_id, can_read, can_create, can_update, can_delete) VALUES
(@admin_user_role, @employee_trackers_screen_id, 1, 1, 1, 1),
(@ess_user_role, @employee_trackers_screen_id, 1, 1, 1, 0);

-- ESS secetion. My Trackers.
INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES
( 'My_Trackers', @performance_module_id, 'viewMyPerformanceTrackerList');
SET @my_trackers_screen_id :=  (SELECT LAST_INSERT_ID());

INSERT INTO ohrm_menu_item (`menu_title`, `screen_id`, `parent_id`, `level`, `order_hint`, `url_extras`, `status`) VALUES
('My Trackers', @my_trackers_screen_id, @performance_menu_id, 2, 700, NULL, 1);

INSERT INTO ohrm_user_role_screen (user_role_id, screen_id, can_read, can_create, can_update, can_delete) VALUES
(@admin_user_role, @my_trackers_screen_id, 0, 0, 0, 0),
(@ess_user_role, @my_trackers_screen_id, 1, 0, 1, 0);

-- Tracker Logs. (No menu item)
INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES
( 'Employee_Tracker_Logs', @performance_module_id, 'addPerformanceTrackerLog');
SET @employee_tracker_logs_screen_id :=  (SELECT LAST_INSERT_ID());

INSERT INTO ohrm_user_role_screen (user_role_id, screen_id, can_read, can_create, can_update, can_delete) VALUES
(@admin_user_role, @employee_tracker_logs_screen_id, 1, 1, 1, 0),
(@ess_user_role, @employee_tracker_logs_screen_id, 1, 0, 0, 0);