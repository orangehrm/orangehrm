<?php
include_once 'SchemaIncrementTask.php';

class SchemaIncrementTask30 extends SchemaIncrementTask {
    
    public $userInputs;
    
    public function execute() {
        $this->incrementNumber = 30;
        parent::execute();
        
        $result[] = $this->upgradeUtility->executeSql($this->sql[0]);
        
        $result[] = $this->upgradeUtility->executeSql($this->sql[1]);
        
        $result[] = $this->upgradeUtility->executeSql($this->sql[2]);
        
        $result[] = $this->upgradeUtility->executeSql($this->sql[3]);
        
        $result[] = $this->upgradeUtility->executeSql($this->sql[4]);
        
        $result[] = $this->upgradeUtility->executeSql($this->sql[5]);
        
        $result[] = $this->upgradeUtility->executeSql($this->sql[6]);
        
        $result[] = $this->upgradeUtility->executeSql($this->sql[7]);
        
        $result[] = $this->upgradeUtility->executeSql($this->sql[8]);
        
        $result[] = $this->upgradeUtility->executeSql($this->sql[9]);
        
        $result[] = $this->upgradeUtility->executeSql($this->sql[10]);
        
        $result[] = $this->upgradeUtility->executeSql($this->sql[11]);
        
        $result[] = $this->upgradeUtility->executeSql($this->sql[12]);
        
        $result[] = $this->upgradeUtility->executeSql($this->sql[13]);
        
        $result[] = $this->upgradeUtility->executeSql($this->sql[14]);
        
        $result[] = $this->upgradeUtility->executeSql($this->sql[15]);
        
        $result[] = $this->upgradeUtility->executeSql($this->sql[16]);
        
        $weekdays = $this->upgradeUtility->executeSql($this->sql[17]);
        
        if($weekdays){
            $workweekstring = '';
            while($row = $this->upgradeUtility->fetchArray($weekdays))
            {
                $workweekstring .= ", ".$row['length'];
            }
            
            $sqlString = "INSERT INTO `ohrm_work_week` VALUES (1, NULL $workweekstring);";
            
            $result[] = $this->upgradeUtility->executeSql($sqlString);
        }
        
        $result[] = $this->upgradeUtility->executeSql($this->sql[18]);
        
        $result[] = $this->upgradeUtility->executeSql($this->sql[19]);
        
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
                     hs_hr_employee.emp_oth_email';
        
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
        
        $sql[14] = "INSERT INTO `ohrm_selected_filter_field` (`report_id`, `filter_field_id`, `filter_field_order`, `value1`, `value2`, `where_condition`, `type`) VALUES
                        (5, 22, 1, null, null, 'IS NULL', 'Predefined');";
        
        $sql[15] = "UPDATE ohrm_summary_display_field 
                        SET label = 'Time (Hours)' WHERE summary_display_field_id = 1";
        
        $sql[16] = "INSERT INTO `ohrm_module` (`name`, `status`) VALUES
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
        
        $sql[17] = "SELECT * FROM hs_hr_weekends ORDER BY day";
        
        $sql[18] = "DROP TABLE hs_hr_weekends ;";
        
        $sql[19] = "CREATE TABLE IF NOT EXISTS `ohrm_upgrade_history` (
                      `id` int(10) not null auto_increment,
                      `from_version` varchar(30) DEFAULT NULL,
                      `to_version` varchar(30) DEFAULT NULL,
                      `from_increment` int(11) NOT NULL,
                      `to_increment` int(11) NOT NULL,
                      `date` datetime DEFAULT NULL,
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
        
        $this->sql = $sql;
    
    }
}