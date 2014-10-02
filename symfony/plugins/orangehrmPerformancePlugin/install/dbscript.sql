CREATE TABLE `ohrm_kpi_360` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `job_title_code` varchar(10) DEFAULT NULL,
  `department_code` varchar(10) DEFAULT NULL,
  `kpi_indicators` varchar(255) DEFAULT NULL,
  `kpi_group_id` int(7) DEFAULT NULL,
  `min_rating` int(7) DEFAULT NULL,
  `max_rating` int(7) DEFAULT NULL,
  `default_kpi` smallint(1) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `ohrm_kpi_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `weight` int(7) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` datetime DEFAULT NULL,
  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE `ohrm_performance_review_360` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `instruction_id` int(7) NOT NULL,
  `status_id` int(7) DEFAULT NULL,
  `employee_number` int(7) DEFAULT NULL,
  `work_period_start` date DEFAULT NULL,
  `work_period_end` date DEFAULT NULL,
  `job_title_code` int(7) DEFAULT NULL,
  `department_id` int(7) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `completed_date` date DEFAULT NULL,  
  `activated_date` DATETIME DEFAULT NULL,
  `final_comment` text CHARACTER SET utf8 COLLATE utf8_bin,
  `final_rate` int(7) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_number` (`employee_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE `ohrm_performance_review_template` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `templatevalue` text CHARACTER SET utf8 COLLATE utf8_bin,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE `ohrm_reviewer_360` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `review_id` int(7) DEFAULT NULL,
  `employee_number` int(7) DEFAULT NULL,
  `status` int(7) DEFAULT NULL,
  `reviewer_group_id` int(7) DEFAULT NULL,
  `completed_date` DATETIME DEFAULT NULL,
  `comment` text CHARACTER SET utf8 COLLATE utf8_bin,
  PRIMARY KEY (`id`),
  KEY `review_id` (`review_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE `ohrm_reviewer_group_360` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `piority` int(7) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `ohrm_reviewer_rating_360` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rating` double DEFAULT NULL,
  `kpi_id` int(7) DEFAULT NULL,
  `review_id` int(7) DEFAULT NULL,
  `reviewer_id` int(7) NOT NULL,
  `comment` text CHARACTER SET utf8 COLLATE utf8_bin,
  PRIMARY KEY (`id`),
  KEY `review_id` (`review_id`),
  KEY `reviewer_id` (`reviewer_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `ohrm_reviewer_group_360` (`id`, `name`,`piority`) VALUES
(1, 'Supervisor',4),
(2, 'Subordinate',2),
(3, 'Employee',1),
(4, 'Peer',3);

ALTER TABLE `ohrm_performance_review_360`
  ADD CONSTRAINT FOREIGN KEY (`employee_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE ;

ALTER TABLE `ohrm_reviewer_360`
  ADD CONSTRAINT FOREIGN KEY (`review_id`) REFERENCES `ohrm_performance_review_360` (`id`) ON DELETE CASCADE ;

ALTER TABLE `ohrm_reviewer_rating_360`
  ADD CONSTRAINT FOREIGN KEY (`reviewer_id`) REFERENCES `ohrm_reviewer_360` (`id`) ON DELETE CASCADE ;
ALTER TABLE `ohrm_reviewer_rating_360`
  ADD CONSTRAINT FOREIGN KEY (`review_id`) REFERENCES `ohrm_performance_review_360` (`id`) ON DELETE CASCADE ;

-- droping base performance module menu items

DELETE FROM ohrm_menu_item WHERE `menu_title` = 'KPI List' AND `level` = 2;
DELETE FROM ohrm_menu_item WHERE `menu_title` = 'Add KPI' AND `level` = 2;
DELETE FROM ohrm_menu_item WHERE `menu_title` = 'Copy KPI' AND `level` = 2;
DELETE FROM ohrm_menu_item WHERE `menu_title` = 'Add Review' AND `level` = 2;
DELETE FROM ohrm_menu_item WHERE `menu_title` = 'Reviews' AND `level` = 2;
-- adding performance 360 menu items

SET @admin_role_id := (SELECT `id` FROM ohrm_user_role WHERE `name` = 'Admin');  
SET @ess_role_id := (SELECT `id` FROM ohrm_user_role WHERE `name` = 'ESS');
SET @supervisor_role_id := (SELECT `id` FROM ohrm_user_role WHERE `name` = 'Supervisor');

INSERT INTO `ohrm_module` (`name`, `status`) VALUES ('performance', 1);  

SET @module_id := (SELECT LAST_INSERT_ID());

INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES  
('Save KPI', @module_id, 'saveKpi360');  
SET @save_kpi_screen_id := (SELECT LAST_INSERT_ID());  

INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES  
('Saearch KPI', @module_id, 'searchKpi360');  
SET @search_kpi_screen_id := (SELECT LAST_INSERT_ID());  

INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES  
('Search KPI Group', @module_id, 'searchKpiGroup360');  
SET @search_kpi_group_screen_id := (SELECT LAST_INSERT_ID());

INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES  
('My Reviews', @module_id, 'myPerformanceReview360');  
SET @my_reviews_screen_id := (SELECT LAST_INSERT_ID());  

INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES  
('View Progress', @module_id, 'performanceReviewProgress');  
SET @review_progress_screen_id := (SELECT LAST_INSERT_ID()); 

INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES  
('Add Review', @module_id, 'saveReview360');  
SET @add_review_screen_id := (SELECT LAST_INSERT_ID());

INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES  
('Review Evaluate', @module_id, 'reviewEvaluate360');  
SET @review_evaluate_screen_id := (SELECT LAST_INSERT_ID());

INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES  
('Review Evaluate By Admin', @module_id, 'reviewEvaluateByAdmin360');  
SET @review_evaluate_admin_screen_id := (SELECT LAST_INSERT_ID());

INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES  
('Search Evaluate Performance', @module_id, 'searchEvaluatePerformancReview360');  
SET @search_evaluate_performance_screen_id := (SELECT LAST_INSERT_ID());

INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES  
('Search Performance Review', @module_id, 'searchPerformancReview360');  
SET @search_performance_review_screen_id := (SELECT LAST_INSERT_ID());

SET @performance_menu_id := (SELECT `id` FROM ohrm_menu_item WHERE `menu_title` = 'Performance' AND `level` = 1); 

INSERT INTO ohrm_menu_item (`menu_title`, `screen_id`, `parent_id`, `level`, `order_hint`, `url_extras`, `status`) VALUES  
('Configure KPI', NULL, @performance_menu_id, 2, 400, '', 1);
SET @ConfigureKPI_screen_id := (SELECT LAST_INSERT_ID());

INSERT INTO ohrm_menu_item (`menu_title`, `screen_id`, `parent_id`, `level`, `order_hint`, `url_extras`, `status`) VALUES  
('Manage Reviews', NULL, @performance_menu_id, 2, 500, '', 1);
SET @Manage_Reviews_screen_id := (SELECT LAST_INSERT_ID());


INSERT INTO ohrm_menu_item (`menu_title`, `screen_id`, `parent_id`, `level`, `order_hint`, `url_extras`, `status`) VALUES  
('KPI', @search_kpi_screen_id, @ConfigureKPI_screen_id, 3, 100, '', 1),
('KPI Group', @search_kpi_group_screen_id, @ConfigureKPI_screen_id, 3, 200, '', 1),
('Appraisal Header', @appraisal_header_screen_id, @ConfigureKPI_screen_id, 3, 300, '', 1),  
('Manage Reviews', @search_performance_review_screen_id, @Manage_Reviews_screen_id, 3, 400, '', 1),
('My Reviews', @my_reviews_screen_id, @Manage_Reviews_screen_id, 3, 500, '', 1),
('Review List', @search_evaluate_performance_screen_id, @Manage_Reviews_screen_id, 3, 600, '', 1);
      
INSERT INTO ohrm_user_role_screen (user_role_id, screen_id, can_read, can_create, can_update, can_delete) VALUES  
(@admin_role_id, @save_kpi_screen_id, 1, 1, 1, 0),
(@admin_role_id, @search_kpi_screen_id, 1, 1, 1, 1),
(@admin_role_id, @my_reviews_screen_id, 1, 0, 1, 0),
(@admin_role_id, @review_progress_screen_id, 1, 0, 0, 0),
(@admin_role_id, @add_review_screen_id, 1, 1, 1, 0),
(@admin_role_id, @review_evaluate_admin_screen_id, 1, 1, 1, 0),
(@admin_role_id, @search_performance_review_screen_id, 1, 1, 1, 1),
(@ess_role_id, @search_evaluate_performance_screen_id, 1, 0, 1, 0),
(@ess_role_id, @review_evaluate_screen_id, 1, 1, 1, 0),
(@ess_role_id, @my_reviews_screen_id, 1, 0, 1, 0),
(@supervisor_role_id, @review_progress_screen_id, 1, 0, 0, 0),
(@supervisor_role_id, @review_evaluate_admin_screen_id, 1, 1, 1, 0);

UPDATE ohrm_menu_item SET `screen_id` =  @my_reviews_screen_id WHERE `menu_title` = 'Performance' AND `level` = 1;


UPDATE ohrm_menu_item SET `order_hint` =  500 WHERE `menu_title` = 'Manage Reviews' ;
UPDATE ohrm_menu_item SET `order_hint` =  600 WHERE `menu_title` = 'My Reviews' ;
UPDATE ohrm_menu_item SET `order_hint` =  700 WHERE `menu_title` = 'Review List' ;
