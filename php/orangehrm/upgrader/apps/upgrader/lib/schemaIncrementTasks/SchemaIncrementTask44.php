<?php
include_once 'SchemaIncrementTask.php';

/**
 * Installer Notes: 
 * 
 * 1. Old reports defined under the 'report' module are not converted to new PIM reports.
 * They will have to be manually recreated.
 * 
 */
class SchemaIncrementTask44 extends SchemaIncrementTask {
    
    public $userInputs;
    
    public function execute() {
        $this->incrementNumber = 44;
        parent::execute();
        
        $result = array();
        
        foreach($this->sql as $sql) {
            $result[] = $this->upgradeUtility->executeSql($sql);
        }
        
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
    
        $sql[0] = "ALTER TABLE ohrm_report MODIFY report_id bigint(20) not null auto_increment,
                   ADD COLUMN `type` varchar(255) default null AFTER use_filter_field";

        $sql[1] = "ALTER TABLE ohrm_filter_field DROP COLUMN `type`";

        $sql[2] = "ALTER TABLE ohrm_selected_filter_field 
                   CHANGE COLUMN value value1 varchar(255) default null,
                   ADD COLUMN value2 varchar(255) default null AFTER value1,
                   DROP COLUMN where_clause,
                   ADD COLUMN `type` varchar(255) not null default 'Runtime' after where_condition";
        
        $sql[3] = "ALTER TABLE ohrm_selected_filter_field ALTER COLUMN `type` DROP DEFAULT";        
        
        // Set default to report_group_id because all display fields in 2.6.9.1 are for report group 1.
        $sql[4] = "ALTER TABLE ohrm_display_field 
                   MODIFY COLUMN display_field_id bigint(20) not null auto_increment,
                   ADD COLUMN report_group_id bigint(20) not null default 1 AFTER display_field_id,
                   ADD COLUMN is_value_list boolean not null default false AFTER text_alignment_style,
                   ADD COLUMN display_field_group_id int unsigned AFTER is_value_list,
                   MODIFY COLUMN default_value varchar(255) default null,
                   ADD COLUMN `is_encrypted` boolean not null default false AFTER default_value,
                   ADD COLUMN `is_meta` boolean not null default false AFTER is_encrypted,
                   ADD KEY `report_group_id` (`report_group_id`)";
        
        $sql[5] = "ALTER TABLE ohrm_display_field ALTER COLUMN `report_group_id` DROP DEFAULT";
        
        
        $sql[6] = "ALTER TABLE ohrm_composite_display_field 
                   MODIFY COLUMN composite_display_field_id bigint(20) not null auto_increment,
                   ADD COLUMN report_group_id bigint(20) not null default 1 AFTER composite_display_field_id,
                   ADD COLUMN is_value_list boolean not null default false AFTER text_alignment_style,
                   ADD COLUMN display_field_group_id int unsigned AFTER is_value_list,
                   MODIFY COLUMN default_value varchar(255) default null,
                   ADD COLUMN `is_encrypted` boolean not null default false AFTER default_value,
                   ADD COLUMN `is_meta` boolean not null default false AFTER is_encrypted,
                   ADD KEY `report_group_id` (`report_group_id`)";
        
        $sql[7] = "ALTER TABLE ohrm_composite_display_field ALTER COLUMN `report_group_id` DROP DEFAULT";
        
        $sql[8] = "DROP TABLE ohrm_available_display_field";
        
        $sql[9] = "ALTER TABLE ohrm_selected_display_field MODIFY id bigint(20) not null auto_increment";
        
        $sql[10] = "UPDATE ohrm_display_field set is_meta = 1 where display_field_id IN 
                   (SELECT display_field_id FROM ohrm_meta_display_field)";
        
        $sql[11] = "DROP TABLE ohrm_meta_display_field";
        
        $sql[12] = "ALTER TABLE ohrm_summary_display_field 
                   ADD COLUMN is_value_list boolean not null default false AFTER text_alignment_style,
                   ADD COLUMN display_field_group_id int unsigned AFTER is_value_list,
                   MODIFY COLUMN default_value varchar(255) default null";
        
        // New tables
        $sql[13] = "create table `ohrm_display_field_group` (
                      `id` int unsigned not null auto_increment,
                      `report_group_id` bigint not null,
                      `name` varchar(255) not null,
                      `is_list` boolean not null default false,
                      primary key (`id`)
                    ) engine=innodb default charset=utf8";
        
         $sql[14] = "create table `ohrm_selected_display_field_group` (
                       `id` int unsigned not null auto_increment,
                       `report_id` bigint not null,
                       `display_field_group_id` int unsigned not null,
                       primary key (`id`)
                     ) engine=innodb default charset=utf8;";
        
         // New constraints
         $sql[15] = "alter table ohrm_display_field
                     add constraint foreign key (report_group_id) 
                        references ohrm_report_group(report_group_id) on delete cascade,
                     add constraint foreign key (display_field_group_id)
                        references ohrm_display_field_group(id) on delete set null";

         $sql[16] = "alter table ohrm_composite_display_field
                     add constraint foreign key (report_group_id)
                       references ohrm_report_group(report_group_id) on delete cascade,
                     add constraint foreign key (display_field_group_id)
                       references ohrm_display_field_group(id) on delete set null";

         $sql[17] = "alter table ohrm_summary_display_field
                     add constraint foreign key (display_field_group_id)
                       references ohrm_display_field_group(id) on delete set null";


         // Changes to constraints to add "on delete cascade"
         $sql[18] = "ALTER TABLE ohrm_filter_field DROP FOREIGN KEY ohrm_filter_field_ibfk_1";
         $sql[19] = "alter table ohrm_filter_field
                     add constraint ohrm_filter_field_ibfk_1 foreign key (report_group_id)
                       references ohrm_report_group(report_group_id) on delete cascade";
         
         $sql[20] = "ALTER TABLE ohrm_selected_group_field DROP FOREIGN KEY ohrm_selected_group_field_ibfk_1";         
         $sql[21] = "alter table ohrm_selected_group_field
                     add constraint ohrm_selected_group_field_ibfk_1 foreign key (report_id)
                       references ohrm_report(report_id) on delete cascade";
         
         $sql[22] = "ALTER TABLE ohrm_selected_group_field DROP FOREIGN KEY ohrm_selected_group_field_ibfk_2";
         $sql[23] = "alter table ohrm_selected_group_field
                     add constraint ohrm_selected_group_field_ibfk_2 foreign key (group_field_id)
                       references ohrm_group_field(group_field_id) on delete cascade";
                  
         $sql[24] = "ALTER TABLE ohrm_selected_filter_field DROP FOREIGN KEY ohrm_selected_filter_field_ibfk_1";
         $sql[25] = "alter table ohrm_selected_filter_field
                     add constraint ohrm_selected_filter_field_ibfk_1 foreign key (report_id)
                       references ohrm_report(report_id) on delete cascade";
         
         $sql[26] = "ALTER TABLE ohrm_selected_filter_field DROP FOREIGN KEY ohrm_selected_filter_field_ibfk_2";
         $sql[27] = "alter table ohrm_selected_filter_field
                     add constraint ohrm_selected_filter_field_ibfk_2 foreign key (filter_field_id)
                       references ohrm_filter_field(filter_field_id) on delete cascade";
         
         $sql[28] = "ALTER TABLE ohrm_selected_display_field DROP FOREIGN KEY ohrm_selected_display_field_ibfk_1";         
         $sql[29] = "alter table ohrm_selected_display_field
                     add constraint ohrm_selected_display_field_ibfk_1 foreign key (report_id)
                       references ohrm_report(report_id) on delete cascade";

         $sql[30] = "ALTER TABLE ohrm_selected_display_field DROP FOREIGN KEY ohrm_selected_display_field_ibfk_2";         
         $sql[31] = "alter table ohrm_selected_display_field
                     add constraint ohrm_selected_display_field_ibfk_2 foreign key (display_field_id)
                       references ohrm_display_field(display_field_id) on delete cascade";
         
         $sql[32] = "ALTER TABLE ohrm_selected_composite_display_field DROP FOREIGN KEY ohrm_selected_composite_display_field_ibfk_1";         
         $sql[33] = "alter table ohrm_selected_composite_display_field
                     add constraint ohrm_selected_composite_display_field_ibfk_1 foreign key (report_id)
                       references ohrm_report(report_id) on delete cascade";

         $sql[34] = "ALTER TABLE ohrm_selected_composite_display_field DROP FOREIGN KEY ohrm_selected_composite_display_field_ibfk_2";         
         $sql[35] = "alter table ohrm_selected_composite_display_field
                     add constraint ohrm_selected_composite_display_field_ibfk_2 foreign key (composite_display_field_id)
                       references ohrm_composite_display_field(composite_display_field_id) on delete cascade";


         // New constraints
         $sql[36] = "alter table ohrm_display_field_group
                     add constraint foreign key (report_group_id)
                     references ohrm_report_group(report_group_id) on delete cascade";

         $sql[37] = "alter table ohrm_selected_display_field_group
                     add constraint foreign key (report_id)
                        references ohrm_report(report_id) on delete cascade,
                     add constraint foreign key (display_field_group_id)
                        references ohrm_display_field_group(id) on delete cascade";

         // PIM report
         $sql[38] = "INSERT INTO `ohrm_report_group` (`report_group_id`, `name`, `core_sql`) VALUES 
                    (3,'pim', 'SELECT selectCondition FROM hs_hr_employee 
                    LEFT JOIN hs_hr_emp_emergency_contacts ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_emergency_contacts.emp_number) 
                    LEFT JOIN hs_hr_compstructtree ON 
                        (hs_hr_employee.work_station = hs_hr_compstructtree.id) 
                    LEFT JOIN hs_hr_empstat ON 
                        (hs_hr_employee.emp_status = hs_hr_empstat.estat_code) 
                    LEFT JOIN hs_hr_job_title ON 
                        (hs_hr_employee.job_title_code = hs_hr_job_title.jobtit_code) 
                    LEFT JOIN hs_hr_eec ON 
                        (hs_hr_employee.eeo_cat_code = hs_hr_eec.eec_code) 
                    LEFT JOIN hs_hr_nationality ON 
                        (hs_hr_employee.nation_code = hs_hr_nationality.nat_code) 
                    LEFT JOIN hs_hr_ethnic_race ON 
                        (hs_hr_employee.ethnic_race_code = hs_hr_ethnic_race.ethnic_race_code) 
                    LEFT JOIN hs_hr_emp_dependents ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_dependents.emp_number)
                    LEFT JOIN hs_hr_emp_locations AS emp_location ON
                        (hs_hr_employee.emp_number = emp_location.emp_number)
                    LEFT JOIN hs_hr_location ON
                        (emp_location.loc_code = hs_hr_location.loc_code)
                    LEFT JOIN hs_hr_job_spec ON 
                        (hs_hr_job_title.jobspec_id = hs_hr_job_spec.jobspec_id) 
                    LEFT JOIN hs_hr_emp_contract_extend ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_contract_extend.emp_number) 
                    LEFT JOIN hs_hr_emp_basicsalary ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_basicsalary.emp_number) 
                    LEFT JOIN hs_pr_salary_grade ON 
                        (hs_hr_emp_basicsalary.sal_grd_code = hs_pr_salary_grade.sal_grd_code) 
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
                    LEFT JOIN hs_hr_emp_education ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_education.emp_number) 
                    LEFT JOIN hs_hr_education ON 
                        (hs_hr_emp_education.edu_code = hs_hr_education.edu_code) 
                    LEFT JOIN hs_hr_emp_skill ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_skill.emp_number) 
                    LEFT JOIN hs_hr_skill ON 
                        (hs_hr_emp_skill.skill_code = hs_hr_skill.skill_code) 
                    LEFT JOIN hs_hr_emp_language ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_language.emp_number) 
                    LEFT JOIN hs_hr_language ON 
                        (hs_hr_emp_language.lang_code = hs_hr_language.lang_code) 
                    LEFT JOIN hs_hr_emp_licenses ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_licenses.emp_number) 
                    LEFT JOIN hs_hr_licenses ON 
                        (hs_hr_emp_licenses.licenses_code = hs_hr_licenses.licenses_code) 
                    LEFT JOIN hs_hr_emp_member_detail ON 
                        (hs_hr_employee.emp_number = hs_hr_emp_member_detail.emp_number) 
                    LEFT JOIN hs_hr_membership ON 
                        (hs_hr_emp_member_detail.membship_code = hs_hr_membership.membship_code) 
                    LEFT JOIN hs_hr_membership_type ON 
                        (hs_hr_emp_member_detail.membtype_code = hs_hr_membership_type.membtype_code) 
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
                WHERE whereCondition1
                GROUP BY 
                     hs_hr_employee.emp_number,
                     hs_hr_employee.emp_lastname,
                     hs_hr_employee.emp_firstname,
                     hs_hr_employee.emp_middle_name,
                     hs_hr_employee.emp_birthday,
                     hs_hr_nationality.nat_name,
                     hs_hr_employee.emp_gender,
                     hs_hr_ethnic_race.ethnic_race_desc,
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
                     hs_hr_employee.emp_oth_email')";
         
         $sql[39] = "INSERT INTO `ohrm_report` (`report_id`, `name`, `report_group_id`, `use_filter_field`, `type`) VALUES 
                     (5, 'PIM Sample Report', 3, 1, 'PIM_DEFINED')";
         
         $sql[40] = "INSERT INTO `ohrm_filter_field` (`filter_field_id`, `report_group_id`, `name`, 
    `where_clause_part`, `filter_field_widget`, `condition_no`, `required`) VALUES 
    (8, 3, 'employee_name', 'hs_hr_employee.emp_number', 'ohrmReportWidgetEmployeeListAutoFill', 1, null),
    (9, 3, 'pay_grade', 'hs_hr_emp_basicsalary.sal_grd_code', 'ohrmReportWidgetPayGradeDropDown', 1, null),
    (10, 3, 'education', 'hs_hr_emp_education.edu_code', 'ohrmReportWidgetEducationtypeDropDown', 1, null),
    (11, 3, 'employment_status', 'hs_hr_empstat.estat_code', 'ohrmWidgetEmploymentStatusList', 1, null),
    (12, 3, 'service_period', 'ROUND(datediff(current_date(), hs_hr_employee.joined_date)/365,1)', 'ohrmReportWidgetServicePeriod', 1, null),
    (13, 3, 'joined_date', 'hs_hr_employee.joined_date', 'ohrmReportWidgetJoinedDate', 1, null),
    (14, 3, 'job_title', 'hs_hr_job_title.jobtit_code', 'ohrmWidgetJobTitleList', 1, null),
    (15, 3, 'language', 'hs_hr_language.lang_code', 'ohrmReportWidgetLanguageDropDown', 1, null),
    (16, 3, 'skill', 'hs_hr_skill.skill_code', 'ohrmReportWidgetSkillDropDown', 1, null),
    (17, 3, 'age_group', 'ROUND(datediff(current_date(), hs_hr_employee.emp_birthday)/365,1)', 'ohrmReportWidgetAgeGroup', 1, null),
    (18, 3, 'sub_unit', 'hs_hr_compstructtree.id', 'ohrmWidgetSubDivisionList', 1, null),
    (19, 3, 'gender', 'hs_hr_employee.emp_gender', 'ohrmReportWidgetGenderDropDown', 1, null),
    (20, 3, 'location', 'hs_hr_location.loc_code', 'ohrmReportWidgetLocationDropDown', 1, null),
    (21, 1, 'is_deleted', 'hs_hr_project_activity.deleted', '', 2, null)";


         $sql[41] = "INSERT INTO `ohrm_display_field_group`(`id`, `report_group_id`, `name`, `is_list`) VALUES
    (1, 3, 'Personal', false),
    (2, 3, 'Contact Details', false),
    (3, 3, 'Emergency Contacts', true),
    (4, 3, 'Dependents', true),
    (5, 3, 'Immigration', true),
    (6, 3, 'Job', false),
    (7, 3, 'Salary', true),
    (8, 3, 'Subordinates', true),
    (9, 3, 'Supervisors', true),
    (10, 3, 'Work Experience', true),
    (11, 3, 'Education', true),
    (12, 3, 'Skills', true),
    (13, 3, 'Languages', true),
    (14, 3, 'License', true),
    (15, 3, 'Memberships', true),
    (16, 3, 'Custom Fields', false)";
         
         $sql[42] =  "UPDATE ohrm_display_field SET `label` = 'Employee First Name' WHERE display_field_id = 6";
         $sql[43] =  "UPDATE ohrm_display_field SET `label` = 'Employee Last Name' WHERE display_field_id = 7";
         
         $sql[44] = <<< DISPLAY_FIELDS
INSERT INTO `ohrm_display_field` (`display_field_id`, `report_group_id`, `name`, `label`, `field_alias`, `is_sortable`, `sort_order`, `sort_field`, `element_type`, `element_property`, `width`, `is_exportable`, `text_alignment_style`, `is_value_list`, `display_field_group_id`, `default_value`, `is_encrypted`, `is_meta`) VALUES
    (9, 3, 'hs_hr_employee.employee_id', 'Employee Id',          'employeeId',  'false', null, null, 'label', '<xml><getter>employeeId</getter></xml>', 100, '0', null, false, 1, '---', false, false),
    (10, 3, 'hs_hr_employee.emp_lastname', 'Employee Last Name',  'employeeLastname',  'false', null, null, 'label', '<xml><getter>employeeLastname</getter></xml>', 200, '0', null, false, 1, '---', false, false),
    (11, 3, 'hs_hr_employee.emp_firstname', 'Employee First Name','employeeFirstname',  'false', null, null, 'label', '<xml><getter>employeeFirstname</getter></xml>', 200, '0', null, false, 1, '---', false, false),
    (12, 3, 'hs_hr_employee.emp_middle_name', 'Employee Middle Name', 'employeeMiddlename',  'false', null, null, 'label', '<xml><getter>employeeMiddlename</getter></xml>', 200, '0', null, false, 1, '---', false, false),
    (13, 3, 'hs_hr_employee.emp_birthday', 'Date of Birth',           'empBirthday',  'false', null, null, 'label', '<xml><getter>empBirthday</getter></xml>', 100, '0', null, false, 1, '---', false, false),
    (14, 3, 'hs_hr_nationality.nat_name', 'Nationality',              'nationality',  'false', null, null, 'label', '<xml><getter>nationality</getter></xml>', 200, '0', null, false, 1, '---', false, false),
    (15, 3, 'CASE hs_hr_employee.emp_gender WHEN 1 THEN "Male" WHEN 2 THEN "Female" WHEN 3 THEN "Other" END', 'Gender', 'empGender',  'false', null, null, 'label', '<xml><getter>empGender</getter></xml>', 80, '0', null, false, 1, '---', false, false),
    (16, 3, 'hs_hr_ethnic_race.ethnic_race_desc', 'Ethnic Race', 'ethnicRace',  'false', null, null, 'label', '<xml><getter>ethnicRace</getter></xml>', 200, '0', null, false, 1, '---', false, false),
    (17, 3, 'hs_hr_employee.emp_marital_status', 'Marital Status',    'maritalStatus',  'false', null, null, 'label', '<xml><getter>maritalStatus</getter></xml>', 100, '0', null, false, 1, '---', false, false),
    (18, 3, 'hs_hr_employee.emp_dri_lice_num', 'Driver License Number', 'driversLicenseNumber',  'false', null, null, 'label', '<xml><getter>driversLicenseNumber</getter></xml>', 240, '0', null, false, 1, '---', false, false),
    (19, 3, 'hs_hr_employee.emp_dri_lice_exp_date', 'License Expiry Date', 'licenseExpiryDate',  'false', null, null, 'label', '<xml><getter>licenseExpiryDate</getter></xml>', 135, '0', null, false, 1, '---', false, false),
    (20, 3, 'CONCAT_WS(", ", NULLIF(hs_hr_employee.emp_street1, ""), NULLIF(hs_hr_employee.emp_street2, ""), NULLIF(hs_hr_employee.city_code, ""), NULLIF(hs_hr_employee.provin_code,""), NULLIF(hs_hr_employee.emp_zipcode,""), NULLIF(hs_hr_country.cou_name,""))', 'Address', 'address',  'false', null, null, 'label', '<xml><getter>address</getter></xml>', 200, '0', null, false, 2, '---', false, false),
    (21, 3, 'hs_hr_employee.emp_hm_telephone', 'Home Telephone',  'homeTelephone',  'false', null, null, 'label', '<xml><getter>homeTelephone</getter></xml>', 130, '0', null, false, 2, '---', false, false),
    (22, 3, 'hs_hr_employee.emp_mobile', 'Mobile', 'mobile',  'false', null, null, 'label', '<xml><getter>mobile</getter></xml>', 100, '0', null, false, 2, '---', false, false),
    (23, 3, 'hs_hr_employee.emp_work_telephone', 'Work Telephone', 'workTelephone',  'false', null, null, 'label', '<xml><getter>workTelephone</getter></xml>', 100, '0', null, false, 2, '---', false, false),
    (24, 3, 'hs_hr_employee.emp_work_email', 'Work Email',         'workEmail',  'false', null, null, 'label', '<xml><getter>workEmail</getter></xml>', 200, '0', null, false, 2, '---', false, false),
    (25, 3, 'hs_hr_employee.emp_oth_email', 'Other Email',         'otherEmail',  'false', null, null, 'label', '<xml><getter>otherEmail</getter></xml>', 200, '0', null, false, 2, '---', false, false),
    (26, 3, 'hs_hr_emp_emergency_contacts.eec_name', 'Name', 'ecname',  'false', null, null, 'label', '<xml><getter>ecname</getter></xml>', 200, '0', null, true, 3, '---', false, false),
    (27, 3, 'hs_hr_emp_emergency_contacts.eec_home_no', 'Home Telephone', 'ecHomeTelephone',  'false', null, null, 'label', '<xml><getter>ecHomeTelephone</getter></xml>', 130, '0', null, true, 3, '---', false, false),
    (28, 3, 'hs_hr_emp_emergency_contacts.eec_office_no', 'Work Telephone', 'ecWorkTelephone',  'false', null, null, 'label', '<xml><getter>ecWorkTelephone</getter></xml>', 100, '0', null, true, 3, '---', false, false),
    (29, 3, 'hs_hr_emp_emergency_contacts.eec_relationship', 'Relationship', 'ecRelationship',  'false', null, null, 'label', '<xml><getter>ecRelationship</getter></xml>', 200, '0', null, true, 3, '---', false, false),
    (30, 3, 'hs_hr_emp_emergency_contacts.eec_mobile_no', 'Mobile', 'ecMobile',  'false', null, null, 'label', '<xml><getter>ecMobile</getter></xml>', 100, '0', null, true, 3, '---', false, false),
    (31, 3, 'hs_hr_emp_dependents.ed_name', 'Name', 'dependentName',  'false', null, null, 'label', '<xml><getter>dependentName</getter></xml>', 200, '0', null, true, 4, '---', false, false),
    (32, 3, 'IF (hs_hr_emp_dependents.ed_relationship_type = \'other\', hs_hr_emp_dependents.ed_relationship, hs_hr_emp_dependents.ed_relationship_type)', 'Relationship', 'dependentRelationship',  'false', null, null, 'label', '<xml><getter>dependentRelationship</getter></xml>', 200, '0', null, true, 4, '---', false, false),
    (33, 3, 'hs_hr_emp_dependents.ed_date_of_birth', 'Date of Birth', 'dependentDateofBirth',  'false', null, null, 'label', '<xml><getter>dependentDateofBirth</getter></xml>', 100, '0', null, true, 4, '---', false, false),
    (34, 3, 'hs_hr_membership_type.membtype_name', 'Membership Type', 'membershipType',  'false', null, null, 'label', '<xml><getter>membershipType</getter></xml>', 200, '0', null, true, 15, '---', false, false),
    (35, 3, 'hs_hr_membership.membship_name', 'Membership', 'membershipName',  'false', null, null, 'label', '<xml><getter>membershipName</getter></xml>', 200, '0', null, true, 15, '---', false, false),
    (36, 3, 'hs_hr_emp_member_detail.ememb_subscript_ownership', 'Subscription Paid By', 'subscriptionPaidBy',  'false', null, null, 'label', '<xml><getter>subscriptionPaidBy</getter></xml>', 200, '0', null, true, 15, '---', false, false),
    (37, 3, 'hs_hr_emp_member_detail.ememb_subscript_amount', 'Subscription Amount', 'subscriptionAmount',  'false', null, null, 'label', '<xml><getter>subscriptionAmount</getter></xml>', 200, '0', null, true, 15, '---', false, false),
    (38, 3, 'hs_hr_emp_member_detail.ememb_subs_currency', 'Currency', 'membershipCurrency',  'false', null, null, 'label', '<xml><getter>membershipCurrency</getter></xml>', 200, '0', null, true, 15, '---', false, false),
    (39, 3, 'hs_hr_emp_member_detail.ememb_commence_date', 'Subscription Commence Date', 'subscriptionCommenceDate',  'false', null, null, 'label', '<xml><getter>subscriptionCommenceDate</getter></xml>', 200, '0', null, true, 15, '---', false, false),
    (40, 3, 'hs_hr_emp_member_detail.ememb_renewal_date', 'Subscription Renewal Date', 'subscriptionRenewalDate',  'false', null, null, 'label', '<xml><getter>subscriptionRenewalDate</getter></xml>', 200, '0', null, true, 15, '---', false, false),
    (41, 3, 'hs_hr_emp_work_experience.eexp_employer', 'Company', 'expCompany',  'false', null, null, 'label', '<xml><getter>expCompany</getter></xml>', 200, '0', null, true, 10, '---', false, false),
    (42, 3, 'hs_hr_emp_work_experience.eexp_jobtit', 'Job Title', 'expJobTitle',  'false', null, null, 'label', '<xml><getter>expJobTitle</getter></xml>', 200, '0', null, true, 10, '---', false, false),
    (43, 3, 'DATE(hs_hr_emp_work_experience.eexp_from_date)', 'From', 'expFrom',  'false', null, null, 'label', '<xml><getter>expFrom</getter></xml>', 100, '0', null, true, 10, '---', false, false),
    (44, 3, 'DATE(hs_hr_emp_work_experience.eexp_to_date)', 'To', 'expTo',  'false', null, null, 'label', '<xml><getter>expTo</getter></xml>', 100, '0', null, true, 10, '---', false, false),
    (45, 3, 'hs_hr_emp_work_experience.eexp_comments', 'Comment', 'expComment',  'false', null, null, 'label', '<xml><getter>expComment</getter></xml>', 200, '0', null, true, 10, '---', false, false),
    (47, 3, 'CONCAT(hs_hr_education.edu_uni, " , " ,hs_hr_education.edu_deg)', 'Program', 'eduProgram',  'false', null, null, 'label', '<xml><getter>eduProgram</getter></xml>', 200, '0', null, true, 11, '---', false, false),
    (48, 3, 'hs_hr_emp_education.edu_year', 'Year', 'eduYear',  'false', null, null, 'label', '<xml><getter>eduYear</getter></xml>', 100, '0', null, true, 11, '---', false, false),
    (49, 3, 'hs_hr_emp_education.edu_gpa', 'GPA/Score', 'eduGPAOrScore',  'false', null, null, 'label', '<xml><getter>eduGPAOrScore</getter></xml>', 80, '0', null, true, 11, '---', false, false),
    (52, 3, 'hs_hr_skill.skill_name', 'Skill', 'skill',  'false', null, null, 'label', '<xml><getter>skill</getter></xml>', 200, '0', null, true, 12, '---', false, false),
    (53, 3, 'hs_hr_emp_skill.years_of_exp', 'Years of Experience', 'skillYearsOfExperience',  'false', null, null, 'label', '<xml><getter>skillYearsOfExperience</getter></xml>', 135, '0', null, true, 12, '---', false, false),
    (54, 3, 'hs_hr_emp_skill.comments', 'Comments', 'skillComments',  'false', null, null, 'label', '<xml><getter>skillComments</getter></xml>', 200, '0', null, true, 12, '---', false, false),
    (55, 3, 'hs_hr_language.lang_name', 'Language', 'langName',  'false', null, null, 'label', '<xml><getter>langName</getter></xml>', 200, '0', null, true, 13, '---', false, false),
    (57, 3, 'CASE hs_hr_emp_language.competency WHEN 1 THEN "Poor" WHEN 2 THEN "Basic" WHEN 3 THEN "Good" WHEN 4 THEN "Mother Tongue" END', 'Competency', 'langCompetency',  'false', null, null, 'label', '<xml><getter>langCompetency</getter></xml>', 130, '0', null, true, 13, '---', false, false),
    (58, 3, 'hs_hr_emp_language.comments', 'Comments', 'langComments',  'false', null, null, 'label', '<xml><getter>langComments</getter></xml>', 200, '0', null, true, 13, '---', false, false),
    (59, 3, 'hs_hr_licenses.licenses_desc', 'License Type', 'empLicenseType',  'false', null, null, 'label', '<xml><getter>empLicenseType</getter></xml>', 200, '0', null, true, 14, '---', false, false),
    (60, 3, 'hs_hr_emp_licenses.licenses_date', 'Issued Date', 'empLicenseIssuedDate',  'false', null, null, 'label', '<xml><getter>empLicenseIssuedDate</getter></xml>', 100, '0', null, true, 14, '---', false, false),
    (61, 3, 'hs_hr_emp_licenses.licenses_renewal_date', 'Expiry Date', 'empLicenseExpiryDate',  'false', null, null, 'label', '<xml><getter>empLicenseExpiryDate</getter></xml>', 100, '0', null, true, 14, '---', false, false),
    (62, 3, 'supervisor.emp_firstname', 'First Name', 'supervisorFirstName',  'false', null, null, 'label', '<xml><getter>supervisorFirstName</getter></xml>', 200, '0', null, true, 9, '---', false, false),
    (63, 3, 'subordinate.emp_firstname', 'First Name', 'subordinateFirstName',  'false', null, null, 'label', '<xml><getter>subordinateFirstName</getter></xml>', 200, '0', null, true, 8, '---', false, false),
    (64, 3, 'supervisor.emp_lastname', 'Last Name', 'supervisorLastName',  'false', null, null, 'label', '<xml><getter>supervisorLastName</getter></xml>', 200, '0', null, true, 9, '---', false, false),
    (65, 3, 'hs_pr_salary_grade.sal_grd_name', 'Pay Grade', 'salPayGrade',  'false', null, null, 'label', '<xml><getter>salPayGrade</getter></xml>', 200, '0', null, true, 7, '---', false, false),
    (66, 3, 'hs_hr_emp_basicsalary.salary_component', 'Salary Component', 'salSalaryComponent',  'false', null, null, 'label', '<xml><getter>salSalaryComponent</getter></xml>', 200, '0', null, true, 7, '---', false, false),
    (67, 3, 'hs_hr_emp_basicsalary.ebsal_basic_salary', 'Amount', 'salAmount',  'false', null, null, 'label', '<xml><getter>salAmount</getter></xml>', 200, '0', null, true, 7, '---', true, false),
    (68, 3, 'hs_hr_emp_basicsalary.comments', 'Comments', 'salComments',  'false', null, null, 'label', '<xml><getter>salComments</getter></xml>', 200, '0', null, true, 7, '---', false, false),
    (69, 3, 'hs_hr_payperiod.payperiod_name', 'Pay Frequency', 'salPayFrequency',  'false', null, null, 'label', '<xml><getter>salPayFrequency</getter></xml>', 200, '0', null, true, 7, '---', false, false),
    (70, 3, 'hs_hr_currency_type.currency_name', 'Currency', 'salCurrency',  'false', null, null, 'label', '<xml><getter>salCurrency</getter></xml>', 200, '0', null, true, 7, '---', false, false),
    (71, 3, 'hs_hr_emp_directdebit.dd_account', 'Direct Deposit Account Number', 'ddAccountNumber',  'false', null, null, 'label', '<xml><getter>ddAccountNumber</getter></xml>', 200, '0', null, true, 7, '---', false, false),
    (72, 3, 'hs_hr_emp_directdebit.dd_account_type', 'Direct Deposit Account Type', 'ddAccountType',  'false', null, null, 'label', '<xml><getter>ddAccountType</getter></xml>', 200, '0', null, true, 7, '---', false, false),
    (73, 3, 'hs_hr_emp_directdebit.dd_routing_num', 'Direct Deposit Routing Number', 'ddRoutingNumber',  'false', null, null, 'label', '<xml><getter>ddRoutingNumber</getter></xml>', 200, '0', null, true, 7, '---', false, false),
    (74, 3, 'hs_hr_emp_directdebit.dd_amount', 'Direct Deposit Amount', 'ddAmount',  'false', null, null, 'label', '<xml><getter>ddAmount</getter></xml>', 200, '0', null, true, 7, '---', false, false),
    (75, 3, 'DATE(hs_hr_emp_contract_extend.econ_extend_start_date)', 'Contract Start Date', 'empContStartDate',  'false', null, null, 'label', '<xml><getter>empContStartDate</getter></xml>', 200, '0', null, true, 6, '---', false, false),
    (76, 3, 'DATE(hs_hr_emp_contract_extend.econ_extend_end_date)', 'Contract End Date', 'empContEndDate',  'false', null, null, 'label', '<xml><getter>empContEndDate</getter></xml>', 200, '0', null, true, 6, '---', false, false),
    (77, 3, 'hs_hr_job_title.jobtit_name', 'Job Title', 'empJobTitle',  'false', null, null, 'label', '<xml><getter>empJobTitle</getter></xml>', 200, '0', null, true, 6, '---', false, false),
    (78, 3, 'hs_hr_empstat.estat_name', 'Employment Status', 'empEmploymentStatus',  'false', null, null, 'label', '<xml><getter>empEmploymentStatus</getter></xml>', 200, '0', null, true, 6, '---', false, false),
    (79, 3, 'hs_hr_job_spec.jobspec_name', 'Job Specification', 'empJobSpecification',  'false', null, null, 'label', '<xml><getter>empJobSpecification</getter></xml>', 200, '0', null, true, 6, '---', false, false),
    (80, 3, 'hs_hr_eec.eec_desc', 'Job Category', 'empJobCategory',  'false', null, null, 'label', '<xml><getter>empJobCategory</getter></xml>', 200, '0', null, true, 6, '---', false, false),
    (81, 3, 'hs_hr_employee.joined_date', 'Joined Date', 'empJoinedDate',  'false', null, null, 'label', '<xml><getter>empJoinedDate</getter></xml>', 100, '0', null, true, 6, '---', false, false),
    (82, 3, 'hs_hr_compstructtree.title', 'Sub Unit', 'empSubUnit',  'false', null, null, 'label', '<xml><getter>empSubUnit</getter></xml>', 200, '0', null, true, 6, '---', false, false),
    (83, 3, 'hs_hr_location.loc_name', 'Location', 'empLocation',  'false', null, null, 'label', '<xml><getter>empLocation</getter></xml>', 200, '0', null, true, 6, '---', false, false),
    (84, 3, 'hs_hr_emp_passport.ep_passport_num', 'Number', 'empPassportNo',  'false', null, null, 'label', '<xml><getter>empPassportNo</getter></xml>', 200, '0', null, true, 5, '---', false, false),
    (85, 3, 'DATE(hs_hr_emp_passport.ep_passportissueddate)', 'Issued Date', 'empPassportIssuedDate',  'false', null, null, 'label', '<xml><getter>empPassportIssuedDate</getter></xml>', 100, '0', null, true, 5, '---', false, false),
    (86, 3, 'DATE(hs_hr_emp_passport.ep_passportexpiredate)', 'Expiry Date', 'empPassportExpiryDate',  'false', null, null, 'label', '<xml><getter>empPassportExpiryDate</getter></xml>', 100, '0', null, true, 5, '---', false, false),
    (87, 3, 'hs_hr_emp_passport.ep_i9_status', 'Eligible Status', 'empPassportEligibleStatus',  'false', null, null, 'label', '<xml><getter>empPassportEligibleStatus</getter></xml>', 200, '0', null, true, 5, '---', false, false),
    (88, 3, 'hs_hr_emp_passport.cou_code', 'Issued By', 'empPassportIssuedBy',  'false', null, null, 'label', '<xml><getter>empPassportIssuedBy</getter></xml>', 200, '0', null, true, 5, '---', false, false),
    (89, 3, 'hs_hr_emp_passport.ep_i9_review_date', 'Eligible Review Date', 'empPassportEligibleReviewDate',  'false', null, null, 'label', '<xml><getter>empPassportEligibleReviewDate</getter></xml>', 200, '0', null, true, 5, '---', false, false),
    (90, 3, 'hs_hr_emp_passport.ep_comments', 'Comments', 'empPassportComments',  'false', null, null, 'label', '<xml><getter>empPassportComments</getter></xml>', 200, '0', null, true, 5, '---', false, false),
    (91, 3, 'subordinate.emp_lastname', 'Last Name', 'subordinateLastName',  'false', null, null, 'label', '<xml><getter>subordinateLastName</getter></xml>', 200, '0', null, true, 8, '---', false, false),
    (92, 3, 'CASE hs_hr_emp_language.elang_type WHEN 1 THEN "Writing" WHEN 2 THEN "Speaking" WHEN 3 THEN "Reading" END', 'Fluency', 'langFluency',  'false', null, null, 'label', '<xml><getter>langFluency</getter></xml>', 200, '0', null, true, 13, '---', false, false),
    (93, 3, 'supervisor_reporting_method.reporting_method_name', 'Reporting Method', 'supReportingMethod',  'false', null, null, 'label', '<xml><getter>supReportingMethod</getter></xml>', 200, '0', null, true, 9, '---', false, false),
    (94, 3, 'subordinate_reporting_method.reporting_method_name', 'Reporting Method', 'subReportingMethod',  'false', null, null, 'label', '<xml><getter>subReportingMethod</getter></xml>', 200, '0', null, true, 8, '---', false, false),
    (95, 3, 'CASE hs_hr_emp_passport.ep_passport_type_flg WHEN 1 THEN "Passport" WHEN 2 THEN "Visa" END', 'Document Type', 'documentType',  'false', null, null, 'label', '<xml><getter>documentType</getter></xml>', 200, '0', null, true, 5, '---', false, false),
    (97, 3, 'hs_hr_employee.emp_other_id', 'Other Id', 'otherId', 'false', null, null, 'label', '<xml><getter>otherId</getter></xml>', 100, '0', null, false, 1, '---', false, false),
    (98, 3, 'hs_hr_emp_emergency_contacts.eec_seqno', 'ecSeqNo', 'ecSeqNo',  'false', null, null, 'label', '<xml><getter>ecMobile</getter></xml>', 100, '0', null, true, 3, '---', false, true),
    (99, 3, 'hs_hr_emp_dependents.ed_seqno', 'SeqNo', 'edSeqNo',  'false', null, null, 'label', '<xml><getter>ecMobile</getter></xml>', 100, '0', null, true, 4, '---', false, true),
    (100, 3, 'hs_hr_emp_passport.ep_seqno', 'SeqNo', 'epSeqNo',  'false', null, null, 'label', '<xml><getter>ecMobile</getter></xml>', 100, '0', null, true, 5, '---', false, true),
    (101, 3, 'hs_hr_emp_basicsalary.id', 'salaryId', 'salaryId',  'false', null, null, 'label', '<xml><getter>ecMobile</getter></xml>', 100, '0', null, true, 7, '---', false, true),
    (102, 3, 'subordinate.emp_number', 'subordinateId', 'subordinateId',  'false', null, null, 'label', '<xml><getter>ecMobile</getter></xml>', 100, '0', null, true, 8, '---', false, true),
    (103, 3, 'supervisor.emp_number', 'supervisorId', 'supervisorId',  'false', null, null, 'label', '<xml><getter>ecMobile</getter></xml>', 100, '0', null, true, 9, '---', false, true),
    (104, 3, 'hs_hr_emp_work_experience.eexp_seqno', 'workExpSeqNo', 'workExpSeqNo',  'false', null, null, 'label', '<xml><getter>ecMobile</getter></xml>', 100, '0', null, true, 10, '---', false, true),
    (105, 3, 'hs_hr_emp_education.edu_code', 'empEduCode', 'empEduCode',  'false', null, null, 'label', '<xml><getter>ecMobile</getter></xml>', 100, '0', null, true, 11, '---', false, true),
    (106, 3, 'hs_hr_emp_skill.skill_code', 'empSkillCode', 'empSkillCode',  'false', null, null, 'label', '<xml><getter>ecMobile</getter></xml>', 100, '0', null, true, 12, '---', false, true),
    (107, 3, 'hs_hr_emp_language.lang_code', 'empLangCode', 'empLangCode',  'false', null, null, 'label', '<xml><getter>ecMobile</getter></xml>', 100, '0', null, true, 13, '---', false, true),
    (108, 3, 'hs_hr_emp_language.elang_type', 'empLangType', 'empLangType',  'false', null, null, 'label', '<xml><getter>ecMobile</getter></xml>', 100, '0', null, true, 13, '---', false, true),
    (109, 3, 'hs_hr_emp_licenses.licenses_code', 'empLicenseCode', 'empLicenseCode',  'false', null, null, 'label', '<xml><getter>ecMobile</getter></xml>', 100, '0', null, true, 14, '---', false, true),
    (110, 3, 'hs_hr_emp_member_detail.membship_code', 'membershipCode', 'membershipCode',  'false', null, null, 'label', '<xml><getter>ecMobile</getter></xml>', 100, '0', null, true, 15, '---', false, true),
    (111, 3, 'hs_hr_emp_member_detail.membtype_code', 'membershipTypeCode', 'membershipTypeCode',  'false', null, null, 'label', '<xml><getter>ecMobile</getter></xml>', 100, '0', null, true, 15, '---', false, true),
    (112, 3, 'ROUND(DATEDIFF(hs_hr_emp_work_experience.eexp_to_date, hs_hr_emp_work_experience.eexp_from_date)/365,1)', 'Duration', 'expDuration',  'false', null, null, 'label', '<xml><getter>expDuration</getter></xml>', 100, '0', null, true, 10, '---', false, false);
    
DISPLAY_FIELDS;
         
         $sql[45] = "INSERT INTO `ohrm_selected_filter_field` (`report_id`, `filter_field_id`, `filter_field_order`, `value1`, `value2`, `where_condition`, `type`) VALUES
                     (1, 21, 4, '0', null, '=', 'Predefined')";

         $sql[46] = <<< SELECTED_DISPLAY_FIELDS
INSERT INTO `ohrm_selected_display_field` (`id`, `display_field_id`, `report_id`) VALUES 
    (5, 9, 5),
    (6, 10, 5),
    (7, 11, 5),
    (8, 12, 5),
    (9, 13, 5),
    (10, 14, 5),
    (11, 15, 5),
    (12, 16, 5),
    (13, 17, 5),
    (14, 18, 5),
    (15, 19, 5),
    (16, 20, 5),
    (17, 21, 5),
    (18, 22, 5),
    (19, 23, 5),
    (20, 24, 5),
    (21, 25, 5),
    (22, 26, 5),
    (23, 27, 5),
    (24, 28, 5),
    (25, 29, 5),
    (26, 30, 5),
    (27, 31, 5),
    (28, 32, 5),
    (29, 33, 5),
    (30, 34, 5),
    (31, 35, 5),
    (32, 36, 5),
    (33, 37, 5),
    (34, 38, 5),
    (35, 39, 5),
    (36, 40, 5),
    (37, 41, 5),
    (38, 42, 5),
    (39, 43, 5),
    (40, 44, 5),
    (41, 45, 5),
    (43, 47, 5),
    (44, 48, 5),
    (45, 49, 5),
    (48, 52, 5),
    (49, 53, 5),
    (50, 54, 5),
    (51, 55, 5),
    (53, 57, 5),
    (54, 58, 5),
    (55, 59, 5),
    (56, 60, 5),
    (57, 61, 5),
    (58, 62, 5),
    (59, 63, 5),
    (60, 64, 5),
    (61, 65, 5),
    (62, 66, 5),
    (63, 67, 5),
    (64, 68, 5),
    (65, 69, 5),
    (66, 70, 5),
    (67, 71, 5),
    (68, 72, 5),
    (69, 73, 5),
    (70, 74, 5),
    (71, 75, 5),
    (72, 76, 5),
    (73, 77, 5),
    (74, 78, 5),
    (75, 79, 5),
    (76, 80, 5),
    (77, 81, 5),
    (78, 82, 5),
    (79, 83, 5),
    (80, 84, 5),
    (81, 85, 5),
    (82, 86, 5),
    (83, 87, 5),
    (84, 88, 5),
    (85, 89, 5),
    (86, 90, 5),
    (87, 91, 5),
    (88, 92, 5),
    (89, 93, 5),
    (90, 94, 5),
    (91, 95, 5),
    (93, 97, 5)         
SELECTED_DISPLAY_FIELDS;
         
    $sql[49] = <<< SELECTED_DISPLAY_FIELD_GROUP
INSERT INTO `ohrm_selected_display_field_group`(`id`, `report_id`, `display_field_group_id`) VALUES
    (1, 5, 1),
    (2, 5, 2),
    (3, 5, 3),
    (4, 5, 4),
    (5, 5, 5),
    (6, 5, 6),
    (7, 5, 7),
    (8, 5, 8),
    (9, 5, 9),
    (10, 5, 10),
    (11, 5, 11),
    (12, 5, 12),
    (13, 5, 13),
    (14, 5, 14),
    (15, 5, 15)   
SELECTED_DISPLAY_FIELD_GROUP;
    
         $this->sql = $sql;
    }
    
    public function getNotes() {
        
    }    
    
}