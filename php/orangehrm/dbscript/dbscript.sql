create database hr_mysql;

use hr_mysql;

create table `hs_hr_bank` (
  `bank_code` varchar(8) not null default '',
  `bank_name` varchar(100) default null,
  `bank_address` varchar(200) default null,
  `bank_auto_clr_house_code` varchar(20) default null,
  primary key  (`bank_code`)
);


create table `hs_hr_branch` (
  `bbranch_code` varchar(6) not null default '',
  `bank_code` varchar(8) default null,
  `bbranch_name` varchar(50) default null,
  `bbranch_address` varchar(200) default null,
  `bbranch_sliptransfers_flg` decimal(1,0) default null,
  `bbranch_auto_clr_house_code` varchar(20) default null,
  primary key  (`bbranch_code`)
);


create table `hs_hr_bugs` (
  `id` varchar(36) not null default '',
  `number` int(11) not null default '0',
  `date_entered` datetime default '0000-00-00 00:00:00',
  `date_modified` datetime default '0000-00-00 00:00:00',
  `modified_user_id` varchar(36) default null,
  `assigned_developer_id` varchar(36) default null,
  `deleted` tinyint(1) default '0',
  `name` varchar(255) default null,
  `status` varchar(25) default null,
  `priority` varchar(25) default null,
  `description` text,
  `created_by` varchar(36) default null,
  `resolution` varchar(255) default null,
  `found_in_release` varchar(36) default null,
  `type` varchar(255) default null,
  `fixed_in_release` varchar(36) default null,
  `work_log` text,
  `source` varchar(255) default null,
  `module` varchar(255) default null,
  primary key  (`id`)
) ;



create table `hs_hr_cash_ben_salgrade` (
  `ben_code` varchar(6) not null default '',
  `sal_grd_code` varchar(6) not null default '',
  `bensalgrd_amount` float default null,
  primary key  (`ben_code`,`sal_grd_code`)
);


create table `hs_hr_cash_benefit` (
  `ben_code` varchar(6) not null default '',
  `ben_name` varchar(120) default null,
  `ben_amount` float default null,
  primary key  (`ben_code`)
);



create table `hs_hr_category` (
  `cat_code` varchar(6) not null default '',
  `cat_name` varchar(50) default null,
  primary key  (`cat_code`)
);


create table `hs_hr_company_hierarchy` (
  `hie_code` varchar(6) not null default '',
  `hie_name` varchar(70) default null,
  `hie_relationship` varchar(6) default null,
  `emp_number` varchar(6) default null,
  `def_level` int(11) default null,
  `hie_telephone` varchar(30) default null,
  `hie_fax` varchar(20) default null,
  `hie_email` varchar(50) default null,
  `hie_url` varchar(200) default null,
  `hie_lo` varchar(100) default null,
  `loc_code` varchar(6) default null,
  primary key  (`hie_code`)
);



create table `hs_hr_company_hierarchy_def` (
  `def_level` int(11) not null default '0',
  `def_name` varchar(70) not null default '',
  primary key  (`def_level`)
);



create table `hs_hr_corporate_title` (
  `ct_code` varchar(6) not null default '',
  `ct_name` varchar(120) default null,
  `ct_toplev_flg` smallint(6) default null,
  `ct_heads_flg` smallint(6) default null,
  `ct_next_upgrade` varchar(6) default null,
  `ct_heads_count` decimal(10,0) default null,
  `sal_grd_code` varchar(6) default null,
  primary key  (`ct_code`)
);


create table `hs_hr_country` (
  `cou_code` varchar(6) not null default '',
  `cou_name` varchar(50) default null,
  primary key  (`cou_code`)
);


create table `hs_hr_currency_type` (
  `currency_id` varchar(6) not null default '',
  `currency_name` varchar(20) default null,
  primary key  (`currency_id`)
);


create table `hs_hr_db_version` (
  `id` varchar(36) not null default '',
  `name` varchar(45) default null,
  `description` varchar(100) default null,
  `entered_date` datetime default '0000-00-00 00:00:00',
  `modified_date` datetime default '0000-00-00 00:00:00',
  `entered_by` varchar(36) default null,
  `modified_by` varchar(36) default null,
  primary key  (`id`)
) ;


create table `hs_hr_designation` (
  `dsg_code` varchar(6) not null default '',
  `dsg_name` varchar(120) default null,
  `ct_code` varchar(6) default null,
  `dsg_snrmgt_flg` smallint(6) default null,
  `dsg_review_date` datetime default null,
  `dsg_next_upgrade` varchar(6) default null,
  primary key  (`dsg_code`)
);



create table `hs_hr_developer` (
  `id` varchar(36) not null default '',
  `first_name` varchar(45) default null,
  `last_name` varchar(45) default null,
  `reports_to_id` varchar(45) default null,
  `description` varchar(200) default null,
  `department` varchar(45) default null,
  primary key  (`id`)
) ;


create table `hs_hr_district` (
  `district_code` varchar(6) not null default '',
  `district_name` varchar(50) default null,
  `province_code` varchar(6) default null,
  primary key  (`district_code`)
);



create table `hs_hr_dwelling_type` (
  `dwelling_code` varchar(6) not null default '',
  `dwelling_name` varchar(50) default null,
  primary key  (`dwelling_code`)
);



create table `hs_hr_electorate` (
  `electorate_code` varchar(6) not null default '',
  `electorate_name` varchar(50) default null,
  primary key  (`electorate_code`)
);


create table `hs_hr_emp_attachment` (
  `emp_number` varchar(6) not null default '',
  `eattach_id` decimal(10,0) not null default '0',
  `eattach_desc` varchar(200) default null,
  `eattach_filename` varchar(100) default null,
  `eattach_size` int(11) default '0',
  `eattach_attachment` mediumblob,
  `eattach_type` varchar(50) default null,
  primary key  (`emp_number`,`eattach_id`)
);


create table `hs_hr_emp_bank` (
  `bbranch_code` varchar(6) not null default '',
  `emp_number` varchar(6) not null default '',
  `ebank_acc_no` varchar(80) default null,
  `ebank_acc_type_flg` smallint(6) default null,
  `ebank_amount` decimal(15,2) default null,
  `ebank_order` decimal(10,0) default null,
  primary key  (`emp_number`,`bbranch_code`)
);


create table `hs_hr_emp_basicsalary` (
  `emp_number` varchar(6) not null default '',
  `sal_grd_code` varchar(6) not null default '',
  `currency_id` varchar(6) not null default '',
  `ebsal_basic_salary` float default null,
  primary key  (`emp_number`,`sal_grd_code`,`currency_id`)
);


create table `hs_hr_emp_cash_ben_remove` (
  `emp_number` varchar(6) not null default '',
  `ben_code` varchar(6) not null default '',
  primary key  (`emp_number`,`ben_code`)
);


create table `hs_hr_emp_cash_benefit` (
  `emp_number` varchar(6) not null default '',
  `ben_code` varchar(6) not null default '',
  `eben_amount` float default null,
  `eben_date_assigned` datetime default null,
  `eben_filter_type` smallint(6) default null,
  primary key  (`emp_number`,`ben_code`)
);


create table `hs_hr_emp_contract_extend` (
  `emp_number` varchar(6) not null default '',
  `econ_extend_id` decimal(10,0) not null default '0',
  `econ_extend_start_date` datetime default null,
  `econ_extend_end_date` datetime default null,
  primary key  (`emp_number`,`econ_extend_id`)
);



create table `hs_hr_emp_extra_activity` (
  `emp_number` varchar(6) not null default '',
  `eextact_seqno` decimal(10,0) not null default '0',
  `eacat_code` varchar(6) default null,
  `eatype_code` varchar(6) default null,
  `eextact_achievement` varchar(225) default null,
  primary key  (`emp_number`,`eextact_seqno`)
);


create table `hs_hr_emp_jobspec` (
  `emp_number` varchar(6) not null default '',
  `jdcat_code` varchar(6) not null default '',
  `ejobspec_attributes` varchar(225) default null,
  primary key  (`emp_number`,`jdcat_code`)
);


create table `hs_hr_emp_language` (
  `emp_number` varchar(6) not null default '',
  `lang_code` varchar(6) not null default '',
  `elang_type` smallint(6) default '0',
  `rating_code` varchar(6) default null,
  `rating_grade_code` varchar(6) default null,
  primary key  (`emp_number`,`lang_code`,`elang_type`)
);


create table `hs_hr_emp_member_detail` (
  `emp_number` varchar(6) not null default '',
  `membship_code` varchar(6) not null default '',
  `membtype_code` varchar(6) not null default '',
  `ememb_subscript_ownership` varchar(20) default null,
  `ememb_subscript_amount` decimal(15,2) default null,
  `ememb_commence_date` datetime default null,
  `ememb_renewal_date` datetime default null,
  primary key  (`emp_number`,`membship_code`,`membtype_code`)
);



create table `hs_hr_emp_noncash_ben_remove` (
  `emp_number` varchar(6) not null default '',
  `nben_code` varchar(6) not null default '',
  primary key  (`emp_number`,`nben_code`)
);



create table `hs_hr_emp_noncash_benefit` (
  `emp_number` varchar(6) not null default '',
  `nben_code` varchar(6) not null default '',
  `enben_issue_date` datetime default null,
  `enben_quantity` float default null,
  `enben_comments` varchar(100) default null,
  `enben_item_returnable_flg` smallint(6) default null,
  `enben_item_returned_date` datetime default null,
  `enben_item_returned_flg` smallint(6) default null,
  `enben_filter_type` smallint(6) default null,
  primary key  (`emp_number`,`nben_code`)
);



create table `hs_hr_emp_passport` (
  `emp_number` varchar(6) not null default '',
  `ep_seqno` decimal(10,0) not null default '0',
  `ep_passportnumber` varchar(20) default null,
  `ep_passportissueddate` datetime default null,
  `ep_placepassportissued` varchar(30) default null,
  `ep_passportexpiredate` datetime default null,
  `ep_comments` varchar(255) default null,
  `ep_visa_type` varchar(20) default null,
  `ep_passport_type_flg` smallint(6) default null,
  `cou_code` varchar(6) default null,
  `ep_no_of_entries` smallint(6) default null,
  primary key  (`emp_number`,`ep_seqno`)
);



create table `hs_hr_emp_picture` (
  `emp_number` varchar(6) not null default '',
  `epic_picture` blob,
  `epic_filename` varchar(100) default null,
  `epic_type` varchar(50) default null,
  `epic_file_size` varchar(20) default null,
  primary key  (`emp_number`)
);



create table `hs_hr_emp_qualification` (
  `qualifi_code` varchar(6) not null default '',
  `emp_number` varchar(6) not null default '',
  `equalifi_institute` varchar(50) default null,
  `equalifi_year` decimal(4,0) default null,
  `equalifi_status` varchar(20) default null,
  `equalifi_comments` varchar(200) default null,
  primary key  (`qualifi_code`,`emp_number`)
);



create table `hs_hr_emp_reportto` (
  `erep_sup_emp_number` varchar(6) not null default '',
  `erep_sub_emp_number` varchar(6) not null default '',
  `erep_reporting_mode` smallint(6) not null default '0',
  primary key  (`erep_sup_emp_number`,`erep_sub_emp_number`,`erep_reporting_mode`)
);



create table `hs_hr_emp_subject` (
  `emp_number` varchar(6) not null default '',
  `qualifi_code` varchar(6) not null default '',
  `sbj_code` varchar(6) not null default '',
  `esbj_marks` smallint(6) default null,
  `esbj_year` decimal(4,0) default null,
  `esbj_comments` varchar(200) default null,
  `rating_grade_code` varchar(6) default null,
  primary key  (`emp_number`,`qualifi_code`,`sbj_code`)
);




create table `hs_hr_emp_work_experience` (
  `emp_number` varchar(6) not null default '',
  `eexp_seqno` decimal(10,0) not null default '0',
  `eexp_company` varchar(100) default null,
  `eexp_address1` varchar(50) default null,
  `eexp_address2` varchar(50) default null,
  `eexp_address3` varchar(50) default null,
  `eexp_desig_on_leave` varchar(120) default null,
  `eexp_work_related_flg` smallint(6) default null,
  `eexp_from_date` datetime default null,
  `eexp_to_date` datetime default null,
  `eexp_years` decimal(10,0) default null,
  `eexp_months` smallint(6) default null,
  `eexp_reason_for_leave` varchar(100) default null,
  `eexp_contact_person` varchar(50) default null,
  `eexp_telephone` varchar(20) default null,
  `eexp_email` varchar(50) default null,
  `eexp_accountabilities` varchar(200) default null,
  `eexp_achievements` varchar(200) default null,
  primary key  (`emp_number`,`eexp_seqno`)
);




create table `hs_hr_employee` (
  `emp_number` varchar(6) not null default '',
  `emp_calling_name` varchar(50) default null,
  `emp_middle_ini` varchar(50) default null,
  `emp_surname` varchar(50) default null,
  `emp_title` varchar(10) default null,
  `emp_maiden_name` varchar(70) default null,
  `emp_names_by_ini` varchar(200) default null,
  `emp_fullname` varchar(200) default null,
  `emp_other_names` varchar(200) default null,
  `emp_tax_country` varchar(6) default '',
  `emp_nic_no` varchar(20) default null,
  `emp_nic_date` datetime default null,
  `emp_birthday` datetime default null,
  `emp_birthplace` varchar(100) default null,
  `emp_gender` smallint(6) default null,
  `emp_blood_group` varchar(4) default null,
  `nat_code` varchar(6) default null,
  `rlg_code` varchar(6) default null,
  `emp_marital_status` varchar(20) default null,
  `emp_married_date` datetime default null,
  `emp_date_joined` datetime default null,
  `emp_confirm_flg` smallint(6) default null,
  `emp_retire_date` datetime default null,
  `ct_code` varchar(6) default null,
  `dsg_code` varchar(6) default null,
  `emp_workhours` double default null,
  `emp_job_preference` varchar(200) default null,
  `centre_code` varchar(6) default null,
  `emp_type` varchar(50) default null,
  `emp_contarct_start_date` datetime default null,
  `cat_code` varchar(6) default null,
  `emp_contract_end_date` datetime default null,
  `emp_cont_to_perm_flg` smallint(6) default null,
  `emp_cont_to_perm_date` datetime default null,
  `emp_active_hrm_flg` smallint(6) default null,
  `emp_active_payroll_flg` smallint(6) default null,
  `emp_active_att_flg` smallint(6) default null,
  `emp_payrollno` varchar(20) default null,
  `emp_barcodeno` varchar(6) default null,
  `emp_paytype_flg` smallint(6) default null,
  `loc_code` varchar(6) default null,
  `emp_taxontax_flg` smallint(6) default null,
  `emp_tax_id_number` varchar(20) default null,
  `emp_epf_eligible_flg` smallint(6) default null,
  `emp_epf_number` varchar(6) default null,
  `emp_paye_tax_exempt` varchar(20) default null,
  `emp_epf_employee_amount` decimal(15,2) default null,
  `emp_epf_payment_type_flg` smallint(6) default null,
  `emp_epf_employer_amount` decimal(15,2) default null,
  `emp_etf_eligible_flg` smallint(6) default null,
  `emp_etf_number` varchar(20) default null,
  `emp_etf_employee_amount` decimal(15,2) default null,
  `emp_etf_date` datetime default null,
  `emp_msps_eligible_flg` smallint(6) default null,
  `emp_msps_employee_amount` decimal(15,2) default null,
  `emp_msps_employer_amount` decimal(15,2) default null,
  `emp_per_address1` varchar(50) default null,
  `emp_per_address2` varchar(50) default null,
  `emp_per_address3` varchar(50) default null,
  `emp_per_city` varchar(30) default null,
  `emp_per_postalcode` varchar(20) default null,
  `emp_per_telephone` varchar(30) default null,
  `emp_per_mobile` varchar(20) default null,
  `emp_per_fax` varchar(20) default null,
  `emp_per_email` varchar(50) default null,
  `emp_per_cou_code` varchar(6) default null,
  `emp_per_province_code` varchar(6) default null,
  `emp_per_district_code` varchar(6) default null,
  `emp_tem_address1` varchar(50) default null,
  `emp_tem_address2` varchar(50) default null,
  `emp_per_electorate_code` varchar(6) default null,
  `emp_tem_address3` varchar(50) default null,
  `emp_tem_city` varchar(30) default null,
  `emp_tem_postalcode` varchar(20) default null,
  `emp_tem_telephone` varchar(30) default null,
  `emp_tem_mobile` varchar(20) default null,
  `emp_tem_fax` varchar(20) default null,
  `emp_tem_email` varchar(50) default null,
  `emp_office_phone` varchar(20) default null,
  `emp_office_extn` varchar(10) default null,
  `emp_tem_province_code` varchar(6) default null,
  `emp_tem_cou_code` varchar(6) default null,
  `emp_office_email` varchar(50) default null,
  `emp_tem_electorate_code` varchar(6) default null,
  `emp_tem_district_code` varchar(6) default null,
  `staffcat_code` varchar(6) default null,
  `emp_resign_date` datetime default null,
  `sal_grd_code` varchar(6) default null,
  `emp_pref_work_station` varchar(6) default null,
  primary key  (`emp_number`)
);



create table `hs_hr_emptype` (
  `empt_type_code` varchar(6) not null default '',
  `empt_typedescription` varchar(50) default null,
  `empt_isdatelimited_flg` int(11) default null,
  `empt_typeprefix` char(2) default null,
  primary key  (`empt_type_code`)
);



create table `hs_hr_extra_activity_catery` (
  `eacat_code` varchar(6) not null default '',
  `eacat_name` varchar(120) default null,
  primary key  (`eacat_code`)
);



create table `hs_hr_extra_activity_type` (
  `eatype_code` varchar(6) not null default '',
  `eatype_name` varchar(120) default null,
  `eacat_code` varchar(6) default null,
  primary key  (`eatype_code`)
);




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
) ;




create table `hs_hr_group` (
  `gp_code` varchar(6) not null default '',
  `gp_name` varchar(50) default null,
  primary key  (`gp_code`)
);



create table `hs_hr_jd_catery` (
  `jdcat_code` varchar(6) not null default '',
  `jdcat_name` varchar(100) default null,
  primary key  (`jdcat_code`)
);




create table `hs_hr_jd_key_result_area` (
  `jdkra_code` varchar(6) not null default '',
  `jdkra_name` varchar(255) default null,
  `jdkra_brief_desc` varchar(100) default null,
  `jdtype_code` varchar(6) default null,
  `skill_code` varchar(6) default null,
  primary key  (`jdkra_code`)
);



create table `hs_hr_jd_kpi` (
  `jdkra_code` varchar(6) not null default '',
  `dsg_code` varchar(6) not null default '',
  `jdkpi_indicators` varchar(255) default null,
  primary key  (`jdkra_code`,`dsg_code`)
);



create table `hs_hr_jd_qualification` (
  `dsg_code` varchar(6) not null default '',
  `qualifi_code` varchar(6) not null default '',
  `jdqualifi_institute` varchar(100) default null,
  `jdqualifi_status` varchar(30) default null,
  primary key  (`dsg_code`,`qualifi_code`)
);


create table `hs_hr_jd_subject` (
  `dsg_code` varchar(6) not null default '',
  `sbj_code` varchar(6) not null default '',
  `qualifi_code` varchar(6) not null default '',
  `rating_code` varchar(6) default null,
  `rating_grade_code` varchar(6) default null,
  primary key  (`dsg_code`,`sbj_code`,`qualifi_code`)
);


create table `hs_hr_jd_type` (
  `jdtype_code` varchar(6) not null default '',
  `jdtype_name` varchar(100) default null,
  `jdcat_code` varchar(6) default null,
  primary key  (`jdtype_code`)
);


create table `hs_hr_jd_work_experience` (
  `dsg_code` varchar(6) not null default '',
  `jdexp_seqno` decimal(10,0) not null default '0',
  `jdexp_prev_designation_name` varchar(100) default null,
  `jdexp_workrel_service_years` decimal(3,0) default null,
  `jdexp_nonworkrel_service_years` decimal(3,0) default null,
  primary key  (`dsg_code`,`jdexp_seqno`)
);


create table `hs_hr_language` (
  `lang_code` varchar(6) not null default '',
  `lang_name` varchar(120) default null,
  `rating_code` varchar(6) default null,
  primary key  (`lang_code`)
);


create table `hs_hr_location` (
  `loc_code` varchar(6) not null default '',
  `loc_name` varchar(100) default null,
  primary key  (`loc_code`)
);


create table `hs_hr_membership` (
  `membship_code` varchar(6) not null default '',
  `membtype_code` varchar(6) default null,
  `membship_name` varchar(120) default null,
  primary key  (`membship_code`)
);


create table `hs_hr_membership_type` (
  `membtype_code` varchar(6) not null default '',
  `membtype_name` varchar(120) default null,
  primary key  (`membtype_code`)
);


create table `hs_hr_module` (
  `mod_id` varchar(36) not null default '',
  `name` varchar(45) default null,
  `owner` varchar(45) default null,
  `owner_email` varchar(100) default null,
  `version` varchar(36) default null,
  `description` text,
  primary key  (`mod_id`)
) ;


create table `hs_hr_nationality` (
  `nat_code` varchar(6) not null default '',
  `nat_name` varchar(120) default null,
  primary key  (`nat_code`)
);


create table `hs_hr_noncash_ben_salgrade` (
  `nben_code` varchar(6) not null default '',
  `sal_grd_code` varchar(6) not null default '',
  `nbensalgrd_amount` float default null,
  primary key  (`nben_code`,`sal_grd_code`)
);



create table `hs_hr_noncash_benefit` (
  `nben_code` varchar(6) not null default '',
  `nben_name` varchar(120) default null,
  `nben_item_returnable_flg` smallint(6) default null,
  primary key  (`nben_code`)
);



create table `hs_hr_province` (
  `province_code` varchar(6) not null default '',
  `province_name` varchar(50) default null,
  `cou_code` varchar(6) default null,
  primary key  (`province_code`)
);



create table `hs_hr_qualification` (
  `qualifi_code` varchar(6) not null default '',
  `qualifi_type_code` varchar(6) default null,
  `qualifi_name` varchar(100) default null,
  `rating_code` varchar(6) default null,
  `qualifi_level` smallint(6) default null,
  primary key  (`qualifi_code`)
);



create table `hs_hr_qualification_type` (
  `qualifi_type_code` varchar(6) not null default '',
  `qualifi_type_name` varchar(100) default null,
  primary key  (`qualifi_type_code`)
);



create table `hs_hr_rating_method` (
  `rating_code` varchar(6) not null default '',
  `rating_name` varchar(100) default null,
  primary key  (`rating_code`)
);


create table `hs_hr_rating_method_grade` (
  `rating_grade_code` varchar(6) not null default '',
  `rating_code` varchar(6) not null default '',
  `rating_grade` varchar(20) default null,
  `rating_grade_min_mark` decimal(10,0) default null,
  `rating_grade_max_mark` decimal(10,0) default null,
  `rating_grade_avg_mark` decimal(10,0) default null,
  primary key  (`rating_grade_code`,`rating_code`)
);


create table `hs_hr_religion` (
  `rlg_code` varchar(6) not null default '',
  `rlg_name` varchar(50) default null,
  primary key  (`rlg_code`)
);



create table `hs_hr_rights` (
  `userg_id` varchar(36) not null default '',
  `mod_id` varchar(36) not null default '',
  `addition` smallint(5) unsigned default '0',
  `editing` smallint(5) unsigned default '0',
  `deletion` smallint(5) unsigned default '0',
  `viewing` smallint(5) unsigned default '0',
  primary key  (`mod_id`,`userg_id`)
);



create table `hs_hr_route` (
  `rt_id` varchar(6) not null default '',
  `rt_name` varchar(100) default null,
  primary key  (`rt_id`)
);



create table `hs_hr_skill` (
  `skill_code` varchar(6) not null default '',
  `skill_name` varchar(120) default null,
  primary key  (`skill_code`)
);



create table `hs_hr_staffcat` (
  `staffcat_code` varchar(6) not null default '',
  `staffcat_name` varchar(50) default null,
  primary key  (`staffcat_code`)
);



create table `hs_hr_subject` (
  `sbj_code` varchar(6) not null default '',
  `sbj_name` varchar(50) default null,
  `qualifi_code` varchar(6) default null,
  primary key  (`sbj_code`)
);



create table `hs_hr_subskill` (
  `subskill_code` varchar(6) not null default '',
  `subskill_name` varchar(120) default null,
  `subskill_description` varchar(200) default null,
  `skill_code` varchar(6) default null,
  primary key  (`subskill_code`)
);



create table `hs_hr_tax` (
  `tax_code` varchar(6) default null,
  `tax_name` varchar(120) default null
);



create table `hs_hr_tax_emp` (
  `emp_number` varchar(6) not null default '',
  `tax_code` varchar(6) not null default '',
  `federal_state_flg` smallint(6) not null default '0',
  `tax_filling_status` varchar(20) default null,
  `tax_allowances` smallint(6) default null,
  `tax_extra` smallint(6) default null,
  `taxed_state` varchar(50) default null,
  primary key  (`emp_number`,`tax_code`,`federal_state_flg`)
);



create table `hs_hr_uniform_type` (
  `uf_id` varchar(6) not null default '',
  `uf_name` varchar(100) default null,
  primary key  (`uf_id`)
);



create table `hs_hr_user_group` (
  `userg_id` varchar(36) not null default '',
  `userg_name` varchar(45) default null,
  primary key  (`userg_id`)
) ;



create table `hs_hr_users` (
  `id` varchar(36) not null default '',
  `user_name` varchar(20) default '',
  `user_password` varchar(30) default null,
  `first_name` varchar(45) default null,
  `last_name` varchar(45) default null,
  `reports_to_id` varchar(36) default null,
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
) ;



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
) ;



create table `hs_pr_cost_centre` (
  `centre_code` varchar(6) not null default '',
  `centre_name` varchar(120) default null,
  primary key  (`centre_code`)
);



create table `hs_pr_salary_currency_detail` (
  `sal_grd_code` varchar(6) not null default '',
  `currency_id` varchar(6) not null default '',
  `salcurr_dtl_minsalary` float default null,
  `salcurr_dtl_midsalary` float default null,
  `salcurr_dtl_maxsalary` float default null,
  primary key  (`sal_grd_code`,`currency_id`)
);



create table `hs_pr_salary_grade` (
  `sal_grd_code` varchar(6) not null default '',
  `sal_grd_name` varchar(60) default null,
  primary key  (`sal_grd_code`)
);




alter table hs_hr_company_hierarchy
       add constraint foreign key (loc_code)
                             references hs_hr_location(loc_code) on delete cascade;



alter table hs_hr_company_hierarchy
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number)on delete cascade;



alter table hs_hr_company_hierarchy
       add constraint foreign key (def_level)
                             references hs_hr_company_hierarchy_def(def_level) on delete cascade;



alter table hs_hr_company_hierarchy
       add constraint foreign key (hie_relationship)
                             references hs_hr_company_hierarchy(hie_code) on delete cascade;

alter table hs_pr_salary_currency_detail
       add constraint foreign key (currency_id)
                             references hs_hr_currency_type(currency_id) on delete cascade;



alter table hs_pr_salary_currency_detail
       add constraint foreign key (sal_grd_code)
                             references hs_pr_salary_grade(sal_grd_code) on delete cascade;


alter table hs_hr_corporate_title
       add constraint foreign key (ct_next_upgrade)
                             references hs_hr_corporate_title(ct_code) on delete cascade;


alter table hs_hr_corporate_title
       add constraint foreign key (sal_grd_code)
                             references hs_pr_salary_grade(sal_grd_code) on delete cascade;


alter table hs_hr_designation
       add constraint foreign key (dsg_next_upgrade)
                             references hs_hr_designation(dsg_code) on delete cascade;

alter table hs_hr_designation
       add constraint foreign key (ct_code)
                             references hs_hr_corporate_title(ct_code) on delete cascade;


alter table hs_hr_jd_type
       add constraint foreign key (jdcat_code)
                             references hs_hr_jd_catery(jdcat_code) on delete cascade;


alter table hs_hr_jd_key_result_area
       add constraint foreign key (jdtype_code)
                             references hs_hr_jd_type(jdtype_code) on delete cascade;
alter table hs_hr_jd_key_result_area
       add constraint foreign key (skill_code)
                             references hs_hr_skill(skill_code) on delete cascade;
alter table hs_hr_jd_kpi
       add constraint foreign key (dsg_code)
                             references hs_hr_designation(dsg_code) on delete cascade;


alter table hs_hr_jd_kpi
       add constraint foreign key (jdkra_code)
                             references hs_hr_jd_key_result_area(jdkra_code) on delete cascade;

alter table hs_hr_jd_qualification
       add constraint foreign key (dsg_code)
                             references hs_hr_designation(dsg_code) on delete cascade;

alter table hs_hr_jd_qualification
       add constraint foreign key (qualifi_code)
                             references hs_hr_qualification(qualifi_code) on delete cascade;



alter table hs_hr_jd_qualification
       add constraint foreign key (dsg_code)
                             references hs_hr_designation(dsg_code) on delete cascade;

alter table hs_hr_jd_qualification
       add constraint foreign key (qualifi_code)
                             references hs_hr_qualification(qualifi_code) on delete cascade;

alter table hs_hr_jd_work_experience
       add constraint foreign key (dsg_code)
                             references hs_hr_designation(dsg_code) on delete cascade;

alter table hs_hr_language
	  add constraint foreign key (rating_code)
	  						references hs_hr_rating_method(rating_code) on delete cascade;

alter table hs_hr_qualification
       add constraint foreign key (qualifi_type_code)
                             references hs_hr_qualification_type(qualifi_type_code) on delete cascade;

alter table hs_hr_qualification
       add constraint foreign key (rating_code)
                             references hs_hr_rating_method(rating_code) on delete cascade; 

alter table hs_hr_rating_method_grade
       add constraint foreign key (rating_code)
                             references hs_hr_rating_method(rating_code) on delete cascade;


alter table hs_hr_subject
       add constraint foreign key (qualifi_code)
                             references hs_hr_qualification(qualifi_code) on delete cascade;
       
alter table hs_hr_subskill
       add constraint foreign key (skill_code)
                             references hs_hr_skill(skill_code) on delete cascade;
alter table hs_hr_extra_activity_type
       add constraint foreign key (eacat_code)
                             references hs_hr_extra_activity_catery(eacat_code) on delete cascade;
alter table hs_hr_membership
       add constraint foreign key (membtype_code)
                             references hs_hr_membership_type(membtype_code) on delete cascade;
alter table hs_hr_branch
       add constraint foreign key (bank_code)
                             references hs_hr_bank(bank_code) on delete cascade;
alter table hs_hr_cash_ben_salgrade
       add constraint foreign key (sal_grd_code)
                             references hs_pr_salary_grade(sal_grd_code) on delete cascade;



alter table hs_hr_cash_ben_salgrade
       add constraint foreign key (ben_code)
                             references hs_hr_cash_benefit(ben_code) on delete cascade;
alter table hs_hr_noncash_ben_salgrade
       add constraint foreign key (sal_grd_code)
                             references hs_pr_salary_grade(sal_grd_code) on delete cascade;



alter table hs_hr_noncash_ben_salgrade
       add constraint foreign key (nben_code)
                             references hs_hr_noncash_benefit(nben_code) on delete cascade;
alter table hs_hr_province
       add constraint foreign key (cou_code)
                             references hs_hr_country(cou_code) on delete cascade;
alter table hs_hr_district
       add constraint foreign key (province_code)
                             references hs_hr_province(province_code) on delete cascade;


alter table hs_hr_employee 
       add constraint foreign key (sal_grd_code)
                             references hs_pr_salary_grade(sal_grd_code) on delete cascade;



alter table hs_hr_employee
       add constraint foreign key (emp_pref_work_station)
                             references hs_hr_company_hierarchy(hie_code) on delete cascade;



alter table hs_hr_employee
       add constraint foreign key (staffcat_code)
                             references hs_hr_staffcat(staffcat_code) on delete cascade;



alter table hs_hr_employee
       add constraint foreign key (emp_tem_electorate_code)
                             references hs_hr_electorate(electorate_code) on delete cascade;



alter table hs_hr_employee
       add constraint foreign key (emp_tem_district_code)
                             references hs_hr_district(district_code) on delete cascade;



alter table hs_hr_employee
       add constraint foreign key (emp_tem_province_code)
                             references hs_hr_province(province_code) on delete cascade;



alter table hs_hr_employee
       add constraint foreign key (emp_tem_cou_code)
                             references hs_hr_country(cou_code) on delete cascade;



alter table hs_hr_employee
       add constraint foreign key (emp_per_electorate_code)
                             references hs_hr_electorate(electorate_code) on delete cascade;



alter table hs_hr_employee
       add constraint foreign key (emp_per_district_code)
                            references hs_hr_district(district_code) on delete cascade;



alter table hs_hr_employee
       add constraint foreign key (emp_per_province_code)
                             references hs_hr_province(province_code) on delete cascade;



alter table hs_hr_employee
       add constraint foreign key (emp_per_cou_code)
                             references hs_hr_country(cou_code) on delete cascade;



alter table hs_hr_employee
       add constraint foreign key (loc_code)
                             references hs_hr_location(loc_code) on delete cascade;


alter table hs_hr_employee
       add constraint foreign key (cat_code)
                             references hs_hr_category(cat_code) on delete cascade;



alter table hs_hr_employee
       add constraint foreign key (centre_code)
                             references hs_pr_cost_centre(centre_code) on delete cascade;



alter table hs_hr_employee
       add constraint foreign key (dsg_code)
                             references hs_hr_designation(dsg_code) on delete cascade;



alter table hs_hr_employee
       add constraint foreign key (ct_code)
                             references hs_hr_corporate_title(ct_code) on delete cascade;



alter table hs_hr_employee
       add constraint foreign key (rlg_code)
                             references hs_hr_religion(rlg_code) on delete cascade;



alter table hs_hr_employee
       add constraint foreign key (nat_code)
                             references hs_hr_nationality(nat_code) on delete cascade;

alter table hs_hr_emp_qualification
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_qualification
       add constraint foreign key (qualifi_code)
                             references hs_hr_qualification(qualifi_code) on delete cascade;

alter table hs_hr_emp_subject
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;
alter table hs_hr_emp_subject
       add constraint foreign key (qualifi_code)
                             references hs_hr_qualification(qualifi_code) on delete cascade;

alter table hs_hr_emp_subject
       add constraint foreign key (sbj_code)
                             references hs_hr_subject(sbj_code) on delete cascade;

alter table hs_hr_emp_subject
       add constraint foreign key (rating_grade_code)
                             references hs_hr_rating_method_grade(rating_grade_code) on delete cascade;
alter table hs_hr_emp_work_experience
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;
alter table hs_hr_emp_member_detail
       add constraint foreign key (membtype_code)
                             references hs_hr_membership_type(membtype_code) on delete cascade;

alter table hs_hr_emp_member_detail
       add constraint foreign key (membship_code)
                             references hs_hr_membership(membship_code) on delete cascade;

alter table hs_hr_emp_member_detail
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;
alter table hs_hr_emp_cash_benefit
       add constraint foreign key (ben_code)
                             references hs_hr_cash_benefit(ben_code) on delete cascade;

alter table hs_hr_emp_cash_benefit
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;
alter table hs_hr_emp_cash_ben_remove
       add constraint foreign key (ben_code)
                             references hs_hr_cash_benefit(ben_code) on delete cascade;

alter table hs_hr_emp_cash_ben_remove
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_noncash_benefit
       add constraint foreign key (nben_code)
                             references hs_hr_noncash_benefit(nben_code) on delete cascade;



alter table hs_hr_emp_noncash_benefit
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_noncash_ben_remove 
       add constraint foreign key (nben_code)
                             references hs_hr_noncash_benefit(nben_code) on delete cascade;

alter table hs_hr_emp_noncash_ben_remove
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;
alter table hs_hr_emp_reportto
       add constraint foreign key (erep_sup_emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_reportto
       add constraint foreign key (erep_sub_emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;
alter table hs_hr_emp_jobspec
       add constraint foreign key (jdcat_code)
                             references hs_hr_jd_catery(jdcat_code) on delete cascade;

alter table hs_hr_emp_jobspec
       add constraint foreign key (emp_number )
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

alter table hs_hr_emp_language
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;


alter table hs_hr_emp_language
       add constraint foreign key (lang_code)
                             references hs_hr_language(lang_code) on delete cascade;


alter table hs_hr_emp_language
       add constraint foreign key (rating_grade_code)
                             references hs_hr_rating_method_grade(rating_grade_code) on delete cascade;

alter table hs_hr_emp_language
       add constraint foreign key (rating_code)
                             references hs_hr_rating_method(rating_code) on delete cascade;

alter table hs_hr_emp_extra_activity
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;



alter table hs_hr_emp_extra_activity
       add constraint foreign key (eatype_code)
                             references hs_hr_extra_activity_type(eatype_code) on delete cascade;



alter table hs_hr_emp_extra_activity
       add constraint foreign key (eacat_code)
                             references hs_hr_extra_activity_catery(eacat_code) on delete cascade;

alter table hs_hr_emp_contract_extend
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

-----------------------------
alter table hs_hr_bugs
       add constraint foreign key (`found_in_release`) 
       						references `hs_hr_versions` (`id`) on delete cascade;
       						
alter table hs_hr_bugs
       add constraint foreign key (`fixed_in_release`) 
       						references `hs_hr_versions` (`id`) on delete cascade;
       						
alter table hs_hr_bugs
       add constraint foreign key (`modified_user_id`) 
       						references `hs_hr_users` (`id`) on delete cascade;
       						
alter table hs_hr_bugs
       add constraint foreign key (`created_by`) 
       						references `hs_hr_users` (`id`) on delete cascade;
       						
alter table hs_hr_bugs
       add constraint foreign key (`assigned_developer_id`) 
       						references `hs_hr_developer` (`id`) on delete cascade;
       						
alter table hs_hr_bugs
       add constraint foreign key (`module`) 
       						references `hs_hr_module` (`mod_id`) on delete cascade;

alter table hs_hr_db_version
       add constraint foreign key (`entered_by`) 
       						references `hs_hr_users` (`id`) on delete cascade;

alter table hs_hr_db_version
       add constraint foreign key (`modified_by`) 
       						references `hs_hr_users` (`id`) on delete cascade;

alter table hs_hr_file_version
       add constraint foreign key (`altered_module`)
							references `hs_hr_module` (`mod_id`) on delete cascade;
       
alter table hs_hr_file_version
       add constraint foreign key (`entered_by`) 
       						references `hs_hr_users` (`id`) on delete cascade;
       						
alter table hs_hr_file_version
       add constraint foreign key (`modified_by`) 
       						references `hs_hr_users` (`id`) on delete cascade;

alter table hs_hr_module
       add constraint foreign key (`version`) 
       						references `hs_hr_versions` (`id`) on delete cascade;

alter table hs_hr_rights
       add constraint foreign key (`mod_id`) 
       						references `hs_hr_module` (`mod_id`) on delete cascade;
       						
alter table hs_hr_rights
       add constraint foreign key (`userg_id`) 
       						references `hs_hr_user_group` (`userg_id`) on delete cascade;

alter table hs_hr_users
       add constraint foreign key (`modified_user_id`)
       						references `hs_hr_users` (`id`) on delete cascade;
       						
alter table hs_hr_users
       add constraint foreign key (`created_by`)
       						references `hs_hr_users` (`id`) on delete cascade;
       
alter table hs_hr_users
       add constraint foreign key (`userg_id`) 
       						references `hs_hr_user_group` (`userg_id`) on delete cascade;
       						
alter table hs_hr_versions
       add constraint foreign key (`modified_by`) 
       						references `hs_hr_users` (`id`) on delete cascade;
       						
alter table hs_hr_versions
       add constraint foreign key (`created_by`) 
       						references `hs_hr_users` (`id`) on delete cascade;
       						
alter table hs_hr_versions
       add constraint foreign key (`db_version`) 
       						references `hs_hr_db_version` (`id`) on delete cascade;
       						
alter table hs_hr_versions
       add constraint foreign key (`file_version`) 
       						references `hs_hr_file_version` (`id`) on delete cascade;

INSERT INTO `hs_hr_user_group` VALUES ('USG001','Admin'),('USG002','Operator');
INSERT INTO `hs_hr_users` VALUES ('USR001','demo','demo','Demo','','','','0','1','','0000-00-00 00:00:00','0000-00-00 00:00:00',null,'USR001','','','','','','','','','','Enabled','','','','','','',0,'','USG001'),('USR002','koshika','koshika','Koshika','','','','0','1','','0000-00-00 00:00:00','0000-00-00 00:00:00',null,'USR001','','','','','','','','','','Enabled','','','','','','',0,'','USG001');
INSERT INTO `hs_hr_db_version` VALUES ('DVR001','mysql4.1','initial DB','2005-10-10 00:00:00','2005-12-20 00:00:00','USR001',null);
INSERT INTO `hs_hr_file_version` VALUES ('FVR001',NULL,'Initial Release','2005-10-10 00:00:00','2005-10-15 00:00:00','USR001',null,'file_ver_01');
INSERT INTO `hs_hr_versions` VALUES ('VER001','beta','2005-10-10 00:00:00','2005-11-10 00:00:00','USR001',null,0,'DVR001','FVR001','testinf\\g version');
INSERT INTO `hs_hr_module` VALUES ('MOD001','Admin','Koshika','koshika@beyondm.net','VER001','HR Admin'),('MOD002','PIM','Koshika','koshika@beyondm.net','VER001','HR Functions'),('MOD003','Maintenance','Koshika','koshika@beyondm.net','VER001','Application Maintenance');
INSERT INTO `hs_hr_rights` VALUES ('USG001','MOD001',1,1,1,1),('USG001','MOD002',1,1,1,1),('USG001','MOD003',1,1,1,1);
