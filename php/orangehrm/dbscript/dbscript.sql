SHOW INNODB STATUS;

create table `hs_hr_geninfo` (
	`code` varchar(13) not null default '',
	`geninfo_keys` varchar(200) default null,
	`geninfo_values` varchar(800) default null,
	primary key (`code`)
) engine=innodb default charset=utf8;

create table `hs_hr_config` (
	`key` varchar(100) not null default '',
	`value` varchar(100) not null default '',
	primary key (`key`)
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
  `id` INT AUTO_INCREMENT, 
  `emp_number` int(7) not null default 0,
  `sal_grd_code` varchar(13) not null default '',
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
  `ec_date_of_birth` date default '0000-00-00',
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
  `user_name` varchar(40) default '',
  `user_password` varchar(40) default null,
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
  `leave_comments` varchar(256) default NULL,
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
  `activity_id` int(11) not null,
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

create table `hs_hr_pay_period` (
	`id` int not null ,
	`start_date` date not null ,
	`end_date` date not null ,
	`close_date` date not null ,
	`check_date` date not null ,
	`timesheet_aproval_due_date` date not null ,
	primary key (`id`)
) engine=innodb default charset=utf8;

create table `hs_hr_job_vacancy` (
  `vacancy_id` int(11) not null,
  `jobtit_code` varchar(13) default null,
  `manager_id` int(7) default null,
  `active` tinyint(1) not null default 0,
  `description` text,
  primary key  (`vacancy_id`),
  key `jobtit_code` (`jobtit_code`),
  key `manager_id` (`manager_id`)
) engine=innodb default charset=utf8;

create table `hs_hr_custom_fields` (
  `field_num` int(11) not null,
  `name` varchar(250) not null,
  `type` int(11) not null,
  `screen` varchar(100) default '',
  `extra_data` varchar(250) default null,
  primary key  (`field_num`),
  key `emp_number` (`field_num`),
  key screen (`screen`)
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
  `resume_name` varchar(100) default null,
  `resume_data` mediumblob,
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

alter table hs_hr_compstructtree
       add constraint foreign key (loc_code)
                             references hs_hr_location(loc_code) on delete restrict;

alter table hs_pr_salary_currency_detail
       add constraint foreign key (currency_id)
                             references hs_hr_currency_type(currency_id) on delete cascade;

alter table hs_pr_salary_currency_detail
       add constraint foreign key (sal_grd_code)
                             references hs_pr_salary_grade(sal_grd_code) on delete cascade;

alter table hs_hr_location
       add constraint foreign key (loc_country)
                             references hs_hr_country(cou_code) on delete cascade;

alter table hs_hr_job_title
       add constraint foreign key (sal_grd_code)
                             references hs_pr_salary_grade(sal_grd_code) on delete set null;

alter table hs_hr_job_title
       add constraint foreign key (jobspec_id)
                             references hs_hr_job_spec(jobspec_id) on delete set null;

alter table hs_hr_jobtit_empstat
       add constraint foreign key (jobtit_code)
                             references hs_hr_job_title(jobtit_code) on delete cascade;

alter table hs_hr_jobtit_empstat
       add constraint foreign key (estat_code)
                             references hs_hr_empstat(estat_code) on delete cascade;

alter table hs_hr_membership
       add constraint foreign key (membtype_code)
                             references hs_hr_membership_type(membtype_code) on delete cascade;

alter table hs_hr_employee
       add constraint foreign key (work_station)
                             references hs_hr_compstructtree(id) on delete set null;

alter table hs_hr_employee
       add constraint foreign key (ethnic_race_code)
                             references hs_hr_ethnic_race(ethnic_race_code) on delete set null;

alter table hs_hr_employee
       add constraint foreign key (nation_code)
                             references hs_hr_nationality(nat_code) on delete set null;

alter table hs_hr_employee
       add constraint foreign key (job_title_code)
                             references hs_hr_job_title(jobtit_code) on delete set null;

alter table hs_hr_employee
       add constraint foreign key (emp_status)
                             references hs_hr_empstat(estat_code) on delete set null;

alter table hs_hr_employee
       add constraint foreign key (eeo_cat_code)
                             references hs_hr_eec(eec_code) on delete set null;

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

alter table hs_hr_emp_licenses
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_licenses
       add constraint foreign key (licenses_code)
                             references hs_hr_licenses(licenses_code) on delete cascade;

alter table hs_hr_emp_skill
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_skill
       add constraint foreign key (skill_code)
                             references hs_hr_skill(skill_code) on delete cascade;

alter table hs_hr_emp_attachment
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_picture
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_education
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_education
       add constraint foreign key (edu_code)
                             references hs_hr_education(edu_code) on delete cascade;

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
       add constraint foreign key (membtype_code)
                             references hs_hr_membership_type(membtype_code) on delete cascade;

alter table hs_hr_emp_member_detail
       add constraint foreign key (membship_code)
                             references hs_hr_membership(membship_code) on delete cascade;

alter table hs_hr_emp_member_detail
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_reportto
       add constraint foreign key (erep_sup_emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_reportto
       add constraint foreign key (erep_sub_emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_basicsalary
       add constraint foreign key (sal_grd_code)
                             references hs_pr_salary_grade(sal_grd_code) on delete cascade;

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
       add constraint foreign key (lang_code)
                             references hs_hr_language(lang_code) on delete cascade;

alter table hs_hr_emp_us_tax
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_contract_extend
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_db_version
       add constraint foreign key (entered_by)
       						references hs_hr_users (id) on delete cascade;

alter table hs_hr_db_version
       add constraint foreign key (modified_by)
       						references hs_hr_users (id) on delete cascade;

alter table hs_hr_file_version
       add constraint foreign key (altered_module)
							references hs_hr_module (mod_id) on delete cascade;

alter table hs_hr_file_version
       add constraint foreign key (entered_by)
       						references hs_hr_users (id) on delete cascade;

alter table hs_hr_file_version
       add constraint foreign key (modified_by)
       						references hs_hr_users (id) on delete cascade;

alter table hs_hr_module
       add constraint foreign key (version)
       						references hs_hr_versions (id) on delete cascade;

alter table hs_hr_rights
       add constraint foreign key (mod_id)
       						references hs_hr_module (mod_id) on delete cascade;

alter table hs_hr_rights
       add constraint foreign key (userg_id)
       						references hs_hr_user_group (userg_id) on delete cascade;

alter table hs_hr_users
       add constraint foreign key (modified_user_id)
       						references hs_hr_users (id) on delete set null;

alter table hs_hr_users
       add constraint foreign key (created_by)
       						references hs_hr_users (id) on delete set null;

alter table hs_hr_users
       add constraint foreign key (userg_id)
       						references hs_hr_user_group (userg_id) on delete set null;

alter table hs_hr_users
       add constraint foreign key (emp_number)
       						references hs_hr_employee (emp_number) on delete set null;

alter table hs_hr_versions
       add constraint foreign key (modified_by)
       						references hs_hr_users (id) on delete cascade;

alter table hs_hr_versions
       add constraint foreign key (created_by)
       						references hs_hr_users (id) on delete cascade;

alter table hs_hr_versions
       add constraint foreign key (db_version)
       						references hs_hr_db_version (id) on delete cascade;

alter table hs_hr_versions
       add constraint foreign key (file_version)
       						references hs_hr_file_version (id) on delete cascade;

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

alter table hs_hr_leave_requests
       add constraint foreign key (employee_id)
       						references hs_hr_employee (emp_number) on delete cascade;

alter table hs_hr_leave_requests
       add constraint foreign key (leave_type_id)
       						references hs_hr_leavetype (leave_type_id) on delete cascade;

alter table hs_hr_leave
		add foreign key (leave_request_id,leave_type_id,employee_id)
							references hs_hr_leave_requests
									(leave_request_id,leave_type_id,employee_id) on delete cascade;

alter table hs_hr_mailnotifications
       add constraint foreign key (user_id)
       						references hs_hr_users (id) on delete cascade;

alter table `hs_hr_project`
  add constraint foreign key (`customer_id`)
	references `hs_hr_customer` (`customer_id`)
		on delete restrict;
alter table `hs_hr_project_activity`
  add constraint foreign key (`project_id`) references `hs_hr_project` (`project_id`) on delete cascade;

alter table `hs_hr_project_admin`
  add constraint foreign key (`project_id`) references `hs_hr_project` (`project_id`) on delete cascade,
  add constraint foreign key (`emp_number`) references `hs_hr_employee` (`emp_number`) on delete cascade;

alter table `hs_hr_employee_timesheet_period`
  add constraint foreign key (`employee_id`) references `hs_hr_employee` (`emp_number`) on delete cascade,
  add constraint foreign key (`timesheet_period_id`) references `hs_hr_timesheet_submission_period` (`timesheet_period_id`) on delete cascade;


alter table `hs_hr_timesheet`
  add constraint foreign key (`employee_id`) references `hs_hr_employee` (`emp_number`) on delete cascade,
  add constraint foreign key (`timesheet_period_id`) references `hs_hr_timesheet_submission_period` (`timesheet_period_id`) on delete cascade;

alter table `hs_hr_time_event`
  add constraint foreign key (`timesheet_id`) references `hs_hr_timesheet` (`timesheet_id`) on delete cascade,
  add constraint foreign key (`activity_id`) references `hs_hr_project_activity` (`activity_id`) on delete cascade,
  add constraint foreign key (`project_id`) references `hs_hr_project` (`project_id`) on delete cascade,
  add constraint foreign key (`employee_id`) references `hs_hr_employee` (`emp_number`) on delete cascade;

alter table `hs_hr_employee_workshift`
  add constraint foreign key (`workshift_id`) references `hs_hr_workshift` (`workshift_id`) on delete cascade,
  add constraint foreign key (`emp_number`) references `hs_hr_employee` (`emp_number`) on delete cascade;

alter table `hs_hr_hsp`
  add constraint foreign key (`employee_id`) references `hs_hr_employee` (`emp_number`) on delete cascade;

alter table `hs_hr_hsp_payment_request`
  add constraint foreign key (`employee_id`) references `hs_hr_employee` (`emp_number`) on delete cascade;

alter table `hs_hr_job_vacancy`
  add constraint foreign key (`manager_id`) references `hs_hr_employee` (`emp_number`) on delete set null,
  add constraint foreign key (jobtit_code) references hs_hr_job_title(jobtit_code) on delete set null;

alter table `hs_hr_job_application`
  add constraint foreign key (`vacancy_id`) references `hs_hr_job_vacancy` (`vacancy_id`) on delete cascade;

alter table `hs_hr_job_application_events`
  add constraint foreign key (`application_id`) references `hs_hr_job_application` (`application_id`) on delete cascade,
  add constraint foreign key (`created_by`) references `hs_hr_users` (`id`) on delete set null,
  add constraint foreign key (`owner`) references `hs_hr_employee` (`emp_number`) on delete set null;

alter table `hs_hr_emp_jobtitle_history`
    add constraint foreign key (`emp_number`)
        references hs_hr_employee(`emp_number`) on delete cascade;

alter table `hs_hr_emp_subdivision_history`
    add constraint foreign key (`emp_number`)
        references hs_hr_employee(`emp_number`) on delete cascade;

alter table `hs_hr_emp_location_history`
    add constraint foreign key (`emp_number`)
        references hs_hr_employee(`emp_number`) on delete cascade;

alter table `hs_hr_emp_locations`
    add constraint foreign key (`loc_code`)
        references hs_hr_location(`loc_code`) on delete cascade,
    add constraint foreign key (`emp_number`)
        references hs_hr_employee(`emp_number`) on delete cascade;

INSERT INTO `hs_hr_country` VALUES ('AF', 'AFGHANISTAN', 'Afghanistan', 'AFG', 4);
INSERT INTO `hs_hr_country` VALUES ('AL', 'ALBANIA', 'Albania', 'ALB', 8);
INSERT INTO `hs_hr_country` VALUES ('DZ', 'ALGERIA', 'Algeria', 'DZA', 12);
INSERT INTO `hs_hr_country` VALUES ('AS', 'AMERICAN SAMOA', 'American Samoa', 'ASM', 16);
INSERT INTO `hs_hr_country` VALUES ('AD', 'ANDORRA', 'Andorra', 'AND', 20);
INSERT INTO `hs_hr_country` VALUES ('AO', 'ANGOLA', 'Angola', 'AGO', 24);
INSERT INTO `hs_hr_country` VALUES ('AI', 'ANGUILLA', 'Anguilla', 'AIA', 660);
INSERT INTO `hs_hr_country` VALUES ('AQ', 'ANTARCTICA', 'Antarctica', NULL, NULL);
INSERT INTO `hs_hr_country` VALUES ('AG', 'ANTIGUA AND BARBUDA', 'Antigua and Barbuda', 'ATG', 28);
INSERT INTO `hs_hr_country` VALUES ('AR', 'ARGENTINA', 'Argentina', 'ARG', 32);
INSERT INTO `hs_hr_country` VALUES ('AM', 'ARMENIA', 'Armenia', 'ARM', 51);
INSERT INTO `hs_hr_country` VALUES ('AW', 'ARUBA', 'Aruba', 'ABW', 533);
INSERT INTO `hs_hr_country` VALUES ('AU', 'AUSTRALIA', 'Australia', 'AUS', 36);
INSERT INTO `hs_hr_country` VALUES ('AT', 'AUSTRIA', 'Austria', 'AUT', 40);
INSERT INTO `hs_hr_country` VALUES ('AZ', 'AZERBAIJAN', 'Azerbaijan', 'AZE', 31);
INSERT INTO `hs_hr_country` VALUES ('BS', 'BAHAMAS', 'Bahamas', 'BHS', 44);
INSERT INTO `hs_hr_country` VALUES ('BH', 'BAHRAIN', 'Bahrain', 'BHR', 48);
INSERT INTO `hs_hr_country` VALUES ('BD', 'BANGLADESH', 'Bangladesh', 'BGD', 50);
INSERT INTO `hs_hr_country` VALUES ('BB', 'BARBADOS', 'Barbados', 'BRB', 52);
INSERT INTO `hs_hr_country` VALUES ('BY', 'BELARUS', 'Belarus', 'BLR', 112);
INSERT INTO `hs_hr_country` VALUES ('BE', 'BELGIUM', 'Belgium', 'BEL', 56);
INSERT INTO `hs_hr_country` VALUES ('BZ', 'BELIZE', 'Belize', 'BLZ', 84);
INSERT INTO `hs_hr_country` VALUES ('BJ', 'BENIN', 'Benin', 'BEN', 204);
INSERT INTO `hs_hr_country` VALUES ('BM', 'BERMUDA', 'Bermuda', 'BMU', 60);
INSERT INTO `hs_hr_country` VALUES ('BT', 'BHUTAN', 'Bhutan', 'BTN', 64);
INSERT INTO `hs_hr_country` VALUES ('BO', 'BOLIVIA', 'Bolivia', 'BOL', 68);
INSERT INTO `hs_hr_country` VALUES ('BA', 'BOSNIA AND HERZEGOVINA', 'Bosnia and Herzegovina', 'BIH', 70);
INSERT INTO `hs_hr_country` VALUES ('BW', 'BOTSWANA', 'Botswana', 'BWA', 72);
INSERT INTO `hs_hr_country` VALUES ('BV', 'BOUVET ISLAND', 'Bouvet Island', NULL, NULL);
INSERT INTO `hs_hr_country` VALUES ('BR', 'BRAZIL', 'Brazil', 'BRA', 76);
INSERT INTO `hs_hr_country` VALUES ('IO', 'BRITISH INDIAN OCEAN TERRITORY', 'British Indian Ocean Territory', NULL, NULL);
INSERT INTO `hs_hr_country` VALUES ('BN', 'BRUNEI DARUSSALAM', 'Brunei Darussalam', 'BRN', 96);
INSERT INTO `hs_hr_country` VALUES ('BG', 'BULGARIA', 'Bulgaria', 'BGR', 100);
INSERT INTO `hs_hr_country` VALUES ('BF', 'BURKINA FASO', 'Burkina Faso', 'BFA', 854);
INSERT INTO `hs_hr_country` VALUES ('BI', 'BURUNDI', 'Burundi', 'BDI', 108);
INSERT INTO `hs_hr_country` VALUES ('KH', 'CAMBODIA', 'Cambodia', 'KHM', 116);
INSERT INTO `hs_hr_country` VALUES ('CM', 'CAMEROON', 'Cameroon', 'CMR', 120);
INSERT INTO `hs_hr_country` VALUES ('CA', 'CANADA', 'Canada', 'CAN', 124);
INSERT INTO `hs_hr_country` VALUES ('CV', 'CAPE VERDE', 'Cape Verde', 'CPV', 132);
INSERT INTO `hs_hr_country` VALUES ('KY', 'CAYMAN ISLANDS', 'Cayman Islands', 'CYM', 136);
INSERT INTO `hs_hr_country` VALUES ('CF', 'CENTRAL AFRICAN REPUBLIC', 'Central African Republic', 'CAF', 140);
INSERT INTO `hs_hr_country` VALUES ('TD', 'CHAD', 'Chad', 'TCD', 148);
INSERT INTO `hs_hr_country` VALUES ('CL', 'CHILE', 'Chile', 'CHL', 152);
INSERT INTO `hs_hr_country` VALUES ('CN', 'CHINA', 'China', 'CHN', 156);
INSERT INTO `hs_hr_country` VALUES ('CX', 'CHRISTMAS ISLAND', 'Christmas Island', NULL, NULL);
INSERT INTO `hs_hr_country` VALUES ('CC', 'COCOS (KEELING) ISLANDS', 'Cocos (Keeling) Islands', NULL, NULL);
INSERT INTO `hs_hr_country` VALUES ('CO', 'COLOMBIA', 'Colombia', 'COL', 170);
INSERT INTO `hs_hr_country` VALUES ('KM', 'COMOROS', 'Comoros', 'COM', 174);
INSERT INTO `hs_hr_country` VALUES ('CG', 'CONGO', 'Congo', 'COG', 178);
INSERT INTO `hs_hr_country` VALUES ('CD', 'CONGO, THE DEMOCRATIC REPUBLIC OF THE', 'Congo, the Democratic Republic of the', 'COD', 180);
INSERT INTO `hs_hr_country` VALUES ('CK', 'COOK ISLANDS', 'Cook Islands', 'COK', 184);
INSERT INTO `hs_hr_country` VALUES ('CR', 'COSTA RICA', 'Costa Rica', 'CRI', 188);
INSERT INTO `hs_hr_country` VALUES ('CI', 'COTE D''IVOIRE', 'Cote D''Ivoire', 'CIV', 384);
INSERT INTO `hs_hr_country` VALUES ('HR', 'CROATIA', 'Croatia', 'HRV', 191);
INSERT INTO `hs_hr_country` VALUES ('CU', 'CUBA', 'Cuba', 'CUB', 192);
INSERT INTO `hs_hr_country` VALUES ('CY', 'CYPRUS', 'Cyprus', 'CYP', 196);
INSERT INTO `hs_hr_country` VALUES ('CZ', 'CZECH REPUBLIC', 'Czech Republic', 'CZE', 203);
INSERT INTO `hs_hr_country` VALUES ('DK', 'DENMARK', 'Denmark', 'DNK', 208);
INSERT INTO `hs_hr_country` VALUES ('DJ', 'DJIBOUTI', 'Djibouti', 'DJI', 262);
INSERT INTO `hs_hr_country` VALUES ('DM', 'DOMINICA', 'Dominica', 'DMA', 212);
INSERT INTO `hs_hr_country` VALUES ('DO', 'DOMINICAN REPUBLIC', 'Dominican Republic', 'DOM', 214);
INSERT INTO `hs_hr_country` VALUES ('EC', 'ECUADOR', 'Ecuador', 'ECU', 218);
INSERT INTO `hs_hr_country` VALUES ('EG', 'EGYPT', 'Egypt', 'EGY', 818);
INSERT INTO `hs_hr_country` VALUES ('SV', 'EL SALVADOR', 'El Salvador', 'SLV', 222);
INSERT INTO `hs_hr_country` VALUES ('GQ', 'EQUATORIAL GUINEA', 'Equatorial Guinea', 'GNQ', 226);
INSERT INTO `hs_hr_country` VALUES ('ER', 'ERITREA', 'Eritrea', 'ERI', 232);
INSERT INTO `hs_hr_country` VALUES ('EE', 'ESTONIA', 'Estonia', 'EST', 233);
INSERT INTO `hs_hr_country` VALUES ('ET', 'ETHIOPIA', 'Ethiopia', 'ETH', 231);
INSERT INTO `hs_hr_country` VALUES ('FK', 'FALKLAND ISLANDS (MALVINAS)', 'Falkland Islands (Malvinas)', 'FLK', 238);
INSERT INTO `hs_hr_country` VALUES ('FO', 'FAROE ISLANDS', 'Faroe Islands', 'FRO', 234);
INSERT INTO `hs_hr_country` VALUES ('FJ', 'FIJI', 'Fiji', 'FJI', 242);
INSERT INTO `hs_hr_country` VALUES ('FI', 'FINLAND', 'Finland', 'FIN', 246);
INSERT INTO `hs_hr_country` VALUES ('FR', 'FRANCE', 'France', 'FRA', 250);
INSERT INTO `hs_hr_country` VALUES ('GF', 'FRENCH GUIANA', 'French Guiana', 'GUF', 254);
INSERT INTO `hs_hr_country` VALUES ('PF', 'FRENCH POLYNESIA', 'French Polynesia', 'PYF', 258);
INSERT INTO `hs_hr_country` VALUES ('TF', 'FRENCH SOUTHERN TERRITORIES', 'French Southern Territories', NULL, NULL);
INSERT INTO `hs_hr_country` VALUES ('GA', 'GABON', 'Gabon', 'GAB', 266);
INSERT INTO `hs_hr_country` VALUES ('GM', 'GAMBIA', 'Gambia', 'GMB', 270);
INSERT INTO `hs_hr_country` VALUES ('GE', 'GEORGIA', 'Georgia', 'GEO', 268);
INSERT INTO `hs_hr_country` VALUES ('DE', 'GERMANY', 'Germany', 'DEU', 276);
INSERT INTO `hs_hr_country` VALUES ('GH', 'GHANA', 'Ghana', 'GHA', 288);
INSERT INTO `hs_hr_country` VALUES ('GI', 'GIBRALTAR', 'Gibraltar', 'GIB', 292);
INSERT INTO `hs_hr_country` VALUES ('GR', 'GREECE', 'Greece', 'GRC', 300);
INSERT INTO `hs_hr_country` VALUES ('GL', 'GREENLAND', 'Greenland', 'GRL', 304);
INSERT INTO `hs_hr_country` VALUES ('GD', 'GRENADA', 'Grenada', 'GRD', 308);
INSERT INTO `hs_hr_country` VALUES ('GP', 'GUADELOUPE', 'Guadeloupe', 'GLP', 312);
INSERT INTO `hs_hr_country` VALUES ('GU', 'GUAM', 'Guam', 'GUM', 316);
INSERT INTO `hs_hr_country` VALUES ('GT', 'GUATEMALA', 'Guatemala', 'GTM', 320);
INSERT INTO `hs_hr_country` VALUES ('GN', 'GUINEA', 'Guinea', 'GIN', 324);
INSERT INTO `hs_hr_country` VALUES ('GW', 'GUINEA-BISSAU', 'Guinea-Bissau', 'GNB', 624);
INSERT INTO `hs_hr_country` VALUES ('GY', 'GUYANA', 'Guyana', 'GUY', 328);
INSERT INTO `hs_hr_country` VALUES ('HT', 'HAITI', 'Haiti', 'HTI', 332);
INSERT INTO `hs_hr_country` VALUES ('HM', 'HEARD ISLAND AND MCDONALD ISLANDS', 'Heard Island and Mcdonald Islands', NULL, NULL);
INSERT INTO `hs_hr_country` VALUES ('VA', 'HOLY SEE (VATICAN CITY STATE)', 'Holy See (Vatican City State)', 'VAT', 336);
INSERT INTO `hs_hr_country` VALUES ('HN', 'HONDURAS', 'Honduras', 'HND', 340);
INSERT INTO `hs_hr_country` VALUES ('HK', 'HONG KONG', 'Hong Kong', 'HKG', 344);
INSERT INTO `hs_hr_country` VALUES ('HU', 'HUNGARY', 'Hungary', 'HUN', 348);
INSERT INTO `hs_hr_country` VALUES ('IS', 'ICELAND', 'Iceland', 'ISL', 352);
INSERT INTO `hs_hr_country` VALUES ('IN', 'INDIA', 'India', 'IND', 356);
INSERT INTO `hs_hr_country` VALUES ('ID', 'INDONESIA', 'Indonesia', 'IDN', 360);
INSERT INTO `hs_hr_country` VALUES ('IR', 'IRAN, ISLAMIC REPUBLIC OF', 'Iran, Islamic Republic of', 'IRN', 364);
INSERT INTO `hs_hr_country` VALUES ('IQ', 'IRAQ', 'Iraq', 'IRQ', 368);
INSERT INTO `hs_hr_country` VALUES ('IE', 'IRELAND', 'Ireland', 'IRL', 372);
INSERT INTO `hs_hr_country` VALUES ('IL', 'ISRAEL', 'Israel', 'ISR', 376);
INSERT INTO `hs_hr_country` VALUES ('IT', 'ITALY', 'Italy', 'ITA', 380);
INSERT INTO `hs_hr_country` VALUES ('JM', 'JAMAICA', 'Jamaica', 'JAM', 388);
INSERT INTO `hs_hr_country` VALUES ('JP', 'JAPAN', 'Japan', 'JPN', 392);
INSERT INTO `hs_hr_country` VALUES ('JO', 'JORDAN', 'Jordan', 'JOR', 400);
INSERT INTO `hs_hr_country` VALUES ('KZ', 'KAZAKHSTAN', 'Kazakhstan', 'KAZ', 398);
INSERT INTO `hs_hr_country` VALUES ('KE', 'KENYA', 'Kenya', 'KEN', 404);
INSERT INTO `hs_hr_country` VALUES ('KI', 'KIRIBATI', 'Kiribati', 'KIR', 296);
INSERT INTO `hs_hr_country` VALUES ('KP', 'KOREA, DEMOCRATIC PEOPLE''S REPUBLIC OF', 'Korea, Democratic People''s Republic of', 'PRK', 408);
INSERT INTO `hs_hr_country` VALUES ('KR', 'KOREA, REPUBLIC OF', 'Korea, Republic of', 'KOR', 410);
INSERT INTO `hs_hr_country` VALUES ('KW', 'KUWAIT', 'Kuwait', 'KWT', 414);
INSERT INTO `hs_hr_country` VALUES ('KG', 'KYRGYZSTAN', 'Kyrgyzstan', 'KGZ', 417);
INSERT INTO `hs_hr_country` VALUES ('LA', 'LAO PEOPLE''S DEMOCRATIC REPUBLIC', 'Lao People''s Democratic Republic', 'LAO', 418);
INSERT INTO `hs_hr_country` VALUES ('LV', 'LATVIA', 'Latvia', 'LVA', 428);
INSERT INTO `hs_hr_country` VALUES ('LB', 'LEBANON', 'Lebanon', 'LBN', 422);
INSERT INTO `hs_hr_country` VALUES ('LS', 'LESOTHO', 'Lesotho', 'LSO', 426);
INSERT INTO `hs_hr_country` VALUES ('LR', 'LIBERIA', 'Liberia', 'LBR', 430);
INSERT INTO `hs_hr_country` VALUES ('LY', 'LIBYAN ARAB JAMAHIRIYA', 'Libyan Arab Jamahiriya', 'LBY', 434);
INSERT INTO `hs_hr_country` VALUES ('LI', 'LIECHTENSTEIN', 'Liechtenstein', 'LIE', 438);
INSERT INTO `hs_hr_country` VALUES ('LT', 'LITHUANIA', 'Lithuania', 'LTU', 440);
INSERT INTO `hs_hr_country` VALUES ('LU', 'LUXEMBOURG', 'Luxembourg', 'LUX', 442);
INSERT INTO `hs_hr_country` VALUES ('MO', 'MACAO', 'Macao', 'MAC', 446);
INSERT INTO `hs_hr_country` VALUES ('MK', 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'Macedonia, the Former Yugoslav Republic of', 'MKD', 807);
INSERT INTO `hs_hr_country` VALUES ('MG', 'MADAGASCAR', 'Madagascar', 'MDG', 450);
INSERT INTO `hs_hr_country` VALUES ('MW', 'MALAWI', 'Malawi', 'MWI', 454);
INSERT INTO `hs_hr_country` VALUES ('MY', 'MALAYSIA', 'Malaysia', 'MYS', 458);
INSERT INTO `hs_hr_country` VALUES ('MV', 'MALDIVES', 'Maldives', 'MDV', 462);
INSERT INTO `hs_hr_country` VALUES ('ML', 'MALI', 'Mali', 'MLI', 466);
INSERT INTO `hs_hr_country` VALUES ('MT', 'MALTA', 'Malta', 'MLT', 470);
INSERT INTO `hs_hr_country` VALUES ('MH', 'MARSHALL ISLANDS', 'Marshall Islands', 'MHL', 584);
INSERT INTO `hs_hr_country` VALUES ('MQ', 'MARTINIQUE', 'Martinique', 'MTQ', 474);
INSERT INTO `hs_hr_country` VALUES ('MR', 'MAURITANIA', 'Mauritania', 'MRT', 478);
INSERT INTO `hs_hr_country` VALUES ('MU', 'MAURITIUS', 'Mauritius', 'MUS', 480);
INSERT INTO `hs_hr_country` VALUES ('YT', 'MAYOTTE', 'Mayotte', NULL, NULL);
INSERT INTO `hs_hr_country` VALUES ('MX', 'MEXICO', 'Mexico', 'MEX', 484);
INSERT INTO `hs_hr_country` VALUES ('FM', 'MICRONESIA, FEDERATED STATES OF', 'Micronesia, Federated States of', 'FSM', 583);
INSERT INTO `hs_hr_country` VALUES ('MD', 'MOLDOVA, REPUBLIC OF', 'Moldova, Republic of', 'MDA', 498);
INSERT INTO `hs_hr_country` VALUES ('MC', 'MONACO', 'Monaco', 'MCO', 492);
INSERT INTO `hs_hr_country` VALUES ('MN', 'MONGOLIA', 'Mongolia', 'MNG', 496);
INSERT INTO `hs_hr_country` VALUES ('MS', 'MONTSERRAT', 'Montserrat', 'MSR', 500);
INSERT INTO `hs_hr_country` VALUES ('MA', 'MOROCCO', 'Morocco', 'MAR', 504);
INSERT INTO `hs_hr_country` VALUES ('MZ', 'MOZAMBIQUE', 'Mozambique', 'MOZ', 508);
INSERT INTO `hs_hr_country` VALUES ('MM', 'MYANMAR', 'Myanmar', 'MMR', 104);
INSERT INTO `hs_hr_country` VALUES ('NA', 'NAMIBIA', 'Namibia', 'NAM', 516);
INSERT INTO `hs_hr_country` VALUES ('NR', 'NAURU', 'Nauru', 'NRU', 520);
INSERT INTO `hs_hr_country` VALUES ('NP', 'NEPAL', 'Nepal', 'NPL', 524);
INSERT INTO `hs_hr_country` VALUES ('NL', 'NETHERLANDS', 'Netherlands', 'NLD', 528);
INSERT INTO `hs_hr_country` VALUES ('AN', 'NETHERLANDS ANTILLES', 'Netherlands Antilles', 'ANT', 530);
INSERT INTO `hs_hr_country` VALUES ('NC', 'NEW CALEDONIA', 'New Caledonia', 'NCL', 540);
INSERT INTO `hs_hr_country` VALUES ('NZ', 'NEW ZEALAND', 'New Zealand', 'NZL', 554);
INSERT INTO `hs_hr_country` VALUES ('NI', 'NICARAGUA', 'Nicaragua', 'NIC', 558);
INSERT INTO `hs_hr_country` VALUES ('NE', 'NIGER', 'Niger', 'NER', 562);
INSERT INTO `hs_hr_country` VALUES ('NG', 'NIGERIA', 'Nigeria', 'NGA', 566);
INSERT INTO `hs_hr_country` VALUES ('NU', 'NIUE', 'Niue', 'NIU', 570);
INSERT INTO `hs_hr_country` VALUES ('NF', 'NORFOLK ISLAND', 'Norfolk Island', 'NFK', 574);
INSERT INTO `hs_hr_country` VALUES ('MP', 'NORTHERN MARIANA ISLANDS', 'Northern Mariana Islands', 'MNP', 580);
INSERT INTO `hs_hr_country` VALUES ('NO', 'NORWAY', 'Norway', 'NOR', 578);
INSERT INTO `hs_hr_country` VALUES ('OM', 'OMAN', 'Oman', 'OMN', 512);
INSERT INTO `hs_hr_country` VALUES ('PK', 'PAKISTAN', 'Pakistan', 'PAK', 586);
INSERT INTO `hs_hr_country` VALUES ('PW', 'PALAU', 'Palau', 'PLW', 585);
INSERT INTO `hs_hr_country` VALUES ('PS', 'PALESTINIAN TERRITORY, OCCUPIED', 'Palestinian Territory, Occupied', NULL, NULL);
INSERT INTO `hs_hr_country` VALUES ('PA', 'PANAMA', 'Panama', 'PAN', 591);
INSERT INTO `hs_hr_country` VALUES ('PG', 'PAPUA NEW GUINEA', 'Papua New Guinea', 'PNG', 598);
INSERT INTO `hs_hr_country` VALUES ('PY', 'PARAGUAY', 'Paraguay', 'PRY', 600);
INSERT INTO `hs_hr_country` VALUES ('PE', 'PERU', 'Peru', 'PER', 604);
INSERT INTO `hs_hr_country` VALUES ('PH', 'PHILIPPINES', 'Philippines', 'PHL', 608);
INSERT INTO `hs_hr_country` VALUES ('PN', 'PITCAIRN', 'Pitcairn', 'PCN', 612);
INSERT INTO `hs_hr_country` VALUES ('PL', 'POLAND', 'Poland', 'POL', 616);
INSERT INTO `hs_hr_country` VALUES ('PT', 'PORTUGAL', 'Portugal', 'PRT', 620);
INSERT INTO `hs_hr_country` VALUES ('PR', 'PUERTO RICO', 'Puerto Rico', 'PRI', 630);
INSERT INTO `hs_hr_country` VALUES ('QA', 'QATAR', 'Qatar', 'QAT', 634);
INSERT INTO `hs_hr_country` VALUES ('RE', 'REUNION', 'Reunion', 'REU', 638);
INSERT INTO `hs_hr_country` VALUES ('RO', 'ROMANIA', 'Romania', 'ROM', 642);
INSERT INTO `hs_hr_country` VALUES ('RU', 'RUSSIAN FEDERATION', 'Russian Federation', 'RUS', 643);
INSERT INTO `hs_hr_country` VALUES ('RW', 'RWANDA', 'Rwanda', 'RWA', 646);
INSERT INTO `hs_hr_country` VALUES ('SH', 'SAINT HELENA', 'Saint Helena', 'SHN', 654);
INSERT INTO `hs_hr_country` VALUES ('KN', 'SAINT KITTS AND NEVIS', 'Saint Kitts and Nevis', 'KNA', 659);
INSERT INTO `hs_hr_country` VALUES ('LC', 'SAINT LUCIA', 'Saint Lucia', 'LCA', 662);
INSERT INTO `hs_hr_country` VALUES ('PM', 'SAINT PIERRE AND MIQUELON', 'Saint Pierre and Miquelon', 'SPM', 666);
INSERT INTO `hs_hr_country` VALUES ('VC', 'SAINT VINCENT AND THE GRENADINES', 'Saint Vincent and the Grenadines', 'VCT', 670);
INSERT INTO `hs_hr_country` VALUES ('WS', 'SAMOA', 'Samoa', 'WSM', 882);
INSERT INTO `hs_hr_country` VALUES ('SM', 'SAN MARINO', 'San Marino', 'SMR', 674);
INSERT INTO `hs_hr_country` VALUES ('ST', 'SAO TOME AND PRINCIPE', 'Sao Tome and Principe', 'STP', 678);
INSERT INTO `hs_hr_country` VALUES ('SA', 'SAUDI ARABIA', 'Saudi Arabia', 'SAU', 682);
INSERT INTO `hs_hr_country` VALUES ('SN', 'SENEGAL', 'Senegal', 'SEN', 686);
INSERT INTO `hs_hr_country` VALUES ('CS', 'SERBIA AND MONTENEGRO', 'Serbia and Montenegro', NULL, NULL);
INSERT INTO `hs_hr_country` VALUES ('SC', 'SEYCHELLES', 'Seychelles', 'SYC', 690);
INSERT INTO `hs_hr_country` VALUES ('SL', 'SIERRA LEONE', 'Sierra Leone', 'SLE', 694);
INSERT INTO `hs_hr_country` VALUES ('SG', 'SINGAPORE', 'Singapore', 'SGP', 702);
INSERT INTO `hs_hr_country` VALUES ('SK', 'SLOVAKIA', 'Slovakia', 'SVK', 703);
INSERT INTO `hs_hr_country` VALUES ('SI', 'SLOVENIA', 'Slovenia', 'SVN', 705);
INSERT INTO `hs_hr_country` VALUES ('SB', 'SOLOMON ISLANDS', 'Solomon Islands', 'SLB', 90);
INSERT INTO `hs_hr_country` VALUES ('SO', 'SOMALIA', 'Somalia', 'SOM', 706);
INSERT INTO `hs_hr_country` VALUES ('ZA', 'SOUTH AFRICA', 'South Africa', 'ZAF', 710);
INSERT INTO `hs_hr_country` VALUES ('GS', 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS', 'South Georgia and the South Sandwich Islands', NULL, NULL);
INSERT INTO `hs_hr_country` VALUES ('ES', 'SPAIN', 'Spain', 'ESP', 724);
INSERT INTO `hs_hr_country` VALUES ('LK', 'SRI LANKA', 'Sri Lanka', 'LKA', 144);
INSERT INTO `hs_hr_country` VALUES ('SD', 'SUDAN', 'Sudan', 'SDN', 736);
INSERT INTO `hs_hr_country` VALUES ('SR', 'SURINAME', 'Suriname', 'SUR', 740);
INSERT INTO `hs_hr_country` VALUES ('SJ', 'SVALBARD AND JAN MAYEN', 'Svalbard and Jan Mayen', 'SJM', 744);
INSERT INTO `hs_hr_country` VALUES ('SZ', 'SWAZILAND', 'Swaziland', 'SWZ', 748);
INSERT INTO `hs_hr_country` VALUES ('SE', 'SWEDEN', 'Sweden', 'SWE', 752);
INSERT INTO `hs_hr_country` VALUES ('CH', 'SWITZERLAND', 'Switzerland', 'CHE', 756);
INSERT INTO `hs_hr_country` VALUES ('SY', 'SYRIAN ARAB REPUBLIC', 'Syrian Arab Republic', 'SYR', 760);
INSERT INTO `hs_hr_country` VALUES ('TW', 'TAIWAN, PROVINCE OF CHINA', 'Taiwan', 'TWN', 158);
INSERT INTO `hs_hr_country` VALUES ('TJ', 'TAJIKISTAN', 'Tajikistan', 'TJK', 762);
INSERT INTO `hs_hr_country` VALUES ('TZ', 'TANZANIA, UNITED REPUBLIC OF', 'Tanzania, United Republic of', 'TZA', 834);
INSERT INTO `hs_hr_country` VALUES ('TH', 'THAILAND', 'Thailand', 'THA', 764);
INSERT INTO `hs_hr_country` VALUES ('TL', 'TIMOR-LESTE', 'Timor-Leste', NULL, NULL);
INSERT INTO `hs_hr_country` VALUES ('TG', 'TOGO', 'Togo', 'TGO', 768);
INSERT INTO `hs_hr_country` VALUES ('TK', 'TOKELAU', 'Tokelau', 'TKL', 772);
INSERT INTO `hs_hr_country` VALUES ('TO', 'TONGA', 'Tonga', 'TON', 776);
INSERT INTO `hs_hr_country` VALUES ('TT', 'TRINIDAD AND TOBAGO', 'Trinidad and Tobago', 'TTO', 780);
INSERT INTO `hs_hr_country` VALUES ('TN', 'TUNISIA', 'Tunisia', 'TUN', 788);
INSERT INTO `hs_hr_country` VALUES ('TR', 'TURKEY', 'Turkey', 'TUR', 792);
INSERT INTO `hs_hr_country` VALUES ('TM', 'TURKMENISTAN', 'Turkmenistan', 'TKM', 795);
INSERT INTO `hs_hr_country` VALUES ('TC', 'TURKS AND CAICOS ISLANDS', 'Turks and Caicos Islands', 'TCA', 796);
INSERT INTO `hs_hr_country` VALUES ('TV', 'TUVALU', 'Tuvalu', 'TUV', 798);
INSERT INTO `hs_hr_country` VALUES ('UG', 'UGANDA', 'Uganda', 'UGA', 800);
INSERT INTO `hs_hr_country` VALUES ('UA', 'UKRAINE', 'Ukraine', 'UKR', 804);
INSERT INTO `hs_hr_country` VALUES ('AE', 'UNITED ARAB EMIRATES', 'United Arab Emirates', 'ARE', 784);
INSERT INTO `hs_hr_country` VALUES ('GB', 'UNITED KINGDOM', 'United Kingdom', 'GBR', 826);
INSERT INTO `hs_hr_country` VALUES ('US', 'UNITED STATES', 'United States', 'USA', 840);
INSERT INTO `hs_hr_country` VALUES ('UM', 'UNITED STATES MINOR OUTLYING ISLANDS', 'United States Minor Outlying Islands', NULL, NULL);
INSERT INTO `hs_hr_country` VALUES ('UY', 'URUGUAY', 'Uruguay', 'URY', 858);
INSERT INTO `hs_hr_country` VALUES ('UZ', 'UZBEKISTAN', 'Uzbekistan', 'UZB', 860);
INSERT INTO `hs_hr_country` VALUES ('VU', 'VANUATU', 'Vanuatu', 'VUT', 548);
INSERT INTO `hs_hr_country` VALUES ('VE', 'VENEZUELA', 'Venezuela', 'VEN', 862);
INSERT INTO `hs_hr_country` VALUES ('VN', 'VIET NAM', 'Viet Nam', 'VNM', 704);
INSERT INTO `hs_hr_country` VALUES ('VG', 'VIRGIN ISLANDS, BRITISH', 'Virgin Islands, British', 'VGB', 92);
INSERT INTO `hs_hr_country` VALUES ('VI', 'VIRGIN ISLANDS, U.S.', 'Virgin Islands, U.s.', 'VIR', 850);
INSERT INTO `hs_hr_country` VALUES ('WF', 'WALLIS AND FUTUNA', 'Wallis and Futuna', 'WLF', 876);
INSERT INTO `hs_hr_country` VALUES ('EH', 'WESTERN SAHARA', 'Western Sahara', 'ESH', 732);
INSERT INTO `hs_hr_country` VALUES ('YE', 'YEMEN', 'Yemen', 'YEM', 887);
INSERT INTO `hs_hr_country` VALUES ('ZM', 'ZAMBIA', 'Zambia', 'ZMB', 894);
INSERT INTO `hs_hr_country` VALUES ('ZW', 'ZIMBABWE', 'Zimbabwe', 'ZWE', 716);


INSERT INTO `hs_hr_currency_type` VALUES (3, 'AED', 'Utd. Arab Emir. Dirham');
INSERT INTO `hs_hr_currency_type` VALUES (4, 'AFN', 'Afghanistan Afghani');
INSERT INTO `hs_hr_currency_type` VALUES (5, 'ALL', 'Albanian Lek');
INSERT INTO `hs_hr_currency_type` VALUES (6, 'ANG', 'NL Antillian Guilder');
INSERT INTO `hs_hr_currency_type` VALUES (7, 'AOR', 'Angolan New Kwanza');
INSERT INTO `hs_hr_currency_type` VALUES (177, 'ARP', 'Argentina Pesos');
INSERT INTO `hs_hr_currency_type` VALUES (8, 'ARS', 'Argentine Peso');
INSERT INTO `hs_hr_currency_type` VALUES (10, 'AUD', 'Australian Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (11, 'AWG', 'Aruban Florin');
INSERT INTO `hs_hr_currency_type` VALUES (12, 'BBD', 'Barbados Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (13, 'BDT', 'Bangladeshi Taka');
INSERT INTO `hs_hr_currency_type` VALUES (15, 'BGL', 'Bulgarian Lev');
INSERT INTO `hs_hr_currency_type` VALUES (16, 'BHD', 'Bahraini Dinar');
INSERT INTO `hs_hr_currency_type` VALUES (17, 'BIF', 'Burundi Franc');
INSERT INTO `hs_hr_currency_type` VALUES (18, 'BMD', 'Bermudian Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (19, 'BND', 'Brunei Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (20, 'BOB', 'Bolivian Boliviano');
INSERT INTO `hs_hr_currency_type` VALUES (21, 'BRL', 'Brazilian Real');
INSERT INTO `hs_hr_currency_type` VALUES (22, 'BSD', 'Bahamian Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (23, 'BTN', 'Bhutan Ngultrum');
INSERT INTO `hs_hr_currency_type` VALUES (24, 'BWP', 'Botswana Pula');
INSERT INTO `hs_hr_currency_type` VALUES (25, 'BZD', 'Belize Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (26, 'CAD', 'Canadian Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (27, 'CHF', 'Swiss Franc');
INSERT INTO `hs_hr_currency_type` VALUES (28, 'CLP', 'Chilean Peso');
INSERT INTO `hs_hr_currency_type` VALUES (29, 'CNY', 'Chinese Yuan Renminbi');
INSERT INTO `hs_hr_currency_type` VALUES (30, 'COP', 'Colombian Peso');
INSERT INTO `hs_hr_currency_type` VALUES (31, 'CRC', 'Costa Rican Colon');
INSERT INTO `hs_hr_currency_type` VALUES (171, 'CZK', 'Czech Koruna');
INSERT INTO `hs_hr_currency_type` VALUES (32, 'CUP', 'Cuban Peso');
INSERT INTO `hs_hr_currency_type` VALUES (33, 'CVE', 'Cape Verde Escudo');
INSERT INTO `hs_hr_currency_type` VALUES (34, 'CYP', 'Cyprus Pound');
INSERT INTO `hs_hr_currency_type` VALUES (37, 'DJF', 'Djibouti Franc');
INSERT INTO `hs_hr_currency_type` VALUES (38, 'DKK', 'Danish Krona');
INSERT INTO `hs_hr_currency_type` VALUES (39, 'DOP', 'Dominican Peso');
INSERT INTO `hs_hr_currency_type` VALUES (40, 'DZD', 'Algerian Dinar');
INSERT INTO `hs_hr_currency_type` VALUES (41, 'ECS', 'Ecuador Sucre');
INSERT INTO `hs_hr_currency_type` VALUES (43, 'EEK', 'Estonian Krona');
INSERT INTO `hs_hr_currency_type` VALUES (44, 'EGP', 'Egyptian Pound');
INSERT INTO `hs_hr_currency_type` VALUES (46, 'ETB', 'Ethiopian Birr');
INSERT INTO `hs_hr_currency_type` VALUES (42, 'EUR', 'Euro');
INSERT INTO `hs_hr_currency_type` VALUES (48, 'FJD', 'Fiji Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (49, 'FKP', 'Falkland Islands Pound');
INSERT INTO `hs_hr_currency_type` VALUES (51, 'GBP', 'Pound Sterling');
INSERT INTO `hs_hr_currency_type` VALUES (52, 'GHC', 'Ghanaian Cedi');
INSERT INTO `hs_hr_currency_type` VALUES (53, 'GIP', 'Gibraltar Pound');
INSERT INTO `hs_hr_currency_type` VALUES (54, 'GMD', 'Gambian Dalasi');
INSERT INTO `hs_hr_currency_type` VALUES (55, 'GNF', 'Guinea Franc');
INSERT INTO `hs_hr_currency_type` VALUES (57, 'GTQ', 'Guatemalan Quetzal');
INSERT INTO `hs_hr_currency_type` VALUES (58, 'GYD', 'Guyanan Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (59, 'HKD', 'Hong Kong Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (60, 'HNL', 'Honduran Lempira');
INSERT INTO `hs_hr_currency_type` VALUES (61, 'HRK', 'Croatian Kuna');
INSERT INTO `hs_hr_currency_type` VALUES (62, 'HTG', 'Haitian Gourde');
INSERT INTO `hs_hr_currency_type` VALUES (63, 'HUF', 'Hungarian Forint');
INSERT INTO `hs_hr_currency_type` VALUES (64, 'IDR', 'Indonesian Rupiah');
INSERT INTO `hs_hr_currency_type` VALUES (66, 'ILS', 'Israeli New Shekel');
INSERT INTO `hs_hr_currency_type` VALUES (67, 'INR', 'Indian Rupee');
INSERT INTO `hs_hr_currency_type` VALUES (68, 'IQD', 'Iraqi Dinar');
INSERT INTO `hs_hr_currency_type` VALUES (69, 'IRR', 'Iranian Rial');
INSERT INTO `hs_hr_currency_type` VALUES (70, 'ISK', 'Iceland Krona');
INSERT INTO `hs_hr_currency_type` VALUES (72, 'JMD', 'Jamaican Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (73, 'JOD', 'Jordanian Dinar');
INSERT INTO `hs_hr_currency_type` VALUES (74, 'JPY', 'Japanese Yen');
INSERT INTO `hs_hr_currency_type` VALUES (75, 'KES', 'Kenyan Shilling');
INSERT INTO `hs_hr_currency_type` VALUES (76, 'KHR', 'Kampuchean Riel');
INSERT INTO `hs_hr_currency_type` VALUES (77, 'KMF', 'Comoros Franc');
INSERT INTO `hs_hr_currency_type` VALUES (78, 'KPW', 'North Korean Won');
INSERT INTO `hs_hr_currency_type` VALUES (79, 'KRW', 'Korean Won');
INSERT INTO `hs_hr_currency_type` VALUES (80, 'KWD', 'Kuwaiti Dinar');
INSERT INTO `hs_hr_currency_type` VALUES (81, 'KYD', 'Cayman Islands Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (82, 'KZT', 'Kazakhstan Tenge');
INSERT INTO `hs_hr_currency_type` VALUES (83, 'LAK', 'Lao Kip');
INSERT INTO `hs_hr_currency_type` VALUES (84, 'LBP', 'Lebanese Pound');
INSERT INTO `hs_hr_currency_type` VALUES (85, 'LKR', 'Sri Lanka Rupee');
INSERT INTO `hs_hr_currency_type` VALUES (86, 'LRD', 'Liberian Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (87, 'LSL', 'Lesotho Loti');
INSERT INTO `hs_hr_currency_type` VALUES (88, 'LTL', 'Lithuanian Litas');
INSERT INTO `hs_hr_currency_type` VALUES (90, 'LVL', 'Latvian Lats');
INSERT INTO `hs_hr_currency_type` VALUES (91, 'LYD', 'Libyan Dinar');
INSERT INTO `hs_hr_currency_type` VALUES (92, 'MAD', 'Moroccan Dirham');
INSERT INTO `hs_hr_currency_type` VALUES (93, 'MGF', 'Malagasy Franc');
INSERT INTO `hs_hr_currency_type` VALUES (94, 'MMK', 'Myanmar Kyat');
INSERT INTO `hs_hr_currency_type` VALUES (95, 'MNT', 'Mongolian Tugrik');
INSERT INTO `hs_hr_currency_type` VALUES (96, 'MOP', 'Macau Pataca');
INSERT INTO `hs_hr_currency_type` VALUES (97, 'MRO', 'Mauritanian Ouguiya');
INSERT INTO `hs_hr_currency_type` VALUES (98, 'MTL', 'Maltese Lira');
INSERT INTO `hs_hr_currency_type` VALUES (99, 'MUR', 'Mauritius Rupee');
INSERT INTO `hs_hr_currency_type` VALUES (100, 'MVR', 'Maldive Rufiyaa');
INSERT INTO `hs_hr_currency_type` VALUES (101, 'MWK', 'Malawi Kwacha');
INSERT INTO `hs_hr_currency_type` VALUES (102, 'MXN', 'Mexican New Peso');
INSERT INTO `hs_hr_currency_type` VALUES (172, 'MXP', 'Mexican Peso');
INSERT INTO `hs_hr_currency_type` VALUES (103, 'MYR', 'Malaysian Ringgit');
INSERT INTO `hs_hr_currency_type` VALUES (104, 'MZM', 'Mozambique Metical');
INSERT INTO `hs_hr_currency_type` VALUES (105, 'NAD', 'Namibia Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (106, 'NGN', 'Nigerian Naira');
INSERT INTO `hs_hr_currency_type` VALUES (107, 'NIO', 'Nicaraguan Cordoba Oro');
INSERT INTO `hs_hr_currency_type` VALUES (109, 'NOK', 'Norwegian Krona');
INSERT INTO `hs_hr_currency_type` VALUES (110, 'NPR', 'Nepalese Rupee');
INSERT INTO `hs_hr_currency_type` VALUES (111, 'NZD', 'New Zealand Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (112, 'OMR', 'Omani Rial');
INSERT INTO `hs_hr_currency_type` VALUES (113, 'PAB', 'Panamanian Balboa');
INSERT INTO `hs_hr_currency_type` VALUES (114, 'PEN', 'Peruvian Nuevo Sol');
INSERT INTO `hs_hr_currency_type` VALUES (115, 'PGK', 'Papua New Guinea Kina');
INSERT INTO `hs_hr_currency_type` VALUES (116, 'PHP', 'Philippine Peso');
INSERT INTO `hs_hr_currency_type` VALUES (117, 'PKR', 'Pakistan Rupee');
INSERT INTO `hs_hr_currency_type` VALUES (118, 'PLN', 'Polish Zloty');
INSERT INTO `hs_hr_currency_type` VALUES (120, 'PYG', 'Paraguay Guarani');
INSERT INTO `hs_hr_currency_type` VALUES (121, 'QAR', 'Qatari Rial');
INSERT INTO `hs_hr_currency_type` VALUES (122, 'ROL', 'Romanian Leu');
INSERT INTO `hs_hr_currency_type` VALUES (123, 'RUB', 'Russian Rouble');
INSERT INTO `hs_hr_currency_type` VALUES (180, 'RUR', 'Russia Rubles');
INSERT INTO `hs_hr_currency_type` VALUES (124, 'SAR', 'South African Rand');
INSERT INTO `hs_hr_currency_type` VALUES (125, 'SBD', 'Solomon Islands Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (126, 'SCR', 'Seychelles Rupee');
INSERT INTO `hs_hr_currency_type` VALUES (127, 'SDD', 'Sudanese Dinar');
INSERT INTO `hs_hr_currency_type` VALUES (128, 'SDP', 'Sudanese Pound');
INSERT INTO `hs_hr_currency_type` VALUES (129, 'SEK', 'Swedish Krona');
INSERT INTO `hs_hr_currency_type` VALUES (131, 'SGD', 'Singapore Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (132, 'SHP', 'St. Helena Pound');
INSERT INTO `hs_hr_currency_type` VALUES (130, 'SKK', 'Slovak Koruna');
INSERT INTO `hs_hr_currency_type` VALUES (135, 'SLL', 'Sierra Leone Leone');
INSERT INTO `hs_hr_currency_type` VALUES (136, 'SOS', 'Somali Shilling');
INSERT INTO `hs_hr_currency_type` VALUES (137, 'SRG', 'Suriname Guilder');
INSERT INTO `hs_hr_currency_type` VALUES (138, 'STD', 'Sao Tome/Principe Dobra');
INSERT INTO `hs_hr_currency_type` VALUES (139, 'SVC', 'El Salvador Colon');
INSERT INTO `hs_hr_currency_type` VALUES (140, 'SYP', 'Syrian Pound');
INSERT INTO `hs_hr_currency_type` VALUES (141, 'SZL', 'Swaziland Lilangeni');
INSERT INTO `hs_hr_currency_type` VALUES (142, 'THB', 'Thai Baht');
INSERT INTO `hs_hr_currency_type` VALUES (143, 'TND', 'Tunisian Dinar');
INSERT INTO `hs_hr_currency_type` VALUES (144, 'TOP', 'Tongan Pa''anga');
INSERT INTO `hs_hr_currency_type` VALUES (145, 'TRL', 'Turkish Lira');
INSERT INTO `hs_hr_currency_type` VALUES (146, 'TTD', 'Trinidad/Tobago Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (147, 'TWD', 'Taiwan Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (148, 'TZS', 'Tanzanian Shilling');
INSERT INTO `hs_hr_currency_type` VALUES (149, 'UAH', 'Ukraine Hryvnia');
INSERT INTO `hs_hr_currency_type` VALUES (150, 'UGX', 'Uganda Shilling');
INSERT INTO `hs_hr_currency_type` VALUES (151, 'USD', 'United States Dollar');
INSERT INTO `hs_hr_currency_type` VALUES (152, 'UYP', 'Uruguayan Peso');
INSERT INTO `hs_hr_currency_type` VALUES (153, 'VEB', 'Venezuelan Bolivar');
INSERT INTO `hs_hr_currency_type` VALUES (154, 'VND', 'Vietnamese Dong');
INSERT INTO `hs_hr_currency_type` VALUES (155, 'VUV', 'Vanuatu Vatu');
INSERT INTO `hs_hr_currency_type` VALUES (156, 'WST', 'Samoan Tala');
INSERT INTO `hs_hr_currency_type` VALUES (158, 'XAF', 'CFA Franc BEAC');
INSERT INTO `hs_hr_currency_type` VALUES (159, 'XAG', 'Silver (oz.)');
INSERT INTO `hs_hr_currency_type` VALUES (160, 'XAU', 'Gold (oz.)');
INSERT INTO `hs_hr_currency_type` VALUES (161, 'XCD', 'Eastern Caribbean Dollars');
INSERT INTO `hs_hr_currency_type` VALUES (179, 'XDR', 'IMF Special Drawing Right');
INSERT INTO `hs_hr_currency_type` VALUES (162, 'XOF', 'CFA Franc BCEAO');
INSERT INTO `hs_hr_currency_type` VALUES (163, 'XPD', 'Palladium (oz.)');
INSERT INTO `hs_hr_currency_type` VALUES (164, 'XPF', 'Franc des Comptoirs français du Pacifique');
INSERT INTO `hs_hr_currency_type` VALUES (165, 'XPT', 'Platinum (oz.)');
INSERT INTO `hs_hr_currency_type` VALUES (166, 'YER', 'Yemeni Riyal');
INSERT INTO `hs_hr_currency_type` VALUES (167, 'YUM', 'Yugoslavian Dinar');
INSERT INTO `hs_hr_currency_type` VALUES (175, 'YUN', 'Yugoslav Dinar');
INSERT INTO `hs_hr_currency_type` VALUES (168, 'ZAR', 'South African Rand');
INSERT INTO `hs_hr_currency_type` VALUES (176, 'ZMK', 'Zambian Kwacha');
INSERT INTO `hs_hr_currency_type` VALUES (169, 'ZRN', 'New Zaire');
INSERT INTO `hs_hr_currency_type` VALUES (170, 'ZWD', 'Zimbabwe Dollar');



INSERT INTO `hs_hr_province` VALUES (1, 'Alaska', 'AK', 'US');
INSERT INTO `hs_hr_province` VALUES (2, 'Alabama', 'AL', 'US');
INSERT INTO `hs_hr_province` VALUES (3, 'American Samoa', 'AS', 'US');
INSERT INTO `hs_hr_province` VALUES (4, 'Arizona', 'AZ', 'US');
INSERT INTO `hs_hr_province` VALUES (5, 'Arkansas', 'AR', 'US');
INSERT INTO `hs_hr_province` VALUES (6, 'California', 'CA', 'US');
INSERT INTO `hs_hr_province` VALUES (7, 'Colorado', 'CO', 'US');
INSERT INTO `hs_hr_province` VALUES (8, 'Connecticut', 'CT', 'US');
INSERT INTO `hs_hr_province` VALUES (9, 'Delaware', 'DE', 'US');
INSERT INTO `hs_hr_province` VALUES (10, 'District of Columbia', 'DC', 'US');
INSERT INTO `hs_hr_province` VALUES (11, 'Federated States of Micronesia', 'FM', 'US');
INSERT INTO `hs_hr_province` VALUES (12, 'Florida', 'FL', 'US');
INSERT INTO `hs_hr_province` VALUES (13, 'Georgia', 'GA', 'US');
INSERT INTO `hs_hr_province` VALUES (14, 'Guam', 'GU', 'US');
INSERT INTO `hs_hr_province` VALUES (15, 'Hawaii', 'HI', 'US');
INSERT INTO `hs_hr_province` VALUES (16, 'Idaho', 'ID', 'US');
INSERT INTO `hs_hr_province` VALUES (17, 'Illinois', 'IL', 'US');
INSERT INTO `hs_hr_province` VALUES (18, 'Indiana', 'IN', 'US');
INSERT INTO `hs_hr_province` VALUES (19, 'Iowa', 'IA', 'US');
INSERT INTO `hs_hr_province` VALUES (20, 'Kansas', 'KS', 'US');
INSERT INTO `hs_hr_province` VALUES (21, 'Kentucky', 'KY', 'US');
INSERT INTO `hs_hr_province` VALUES (22, 'Louisiana', 'LA', 'US');
INSERT INTO `hs_hr_province` VALUES (23, 'Maine', 'ME', 'US');
INSERT INTO `hs_hr_province` VALUES (24, 'Marshall Islands', 'MH', 'US');
INSERT INTO `hs_hr_province` VALUES (25, 'Maryland', 'MD', 'US');
INSERT INTO `hs_hr_province` VALUES (26, 'Massachusetts', 'MA', 'US');
INSERT INTO `hs_hr_province` VALUES (27, 'Michigan', 'MI', 'US');
INSERT INTO `hs_hr_province` VALUES (28, 'Minnesota', 'MN', 'US');
INSERT INTO `hs_hr_province` VALUES (29, 'Mississippi', 'MS', 'US');
INSERT INTO `hs_hr_province` VALUES (30, 'Missouri', 'MO', 'US');
INSERT INTO `hs_hr_province` VALUES (31, 'Montana', 'MT', 'US');
INSERT INTO `hs_hr_province` VALUES (32, 'Nebraska', 'NE', 'US');
INSERT INTO `hs_hr_province` VALUES (33, 'Nevada', 'NV', 'US');
INSERT INTO `hs_hr_province` VALUES (34, 'New Hampshire', 'NH', 'US');
INSERT INTO `hs_hr_province` VALUES (35, 'New Jersey', 'NJ', 'US');
INSERT INTO `hs_hr_province` VALUES (36, 'New Mexico', 'NM', 'US');
INSERT INTO `hs_hr_province` VALUES (37, 'New York', 'NY', 'US');
INSERT INTO `hs_hr_province` VALUES (38, 'North Carolina', 'NC', 'US');
INSERT INTO `hs_hr_province` VALUES (39, 'North Dakota', 'ND', 'US');
INSERT INTO `hs_hr_province` VALUES (40, 'Northern Mariana Islands', 'MP', 'US');
INSERT INTO `hs_hr_province` VALUES (41, 'Ohio', 'OH', 'US');
INSERT INTO `hs_hr_province` VALUES (42, 'Oklahoma', 'OK', 'US');
INSERT INTO `hs_hr_province` VALUES (43, 'Oregon', 'OR', 'US');
INSERT INTO `hs_hr_province` VALUES (44, 'Palau', 'PW', 'US');
INSERT INTO `hs_hr_province` VALUES (45, 'Pennsylvania', 'PA', 'US');
INSERT INTO `hs_hr_province` VALUES (46, 'Puerto Rico', 'PR', 'US');
INSERT INTO `hs_hr_province` VALUES (47, 'Rhode Island', 'RI', 'US');
INSERT INTO `hs_hr_province` VALUES (48, 'South Carolina', 'SC', 'US');
INSERT INTO `hs_hr_province` VALUES (49, 'South Dakota', 'SD', 'US');
INSERT INTO `hs_hr_province` VALUES (50, 'Tennessee', 'TN', 'US');
INSERT INTO `hs_hr_province` VALUES (51, 'Texas', 'TX', 'US');
INSERT INTO `hs_hr_province` VALUES (52, 'Utah', 'UT', 'US');
INSERT INTO `hs_hr_province` VALUES (53, 'Vermont', 'VT', 'US');
INSERT INTO `hs_hr_province` VALUES (54, 'Virgin Islands', 'VI', 'US');
INSERT INTO `hs_hr_province` VALUES (55, 'Virginia', 'VA', 'US');
INSERT INTO `hs_hr_province` VALUES (56, 'Washington', 'WA', 'US');
INSERT INTO `hs_hr_province` VALUES (57, 'West Virginia', 'WV', 'US');
INSERT INTO `hs_hr_province` VALUES (58, 'Wisconsin', 'WI', 'US');
INSERT INTO `hs_hr_province` VALUES (59, 'Wyoming', 'WY', 'US');
INSERT INTO `hs_hr_province` VALUES (60, 'Armed Forces Africa', 'AE', 'US');
INSERT INTO `hs_hr_province` VALUES (61, 'Armed Forces Americas (except Canada)', 'AA', 'US');
INSERT INTO `hs_hr_province` VALUES (62, 'Armed Forces Canada', 'AE', 'US');
INSERT INTO `hs_hr_province` VALUES (63, 'Armed Forces Europe', 'AE', 'US');
INSERT INTO `hs_hr_province` VALUES (64, 'Armed Forces Middle East', 'AE', 'US');
INSERT INTO `hs_hr_province` VALUES (65, 'Armed Forces Pacific', 'AP', 'US');

INSERT INTO `hs_hr_eec` VALUES ('EEC001', 'OFFICIALS AND ADMINISTRATORS');
INSERT INTO `hs_hr_eec` VALUES ('EEC002', 'PROFESSIONALS');
INSERT INTO `hs_hr_eec` VALUES ('EEC003', 'TECHNICIANS');
INSERT INTO `hs_hr_eec` VALUES ('EEC004', 'PROTECTIVE SERVICE WORKERS');
INSERT INTO `hs_hr_eec` VALUES ('EEC005', 'PARAPROFESSIONALS');
INSERT INTO `hs_hr_eec` VALUES ('EEC006', 'ADMINISTRATIVE SUPPORT');
INSERT INTO `hs_hr_eec` VALUES ('EEC007', 'SKILLED CRAFT WORKERS');
INSERT INTO `hs_hr_eec` VALUES ('EEC008', 'SERVICE-MAINTENANCE');

INSERT INTO `hs_hr_empstat` VALUES ('EST001', 'Full Time Contract');
INSERT INTO `hs_hr_empstat` VALUES ('EST002', 'Full Time Internship');
INSERT INTO `hs_hr_empstat` VALUES ('EST003', 'Full Time Permanent');
INSERT INTO `hs_hr_empstat` VALUES ('EST004', 'Part Time Contract');
INSERT INTO `hs_hr_empstat` VALUES ('EST005', 'Part Time Internship');
INSERT INTO `hs_hr_empstat` VALUES ('EST006', 'Part Time Permanent');

INSERT INTO `hs_hr_geninfo` VALUES ('001','','');
INSERT INTO `hs_hr_user_group` VALUES ('USG001','Admin','1');
INSERT INTO `hs_hr_db_version` VALUES ('DVR001','mysql4.1','initial DB','2005-10-10 00:00:00','2005-12-20 00:00:00',null,null);
INSERT INTO `hs_hr_file_version` VALUES ('FVR001',NULL,'Release 1','2006-03-15 00:00:00','2006-03-15 00:00:00',null,null,'file_ver_01');
INSERT INTO `hs_hr_versions` VALUES ('VER001','Release 1','2006-03-15 00:00:00','2006-03-15 00:00:00',null,null,0,'DVR001','FVR001','version 1.0');
INSERT INTO `hs_hr_module` VALUES ('MOD001','Admin','Koshika','koshika@beyondm.net','VER001','HR Admin'),
								  ('MOD002','PIM','Koshika','koshika@beyondm.net','VER001','HR Functions'),
								  ('MOD004','Report','Koshika','koshika@beyondm.net','VER001','Reporting'),
								  ('MOD005', 'Leave', 'Mohanjith', 'mohanjith@beyondm.net', 'VER001', 'Leave Tracking'),
								  ('MOD006', 'Time', 'Mohanjith', 'mohanjith@orangehrm.com', 'VER001', 'Time Tracking'),
								  ('MOD007', 'Benefits', 'Gayanath', 'mohanjith@orangehrm.com', 'VER001', 'Benefits Tracking'),
								  ('MOD008', 'Recruitment', 'OrangeHRM', 'info@orangehrm.com', 'VER001', 'Recruitment');
INSERT INTO `hs_hr_rights` ( `userg_id` , `mod_id` , `addition` , `editing` , `deletion` , `viewing` )
VALUES  ('USG001', 'MOD001', '1', '1', '1', '1'),
		('USG001', 'MOD002', '1', '1', '1', '1'),
		('USG001', 'MOD004', '1', '1', '1', '1'),
		('USG001', 'MOD005', '1', '1', '1', '1'),
		('USG001', 'MOD006', '1', '1', '1', '1'),
		('USG001', 'MOD007', '1', '1', '1', '1'),
		('USG001', 'MOD008', '1', '1', '1', '1');
INSERT INTO `hs_hr_compstructtree`(`title`, `description`, `loc_code`, `lft`, `rgt`, `id`, `parnt`, `dept_id`) VALUES ('', 'Parent Company', null , 1, 2, 1, 0, null);
INSERT INTO `hs_hr_users` VALUES ('USR001','demo','fe01ce2a7fbac8fafaed7c982a04e229','Admin','',null,'','Yes','1','','0000-00-00 00:00:00','0000-00-00 00:00:00',null,null,'','','','','','','','','','Enabled','','','','','','',0,'','USG001');

INSERT INTO `hs_hr_leavetype` VALUES ('LTY001', 'Casual', 1);
INSERT INTO `hs_hr_leavetype` VALUES ('LTY002', 'Medical', 1);

INSERT INTO `hs_hr_weekends` VALUES (1, 0);
INSERT INTO `hs_hr_weekends` VALUES (2, 0);
INSERT INTO `hs_hr_weekends` VALUES (3, 0);
INSERT INTO `hs_hr_weekends` VALUES (4, 0);
INSERT INTO `hs_hr_weekends` VALUES (5, 0);
INSERT INTO `hs_hr_weekends` VALUES (6, 8);
INSERT INTO `hs_hr_weekends` VALUES (7, 8);

INSERT INTO `hs_hr_timesheet_submission_period` VALUES (1, 'week', 7, 1, 0, 6, 'Weekly');

INSERT INTO `hs_hr_empstat`
  (`estat_code`, `estat_name`)
  VALUES ('EST000', 'Terminated');

INSERT INTO `hs_hr_payperiod`(payperiod_code, payperiod_name) VALUES(1, 'Weekly');
INSERT INTO `hs_hr_payperiod`(payperiod_code, payperiod_name) VALUES(2, 'Bi Weekly');
INSERT INTO `hs_hr_payperiod`(payperiod_code, payperiod_name) VALUES(3, 'Semi Monthly');
INSERT INTO `hs_hr_payperiod`(payperiod_code, payperiod_name) VALUES(4, 'Monthly');
INSERT INTO `hs_hr_payperiod`(payperiod_code, payperiod_name) VALUES(5, 'Monthly on first pay of month.');
INSERT INTO `hs_hr_payperiod`(payperiod_code, payperiod_name) VALUES(6, 'Hourly');

INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_nationality', 'nat_code');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_language', 'lang_code');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_customer', 'customer_id');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_job_title', 'jobtit_code');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(6, 'hs_hr_empstat', 'estat_code');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(8, 'hs_hr_eec', 'eec_code');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_licenses', 'licenses_code');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_employee', 'emp_number');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_location', 'loc_code');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_membership', 'membship_code');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_membership_type', 'membtype_code');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(6, 'hs_hr_module', 'mod_id');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_education', 'edu_code');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_ethnic_race', 'ethnic_race_code');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_skill', 'skill_code');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(1, 'hs_hr_user_group', 'userg_id');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(1, 'hs_hr_users', 'id');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_pr_salary_grade', 'sal_grd_code');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_empreport', 'rep_code');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_leave', 'leave_id');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(2, 'hs_hr_leavetype', 'leave_type_id');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_holidays', 'holiday_id');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_project', 'project_id');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_timesheet', 'timesheet_id');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_timesheet_submission_period', 'timesheet_period_id');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_time_event', 'time_event_id');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(1, 'hs_hr_compstructtree', 'id');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_leave_requests', 'leave_request_id');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_project_activity', 'activity_id');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_workshift', 'workshift_id');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_custom_export', 'export_id');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_custom_import', 'import_id');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_pay_period', 'id');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_hsp_summary', 'summary_id');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_hsp_payment_request', 'id');

INSERT INTO `hs_hr_config`(`key`, `value`) VALUES('ldap_server', '');
INSERT INTO `hs_hr_config`(`key`, `value`) VALUES('ldap_domain_name', '');
INSERT INTO `hs_hr_config`(`key`, `value`) VALUES('ldap_port', '');
INSERT INTO `hs_hr_config`(`key`, `value`) VALUES('ldap_status', '');
INSERT INTO `hs_hr_config`(`key`, `value`) VALUES('hsp_current_plan', '0');
INSERT INTO `hs_hr_config`(`key`, `value`) VALUES('hsp_accrued_last_updated', '0000-00-00');
INSERT INTO `hs_hr_config`(`key`, `value`) VALUES('hsp_used_last_updated', '0000-00-00');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_job_spec', 'jobspec_id');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_job_vacancy', 'vacancy_id');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_job_application', 'application_id');
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name) VALUES(0, 'hs_hr_job_application_events', 'id');
