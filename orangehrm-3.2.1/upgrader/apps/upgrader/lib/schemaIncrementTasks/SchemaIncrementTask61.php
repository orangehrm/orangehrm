<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

/**
 * Description of SchemaIncrementTask61
 */
class SchemaIncrementTask61 extends SchemaIncrementTask {

    public function getNotes() {
        
    }

    public function getUserInputWidgets() {
        
    }

    public function loadSql() {
        $sql = array();

        $sql[] = "DROP FUNCTION IF EXISTS dashboard_get_subunit_parent_id;";

        $sql[] = "CREATE FUNCTION  dashboard_get_subunit_parent_id
                (
                  id INT
                )
                RETURNS INT
                DETERMINISTIC
                READS SQL DATA
                BEGIN
                SELECT (SELECT t2.id 
                               FROM ohrm_subunit t2 
                               WHERE t2.lft < t1.lft AND t2.rgt > t1.rgt    
                               ORDER BY t2.rgt-t1.rgt ASC LIMIT 1) INTO @parent
                FROM ohrm_subunit t1 WHERE t1.id = id;

                RETURN @parent;

                END;";

        $sql[] = "CREATE TABLE `ohrm_kpi` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `job_title_code` varchar(10) DEFAULT NULL,
                `kpi_indicators` varchar(255) DEFAULT NULL,
                `min_rating` int(7) DEFAULT 0,
                `max_rating` int(7) DEFAULT 0,
                `default_kpi` smallint(1) DEFAULT NULL,
                `deleted_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`)
              )ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

        $sql[] = "CREATE TABLE `ohrm_performance_review` (
                    `id` int(7) NOT NULL AUTO_INCREMENT,
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
                    `final_rate` DECIMAL(18, 2) DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    KEY `employee_number` (`employee_number`)
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

        $sql[] = "CREATE TABLE `ohrm_reviewer` (
                    `id` int(7) NOT NULL AUTO_INCREMENT,
                    `review_id` int(7) DEFAULT NULL,
                    `employee_number` int(7) DEFAULT NULL,
                    `status` int(7) DEFAULT NULL,
                    `reviewer_group_id` int(7) DEFAULT NULL,
                    `completed_date` DATETIME DEFAULT NULL,
                    `comment` text CHARACTER SET utf8 COLLATE utf8_bin,
                    PRIMARY KEY (`id`),
                    KEY `review_id` (`review_id`)
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

        $sql[] = "CREATE TABLE `ohrm_reviewer_group` (
                    `id` int(7) NOT NULL AUTO_INCREMENT,
                    `name` varchar(50) DEFAULT NULL,
                    `piority` int(7) DEFAULT NULL,
                    PRIMARY KEY (`id`)
                  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

        $sql[] = "CREATE TABLE `ohrm_reviewer_rating` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `rating` DECIMAL(18, 2) DEFAULT NULL,
                    `kpi_id` int(7) DEFAULT NULL,
                    `review_id` int(7) DEFAULT NULL,
                    `reviewer_id` int(7) NOT NULL,
                    `comment` text CHARACTER SET utf8 COLLATE utf8_bin,
                    PRIMARY KEY (`id`),
                    KEY `review_id` (`review_id`),
                    KEY `reviewer_id` (`reviewer_id`)
                  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

        $sql[] = "ALTER TABLE `ohrm_performance_review`
                  ADD CONSTRAINT FOREIGN KEY (`employee_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE ;";

        $sql[] = "ALTER TABLE `ohrm_reviewer`
                    ADD CONSTRAINT FOREIGN KEY (`review_id`) REFERENCES `ohrm_performance_review` (`id`) ON DELETE CASCADE ;";

        $sql[] = "ALTER TABLE `ohrm_reviewer_rating`
                  ADD CONSTRAINT FOREIGN KEY (`reviewer_id`) REFERENCES `ohrm_reviewer` (`id`) ON DELETE CASCADE ;";

        $sql[] = "ALTER TABLE `ohrm_reviewer_rating`
                  ADD CONSTRAINT FOREIGN KEY (`review_id`) REFERENCES `ohrm_performance_review` (`id`) ON DELETE CASCADE ;";

        $sql[] = "CREATE TABLE `ohrm_performance_track` ( 
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
                CONSTRAINT `ohrm_performance_track_fk1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE ON UPDATE CASCADE, 
                CONSTRAINT `ohrm_performance_track_fk2` FOREIGN KEY (`added_by`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE ON UPDATE CASCADE 
              ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

        $sql[] = "CREATE TABLE `ohrm_performance_tracker_log` ( 
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
                CONSTRAINT `fk_ohrm_performance_tracker_log_1` FOREIGN KEY (`user_id`) REFERENCES `ohrm_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE, 
                CONSTRAINT `ohrm_performance_tracker_log_fk1` FOREIGN KEY (`performance_track_id`) REFERENCES `ohrm_performance_track` (`id`) ON DELETE CASCADE ON UPDATE CASCADE, 
                CONSTRAINT `ohrm_performance_tracker_log_fk2` FOREIGN KEY (`reviewer_id`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE ON UPDATE CASCADE 
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $sql[] = "CREATE TABLE `ohrm_performance_tracker_reviewer` ( 
                `id` int(11) NOT NULL AUTO_INCREMENT, 
                `performance_track_id` int(11) NOT NULL, 
                `reviewer_id` int(7) NOT NULL, 
                `added_date` timestamp NULL DEFAULT NULL, 
                `status` int(2) DEFAULT NULL, 
                PRIMARY KEY (`id`), 
                KEY `ohrm_performance_tracker_reviewer_fk1_idx` (`performance_track_id`), 
                KEY `ohrm_performance_tracker_reviewer_fk2_idx` (`reviewer_id`), 
                CONSTRAINT `ohrm_performance_tracker_reviewer_fk1` FOREIGN KEY (`performance_track_id`) REFERENCES `ohrm_performance_track` (`id`) ON DELETE CASCADE ON UPDATE CASCADE, 
                CONSTRAINT `ohrm_performance_tracker_reviewer_fk2` FOREIGN KEY (`reviewer_id`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE ON UPDATE CASCADE 
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $sql[] = "SET @performance_module_id := (SELECT id FROM ohrm_module WHERE name = 'performance' LIMIT 1)";

        $sql[] = "SET @kpi_list_screen_id := (SELECT id FROM ohrm_screen WHERE name = 'KPI List' LIMIT 1)";
        $sql[] = "SET @save_kpi_screen_id := (SELECT id FROM ohrm_screen WHERE name = 'Add/Edit KPI' LIMIT 1)";
        $sql[] = "SET @copy_kpi_screen_id := (SELECT id FROM ohrm_screen WHERE name = 'Copy KPI' LIMIT 1)";
        $sql[] = "SET @save_review_screen_id := (SELECT id FROM ohrm_screen WHERE name = 'Add Review' LIMIT 1)";
        $sql[] = "SET @view_review_screen_id := (SELECT id FROM ohrm_screen WHERE name = 'Review List' LIMIT 1)";
        $sql[] = "SET @view_performance_screen_id := (SELECT id FROM ohrm_screen WHERE name = 'View Performance Module' LIMIT 1)";

        $sql[] = "DELETE FROM ohrm_user_role_screen WHERE screen_id = @view_performance_screen_id";
        $sql[] = "DELETE FROM ohrm_user_role_screen WHERE screen_id = @kpi_list_screen_id";
        $sql[] = "DELETE FROM ohrm_user_role_screen WHERE screen_id = @save_kpi_screen_id";
        $sql[] = "DELETE FROM ohrm_user_role_screen WHERE screen_id = @copy_kpi_screen_id";
        $sql[] = "DELETE FROM ohrm_user_role_screen WHERE screen_id = @save_review_screen_id";
        $sql[] = "DELETE FROM ohrm_user_role_screen WHERE screen_id = @view_review_screen_id";

        $sql[] = "DELETE FROM ohrm_menu_item WHERE screen_id = @view_performance_screen_id";
        $sql[] = "DELETE FROM ohrm_menu_item WHERE screen_id = @kpi_list_screen_id";
        $sql[] = "DELETE FROM ohrm_menu_item WHERE screen_id = @save_kpi_screen_id";
        $sql[] = "DELETE FROM ohrm_menu_item WHERE screen_id = @copy_kpi_screen_id";
        $sql[] = "DELETE FROM ohrm_menu_item WHERE screen_id = @save_review_screen_id";
        $sql[] = "DELETE FROM ohrm_menu_item WHERE screen_id = @view_review_screen_id";

        $sql[] = "DELETE FROM ohrm_screen WHERE id = @kpi_list_screen_id";
        $sql[] = "DELETE FROM ohrm_screen WHERE id = @save_kpi_screen_id";
        $sql[] = "DELETE FROM ohrm_screen WHERE id = @copy_kpi_screen_id";
        $sql[] = "DELETE FROM ohrm_screen WHERE id = @save_review_screen_id";
        $sql[] = "DELETE FROM ohrm_screen WHERE id = @view_review_screen_id";
        $sql[] = "DELETE FROM ohrm_screen WHERE id = @view_performance_screen_id";

        $sql[] = "DELETE FROM ohrm_module_default_page WHERE module_id = @performance_module_id";

        $sql[] = "DELETE FROM ohrm_module WHERE id = @performance_module_id";
        
        $sql[] = "DELETE FROM ohrm_email_notification WHERE name = 'Performance Review Submissions'";

        $sql[] = "SET @admin_user_role_id := (SELECT id FROM ohrm_user_role WHERE name = 'Admin' LIMIT 1);";
        $sql[] = "SET @ess_user_role_id := (SELECT id FROM ohrm_user_role WHERE name = 'ESS' LIMIT 1);";

        $sql[] = "SET @admin_home_page := (SELECT id FROM ohrm_home_page WHERE user_role_id = @admin_user_role_id LIMIT 1);";
        $sql[] = "SET @ess_home_page := (SELECT id FROM ohrm_home_page WHERE user_role_id = @ess_user_role_id LIMIT 1);";
        $sql[] = "UPDATE ohrm_home_page SET action = 'dashboard/index', priority = '15' WHERE user_role_id = @admin_home_page;";
        $sql[] = "UPDATE ohrm_home_page SET action = 'dashboard/index', priority = '5' WHERE user_role_id = @ess_home_page;";

        $sql[] = "INSERT INTO ohrm_module (name, status) VALUES ('dashboard', 1);";
        $sql[] = "SET @dashboard_module := (SELECT id FROM ohrm_module WHERE name = 'dashboard' LIMIT 1);";

        $sql[] = "INSERT INTO ohrm_screen (name, module_id, action_url) VALUES ('Dashboard', @dashboard_module, 'index');";
        $sql[] = "SET @dashboard_screen := (SELECT id FROM ohrm_screen WHERE name = 'Dashboard' LIMIT 1);";

        $sql[] = "INSERT INTO ohrm_menu_item (menu_title, screen_id, parent_id, level, order_hint, url_extras, status) VALUES
('Dashboard', @dashboard_screen, NULL, 1, 800, NULL, 1);";

        $sql[] = "INSERT INTO ohrm_user_role_screen (user_role_id, screen_id, can_read, can_create, can_update, can_delete) VALUES
(@admin_user_role_id, @dashboard_screen, 1, 0, 0, 0),
(@ess_user_role_id, @dashboard_screen, 1, 0, 0, 0);";

        $sql[] = "INSERT INTO `ohrm_reviewer_group` (`id`, `name`,`piority`) VALUES
(1, 'Supervisor',1),
(2, 'Employee',2);";

        $sql[] = "SET @admin_role_id := (SELECT `id` FROM ohrm_user_role WHERE `name` = 'Admin');";
        $sql[] = "SET @ess_role_id := (SELECT `id` FROM ohrm_user_role WHERE `name` = 'ESS');";
        $sql[] = "SET @supervisor_role_id := (SELECT `id` FROM ohrm_user_role WHERE `name` = 'Supervisor');";

        $sql[] = "INSERT INTO `ohrm_module` (`name`, `status`) VALUES ('performance', 1);";
        $sql[] = "SET @module_id := (SELECT LAST_INSERT_ID());";

        $sql[] = "INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES  
('Save KPI', @module_id, 'saveKpi');";
        $sql[] = "SET @save_kpi_screen_id := (SELECT LAST_INSERT_ID());";

        $sql[] = "INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES  
('Saearch KPI', @module_id, 'searchKpi');";
        $sql[] = "SET @search_kpi_screen_id := (SELECT LAST_INSERT_ID());";

        $sql[] = "INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES  
('My Reviews', @module_id, 'myPerformanceReview');";
        $sql[] = "SET @my_reviews_screen_id := (SELECT LAST_INSERT_ID());";

        $sql[] = "INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES  
('Add Review', @module_id, 'saveReview');";
        $sql[] = "SET @add_review_screen_id := (SELECT LAST_INSERT_ID());";

        $sql[] = "INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES  
('Review Evaluate', @module_id, 'reviewEvaluate');";
        $sql[] = "SET @review_evaluate_screen_id := (SELECT LAST_INSERT_ID());";

        $sql[] = "INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES  
('Review Evaluate By Admin', @module_id, 'reviewEvaluateByAdmin');";
        $sql[] = "SET @review_evaluate_admin_screen_id := (SELECT LAST_INSERT_ID());";

        $sql[] = "INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES  
('Search Evaluate Performance', @module_id, 'searchEvaluatePerformancReview');";
        $sql[] = "SET @search_evaluate_performance_screen_id := (SELECT LAST_INSERT_ID());";

        $sql[] = "INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES  
('Search Performance Review', @module_id, 'searchPerformancReview');";
        $sql[] = "SET @search_performance_review_screen_id := (SELECT LAST_INSERT_ID());";

        $sql[] = "INSERT INTO ohrm_menu_item (`menu_title`, `screen_id`, `parent_id`, `level`, `order_hint`, `url_extras`, `status`) VALUES  
('Performance', NULL, NULL, 1, 700, '', 1);";
        $sql[] = "SET @performance_menu_id := (SELECT `id` FROM ohrm_menu_item WHERE `menu_title` = 'Performance' AND `level` = 1);";

        $sql[] = "INSERT INTO ohrm_menu_item (`menu_title`, `screen_id`, `parent_id`, `level`, `order_hint`, `url_extras`, `status`) VALUES  
('Configure', NULL, @performance_menu_id, 2, 100, '', 1);";
        $sql[] = "SET @ConfigureKPI_screen_id := (SELECT LAST_INSERT_ID());";

        $sql[] = "INSERT INTO ohrm_menu_item (`menu_title`, `screen_id`, `parent_id`, `level`, `order_hint`, `url_extras`, `status`) VALUES  
('Manage Reviews', NULL, @performance_menu_id, 2, 200, '', 1);";
        $sql[] = "SET @Manage_Reviews_screen_id := (SELECT LAST_INSERT_ID());";

        $sql[] = "INSERT INTO ohrm_menu_item (`menu_title`, `screen_id`, `parent_id`, `level`, `order_hint`, `url_extras`, `status`) VALUES  
('KPIs', @search_kpi_screen_id, @ConfigureKPI_screen_id, 3, 100, '', 1),
('Manage Reviews', @search_performance_review_screen_id, @Manage_Reviews_screen_id, 3, 100, '', 1),
('My Reviews', @my_reviews_screen_id, @Manage_Reviews_screen_id, 3, 200, '', 1),
('Review List', @search_evaluate_performance_screen_id, @Manage_Reviews_screen_id, 3, 300, '', 1);";

        $sql[] = "INSERT INTO ohrm_user_role_screen (user_role_id, screen_id, can_read, can_create, can_update, can_delete) VALUES  
(@admin_role_id, @save_kpi_screen_id, 1, 1, 1, 0),
(@admin_role_id, @search_kpi_screen_id, 1, 1, 1, 1),
(@admin_role_id, @add_review_screen_id, 1, 1, 1, 0),
(@admin_role_id, @review_evaluate_admin_screen_id, 1, 1, 1, 0),
(@admin_role_id, @search_performance_review_screen_id, 1, 1, 1, 1),
(@ess_role_id, @search_evaluate_performance_screen_id, 1, 0, 1, 0),
(@ess_role_id, @review_evaluate_screen_id, 1, 1, 1, 0),
(@ess_role_id, @my_reviews_screen_id, 1, 0, 1, 0),
(@supervisor_role_id, @review_evaluate_admin_screen_id, 1, 1, 1, 0),
(@ess_role_id, @review_evaluate_admin_screen_id, 1, 1, 1, 0);";

        $sql[] = "SET @admin_user_role := (SELECT id FROM ohrm_user_role WHERE name = 'Admin');";
        $sql[] = "SET @ess_user_role := (SELECT id FROM ohrm_user_role WHERE name = 'ESS');";

        $sql[] = "INSERT INTO `ohrm_module`( `name`, `status`) VALUES
('performanceTracker', 1);";
        $sql[] = "SET @performance_module_id:= (SELECT LAST_INSERT_ID());";

        $sql[] = "INSERT INTO `ohrm_screen` (`name`, `module_id`, `action_url`) VALUES
( 'Manage_Trackers', @performance_module_id, 'addPerformanceTracker');";
        $sql[] = "SET @manage_performance_trackers_screen_id :=  (SELECT LAST_INSERT_ID());";

        $sql[] = "SET @performance_menu_id:= (SELECT id FROM ohrm_menu_item where menu_title = 'Performance');";
        $sql[] = "INSERT INTO ohrm_menu_item (`menu_title`, `screen_id`, `parent_id`, `level`, `order_hint`, `url_extras`, `status`) VALUES
('Trackers', @manage_performance_trackers_screen_id, @ConfigureKPI_screen_id, 3, 200, NULL, 1);";

        $sql[] = "INSERT INTO ohrm_user_role_screen (user_role_id, screen_id, can_read, can_create, can_update, can_delete) VALUES
(@admin_user_role, @manage_performance_trackers_screen_id, 1, 1, 1, 1),
(@ess_user_role, @manage_performance_trackers_screen_id, 0, 0, 0, 0);";

        $sql[] = "INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES
( 'Employee_Trackers', @performance_module_id, 'viewEmployeePerformanceTrackerList');";
        $sql[] = "SET @employee_trackers_screen_id :=  (SELECT LAST_INSERT_ID());";
        $sql[] = "INSERT INTO ohrm_menu_item (`menu_title`, `screen_id`, `parent_id`, `level`, `order_hint`, `url_extras`, `status`) VALUES
('Employee Trackers', @employee_trackers_screen_id, @performance_menu_id, 2, 800, NULL, 1);";

        $sql[] = "INSERT INTO ohrm_user_role_screen (user_role_id, screen_id, can_read, can_create, can_update, can_delete) VALUES
(@admin_user_role, @employee_trackers_screen_id, 1, 1, 1, 1),
(@ess_user_role, @employee_trackers_screen_id, 1, 1, 1, 0);";

        $sql[] = "INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES
( 'My_Trackers', @performance_module_id, 'viewMyPerformanceTrackerList');";
        $sql[] = "SET @my_trackers_screen_id :=  (SELECT LAST_INSERT_ID());";

        $sql[] = "INSERT INTO ohrm_menu_item (`menu_title`, `screen_id`, `parent_id`, `level`, `order_hint`, `url_extras`, `status`) VALUES
('My Trackers', @my_trackers_screen_id, @performance_menu_id, 2, 700, NULL, 1);";

        $sql[] = "INSERT INTO ohrm_user_role_screen (user_role_id, screen_id, can_read, can_create, can_update, can_delete) VALUES
(@admin_user_role, @my_trackers_screen_id, 0, 0, 0, 0),
(@ess_user_role, @my_trackers_screen_id, 1, 0, 1, 0);";

        $sql[] = "INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES
( 'Employee_Tracker_Logs', @performance_module_id, 'addPerformanceTrackerLog');";
        $sql[] = "SET @employee_tracker_logs_screen_id :=  (SELECT LAST_INSERT_ID());";

        $sql[] = "INSERT INTO ohrm_user_role_screen (user_role_id, screen_id, can_read, can_create, can_update, can_delete) VALUES
(@admin_user_role, @employee_tracker_logs_screen_id, 1, 1, 1, 0),
(@ess_user_role, @employee_tracker_logs_screen_id, 1, 0, 0, 0);";

        $this->sql = $sql;
    }

    public function setUserInputs() {
        
    }

    public function execute() {
        $this->incrementNumber = 61;
        parent::execute();

        $result = array();

        foreach ($this->sql as $sql) {
            $result[] = $this->upgradeUtility->executeSql($sql);
        }

        $this->upgradeOSData();

        $this->checkTransactionComplete($result);
        $this->updateOhrmUpgradeInfo($this->transactionComplete, $this->incrementNumber);
        $this->upgradeUtility->finalizeTransaction($this->transactionComplete);
        $this->upgradeUtility->closeDbConnection();
    }

    public function upgradeOSData() {
        $this->transferKpis();
        $this->transferReviews();
    }

    public function transferKpis() {
        $sql = "SELECT * FROM `hs_hr_kpi`";
        $result = $this->upgradeUtility->executeSql($sql);
        while ($row = mysqli_fetch_array($result)) {
            $kpiId = $row['id'];
            $jobTitleCode = $row['job_title_code'];
            $description = $row['description'];
            $rateMin = $row['rate_min'];
            $rateMax = $row['rate_max'];
            $rateDefault = $row['rate_default'];
            $isActive = $row['is_active'];

            if ($rateDefault != '1') {
                $rateDefault = 'NULL';
            }

            if ($isActive == '1') {
                $isActive = 'null';
            } else {
                $isActive = "'" . date('Y-m-d') . "'";
            }

            $this->upgradeUtility->executeSql("INSERT INTO `ohrm_kpi`(`id`, `job_title_code`, `kpi_indicators`, `min_rating`, `max_rating`, `default_kpi`,`deleted_at`) VALUES "
                    . "(" . $this->upgradeUtility->escapeString($kpiId) . ", " . $this->upgradeUtility->escapeString($jobTitleCode) . ",'" . $this->upgradeUtility->escapeString($description) . "'," . $this->upgradeUtility->escapeString($rateMin) . "," . $this->upgradeUtility->escapeString($rateMax) . "," . $this->upgradeUtility->escapeString($rateDefault) . ",$isActive)");
        }
    }

    public function transferReviews() {
        $sql = "SELECT * FROM `hs_hr_performance_review`";
        $result = $this->upgradeUtility->executeSql($sql);
        while ($row = mysqli_fetch_array($result)) {
            $reviewId = $row['id'];
            $employeeId = $row['employee_id'];
            $reviewerId = $row['reviewer_id'];
            $jobTitleCode = $row['job_title_code'];
            $subDivisionId = $row['sub_division_id'];
            $creationDate = "'" . date('Y-m-d', strtotime($row['creation_date'])) . "'";
            $periodFrom = "'" . date('Y-m-d', strtotime($row['period_from'])) . "'";
            $periodTo = "'" . date('Y-m-d', strtotime($row['period_to'])) . "'";
            $dueDate = "'" . date('Y-m-d', strtotime($row['due_date'])) . "'";
            $state = $row['state'];
            $kpis = $row['kpis'];

            $stateId = $this->getStateId($state);

            if ($stateId == 4) {
                $completedDate = $dueDate;
            } else {
                $completedDate = 'null';
            }

            if ($stateId >= 2) {
                $activatedDate = $creationDate;
            } else {
                $activatedDate = 'null';
            }

            $this->upgradeUtility->executeSql("INSERT INTO `ohrm_performance_review`(`id`, `status_id`, `employee_number`, `work_period_start`, `work_period_end`, `job_title_code`, `department_id`, `due_date`, `completed_date`, `activated_date`) VALUES"
                    . "(" . $this->upgradeUtility->escapeString($reviewId) . ", " . $this->upgradeUtility->escapeString($stateId) . "," . $this->upgradeUtility->escapeString($employeeId) . "," . $periodFrom . "," . $periodTo . "," . $this->upgradeUtility->escapeString($jobTitleCode) . "," . $this->upgradeUtility->escapeString($subDivisionId) . "," . $dueDate . "," . $completedDate . "," . $activatedDate . ")");

            $reviewerState = $this->getReviewerState($state);
            $this->upgradeUtility->executeSql("INSERT INTO `ohrm_reviewer`(`review_id`, `employee_number`, `status`, `reviewer_group_id`, `completed_date`, `comment`) VALUES "
                    . "(" . $this->upgradeUtility->escapeString($reviewId) . "," . $this->upgradeUtility->escapeString($reviewerId) . "," . $this->upgradeUtility->escapeString($reviewerState) . "," . $this->upgradeUtility->escapeString('1') . "," . $completedDate . "," . $this->upgradeUtility->escapeString('NULL') . ")");

            if ($stateId >= 2) {
                $this->transferKpisToReviewer($reviewId, $reviewerId, $state, $completedDate, $kpis);
            }
        }
    }

    public function getStateId($state) {
        switch ($state) {
            case 1:
                return 2;
                break;
            case 3:

                return 3;
                break;
            case 5:
                return 3;
                break;
            case 7:
                return 1;
                break;
            case 9:
                return 4;
                break;
            default:
                return 1;
                break;
        }
    }

    public function transferKpisToReviewer($reviewId, $reviewerId, $state, $completedDate, $kpisXml) {
        $xmlDoc = new DOMDocument();
        $xmlDoc->loadXML($kpisXml);
        $x = $xmlDoc->documentElement;
        $kpis = array();
        $count = 0;
        if ($x->hasChildNodes()) {
            foreach ($x->childNodes as $item) {
                if ($item->hasChildNodes()) {
                    foreach ($item->childNodes as $childItem) {
                        if ($childItem->hasChildNodes()) {
                            foreach ($childItem->childNodes as $grandChildItem) {
                                if ($grandChildItem->nodeName == 'id') {
                                    $kpis[$count]['id'] = $grandChildItem->nodeValue;
                                } else if ($grandChildItem->nodeName == 'desc') {
                                    $kpis[$count]['description'] = $grandChildItem->nodeValue;
                                } else if ($grandChildItem->nodeName == 'rate') {
                                    $kpis[$count]['rate'] = $grandChildItem->nodeValue;
                                } else if ($grandChildItem->nodeName == 'comment') {
                                    $kpis[$count]['comment'] = trim($grandChildItem->nodeValue);
                                } else if ($grandChildItem->nodeName == 'min') {
                                    $kpis[$count]['minrate'] = $grandChildItem->nodeValue;
                                } else if ($grandChildItem->nodeName == 'max') {
                                    $kpis[$count]['maxrate'] = $grandChildItem->nodeValue;
                                }
                            }
                            $count++;
                        }
                    }
                }
            }
        }
        $reviewerReviewId = $this->upgradeUtility->executeSql("SELECT `id` FROM `ohrm_reviewer` WHERE `review_id`=$reviewId AND `employee_number`=$reviewerId LIMIT 1");
        if (mysqli_num_rows($reviewerReviewId) > 0) {
            $finalRate = 0;
            $reviewerReviewId = mysqli_fetch_array($reviewerReviewId, MYSQLI_ASSOC);
            foreach ($kpis as $kpi) {
                if ($kpi['rate'] != "") {
                    $rate = $kpi['rate'];
                } else {
                    $rate = 0;
                }
                $this->upgradeUtility->executeSql("INSERT INTO `ohrm_reviewer_rating`(`rating`, `kpi_id`, `review_id`, `reviewer_id`, `comment`) VALUES "
                        . "(" . ($rate > 0 ? $rate : '0') . "," . $this->upgradeUtility->escapeString($kpi['id']) . "," . $this->upgradeUtility->escapeString($reviewId) . "," . $this->upgradeUtility->escapeString($reviewerReviewId['id']) . ",'" . $this->upgradeUtility->escapeString($kpi['comment']) . "')");
            }
        }
    }

    public function getReviewerState($state) {
        switch ($state) {
            case 1:
                return 1;
                break;
            case 3:

                return 2;
                break;
            case 5:
                return 2;
                break;
            case 7:
                return 1;
                break;
            case 9:
                return 3;
                break;
            default:
                return 1;
                break;
        }
    }

}
