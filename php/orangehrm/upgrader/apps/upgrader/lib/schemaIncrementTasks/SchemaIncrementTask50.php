<?php
include_once 'SchemaIncrementTask.php';

class SchemaIncrementTask50 extends SchemaIncrementTask {
    
    public $userInputs;
    
    public function execute() {
        $this->incrementNumber = 50;
        parent::execute();
        
        for($i = 0; $i <= 15; $i++) {
            $result[] = $this->upgradeUtility->executeSql($this->sql[$i]);
        }
        
        $result[] = $this->insertOhrmWorkWeek();
        
        for($i = 17; $i <= 34; $i++) {
            $result[] = $this->upgradeUtility->executeSql($this->sql[$i]);
        }
        
        $result[] = $this->updateHsHrEmpPicture();
        
        $this->checkTransactionComplete($result);
        $this->updateOhrmUpgradeInfo($this->transactionComplete, $this->incrementNumber);
        $this->upgradeUtility->finalizeTransaction($this->transactionComplete);
        $this->upgradeUtility->closeDbConnection();
    }
    
    
    
    public function getUserInputWidgets() {
        
    }
    
    public function setUserInputs() {
        
    }
    
    public function loadSql() {
    
        $sql[0] = "ALTER TABLE hs_hr_emp_picture 
                    ADD column epic_file_width varchar(20) default null,
                    ADD column epic_file_height varchar(20) default null";
        
        $sql[1] = "CREATE  TABLE ohrm_operational_country (
                    id INT UNSIGNED NOT NULL AUTO_INCREMENT ,
                    country_code CHAR(2) DEFAULT NULL,
                    PRIMARY KEY (id)    
                   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
        
        $sql[2] = "ALTER TABLE hs_hr_leavetype 
                    ADD column operational_country_id int unsigned default null,
                    add foreign key (operational_country_id) references ohrm_operational_country(id) on delete set null";
        
        $sql[3] = "CREATE  TABLE ohrm_module (
                    id int not null auto_increment,
                    name varchar(120) default null,
                    status tinyint default 1,
                    primary key  (id)
                   ) engine=innodb default charset=utf8;";
        
        $sql[4] = "RENAME TABLE hs_hr_holidays  
                    TO ohrm_holiday;";
        
        $sql[5] = "ALTER TABLE ohrm_holiday 
                    CHANGE holiday_id id int unsigned AUTO_INCREMENT,
                    CHANGE recurring recurring tinyint unsigned default '0',
                    CHANGE length length int unsigned,
                    ADD column operational_country_id int unsigned default null,
                    DROP INDEX holiday_id,
                    ADD primary key (id),
                    ADD CONSTRAINT fk_ohrm_holiday_ohrm_operational_country
                    FOREIGN KEY (operational_country_id)
                    REFERENCES ohrm_operational_country (id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE;";
        
        $sql[6] = "ALTER TABLE ohrm_operational_country
                    ADD CONSTRAINT fk_ohrm_operational_country_hs_hr_country
                    FOREIGN KEY (country_code)
                    REFERENCES hs_hr_country (cou_code)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE;";
        
        $sql[7] = "CREATE  TABLE `ohrm_work_week` (
                    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
                    `operational_country_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
                    `mon` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
                    `tue` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
                    `wed` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
                    `thu` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
                    `fri` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
                    `sat` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
                    `sun` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
                    PRIMARY KEY (`id`)
                   ) ENGINE = InnoDB;";
        
        $sql[8] = "ALTER TABLE `ohrm_work_week`
                    ADD CONSTRAINT `fk_ohrm_work_week_ohrm_operational_country`
                    FOREIGN KEY (`operational_country_id`)
                    REFERENCES `ohrm_operational_country` (`id`)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE;";
        
        $sql[9] = "UPDATE hs_hr_currency_type SET currency_name = 'CFP Franc'
                    WHERE code = '164'";
        
        $sql[10] = "DELETE FROM hs_hr_unique_id WHERE table_name='hs_hr_holidays' AND field_name='holiday_id'";
        
        $row[1] = 'SELECT selectCondition FROM ohrm_project_activity LEFT JOIN (SELECT * FROM ohrm_timesheet_item WHERE whereCondition1) AS ohrm_timesheet_item  ON (ohrm_timesheet_item.activity_id = ohrm_project_activity.activity_id) LEFT JOIN ohrm_project ON (ohrm_project.project_id = ohrm_project_activity.project_id) LEFT JOIN hs_hr_employee ON (hs_hr_employee.emp_number = ohrm_timesheet_item.employee_id) LEFT JOIN ohrm_timesheet ON (ohrm_timesheet.timesheet_id = ohrm_timesheet_item.timesheet_id) LEFT JOIN ohrm_customer ON (ohrm_customer.customer_id = ohrm_project.customer_id) WHERE whereCondition2 groupByClause ORDER BY ohrm_customer.name, ohrm_project.name, ohrm_project_activity.name, hs_hr_employee.emp_lastname, hs_hr_employee.emp_firstname';
        $row[2] = 'SELECT selectCondition FROM hs_hr_employee LEFT JOIN (SELECT * FROM ohrm_attendance_record WHERE ( ( ohrm_attendance_record.punch_in_user_time BETWEEN "#@fromDate@,@1970-01-01@#" AND #@"toDate"@,@CURDATE()@# ) AND ( ohrm_attendance_record.punch_out_user_time BETWEEN "#@fromDate@,@1970-01-01@#" AND #@"toDate"@,@CURDATE()@# ) ) ) AS ohrm_attendance_record ON (hs_hr_employee.emp_number = ohrm_attendance_record.employee_id) WHERE hs_hr_employee.emp_number = #@employeeId@,@hs_hr_employee.emp_number AND (hs_hr_employee.termination_id is null) @# AND (hs_hr_employee.job_title_code = #@"jobTitle")@,@hs_hr_employee.job_title_code OR hs_hr_employee.job_title_code is null)@# AND (hs_hr_employee.work_station IN (#@subUnit)@,@SELECT id FROM ohrm_subunit) OR hs_hr_employee.work_station is null@#) AND (hs_hr_employee.emp_status = #@"employeeStatus")@,@hs_hr_employee.emp_status OR hs_hr_employee.emp_status is null)@# groupByClause ORDER BY hs_hr_employee.emp_lastname, hs_hr_employee.emp_firstname';
        $row[3] = 'SELECT selectCondition FROM hs_hr_employee 
                    LEFT JOIN hs_hr_emp_emergency_contacts ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_emergency_contacts.emp_number) 
                    LEFT JOIN ohrm_subunit ON 
                        (hs_hr_employee.work_station = ohrm_subunit.id) 
                    LEFT JOIN ohrm_employment_status ON 
                        (hs_hr_employee.emp_status = ohrm_employment_status.id) 
                    LEFT JOIN ohrm_job_title ON
                        (hs_hr_employee.job_title_code = ohrm_job_title.id)
                    LEFT JOIN ohrm_job_category ON 
                        (hs_hr_employee.eeo_cat_code = ohrm_job_category.id) 
                    LEFT JOIN ohrm_nationality ON
                        (hs_hr_employee.nation_code = ohrm_nationality.id)
                    LEFT JOIN hs_hr_emp_dependents ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_dependents.emp_number)
                    LEFT JOIN hs_hr_emp_locations AS emp_location ON
                        (hs_hr_employee.emp_number = emp_location.emp_number)
                    LEFT JOIN ohrm_location ON
                        (emp_location.location_id = ohrm_location.id)
                    LEFT JOIN hs_hr_emp_contract_extend ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_contract_extend.emp_number) 
                    LEFT JOIN hs_hr_emp_basicsalary ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_basicsalary.emp_number) 
                    LEFT JOIN ohrm_pay_grade ON 
                        (hs_hr_emp_basicsalary.sal_grd_code = ohrm_pay_grade.id) 
                    LEFT JOIN hs_hr_currency_type ON 
                        (hs_hr_emp_basicsalary.currency_id = hs_hr_currency_type.currency_id) 
                    LEFT JOIN hs_hr_payperiod ON 
                        (hs_hr_emp_basicsalary.payperiod_code = hs_hr_payperiod.payperiod_code) 
                    LEFT JOIN hs_hr_emp_passport ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_passport.emp_number) 
                    LEFT JOIN hs_hr_emp_reportto AS subordinate_list ON 
                        (hs_hr_employee.emp_number = subordinate_list.erep_sup_emp_number) 
                    LEFT JOIN hs_hr_employee AS subordinate ON
                        (subordinate.emp_number = subordinate_list.erep_sub_emp_number)
                    LEFT JOIN ohrm_emp_reporting_method AS subordinate_reporting_method ON 
                        (subordinate_list.erep_reporting_mode = subordinate_reporting_method.reporting_method_id) 
                    LEFT JOIN hs_hr_emp_work_experience ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_work_experience.emp_number) 
                    LEFT JOIN ohrm_emp_education ON 
                        (hs_hr_employee.emp_number = ohrm_emp_education.emp_number) 
                    LEFT JOIN ohrm_education ON 
                        (ohrm_emp_education.education_id = ohrm_education.id) 
                    LEFT JOIN hs_hr_emp_skill ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_skill.emp_number) 
                    LEFT JOIN ohrm_skill ON 
                        (hs_hr_emp_skill.skill_id = ohrm_skill.id) 
                    LEFT JOIN hs_hr_emp_language ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_language.emp_number) 
                    LEFT JOIN ohrm_language ON 
                        (hs_hr_emp_language.lang_id = ohrm_language.id) 
                    LEFT JOIN ohrm_emp_license ON 
                        (hs_hr_employee.emp_number = ohrm_emp_license.emp_number) 
                    LEFT JOIN ohrm_license ON 
                        (ohrm_emp_license.license_id = ohrm_license.id) 
                    LEFT JOIN hs_hr_emp_member_detail ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_member_detail.emp_number) 
                    LEFT JOIN ohrm_membership ON
                        (hs_hr_emp_member_detail.membship_code = ohrm_membership.id)
                    LEFT JOIN hs_hr_country ON 
                        (hs_hr_employee.coun_code = hs_hr_country.cou_code) 
                    LEFT JOIN hs_hr_emp_directdebit ON 
                        (hs_hr_emp_basicsalary.id = hs_hr_emp_directdebit.salary_id) 
                    LEFT JOIN hs_hr_emp_reportto AS supervisor_list ON 
                        (hs_hr_employee.emp_number = supervisor_list.erep_sub_emp_number) 
                    LEFT JOIN hs_hr_employee AS supervisor ON
                        (supervisor.emp_number = supervisor_list.erep_sup_emp_number)
                    LEFT JOIN ohrm_emp_reporting_method AS supervisor_reporting_method ON 
                        (supervisor_list.erep_reporting_mode = supervisor_reporting_method.reporting_method_id) 
                    LEFT JOIN ohrm_emp_termination ON
                        (hs_hr_employee.termination_id = ohrm_emp_termination.id)
                    LEFT JOIN ohrm_emp_termination_reason ON
                        (ohrm_emp_termination.reason_id = ohrm_emp_termination_reason.id)
                WHERE hs_hr_employee.emp_number in (
                    SELECT hs_hr_employee.emp_number FROM hs_hr_employee
                        LEFT JOIN hs_hr_emp_basicsalary ON 
                            (hs_hr_employee.emp_number = hs_hr_emp_basicsalary.emp_number) 
                        LEFT JOIN ohrm_emp_education ON 
                            (hs_hr_employee.emp_number = ohrm_emp_education.emp_number) 
                        LEFT JOIN hs_hr_emp_skill ON 
                            (hs_hr_employee.emp_number = hs_hr_emp_skill.emp_number) 
                        LEFT JOIN hs_hr_emp_language ON 
                            (hs_hr_employee.emp_number = hs_hr_emp_language.emp_number) 
                    WHERE whereCondition1
                )
                GROUP BY 
                     hs_hr_employee.emp_number,
                     hs_hr_employee.emp_lastname,
                     hs_hr_employee.emp_firstname,
                     hs_hr_employee.emp_middle_name,
                     hs_hr_employee.emp_birthday,
                     ohrm_nationality.name,
                     hs_hr_employee.emp_gender,
                     hs_hr_employee.emp_marital_status,
                     hs_hr_employee.emp_dri_lice_num,
                     hs_hr_employee.emp_dri_lice_exp_date,
                     hs_hr_employee.emp_street1,
                     hs_hr_employee.emp_street2,
                     hs_hr_employee.city_code,
                     hs_hr_employee.provin_code,
                     hs_hr_employee.emp_zipcode,
                     hs_hr_country.cou_code,
                     hs_hr_employee.emp_hm_telephone,
                     hs_hr_employee.emp_mobile,
                     hs_hr_employee.emp_work_telephone,
                     hs_hr_employee.emp_work_email,
                     hs_hr_employee.emp_oth_email
                     ORDER BY hs_hr_employee.emp_lastname';
        
        $sql[11] = "UPDATE ohrm_report_group SET core_sql = CASE report_group_id
                        WHEN '1' THEN '$row[1]'
                        WHEN '2' THEN '$row[2]'
                        WHEN '3' THEN '$row[3]'
                        END
                        WHERE report_group_id in(1,2,3)";
        
        $sql[12] = "UPDATE ohrm_filter_field 
                        SET name = 'only_include_approved_timesheets' WHERE filter_field_id = 7";
        
        $sql[13] = "INSERT INTO `ohrm_display_field` (`report_group_id`, `name`, `label`, `field_alias`, `is_sortable`, `sort_order`, `sort_field`, `element_type`, `element_property`, `width`, `is_exportable`, `text_alignment_style`, `is_value_list`, `display_field_group_id`, `default_value`, `is_encrypted`, `is_meta`) VALUES
                        (3, 'ohrm_emp_termination.termination_date', 'Termination Date', 'terminationDate',  'false', null, null, 'label', '<xml><getter>terminationDate</getter></xml>', 100, '0', null, true, 6, '---', false, false),
                        (3, 'ohrm_emp_termination_reason.name', 'Termination Reason', 'terminationReason',  'false', null, null, 'label', '<xml><getter>terminationReason</getter></xml>', 100, '0', null, true, 6, '---', false, false);";
        
        // Not required here. This is done in schmatask 49.
        // $sql[14] = "INSERT INTO `ohrm_selected_filter_field` (`report_id`, `filter_field_id`, `filter_field_order`, `value1`, `value2`, `where_condition`, `type`) VALUES
        //                (5, 22, 1, null, null, 'IS NULL', 'Predefined');";
        
        $sql[14] = "UPDATE ohrm_summary_display_field 
                        SET label = 'Time (Hours)' WHERE summary_display_field_id = 1";
        
        $sql[15] = "INSERT INTO `ohrm_module` (`name`, `status`) VALUES
                        ('core', 1),
                        ('admin', 1),
                        ('pim', 1),
                        ('leave', 1),
                        ('time', 1),
                        ('attendance', 1),
                        ('recruitment', 1),
                        ('recruitmentApply', 1),
                        ('performance', 1),
                        ('benefits', 1);";
        
        $sql[16] = "SELECT * FROM hs_hr_weekends ORDER BY day";
        
        $sql[17] = "DROP TABLE hs_hr_weekends ;";
        
        $sql[18] = "CREATE TABLE IF NOT EXISTS `ohrm_upgrade_history` (
                      `id` int(10) not null auto_increment,
                      `start_version` varchar(30) DEFAULT NULL,
                      `end_version` varchar(30) DEFAULT NULL,
                      `start_increment` int(11) NOT NULL,
                      `end_increment` int(11) NOT NULL,
                      `upgraded_date` datetime DEFAULT NULL,
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
        
        $sql[19] = "ALTER TABLE ohrm_user_role
                        add column `display_name` varchar(255) not null;";
        
        $sql[20] = "create table ohrm_screen (
                        `id` int not null auto_increment, 
                        `name` varchar(100) not null, 
                        `module_id` int not null, 
                        `action_url` varchar(255) not null, 
                        primary key (`id`)
                    ) engine=innodb default charset=utf8;";
        
        $sql[21] = "create table ohrm_user_role_screen (
                        id int not null auto_increment,
                        user_role_id int not null, 
                        screen_id int not null, 
                        can_read tinyint(1) not null default '0', 
                        can_create tinyint(1) not null default '0',
                        can_update tinyint(1) not null default '0', 
                        can_delete tinyint(1) not null default '0',
                        primary key (`id`)
                    ) engine=innodb default charset=utf8;";
        
        $sql[22] = "alter table ohrm_screen
                       add constraint foreign key (module_id)
                           references ohrm_module(id) on delete cascade;";
        
        $sql[23] = "alter table ohrm_user_role_screen
                        add constraint foreign key (user_role_id)
                            references ohrm_user_role(id) on delete cascade;";
        
        $sql[24] = "alter table ohrm_user_role_screen
                        add constraint foreign key (screen_id)
                            references ohrm_screen(id) on delete cascade;";
        
        $sql[25] = "INSERT INTO `hs_hr_config`(`key`, `value`) VALUES
                        ('authorize_user_role_manager_class', 'BasicUserRoleManager');";
        
        $sql[26] = "UPDATE ohrm_filter_field 
                        SET filter_field_widget = 'ohrmReportWidgetOperationalCountryLocationDropDown' WHERE filter_field_id = 20";
        
        $sql[27] = "UPDATE ohrm_user_role 
                        SET is_assignable = is_predefined ";
        
        $sql[28] = "UPDATE ohrm_user_role 
                        SET is_predefined = 1 ";
        
        $sql[29] = "UPDATE ohrm_user_role 
                        SET display_name = name ";
        
        $sql[30] = "UPDATE ohrm_module SET id = CASE name
                        WHEN 'core' THEN '1'
                        WHEN 'admin' THEN '2'
                        WHEN 'pim' THEN '3'
                        WHEN 'leave' THEN '4'
                        WHEN 'time' THEN '5'
                        WHEN 'attendance' THEN '6'
                        WHEN 'recruitment' THEN '7'
                        WHEN 'recruitmentApply' THEN '8'
                        WHEN 'performance' THEN '9'
                        WHEN 'benefits' THEN '10'
                        END
                        WHERE name in('core', 'admin', 'pim', 'leave', 'time', 'attendance', 'recruitment', 'recruitmentApply', 'performance', 'benefits')";
        
        $sql[31] = "INSERT INTO ohrm_screen (`id`, `name`, `module_id`, `action_url`) VALUES
                        (1, 'User List', 2, 'viewSystemUsers'),
                        (2, 'Add/Edit System User', 2, 'saveSystemUser'),
                        (3, 'Delete System Users', 2, 'deleteSystemUsers'),
                        (4, 'Add Employee', 3, 'addEmployee'),
                        (5, 'View Employee List', 3, 'viewEmployeeList'),
                        (6, 'Delete Employees', 3, 'deleteEmployees'),
                        (7, 'Leave Type List', 4, 'leaveTypeList'),
                        (8, 'Define Leave Type', 4, 'defineLeaveType'),
                        (9, 'Undelete Leave Type', 4, 'undeleteLeaveType'),
                        (10, 'Delete Leave Type', 4, 'deleteLeaveType'),
                        (11, 'View Holiday List', 4, 'viewHolidayList'),
                        (12, 'Define Holiday', 4, 'defineHoliday'),
                        (13, 'Delete Holiday', 4, 'deleteHoliday'),
                        (14, 'Define WorkWeek', 4, 'defineWorkWeek'),
                        (16, 'Leave List', 4, 'viewLeaveList'),
                        (17, 'Assign Leave', 4, 'assignLeave'),
                        (18, 'View Leave Summary', 4, 'viewLeaveSummary'),
                        (19, 'Save Leave Entitlements', 4, 'saveLeaveEntitlements');";
        $sql[32] = "INSERT INTO ohrm_user_role_screen (user_role_id, screen_id, can_read, can_create, can_update, can_delete) VALUES
                        (1, 1, 1, 1, 1, 1),
                        (2, 1, 0, 0, 0, 0),
                        (3, 1, 0, 0, 0, 0),
                        (1, 2, 1, 1, 1, 1),
                        (2, 2, 0, 0, 0, 0),
                        (3, 2, 0, 0, 0, 0),
                        (1, 3, 1, 1, 1, 1),
                        (2, 3, 0, 0, 0, 0),
                        (3, 3, 0, 0, 0, 0),
                        (1, 4, 1, 1, 1, 1),
                        (1, 5, 1, 1, 1, 1),
                        (3, 5, 1, 0, 0, 0),
                        (1, 6, 1, 0, 0, 1),
                        (1, 7, 1, 1, 1, 1),
                        (1, 8, 1, 1, 1, 1),
                        (1, 9, 1, 1, 1, 1),
                        (1, 10, 1, 1, 1, 1),
                        (1, 11, 1, 1, 1, 1),
                        (1, 12, 1, 1, 1, 1),
                        (1, 13, 1, 1, 1, 1),
                        (1, 14, 1, 1, 1, 1),
                        (1, 16, 1, 1, 1, 0),
                        (2, 16, 1, 1, 1, 0),
                        (1, 17, 1, 1, 1, 0),
                        (2, 17, 1, 1, 1, 0),
                        (1, 18, 1, 1, 1, 0),
                        (2, 18, 1, 0, 0, 0),
                        (3, 18, 1, 0, 0, 0),
                        (1, 19, 1, 1, 1, 1);";
        
        $sql[33] = "create table `ohrm_email_configuration` (
                      `id` int(10) not null auto_increment,
                      `mail_type` varchar(50) DEFAULT NULL,
                      `sent_as` varchar(250) NOT NULL,
                      `sendmail_path` varchar(250) DEFAULT NULL,
                      `smtp_host` varchar(250) DEFAULT NULL,
                      `smtp_port` int(10) DEFAULT NULL,
                      `smtp_username` varchar(250) DEFAULT NULL,
                      `smtp_password` varchar(250) DEFAULT NULL,
                      `smtp_auth_type` varchar(50) DEFAULT NULL,
                      `smtp_security_type` varchar(50) DEFAULT NULL,
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
        
        $sql[34] = "DROP TABLE hs_hr_file_version ;";
        
        $sql[35] = "SELECT * FROM hs_hr_emp_picture";
        
        $this->sql = $sql;
    
    }
    
    private function insertOhrmWorkWeek() {
        $weekdays = $this->upgradeUtility->executeSql($this->sql[16]);
        $success = true;
        if($weekdays){
            $workweekstring = '';
            while($row = $this->upgradeUtility->fetchArray($weekdays))
            {
                $workweekstring .= ", ".$row['length'];
            }
            
            $sqlString = "INSERT INTO `ohrm_work_week` VALUES (1, NULL $workweekstring);";
            
            $result = $this->upgradeUtility->executeSql($sqlString);
            if (!$result) {
                $success = false;
            }
        }
        return $success;
    }
    
    
    private function updateHsHrEmpPicture() {
        $pictures = $this->upgradeUtility->executeSql($this->sql[35]);
        $success = true;
        if($pictures){
            $baseDir = sfConfig::get('sf_root_dir')."/cache/tempImages/";
            if (!file_exists($baseDir)) {
                mkdir($baseDir);
            }
            
            while($row = $this->upgradeUtility->fetchArray($pictures))
            {
                $empNumber = $row['emp_number'];
                $filename = $row['epic_filename'];
                $imageData = $row['epic_picture'];
                $filePath = $baseDir.$filename;
                
                $this->upgradeUtility->saveImage($filePath, $imageData);
                list($width, $height) = getimagesize($filePath);
                $sizeArray = $this->pictureSizeAdjust($height, $width);
                $adjustedWidth = $sizeArray['width'];
                $adjustedheight = $sizeArray['height'];
                $sql = "UPDATE hs_hr_emp_picture 
                        SET epic_file_width = '$adjustedWidth', epic_file_height = '$adjustedheight' WHERE emp_number = $empNumber";
                
                $result = $this->upgradeUtility->executeSql($sql);
                if (!$result) {
                    $success = false;
                }
                
                unlink($filePath);
            }
            rmdir($baseDir);
        }
        return $success;
    }
    
    private function pictureSizeAdjust($imgHeight, $imgWidth) {

        if ($imgHeight > 180 || $imgWidth > 150) {
            $newHeight = 0;
            $newWidth = 0;

            $propHeight = floor(($imgHeight / $imgWidth) * 150);
            $propWidth = floor(($imgWidth / $imgHeight) * 180);

            if ($propHeight <= 180) {
                $newHeight = $propHeight;
                $newWidth = 150;
            }

            if ($propWidth <= 150) {
                $newWidth = $propWidth;
                $newHeight = 180;
            }
        } else {
            if ($imgHeight <= 180)
                $newHeight = $imgHeight;

            if ($imgWidth <= 150)
                $newWidth = $imgWidth;
        }
        return array('width' => $newWidth, 'height' => $newHeight);
    }
    
    public function getNotes() {
        
        $notes[] = "If you have already set email configuration details, you have to reset the details at Admin > Email Notifications > Configuration.";
        $notes[] = "You need to reset the leave period starting date in the Leave module even though you would see it as set. Simply click Edit button and save same value.";
        
        return $notes;
    }
    
}