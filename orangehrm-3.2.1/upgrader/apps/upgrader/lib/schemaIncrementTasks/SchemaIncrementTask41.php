<?php

include_once 'SchemaIncrementTask.php';

/**
 * 2.6.6 -> 2.6.7 
 */
class SchemaIncrementTask41 extends SchemaIncrementTask {

    public $userInputs;

    public function execute() {
        $this->incrementNumber = 41;
        parent::execute();

        $result = array();

        foreach ($this->sql as $sql) {
            $result[] = $this->upgradeUtility->executeSql($sql);
        }

        $result[] = $this->upgradeAttendanceConfiguration();
        
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

        // New Tables
        $sql[0] = <<<SQL0
create table `ohrm_timesheet`(
  `timesheet_id` bigint(20) not null,
  `state` varchar(255) not null,
  `start_date` date not null,
  `end_date` date not null,
  `employee_id` bigint(20) not null,
  primary key  (`timesheet_id`)
) engine=innodb default charset=utf8

SQL0;

        $sql[1] = <<<SQL1
create table `ohrm_timesheet_item`(
  `timesheet_item_id` bigint(20) not null,
  `timesheet_id` bigint(20) not null,
  `date` date not null,
  `duration` bigint(20) default null,
  `comment` varchar(255) default null,
  `project_id` bigint(20) not null,
  `employee_id` bigint(20) not null,
  `activity_id` bigint(20) not null,
  primary key  (`timesheet_item_id`),
  key `timesheet_id` (`timesheet_id`),
  key `activity_id` (`activity_id`)
) engine=innodb default charset=utf8

SQL1;

        $sql[2] = <<<SQL2
create table `ohrm_timesheet_action_log`(
  `timesheet_action_log_id` bigint(20) not null,
  `comment` varchar(255) default null,
  `action` varchar(255),
  `date_time` date not null,
  `performed_by` varchar(255) not null,
  `timesheet_id` bigint(20) not null,
  primary key  (`timesheet_action_log_id`),
  key `timesheet_id` (`timesheet_id`)
) engine=innodb default charset=utf8

SQL2;

        $sql[3] = <<<SQL3
create table `ohrm_workflow_state_machine`(
  `id` bigint(20) not null,
  `workflow` varchar(255) not null,
  `state` varchar(255) not null,
  `role` varchar(255) not null,
  `action` varchar(255) not null,
  `resulting_state` varchar(255) not null,
  primary key (`id`)
) engine=innodb default charset=utf8
    
SQL3;

        $sql[4] = <<<SQL4
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
  primary key (`id`)
) engine=innodb default charset=utf8

SQL4;

        $sql[5] = <<<SQL5
create table `ohrm_report_group` (
  `report_group_id` bigint(20) not null,
  `name` varchar(255) not null,
  `core_sql` mediumtext not null,
  primary key (`report_group_id`)
) engine=innodb default charset=utf8

SQL5;

        $sql[6] = <<<SQL6
create table `ohrm_report` (
  `report_id` bigint(20) not null,
  `name` varchar(255) not null,
  `report_group_id` bigint(20) not null,
  `use_filter_field` boolean not null,
  primary key (`report_id`),
  key `report_group_id` (`report_group_id`)
) engine=innodb default charset=utf8

SQL6;

        $sql[7] = <<<SQL7
create table `ohrm_filter_field` (
  `filter_field_id` bigint(20) not null,
  `report_group_id` bigint(20) not null,
  `name` varchar(255) not null,
  `where_clause_part` mediumtext not null,
  `filter_field_widget` varchar(255),
  `condition_no` int(20) not null,
  `type` varchar(255) not null,
  `required` varchar(10),
  primary key (`filter_field_id`),
  key `report_group_id` (`report_group_id`)
) engine=innodb default charset=utf8

SQL7;

        $sql[8] = <<<SQL8
create table `ohrm_selected_filter_field` (
  `report_id` bigint(20) not null,
  `filter_field_id` bigint(20) not null,
  `filter_field_order` bigint(20) not null,
  `value` varchar(255) default null,
  `where_condition` varchar(255) default null,
  `where_clause` mediumtext default null,
  primary key (`report_id`,`filter_field_id`),
  key `report_id` (`report_id`),
  key `filter_field_id` (`filter_field_id`)
) engine=innodb default charset=utf8

SQL8;

        $sql[9] = <<<SQL9
create table `ohrm_display_field` (
  `display_field_id` bigint(20) not null,
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
  primary key (`display_field_id`)
) engine=innodb default charset=utf8

SQL9;

        $sql[10] = <<<SQL10
create table `ohrm_composite_display_field` (
  `composite_display_field_id` bigint(20) not null,
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
  primary key (`composite_display_field_id`)
) engine=innodb default charset=utf8

SQL10;

        $sql[11] = <<<SQL11
create table `ohrm_available_display_field` (
  `report_group_id` bigint(20) not null,
  `display_field_id` bigint(20) not null,
  primary key (`report_group_id`,`display_field_id`),
  key `report_group_id` (`report_group_id`),
  key `display_field_id` (`display_field_id`)
) engine=innodb default charset=utf8

SQL11;

        $sql[12] = <<<SQL12
create table `ohrm_group_field` (
  `group_field_id` bigint(20) not null,
  `name` varchar(255) not null,
  `group_by_clause` mediumtext not null,
  `group_field_widget` varchar(255),
  primary key (`group_field_id`)
) engine=innodb default charset=utf8

SQL12;

        $sql[13] = <<<SQL13
create table `ohrm_available_group_field` (
  `report_group_id` bigint(20) not null,
  `group_field_id` bigint(20) not null,
  primary key (`report_group_id`,`group_field_id`),
  key `report_group_id` (`report_group_id`),
  key `group_field_id` (`group_field_id`)
) engine=innodb default charset=utf8

SQL13;

        $sql[14] = <<<SQL14
create table `ohrm_selected_display_field` (
  `id` bigint(20) not null,
  `display_field_id` bigint(20) not null,
  `report_id` bigint(20) not null,
  primary key (`id`,`display_field_id`,`report_id`),
  key `display_field_id` (`display_field_id`),
  key `report_id` (`report_id`)
) engine=innodb default charset=utf8

SQL14;

        $sql[15] = <<<SQL15
create table `ohrm_selected_composite_display_field` (
  `id` bigint(20) not null,
  `composite_display_field_id` bigint(20) not null,
  `report_id` bigint(20) not null,
  primary key (`id`,`composite_display_field_id`,`report_id`),
  key `composite_display_field_id` (`composite_display_field_id`),
  key `report_id` (`report_id`)
) engine=innodb default charset=utf8

SQL15;

        $sql[16] = <<<SQL16
create table `ohrm_meta_display_field` (
  `id` bigint(20) not null,
  `display_field_id` bigint(20) not null,
  `report_id` bigint(20) not null,
  primary key (`id`,`display_field_id`,`report_id`),
  key `display_field_id` (`display_field_id`),
  key `report_id` (`report_id`)
) engine=innodb default charset=utf8

SQL16;

        $sql[17] = <<<SQL17
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
  primary key (`summary_display_field_id`)
) engine=innodb default charset=utf8

SQL17;

        $sql[18] = <<<SQL18
create table `ohrm_selected_group_field` (
  `group_field_id` bigint(20) not null,
  `summary_display_field_id` bigint(20) not null,
  `report_id` bigint(20) not null,
  primary key (`group_field_id`,`summary_display_field_id`,`report_id`),
  key `group_field_id` (`group_field_id`),
  key `summary_display_field_id` (`summary_display_field_id`),
  key `report_id` (`report_id`)
) engine=innodb default charset=utf8

SQL18;

        $sql[19] = <<<SQL19
create table `ohrm_job_vacancy`(
	`id` int(13) not null,
	`job_title_code` varchar(10) not null,
        `hiring_manager_id` int(13) default null,
	`name` varchar(100) not null,
	`description` text default null,
	`no_of_positions` int(13) default null,
    `status` int(4) not null,
    `published_in_feed` boolean not null default false,
    `defined_time` datetime not null,
    `updated_time` datetime not null,
	primary key (`id`)
)engine=innodb default charset=utf8

SQL19;

        $sql[20] = <<<SQL20
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
)engine=innodb default charset=utf8

SQL20;

        $sql[21] = <<<SQL21
create table `ohrm_job_candidate_vacancy`(
        `id` int(13) default null unique,
	`candidate_id` int(13) not null,
        `vacancy_id` int(13) not null,
	`status` varchar(100) not null,
        `applied_date` date not null,
	primary key (`candidate_id`, `vacancy_id`)
)engine=innodb default charset=utf8

SQL21;

        $sql[22] = <<<SQL22
create table `ohrm_job_candidate_attachment`(
	`id` int(13) not null auto_increment,
	`candidate_id` int(13) not null,
	`file_name` varchar(200) not null,
        `file_type` varchar(200) default null,
	`file_size` int(11) not null,
	`file_content` mediumblob,
        `attachment_type` int(4) default null,
	primary key (`id`)
)engine=innodb default charset=utf8

SQL22;

        $sql[23] = <<<SQL23
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
)engine=innodb default charset=utf8

SQL23;

        $sql[24] = <<<SQL24
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
)engine=innodb default charset=utf8

SQL24;

        $sql[25] = <<<SQL25
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
	primary key (`id`)
)engine=innodb default charset=utf8

SQL25;

        $sql[26] = <<<SQL26
create table `ohrm_job_interview`(
	`id` int(13) not null auto_increment,
	`candidate_vacancy_id` int(13) default null,
        `candidate_id` int(13) default null,
        `interview_name` varchar(100) not null,
	`interview_date` date default null,
        `interview_time` time default null,
	`note` text default null,
	primary key (`id`)
)engine=innodb default charset=utf8

SQL26;

        $sql[27] = <<<SQL27
create table `ohrm_job_interview_interviewer`(
	`interview_id` int(13) not null,
	`interviewer_id` int(13) not null,
	primary key (`interview_id`, `interviewer_id`)
)engine=innodb default charset=utf8

SQL27;

        $sql[28] = <<<SQL28
alter table ohrm_available_group_field
       add constraint foreign key (group_field_id)
                             references ohrm_group_field(group_field_id)

SQL28;

        $sql[29] = <<<SQL29
alter table ohrm_available_display_field
       add constraint foreign key (display_field_id)
                             references ohrm_display_field(display_field_id)

SQL29;

        $sql[30] = <<<SQL30
alter table ohrm_available_display_field
       add constraint foreign key (report_group_id)
                             references ohrm_report_group(report_group_id)

SQL30;

        $sql[31] = <<<SQL31
alter table ohrm_filter_field
       add constraint foreign key (report_group_id)
                             references ohrm_report_group(report_group_id)

SQL31;

        $sql[32] = <<<SQL32
alter table ohrm_selected_group_field
       add constraint foreign key (report_id)
                             references ohrm_report(report_id)

SQL32;

        $sql[33] = <<<SQL33
alter table ohrm_selected_group_field
       add constraint foreign key (group_field_id)
                             references ohrm_group_field(group_field_id)
SQL33;

        $sql[34] = <<<SQL34
alter table ohrm_selected_group_field
       add constraint foreign key (summary_display_field_id)
                             references ohrm_summary_display_field(summary_display_field_id)

SQL34;

        $sql[35] = <<<SQL35
alter table ohrm_selected_filter_field
       add constraint foreign key (report_id)
                             references ohrm_report(report_id)
SQL35;

        $sql[36] = <<<SQL36
alter table ohrm_selected_filter_field
       add constraint foreign key (filter_field_id)
                             references ohrm_filter_field(filter_field_id)

SQL36;

        $sql[37] = <<<SQL37
alter table ohrm_selected_display_field
       add constraint foreign key (report_id)
                             references ohrm_report(report_id)

SQL37;

        $sql[38] = <<<SQL38
alter table ohrm_selected_display_field
       add constraint foreign key (display_field_id)
                             references ohrm_display_field(display_field_id)

SQL38;

        $sql[39] = <<<SQL39
alter table ohrm_selected_composite_display_field
       add constraint foreign key (report_id)
                             references ohrm_report(report_id)
SQL39;

        $sql[40] = <<<SQL40
alter table ohrm_selected_composite_display_field
       add constraint foreign key (composite_display_field_id)
                             references ohrm_composite_display_field(composite_display_field_id)

SQL40;

        $sql[41] = <<<SQL41
alter table ohrm_meta_display_field
       add constraint foreign key (report_id)
                             references ohrm_report(report_id)

SQL41;

        $sql[42] = <<<SQL42
alter table ohrm_meta_display_field
       add constraint foreign key (display_field_id)
                             references ohrm_display_field(display_field_id)

SQL42;

        $sql[43] = <<<SQL43
alter table ohrm_report
       add constraint foreign key (report_group_id)
                             references ohrm_report_group(report_group_id) on delete cascade

SQL43;

        $sql[44] = <<<SQL44
alter table ohrm_timesheet_action_log
       add constraint foreign key (performed_by)
                             references hs_hr_users(id) on delete cascade

SQL44;

        $sql[45] = <<<SQL45
alter table ohrm_job_interview
       add constraint foreign key (candidate_vacancy_id)
                             references ohrm_job_candidate_vacancy(id) on delete set null

SQL45;

        $sql[46] = <<<SQL46
alter table ohrm_job_interview
       add constraint foreign key (candidate_id)
                             references ohrm_job_candidate(id) on delete cascade

SQL46;

        $sql[47] = <<<SQL47
alter table ohrm_job_interview_interviewer
       add constraint foreign key (interview_id)
                             references ohrm_job_interview(id) on delete cascade

SQL47;

        $sql[48] = <<<SQL48
alter table ohrm_job_interview_interviewer
       add constraint foreign key (interviewer_id)
                             references hs_hr_employee(emp_number) on delete cascade

SQL48;
        $sql[49] = <<<SQL49
alter table ohrm_job_candidate_attachment
       add constraint foreign key (candidate_id)
                             references ohrm_job_candidate(id) on delete cascade

SQL49;

        $sql[50] = <<<SQL50
alter table ohrm_job_vacancy_attachment
       add constraint foreign key (vacancy_id)
                             references ohrm_job_vacancy(id) on delete cascade

SQL50;

        $sql[51] = <<<SQL51
alter table ohrm_job_interview_attachment
       add constraint foreign key (interview_id)
                             references ohrm_job_interview(id) on delete cascade

SQL51;

        $sql[52] = <<<SQL52
alter table ohrm_job_candidate_history
       add constraint foreign key (candidate_id)
                             references ohrm_job_candidate(id) on delete cascade

SQL52;

        $sql[53] = <<<SQL53
alter table ohrm_job_candidate_history
       add constraint foreign key (vacancy_id)
                             references ohrm_job_vacancy(id) on delete set null

SQL53;

        $sql[54] = <<<SQL54
alter table ohrm_job_candidate_history
       add constraint foreign key (interview_id)
                             references ohrm_job_interview(id) on delete set null

SQL54;

        $sql[55] = <<<SQL55
alter table ohrm_job_candidate_history
       add constraint foreign key (performed_by)
                             references hs_hr_employee(emp_number) on delete set null

SQL55;

        $sql[56] = <<<SQL56
alter table ohrm_job_vacancy
       add constraint foreign key (job_title_code)
                             references hs_hr_job_title(jobtit_code) on delete cascade

SQL56;

        $sql[57] = <<<SQL57
alter table ohrm_job_vacancy
       add constraint foreign key (hiring_manager_id)
                             references hs_hr_employee(emp_number) on delete set null

SQL57;

        $sql[58] = <<<SQL58
alter table ohrm_job_candidate
       add constraint foreign key (added_person)
                             references hs_hr_employee(emp_number) on delete set null
SQL58;

        $sql[59] = <<<SQL59
alter table ohrm_job_candidate_vacancy
       add constraint foreign key (candidate_id)
                             references ohrm_job_candidate(id) on delete cascade

SQL59;

        $sql[60] = <<<SQL60
alter table ohrm_job_candidate_vacancy
       add constraint foreign key (vacancy_id)
                             references ohrm_job_vacancy(id) on delete cascade
SQL60;

        // Table modifications
        $sql[61] = "ALTER TABLE hs_hr_config MODIFY COLUMN `value` varchar(255) not null default ''";

        // Data changes
        $sql[62] = <<<SQL62
INSERT INTO `hs_hr_unique_id`(last_id, table_name, field_name)
VALUES (0, 'ohrm_timesheet', 'timesheet_id'),
    (0, 'ohrm_timesheet_action_log', 'timesheet_action_log_id'),
    (0, 'ohrm_timesheet_item', 'timesheet_item_id'),
    (0,'ohrm_attendance_record', 'id'),
    (0, 'ohrm_job_vacancy', 'id'),
    (0, 'ohrm_job_candidate', 'id'),
    (89,'ohrm_workflow_state_machine', 'id'),
    (0, 'ohrm_job_candidate_attachment', 'id'),
    (0, 'ohrm_job_vacancy_attachment', 'id'),
    (0, 'ohrm_job_candidate_vacancy', 'id'),
    (0, 'ohrm_job_candidate_history', 'id'),
    (0, 'ohrm_job_interview', 'id')
                 
SQL62;
        
    $sql[63] = <<<SQL63
INSERT INTO `ohrm_workflow_state_machine` VALUES ('1','0','INITIAL','SYSTEM','7','NOT SUBMITTED'),
                                   ('2','0','SUBMITTED','ADMIN','2','APPROVED'),
                                   ('3','0','SUBMITTED','ADMIN','3','REJECTED'),
                                   ('4','0','SUBMITTED','ADMIN','0','SUBMITTED'),
                                   ('5','0','SUBMITTED','ADMIN','5','SUBMITTED'),
                                   ('6','0','SUBMITTED','SUPERVISOR','2','APPROVED'),
                                   ('7','0','SUBMITTED','SUPERVISOR','3','REJECTED'),
                                   ('8','0','SUBMITTED','SUPERVISOR','5','SUBMITTED'),
                                   ('9','0','SUBMITTED','SUPERVISOR','0','SUBMITTED'),
                                   ('10','0','SUBMITTED','ESS USER','0','SUBMITTED'),
                                   ('11','0','SUBMITTED','ESS USER','5','SUBMITTED'),
                                   ('12','0','NOT SUBMITTED','ESS USER','1','SUBMITTED'),
                                   ('13','0','NOT SUBMITTED','ESS USER','5','NOT SUBMITTED'),
                                   ('14','0','NOT SUBMITTED','ESS USER','6','NOT SUBMITTED'),
                                   ('15','0','NOT SUBMITTED','ESS USER','0','NOT SUBMITTED'),
                                   ('16','0','NOT SUBMITTED','SUPERVISOR','0','NOT SUBMITTED'),
                                   ('17','0','NOT SUBMITTED','SUPERVISOR','5','NOT SUBMITTED'),
                                   ('18','0','NOT SUBMITTED','SUPERVISOR','1','SUBMITTED'),
                                   ('19','0','NOT SUBMITTED','ADMIN','0','NOT SUBMITTED'),
                                   ('20','0','NOT SUBMITTED','ADMIN','5','NOT SUBMITTED'),
                                   ('21','0','NOT SUBMITTED','ADMIN','1','SUBMITTED'),
                                   ('22','0','REJECTED','ESS USER','1','SUBMITTED'),
                                   ('23','0','REJECTED','ESS USER','0','REJECTED'),
                                   ('24','0','REJECTED','ESS USER','5','REJECTED'),
                                   ('25','0','REJECTED','SUPERVISOR','1','SUBMITTED'),
                                   ('26','0','REJECTED','SUPERVISOR','0','REJECTED'),
                                   ('27','0','REJECTED','SUPERVISOR','5','REJECTED'),
                                   ('28','0','REJECTED','ADMIN','0','REJECTED'),
                                   ('29','0','REJECTED','ADMIN','5','SUBMITTED'),
                                   ('30','0','REJECTED','ADMIN','1','SUBMITTED'),
                                   ('31','0','APPROVED','ESS USER','0','APPROVED'),
                                   ('32','0','APPROVED','SUPERVISOR','0','APPROVED'),
                                   ('33','0','APPROVED','ADMIN','0','APPROVED'),
                                   ('34','0','APPROVED','ADMIN','4','SUBMITTED'),
                                   ('35','1','PUNCHED IN','ESS USER','1','PUNCHED OUT'),
                                   ('36','1','INITIAL','ESS USER','0','PUNCHED IN'),
                                   ('37','2','INITIAL','ADMIN','1','APPLICATION INITIATED'),
                                   ('38','2','APPLICATION INITIATED','ADMIN','2','SHORTLISTED'),
                                   ('39','2','APPLICATION INITIATED','ADMIN','3','REJECTED'),
                                   ('40','2','SHORTLISTED','ADMIN','4','1ST INTERVIEW SCHEDULED'),
                                   ('41','2','SHORTLISTED','ADMIN','3','REJECTED'),
                                   ('42','2','1ST INTERVIEW SCHEDULED','ADMIN','3','REJECTED'),
                                   ('43','2','1ST INTERVIEW SCHEDULED','ADMIN','5','1ST INTERVIEW PASSED'),
                                   ('44','2','1ST INTERVIEW SCHEDULED','ADMIN','6','INTERVIEW FAILED'),
                                   ('45','2','1ST INTERVIEW PASSED','ADMIN','10','2ND INTERVIEW SCHEDULED'),
                                   ('46','2','1ST INTERVIEW PASSED','ADMIN','3','REJECTED'),
                                   ('47','2','1ST INTERVIEW PASSED','ADMIN','7','JOB OFFERED'),
                                   ('48','2','2ND INTERVIEW SCHEDULED','ADMIN','3','REJECTED'),
                                   ('49','2','2ND INTERVIEW SCHEDULED','ADMIN','5','2ND INTERVIEW PASSED'),
                                   ('50','2','2ND INTERVIEW SCHEDULED','ADMIN','6','INTERVIEW FAILED'),
                                   ('51','2','2ND INTERVIEW PASSED','ADMIN','7','JOB OFFERED'),
                                   ('52','2','2ND INTERVIEW PASSED','ADMIN','3','REJECTED'),
                                   ('53','2','INTERVIEW FAILED','ADMIN','3','REJECTED'),
                                   ('54','2','JOB OFFERED','ADMIN','8','OFFER DECLINED'),
                                   ('55','2','JOB OFFERED','ADMIN','3','REJECTED'),
                                   ('56','2','JOB OFFERED','ADMIN','9','HIRED'),
                                   ('57','2','OFFER DECLINED','ADMIN','3','REJECTED'),
                                   ('58','2','INITIAL','HIRING MANAGER','1','APPLICATION INITIATED'),
                                   ('59','2','APPLICATION INITIATED','HIRING MANAGER','2','SHORTLISTED'),
                                   ('60','2','APPLICATION INITIATED','HIRING MANAGER','3','REJECTED'),
                                   ('61','2','SHORTLISTED','HIRING MANAGER','4','1ST INTERVIEW SCHEDULED'),
                                   ('62','2','SHORTLISTED','HIRING MANAGER','3','REJECTED'),
                                   ('63','2','1ST INTERVIEW SCHEDULED','HIRING MANAGER','3','REJECTED'),
                                   ('64','2','1ST INTERVIEW SCHEDULED','HIRING MANAGER','5','1ST INTERVIEW PASSED'),
                                   ('65','2','1ST INTERVIEW SCHEDULED','HIRING MANAGER','6','INTERVIEW FAILED'),
                                   ('66','2','1ST INTERVIEW PASSED','HIRING MANAGER','10','2ND INTERVIEW SCHEDULED'),
                                   ('67','2','1ST INTERVIEW PASSED','HIRING MANAGER','3','REJECTED'),
                                   ('68','2','1ST INTERVIEW PASSED','HIRING MANAGER','7','JOB OFFERED'),
                                   ('69','2','2ND INTERVIEW SCHEDULED','HIRING MANAGER','3','REJECTED'),
                                   ('70','2','2ND INTERVIEW SCHEDULED','HIRING MANAGER','5','2ND INTERVIEW PASSED'),
                                   ('71','2','2ND INTERVIEW SCHEDULED','HIRING MANAGER','6','INTERVIEW FAILED'),
                                   ('72','2','2ND INTERVIEW PASSED','HIRING MANAGER','7','JOB OFFERED'),
                                   ('73','2','2ND INTERVIEW PASSED','HIRING MANAGER','3','REJECTED'),
                                   ('74','2','INTERVIEW FAILED','HIRING MANAGER','3','REJECTED'),
                                   ('75','2','JOB OFFERED','HIRING MANAGER','8','OFFER DECLINED'),
                                   ('76','2','JOB OFFERED','HIRING MANAGER','3','REJECTED'),
                                   ('77','2','JOB OFFERED','HIRING MANAGER','9','HIRED'),
                                   ('78','2','OFFER DECLINED','HIRING MANAGER','3','REJECTED'),
                                   ('79','2','1ST INTERVIEW SCHEDULED','INTERVIEWER','5','1ST INTERVIEW PASSED'),
                                   ('80','2','1ST INTERVIEW SCHEDULED','INTERVIEWER','6','INTERVIEW FAILED'),
                                   ('81','2','2ND INTERVIEW SCHEDULED','INTERVIEWER','5','2ND INTERVIEW PASSED'),
                                   ('82','2','2ND INTERVIEW SCHEDULED','INTERVIEWER','6','INTERVIEW FAILED'),
                                    ('83','1','INITIAL','ADMIN','5','PUNCHED IN'),
                                    ('84','1','PUNCHED IN','ADMIN','6','PUNCHED OUT'),
                                    ('85','1','PUNCHED IN','ADMIN','2','PUNCHED IN'),
                                    ('86','1','PUNCHED IN','ADMIN','7','N/A'),
                                    ('87','1','PUNCHED OUT','ADMIN','2','PUNCHED OUT'),
                                    ('88','1','PUNCHED OUT','ADMIN','3','PUNCHED OUT'),
                                    ('89','1','PUNCHED OUT','ADMIN','7','N/A')
SQL63;

        $sql[64] = <<<SQL64
INSERT INTO `ohrm_report_group` VALUES (1,'timesheet', 'SELECT selectCondition FROM hs_hr_project_activity LEFT JOIN (SELECT * FROM ohrm_timesheet_item WHERE whereCondition1) AS ohrm_timesheet_item  ON (ohrm_timesheet_item.activity_id = hs_hr_project_activity.activity_id) LEFT JOIN hs_hr_project ON (hs_hr_project.project_id = hs_hr_project_activity.project_id) LEFT JOIN hs_hr_employee ON (hs_hr_employee.emp_number = ohrm_timesheet_item.employee_id) LEFT JOIN ohrm_timesheet ON (ohrm_timesheet.timesheet_id = ohrm_timesheet_item.timesheet_id) LEFT JOIN hs_hr_customer ON (hs_hr_customer.customer_id = hs_hr_project.customer_id) WHERE whereCondition2'),
                                       (2,'attendance', 'SELECT selectCondition FROM hs_hr_employee LEFT JOIN (SELECT * FROM ohrm_attendance_record WHERE ( ( ohrm_attendance_record.punch_in_user_time BETWEEN "#@fromDate@,@1970-01-01@#" AND "#@toDate@,@CURDATE()@#" ) AND ( ohrm_attendance_record.punch_out_user_time BETWEEN "#@fromDate@,@1970-01-01@#" AND "#@toDate@,@CURDATE()@#" ) ) ) AS ohrm_attendance_record ON (hs_hr_employee.emp_number = ohrm_attendance_record.employee_id) WHERE hs_hr_employee.emp_number = #@employeeId@,@hs_hr_employee.emp_number AND (hs_hr_employee.emp_status != "EST000" OR hs_hr_employee.emp_status is null) @# AND (hs_hr_employee.job_title_code = #@"jobTitle")@,@hs_hr_employee.job_title_code OR hs_hr_employee.job_title_code is null)@# AND (hs_hr_employee.work_station IN (#@subUnit)@,@SELECT id FROM hs_hr_compstructtree) OR hs_hr_employee.work_station is null@#) AND (hs_hr_employee.emp_status = #@"employeeStatus")@,@hs_hr_employee.emp_status OR hs_hr_employee.emp_status is null)@#')

SQL64;

        $sql[65] = <<<SQL65
INSERT INTO `ohrm_report` VALUES (1, 'Project Report', 1, 1),
                                 (2, 'Employee Report', 1, 1),
                                 (3, 'Project Activity Details', 1,1),
                                 (4, 'Attendance Total Summary Report', 2,0)

SQL65;

        $sql[66] = <<<SQL66
INSERT INTO `ohrm_filter_field` VALUES (1, 1, 'project_name', 'hs_hr_project.project_id', 'ohrmWidgetProjectList', 2, 'Runtime', 'true'),
                                       (2, 1, 'activity_show_deleted', 'hs_hr_project_activity.deleted', 'ohrmWidgetInputCheckbox', 2, 'Runtime', 'false'),
                                       (3, 1, 'project_date_range', 'date', 'ohrmWidgetDateRange', 1, 'Runtime', 'false'),
                                       (4, 1, 'employee', 'hs_hr_employee.emp_number', 'ohrmWidgetEmployeeListAutoFill', 2, 'Runtime', 'true'),
                                       (5, 1, 'activity_name', 'hs_hr_project_activity.activity_id', 'ohrmWidgetProjectActivityList', 2, 'Runtime', 'true'),
                                       (6, 1, 'project_name', 'hs_hr_project.project_id', 'ohrmWidgetProjectListWithAllOption', 2, 'Runtime', 'true'),
                                       (7, 1, 'only_inlclude_approved_timesheets', 'ohrm_timesheet.state', 'ohrmWidgetApprovedTimesheetInputCheckBox', 2, 'Runtime', 'false')
SQL66;

        $sql[67] = <<<SQL67
   
INSERT INTO `ohrm_display_field` VALUES (1, 'hs_hr_project.name', 'Project Name', 'projectname',  'false', null, null, 'label', '<xml><getter>projectname</getter></xml>', 200, '0', null),
                                        (2, 'hs_hr_project_activity.name', 'Activity Name', 'activityname', 'false', null, null, 'link', '<xml><labelGetter>activityname</labelGetter><placeholderGetters><id>activity_id</id><total>totalduration</total><projectId>projectId</projectId><from>fromDate</from><to>toDate</to></placeholderGetters><urlPattern>../../displayProjectActivityDetailsReport?reportId=3#activityId={id}#total={total}#from={from}#to={to}#projectId={projectId}</urlPattern></xml>', 200, '0', null),
                                        (3, 'hs_hr_project_activity.project_id', 'Project Id', null, 'false', null, null, 'label', '<xml><getter>project_id</getter></xml>', 75, '0', 'right'),
                                        (4, 'hs_hr_project_activity.activity_id', 'Activity Id', null,  'false', null, null, 'label', '<xml><getter>activity_id</getter></xml>', 75, '0', 'right'),
                                        (5, 'ohrm_timesheet_item.duration', 'Time (hours)', null, 'false', null, null, 'label', '<xml><getter>duration</getter></xml>', 75, '0', 'right'),
                                        (6, 'hs_hr_employee.emp_firstname', 'Employee Firstname', null,  'false', null, null, 'label', '<xml><getter>emp_firstname</getter></xml>', 200, '0', null),
                                        (7, 'hs_hr_employee.emp_lastname', 'Employee Lastname', null, 'false', null, null, 'label', '<xml><getter>emp_lastname</getter></xml>', 200, '0', null),
                                        (8, 'hs_hr_project_activity.name', 'Activity Name', 'activityname', 'false', null, null, 'label', '<xml><getter>activityname</getter></xml>', 200, '0', null);
SQL67;

   $sql[68] = <<<SQL68
INSERT INTO `ohrm_group_field` VALUES (1, 'activity id', 'GROUP BY hs_hr_project_activity.activity_id', null),
                                      (2, 'employee number', 'GROUP BY hs_hr_employee.emp_number', null);
SQL68;
   
   $sql[69] = <<<SQL69
INSERT INTO `ohrm_selected_filter_field` VALUES (1, 1, 1, null, null, null),
                                                (1, 3, 2, null, null, null),
                                                (1, 7, 3, null, null, null),
                                                (2, 3, 4, null, null, null),
                                                (2, 4, 1, null, null, null),
                                                (2, 5, 3, null, null, null),
                                                (2, 6, 2, null, null, null),
                                                (2, 7, 5, null, null, null),
                                                (3, 3, 2, null, null, null),
                                                (3, 5, 1, null, null, null);
SQL69;

    $sql[70] = <<<SQL70
INSERT INTO `ohrm_selected_display_field` VALUES (2, 2, 1),
                                                 (4, 8, 2);
SQL70;

    $sql[71] = <<<SQL71
   
INSERT INTO `ohrm_composite_display_field` VALUES (1, 'CONCAT(hs_hr_employee.emp_firstname, " " ,hs_hr_employee.emp_lastname)', 'Employee Name', 'employeeName', 'false', null, null, 'label', '<xml><getter>employeeName</getter></xml>', 300, '0', null),
                                                  (2, 'CONCAT(hs_hr_customer.name, " - " ,hs_hr_project.name)', 'Project Name', 'projectname', 'false', null, null, 'label', '<xml><getter>projectname</getter></xml>', 300, '0', null);
SQL71;

    $sql[72] = <<<SQL72
INSERT INTO `ohrm_meta_display_field` VALUES (1, 3, 1),
                                             (2, 4, 1);
SQL72;
   
        $sql[73] = <<<SQL73
INSERT INTO `ohrm_selected_composite_display_field` VALUES (1, 1, 3),
                                                           (2, 1, 4),
                                                           (3, 2, 2);
SQL73;
        
        $sql[74] = <<<SQL74
INSERT INTO `ohrm_summary_display_field` VALUES (1, 'ROUND(COALESCE(sum(duration)/3600, 0),2)', 'Time (hours)', 'totalduration', 'false', null, null, 'label', '<xml><getter>totalduration</getter></xml>', 100, 'false', 'right'),
                                                (2, 'ROUND(COALESCE(sum(TIMESTAMPDIFF(SECOND , ohrm_attendance_record.punch_in_utc_time , ohrm_attendance_record.punch_out_utc_time))/3600, 0),2)', 'Time (hours)', 'totalduration', 'false', null, null, 'label', '<xml><getter>totalduration</getter></xml>', 100, 'false', 'right');
SQL74;
   
        $sql[75] = <<<SQL75
INSERT INTO `ohrm_selected_group_field` VALUES (1, 1, 1),
                                               (1, 1, 2),
                                               (2, 1, 3),
                                               (2, 2, 4);
SQL75;
        


        $sql[77] = <<<SQL77
INSERT INTO `hs_hr_config`(`key`, `value`) VALUES
    ('timesheet_time_format', '1'),
    ('timesheet_period_and_start_date', '<TimesheetPeriod><PeriodType>Weekly</PeriodType><ClassName>WeeklyTimesheetPeriod</ClassName><StartDate>1</StartDate><Heading>Week</Heading></TimesheetPeriod>')

SQL77;

        $sql[78] = <<<SQL78
INSERT INTO `hs_hr_config` (`key`, `value`) VALUES
('timesheet_period_set', 'Yes')
ON DUPLICATE KEY UPDATE `key`='timesheet_period_set'

SQL78;

        $sql[79] = <<<SQL79
INSERT INTO `ohrm_timesheet` (`ohrm_timesheet`.`timesheet_id`, `ohrm_timesheet`.`state`, `ohrm_timesheet`.`start_date`, `ohrm_timesheet`.`end_date`, `ohrm_timesheet`.`employee_id`)
SELECT `hs_hr_timesheet`.`timesheet_id`, `hs_hr_timesheet`.`status`, `hs_hr_timesheet`.`start_date`, `hs_hr_timesheet`.`end_date`, `hs_hr_timesheet`.`employee_id`
FROM `hs_hr_timesheet`

SQL79;

        $sql[80] = "UPDATE `ohrm_timesheet` SET `state`='NOT SUBMITTED' WHERE `state`='0'";
        $sql[81] = "UPDATE `ohrm_timesheet` SET `state`='SUBMITTED' WHERE `state`='10'";
        $sql[82] = "UPDATE `ohrm_timesheet` SET `state`='APPROVED' WHERE `state`='20'";
        $sql[83] = "UPDATE `ohrm_timesheet` SET `state`='REJECTED' WHERE `state`='30'";

        $sql[84] = <<<SQL84
INSERT INTO `ohrm_timesheet_item` (`ohrm_timesheet_item`.`timesheet_item_id`, `ohrm_timesheet_item`.`timesheet_id`, `ohrm_timesheet_item`.`date`, `ohrm_timesheet_item`.`duration`, `ohrm_timesheet_item`.`comment`, `ohrm_timesheet_item`.`project_id`, `ohrm_timesheet_item`.`employee_id`, `ohrm_timesheet_item`.`activity_id`)
SELECT `hs_hr_time_event`.`time_event_id`, `hs_hr_time_event`.`timesheet_id`, `hs_hr_time_event`.`reported_date`, `hs_hr_time_event`.`duration`, `hs_hr_time_event`.`description`, `hs_hr_time_event`.`project_id`, `hs_hr_time_event`.`employee_id`, `hs_hr_time_event`.`activity_id`
FROM `hs_hr_time_event`

SQL84;

        $sql[85] = <<<SQL85
INSERT INTO `ohrm_attendance_record` (`ohrm_attendance_record`.`id`, `ohrm_attendance_record`.`employee_id`, `ohrm_attendance_record`.`punch_in_user_time`, `ohrm_attendance_record`.`punch_out_user_time`, `ohrm_attendance_record`.`punch_in_note`, `ohrm_attendance_record`.`punch_out_note`, `ohrm_attendance_record`.`state` )
SELECT `hs_hr_attendance`.`attendance_id`, `hs_hr_attendance`.`employee_id`, `hs_hr_attendance`.`punchin_time`, `hs_hr_attendance`.`punchout_time`, `hs_hr_attendance`.`in_note`, `hs_hr_attendance`.`out_note`, `hs_hr_attendance`.`status` 
FROM hs_hr_attendance

SQL85;


        $sql[86] = <<<SQL86
UPDATE `hs_hr_unique_id` SET last_id = (SELECT COALESCE(MAX( id ),0)
FROM `ohrm_attendance_record`
WHERE 1) WHERE table_name='ohrm_attendance_record' AND field_name='id'

SQL86;

        $sql[87] = <<<SQL87
UPDATE `hs_hr_unique_id` SET last_id = (SELECT COALESCE(MAX( timesheet_item_id ),0)
FROM `ohrm_timesheet_item`
WHERE 1) WHERE table_name='ohrm_timesheet_item' AND field_name='timesheet_item_id'

SQL87;

        $sql[88] = <<<SQL88
UPDATE `hs_hr_unique_id` SET last_id = (SELECT COALESCE(MAX( timesheet_action_log_id ),0)
FROM `ohrm_timesheet_action_log`
WHERE 1) WHERE table_name='ohrm_timesheet_action_log' AND field_name='timesheet_action_log_id'

SQL88;

        $sql[89] = <<<SQL89
UPDATE `hs_hr_unique_id` SET last_id = (SELECT COALESCE(MAX( timesheet_id ),0)
FROM `ohrm_timesheet`
WHERE 1) WHERE table_name='ohrm_timesheet' AND field_name='timesheet_id'

SQL89;

        $sql[90] = <<<SQL90
UPDATE `hs_hr_unique_id` SET last_id = (SELECT COALESCE(MAX( reporting_method_id ),0)
FROM `ohrm_emp_reporting_method`
WHERE 1) WHERE table_name='ohrm_emp_reporting_method' AND field_name='reporting_method_id'

SQL90;


        $sql[91] = "UPDATE `ohrm_attendance_record` SET `state` = 'PUNCHED IN' WHERE `punch_out_user_time` IS NULL";
        $sql[92] = "UPDATE `ohrm_attendance_record` SET `state` = 'PUNCHED OUT' WHERE `punch_out_user_time` IS NOT NULL";
        
        $sql[93] = "ALTER TABLE `hs_hr_emp_basicsalary` 
                            DROP FOREIGN KEY `hs_hr_emp_basicsalary_ibfk_2`";
        
        $sql[94] = "ALTER TABLE `hs_pr_salary_currency_detail` 
                            DROP FOREIGN KEY `hs_pr_salary_currency_detail_ibfk_1`";
        
        $sql[95] = "UPDATE `hs_hr_currency_type` SET `currency_id` = 'SRD', `currency_name` = 'Surinamese Dollar' WHERE `code` = 137";
        
        $sql[96] = "UPDATE `hs_hr_emp_basicsalary` SET `currency_id` = 'SRD' WHERE `currency_id` = 'SRG'";
        
        $sql[97] = "UPDATE `hs_pr_salary_currency_detail` SET `currency_id` = 'SRD' WHERE `currency_id` = 'SRG'";
        
        $sql[98] = "alter table hs_pr_salary_currency_detail
                        add constraint `hs_pr_salary_currency_detail_ibfk_1` foreign key (currency_id)
                        references hs_hr_currency_type(currency_id) on delete cascade;";
        
        $sql[99] = "alter table hs_hr_emp_basicsalary
                        add constraint `hs_hr_emp_basicsalary_ibfk_2` foreign key (currency_id)
                        references hs_hr_currency_type(currency_id) on delete cascade;";        

        
        $this->sql = $sql;
        
        $this->loadSqlForAttendanceUTCConversion();        
    }

    public function getNotes() {
        $notes = array();
        $notes[] = "Timesheet action history will be empty for Timesheets created by OrangeHRM 2.6.6 or earlier.";
        
        return $notes;        
    }   
    
    private function loadSqlForAttendanceUTCConversion() {

        $q = "SELECT * FROM `hs_hr_attendance`";

        $result = $this->upgradeUtility->executeSql($q);
        $serverTimeZoneCode = date_default_timezone_get();
        $serverTimeZone = new DateTimeZone($serverTimeZoneCode);
        
        while ($row = $this->upgradeUtility->fetchArray($result)) {
            
            $timeStampDiff = $row['timestamp_diff'];
            
            // Calculate punch in related values:            
            $punchInTimeServer = new DateTime($row['punchin_time'], $serverTimeZone);

            $serverPunchInOffset = $serverTimeZone->getOffset($punchInTimeServer);
            
            // Calculate UTC and User punch in time.
            $punchInTimeUTC = date('Y-m-d H:i:s', $punchInTimeServer->format('U') - $serverPunchInOffset);
            $punchInTimeUser = date('Y-m-d H:i:s', $punchInTimeServer->format('U') + $timeStampDiff);

            // User timezone diff from UTC
            $userPunchInOffset = $timeStampDiff + $serverPunchInOffset;
            $userPunchInOffsetHours = $userPunchInOffset / (60*60);

            $punchOutTimeServer = null;
            $punchOutTimeUTC = null;
            
            // Calculate punch out related values
            if ($row['punchout_time'] != null) {
                $punchOutTimeServer = new DateTime($row['punchout_time'], $serverTimeZone);
                
                $serverPunchOutOffset = $serverTimeZone->getOffset($punchOutTimeServer);

                // Calculate UTC and User punch out time.
                $punchOutTimeUTC = date('Y-m-d H:i:s', $punchOutTimeServer->format('U') - $serverPunchOutOffset);
                $punchOutTimeUser = date('Y-m-d H:i:s', $punchOutTimeServer->format('U') + $timeStampDiff);

                // User timezone diff from UTC
                $userPunchOutOffset = $timeStampDiff + $serverPunchOutOffset;   
                $userPunchOutOffsetHours = $userPunchOutOffset / (60*60);
            }            
            
            $id = $row['attendance_id'];

            $query = "UPDATE ohrm_attendance_record SET
                punch_in_user_time='{$punchInTimeUser}',
                punch_in_time_offset='{$userPunchInOffsetHours}',
                punch_in_utc_time='{$punchInTimeUTC}'";
            
            if ($punchOutTimeUTC != null) {
                $query .= ", punch_out_user_time='{$punchOutTimeUser}',
                    punch_out_time_offset='{$userPunchOutOffsetHours}',
                    punch_out_utc_time='{$punchOutTimeUTC}'";
            }
            
            $query .= " WHERE id = '{$id}'"; 

            $this->sql[] = $query;
        }
    }    
    
    protected function getBooleanConfigValue($key) {
        $value = false;
        
        $res = $this->upgradeUtility->executeSql("SELECT `value` from hs_hr_config where `key` = '{$key}'");
        if ($res) {
            $values = $this->upgradeUtility->fetchArray($res);
            if ($values && isset($values[0])) {
                $value = ($values[0] == 'Yes');
            }
        }
        
        return $value;
    }
    
    protected function upgradeAttendanceConfiguration() {
        
        $success = true;
        
        $attendanceEmpChangeTime = $this->getBooleanConfigValue('attendanceEmpChangeTime');
        $attendanceEmpEditSubmitted = $this->getBooleanConfigValue('attendanceEmpEditSubmitted');
        $attendanceSupEditSubmitted = $this->getBooleanConfigValue('attendanceSupEditSubmitted');
        
        $rows = array();
        $id = $this->getNextWorkflowId();
        
        if ($attendanceEmpChangeTime) {
            $rows[] = "({$id}, '1', 'INITIAL', 'ESS USER', '2', 'INITIAL')";
            $id++;
            $rows[] = "({$id}, '1', 'PUNCHED IN', 'ESS USER', '3', 'PUNCHED IN')";
            $id++;
        }
        
        if ($attendanceEmpEditSubmitted) {
            $rows[] = "({$id}, '1', 'PUNCHED IN', 'ESS USER', '2', 'PUNCHED IN')";
            $id++;
            $rows[] = "({$id}, '1', 'PUNCHED OUT', 'ESS USER', '3', 'PUNCHED OUT')";
            $id++;
            $rows[] = "({$id}, '1', 'PUNCHED OUT', 'ESS USER', '2', 'PUNCHED OUT')";
            $id++;
            $rows[] = "({$id}, '1', 'PUNCHED IN', 'ESS USER', '7', 'NA')";
            $id++;
            $rows[] = "({$id}, '1', 'PUNCHED OUT', 'ESS USER', '7', 'NA')";            
            $id++;
        }
        
        if ($attendanceSupEditSubmitted) {
            $rows[] = "({$id}, '1', 'PUNCHED IN', 'SUPERVISOR', '2', 'PUNCHED IN')";
            $id++;
            $rows[] = "({$id}, '1', 'PUNCHED OUT', 'SUPERVISOR', '2', 'PUNCHED OUT')";
            $id++;
            $rows[] = "({$id}, '1', 'PUNCHED OUT', 'SUPERVISOR', '3', 'PUNCHED OUT')";
            $id++;
            $rows[] = "({$id}, '1', 'PUNCHED IN', 'SUPERVISOR', '7', 'NA')";
            $id++;
            $rows[] = "({$id}, '1', 'PUNCHED OUT', 'SUPERVISOR', '7', 'NA')";
            $id++;
            $rows[] = "({$id}, '1', 'INITIAL', 'SUPERVISOR', '5', 'PUNCHED IN')";
            $id++;
            $rows[] = "({$id}, '1', 'PUNCHED IN', 'SUPERVISOR', '6', 'PUNCHED OUT')";
            $id++;
        }
        
        if (count($rows) > 0) {
            $sql = "INSERT INTO ohrm_workflow_state_machine(`id`, `workflow`, `state`, `role`, `action`, `resulting_state`) VALUES ";
            
            for ($i = 0; $i < count($rows); $i++) {
                if ($i > 0) {
                    $sql .= ', ';
                }
                $sql .= $rows[$i];
            }
            
            $result = $this->upgradeUtility->executeSql($sql);
            if (!$result) {
                $success = false;
            }            
        }

        return $success;        
    }
    
    protected function getNextWorkflowId() {
        $result = $this->upgradeUtility->executeSql('SELECT COALESCE(MAX(id),0) + 1 FROM `ohrm_workflow_state_machine`');
        $resultArray = $this->upgradeUtility->fetchArray($result);
        $nextId = $resultArray[0];
        return $nextId;
    }    

}
