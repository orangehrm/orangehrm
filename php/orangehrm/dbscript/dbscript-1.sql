create table `hs_hr_config` (
	`key` varchar(100) not null default '',
	`value` varchar(512) not null default '',
	primary key (`key`)
) engine=innodb default charset=utf8;

create table `ohrm_employment_status` (
	`id` int not null auto_increment,
	`name` varchar(60) not null,
  primary key  (`id`)
) engine=innodb default charset=utf8;

create table `ohrm_job_category` (
	`id` int not null auto_increment,
	`name` varchar(60) default null,
  primary key  (`id`)
) engine=innodb default charset=utf8;

create table `hs_hr_jobtit_empstat` (
	`jobtit_code` int(7) not null,
	`estat_code` int(13) not null,
  primary key  (`jobtit_code`,`estat_code`)
) engine=innodb default charset=utf8;

create table `hs_hr_country` (
  `cou_code` char(2) not null default '',
  `name` varchar(80) not null default '',
  `cou_name` varchar(80) not null default '',
  `iso3` char(3) default null,
  `numcode` smallint(6) default null,
  primary key  (`cou_code`)
) engine=innodb default charset=utf8;

create table `hs_hr_currency_type` (
  `code` int(11) not null default '0',
  `currency_id` char(3) not null default '',
  `currency_name` varchar(70) not null default '',
  primary key  (`currency_id`)
) engine=innodb default charset=utf8;

create table `ohrm_license` (
	`id` int not null auto_increment,
	`name` varchar(100) not null,
  primary key  (`id`)
) engine=innodb default charset=utf8;

create table `hs_hr_district` (
  `district_code` varchar(13) not null default '',
  `district_name` varchar(50) default null,
  `province_code` varchar(13) default null,
  primary key  (`district_code`)
) engine=innodb default charset=utf8;

create table `hs_hr_payperiod` (
  `payperiod_code` varchar(13) not null default '',
  `payperiod_name` varchar(100) default null,
  primary key  (`payperiod_code`)
) engine=innodb default charset=utf8;

create table `hs_hr_emp_basicsalary` (
  `id` INT AUTO_INCREMENT, 
  `emp_number` int(7) not null default 0,
  `sal_grd_code` int default null,
  `currency_id` varchar(6) not null default '',
  `ebsal_basic_salary` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT null,
  `payperiod_code` varchar(13) default null,
  `salary_component` varchar(100), 
  `comments` varchar(255), 
  primary key  (`id`)
) engine=innodb default charset=utf8;

create table `hs_hr_emp_contract_extend` (
  `emp_number` int(7) not null default 0,
  `econ_extend_id` decimal(10,0) not null default '0',
  `econ_extend_start_date` datetime default null,
  `econ_extend_end_date` datetime default null,
  primary key  (`emp_number`,`econ_extend_id`)
) engine=innodb default charset=utf8;


create table `hs_hr_emp_language` (
  `emp_number` int(7) not null default 0,
  `lang_id` int not null,
  `fluency` smallint default '0',
  `competency` smallint default '0',
  `comments` varchar(100),
  primary key  (`emp_number`,`lang_id`,`fluency`)
) engine=innodb default charset=utf8;

create table `hs_hr_emp_us_tax` (
  `emp_number` int(7) not null default 0,
  `tax_federal_status` varchar(13) default null,
  `tax_federal_exceptions` int(2) default 0,
  `tax_state` varchar(13) default null,
  `tax_state_status` varchar(13) default null,
  `tax_state_exceptions` int(2) default 0,
  `tax_unemp_state` varchar(13) default null,
  `tax_work_state` varchar(13) default null,
  primary key  (`emp_number`)
) engine=innodb default charset=utf8;

create table `hs_hr_emp_attachment` (
  `emp_number` int(7) not null default 0,
  `eattach_id` int not null default '0',
  `eattach_desc` varchar(200) default null,
  `eattach_filename` varchar(100) default null,
  `eattach_size` int(11) default '0',
  `eattach_attachment` mediumblob,
  `eattach_type` varchar(200) default null,
  `screen` varchar(100) default '',
  `attached_by` int default null,
  `attached_by_name` varchar(200),
  `attached_time` timestamp default now(),
  primary key  (`emp_number`,`eattach_id`),
  key screen (`screen`)
) engine=innodb default charset=utf8;


create table `hs_hr_emp_children` (
  `emp_number` int(7) not null default 0,
  `ec_seqno` decimal(2,0) not null default '0',
  `ec_name` varchar(100) default '',
  `ec_date_of_birth` date null default null,
  primary key  (`emp_number`,`ec_seqno`)
) engine=innodb default charset=utf8;

create table `hs_hr_emp_dependents` (
  `emp_number` int(7) not null default 0,
  `ed_seqno` decimal(2,0) not null default '0',
  `ed_name` varchar(100) default '',
  `ed_relationship_type` ENUM('child', 'other'),
  `ed_relationship` varchar(100) default '',
  `ed_date_of_birth` date null default null,
  primary key  (`emp_number`,`ed_seqno`)
) engine=innodb default charset=utf8;

create table `hs_hr_emp_emergency_contacts` (
  `emp_number` int(7) not null default 0,
  `eec_seqno` decimal(2,0) not null default '0',
  `eec_name` varchar(100) default '',
  `eec_relationship` varchar(100) default '',
  `eec_home_no` varchar(100) default '',
  `eec_mobile_no` varchar(100) default '',
  `eec_office_no` varchar(100) default '',
  primary key  (`emp_number`,`eec_seqno`)
) engine=innodb default charset=utf8;


create table `hs_hr_emp_history_of_ealier_pos` (
  `emp_number` int(7) not null default 0,
  `emp_seqno` decimal(2,0) not null default '0',
  `ehoep_job_title` varchar(100) default '',
  `ehoep_years` varchar(100) default '',
  primary key  (`emp_number`,`emp_seqno`)
) engine=innodb default charset=utf8;


create table `ohrm_emp_license` (
  `emp_number` int not null,
  `license_id` int not null,
  `license_no` varchar(50) default null,
  `license_issued_date` date null default null,
  `license_expiry_date` date null default null,
  primary key (`emp_number`,`license_id`)
) engine=innodb default charset=utf8;


create table `hs_hr_emp_member_detail` (
  `emp_number` int(7) not null default 0,
  `membship_code` int(6) not null default 0,
  `ememb_subscript_ownership` varchar(20) default null,
  `ememb_subscript_amount` decimal(15,2) default null,
  `ememb_subs_currency` varchar(20) default null,
  `ememb_commence_date` date default null,
  `ememb_renewal_date` date default null,
  primary key  (`emp_number`,`membship_code`)
) engine=innodb default charset=utf8;


create table `hs_hr_emp_passport` (
  `emp_number` int(7) not null default 0,
  `ep_seqno` decimal(2,0) not null default '0',
  `ep_passport_num` varchar(100) not null default '',
  `ep_passportissueddate` datetime default null,
  `ep_passportexpiredate` datetime default null,
  `ep_comments` varchar(255) default null,
  `ep_passport_type_flg` smallint(6) default null,
  `ep_i9_status` varchar(100) default '',
  `ep_i9_review_date` date null default null,
  `cou_code` varchar(6) default null,
  primary key  (`emp_number`,`ep_seqno`)
) engine=innodb default charset=utf8;

create table `hs_hr_emp_directdebit` (
  `id` INT AUTO_INCREMENT, 
  `salary_id` INT NOT NULL, 
  `dd_routing_num` int(9) not null,
  `dd_account` varchar(100) not null default '',
  `dd_amount` decimal(11,2) not null,
  `dd_account_type` varchar(20) not null default '' comment 'CHECKING, SAVINGS',
  `dd_transaction_type` varchar(20) not null default '' comment 'BLANK, PERC, FLAT, FLATMINUS',
  primary key  (`id`)
) engine=innodb default charset=utf8;

create table `hs_hr_emp_skill` (
  `emp_number` int(7) not null default 0,
  `skill_id` int not null,
  `years_of_exp` decimal(2,0) default null,
  `comments` varchar(100) not null default ''
) engine=innodb default charset=utf8;

create table `hs_hr_emp_picture` (
  `emp_number` int(7) not null default 0,
  `epic_picture` mediumblob,
  `epic_filename` varchar(100) default null,
  `epic_type` varchar(50) default null,
  `epic_file_size` varchar(20) default null,
  `epic_file_width` varchar(20) default null,
  `epic_file_height` varchar(20) default null,
  primary key  (`emp_number`)
) engine=innodb default charset=utf8;


create table `ohrm_emp_education` (
  `id` int not null auto_increment,
  `emp_number` int not null,
  `education_id` int not null,
  `institute` varchar(100) default null,
  `major` varchar(100) default null,
  `year` decimal(4,0) default null,
  `score` varchar(25) default null,
  `start_date` date default null,
  `end_date` date default null,
  primary key  (`id`)
) engine=innodb default charset=utf8;


create table `hs_hr_emp_reportto` (
  `erep_sup_emp_number` int(7) not null default 0,
  `erep_sub_emp_number` int(7) not null default 0,
  `erep_reporting_mode` int(7) not null default 0,
  primary key  (`erep_sup_emp_number`,`erep_sub_emp_number`, `erep_reporting_mode`)
) engine=innodb default charset=utf8;

create table `ohrm_emp_reporting_method` (
  `reporting_method_id` int(7) not null auto_increment,
  `reporting_method_name` varchar(100) not null,
  primary key  (`reporting_method_id`)
) engine=innodb default charset=utf8;

create table `hs_hr_emp_work_experience` (
  `emp_number` int(7) not null default 0,
  `eexp_seqno` decimal(10,0) not null default '0',
  `eexp_employer` varchar(100) default null,
  `eexp_jobtit` varchar(120) default null,
  `eexp_from_date` datetime default null,
  `eexp_to_date` datetime default null,
  `eexp_comments` varchar(200) default null,
  `eexp_internal` int(1) default null,
  primary key  (`emp_number`,`eexp_seqno`)
) engine=innodb default charset=utf8;


create table `hs_hr_employee` (
  `emp_number` int(7) not null default 0,
  `employee_id` varchar(50) default null,
  `emp_lastname` varchar(100) default '' not null,
  `emp_firstname` varchar(100) default '' not null,
  `emp_middle_name` varchar(100) default '' not null,
  `emp_nick_name` varchar(100) default '',
  `emp_smoker` smallint(6) default '0',
  `ethnic_race_code` varchar(13) default null,
  `emp_birthday` date null default null,
  `nation_code` int(4) default null,
  `emp_gender` smallint(6) default null,
  `emp_marital_status` varchar(20) default null,
  `emp_ssn_num` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT '',
  `emp_sin_num` varchar(100) default '',
  `emp_other_id` varchar(100) default '',
  `emp_dri_lice_num` varchar(100) default '',
  `emp_dri_lice_exp_date` date null default null,
  `emp_military_service` varchar(100) default '',
  `emp_status` int(13) default null,
  `job_title_code` int(7) default null,
  `eeo_cat_code` int default null,
  `work_station` int(6) default null,
  `emp_street1` varchar(100) default '',
  `emp_street2` varchar(100) default '',
  `city_code` varchar(100) default '',
  `coun_code` varchar(100) default '',
  `provin_code` varchar(100) default '',
  `emp_zipcode` varchar(20) default null,
  `emp_hm_telephone` varchar(50) default null,
  `emp_mobile` varchar(50) default null,
  `emp_work_telephone` varchar(50) default null,
  `emp_work_email` varchar(50) default null,
  `sal_grd_code` varchar(13) default null,
  `joined_date` date null default null,
  `emp_oth_email` varchar(50) default null,
  `termination_id` int(4) default null,
  `custom1` varchar(250) default null,
  `custom2` varchar(250) default null,
  `custom3` varchar(250) default null,
  `custom4` varchar(250) default null,
  `custom5` varchar(250) default null,
  `custom6` varchar(250) default null,
  `custom7` varchar(250) default null,
  `custom8` varchar(250) default null,
  `custom9` varchar(250) default null,
  `custom10` varchar(250) default null,
  primary key  (`emp_number`)
) engine=innodb default charset=utf8;


create table `ohrm_language` (
  `id` int not null auto_increment,
  `name` varchar(120) default null,
  primary key  (`id`)
) engine=innodb default charset=utf8;


create table `ohrm_location` (
  `id` int not null auto_increment,
  `name` varchar(110) not null,
  `country_code` varchar(3) not null,
  `province` varchar(60) default null,
  `city` varchar(60) default null,
  `address` varchar(255) default null,
  `zip_code` varchar(35) default null,
  `phone` varchar(35) default null,
  `fax` varchar(35) default null,
  `notes` varchar(255) default null,
  primary key  (`id`)
) engine=innodb default charset=utf8;

CREATE  TABLE `ohrm_operational_country` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `country_code` CHAR(2) DEFAULT NULL,
  PRIMARY KEY (`id`)    
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

create table `hs_hr_module` (
  `mod_id` varchar(36) not null default '',
  `name` varchar(45) default null,
  `owner` varchar(45) default null,
  `owner_email` varchar(100) default null,
  `version` varchar(36) default null,
  `description` text,
  primary key  (`mod_id`)
) engine=innodb default charset=utf8;

create table `hs_hr_province` (
  `id` int(11) not null auto_increment,
  `province_name` varchar(40) not null default '',
  `province_code` char(2) not null default '',
  `cou_code` char(2) not null default 'us',
  primary key  (`id`)
) engine=innodb default charset=utf8;

create table `ohrm_education` (
	`id` int not null auto_increment,
	`name` varchar(100) not null,
	primary key (`id`)
) engine=innodb default charset=utf8;

create table `hs_hr_rights` (
  `userg_id` varchar(36) not null default '',
  `mod_id` varchar(36) not null default '',
  `addition` smallint(5) unsigned default '0',
  `editing` smallint(5) unsigned default '0',
  `deletion` smallint(5) unsigned default '0',
  `viewing` smallint(5) unsigned default '0',
  primary key  (`mod_id`,`userg_id`)
) engine=innodb default charset=utf8;


create table `ohrm_skill` (
  `id` int not null auto_increment,
  `name` varchar(120) default null,
  `description` text default null,
  primary key  (`id`)
) engine=innodb default charset=utf8;


create table `hs_hr_user_group` (
  `userg_id` varchar(36) not null default '',
  `userg_name` varchar(45) default null,
  `userg_repdef` smallint(5) unsigned default '0',
  primary key  (`userg_id`)
)  engine=innodb default charset=utf8;


create table `ohrm_pay_grade_currency` (
  `pay_grade_id` int not null ,
  `currency_id` varchar(6) not null default '',
  `min_salary` double default null,
  `max_salary` double default null,
  primary key  (`pay_grade_id`,`currency_id`)
) engine=innodb default charset=utf8;

create table `ohrm_pay_grade` (
  `id` int not null auto_increment,
  `name` varchar(60) default null unique,
  primary key  (`id`)
) engine=innodb default charset=utf8;


create table `hs_hr_empreport` (
  `rep_code` varchar(13) not null default '',
  `rep_name` varchar(60) unique default null,
  `rep_cridef_str` varchar(200) default null,
  `rep_flddef_str` varchar(200) default null,
  primary key  (`rep_code`)
) engine=innodb default charset=utf8;

create table `hs_hr_emprep_usergroup` (
  `userg_id` varchar(13) not null default '',
  `rep_code` varchar(13) not null default '',
  primary key  (`userg_id`,`rep_code`)
) engine=innodb default charset=utf8;

CREATE TABLE `hs_hr_leave_requests` (
  `leave_request_id` int(11) NOT NULL,
  `leave_type_id` varchar(13) NOT NULL,
  `leave_period_id` int(7) NOT NULL,
  `leave_type_name` char(50) default NULL,
  `date_applied` date NOT NULL,
  `employee_id` int(7) NOT NULL,
  `leave_comments` varchar(256) default NULL,
  PRIMARY KEY  (`leave_request_id`,`leave_type_id`,`employee_id`),
  KEY `employee_id` (`employee_id`),
  KEY `leave_type_id` (`leave_type_id`),
  KEY `leave_period_id` (`leave_period_id`),
  KEY `leave_period_id_2` (`leave_period_id`,`employee_id`,`leave_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `hs_hr_leave` (
  `leave_id` int(11) NOT NULL,
  `leave_date` date default NULL,
  `leave_length_hours` decimal(6,2) unsigned default NULL,
  `leave_length_days` decimal(4,2) unsigned default NULL,
  `leave_status` smallint(6) default NULL,
  `leave_comments` varchar(256) default NULL,
  `leave_request_id` int(11) NOT NULL,
  `leave_type_id` varchar(13) NOT NULL,
  `employee_id` int(7) NOT NULL,
  `start_time` time default NULL,
  `end_time` time default NULL,
  PRIMARY KEY  (`leave_id`,`leave_request_id`,`leave_type_id`,`employee_id`),
  KEY `leave_request_id` (`leave_request_id`,`leave_type_id`,`employee_id`),
  KEY `leave_type_id` (`leave_type_id`),
  KEY `employee_id` (`employee_id`),
  KEY `type_status` (`leave_request_id`,`leave_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table `hs_hr_leavetype` (
  `leave_type_id` varchar(13) not null,
  `leave_type_name` varchar(50) default null,
  `available_flag` smallint(6) default null,
  `operational_country_id` int unsigned default null,
  primary key  (`leave_type_id`)
) engine=innodb default charset=utf8;

create table `hs_hr_employee_leave_quota` (
  `leave_type_id` varchar(13) not null,
  `leave_period_id` int(7) NOT NULL,
  `employee_id` int(7) not null,
  `no_of_days_allotted` decimal(6,2) default null,
  `leave_taken` decimal(6,2) default '0.00',
  `leave_brought_forward` decimal(6,2) default '0.00',
  `leave_carried_forward` decimal(6,2) default '0.00',
   primary key  (`leave_type_id`,`employee_id`,`leave_period_id`),
   KEY `per_emp_type_key` (`leave_period_id`,`employee_id`,`leave_type_id`)
) engine=innodb default charset=utf8;

CREATE TABLE `ohrm_holiday` (
  `id` INT UNSIGNED AUTO_INCREMENT,
  `description` TEXT DEFAULT NULL,
  `date` DATE DEFAULT NULL,
  `recurring` TINYINT UNSIGNED DEFAULT 0,
  `length` INT UNSIGNED DEFAULT NULL,
  `operational_country_id` INT UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE  TABLE IF NOT EXISTS `ohrm_work_week` (
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
) ENGINE = InnoDB;

create table `hs_hr_mailnotifications` (
	`user_id` int(20) not null,
	`notification_type_id` int not null ,
	`status` int(2) not null,
    `email` varchar(100) default null,
	KEY `user_id` (`user_id`),
	KEY `notification_type_id` (`notification_type_id`)
) engine=innodb default charset=utf8;

create table `ohrm_customer` (
  `customer_id` int(11) not null auto_increment,
  `name` varchar(100) not null,
  `description` varchar(255) default null,
  `is_deleted` tinyint(1) default 0,
  primary key  (`customer_id`)
) engine=innodb default charset=utf8;

create table `ohrm_project` (
  `project_id` int(11) not null auto_increment,
  `customer_id` int(11) not null,
  `name` varchar(100) default null,
  `description` varchar(256) default null,
  `is_deleted` tinyint(1) default 0,
  primary key  (`project_id`,`customer_id`),
  key `customer_id` (`customer_id`)
) engine=innodb default charset=utf8;

create table `ohrm_project_activity` (
  `activity_id` int(11) not null auto_increment,
  `project_id` int(11) not null,
  `name` varchar(110) default null,
  `is_deleted` tinyint(1) default 0,
  primary key  (`activity_id`),
  key `project_id` (`project_id`)
) engine=innodb default charset=utf8;

create table `ohrm_project_admin` (
  `project_id` int(11) not null,
  `emp_number` int(11) not null,
  primary key  (`project_id`,`emp_number`),
  key `emp_number` (`emp_number`)
) engine=innodb default charset=utf8;

create table `hs_hr_unique_id` (
  `id` int not null auto_increment,
  `last_id` int unsigned not null,
  `table_name` varchar(50) not null,
  `field_name` varchar(50) not null,
  primary key(`id`),
  unique key `table_field` (`table_name`, `field_name`)
) engine=innodb default charset=utf8;

create table `ohrm_work_shift` (
  `id` int(11) not null auto_increment,
  `name` varchar(250) not null,
  `hours_per_day` decimal(4,2) not null,
  primary key  (`id`)
) engine=innodb default charset=utf8;

create table `ohrm_employee_work_shift` (
  `work_shift_id` int(11) not null auto_increment,
  `emp_number` int(11) not null,
  primary key  (`work_shift_id`,`emp_number`),
  key `emp_number` (`emp_number`)
) engine=innodb default charset=utf8;

create table `hs_hr_custom_fields` (
  `field_num` int(11) not null,
  `name` varchar(250) not null,
  `type` int(11) not null,
  `screen` varchar(100),
  `extra_data` varchar(250) default null,
  primary key  (`field_num`),
  key `emp_number` (`field_num`),
  key screen (`screen`)
) engine=innodb default charset=utf8;

create table `hs_hr_pay_period` (
	`id` int not null ,
	`start_date` date not null ,
	`end_date` date not null ,
	`close_date` date not null ,
	`check_date` date not null ,
	`timesheet_aproval_due_date` date not null ,
	primary key (`id`)
) engine=innodb default charset=utf8;

create table `hs_hr_custom_export` (
  `export_id` int(11) not null,
  `name` varchar(250) not null,
  `fields` text default null,
  `headings` text default null,
  primary key  (`export_id`),
  key `emp_number` (`export_id`)
) engine=innodb default charset=utf8;

create table `hs_hr_custom_import` (
  `import_id` int(11) not null,
  `name` varchar(250) not null,
  `fields` text default null,
  `has_heading` tinyint(1) default 0,
  primary key  (`import_id`),
  key `emp_number` (`import_id`)
) engine=innodb default charset=utf8;

create table `hs_hr_hsp` (
	`id` int not null ,
	`employee_id` int not null ,
	`benefit_year` date default null ,
	`hsp_value` decimal(10,2) not null ,
	`total_acrued` decimal(10,2) not null ,
	`accrued_last_updated` date default null ,
	`amount_per_day` decimal(10,2) not null ,
	`edited_status` tinyint default 0 ,
	`termination_date` date default null ,
	`halted` tinyint default 0 ,
	`halted_date` date default null ,
	`terminated` tinyint default 0 ,
	primary key (`id`),
	key `employee_id` (`employee_id`)
) engine=innodb default charset=utf8;

create table `hs_hr_hsp_payment_request` (
	`id` int not null ,
	`hsp_id` int not null ,
	`employee_id` int not null ,
	`date_incurred` date not null ,
	`provider_name` varchar(100) default null ,
	`person_incurring_expense` varchar(100) default null ,
	`expense_description` varchar(250) default null ,
	`expense_amount` decimal(10,2) not null ,
	`payment_made_to` varchar(100) default null ,
	`third_party_account_number` varchar(50) default null ,
	`mail_address` varchar(250) default null ,
	`comments` varchar(250) default null ,
	`date_paid` date default null ,
	`check_number` varchar(50) default null ,
	`status` tinyint default 0 ,
	`hr_notes` varchar(250) default null ,
	primary key (`id`),
	key `employee_id` (`employee_id`),
	key `hsp_id` (`hsp_id`)
) engine=innodb default charset=utf8;

create table `hs_hr_hsp_summary` (
  `summary_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `hsp_plan_id` tinyint(2) NOT NULL,
  `hsp_plan_year` int(6) NOT NULL,
  `hsp_plan_status` tinyint(2) NOT NULL default '0',
  `annual_limit` decimal(10,2) NOT NULL default '0.00',
  `employer_amount` decimal(10,2) NOT NULL default '0.00',
  `employee_amount` decimal(10,2) NOT NULL default '0.00',
  `total_accrued` decimal(10,2) NOT NULL default '0.00',
  `total_used` decimal(10,2) NOT NULL default '0.00',
  primary key (`summary_id`)
) engine=innodb default charset=utf8;

create table `hs_hr_emp_locations` (
  `emp_number` int not null,
  `location_id` int not null,
  primary key  (`emp_number`, `location_id`)
) engine=innodb default charset=utf8;

CREATE TABLE `hs_hr_leave_period` (
  `leave_period_id` int(11) NOT NULL,
  `leave_period_start_date` date NOT NULL,
  `leave_period_end_date` date NOT NULL,
  PRIMARY KEY (`leave_period_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


create table `hs_hr_kpi` (
  `id` int(13) not null,
  `job_title_code` varchar(13) default null,
  `description` varchar(200) default null,
  `rate_min` double default null,
  `rate_max` double default null,
  `rate_default` tinyint(4) default null,
  `is_active` tinyint(4) default null,
  primary key (`id`)
) engine=innodb default charset=utf8;

create table `hs_hr_performance_review` (
  `id` int(13) not null,
  `employee_id` int(13) not null,
  `reviewer_id` int(13) not null,
  `creator_id` varchar(36) default null,
  `job_title_code` varchar(10) not null,
  `sub_division_id` int(13) default null,  
  `creation_date` date not null,
  `period_from` date not null,
  `period_to` date not null,
  `due_date` date not null,
  `state` tinyint(2) default null,
  `kpis` text default null,
  primary key (`id`)
) engine=innodb default charset=utf8;

create table `hs_hr_performance_review_comments`(
	`id` int(13) not null auto_increment,
	`pr_id` int(13) not null,
	`employee_id` int(13) default null,
	`comment` text default null,
	`create_date` date not null,
	primary key (`id`)
)engine=innodb default charset=utf8;

create table `ohrm_timesheet`(
  `timesheet_id` bigint(20) not null,
  `state` varchar(255) not null,
  `start_date` date not null,
  `end_date` date not null,
  `employee_id` bigint(20) not null,
  primary key  (`timesheet_id`)
) engine=innodb default charset=utf8;

create table `ohrm_timesheet_item`(
  `timesheet_item_id` bigint(20) not null,
  `timesheet_id` bigint(20) not null,
  `date` date not null,
  `duration` bigint(20) default null,
  `comment` text default null,
  `project_id` bigint(20) not null,
  `employee_id` bigint(20) not null,
  `activity_id` bigint(20) not null,
  primary key  (`timesheet_item_id`),
  key `timesheet_id` (`timesheet_id`),
  key `activity_id` (`activity_id`)
) engine=innodb default charset=utf8;

create table `ohrm_timesheet_action_log`(
  `timesheet_action_log_id` bigint(20) not null,
  `comment` varchar(255) default null,
  `action` varchar(255),
  `date_time` date not null,
  `performed_by` int(20) not null,
  `timesheet_id` bigint(20) not null,
  primary key  (`timesheet_action_log_id`),
  key `timesheet_id` (`timesheet_id`),
  key `performed_by`(`performed_by`)
) engine=innodb default charset=utf8;

create table `ohrm_workflow_state_machine`(
  `id` bigint(20) not null,
  `workflow` varchar(255) not null,
  `state` varchar(255) not null,
  `role` varchar(255) not null,
  `action` varchar(255) not null,
  `resulting_state` varchar(255) not null,
  primary key (`id`)
) engine=innodb default charset=utf8;

create table `ohrm_attendance_record`(
  `id` bigint(20) not null,
  `employee_id` bigint(20) not null,
  `punch_in_utc_time` datetime ,
  `punch_in_note` varchar(255),
  `punch_in_time_offset` varchar(255),
  `punch_in_user_time` datetime,
  `punch_out_utc_time` datetime,
  `punch_out_note` varchar(255),
  `punch_out_time_offset` varchar(255),
  `punch_out_user_time` datetime,
  `state` varchar(255) not null,
  primary key (`id`),
  KEY `emp_id_state` (`employee_id`,`state`),
  KEY `emp_id_time` (`employee_id`,`punch_in_utc_time`,`punch_out_utc_time`)
) engine=innodb default charset=utf8;

create table `ohrm_report_group` (
  `report_group_id` bigint(20) not null,
  `name` varchar(255) not null,
  `core_sql` mediumtext not null,
  primary key (`report_group_id`)
) engine=innodb default charset=utf8;

create table `ohrm_report` (
  `report_id` bigint(20) not null auto_increment,
  `name` varchar(255) not null,
  `report_group_id` bigint(20) not null,
  `use_filter_field` boolean not null,
  `type` varchar(255) default null,
  primary key (`report_id`),
  key `report_group_id` (`report_group_id`)
) engine=innodb default charset=utf8;

create table `ohrm_filter_field` (
  `filter_field_id` bigint(20) not null,
  `report_group_id` bigint(20) not null,
  `name` varchar(255) not null,
  `where_clause_part` mediumtext not null,
  `filter_field_widget` varchar(255),
  `condition_no` int(20) not null,
  `required` varchar(10) default null,
  primary key (`filter_field_id`),
  key `report_group_id` (`report_group_id`)
) engine=innodb default charset=utf8;

create table `ohrm_selected_filter_field` (
  `report_id` bigint(20) not null,
  `filter_field_id` bigint(20) not null,
  `filter_field_order` bigint(20) not null,
  `value1` varchar(255) default null,
  `value2` varchar(255) default null,
  `where_condition` varchar(255) default null,
  `type` varchar(255) not null,
  primary key (`report_id`,`filter_field_id`),
  key `report_id` (`report_id`),
  key `filter_field_id` (`filter_field_id`)
) engine=innodb default charset=utf8;

create table `ohrm_display_field` (
  `display_field_id` bigint(20) not null auto_increment,
  `report_group_id` bigint(20) not null,
  `name` varchar(255) not null,
  `label` varchar(255) not null,
  `field_alias` varchar(255),
  `is_sortable` varchar(10) not null,
  `sort_order` varchar(255),
  `sort_field` varchar(255),
  `element_type` varchar(255) not null,
  `element_property` varchar(1000) not null,
  `width` varchar(255) not null,
  `is_exportable` varchar(10),
  `text_alignment_style` varchar(20),
  `is_value_list` boolean not null default false,
  `display_field_group_id` int unsigned,
  `default_value` varchar(255) default null,
  `is_encrypted` boolean not null default false,
  `is_meta` boolean not null default false,
  primary key (`display_field_id`),
  key `report_group_id` (`report_group_id`)
) engine=innodb default charset=utf8;

create table `ohrm_composite_display_field` (
  `composite_display_field_id` bigint(20) not null auto_increment,
  `report_group_id` bigint(20) not null,
  `name` varchar(1000) not null,
  `label` varchar(255) not null,
  `field_alias` varchar(255),
  `is_sortable` varchar(10) not null,
  `sort_order` varchar(255),
  `sort_field` varchar(255),
  `element_type` varchar(255) not null,
  `element_property` varchar(1000) not null,
  `width` varchar(255) not null,
  `is_exportable` varchar(10),
  `text_alignment_style` varchar(20),
  `is_value_list` boolean not null default false,
  `display_field_group_id` int unsigned,
  `default_value` varchar(255) default null,
  `is_encrypted` boolean not null default false,
  `is_meta` boolean not null default false,
  primary key (`composite_display_field_id`),
  key `report_group_id` (`report_group_id`)
) engine=innodb default charset=utf8;

create table `ohrm_group_field` (
  `group_field_id` bigint(20) not null,
  `name` varchar(255) not null,
  `group_by_clause` mediumtext not null,
  `group_field_widget` varchar(255),
  primary key (`group_field_id`)
) engine=innodb default charset=utf8;

create table `ohrm_available_group_field` (
  `report_group_id` bigint(20) not null,
  `group_field_id` bigint(20) not null,
  primary key (`report_group_id`,`group_field_id`),
  key `report_group_id` (`report_group_id`),
  key `group_field_id` (`group_field_id`)
) engine=innodb default charset=utf8;

create table `ohrm_selected_display_field` (
  `id` bigint(20) not null auto_increment,
  `display_field_id` bigint(20) not null,
  `report_id` bigint(20) not null,
  primary key (`id`,`display_field_id`,`report_id`),
  key `display_field_id` (`display_field_id`),
  key `report_id` (`report_id`)
) engine=innodb default charset=utf8;

create table `ohrm_selected_composite_display_field` (
  `id` bigint(20) not null,
  `composite_display_field_id` bigint(20) not null,
  `report_id` bigint(20) not null,
  primary key (`id`,`composite_display_field_id`,`report_id`),
  key `composite_display_field_id` (`composite_display_field_id`),
  key `report_id` (`report_id`)
) engine=innodb default charset=utf8;

create table `ohrm_summary_display_field` (
  `summary_display_field_id` bigint(20) not null,
  `function` varchar(1000) not null,
  `label` varchar(255) not null,
  `field_alias` varchar(255),
  `is_sortable` varchar(10) not null,
  `sort_order` varchar(255),
  `sort_field` varchar(255),
  `element_type` varchar(255) not null,
  `element_property` varchar(1000) not null,
  `width` varchar(255) not null,
  `is_exportable` varchar(10),
  `text_alignment_style` varchar(20),
  `is_value_list` boolean not null default false,
  `display_field_group_id` int unsigned,
  `default_value` varchar(255) default null,
  primary key (`summary_display_field_id`)
) engine=innodb default charset=utf8;

create table `ohrm_selected_group_field` (
  `group_field_id` bigint(20) not null,
  `summary_display_field_id` bigint(20) not null,
  `report_id` bigint(20) not null,
  primary key (`group_field_id`,`summary_display_field_id`,`report_id`),
  key `group_field_id` (`group_field_id`),
  key `summary_display_field_id` (`summary_display_field_id`),
  key `report_id` (`report_id`)
) engine=innodb default charset=utf8;

create table `ohrm_display_field_group` (
  `id` int unsigned not null auto_increment,
  `report_group_id` bigint not null,
  `name` varchar(255) not null,
  `is_list` boolean not null default false,
  primary key (`id`)
) engine=innodb default charset=utf8;

create table `ohrm_selected_display_field_group` (
  `id` int unsigned not null auto_increment,
  `report_id` bigint not null,
  `display_field_group_id` int unsigned not null,
  primary key (`id`)
) engine=innodb default charset=utf8;

create table `ohrm_job_vacancy`(
	`id` int(13) not null,
	`job_title_code` int(4) not null,
        `hiring_manager_id` int(13) default null,
	`name` varchar(100) not null,
	`description` text default null,
	`no_of_positions` int(13) default null,
    `status` int(4) not null,
    `published_in_feed` boolean not null default false,
    `defined_time` datetime not null,
    `updated_time` datetime not null,
	primary key (`id`)
)engine=innodb default charset=utf8;

create table `ohrm_job_candidate`(
	`id` int(13) not null,
	`first_name` varchar(30) not null,
	`middle_name` varchar(30) default null,
    `last_name` varchar(30) not null,
	`email` varchar(100) not null,
	`contact_number` varchar(30) default null,
	`status` int(4) not null,
	`comment` text default null,
	`mode_of_application` int(4) not null,
	`date_of_application` date not null,
    `cv_file_id` int(13) default null,
    `cv_text_version` text default null,
    `keywords` varchar(255) default null,
    `added_person` int(13) default null,
	primary key (`id`)
)engine=innodb default charset=utf8;

create table `ohrm_job_candidate_vacancy`(
        `id` int(13) default null unique,
	`candidate_id` int(13) not null,
        `vacancy_id` int(13) not null,
	`status` varchar(100) not null,
        `applied_date` date not null,
	primary key (`candidate_id`, `vacancy_id`)
)engine=innodb default charset=utf8;

create table `ohrm_job_candidate_attachment`(
	`id` int(13) not null auto_increment,
	`candidate_id` int(13) not null,
	`file_name` varchar(200) not null,
        `file_type` varchar(200) default null,
	`file_size` int(11) not null,
	`file_content` mediumblob,
        `attachment_type` int(4) default null,
	primary key (`id`)
)engine=innodb default charset=utf8;

create table `ohrm_job_vacancy_attachment`(
	`id` int(13) not null auto_increment,
	`vacancy_id` int(13) not null,
	`file_name` varchar(200) not null,
        `file_type` varchar(200) default null,
	`file_size` int(11) not null,
	`file_content` mediumblob,
        `attachment_type` int(4) default null,
	`comment` varchar(255) default null,
	primary key (`id`)
)engine=innodb default charset=utf8;

create table `ohrm_job_interview_attachment`(
	`id` int(13) not null auto_increment,
	`interview_id` int(13) not null,
	`file_name` varchar(200) not null,
        `file_type` varchar(200) default null,
	`file_size` int(11) not null,
	`file_content` mediumblob,
        `attachment_type` int(4) default null,
	`comment` varchar(255) default null,
	primary key (`id`)
)engine=innodb default charset=utf8;

create table `ohrm_job_candidate_history`(
	`id` int(13) not null auto_increment,
	`candidate_id` int(13) not null,
	`vacancy_id` int(13) default null,
	`candidate_vacancy_name` varchar(255) default null,
	`interview_id` int(13) default null,
	`action` int(4) not null,
	`performed_by` int(13) default null,
        `performed_date` datetime not null,
	`note` text default null,
	`interviewers` varchar(255) default null,
	primary key (`id`)
)engine=innodb default charset=utf8;

create table `ohrm_job_interview`(
	`id` int(13) not null auto_increment,
	`candidate_vacancy_id` int(13) default null,
        `candidate_id` int(13) default null,
        `interview_name` varchar(100) not null,
	`interview_date` date default null,
        `interview_time` time default null,
	`note` text default null,
	primary key (`id`)
)engine=innodb default charset=utf8;

create table `ohrm_job_interview_interviewer`(
	`interview_id` int(13) not null,
	`interviewer_id` int(13) not null,
	primary key (`interview_id`, `interviewer_id`)
)engine=innodb default charset=utf8;

create table `ohrm_subunit` (
  `id` int(6) not null auto_increment,
  `name` varchar(100) not null unique,
  `unit_id` varchar(100) default null,
  `description` varchar(400),
  `lft` smallint(6) unsigned default null,
  `rgt` smallint(6) unsigned default null,
  `level` smallint(6) unsigned default null,
  primary key (`id`)
) engine=innodb default charset=utf8;

create table `ohrm_organization_gen_info` (
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
) engine=innodb default charset=utf8;

create table `ohrm_job_title` (
  `id` int(13) not null auto_increment,
  `job_title` varchar(100) not null,
  `job_description` varchar(400) default null,
  `note` varchar(400) default null,
  `is_deleted` tinyint(1) default 0,
  primary key (`id`)
) engine=innodb default charset=utf8;

create table `ohrm_job_specification_attachment`(
	`id` int(13) not null auto_increment,
	`job_title_id` int(13) not null,
	`file_name` varchar(200) not null,
        `file_type` varchar(200) default null,
	`file_size` int(11) not null,
	`file_content` mediumblob,
	primary key (`id`)
)engine=innodb default charset=utf8;

create table `ohrm_emp_termination`(
	`id` int(4) not null auto_increment,
	`emp_number` int(4) default null,
        `reason_id` int(4) default null,
	`termination_date` date not null,
        `note` varchar(255) default null,
	primary key (`id`)
)engine=innodb default charset=utf8;

create table `ohrm_emp_termination_reason`(
	`id` int(4) not null auto_increment,
    `name` varchar(100) default null,
	primary key (`id`)
)engine=innodb default charset=utf8;

create table `ohrm_user`(
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
)engine=innodb default charset=utf8;

create table `ohrm_user_role`(
	`id` int(10) not null auto_increment,
	`name` varchar(255) not null,
	`display_name` varchar(255) not null,
	`is_assignable` tinyint(1) default 0,
        `is_predefined` tinyint(1) default 0,
        unique key user_role_name (`name`),
	primary key (`id`)
)engine=innodb default charset=utf8;

create table `ohrm_user_selection_rule`(
	`id` int(10) not null auto_increment,
	`name` varchar(255) not null,
        `description` varchar(255) ,
	`implementation_class` varchar(255) not null,
        `rule_xml_data` text,
	primary key (`id`)
)engine=innodb default charset=utf8;

create table `ohrm_role_user_selection_rule`(
	`user_role_id` int(10) not null,
        `selection_rule_id` int(10) not null,
        `configurable_params` text,
	primary key (`user_role_id`,`selection_rule_id`)
)engine=innodb default charset=utf8;

create table `ohrm_membership` (
  `id` int(6) not null auto_increment,
  `name` varchar(100) not null,
  primary key  (`id`)
) engine=innodb default charset=utf8;

create table `ohrm_nationality` (
  `id` int(6) not null auto_increment,
  `name` varchar(100) not null,
  primary key  (`id`)
) engine=innodb default charset=utf8;

create table `ohrm_email_notification` (
  `id` int(6) not null auto_increment,
  `name` varchar(100) not null,
  `is_enable` int(6) not null,
  primary key  (`id`)
) engine=innodb default charset=utf8;

create table `ohrm_email_subscriber` (
  `id` int(6) not null auto_increment,
  `notification_id` int(6) not null,
  `name` varchar(100) not null,
  `email` varchar(100) not null,
  primary key  (`id`)
) engine=innodb default charset=utf8;

create table `ohrm_module` (
  `id` int not null auto_increment,
  `name` varchar(120) default null,
  `status` tinyint default 1,
  primary key  (`id`)
) engine=innodb default charset=utf8;

create table ohrm_screen (
  `id` int not null auto_increment, 
   `name` varchar(100) not null, 
   `module_id` int not null, 
   `action_url` varchar(255) not null, 
   primary key (`id`)
) engine=innodb default charset=utf8;

create table ohrm_user_role_screen (
  id int not null auto_increment,
  user_role_id int not null, 
  screen_id int not null, 
  can_read tinyint(1) not null default '0', 
  can_create tinyint(1) not null default '0',
  can_update tinyint(1) not null default '0', 
  can_delete tinyint(1) not null default '0',
  primary key (`id`)
) engine=innodb default charset=utf8;

create table `ohrm_upgrade_history` (
  `id` int(10) not null auto_increment,
  `start_version` varchar(30) DEFAULT NULL,
  `end_version` varchar(30) DEFAULT NULL,
  `start_increment` int(11) NOT NULL,
  `end_increment` int(11) NOT NULL,
  `upgraded_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


create table `ohrm_email_configuration` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE ohrm_data_group (
    `id` int AUTO_INCREMENT, 
    `name` VARCHAR(255), description VARCHAR(255), 
    `can_read` TINYINT, can_create TINYINT, 
    `can_update` TINYINT, 
    `can_delete` TINYINT, 
    PRIMARY KEY(`id`)
) ENGINE = INNODB DEFAULT CHARSET=utf8;

CREATE TABLE ohrm_user_role_data_group (
    id int AUTO_INCREMENT, 
    user_role_id int, 
    data_group_id int, 
    can_read TINYINT, 
    can_create TINYINT, 
    can_update TINYINT, 
    can_delete TINYINT, 
    self TINYINT, 
    PRIMARY KEY(id)
) ENGINE = INNODB DEFAULT CHARSET=utf8;

alter table ohrm_user_role_data_group 
       add constraint foreign key (user_role_id)
                             references ohrm_user_role(id) on delete cascade;

alter table ohrm_user_role_data_group 
       add constraint foreign key (data_group_id)
                             references ohrm_data_group(id) on delete cascade;

alter table ohrm_email_subscriber
       add constraint foreign key (notification_id)
                             references ohrm_email_notification(id) on delete cascade;

alter table ohrm_emp_termination
       add constraint foreign key (reason_id)
                             references ohrm_emp_termination_reason(id) on delete set null;

alter table ohrm_emp_termination
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table ohrm_job_specification_attachment
       add constraint foreign key (job_title_id)
                             references ohrm_job_title(id) on delete cascade;

alter table ohrm_available_group_field
       add constraint foreign key (group_field_id)
                             references ohrm_group_field(group_field_id);

alter table ohrm_filter_field
       add constraint foreign key (report_group_id)
                             references ohrm_report_group(report_group_id) on delete cascade;

alter table ohrm_display_field
       add constraint foreign key (report_group_id)
                             references ohrm_report_group(report_group_id) on delete cascade;

alter table ohrm_display_field
       add constraint foreign key (display_field_group_id)
                             references ohrm_display_field_group(id) on delete set null;

alter table ohrm_composite_display_field
       add constraint foreign key (report_group_id)
                             references ohrm_report_group(report_group_id) on delete cascade;

alter table ohrm_composite_display_field
       add constraint foreign key (display_field_group_id)
                             references ohrm_display_field_group(id) on delete set null;

alter table ohrm_summary_display_field
       add constraint foreign key (display_field_group_id)
                             references ohrm_display_field_group(id) on delete set null;

alter table ohrm_selected_group_field
       add constraint foreign key (report_id)
                             references ohrm_report(report_id) on delete cascade;

alter table ohrm_selected_group_field
       add constraint foreign key (group_field_id)
                             references ohrm_group_field(group_field_id) on delete cascade;

alter table ohrm_selected_group_field
       add constraint foreign key (summary_display_field_id)
                             references ohrm_summary_display_field(summary_display_field_id);

alter table ohrm_selected_filter_field
       add constraint foreign key (report_id)
                             references ohrm_report(report_id) on delete cascade;

alter table ohrm_selected_filter_field
       add constraint foreign key (filter_field_id)
                             references ohrm_filter_field(filter_field_id) on delete cascade;

alter table ohrm_selected_display_field
       add constraint foreign key (report_id)
                             references ohrm_report(report_id) on delete cascade;

alter table ohrm_selected_display_field
       add constraint foreign key (display_field_id)
                             references ohrm_display_field(display_field_id) on delete cascade;

alter table ohrm_selected_composite_display_field
       add constraint foreign key (report_id)
                             references ohrm_report(report_id) on delete cascade;

alter table ohrm_selected_composite_display_field
       add constraint foreign key (composite_display_field_id)
                             references ohrm_composite_display_field(composite_display_field_id) on delete cascade;

alter table ohrm_report
       add constraint foreign key (report_group_id)
                             references ohrm_report_group(report_group_id) on delete cascade;

alter table ohrm_display_field_group
       add constraint foreign key (report_group_id)
                             references ohrm_report_group(report_group_id) on delete cascade;

alter table ohrm_selected_display_field_group
       add constraint foreign key (report_id)
                             references ohrm_report(report_id) on delete cascade;

alter table ohrm_selected_display_field_group
       add constraint foreign key (display_field_group_id)
                             references ohrm_display_field_group(id) on delete cascade;

alter table ohrm_timesheet_action_log
       add constraint foreign key (performed_by)
                             references ohrm_user(id) on delete cascade;

alter table ohrm_job_interview
       add constraint foreign key (candidate_vacancy_id)
                             references ohrm_job_candidate_vacancy(id) on delete set null;

alter table ohrm_job_interview
       add constraint foreign key (candidate_id)
                             references ohrm_job_candidate(id) on delete cascade;

alter table ohrm_job_interview_interviewer
       add constraint foreign key (interview_id)
                             references ohrm_job_interview(id) on delete cascade;

alter table ohrm_job_interview_interviewer
       add constraint foreign key (interviewer_id)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table ohrm_job_candidate_attachment
       add constraint foreign key (candidate_id)
                             references ohrm_job_candidate(id) on delete cascade;

alter table ohrm_job_vacancy_attachment
       add constraint foreign key (vacancy_id)
                             references ohrm_job_vacancy(id) on delete cascade;

alter table ohrm_job_interview_attachment
       add constraint foreign key (interview_id)
                             references ohrm_job_interview(id) on delete cascade;

alter table ohrm_job_candidate_history
       add constraint foreign key (candidate_id)
                             references ohrm_job_candidate(id) on delete cascade;

alter table ohrm_job_candidate_history
       add constraint foreign key (vacancy_id)
                             references ohrm_job_vacancy(id) on delete set null;

alter table ohrm_job_candidate_history
       add constraint foreign key (interview_id)
                             references ohrm_job_interview(id) on delete set null;

alter table ohrm_job_candidate_history
       add constraint foreign key (performed_by)
                             references hs_hr_employee(emp_number) on delete set null;

alter table ohrm_job_vacancy
       add constraint foreign key (job_title_code)
                             references ohrm_job_title(id) on delete cascade;

alter table ohrm_job_vacancy
       add constraint foreign key (hiring_manager_id)
                             references hs_hr_employee(emp_number) on delete set null;

alter table ohrm_job_candidate
       add constraint foreign key (added_person)
                             references hs_hr_employee(emp_number) on delete set null;

alter table ohrm_job_candidate_vacancy
       add constraint foreign key (candidate_id)
                             references ohrm_job_candidate(id) on delete cascade;

alter table ohrm_job_candidate_vacancy
       add constraint foreign key (vacancy_id)
                             references ohrm_job_vacancy(id) on delete cascade;

alter table ohrm_pay_grade_currency
       add constraint foreign key (currency_id)
                             references hs_hr_currency_type(currency_id) on delete cascade;

alter table ohrm_pay_grade_currency
       add constraint foreign key (pay_grade_id)
                             references ohrm_pay_grade(id) on delete cascade;

alter table ohrm_location
       add constraint foreign key (country_code)
                             references hs_hr_country(cou_code) on delete cascade;

alter table hs_hr_jobtit_empstat
       add constraint foreign key (jobtit_code)
                             references ohrm_job_title(id) on delete cascade;

alter table hs_hr_jobtit_empstat
       add constraint foreign key (estat_code)
                             references ohrm_employment_status(id) on delete cascade;

alter table hs_hr_employee
       add constraint foreign key (work_station)
                             references ohrm_subunit(id) on delete set null;

alter table hs_hr_employee
       add constraint foreign key (nation_code)
                             references ohrm_nationality(id) on delete set null;

alter table hs_hr_employee
       add constraint foreign key (job_title_code)
                             references ohrm_job_title(id) on delete set null;

alter table hs_hr_employee
       add constraint foreign key (emp_status)
                             references ohrm_employment_status(id) on delete set null;

alter table hs_hr_employee
       add constraint foreign key (eeo_cat_code)
                             references ohrm_job_category(id) on delete set null;

alter table hs_hr_employee
       add constraint foreign key (termination_id)
                             references ohrm_emp_termination(id) on delete set null;

alter table hs_hr_emp_children
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_dependents
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_emergency_contacts
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_history_of_ealier_pos
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table ohrm_emp_license
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table ohrm_emp_license
       add constraint foreign key (license_id)
                             references ohrm_license(id) on delete cascade;

alter table hs_hr_emp_skill
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_skill
       add constraint foreign key (skill_id)
                             references ohrm_skill(id) on delete cascade;

alter table hs_hr_emp_attachment
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_picture
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table ohrm_emp_education
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table ohrm_emp_education
       add constraint foreign key (education_id)
                             references ohrm_education(id) on delete cascade;

alter table hs_hr_emp_work_experience
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_passport
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_directdebit
       add constraint foreign key (salary_id)
                             references hs_hr_emp_basicsalary(id) on delete cascade;

alter table hs_hr_emp_member_detail
       add constraint foreign key (membship_code)
                             references ohrm_membership(id) on delete cascade;

alter table hs_hr_emp_member_detail
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_reportto
       add constraint foreign key (erep_sup_emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_reportto
       add constraint foreign key (erep_sub_emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_reportto
       add constraint foreign key (erep_reporting_mode)
                             references ohrm_emp_reporting_method(reporting_method_id) on delete cascade;

alter table hs_hr_emp_basicsalary
       add constraint foreign key (sal_grd_code)
                             references ohrm_pay_grade(id) on delete cascade;

alter table hs_hr_emp_basicsalary
       add constraint foreign key (currency_id)
                             references hs_hr_currency_type(currency_id) on delete cascade;

alter table hs_hr_emp_basicsalary
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_basicsalary
       add constraint foreign key (payperiod_code)
                             references hs_hr_payperiod(payperiod_code) on delete cascade;

alter table hs_hr_emp_language
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_language
       add constraint foreign key (lang_id)
                             references ohrm_language(id) on delete cascade;

alter table hs_hr_emp_us_tax
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_contract_extend
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_rights
       add constraint foreign key (mod_id)
       						references hs_hr_module (mod_id) on delete cascade;

alter table hs_hr_rights
       add constraint foreign key (userg_id)
       						references hs_hr_user_group (userg_id) on delete cascade;

alter table hs_hr_emprep_usergroup
       add constraint foreign key (userg_id)
       						references hs_hr_user_group (userg_id) on delete cascade;

alter table hs_hr_emprep_usergroup
       add constraint foreign key (rep_code)
       						references hs_hr_empreport (rep_code) on delete cascade;

alter table hs_hr_employee_leave_quota
       add constraint foreign key (leave_type_id)
       						references hs_hr_leavetype (leave_type_id) on delete cascade;

alter table hs_hr_employee_leave_quota
       add constraint foreign key (employee_id)
       						references hs_hr_employee (emp_number) on delete cascade;
alter table hs_hr_employee_leave_quota
       add constraint foreign key (leave_period_id)
       						references hs_hr_leave_period (leave_period_id) on delete cascade;
       						
alter table hs_hr_leave_requests
       add constraint foreign key (employee_id)
       						references hs_hr_employee (emp_number) on delete cascade;
alter table hs_hr_leave_requests
       add constraint foreign key (leave_period_id)
       						references hs_hr_leave_period (leave_period_id) on delete cascade;

alter table hs_hr_leave_requests
       add constraint foreign key (leave_type_id)
       						references hs_hr_leavetype (leave_type_id) on delete cascade;

alter table hs_hr_leave
		add foreign key (leave_request_id,leave_type_id,employee_id)
							references hs_hr_leave_requests
									(leave_request_id,leave_type_id,employee_id) on delete cascade;

alter table hs_hr_leavetype
    add foreign key (operational_country_id)
        references ohrm_operational_country(id) on delete set null;

alter table hs_hr_mailnotifications
       add constraint foreign key (user_id)
       						references ohrm_user(id) on delete cascade;

alter table `ohrm_project_activity`
  add constraint foreign key (`project_id`) references `ohrm_project` (`project_id`) on delete cascade;

alter table `ohrm_project_admin`
  add constraint foreign key (`project_id`) references `ohrm_project` (`project_id`) on delete cascade,
  add constraint foreign key (`emp_number`) references `hs_hr_employee` (`emp_number`) on delete cascade;

alter table `ohrm_employee_work_shift`
  add constraint foreign key (`work_shift_id`) references `ohrm_work_shift` (`id`) on delete cascade,
  add constraint foreign key (`emp_number`) references `hs_hr_employee` (`emp_number`) on delete cascade;

alter table `hs_hr_hsp`
  add constraint foreign key (`employee_id`) references `hs_hr_employee` (`emp_number`) on delete cascade;

alter table `hs_hr_hsp_payment_request`
  add constraint foreign key (`employee_id`) references `hs_hr_employee` (`emp_number`) on delete cascade;

alter table `hs_hr_emp_locations`
    add constraint foreign key (`location_id`)
        references ohrm_location(`id`) on delete cascade,
    add constraint foreign key (`emp_number`)
        references hs_hr_employee(`emp_number`) on delete cascade;

alter table `ohrm_user`
    add constraint foreign key (`emp_number`)
        references hs_hr_employee(`emp_number`) on delete cascade;

alter table `ohrm_user`
    add constraint foreign key (`user_role_id`)
        references ohrm_user_role(`id`) on delete restrict;

ALTER TABLE `ohrm_operational_country`
ADD CONSTRAINT `fk_ohrm_operational_country_hs_hr_country`
    FOREIGN KEY (`country_code`)
    REFERENCES `hs_hr_country` (`cou_code`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;

ALTER TABLE `ohrm_work_week`
ADD CONSTRAINT `fk_ohrm_work_week_ohrm_operational_country`
    FOREIGN KEY (`operational_country_id`)
    REFERENCES `ohrm_operational_country` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;

ALTER TABLE `ohrm_holiday`
ADD CONSTRAINT `fk_ohrm_holiday_ohrm_operational_country`
    FOREIGN KEY (`operational_country_id`)
    REFERENCES `ohrm_operational_country` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;

alter table ohrm_screen
       add constraint foreign key (module_id)
                             references ohrm_module(id) on delete cascade;
alter table ohrm_user_role_screen
       add constraint foreign key (user_role_id)
                             references ohrm_user_role(id) on delete cascade;
alter table ohrm_user_role_screen
       add constraint foreign key (screen_id)
                             references ohrm_screen(id) on delete cascade;
