SHOW INNODB STATUS;

create table `hs_hr_geninfo` (
	`code` varchar(13) not null default '',
	`geninfo_keys` varchar(200) default null,
	`geninfo_values` varchar(800) default null,
	primary key (`code`)
) engine=innodb default charset=utf8;

create table `hs_hr_compstructtree` (
  `title` tinytext not null,
  `description` text not null,
  `loc_code` varchar(13) default NULL,
  `lft` int(4) not null default '0',
  `rgt` int(4) not null default '0',
  `id` int(6) not null,
  `parnt` int(6) not null default '0',
  `dept_id` varchar(32) null,
  primary key  (`id`),
  key loc_code (`loc_code`)
) engine=innodb default charset=utf8;

create table `hs_hr_job_spec` (
	`jobspec_id` int(11) not null default 0,
	`jobspec_name` varchar(50) default null,
	`jobspec_desc` text default null,
	`jobspec_duties` text default null,
	primary key(`jobspec_id`)
) engine=innodb default charset=utf8;

create table `hs_hr_job_title` (
	`jobtit_code` varchar(13) not null default '',
	`jobtit_name` varchar(50) default null,
	`jobtit_desc` varchar(200) default null,
	`jobtit_comm` varchar(400) default null,
	`sal_grd_code` varchar(13) default null,
	`jobspec_id` int(11) default null,
	primary key(`jobtit_code`),
    key sal_grd_code (`sal_grd_code`),
    key jobspec_id (`jobspec_id`)
) engine=innodb default charset=utf8;

create table `hs_hr_empstat` (
	`estat_code` varchar(13) not null default '',
	`estat_name` varchar(50) default null,
  primary key  (`estat_code`)
) engine=innodb default charset=utf8;

create table `hs_hr_eec` (
	`eec_code` varchar(13) not null default '',
	`eec_desc` varchar(50) default null,
  primary key  (`eec_code`)
) engine=innodb default charset=utf8;

create table `hs_hr_jobtit_empstat` (
	`jobtit_code` varchar(13) not null default '',
	`estat_code` varchar(13) not null default '',
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

create table `hs_hr_licenses` (
	`licenses_code` varchar(13) not null default '',
	`licenses_desc` varchar(50) default null,
  primary key  (`licenses_code`)
) engine=innodb default charset=utf8;

create table `hs_hr_db_version` (
  `id` varchar(36) not null default '',
  `name` varchar(45) default null,
  `description` varchar(100) default null,
  `entered_date` datetime default '0000-00-00 00:00:00',
  `modified_date` datetime default '0000-00-00 00:00:00',
  `entered_by` varchar(36) default null,
  `modified_by` varchar(36) default null,
  primary key  (`id`)
) engine=innodb default charset=utf8;


create table `hs_hr_developer` (
  `id` varchar(36) not null default '',
  `first_name` varchar(45) default null,
  `last_name` varchar(45) default null,
  `reports_to_id` varchar(45) default null,
  `description` varchar(200) default null,
  `department` varchar(45) default null,
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
  `emp_number` int(7) not null default 0,
  `sal_grd_code` varchar(13) not null default '',
  `currency_id` varchar(6) not null default '',
  `ebsal_basic_salary` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT null,
  `payperiod_code` varchar(13) default null,
  primary key  (`emp_number`,`sal_grd_code`,`currency_id`)
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
  `lang_code` varchar(13) not null default '',
  `elang_type` smallint(6) default '0',
  `competency` smallint default '0',
  `comments` varchar(100),
  primary key  (`emp_number`,`lang_code`,`elang_type`)
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
  `eattach_id` decimal(10,0) not null default '0',
  `eattach_desc` varchar(200) default null,
  `eattach_filename` varchar(100) default null,
  `eattach_size` int(11) default '0',
  `eattach_attachment` mediumblob,
  `eattach_type` varchar(50) default null,
  primary key  (`emp_number`,`eattach_id`)
) engine=innodb default charset=utf8;


create table `hs_hr_emp_children` (
  `emp_number` int(7) not null default 0,
  `ec_seqno` decimal(2,0) not null default '0',
  `ec_name` varchar(100) default '',
  `ec_date_of_birth` date default '0000-00-00',
  primary key  (`emp_number`,`ec_seqno`)
) engine=innodb default charset=utf8;

create table `hs_hr_emp_dependents` (
  `emp_number` int(7) not null default 0,
  `ed_seqno` decimal(2,0) not null default '0',
  `ed_name` varchar(100) default '',
  `ed_relationship` varchar(100) default '',
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


create table `hs_hr_emp_licenses` (
  `emp_number` int(7) not null default 0,
  `licenses_code` varchar(100) not null default '',
  `license_no` varchar(50) default null,
  `licenses_date` date not null default '0000-00-00',
  `licenses_renewal_date` date not null default '0000-00-00',
  primary key  (`emp_number`,`licenses_code`)
) engine=innodb default charset=utf8;

create table `hs_hr_emp_member_detail` (
  `emp_number` int(7) not null default 0,
  `membship_code` varchar(13) not null default '',
  `membtype_code` varchar(13) not null default '',
  `ememb_subscript_ownership` varchar(20) default null,
  `ememb_subscript_amount` decimal(15,2) default null,
  `ememb_commence_date` datetime default null,
  `ememb_renewal_date` datetime default null,
  primary key  (`emp_number`,`membship_code`,`membtype_code`)
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
  `ep_i9_review_date` date default '0000-00-00',
  `cou_code` varchar(6) default null,
  primary key  (`emp_number`,`ep_seqno`)
) engine=innodb default charset=utf8;

create table `hs_hr_emp_directdebit` (
  `emp_number` int(7) not null default 0,
  `dd_seqno` decimal(2,0) not null default '0',
  `dd_routing_num` int(9) not null,
  `dd_account` varchar(100) not null default '',
  `dd_amount` decimal(11,2) not null,
  `dd_account_type` varchar(20) not null default '' comment 'CHECKING, SAVINGS',
  `dd_transaction_type` varchar(20) not null default '' comment 'BLANK, PERC, FLAT, FLATMINUS',
  primary key  (`emp_number`,`dd_seqno`)
) engine=innodb default charset=utf8;

create table `hs_hr_emp_skill` (
  `emp_number` int(7) not null default 0,
  `skill_code` varchar(13) not null default '',
  `years_of_exp` decimal(2,0) not null default '0',
  `comments` varchar(100) not null default ''
) engine=innodb default charset=utf8;

create table `hs_hr_emp_picture` (
  `emp_number` int(7) not null default 0,
  `epic_picture` mediumblob,
  `epic_filename` varchar(100) default null,
  `epic_type` varchar(50) default null,
  `epic_file_size` varchar(20) default null,
  primary key  (`emp_number`)
) engine=innodb default charset=utf8;


create table `hs_hr_emp_education` (
  `emp_number` int(7) not null default 0,
  `edu_code` varchar(13) not null default '',
  `edu_major` varchar(100) default null,
  `edu_year` decimal(4,0) default null,
  `edu_gpa` varchar(25) default null,
  `edu_start_date` datetime default null,
  `edu_end_date` datetime default null,
  primary key  (`edu_code`,`emp_number`)
) engine=innodb default charset=utf8;


create table `hs_hr_emp_reportto` (
  `erep_sup_emp_number` int(7) not null default 0,
  `erep_sub_emp_number` int(7) not null default 0,
  `erep_reporting_mode` smallint(6) not null default '0',
  primary key  (`erep_sup_emp_number`,`erep_sub_emp_number`,`erep_reporting_mode`)
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
  `emp_birthday` date default '0000-00-00',
  `nation_code` varchar(13) default null,
  `emp_gender` smallint(6) default null,
  `emp_marital_status` varchar(20) default null,
  `emp_ssn_num` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT '',
  `emp_sin_num` varchar(100) default '',
  `emp_other_id` varchar(100) default '',
  `emp_dri_lice_num` varchar(100) default '',
  `emp_dri_lice_exp_date` date default '0000-00-00',
  `emp_military_service` varchar(100) default '',
  `emp_status` varchar(13) default null,
  `job_title_code` varchar(13) default null,
  `eeo_cat_code` varchar(13) default null,
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
  `joined_date` date default '0000-00-00',
  `emp_oth_email` varchar(50) default null,
  `terminated_date` DATE null,
  `termination_reason` varchar(256) default null,
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


create table `hs_hr_file_version` (
  `id` varchar(36) not null default '',
  `altered_module` varchar(36) default null,
  `description` varchar(200) default null,
  `entered_date` datetime not null default '0000-00-00 00:00:00',
  `modified_date` datetime default '0000-00-00 00:00:00',
  `entered_by` varchar(36) default null,
  `modified_by` varchar(36) default null,
  `name` varchar(50) default null,
  primary key  (`id`)
) engine=innodb default charset=utf8;


create table `hs_hr_language` (
  `lang_code` varchar(13) not null default '',
  `lang_name` varchar(120) default null,
  primary key  (`lang_code`)
) engine=innodb default charset=utf8;


create table `hs_hr_location` (
  `loc_code` varchar(13) not null default '',
  `loc_name` varchar(100) default null,
  `loc_country` varchar(3) default null,
  `loc_state` varchar(50) default null,
  `loc_city` varchar(50) default null,
  `loc_add` varchar(100) default null,
  `loc_zip` varchar(10) default null,
  `loc_phone` varchar(30) default null,
  `loc_fax` varchar(30) default null,
  `loc_comments` varchar(100) default null,
  primary key  (`loc_code`)
) engine=innodb default charset=utf8;

create table `hs_hr_membership` (
  `membship_code` varchar(13) not null default '',
  `membtype_code` varchar(13) default null,
  `membship_name` varchar(120) default null,
  primary key  (`membship_code`)
) engine=innodb default charset=utf8;


create table `hs_hr_membership_type` (
  `membtype_code` varchar(13) not null default '',
  `membtype_name` varchar(120) default null,
  primary key  (`membtype_code`)
) engine=innodb default charset=utf8;


create table `hs_hr_module` (
  `mod_id` varchar(36) not null default '',
  `name` varchar(45) default null,
  `owner` varchar(45) default null,
  `owner_email` varchar(100) default null,
  `version` varchar(36) default null,
  `description` text,
  primary key  (`mod_id`)
) engine=innodb default charset=utf8;


create table `hs_hr_nationality` (
  `nat_code` varchar(13) not null default '',
  `nat_name` varchar(120) default null,
  primary key  (`nat_code`)
) engine=innodb default charset=utf8;


create table `hs_hr_province` (
  `id` int(11) not null auto_increment,
  `province_name` varchar(40) not null default '',
  `province_code` char(2) not null default '',
  `cou_code` char(2) not null default 'us',
  primary key  (`id`)
) engine=innodb default charset=utf8;

create table `hs_hr_education` (
	`edu_code` varchar(13) not null default '',
	`edu_uni` varchar(100) default null,
	`edu_deg` varchar(100) default null,
	primary key (`edu_code`)
) engine=innodb default charset=utf8;


create table `hs_hr_ethnic_race` (
  `ethnic_race_code` varchar(13) not null default '',
  `ethnic_race_desc` varchar(50) default null,
  primary key  (`ethnic_race_code`)
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


create table `hs_hr_skill` (
  `skill_code` varchar(13) not null default '',
  `skill_name` varchar(120) default null,
  `skill_description` text default null,
  primary key  (`skill_code`)
) engine=innodb default charset=utf8;


create table `hs_hr_user_group` (
  `userg_id` varchar(36) not null default '',
  `userg_name` varchar(45) default null,
  `userg_repdef` smallint(5) unsigned default '0',
  primary key  (`userg_id`)
)  engine=innodb default charset=utf8;


create table `hs_hr_users` (
  `id` varchar(36) not null default '',
  `user_name` varchar(20) default '',
  `user_password` varchar(32) default null,
  `first_name` varchar(45) default null,
  `last_name` varchar(45) default null,
  `emp_number` int(7) default null,
  `user_hash` varchar(32) default null,
  `is_admin` char(3) default null,
  `receive_notification` char(1) default null,
  `description` text,
  `date_entered` datetime default '0000-00-00 00:00:00',
  `date_modified` datetime default '0000-00-00 00:00:00',
  `modified_user_id` varchar(36) default null,
  `created_by` varchar(36) default null,
  `title` varchar(50) default null,
  `department` varchar(50) default null,
  `phone_home` varchar(45) default null,
  `phone_mobile` varchar(45) default null,
  `phone_work` varchar(45) default null,
  `phone_other` varchar(45) default null,
  `phone_fax` varchar(45) default null,
  `email1` varchar(100) default null,
  `email2` varchar(100) default null,
  `status` varchar(25) default null,
  `address_street` varchar(150) default null,
  `address_city` varchar(150) default null,
  `address_state` varchar(100) default null,
  `address_country` varchar(25) default null,
  `address_postalcode` varchar(10) default null,
  `user_preferences` text,
  `deleted` tinyint(1) not null default '0',
  `employee_status` varchar(25) default null,
  `userg_id` varchar(36) default null,
  primary key  (`id`),
  unique key `user_name` type btree (`user_name`)
) engine=innodb default charset=utf8;


create table `hs_hr_versions` (
  `id` varchar(36) not null default '',
  `name` varchar(45) default null,
  `entered_date` datetime default '0000-00-00 00:00:00',
  `modified_date` datetime default '0000-00-00 00:00:00',
  `modified_by` varchar(36) default null,
  `created_by` varchar(36) default null,
  `deleted` tinyint(4) not null default '0',
  `db_version` varchar(36) default null,
  `file_version` varchar(36) default null,
  `description` text,
  primary key  (`id`)
) engine=innodb default charset=utf8;


create table `hs_pr_salary_currency_detail` (
  `sal_grd_code` varchar(13) not null default '',
  `currency_id` varchar(6) not null default '',
  `salcurr_dtl_minsalary` double default null,
  `salcurr_dtl_stepsalary` double default null,
  `salcurr_dtl_maxsalary` double default null,
  primary key  (`sal_grd_code`,`currency_id`)
) engine=innodb default charset=utf8;

create table `hs_pr_salary_grade` (
  `sal_grd_code` varchar(13) not null default '',
  `sal_grd_name` varchar(60) default null unique,
  primary key  (`sal_grd_code`)
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
  `leave_type_name` char(20) default NULL,
  `date_applied` date NOT NULL,
  `employee_id` int(7) NOT NULL,
  PRIMARY KEY  (`leave_request_id`,`leave_type_id`,`employee_id`),
  KEY `employee_id` (`employee_id`),
  KEY `leave_type_id` (`leave_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `hs_hr_leave` (
  `leave_id` int(11) NOT NULL,
  `leave_date` date default NULL,
  `leave_length_hours` decimal(6,2) unsigned default NULL,
  `leave_length_days` decimal(4,2) unsigned default NULL,
  `leave_status` smallint(6) default NULL,
  `leave_comments` varchar(80) default NULL,
  `leave_request_id` int(11) NOT NULL,
  `leave_type_id` varchar(13) NOT NULL,
  `employee_id` int(7) NOT NULL,
  `start_time` time default NULL,
  `end_time` time default NULL,
  PRIMARY KEY  (`leave_id`,`leave_request_id`,`leave_type_id`,`employee_id`),
  KEY `leave_request_id` (`leave_request_id`,`leave_type_id`,`employee_id`),
  KEY `leave_type_id` (`leave_type_id`),
  KEY `employee_id` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table `hs_hr_leavetype` (
  `leave_type_id` varchar(13) not null,
  `leave_type_name` varchar(20) default null,
  `available_flag` smallint(6) default null,
  primary key  (`leave_type_id`)
) engine=innodb default charset=utf8;

create table `hs_hr_employee_leave_quota` (
  `year` year(4) NOT NULL,
  `leave_type_id` varchar(13) not null,
  `employee_id` int(7) not null,
  `no_of_days_allotted` decimal(6,2) default null,
  `leave_taken` decimal(6,2) default '0.00',
  `leave_brought_forward` decimal(6,2) default '0.00',
  primary key  (`leave_type_id`,`employee_id`, `year`)
) engine=innodb default charset=utf8;

create table `hs_hr_holidays` (
  `holiday_id` int(11) not null,
  `description` text default null,
  `date` date default '0000-00-00',
  `recurring` tinyint(1) default '0',
  `length` int(2) default null,
  unique key `holiday_id` (`holiday_id`)
) engine=innodb default charset=utf8;

create table `hs_hr_weekends` (
  `day` int(2) not null,
  `length` int(2) not null,
  unique key `day` (`day`)
) engine=innodb default charset=utf8;

create table `hs_hr_mailnotifications` (
	`user_id` varchar(36) not null,
	`notification_type_id` int not null ,
	`status` int(2) not null,
	KEY `user_id` (`user_id`),
	KEY `notification_type_id` (`notification_type_id`)
) engine=innodb default charset=utf8;

create table `hs_hr_customer` (
  `customer_id` int(11) not null,
  `name` varchar(100) default null,
  `description` varchar(250) default null,
  `deleted` tinyint(1) default 0,
  primary key  (`customer_id`)
) engine=innodb default charset=utf8;


create table `hs_hr_employee_timesheet_period` (
  `timesheet_period_id` int(11) not null,
  `employee_id` int(11) not null,
  primary key  (`timesheet_period_id`,`employee_id`),
  key `employee_id` (`employee_id`)
) engine=innodb default charset=utf8;


create table `hs_hr_project` (
  `project_id` int(11) not null,
  `customer_id` int(11) not null,
  `name` varchar(100) default null,
  `description` varchar(250) default null,
  `deleted` tinyint(1) default 0,
  primary key  (`project_id`,`customer_id`),
  key `customer_id` (`customer_id`)
) engine=innodb default charset=utf8;

create table `hs_hr_project_activity` (
  `activity_id` int(11) not null,
  `project_id` int(11) not null,
  `name` varchar(100) default null,
  `deleted` tinyint(1) default 0,
  primary key  (`activity_id`),
  key `project_id` (`project_id`)
) engine=innodb default charset=utf8;

create table `hs_hr_project_admin` (
  `project_id` int(11) not null,
  `emp_number` int(11) not null,
  primary key  (`project_id`,`emp_number`),
  key `emp_number` (`emp_number`)
) engine=innodb default charset=utf8;

create table `hs_hr_timesheet` (
  `timesheet_id` int(11) not null,
  `employee_id` int(11) not null,
  `timesheet_period_id` int(11) not null,
  `start_date` datetime default null,
  `end_date` datetime default null,
  `status` int(11) default null,
  `comment` varchar(250) default null,
  primary key  (`timesheet_id`,`employee_id`,`timesheet_period_id`),
  key `employee_id` (`employee_id`),
  key `timesheet_period_id` (`timesheet_period_id`)
) engine=innodb default charset=utf8;


create table `hs_hr_timesheet_submission_period` (
  `timesheet_period_id` int(11) not null,
  `name` varchar(100) default null,
  `frequency` int(11) not null,
  `period` int(11) default '1',
  `start_day` int(11) default null,
  `end_day` int(11) default null,
  `description` varchar(250) default null,
  primary key  (`timesheet_period_id`)
) engine=innodb default charset=utf8;


create table `hs_hr_time_event` (
  `time_event_id` int(11) not null,
  `project_id` int(11) not null,
  `activity_id` int(11) default null,
  `employee_id` int(11) not null,
  `timesheet_id` int(11) not null,
  `start_time` datetime default null,
  `end_time` datetime default null,
  `reported_date` datetime default null,
  `duration` int(11) default null,
  `description` varchar(250) default null,
  primary key  (`time_event_id`,`project_id`,`employee_id`,`timesheet_id`),
  key `project_id` (`project_id`),
  key `activity_id` (`activity_id`),
  key `employee_id` (`employee_id`),
  key `timesheet_id` (`timesheet_id`)
) engine=innodb default charset=utf8;

create table `hs_hr_unique_id` (
  `id` int not null auto_increment,
  `last_id` int unsigned not null,
  `table_name` varchar(50) not null,
  `field_name` varchar(50) not null,
  primary key(`id`),
  unique key `table_field` (`table_name`, `field_name`)
) engine=innodb default charset=utf8;

create table `hs_hr_workshift` (
  `workshift_id` int(11) not null,
  `name` varchar(250) not null,
  `hours_per_day` decimal(4,2) not null,
  primary key  (`workshift_id`)
) engine=innodb default charset=utf8;

create table `hs_hr_employee_workshift` (
  `workshift_id` int(11) not null,
  `emp_number` int(11) not null,
  primary key  (`workshift_id`,`emp_number`),
  key `emp_number` (`emp_number`)
) engine=innodb default charset=utf8;

create table `hs_hr_custom_fields` (
  `field_num` int(11) not null,
  `name` varchar(250) not null,
  `type` int(11) not null,
  `extra_data` varchar(250) default null,
  primary key  (`field_num`),
  key `emp_number` (`field_num`)
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

create table `hs_hr_job_vacancy` (
  `vacancy_id` int(11) not null,
  `jobtit_code` varchar(13) default null,
  `manager_id` int(7) default null,
  `active` tinyint(1) not null default 0,
  `description` text,
  primary key  (`vacancy_id`),
  key `jobtit_code` (`jobtit_code`),
  key `manager_id` (`manager_id`),
) engine=innodb default charset=utf8;

create table `hs_hr_job_application` (
  `application_id` int(11) not null,
  `vacancy_id` int(11) not null,
  `lastname` varchar(100) default '' not null,
  `firstname` varchar(100) default '' not null,
  `middlename` varchar(100) default '' not null,
  `street1` varchar(100) default '',
  `street2` varchar(100) default '',
  `city` varchar(100) default '',
  `country_code` varchar(100) default '',
  `province` varchar(100) default '',
  `zip` varchar(20) default null,
  `phone` varchar(50) default null,
  `mobile` varchar(50) default null,
  `email` varchar(50) default null,
  `qualifications` text,
  `status` smallint(2) default 0,
  `applied_datetime` datetime default null,
  `emp_number` int(7) default null,
  primary key  (`application_id`),
  key `vacancy_id` (`vacancy_id`)
) engine=innodb default charset=utf8;

create table `hs_hr_job_application_events` (
  `id` int(11) not null,
  `application_id` int(11) not null,
  `created_time` datetime default null,
  `created_by` varchar(36) default null,
  `owner` int(7) default null,
  `event_time` datetime default null,
  `event_type` smallint(2) default null,
  `status` smallint(2) default 0,
  `notes` text,
  primary key  (`id`),
  key `application_id` (`application_id`),
  key `created_by` (`created_by`),
  key `owner` (`owner`)
) engine=innodb default charset=utf8;

create table `hs_hr_emp_jobtitle_history` (
  `id` int(11) not null auto_increment,
  `emp_number` int(7) not null,
  `code` varchar(15) not null,
  `name` varchar(250) default null,
  `start_date` datetime default null,
  `end_date` datetime default null,
  primary key  (`id`),
  key  `emp_number` (`emp_number`)
) engine=innodb default charset=utf8;

create table `hs_hr_emp_subdivision_history` (
  `id` int(11) not null auto_increment,
  `emp_number` int(7) not null,
  `code` varchar(15) not null,
  `name` varchar(250) default null,
  `start_date` datetime default null,
  `end_date` datetime default null,
  primary key  (`id`),
  key  `emp_number` (`emp_number`)
) engine=innodb default charset=utf8;

create table `hs_hr_emp_location_history` (
  `id` int(11) not null auto_increment,
  `emp_number` int(7) not null,
  `code` varchar(15) not null,
  `name` varchar(250) default null,
  `start_date` datetime default null,
  `end_date` datetime default null,
  primary key  (`id`),
  key  `emp_number` (`emp_number`)
) engine=innodb default charset=utf8;

create table `hs_hr_comp_property` (
  `prop_id` int(11) not null auto_increment,
  `prop_name` varchar(250) not null,
  `emp_id` int(7) not null,
  primary key  (`prop_id`),
  key  `emp_id` (`emp_id`)
) engine=innodb default charset=utf8;

create table `hs_hr_emp_locations` (
  `emp_number` int(7) not null,
  `loc_code` varchar(13) not null,
  primary key  (`emp_number`, `loc_code`)
) engine=innodb default charset=utf8;

INSERT INTO `hs_hr_customer`
  (`customer_id`, `name`, `description`)
  VALUES (0, 'Internal', "Internal time tracker")
  ON DUPLICATE KEY UPDATE `name`= 'Internal';

INSERT INTO `hs_hr_project`
  (`project_id`, `customer_id`, `name`, `description`)
  VALUES (0, 0, 'Internal', "Used to track special time events")
  ON DUPLICATE KEY UPDATE `name`= 'Internal';

INSERT INTO `hs_hr_project_activity`
  (`activity_id`, `project_id`, `name`)
  VALUES (0, 0, 'Work time')
  ON DUPLICATE KEY UPDATE `name`= 'Work time';

