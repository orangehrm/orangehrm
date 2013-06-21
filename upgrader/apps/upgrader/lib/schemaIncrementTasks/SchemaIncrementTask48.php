<?php
include_once 'SchemaIncrementTask.php';

class SchemaIncrementTask48 extends SchemaIncrementTask {
    
    public $userInputs;
    private $jobTitleMapArray;
    private $empStatusMapArray;
    private $jobTitEmpStatusMapArray;
    private $jobCategoryMapArray;
    private $licenseMapArray;
    private $salaryGradeMapArray;
    private $languageMapArray;
    private $membershipMapArray;
    private $skillMapArray;
    private $educationMapArray;
    private $eduUniversitymapArray;
    private $locationMapArray;
    private $nationalityMapArray;
    private $usersMapArray;
    private $userIdMapArray;
    private $parentMapArray;
    
    
    public function execute() {
        $this->incrementNumber = 48;
        parent::execute();
        
        $result[] = $this->upgradeUtility->executeSql($this->sql[0]);
        
        $result[] = $this->insertIntoOhrmOrganizationGeneralInfo();
        
        for($i = 1; $i <= 3; $i++) {
            $result[] = $this->upgradeUtility->executeSql($this->sql[$i]);
        }
        
        $result[] = $this->insertIntoOhrmJobTitle();
        
        $result[] = $this->insertIntoOhrmEmploymentStatus();
        
        $result[] = $this->readHsHrJobtitEmpStat();
        
        for($i = 4; $i <= 6; $i++) {
            $result[] = $this->upgradeUtility->executeSql($this->sql[$i]);
        }
        
        $result[] = $this->insertIntoHsHrJobtitEmpstat();
        
        $result[] = $this->upgradeUtility->executeSql($this->sql[7]);
        
        $result[] = $this->insertIntoOhrmJobCategory();
        
        $result[] = $this->upgradeUtility->executeSql($this->sql[8]);
        
        $result[] = $this->updateEmplyeeJobDetails();
        
        for($i = 9; $i <= 10; $i++) {
            $result[] = $this->upgradeUtility->executeSql($this->sql[$i]);
        }
        
        $result[] = $this->updateHsHrLicense();
        
        for($i = 11; $i <= 12; $i++) {
            $result[] = $this->upgradeUtility->executeSql($this->sql[$i]);
        }
        
        $result[] = $this->updateHsHrEmpLicenses();
        
        for($i = 13; $i <= 18; $i++) {
            $result[] = $this->upgradeUtility->executeSql($this->sql[$i]);
        }
        
        $result[] = $this->updateHsPrSalaryGrade();
        
        
        for($i = 19; $i <= 20; $i++) {
            $result[] = $this->upgradeUtility->executeSql($this->sql[$i]);
        }
        
        $result[] = $this->updateHsHrEmpBasicsalary();
        
        $result[] = $this->upgradeUtility->executeSql($this->sql[21]);
        
        $result[] = $this->updateHsPrSalaryCurrencyDetail();
        
        for($i = 22; $i <= 26; $i++) {
            $result[] = $this->upgradeUtility->executeSql($this->sql[$i]);
        }
        
        $result[] = $this->updateHsHrLanguage();
        
        for($i = 27; $i <= 28; $i++) {
            $result[] = $this->upgradeUtility->executeSql($this->sql[$i]);
        }
        
        $result[] = $this->updateHsHrEmpLanguage();
        
        for($i = 29; $i <= 32; $i++) {
            $result[] = $this->upgradeUtility->executeSql($this->sql[$i]);
        }
        
        $result[] = $this->insertIntoOhrmMembership();
        
        $result[] = $this->updateHsHrEmpMemberDetail();
        
        for($i = 33; $i <= 35; $i++) {
            $result[] = $this->upgradeUtility->executeSql($this->sql[$i]);
        }
        
        $result[] = $this->updateHsHrSkill();
        
        for($i = 36; $i <= 37; $i++) {
            $result[] = $this->upgradeUtility->executeSql($this->sql[$i]);
        }
        
        $result[] = $this->updateHsHrEmpSkill();
        
        for($i = 38; $i <= 40; $i++) {
            $result[] = $this->upgradeUtility->executeSql($this->sql[$i]);
        }
        
        $result[] = $this->updateHsHrEducation();
        
        for($i = 41; $i <= 42; $i++) {
            $result[] = $this->upgradeUtility->executeSql($this->sql[$i]);
        }
        
        $result[] = $this->updateHsHrEmpEducation();
        
        for($i = 43; $i <= 49; $i++) {
            $result[] = $this->upgradeUtility->executeSql($this->sql[$i]);
        }
        
        $result[] = $this->updateHsHrLocation();
        
        for($i = 50; $i <= 52; $i++) {
            $result[] = $this->upgradeUtility->executeSql($this->sql[$i]);
        }
        
        $result[] = $this->updateHsHrEmpLocations();
        
        for($i = 53; $i <= 54; $i++) {
            $result[] = $this->upgradeUtility->executeSql($this->sql[$i]);
        }
        
        $result[] = $this->updateHsHrNationality();
        
        for($i = 55; $i <= 56; $i++) {
            $result[] = $this->upgradeUtility->executeSql($this->sql[$i]);
        }
        
        $result[] = $this->updateHsHrEmployeeNationCode();
        
        for($i = 57; $i <= 62; $i++) {
            $result[] = $this->upgradeUtility->executeSql($this->sql[$i]);
        }
        
        $result[] = $this->insertIntoOhrmUser();
        
        $result[] = $this->updateOhrmUser();
        
        $result[] = $this->updateHsHrMailNotifications();
        
        $result[] = $this->upgradeUtility->executeSql($this->sql[63]);
        
        $result[] = $this->updateOhrmTimesheetActionLog();
        
        for($i = 64; $i <= 88; $i++) {
            $result[] = $this->upgradeUtility->executeSql($this->sql[$i]);
        }
        
        $result[] = $this->updateOhrmJobVacancy();
        
        for($i = 89; $i <= 94; $i++) {
            $result[] = $this->upgradeUtility->executeSql($this->sql[$i]);
        }
        
        $result[] = $this->updateHsHrEmployeeTerminationId();
        
        for($i = 95; $i <= 101; $i++) {
            $result[] = $this->upgradeUtility->executeSql($this->sql[$i]);
        }
        
        $result[] = $this->updateOhrmEmailNotification();
        
        for($i = 102; $i <= 104; $i++) {
            $result[] = $this->upgradeUtility->executeSql($this->sql[$i]);
        }
        
        $result[] = $this->updateOhrmSubunit();
        
        $result[] = $this->upgradeUtility->executeSql($this->sql[105]);
        
        for($i = 107; $i <= 135; $i++) {
            $result[] = $this->upgradeUtility->executeSql($this->sql[$i]);
        }
        
        $this->updateHsHrPerformanceReview();
        
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
    
        $sql[0] = "create table `ohrm_organization_gen_info` (
                      `id` int(4) not null auto_increment,
                      `name` varchar(100) not null,
                      `tax_id` varchar(30) default null,
                      `registration_number` varchar(30) default null,
                      `phone` varchar(30) default null,
                      `fax` varchar(30) default null,
                      `email` varchar(30) default null,
                      `country` varchar(30) default null,
                      `province` varchar(30) default null,
                      `city` varchar(30) default null,
                      `zip_code` varchar(30) default null,
                      `street1` varchar(100) default null,
                      `street2` varchar(100) default null,
                      `note` varchar(255) default null,
                      primary key (`id`)
                    ) engine=innodb default charset=utf8;";
        
        $sql[1] = "ALTER TABLE hs_hr_config
                        CHANGE value value varchar(512) not null default '';";
        
        $sql[2] = "create table `ohrm_employment_status` (
                          `id` int not null auto_increment,
                          `name` varchar(60) not null,
                        primary key  (`id`)
                        ) engine=innodb default charset=utf8;";
        
        $sql[3] = "create table `ohrm_job_title` (
                          `id` int(13) not null auto_increment,
                          `job_title` varchar(100) not null,
                          `job_description` varchar(400) default null,
                          `note` varchar(400) default null,
                          `is_deleted` tinyint(1) default 0,
                          primary key (`id`)
                        ) engine=innodb default charset=utf8;";
        
        $sql[4] = "TRUNCATE hs_hr_jobtit_empstat;";
        
        $sql[5] = "ALTER TABLE `hs_hr_jobtit_empstat` 
                            DROP FOREIGN KEY `hs_hr_jobtit_empstat_ibfk_1`,
                            DROP FOREIGN KEY `hs_hr_jobtit_empstat_ibfk_2`;";
        
        $sql[6] = "ALTER TABLE hs_hr_jobtit_empstat
                        CHANGE jobtit_code jobtit_code int(7) not null,
                        CHANGE estat_code estat_code int(13) not null,
                        add constraint foreign key (jobtit_code) references ohrm_job_title(id) on delete cascade,
                        add constraint foreign key (estat_code) references ohrm_employment_status(id) on delete cascade;";

        $sql[7] = "create table `ohrm_job_category` (
                        `id` int not null auto_increment,
                        `name` varchar(60) default null,
                        primary key  (`id`)
                        ) engine=innodb default charset=utf8;";
        
        $sql[8] = "ALTER TABLE hs_hr_employee
                        DROP FOREIGN KEY hs_hr_employee_ibfk_1,
                        DROP FOREIGN KEY hs_hr_employee_ibfk_2,
                        DROP FOREIGN KEY hs_hr_employee_ibfk_3,
                        DROP FOREIGN KEY hs_hr_employee_ibfk_4,
                        DROP FOREIGN KEY hs_hr_employee_ibfk_5,
                        DROP FOREIGN KEY hs_hr_employee_ibfk_6;";
        
        $sql[9] = "ALTER TABLE hs_hr_employee
                        DROP KEY ethnic_race_code,
                        CHANGE emp_status emp_status int(13) default null,
                        CHANGE job_title_code job_title_code int(7) default null,
                        CHANGE eeo_cat_code eeo_cat_code int default null,
                        add constraint `hs_hr_employee_ibfk_3` foreign key (job_title_code)
                             references ohrm_job_title(id) on delete set null,
                        add constraint `hs_hr_employee_ibfk_4` foreign key (emp_status)
                             references ohrm_employment_status(id) on delete set null,
                        add constraint `hs_hr_employee_ibfk_5` foreign key (eeo_cat_code)
                             references ohrm_job_category(id) on delete set null;";
        
        $sql[10] = "ALTER TABLE `hs_hr_emp_licenses` 
                        DROP FOREIGN KEY `hs_hr_emp_licenses_ibfk_2`";
        
        $sql[11] = "ALTER TABLE hs_hr_licenses 
                        DROP PRIMARY KEY,
                        CHANGE licenses_code id int not null auto_increment,
                        CHANGE licenses_desc name varchar(100) not null,
                        ADD primary key (id);";
        
        $sql[12] = "RENAME TABLE hs_hr_licenses TO ohrm_license";
        
        $sql[13] = "ALTER TABLE hs_hr_emp_licenses 
                        DROP PRIMARY KEY,
                        DROP KEY licenses_code,
                        CHANGE emp_number emp_number int not null,
                        CHANGE licenses_code license_id int not null,
                        CHANGE licenses_date license_issued_date date null default null,
                        CHANGE licenses_renewal_date license_expiry_date date null default null,
                        ADD KEY `license_id` (`license_id`),
                        ADD primary key (emp_number,license_id)";
        
        $sql[14] = "RENAME TABLE hs_hr_emp_licenses TO ohrm_emp_license";
        
        $sql[15] = "alter table ohrm_emp_license
                        add constraint foreign key (license_id)
                             references ohrm_license(id) on delete cascade;";
        
        $sql[16] = "ALTER TABLE `hs_pr_salary_currency_detail`
                            DROP FOREIGN KEY `hs_pr_salary_currency_detail_ibfk_2`;";
        
        $sql[17] = "ALTER TABLE `hs_hr_job_title`
                            DROP FOREIGN KEY `hs_hr_job_title_ibfk_1`;";
        
        $sql[18] = "ALTER TABLE `hs_hr_emp_basicsalary`
                            DROP FOREIGN KEY `hs_hr_emp_basicsalary_ibfk_1`;";
        
        $sql[19] = "ALTER TABLE hs_pr_salary_grade 
                        DROP PRIMARY KEY,
                        DROP KEY sal_grd_name,
                        CHANGE sal_grd_code id int not null auto_increment,
                        CHANGE sal_grd_name name varchar(60) default null unique,
                        ADD primary key (id)";
        
        $sql[20] = "RENAME TABLE hs_pr_salary_grade TO ohrm_pay_grade";
        
        $sql[21] = "ALTER TABLE hs_hr_emp_basicsalary 
                        CHANGE sal_grd_code sal_grd_code int default null";
        
        $sql[22] = "ALTER TABLE hs_pr_salary_currency_detail 
                        DROP PRIMARY KEY,
                        CHANGE sal_grd_code pay_grade_id int not null ,
                        CHANGE salcurr_dtl_minsalary min_salary double default null,
                        DROP column salcurr_dtl_stepsalary,
                        CHANGE salcurr_dtl_maxsalary max_salary double default null,
                        ADD primary key (pay_grade_id, currency_id)";
        
        $sql[23] = "RENAME TABLE hs_pr_salary_currency_detail TO ohrm_pay_grade_currency";
        
        $sql[24] = "alter table hs_hr_emp_basicsalary
                        add constraint `hs_hr_emp_basicsalary_ibfk_1` foreign key (sal_grd_code)
                            references ohrm_pay_grade(id) on delete cascade;";
        
        $sql[25] = "alter table ohrm_pay_grade_currency
                        add constraint foreign key (pay_grade_id)
                            references ohrm_pay_grade(id) on delete cascade;";
        
        $sql[26] = "ALTER TABLE `hs_hr_emp_language`
                            DROP FOREIGN KEY `hs_hr_emp_language_ibfk_2`;";
        
        $sql[27] = "ALTER TABLE hs_hr_language 
                        DROP PRIMARY KEY,
                        CHANGE lang_code id int not null auto_increment,
                        CHANGE lang_name name varchar(120) default null,
                        ADD primary key (id)";
        
        $sql[28] = "RENAME TABLE hs_hr_language TO ohrm_language";
        
        $sql[29] = "ALTER TABLE hs_hr_emp_language 
                        DROP PRIMARY KEY,
                        DROP KEY lang_code,
                        CHANGE lang_code lang_id int not null,
                        CHANGE elang_type fluency smallint default '0',
                        ADD KEY `lang_id` (`lang_id`),
                        ADD primary key (emp_number,lang_id,fluency)";
        
        $sql[30] = "alter table hs_hr_emp_language
                        add constraint foreign key (lang_id)
                             references ohrm_language(id) on delete cascade;";
        
        $sql[31] = "create table `ohrm_membership` (
                          `id` int(6) not null auto_increment,
                          `name` varchar(100) not null,
                          primary key  (`id`)
                        ) engine=innodb default charset=utf8;";
        
        $sql[32] = "ALTER TABLE `hs_hr_emp_member_detail`
                            DROP FOREIGN KEY `hs_hr_emp_member_detail_ibfk_1`,
                            DROP FOREIGN KEY `hs_hr_emp_member_detail_ibfk_2`,
                            DROP FOREIGN KEY `hs_hr_emp_member_detail_ibfk_3`;";
        
        $sql[33] = "ALTER TABLE hs_hr_emp_member_detail 
                        DROP PRIMARY KEY,
                        CHANGE membship_code membship_code int(6) not null default 0,
                        DROP column membtype_code,
                        ADD primary key (emp_number,membship_code)";
        
        $sql[34] = "alter table hs_hr_emp_member_detail
                        add constraint `hs_hr_emp_member_detail_ibfk_1` foreign key (membship_code)
                             references ohrm_membership(id) on delete cascade,
                        add constraint `hs_hr_emp_member_detail_ibfk_2` foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;";
        
        $sql[35] = "ALTER TABLE `hs_hr_emp_skill`
                            DROP FOREIGN KEY `hs_hr_emp_skill_ibfk_2`;";
        
        $sql[36] = "ALTER TABLE hs_hr_skill 
                        DROP PRIMARY KEY,
                        CHANGE skill_code id int not null auto_increment,
                        CHANGE skill_name name varchar(120) default null,
                        CHANGE skill_description description text default null,
                        ADD primary key (id)";
        
        $sql[37] = "RENAME TABLE hs_hr_skill TO ohrm_skill";
        
        $sql[38] = "ALTER TABLE hs_hr_emp_skill
                        DROP KEY skill_code,
                        CHANGE skill_code skill_id int not null,
                        ADD KEY `skill_id` (`skill_id`)";
        
        $sql[39] = "alter table hs_hr_emp_skill
                        add constraint foreign key (skill_id)
                             references ohrm_skill(id) on delete cascade;";
        
        $sql[40] = "ALTER TABLE `hs_hr_emp_education`
                            DROP FOREIGN KEY `hs_hr_emp_education_ibfk_2`,
                            ADD column institute varchar(100) default null;";
        
        $sql[41] = "ALTER TABLE hs_hr_education 
                        DROP PRIMARY KEY,
                        CHANGE edu_code id int not null auto_increment,
                        CHANGE edu_deg name varchar(100) not null,
                        DROP column edu_uni,
                        ADD primary key (id)";
        
        $sql[42] = "RENAME TABLE hs_hr_education TO ohrm_education";
        
        $sql[43] = "ALTER TABLE hs_hr_emp_education 
                        DROP PRIMARY KEY,
                        CHANGE emp_number emp_number int not null,
                        CHANGE edu_code education_id int not null,
                        CHANGE edu_major major varchar(100) default null,
                        CHANGE edu_year year decimal(4,0) default null,
                        CHANGE edu_gpa score varchar(25) default null,
                        CHANGE edu_start_date start_date date default null,
                        CHANGE edu_end_date end_date date default null,
                        ADD primary key (emp_number, education_id)";
        
        $sql[44] = "RENAME TABLE hs_hr_emp_education TO ohrm_emp_education";
        
        $sql[45] = "alter table ohrm_emp_education
                        add constraint foreign key (education_id)
                             references ohrm_education(id) on delete cascade;";
        
        $sql[46] = "ALTER TABLE ohrm_emp_reporting_method 
                        DROP PRIMARY KEY,
                        CHANGE reporting_method_id reporting_method_id int(7) not null auto_increment,
                        ADD primary key (reporting_method_id)";
        
        $sql[47] = "ALTER TABLE `hs_hr_compstructtree`
                            DROP FOREIGN KEY `hs_hr_compstructtree_ibfk_1`;";
        
        $sql[48] = "ALTER TABLE `hs_hr_emp_locations`
                            DROP FOREIGN KEY `hs_hr_emp_locations_ibfk_1`,
                            DROP FOREIGN KEY `hs_hr_emp_locations_ibfk_2`;";
        
        $sql[49] = "ALTER TABLE `hs_hr_location`
                            DROP FOREIGN KEY `hs_hr_location_ibfk_1`;";
        
        $sql[50] = "ALTER TABLE hs_hr_location 
                        DROP PRIMARY KEY,
                        DROP KEY loc_country,
                        CHANGE loc_code id int not null auto_increment,
                        CHANGE loc_name name varchar(110) not null,
                        CHANGE loc_country country_code varchar(3) not null,
                        CHANGE loc_state province varchar(60) default null,
                        CHANGE loc_city city varchar(60) default null,
                        CHANGE loc_add address varchar(255) default null,
                        CHANGE loc_zip zip_code varchar(35) default null,
                        CHANGE loc_phone phone varchar(35) default null,
                        CHANGE loc_fax fax varchar(35) default null,
                        CHANGE loc_comments notes varchar(255) default null,
                        ADD KEY `country_code` (`country_code`),
                        ADD primary key (id)";
        
        $sql[51] = "RENAME TABLE hs_hr_location TO ohrm_location";
        
        $sql[52] = "alter table ohrm_location
                          add constraint foreign key (country_code)
                             references hs_hr_country(cou_code) on delete cascade;";
        
        $sql[53] = "ALTER TABLE hs_hr_emp_locations 
                        DROP PRIMARY KEY,
                        DROP KEY loc_code,
                        CHANGE emp_number emp_number int not null,
                        CHANGE loc_code location_id int not null,
                        ADD KEY `location_id` (`location_id`),
                        ADD primary key (emp_number, location_id)";
        
        $sql[54] = "alter table `hs_hr_emp_locations`
                            add constraint `hs_hr_emp_locations_ibfk_1` foreign key (`location_id`)
                                references ohrm_location(`id`) on delete cascade,
                            add constraint `hs_hr_emp_locations_ibfk_2` foreign key (`emp_number`)
                                references hs_hr_employee(`emp_number`) on delete cascade;";
        
        $sql[55] = "ALTER TABLE hs_hr_nationality 
                        DROP PRIMARY KEY,
                        CHANGE nat_code id int(6) not null auto_increment,
                        CHANGE nat_name name varchar(100) not null,
                        ADD primary key (id)";
        
        $sql[56] = "RENAME TABLE hs_hr_nationality TO ohrm_nationality";
        
        $sql[57] = "ALTER TABLE hs_hr_employee
                        CHANGE nation_code nation_code int(4) default null,
                        add constraint `hs_hr_employee_ibfk_2` foreign key (nation_code)
                             references ohrm_nationality(id) on delete set null;";
        
        $sql[58] = "ALTER TABLE `ohrm_timesheet_action_log`
                            DROP FOREIGN KEY `ohrm_timesheet_action_log_ibfk_1`;";
        
        $sql[59] = "ALTER TABLE `hs_hr_mailnotifications`
                            DROP FOREIGN KEY `hs_hr_mailnotifications_ibfk_1`;";
        
        $sql[60] = "create table `ohrm_user`(
                            `id` int(10) not null auto_increment,
                            `user_role_id` int(10) not null,
                            `emp_number` int(13) DEFAULT NULL,
                            `user_name` varchar(40) unique,
                            `user_password` varchar(40) DEFAULT NULL,
                            `deleted` tinyint(1) NOT NULL DEFAULT '0',
                            `status` tinyint(1) NOT NULL DEFAULT '1',
                            `date_entered` datetime null default null,
                            `date_modified` datetime null default null,
                            `modified_user_id` int(10) default null,
                            `created_by` int(10) default null,
                            key `user_role_id` (`user_role_id`),
                            key `emp_number` (`emp_number`),
                            key `modified_user_id`(`modified_user_id`),
                            key `created_by`(`created_by`),
                            primary key (`id`)
                        )engine=innodb default charset=utf8;";
        
        $sql[61] = "create table `ohrm_user_role`(
                            `id` int(10) not null auto_increment,
                            `name` varchar(255) not null,
                            `is_assignable` tinyint(1) default 0,
                            `is_predefined` tinyint(1) default 0,
                            primary key (`id`)
                        )engine=innodb default charset=utf8;";
        
        $sql[62] = "INSERT INTO `ohrm_user_role` (`id`, `name`, `is_assignable`, `is_predefined`) VALUES
                            (1, 'Admin', 0, 1),
                            (2, 'ESS', 0, 1),
                            (3, 'Supervisor', 1, 0),
                            (4, 'ProjectAdmin', 1, 0),
                            (5, 'Interviewer', 1, 0),
                            (6, 'Offerer', 1, 0),
                            (7, 'Interviewer', 1, 0),
                            (8, 'Offerer', 1, 0);";
        
        $sql[63] = "ALTER TABLE hs_hr_mailnotifications 
                        CHANGE user_id user_id int(20) not null";
        
        $sql[64] = "ALTER TABLE ohrm_timesheet_action_log 
                        CHANGE performed_by performed_by int(20) not null,
                        add constraint foreign key (performed_by)
                             references ohrm_user(id) on delete cascade";
        
        $sql[65] = "alter table hs_hr_mailnotifications
                        add constraint foreign key (user_id)
                            references ohrm_user(id) on delete cascade;";
        
        $sql[66] = "alter table `ohrm_user`
                        add constraint `ohrm_user_ibfk_2` foreign key (`user_role_id`)
                            references ohrm_user_role(`id`) on delete cascade;";
        
        $sql[67] = "alter table `ohrm_user`
                        add constraint `ohrm_user_ibfk_1` foreign key (`emp_number`)
                            references hs_hr_employee(`emp_number`) on delete cascade;";
        
        $sql[68] = "ALTER TABLE hs_hr_customer 
                        CHANGE customer_id customer_id int(11) not null auto_increment,
                        CHANGE name name varchar(100) not null,
                        CHANGE description description varchar(255) default null,
                        CHANGE deleted is_deleted tinyint(1) default 0;";
        
        $sql[69] = "ALTER TABLE `hs_hr_project`
                            DROP FOREIGN KEY `hs_hr_project_ibfk_1`;";
        
        $sql[70] = "RENAME TABLE hs_hr_customer TO ohrm_customer";
        
        $sql[71] = "ALTER TABLE `hs_hr_project_activity`
                            DROP FOREIGN KEY `hs_hr_project_activity_ibfk_1`;";
        
        $sql[72] = "ALTER TABLE `hs_hr_project_admin`
                            DROP FOREIGN KEY `hs_hr_project_admin_ibfk_1`,
                            DROP FOREIGN KEY `hs_hr_project_admin_ibfk_2`;";
        
        $sql[73] = "ALTER TABLE hs_hr_project 
                        CHANGE project_id project_id int(11) not null auto_increment,
                        CHANGE description description varchar(256) default null,
                        CHANGE deleted is_deleted tinyint(1) default 0;";
        
        $sql[74] = "RENAME TABLE hs_hr_project TO ohrm_project";
        
        $sql[75] = "ALTER TABLE hs_hr_project_activity 
                        CHANGE activity_id activity_id int(11) not null auto_increment,
                        CHANGE name name varchar(110) default null,
                        CHANGE deleted is_deleted tinyint(1) default 0;";
        
        $sql[76] = "RENAME TABLE hs_hr_project_activity TO ohrm_project_activity";
        
        $sql[77] = "RENAME TABLE hs_hr_project_admin TO ohrm_project_admin";
        
        $sql[78] = "alter table `ohrm_project_activity`
                            add constraint foreign key (`project_id`) references `ohrm_project` (`project_id`) on delete cascade;";
        
        $sql[79] = "alter table `ohrm_project_admin`
                            add constraint foreign key (`project_id`) references `ohrm_project` (`project_id`) on delete cascade,
                            add constraint foreign key (`emp_number`) references `hs_hr_employee` (`emp_number`) on delete cascade;";
        
        $sql[80] = "ALTER TABLE `hs_hr_employee_workshift`
                            DROP FOREIGN KEY `hs_hr_employee_workshift_ibfk_1`,
                            DROP FOREIGN KEY `hs_hr_employee_workshift_ibfk_2`;";
        
        $sql[81] = "ALTER TABLE hs_hr_workshift 
                        DROP PRIMARY KEY,
                        CHANGE workshift_id id int(11) not null auto_increment,
                        ADD primary key (id)";
        
        $sql[82] = "RENAME TABLE hs_hr_workshift TO ohrm_work_shift";
        
        $sql[83] = "ALTER TABLE hs_hr_employee_workshift 
                        DROP PRIMARY KEY,
                        CHANGE workshift_id work_shift_id int(11) not null auto_increment,
                        ADD primary key (work_shift_id,emp_number)";
        
        $sql[84] = "RENAME TABLE hs_hr_employee_workshift TO ohrm_employee_work_shift";
        
        $sql[85] = "alter table `ohrm_employee_work_shift`
                            add constraint foreign key (`work_shift_id`) references `ohrm_work_shift` (`id`) on delete cascade,
                            add constraint foreign key (`emp_number`) references `hs_hr_employee` (`emp_number`) on delete cascade;";
        
        $sql[86] = "create table `ohrm_job_specification_attachment`(
                            `id` int(13) not null auto_increment,
                            `job_title_id` int(13) not null,
                            `file_name` varchar(200) not null,
                                `file_type` varchar(200) default null,
                            `file_size` int(11) not null,
                            `file_content` mediumblob,
                            primary key (`id`)
                        )engine=innodb default charset=utf8;";
        
        $sql[87] = "alter table ohrm_job_specification_attachment
                            add constraint foreign key (job_title_id)
                             references ohrm_job_title(id) on delete cascade;";
        
        $sql[88] = "ALTER TABLE `ohrm_job_vacancy`
                            DROP FOREIGN KEY `ohrm_job_vacancy_ibfk_1`;";
        
        $sql[89] = "ALTER TABLE ohrm_job_vacancy 
                        CHANGE job_title_code job_title_code int(4) not null";
        
        $sql[90] = "alter table ohrm_job_vacancy
                         add constraint `ohrm_job_vacancy_ibfk_1` foreign key (job_title_code)
                             references ohrm_job_title(id) on delete cascade;";
        
        $sql[91] = "create table `ohrm_emp_termination`(
                            `id` int(4) not null auto_increment,
                            `emp_number` int(4) default null,
                                `reason_id` int(4) default null,
                            `termination_date` date not null,
                                `note` varchar(255) default null,
                            primary key (`id`)
                        )engine=innodb default charset=utf8;";
        
        $sql[92] = "create table `ohrm_emp_termination_reason`(
                            `id` int(4) not null auto_increment,
                            `name` varchar(100) default null,
                            primary key (`id`)
                        )engine=innodb default charset=utf8;";
        
        $sql[93] = "INSERT INTO `ohrm_emp_termination_reason` VALUES (1, 'Other'),
                            (2, 'Retired'),
                            (3, 'Contract Not Renewed'),
                            (4, 'Resigned - Company Requested'),
                            (5, 'Resigned - Self Proposed'),
                            (6, 'Resigned'),
                            (7, 'Deceased'),
                            (8, 'Physically Disabled/Compensated'),
                            (9, 'Laid-off'),
                            (10, 'Dismissed');";
        
        $sql[94] = "ALTER TABLE hs_hr_employee
                        add column `termination_id` int(4) default null;";
        
        $sql[95] = "ALTER TABLE hs_hr_employee
                            DROP column terminated_date,
                            DROP column termination_reason,
                            add constraint `hs_hr_employee_ibfk_6` foreign key (termination_id)
                                 references ohrm_emp_termination(id) on delete set null;";
        
        $sql[96] = "alter table ohrm_emp_termination
                         add constraint foreign key (reason_id)
                            references ohrm_emp_termination_reason(id) on delete set null;";
        
        $sql[97] = "alter table ohrm_emp_termination
                         add constraint foreign key (emp_number)
                            references hs_hr_employee(emp_number) on delete cascade;";
        
        $sql[98] = "create table `ohrm_email_notification` (
                          `id` int(6) not null auto_increment,
                          `name` varchar(100) not null,
                          `is_enable` int(6) not null,
                          primary key  (`id`)
                        ) engine=innodb default charset=utf8;";
        
        $sql[99] = "create table `ohrm_email_subscriber` (
                          `id` int(6) not null auto_increment,
                          `notification_id` int(6) not null,
                          `name` varchar(100) not null,
                          `email` varchar(100) not null,
                          primary key  (`id`)
                        ) engine=innodb default charset=utf8;";
        
        $sql[100] = "alter table ohrm_email_subscriber
                        add constraint foreign key (notification_id)
                            references ohrm_email_notification(id) on delete cascade;";
        
        $sql[101] = "INSERT INTO `ohrm_email_notification` (`id`, `name`, `is_enable`) VALUES
                            (1, 'Leave Applications', 0),
                            (2, 'Leave Assignments', 0),
                            (3, 'Leave Approvals', 0),
                            (4, 'Leave Cancellations', 0),
                            (5, 'Leave Rejections', 0),
                            #(6, 'HSP Notifications', 0),
                            (7, 'Performance Review Submissions', 0);";
        
        $sql[102] = "create table `ohrm_user_selection_rule`(
                            `id` int(10) not null auto_increment,
                            `name` varchar(255) not null,
                                `description` varchar(255) ,
                            `implementation_class` varchar(255) not null,
                                `rule_xml_data` text,
                            primary key (`id`)
                        )engine=innodb default charset=utf8;";
        
        $sql[103] = "create table `ohrm_role_user_selection_rule`(
                            `user_role_id` int(10) not null,
                                `selection_rule_id` int(10) not null,
                                `configurable_params` text,
                            primary key (`user_role_id`,`selection_rule_id`)
                        )engine=innodb default charset=utf8;";
        
        $sql[104] = "create table `ohrm_subunit` (
                          `id` int(6) not null auto_increment,
                          `name` varchar(100) not null unique,
                          `unit_id` varchar(100) default null,
                          `description` varchar(400),
                          `lft` smallint(6) unsigned default null,
                          `rgt` smallint(6) unsigned default null,
                          `level` smallint(6) unsigned default null,
                          primary key (`id`)
                        ) engine=innodb default charset=utf8;";
        
        $sql[105] = "alter table hs_hr_employee
                           add constraint `hs_hr_employee_ibfk_1` foreign key (work_station)
                            references ohrm_subunit(id) on delete set null;";
        
        $uniqueIdArray = array(
            'hs_hr_language', 'hs_hr_customer', 'hs_hr_job_title', 'hs_hr_empstat', 'hs_hr_eec', 'hs_hr_licenses', 'hs_hr_location',
            'hs_hr_membership', 'hs_hr_membership_type', 'hs_hr_education', 'hs_hr_ethnic_race', 'hs_hr_skill', 'hs_hr_users', 'hs_pr_salary_grade',
            'hs_hr_project', 'hs_hr_compstructtree', 'hs_hr_project_activity', 'hs_hr_workshift', 'hs_hr_job_spec', 'hs_hr_nationality'
        );
        
        $tabelNames = join("','",$uniqueIdArray);  
        
        $sql[107] = "DELETE FROM hs_hr_unique_id WHERE table_name IN ('$tabelNames')";
        
        $row[1] = 'SELECT selectCondition FROM ohrm_project_activity LEFT JOIN (SELECT * FROM ohrm_timesheet_item WHERE whereCondition1) AS ohrm_timesheet_item  ON (ohrm_timesheet_item.activity_id = ohrm_project_activity.activity_id) LEFT JOIN ohrm_project ON (ohrm_project.project_id = ohrm_project_activity.project_id) LEFT JOIN hs_hr_employee ON (hs_hr_employee.emp_number = ohrm_timesheet_item.employee_id) LEFT JOIN ohrm_timesheet ON (ohrm_timesheet.timesheet_id = ohrm_timesheet_item.timesheet_id) LEFT JOIN ohrm_customer ON (ohrm_customer.customer_id = ohrm_project.customer_id) WHERE whereCondition2';
        $row[2] = 'SELECT selectCondition FROM hs_hr_employee LEFT JOIN (SELECT * FROM ohrm_attendance_record WHERE ( ( ohrm_attendance_record.punch_in_user_time BETWEEN "#@fromDate@,@1970-01-01@#" AND #@"toDate"@,@CURDATE()@# ) AND ( ohrm_attendance_record.punch_out_user_time BETWEEN "#@fromDate@,@1970-01-01@#" AND #@"toDate"@,@CURDATE()@# ) ) ) AS ohrm_attendance_record ON (hs_hr_employee.emp_number = ohrm_attendance_record.employee_id) WHERE hs_hr_employee.emp_number = #@employeeId@,@hs_hr_employee.emp_number AND (hs_hr_employee.termination_id is null) @# AND (hs_hr_employee.job_title_code = #@"jobTitle")@,@hs_hr_employee.job_title_code OR hs_hr_employee.job_title_code is null)@# AND (hs_hr_employee.work_station IN (#@subUnit)@,@SELECT id FROM ohrm_subunit) OR hs_hr_employee.work_station is null@#) AND (hs_hr_employee.emp_status = #@"employeeStatus")@,@hs_hr_employee.emp_status OR hs_hr_employee.emp_status is null)@#';
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
        
        $sql[108] = "UPDATE ohrm_report_group SET core_sql = CASE report_group_id
                        WHEN '1' THEN '$row[1]'
                        WHEN '2' THEN '$row[2]'
                        WHEN '3' THEN '$row[3]'
                        END
                        WHERE report_group_id in(1,2,3)";
        
        $row[4] = 'ohrm_project.project_id';
        $row[5] = 'ohrm_project_activity.is_deleted';
        $row[6] = 'ohrm_project_activity.activity_id';
        $row[7] = 'ohrm_project.project_id';
        $row[8] = 'ohrm_emp_education.education_id';
        $row[9] = 'hs_hr_emp_language.lang_id';
        $row[10] = 'hs_hr_emp_skill.skill_id';
        $row[11] = 'ohrm_location.id';
        $row[12] = 'ohrm_project_activity.is_deleted';
        
        $sql[109] = "UPDATE ohrm_filter_field SET where_clause_part = CASE filter_field_id
                        WHEN '1' THEN '$row[4]'
                        WHEN '2' THEN '$row[5]'
                        WHEN '5' THEN '$row[6]'
                        WHEN '6' THEN '$row[7]'
                        WHEN '10' THEN '$row[8]'
                        WHEN '15' THEN '$row[9]'
                        WHEN '16' THEN '$row[10]'
                        WHEN '20' THEN '$row[11]'
                        WHEN '21' THEN '$row[12]'
                        END
                        WHERE filter_field_id in(1,2,5,6,10,15,16,20,21)";
        
        $row[13] = 'ohrm_project.name';
        $row[14] = 'ohrm_project_activity.name';
        $row[15] = 'ohrm_project_activity.project_id';
        $row[16] = 'ohrm_project_activity.activity_id';
        $row[17] = 'ohrm_project_activity.name';
        $row[18] = 'ohrm_nationality.name';
        $row[19] = 'ohrm_emp_education.year';
        $row[20] = 'ohrm_skill.name';
        $row[21] = 'ohrm_language.name';
        $row[22] = 'ohrm_license.name';
        $row[23] = 'ohrm_emp_license.license_issued_date';
        $row[24] = 'ohrm_emp_license.license_expiry_date';
        $row[25] = 'ohrm_pay_grade.name';
        $row[26] = 'ohrm_job_title.job_title';
        $row[27] = 'ohrm_employment_status.name';
        $row[28] = 'ohrm_subunit.name';
        $row[29] = 'ohrm_location.name';
        $row[30] = 'CASE hs_hr_emp_language.fluency WHEN 1 THEN "Writing" WHEN 2 THEN "Speaking" WHEN 3 THEN "Reading" END';
        $row[31] = 'ohrm_emp_education.education_id';
        $row[32] = 'hs_hr_emp_skill.skill_id';
        $row[33] = 'hs_hr_emp_language.lang_id';
        $row[34] = 'hs_hr_emp_language.fluency';
        $row[35] = 'ohrm_emp_license.license_id';
        $row[36] = 'ohrm_job_category.name';
        
        $sql[110] = "UPDATE ohrm_display_field SET name = CASE display_field_id
                        WHEN '1' THEN '$row[13]'
                        WHEN '2' THEN '$row[14]'
                        WHEN '3' THEN '$row[15]'
                        WHEN '4' THEN '$row[16]'
                        WHEN '8' THEN '$row[17]'
                        WHEN '14' THEN '$row[18]'
                        WHEN '48' THEN '$row[19]'
                        WHEN '52' THEN '$row[20]'
                        WHEN '55' THEN '$row[21]'
                        WHEN '59' THEN '$row[22]'
                        WHEN '60' THEN '$row[23]'
                        WHEN '61' THEN '$row[24]'
                        WHEN '65' THEN '$row[25]'
                        WHEN '77' THEN '$row[26]'
                        WHEN '78' THEN '$row[27]'
                        WHEN '82' THEN '$row[28]'
                        WHEN '83' THEN '$row[29]'
                        WHEN '92' THEN '$row[30]'
                        WHEN '105' THEN '$row[31]'
                        WHEN '106' THEN '$row[32]'
                        WHEN '107' THEN '$row[33]'
                        WHEN '108' THEN '$row[34]'
                        WHEN '109' THEN '$row[35]'
                        WHEN '80' THEN '$row[36]'
                        END
                        WHERE display_field_id in(1,2,3,4,8,14,48,52,55,59,60,61,65,77,78,82,83,92,105,106,107,108,109,80)";
        
        $row[36] = 'ohrm_education.name';
        $row[37] = 'ohrm_emp_education.score';
        $row[38] = 'Level';
        $row[39] = 'Score';
        
        $sql[111] = "UPDATE ohrm_display_field SET name = CASE display_field_id
                        WHEN '47' THEN '$row[36]'
                        WHEN '49' THEN '$row[37]'
                        END,
                        label = CASE display_field_id
                        WHEN '47' THEN '$row[38]'
                        WHEN '49' THEN '$row[39]'
                        END
                        WHERE display_field_id in(47,49)";
        
        $sql[112] = "UPDATE ohrm_display_field 
                        SET name = 'ohrm_membership.name', field_alias = 'name', element_property = '<xml><getter>name</getter></xml>' WHERE display_field_id = '35'";
        
        $sql[113] = "DELETE FROM ohrm_display_field WHERE display_field_id IN ('16', '34', '79', '111');";
        
        $sql[114] = "UPDATE ohrm_group_field SET 
                         group_by_clause = 'GROUP BY ohrm_project_activity.activity_id'
                         WHERE group_field_id = '1'";
        
        $sql[115] = "DELETE FROM ohrm_selected_display_field WHERE id IN ('12', '30', '75');";
        
        $sql[116] = <<<EOT
UPDATE ohrm_composite_display_field SET 
name = 'CONCAT(ohrm_customer.name, " - " ,ohrm_project.name)'
WHERE composite_display_field_id = '2'
EOT;
        
        $sql[117] = "INSERT INTO `ohrm_nationality` ( `name`) VALUES
                            ('Afghan'),
                            ('Albanian'),
                            ('Algerian'),
                            ('American'),
                            ('Andorran'),
                            ('Angolan'),
                            ('Antiguans'),
                            ('Argentinean'),
                            ('Armenian'),
                            ('Australian'),
                            ('Austrian'),
                            ('Azerbaijani'),
                            ('Bahamian'),
                            ('Bahraini'),
                            ('Bangladeshi'),
                            ('Barbadian'),
                            ('Barbudans'),
                            ('Batswana'),
                            ('Belarusian'),
                            ('Belgian'),
                            ('Belizean'),
                            ('Beninese'),
                            ('Bhutanese'),
                            ('Bolivian'),
                            ('Bosnian'),
                            ('Brazilian'),
                            ('British'),
                            ('Bruneian'),
                            ('Bulgarian'),
                            ('Burkinabe'),
                            ('Burmese'),
                            ('Burundian'),
                            ('Cambodian'),
                            ('Cameroonian'),
                            ('Canadian'),
                            ('Cape Verdean'),
                            ('Central African'),
                            ('Chadian'),
                            ('Chilean'),
                            ('Chinese'),
                            ('Colombian'),
                            ('Comoran'),
                            ('Congolese'),
                            ('Costa Rican'),
                            ('Croatian'),
                            ('Cuban'),
                            ('Cypriot'),
                            ('Czech'),
                            ('Danish'),
                            ('Djibouti'),
                            ('Dominican'),
                            ('Dutch'),
                            ('East Timorese'),
                            ('Ecuadorean'),
                            ('Egyptian'),
                            ('Emirian'),
                            ('Equatorial Guinean'),
                            ('Eritrean'),
                            ('Estonian'),
                            ('Ethiopian'),
                            ('Fijian'),
                            ('Filipino'),
                            ('Finnish'),
                            ('French'),
                            ('Gabonese'),
                            ('Gambian'),
                            ('Georgian'),
                            ('German'),
                            ('Ghanaian'),
                            ('Greek'),
                            ('Grenadian'),
                            ('Guatemalan'),
                            ('Guinea-Bissauan'),
                            ('Guinean'),
                            ('Guyanese'),
                            ('Haitian'),
                            ('Herzegovinian'),
                            ('Honduran'),
                            ('Hungarian'),
                            ('I-Kiribati'),
                            ('Icelander'),
                            ('Indian'),
                            ('Indonesian'),
                            ('Iranian'),
                            ('Iraqi'),
                            ('Irish'),
                            ('Israeli'),
                            ('Italian'),
                            ('Ivorian'),
                            ('Jamaican'),
                            ('Japanese'),
                            ('Jordanian'),
                            ('Kazakhstani'),
                            ('Kenyan'),
                            ('Kittian and Nevisian'),
                            ('Kuwaiti'),
                            ('Kyrgyz'),
                            ('Laotian'),
                            ('Latvian'),
                            ('Lebanese'),
                            ('Liberian'),
                            ('Libyan'),
                            ('Liechtensteiner'),
                            ('Lithuanian'),
                            ('Luxembourger'),
                            ('Macedonian'),
                            ('Malagasy'),
                            ('Malawian'),
                            ('Malaysian'),
                            ('Maldivan'),
                            ('Malian'),
                            ('Maltese'),
                            ('Marshallese'),
                            ('Mauritanian'),
                            ('Mauritian'),
                            ('Mexican'),
                            ('Micronesian'),
                            ('Moldovan'),
                            ('Monacan'),
                            ('Mongolian'),
                            ('Moroccan'),
                            ('Mosotho'),
                            ('Motswana'),
                            ('Mozambican'),
                            ('Namibian'),
                            ('Nauruan'),
                            ('Nepalese'),
                            ('New Zealander'),
                            ('Nicaraguan'),
                            ('Nigerian'),
                            ('Nigerien'),
                            ('North Korean'),
                            ('Northern Irish'),
                            ('Norwegian'),
                            ('Omani'),
                            ('Pakistani'),
                            ('Palauan'),
                            ('Panamanian'),
                            ('Papua New Guinean'),
                            ('Paraguayan'),
                            ('Peruvian'),
                            ('Polish'),
                            ('Portuguese'),
                            ('Qatari'),
                            ('Romanian'),
                            ('Russian'),
                            ('Rwandan'),
                            ('Saint Lucian'),
                            ('Salvadoran'),
                            ('Samoan'),
                            ('San Marinese'),
                            ('Sao Tomean'),
                            ('Saudi'),
                            ('Scottish'),
                            ('Senegalese'),
                            ('Serbian'),
                            ('Seychellois'),
                            ('Sierra Leonean'),
                            ('Singaporean'),
                            ('Slovakian'),
                            ('Slovenian'),
                            ('Solomon Islander'),
                            ('Somali'),
                            ('South African'),
                            ('South Korean'),
                            ('Spanish'),
                            ('Sri Lankan'),
                            ('Sudanese'),
                            ('Surinamer'),
                            ('Swazi'),
                            ('Swedish'),
                            ('Swiss'),
                            ('Syrian'),
                            ('Taiwanese'),
                            ('Tajik'),
                            ('Tanzanian'),
                            ('Thai'),
                            ('Togolese'),
                            ('Tongan'),
                            ('Trinidadian or Tobagonian'),
                            ('Tunisian'),
                            ('Turkish'),
                            ('Tuvaluan'),
                            ('Ugandan'),
                            ('Ukrainian'),
                            ('Uruguayan'),
                            ('Uzbekistani'),
                            ('Venezuelan'),
                            ('Vietnamese'),
                            ('Welsh'),
                            ('Yemenite'),
                            ('Zambian'),
                            ('Zimbabwean');";
        
        $sql[118] = "DROP TABLE hs_hr_geninfo ;";
        
        $sql[119] = "DROP TABLE hs_hr_job_title ;";
        
        $sql[120] = "DROP TABLE hs_hr_empstat ;";
        
        $sql[121] = "ALTER TABLE `hs_hr_module`
                            DROP KEY version,
                            DROP FOREIGN KEY `hs_hr_module_ibfk_1`;";
        
        $sql[122] = "DROP TABLE hs_hr_versions ;";
        
        $sql[123] = "DROP TABLE hs_hr_db_version ;";
        
        $sql[124] = "DROP TABLE hs_hr_developer ;";
        
        $sql[125] = "DROP TABLE hs_hr_membership ;";
        
        $sql[126] = "DROP TABLE hs_hr_membership_type ;";
        
        $sql[127] = "ALTER TABLE `hs_hr_file_version`
                            DROP FOREIGN KEY `hs_hr_file_version_ibfk_2`,
                            DROP FOREIGN KEY `hs_hr_file_version_ibfk_3`;";
        
        $sql[128] = "DROP TABLE hs_hr_users ;";
        
        $sql[129] = "DROP TABLE hs_hr_emp_jobtitle_history ;";
        
        $sql[130] = "DROP TABLE hs_hr_emp_subdivision_history ;";
        
        $sql[131] = "DROP TABLE hs_hr_emp_location_history ;";
        
        $sql[132] = "DROP TABLE hs_hr_compstructtree ;";
        
        $sql[133] = "DROP TABLE hs_hr_eec ;";
        
        $sql[134] = "DROP TABLE hs_hr_ethnic_race ;";
        
        $sql[135] = "DROP TABLE hs_hr_job_spec ;";
        
        $this->sql = $sql;
    }
    
    private function insertIntoOhrmOrganizationGeneralInfo() {
        $info = $this->upgradeUtility->executeSql("SELECT * FROM hs_hr_geninfo");
        $success = true;
        if($info){
            while($row = $this->upgradeUtility->fetchArray($info))
            {
                if ($row['geninfo_keys']) {
                    $keys =  explode("|", $row['geninfo_keys']);
                    $values = explode("|", $row['geninfo_values']);
                    for($count = 0; $count < count($keys); $count++) {
                        $keyValue[$keys[$count]] = $this->upgradeUtility->escapeString($values[$count]);
                    }
                    
                    if (strlen($keyValue['COUNTRY']) > 30) {
                        $keyValue['COUNTRY'] = '';
                    }
                    $valueString = "'".$keyValue['COMPANY']."', '". $keyValue['TAX']."', '". $keyValue['NAICS']."', '". $keyValue['PHONE']."', '". $keyValue['FAX']."', '". $keyValue['COUNTRY']."', '". $keyValue['STATE']."', '". $keyValue['CITY']."', '". $keyValue['ZIP']."', '". $keyValue['STREET1']."', '". $keyValue['STREET2']."', '". $keyValue['COMMENTS']."'";
                    $sql = "INSERT INTO ohrm_organization_gen_info 
                                    (name, tax_id, registration_number, phone, fax, country, province, city, zip_code, street1, street2, note) 
                                    VALUES($valueString); ";
                    
                    $success = $this->upgradeUtility->executeSql($sql);
                }
            }
        }
        return $success;
    }
    
    private function insertIntoOhrmJobTitle() {
        $jobTitles = $this->upgradeUtility->executeSql("SELECT * FROM hs_hr_job_title");
        $success = true;
        if($jobTitles) {
            $count = 1;
            while($row = $this->upgradeUtility->fetchArray($jobTitles))
            {
                $isActive = $row['is_active'] == 1 ? 0 : 1;
                $this->jobTitleMapArray[$row['jobtit_code']] = $count;
                $valueString = "'".$count."', '". $this->upgradeUtility->escapeString($this->upgradeUtility->decodeHtmlEntity($row['jobtit_name']))."', '". $this->upgradeUtility->escapeString($this->upgradeUtility->decodeHtmlEntity($row['jobtit_desc']))."', '". $this->upgradeUtility->escapeString($this->upgradeUtility->decodeHtmlEntity($row['jobtit_comm']))."', '".$isActive ."'";
                $sql = "INSERT INTO ohrm_job_title 
                            (id, job_title, job_description, note, is_deleted) 
                            VALUES($valueString); ";
                $result1 = $this->upgradeUtility->executeSql($sql);
                
                $result2 = $this->updatePimReportSelectedFields(14, $row['jobtit_code'], $count);
                if((!$result1) || (!$result2)) {
                    $success = false;
                }
                $count ++;
            }
        }
        return $success;
    }
    
    private function insertIntoOhrmEmploymentStatus() {
        $empStatus = $this->upgradeUtility->executeSql("SELECT * FROM hs_hr_empstat");
        $success = true;
        if($empStatus) {
            $count = 1;
            while($row = $this->upgradeUtility->fetchArray($empStatus))
            {
                $this->empStatusMapArray[$row['estat_code']] = $count;
                $valueString = "'".$count."', '". $this->upgradeUtility->escapeString($row['estat_name'])."'";
                $sql = "INSERT INTO ohrm_employment_status
                            (id, name) 
                            VALUES($valueString); ";
                $result1 = $this->upgradeUtility->executeSql($sql);
                
                $result2 = $this->updatePimReportSelectedFields(11, $row['estat_code'], $count);
                if((!$result1) || (!$result2)) {
                    $success = false;
                }
                $count ++;
            }
        }
        return $success;
    }
    
    private function readHsHrJobtitEmpStat() {
        $jobTitEmpStatus = $this->upgradeUtility->executeSql("SELECT * FROM hs_hr_jobtit_empstat");
        $success = true;
        if($jobTitEmpStatus) {
            $count = 1;
            while($row = $this->upgradeUtility->fetchArray($jobTitEmpStatus))
            {
                if(array_key_exists($row['estat_code'], $this->empStatusMapArray) && array_key_exists($row['jobtit_code'], $this->jobTitleMapArray)) {
                    $this->jobTitEmpStatusMapArray[$this->jobTitleMapArray[$row['jobtit_code']]] = $this->empStatusMapArray[$row['estat_code']];
                }
            }
        }
        return $success;
    }
    
    private function insertIntoHsHrJobtitEmpstat() {
        $success = true;
        if ($this->jobTitEmpStatusMapArray) {
            foreach($this->jobTitEmpStatusMapArray as $key => $value) {
                $valueString = "'".$key."', '". $value."'";
                $sql = "INSERT INTO hs_hr_jobtit_empstat
                            (jobtit_code, estat_code) 
                            VALUES($valueString); ";
                $result = $this->upgradeUtility->executeSql($sql);
                if(!$result) {
                    $success = false;
                }
            }
        }
        return $success;
    }
    
    private function insertIntoOhrmJobCategory() {
        $jobCategories = $this->upgradeUtility->executeSql("SELECT * FROM hs_hr_eec");
        $success = true;
        if($jobCategories) {
            $count = 1;
            while($row = $this->upgradeUtility->fetchArray($jobCategories))
            {
                $this->jobCategoryMapArray[$row['eec_code']] = $count;
                $valueString = "'".$count."', '". $row['eec_desc']."'";
                $sql = "INSERT INTO ohrm_job_category
                            (id, name) 
                            VALUES($valueString); ";
                $result = $this->upgradeUtility->executeSql($sql);
                if(!$result) {
                    $success = false;
                }
                $count ++;
            }
        }
        return $success;
    }
    
    private function updateEmplyeeJobDetails() {
        $employee = $this->upgradeUtility->executeSql("SELECT * FROM hs_hr_employee");
        $success = true;
        if($employee) {
            while($row = $this->upgradeUtility->fetchArray($employee))
            {
                $emp_status = (!array_key_exists($row['emp_status'], $this->empStatusMapArray)) ? NULL : $this->empStatusMapArray[$row['emp_status']];
                $job_title_code = (!array_key_exists($row['job_title_code'], $this->jobTitleMapArray))? NULL : $this->jobTitleMapArray[$row['job_title_code']];
                $job_category = (!array_key_exists($row['eeo_cat_code'], $this->jobCategoryMapArray)) ? NULL : $this->jobCategoryMapArray[$row['eeo_cat_code']];
                $emp_number = $row['emp_number'];
                if($emp_status || $job_title_code || $job_category) {
                    $sql = "UPDATE hs_hr_employee SET ";
                    $sql .= $emp_status ? "emp_status = '$emp_status' " : '';
                    if($emp_status && $job_title_code) {
                        $sql .= ", ";
                    }
                    $sql .= $job_title_code ? "job_title_code = '$job_title_code' " : '';
                    if(($emp_status || $job_title_code) && $job_category) {
                        $sql .= ", ";
                    }
                    $sql .= $job_category ? "eeo_cat_code = '$job_category' " : '';
                    $sql .= "WHERE emp_number = '$emp_number'";
                    $result = $this->upgradeUtility->executeSql($sql);
                    if(!$result) {
                        $success = false;
                    }
                }
            }
        }
        return $success;
    }
    
    private function updateHsHrLicense() {
        $licenses = $this->upgradeUtility->executeSql("SELECT * FROM hs_hr_licenses");
        $success = true;
        if($licenses) {
            $count = 1;
            while($row = $this->upgradeUtility->fetchArray($licenses))
            {
                $this->licenseMapArray[$row['licenses_code']] = $count;
                $license_code = $row['licenses_code'];
                $sql = "UPDATE hs_hr_licenses 
                        SET licenses_code = '$count' WHERE licenses_code = '$license_code'";
                
                $result = $this->upgradeUtility->executeSql($sql);
                if(!$result) {
                    $success = false;
                }
                $count ++;
            }
        }
        return $success;
    }
    
    private function updateHsHrEmpLicenses() {
        $empLicenses = $this->upgradeUtility->executeSql("SELECT licenses_code,emp_number FROM hs_hr_emp_licenses");
        $success = true;
        if($empLicenses) {
            $count = 1;
            while($row = $this->upgradeUtility->fetchArray($empLicenses))
            {
                $licenses_code = $this->licenseMapArray[$row['licenses_code']];
                $emp_number = $row['emp_number'];
                $pre_licenses_code = $row['licenses_code'];
                $sql = "UPDATE hs_hr_emp_licenses SET licenses_code = '$licenses_code'
                                    WHERE licenses_code ='$pre_licenses_code'";
                
                $result = $this->upgradeUtility->executeSql($sql);
                if(!$result) {
                    $success = false;
                }
                $count ++;
            }
        }
        return $success;
    }
    
    private function updateHsPrSalaryGrade() {
        $salary_grade = $this->upgradeUtility->executeSql("SELECT * FROM hs_pr_salary_grade");
        $success = true;
        $count = 1;
        if($salary_grade) {
            $count = 1;
            while($row = $this->upgradeUtility->fetchArray($salary_grade))
            {
                $this->salaryGradeMapArray[$row['sal_grd_code']] = $count;
                $sal_grd_code = $row['sal_grd_code'];
                $sql = "UPDATE hs_pr_salary_grade 
                        SET sal_grd_code = '$count' WHERE sal_grd_code = '$sal_grd_code'";
                
                $result1 = $this->upgradeUtility->executeSql($sql);
                
                $result2 = $this->updatePimReportSelectedFields(9, $sal_grd_code, $count);
                if((!$result1) || (!$result2)) {
                    $success = false;
                }
                $count ++;
            }
        }
        return $success;
    }
    
    private function updateHsHrEmpBasicsalary() {
        $empBasicSalaries = $this->upgradeUtility->executeSql("SELECT * FROM hs_hr_emp_basicsalary");
        $success = true;
        if($empBasicSalaries) {
            while($row = $this->upgradeUtility->fetchArray($empBasicSalaries))
            {
                $salGrdCode = $this->salaryGradeMapArray[$row['sal_grd_code']];
                $id = $row['id'];
                if($salGrdCode) {
                    $sql = "UPDATE hs_hr_emp_basicsalary SET 
                     sal_grd_code = '$salGrdCode'
                     WHERE id = '$id'";
                    
                    $result = $this->upgradeUtility->executeSql($sql);
                    if(!$result) {
                        $success = false;
                    }
                }
            }
        }
        return $success;
    }
    
    private function updateHsPrSalaryCurrencyDetail() {
        $salaryCurrancyDetails = $this->upgradeUtility->executeSql("SELECT * FROM hs_pr_salary_currency_detail");
        $success = true;
        if($salaryCurrancyDetails) {
            while($row = $this->upgradeUtility->fetchArray($salaryCurrancyDetails))
            {
                $salGrdCode = $this->salaryGradeMapArray[$row['sal_grd_code']];
                $currencyId = $row['currency_id'];
                $minSalary = $row['salcurr_dtl_minsalary'] ? $row['salcurr_dtl_minsalary'] : 0;
                $maxSalary = $row['salcurr_dtl_maxsalary'] ? $row['salcurr_dtl_maxsalary'] : 0;
                $preSallaryGrdCode = $row['sal_grd_code'];
                if($salGrdCode) {
                    $sql = "UPDATE hs_pr_salary_currency_detail SET 
                     sal_grd_code = '$salGrdCode', salcurr_dtl_minsalary = '$minSalary', salcurr_dtl_maxsalary = '$maxSalary'
                     WHERE sal_grd_code = '$preSallaryGrdCode' AND currency_id = '$currencyId'";
                    
                    $result = $this->upgradeUtility->executeSql($sql);
                    if(!$result) {
                        $success = false;
                    }
                }
            }
        }
        return $success;
    }
    
    private function updateHsHrLanguage() {
        $languages = $this->upgradeUtility->executeSql("SELECT * FROM hs_hr_language");
        $success = true;
        if($languages) {
            $count = 1;
            while($row = $this->upgradeUtility->fetchArray($languages))
            {
                $this->languageMapArray[$row['lang_code']] = $count;
                $pre_language_code = $row['lang_code'];
                $sql = "UPDATE hs_hr_language 
                        SET lang_code = '$count' WHERE lang_code = '$pre_language_code'";
                
                $result1 = $this->upgradeUtility->executeSql($sql);
                
                $result2 = $this->updatePimReportSelectedFields(15, $pre_language_code, $count);
                if((!$result1) || (!$result2)) {
                    $success = false;
                }
                $count ++;
            }
        }
        return $success;
    }
    
    private function updateHsHrEmpLanguage() {
        $employeeLanguages = $this->upgradeUtility->executeSql("SELECT * FROM hs_hr_emp_language");
        $success = true;
        if($employeeLanguages) {
            while($row = $this->upgradeUtility->fetchArray($employeeLanguages))
            {
                $langCode = (!array_key_exists($row['lang_code'], $this->languageMapArray)) ? NULL : $this->languageMapArray[$row['lang_code']];
                $preLangCode = $row['lang_code'];
                if($langCode) {
                    $sql = "UPDATE hs_hr_emp_language SET 
                     lang_code = '$langCode'
                     WHERE lang_code = '$preLangCode'";
                    
                    $result = $this->upgradeUtility->executeSql($sql);
                    if(!$result) {
                        $success = false;
                    }
                }
            }
        }
        return $success;
    }
    
    private function insertIntoOhrmMembership() {
        $memberships = $this->upgradeUtility->executeSql("SELECT * FROM hs_hr_membership");
        $success = true;
        if($memberships) {
            $count = 1;
            while($row = $this->upgradeUtility->fetchArray($memberships))
            {
                $this->membershipMapArray[$row['membship_code']] = $count;
                $valueString = "'".$count."', '". $this->upgradeUtility->escapeString($row['membship_name'])."'";
                $sql = "INSERT INTO ohrm_membership
                            (id, name) 
                            VALUES($valueString); ";
                
                $result = $this->upgradeUtility->executeSql($sql);
                if(!$result) {
                    $success = false;
                }
                $count ++;
            }
        }
        return $success;
    }
    
    private function updateHsHrEmpMemberDetail() {
        $empMemberDetails = $this->upgradeUtility->executeSql("SELECT * FROM hs_hr_emp_member_detail");
        $success = true;
        if($empMemberDetails) {
            while($row = $this->upgradeUtility->fetchArray($empMemberDetails))
            {
                $membershipCode = (!array_key_exists($row['membship_code'], $this->membershipMapArray)) ? NULL : $this->membershipMapArray[$row['membship_code']];
                $preMembershipCode = $row['membship_code'];
                if($membershipCode) {
                    $sql = "UPDATE hs_hr_emp_member_detail SET 
                     membship_code = '$membershipCode'
                     WHERE membship_code = '$preMembershipCode'";
                    
                    $result = $this->upgradeUtility->executeSql($sql);
                    if(!$result) {
                        $success = false;
                    }
                }
            }
        }
        return $success;
    }
    
    private function updateHsHrSkill() {
        $skills = $this->upgradeUtility->executeSql("SELECT * FROM hs_hr_skill");
        $success = true;
        if($skills) {
            $count = 1;
            while($row = $this->upgradeUtility->fetchArray($skills))
            {
                $this->skillMapArray[$row['skill_code']] = $count;
                $pre_skill = $row['skill_code'];
                $sql = "UPDATE hs_hr_skill 
                        SET skill_code = '$count' WHERE skill_code = '$pre_skill'";
                
                $result1 = $this->upgradeUtility->executeSql($sql);
                
                $result2 = $this->updatePimReportSelectedFields(16, $pre_skill, $count);
                if((!$result1) || (!$result2)) {
                    $success = false;
                }
                $count ++;
            }
        }
        return $success;
    }
    
    private function updateHsHrEmpSkill() {
        $empSkills = $this->upgradeUtility->executeSql("SELECT * FROM hs_hr_emp_skill");
        $success = true;
        if($empSkills) {
            while($row = $this->upgradeUtility->fetchArray($empSkills))
            {
                $skillCode = (!array_key_exists($row['skill_code'], $this->skillMapArray)) ? NULL : $this->skillMapArray[$row['skill_code']];
                $preSkillCode = $row['skill_code'];
                if($skillCode) {
                    $sql = "UPDATE hs_hr_emp_skill SET 
                     skill_code = '$skillCode'
                     WHERE skill_code = '$preSkillCode'";
                    
                    $result = $this->upgradeUtility->executeSql($sql);
                    if(!$result) {
                        $success = false;
                    }
                }
            }
        }
        return $success;
    }
    
    private function updateHsHrEducation() {
        $education = $this->upgradeUtility->executeSql("SELECT * FROM hs_hr_education");
        $success = true;
        if($education) {
            $count = 1;
            while($row = $this->upgradeUtility->fetchArray($education))
            {
                $this->educationMapArray[$row['edu_code']] = $count;
                $this->eduUniversitymapArray[$row['edu_code']] = $row['edu_uni'];
                $pre_education = $row['edu_code'];
                $sql = "UPDATE hs_hr_education 
                        SET edu_code = '$count' WHERE edu_code = '$pre_education'";
                
                $result1 = $this->upgradeUtility->executeSql($sql);
                
                $result2 = $this->updatePimReportSelectedFields(10, $pre_education, $count);
                if((!$result1) || (!$result2)) {
                    $success = false;
                }
                $count ++;
            }
        }
        return $success;
    }
    
    private function updateHsHrEmpEducation() {
        $empEducation = $this->upgradeUtility->executeSql("SELECT * FROM hs_hr_emp_education");
        $success = true;
        if($empEducation) {
            while($row = $this->upgradeUtility->fetchArray($empEducation))
            {
                $eduCode = $this->educationMapArray[$row['edu_code']];
                $institute = $this->eduUniversitymapArray[$row['edu_code']];
                $preEduCode = $row['edu_code'];
                if($eduCode) {
                    $sql = "UPDATE hs_hr_emp_education SET 
                     edu_code = '$eduCode', institute = '$institute'
                     WHERE edu_code = '$preEduCode'";
                    
                    $result = $this->upgradeUtility->executeSql($sql);
                    if(!$result) {
                        $success = false;
                    }
                }
            }
        }
        return $success;
    }
    
    private function updateHsHrLocation() {
        $locations = $this->upgradeUtility->executeSql("SELECT * FROM hs_hr_location");
        $success = true;
        if($locations) {
            $count = 1;
            while($row = $this->upgradeUtility->fetchArray($locations))
            {
                $this->locationMapArray[$row['loc_code']] = $count;
                $pre_location = $row['loc_code'];
                $sql = "UPDATE hs_hr_location 
                        SET loc_code = '$count' WHERE loc_code = '$pre_location'";
                
                $result1 = $this->upgradeUtility->executeSql($sql);
                
                $result2 = $this->updatePimReportSelectedFields(20, $pre_location, $count);
                if((!$result1) || (!$result2)) {
                    $success = false;
                }
                $count ++;
            }
        }
        return $success;
    }
    
    private function updateHsHrEmpLocations() {
        $empLocations = $this->upgradeUtility->executeSql("SELECT * FROM hs_hr_emp_locations");
        $success = true;
        if($empLocations) {
            while($row = $this->upgradeUtility->fetchArray($empLocations))
            {
                $locCode = (!array_key_exists($row['loc_code'], $this->locationMapArray)) ? NULL : $this->locationMapArray[$row['loc_code']];
                $preLocCode = $row['loc_code'];
                if($locCode) {
                    $sql = "UPDATE hs_hr_emp_locations SET 
                     loc_code = '$locCode'
                     WHERE loc_code = '$preLocCode'";
                    
                    $result = $this->upgradeUtility->executeSql($sql);
                    if(!$result) {
                        $success = false;
                    }
                }
            }
        }
        return $success;
    }
    
    private function updateHsHrNationality() {
        $nationalities = $this->upgradeUtility->executeSql("SELECT * FROM hs_hr_nationality");
        $success = true;
        if($nationalities) {
            $count = 1;
            while($row = $this->upgradeUtility->fetchArray($nationalities))
            {
                $this->nationalityMapArray[$row['nat_code']] = $count;
                $pre_natCode = $row['nat_code'];
                $sql = "UPDATE hs_hr_nationality 
                        SET nat_code = '$count' WHERE nat_code = '$pre_natCode'";
                
                $result = $this->upgradeUtility->executeSql($sql);
                if(!$result) {
                    $success = false;
                }
                $count ++;
            }
        }
        return $success;
    }
    
    private function updateHsHrEmployeeNationCode() {
        $employee = $this->upgradeUtility->executeSql("SELECT * FROM hs_hr_employee");
        $success = true;
        if($employee) {
            while($row = $this->upgradeUtility->fetchArray($employee))
            {
                $nation_code = (!array_key_exists($row['nation_code'], $this->nationalityMapArray)) ? NULL :$this->nationalityMapArray[$row['nation_code']];
                $emp_number = $row['emp_number'];
                if($nation_code) {
                    $sql = "UPDATE hs_hr_employee SET 
                        nation_code = '$nation_code'
                        WHERE emp_number = '$emp_number'";
                    
                    $result = $this->upgradeUtility->executeSql($sql);
                    if(!$result) {
                        $success = false;
                    }
                }
            }
        }
        return $success;
    }
    
    private function insertIntoOhrmUser() {
        $users = $this->upgradeUtility->executeSql("SELECT * FROM hs_hr_users");
        $success = true;
        if($users) {
            $count = 1;
            while($row = $this->upgradeUtility->fetchArray($users))
            {
                $this->userIdMapArray[$count] = $row['id'];
                $this->usersMapArray[$row['id']]['id'] = $count;
                $userEmpNumber = $row['emp_number'] == '' ? 'NULL' : $row['emp_number'];
                $userDeleted = $row['deleted'] == '' ? 'NULL' : $row['deleted'];
                $userStatus = $row['status'] == 'Enabled' ? 1 : 0;
                $userDateEntered = $row['date_entered'] == '' ? 'NULL' : "'".$row['date_entered']."'";
                $userDateModified = $row['date_modified'] == '' ? 'NULL' : "'".$row['date_modified']."'";
                $this->usersMapArray[$row['id']]['modified_user_id'] = $row['modified_user_id'];
                $this->usersMapArray[$row['id']]['created_by'] = $row['created_by'];
                $userIsAdmin = $row['is_admin'] == 'Yes' ? 1 : 2;
                $valueString = "'".$count."', '". $userIsAdmin."', ". $userEmpNumber.", '". $row['user_name']."', '".$row['user_password'] ."', "
                                    . $userDeleted.", '". $userStatus."', ". $userDateEntered.", ". $userDateModified."";
                $sql = "INSERT INTO ohrm_user 
                            (id, user_role_id, emp_number, user_name, user_password, deleted, status, date_entered, date_modified) 
                            VALUES($valueString);";
                
                $result = $this->upgradeUtility->executeSql($sql);
                if(!$result) {
                    $success = false;
                }
                $count ++;
            }
        }
        return $success;
    }
    
    private function updateOhrmUser() {
        $users = $this->upgradeUtility->executeSql("SELECT * FROM ohrm_user");
        $success = true;
        if($users) {
            $count = 1;
            while($row = $this->upgradeUtility->fetchArray($users))
            {
                $userId = $row['id'];
                $modifiedUserId = $this->usersMapArray[$this->userIdMapArray[$row['id']]]['modified_user_id'];
                if($modifiedUserId) {
                    $modifiedUserId = $this->usersMapArray[$modifiedUserId]['id'];
                }
                $modifiedUserId = $modifiedUserId == '' ? 'NULL' : $modifiedUserId;
                $createdBy = $this->usersMapArray[$this->userIdMapArray[$row['id']]]['created_by'];
                if($createdBy) {
                    $createdBy = $this->usersMapArray[$createdBy]['id'];
                }
                $createdBy = $createdBy == ''? 'NULL' : $createdBy;
                $sql = "UPDATE ohrm_user 
                                SET modified_user_id = $modifiedUserId, created_by = $createdBy WHERE id = '$userId'";
                
                $result = $this->upgradeUtility->executeSql($sql);
                if(!$result) {
                    $success = false;
                }
                $count ++;
            }
        }
        return $success;
    }
    
    private function updateHsHrMailNotifications() {
        $mailNotifications = $this->upgradeUtility->executeSql("SELECT * FROM hs_hr_mailnotifications");
        $success = true;
        if($mailNotifications) {
            while($row = $this->upgradeUtility->fetchArray($mailNotifications))
            {
                $mailNotificationUserId = $this->usersMapArray[$row['user_id']]['id'];
                $preMailNotificationUserId = $row['user_id'];
                if($mailNotificationUserId) {
                    $sql = "UPDATE hs_hr_mailnotifications SET 
                     user_id = '$mailNotificationUserId'
                     WHERE user_id = '$preMailNotificationUserId'";
                    
                    $result = $this->upgradeUtility->executeSql($sql);
                    if(!$result) {
                        $success = false;
                    }
                }
            }
        }
        return $success;
    }
    
    private function updateOhrmTimesheetActionLog() {
        $timesheetActionLog = $this->upgradeUtility->executeSql("SELECT * FROM ohrm_timesheet_action_log");
        $success = true;
        if($timesheetActionLog) {
            while($row = $this->upgradeUtility->fetchArray($timesheetActionLog))
            {
                $performedBy = $this->usersMapArray[$row['performed_by']]['id'];
                $prePerformedBy = $row['performed_by'];
                if($performedBy) {
                    $sql = "UPDATE ohrm_timesheet_action_log SET 
                     performed_by = '$performedBy'
                     WHERE performed_by = '$prePerformedBy'";
                    
                    $result = $this->upgradeUtility->executeSql($sql);
                    if(!$result) {
                        $success = false;
                    }
                }
            }
        }
        return $success;
    }
    
    private function updateOhrmJobVacancy() {
        $jobVacancies = $this->upgradeUtility->executeSql("SELECT * FROM ohrm_job_vacancy");
        $success = true;
        if($jobVacancies) {
            while($row = $this->upgradeUtility->fetchArray($jobVacancies))
            {
                $jobTitleCode = $this->jobTitleMapArray[$row['job_title_code']];
                $preJobTitleCode = $row['job_title_code'];
                if($jobTitleCode) {
                    $sql = "UPDATE ohrm_job_vacancy SET 
                     job_title_code = '$jobTitleCode'
                     WHERE job_title_code = '$preJobTitleCode'";
                    
                    $result = $this->upgradeUtility->executeSql($sql);
                    if(!$result) {
                        $success = false;
                    }
                }
            }
        }
        return $success;
    }
    
    private function updateHsHrPerformanceReview() {
        $reviews = $this->upgradeUtility->executeSql("SELECT * FROM hs_hr_performance_review");
        $success = true;
        if($reviews) {
            while($row = $this->upgradeUtility->fetchArray($reviews))
            {
                $jobTitleCode = $this->jobTitleMapArray[$row['job_title_code']];
                $preJobTitleCode = $row['job_title_code'];
                if ($jobTitleCode) {
                    $sql = "UPDATE hs_hr_performance_review SET 
                     job_title_code = '$jobTitleCode'
                     WHERE job_title_code = '$preJobTitleCode'";
                    
                    $result = $this->upgradeUtility->executeSql($sql);
                    if(!$result) {
                        $success = false;
                    }
                }
            }
        }
        return $success; 
    }
    
    private function updateHsHrEmployeeTerminationId() {
        $employee = $this->upgradeUtility->executeSql("SELECT * FROM hs_hr_employee");
        $success = true;
        if($employee) {
            $count = 1;
            while($row = $this->upgradeUtility->fetchArray($employee))
            {
                $terminationDate = $row['terminated_date'];
                $terminationReason = $row['termination_reason'] == '' ? 'NULL' : "'".$this->upgradeUtility->escapeString($row['termination_reason'])."'";
                $emp_number = $row['emp_number'];
                $emp_status = $row['emp_status'];
                $estat_name = 'Active';
                if ($emp_status) {
                    $employee_status = $this->upgradeUtility->executeSql("SELECT * FROM ohrm_employment_status WHERE id = '$emp_status'");
                    while($emp_stat = $this->upgradeUtility->fetchArray($employee_status)) {
                        $estat_name = $emp_stat['name'];
                    }
                } 
                
                if ($estat_name == 'Terminated') {
                    if (!$terminationDate) {
                        $terminationDate = date("Y-m-d");
                    }
                    $valueString = "'".$count."', '". $row['emp_number']."', 1 , '". $terminationDate."', ". $terminationReason;
                    $sql= "INSERT INTO ohrm_emp_termination 
                            (id, emp_number, reason_id, termination_date, note) 
                            VALUES($valueString); ";
                    
                    $result = $this->upgradeUtility->executeSql($sql);
                    if(!$result) {
                        $success = false;
                    }
                    
                    $sql = "UPDATE hs_hr_employee SET 
                        termination_id = '$count'
                        WHERE emp_number = '$emp_number'";
                    
                    $result = $this->upgradeUtility->executeSql($sql);
                    if(!$result) {
                        $success = false;
                    }
                    $count++;
                }
            }
        }
        return $success;
    }
    
    private function updateOhrmEmailNotification() {
        $mailNotifications = $this->upgradeUtility->executeSql("SELECT * FROM hs_hr_mailnotifications");
        $success = true;
        if($mailNotifications) {
            $mailNotificationMapArray = array(1 => 1, 2 => 3, 0 => 4, -1 => 5, 3 => 6, 8 => 7);
            while($row = $this->upgradeUtility->fetchArray($mailNotifications))
            {
                $userId = $row['user_id'];
                $notificationTypeId = $mailNotificationMapArray[$row['notification_type_id']];
                $status = $row['status'];
                $email = $row['email'];
                if($notificationTypeId && ($notificationTypeId != 6)) {
                    $sql = "UPDATE ohrm_email_notification SET 
                     is_enable = '$status'
                     WHERE id = '$notificationTypeId'";
                    
                    $result = $this->upgradeUtility->executeSql($sql);
                    if(!$result) {
                        $success = false;
                    }
                    
                    $user = $this->upgradeUtility->executeSql("SELECT * FROM ohrm_user WHERE id = '$userId'");
                    while($row = $this->upgradeUtility->fetchArray($user))
                    {
                        $userName = $row['user_name'];
                    }
                    $valueString = "'".$notificationTypeId."', '". $userName."', '". $email."'";
                    $sql = "INSERT INTO ohrm_email_subscriber 
                            (notification_id, name, email) 
                            VALUES($valueString); ";
                    
                    $result = $this->upgradeUtility->executeSql($sql);
                    if(!$result) {
                        $success = false;
                    }
                }
            }
        }
        return $success;
    }
    
    private function updateOhrmSubunit() {
        $compStructures = $this->upgradeUtility->executeSql("SELECT * FROM hs_hr_compstructtree");
        $success = true;
        if($compStructures) {
            $count = 1;
            while($row = $this->upgradeUtility->fetchArray($compStructures))
            {
                $this->parentMapArray[$row['id']] = $row['parnt'];
                if($row['parnt'] == 0) {
                    $root = $row['id'];
                }
                if (!$row['title']) {
                    $name = 'Organization';
                } else {
                    $name = $row['title'].$count;
                }
                $description = $row['description'];
                $lft = $row['lft'];
                $rgt = $row['rgt'];
                $id = $row['id'];
                $parnt = $row['parnt'];
                $valueString = "'".$id."', '". $this->upgradeUtility->escapeString($name)."', '". $this->upgradeUtility->escapeString($description)."', '". $lft."', '". $rgt."', '". $parnt."'";
                $sql = "INSERT INTO ohrm_subunit 
                        (id, name, description, lft, rgt, level) 
                        VALUES($valueString); ";
                
                $result = $this->upgradeUtility->executeSql($sql);
                if(!$result) {
                    $success = false;
                }
                
                $count++;
            }
        }
        
        if($root) {
            $levelMapArray;
            $levelMapArray[$root] = 0;
            foreach ($this->parentMapArray as $id => $parent) {
                if($id != $root) {
                    $levelMapArray[$id] = $levelMapArray[$parent] +1;
                }
            }
            foreach ($levelMapArray as $id => $level) {
                $sql= "UPDATE ohrm_subunit SET 
                     level = '$level'
                     WHERE id = '$id'";
                
                $result = $this->upgradeUtility->executeSql($sql);
                if(!$result) {
                    $success = false;
                }
            }
        }
        return $success;
    }
    
    private function updatePimReportSelectedFields($filterFieldId, $oldValue, $newValue) {
        $success = true;
        
        $sql = "UPDATE ohrm_selected_filter_field set value1 = '{$newValue}' WHERE filter_field_id = {$filterFieldId}
            AND value1 = '{$oldValue}'";
        
        $result = $this->upgradeUtility->executeSql($sql);
        if(!$result) {
            $success = false;
        }
        
        return $success;
    }
    
    public function getNotes() {
        
        $notes[] = "In the Admin module, duplicate currency records of pay grades will be removed and only one record from each currency will be kept.";
        $notes[] = "In the Company Structure, duplicate sub-unit names will be added a suffix. You can change the names by editing the Company Structure.";
        $notes[] = "A list of nationalities will be added by default. If you have already added nationalities to the system, check and remove duplicates.";
        $notes[] = "Termination date is a compulsory field in the new version. If the termination date is not set for a terminated employee, current date will be set by default. You can edit the termination date at PIM > Job.";
        $notes[] = "Make sure General Information is correct at Admin > Organization.";
        $notes[] = "Job specifications are now added as attachments. You can add specifications at Admin > Job > Job Titles.";
        $notes[] = "Benefits module has been removed in the new version since it wasn't a general module and mostly had country specific features.";
        $notes[] = "Company Property feature has been removed from new version.";
        $notes[] = "Step Increase has been removed from pay grades at Admin > Job > Pay Grades.";
        $notes[] = "Admin User Group feature has been removed from new version.";
        $notes[] = "Membership Type has been removed from Admin > Memberships.";
        $notes[] = "Ethnic Race has been removed from Admin > Nationalities.";
        
        return $notes;
    }
    
}