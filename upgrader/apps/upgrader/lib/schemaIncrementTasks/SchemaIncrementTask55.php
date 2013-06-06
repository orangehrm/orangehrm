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
 *
 */

/**
 * Upgrade for menu changes, new ui changes and leave changes:
 * 
 * NOTE: Assumes that only the default Screen, data group entries are available.
 */
class SchemaIncrementTask55 extends SchemaIncrementTask {
    public $userInputs;
    
    protected $leavePeriodList = NULL;

    public function execute() {
        $this->incrementNumber = 55;
        parent::execute();

        $result = array();

        foreach ($this->sql as $sql) {
            $result[] = $this->upgradeUtility->executeSql($sql);
        }
        
        $this->addLeaveEntitlement();
        
        $this->checkTransactionComplete($result);
        
        $this->transactionComplete = true;
        
        $this->updateOhrmUpgradeInfo($this->transactionComplete, $this->incrementNumber);
        $this->upgradeUtility->finalizeTransaction($this->transactionComplete);
        $this->upgradeUtility->closeDbConnection();
    }

    public function getUserInputWidgets() {
        
    }

    public function setUserInputs() {
        
    }
    
    protected function getScalarValueFromQuery($query) {
        $result = $this->upgradeUtility->executeSql($query);
        $row = mysqli_fetch_row($result);
        
        $logMessage = print_r($row, true);
        UpgradeLogger::writeLogMessage($logMessage);
        $value = $row[0];        
        UpgradeLogger::writeLogMessage('value = ' . $value . ' value + 1 = ' . ($value + 1));        
        
        return $value + 1;        
    }
    
    protected function getNextUserRoleId() {
        return $this->getScalarValueFromQuery('SELECT MAX(id) FROM ohrm_user_role');
    }
    
    protected function getNextScreenId() {
        return $this->getScalarValueFromQuery('SELECT MAX(id) FROM ohrm_screen');
    }    
    
    protected function getNextDataGroupId() {
        return $this->getScalarValueFromQuery('SELECT MAX(id) FROM ohrm_data_group');       
    }
    
    protected function getOldLeavePeriodRecords() {
        $result = $this->upgradeUtility->executeSql('SELECT * FROM hs_hr_leave_period ORDER BY leave_period_id');
        
        $records = array();
        while ($row = mysqli_fetch_array($result)) {
            $records[] = $row;
        }
        
        return $records;
    }
    
    public function getLeavePeriodHistoryRecords($oldRecords) {        
        $history = array();
        $previousStartDate = NULL;
        $previousEndDate = NULL;
        
        foreach ($oldRecords as $row) {
            $startDate = new DateTime($row['leave_period_start_date']);
            $endDate = new DateTime($row['leave_period_end_date']);
            $startDay = $startDate->format('j');
            $startMonth = $startDate->format('n');
                      
            if (empty($history)) {
                $historyItem = array($startDay, $startMonth, $startDate->format('Y-m-d'));                
                $history[] = $historyItem;

                UpgradeLogger::writeLogMessage("leave_period_history item: " . print_r($historyItem, true));  
                
            } else {
                // only add record if the leave period changed
                if (($startDay != $previousStartDate->format('j')) ||
                        ($startMonth != $previousStartDate->format('n'))) {
                    $previousStartDate->add(new DateInterval('P1Y'));
                    $previousStartDate->sub(new DateInterval('P2D'));
                    $historyItem = array($startDay, $startMonth, $previousStartDate->format('Y-m-d'));
                    UpgradeLogger::writeLogMessage("leave_period_history item: " . print_r($historyItem, true));
                    $history[] = $historyItem;
                }                
            }            

            // check and handle if period is more than one year : means that end date was changed in the middle.
            $endDateIfPeriodIsOneYear = clone $startDate;
            $endDateIfPeriodIsOneYear->add(new DateInterval('P1Y'));
            $endDateIfPeriodIsOneYear->sub(new DateInterval('P1D'));
                        
            // leave period is more than one year.
            if ($endDate > $endDateIfPeriodIsOneYear) {
                UpgradeLogger::writeLogMessage("Leave Period " . $row['leave_period_start_date'] . " to " .
                        $row['leave_period_end_date'] . " is more than one year. Leave Period change detected.");
                
                $newStartDate = clone $endDate;
                $newStartDate->add(new DateInterval('P1D'));
                $newStartDay = $newStartDate->format('j');
                $newStartMonth = $newStartDate->format('n');
                $changedDate = clone $endDateIfPeriodIsOneYear;
                $changedDate->sub(new DateInterval('P30D'));
                
                $historyItem = array($newStartDay, $newStartMonth, $changedDate->format('Y-m-d'));
                UpgradeLogger::writeLogMessage("leave_period_history item: " . print_r($historyItem, true));                
                
                $history[] = $historyItem;
            }
            
            $previousStartDate = $startDate;
            $previousEndDate = $endDate;
        }

        return $history;      
    }

    public function loadSql() {

        /* ------------ New database tables ---------------- */

        $sql[] = "create table `ohrm_email` (
                    `id` int(6) not null auto_increment,
                    `name` varchar(100) not null unique,
                    primary key  (`id`),
                    unique key ohrm_email_name(`name`)
                  ) engine=innodb default charset=utf8;";

        $sql[] = "create table `ohrm_email_template` (
                    `id` int(6) not null auto_increment,
                    `email_id` int(6) not null,
                    `locale` varchar(20),
                    `performer_role` varchar(50) default null,
                    `recipient_role` varchar(50) default null,
                    `subject` varchar(255),
                    `body` text,
                    primary key  (`id`)
                  ) engine=innodb default charset=utf8;";


        $sql[] = "create table `ohrm_email_processor` (
                `id` int(6) not null auto_increment,
                `email_id` int(6) not null,
                `class_name` varchar(100),
                primary key  (`id`)
              ) engine=innodb default charset=utf8;";

        $sql[] = "create table `ohrm_menu_item` (
                    `id` int not null auto_increment, 
                    `menu_title` varchar(255) not null, 
                    `screen_id` int default null,
                    `parent_id` int default null,
                    `level` tinyint not null,
                    `order_hint` int not null,
                    `url_extras` varchar(255) default null, 
                    `status` tinyint not null default 1,
                    primary key (`id`)
                 ) engine=innodb default charset=utf8;";

        $sql[] = "CREATE TABLE ohrm_leave_type (
                    `id` int unsigned not null auto_increment,
                    `name` varchar(50) not null,
                    `deleted` tinyint(1) not null default 0,
                    `exclude_in_reports_if_no_entitlement` tinyint(1) not null default 0,
                    `operational_country_id` int unsigned default null,
                    primary key  (`id`)
                  ) engine=innodb default charset=utf8;";

        $sql[] = "CREATE TABLE ohrm_leave_entitlement_type(
                    `id` int unsigned not null auto_increment,
                    `name` varchar(50) not null,
                    `is_editable`  tinyint(1) not null default 0,
                    PRIMARY KEY(`id`)
                  )ENGINE = INNODB DEFAULT CHARSET=utf8;";

        $sql[] = "CREATE TABLE ohrm_leave_entitlement (
                    `id` int unsigned not null auto_increment,
                    emp_number int(7) not null,
                    no_of_days decimal(6,2) not null,
                    days_used decimal(4,2) not null default 0,
                    leave_type_id int unsigned not null,
                    from_date datetime not null,
                    to_date datetime,
                    credited_date datetime,
                    note varchar(255) default null, 
                    entitlement_type int unsigned not null,
                    `deleted` tinyint(1) not null default 0,
                    created_by_id int(10),
                    created_by_name varchar(255),
                    PRIMARY KEY(`id`)
                  ) ENGINE = INNODB DEFAULT CHARSET=utf8;";


        $sql[] = "CREATE TABLE ohrm_leave_adjustment (
                    `id` int unsigned not null auto_increment,
                    emp_number int(7) not null,
                    no_of_days decimal(6,2) not null,
                    leave_type_id int unsigned not null,
                    from_date datetime,
                    to_date datetime,
                    credited_date datetime,
                    note varchar(255) default null,
                    adjustment_type int unsigned not null default 0, 
                    `deleted` tinyint(1) not null default 0,
                    created_by_id int(10),
                    created_by_name varchar(255),
                    PRIMARY KEY(`id`)
                  ) ENGINE = INNODB DEFAULT CHARSET=utf8;";

        $sql[] = "CREATE TABLE `ohrm_leave_request` (
                    `id` int unsigned NOT NULL auto_increment,
                    `leave_type_id` int unsigned NOT NULL,
                    `date_applied` date NOT NULL,
                    `emp_number` int(7) NOT NULL,
                    `comments` varchar(256) default NULL,
                    PRIMARY KEY  (`id`)
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $sql[] = "CREATE TABLE `ohrm_leave` (
                    `id` int(11) NOT NULL  auto_increment,
                    `date` date default NULL,
                    `length_hours` decimal(6,2) unsigned default NULL,
                    `length_days` decimal(4,2) unsigned default NULL,
                    `status` smallint(6) default NULL,
                    `comments` varchar(256) default NULL,
                    `leave_request_id`int unsigned NOT NULL,
                    `leave_type_id` int unsigned NOT NULL,
                    `emp_number` int(7) NOT NULL,
                    `start_time` time default NULL,
                    `end_time` time default NULL,
                    PRIMARY KEY  (`id`),
                    KEY `leave_request_type_emp`(`leave_request_id`,`leave_type_id`,`emp_number`),
                    KEY `request_status` (`leave_request_id`,`status`)
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $sql[] = "CREATE TABLE `ohrm_leave_comment` (
          `id` int(11) NOT NULL  auto_increment,
          `leave_id` int(11) NOT NULL,
          `created` datetime default NULL,
          `created_by_name` varchar(255) NOT NULL,
          `created_by_id` int(10),
          `created_by_emp_number` int(7) default NULL,
          `comments` varchar(255) default NULL,
          PRIMARY KEY  (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $sql[] = "CREATE TABLE `ohrm_leave_request_comment` (
          `id` int(11) NOT NULL  auto_increment,
          `leave_request_id` int unsigned NOT NULL,
          `created` datetime default NULL,
          `created_by_name` varchar(255) NOT NULL,
          `created_by_id` int(10),
          `created_by_emp_number` int(7) default NULL,
          `comments` varchar(255) default NULL,
          PRIMARY KEY  (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $sql[] = "create TABLE `ohrm_leave_leave_entitlement` (
            `id` int(11) NOT NULL   auto_increment,
            `leave_id` int(11) NOT NULL,
            `entitlement_id` int unsigned NOT NULL,
            `length_days` decimal(4,2) unsigned default NULL,
            PRIMARY KEY  (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $sql[] = "create TABLE `ohrm_leave_entitlement_adjustment` (
            `id` int(11) NOT NULL   auto_increment,
            `adjustment_id` int unsigned NOT NULL,
            `entitlement_id` int unsigned NOT NULL,
            `length_days` decimal(4,2) unsigned default NULL,
            PRIMARY KEY  (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $sql[] = "CREATE TABLE `ohrm_leave_period_history` (
          `id` int(11) NOT NULL auto_increment,
          `leave_period_start_month` int NOT NULL,
          `leave_period_start_day` int NOT NULL,
          `created_at` date NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $sql[] = "CREATE TABLE `ohrm_leave_status` (
          `id` int(11) NOT NULL auto_increment,
          `status` smallint(6) NOT NULL,
          `name` varchar(100) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $sql[] = "create table `ohrm_advanced_report` (
          `id` int(10) not null,
          `name` varchar(100) not null,
          `definition` longtext not null,
          primary key (`id`)
        ) engine=innodb default charset=utf8;";

        /* ------------ Foreign key constraints for new tables ---------------- */        
        $sql[] = "alter table ohrm_leave_type
                add foreign key (operational_country_id)
                    references ohrm_operational_country(id) on delete set null;";

        $sql[] = "ALTER TABLE `ohrm_leave_entitlement`
            ADD CONSTRAINT FOREIGN KEY (`leave_type_id`) REFERENCES `ohrm_leave_type` (`id`) ON DELETE CASCADE,
            ADD CONSTRAINT FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE,
            ADD CONSTRAINT FOREIGN KEY (`entitlement_type`) REFERENCES `ohrm_leave_entitlement_type` (`id`) ON DELETE CASCADE,
            ADD CONSTRAINT FOREIGN KEY (`created_by_id`) REFERENCES `ohrm_user` (`id`) ON DELETE SET NULL;";

        $sql[] = "ALTER TABLE `ohrm_leave_adjustment`
        ADD CONSTRAINT FOREIGN KEY (`adjustment_type`) REFERENCES `ohrm_leave_entitlement_type` (`id`) ON DELETE CASCADE,
        ADD CONSTRAINT FOREIGN KEY (`leave_type_id`) REFERENCES `ohrm_leave_type` (`id`) ON DELETE CASCADE,
        ADD CONSTRAINT FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE,
        ADD CONSTRAINT FOREIGN KEY (`created_by_id`) REFERENCES `ohrm_user` (`id`) ON DELETE SET NULL;";


        $sql[] = "alter table ohrm_leave_request
            add constraint foreign key (emp_number) references hs_hr_employee (emp_number) on delete cascade,
            add constraint foreign key (leave_type_id) references ohrm_leave_type (id) on delete cascade;";

        $sql[] = "alter table ohrm_leave 
            add constraint foreign key (leave_request_id) references ohrm_leave_request(id) on delete cascade,
            add constraint foreign key (leave_type_id) references ohrm_leave_type (id) on delete cascade";

        $sql[] = "alter table ohrm_leave_leave_entitlement
            add constraint foreign key (entitlement_id) references ohrm_leave_entitlement (id) on delete cascade,
            add constraint foreign key (leave_id) references ohrm_leave (id) on delete cascade";
        $sql[] = "alter table ohrm_leave_entitlement_adjustment
            add constraint foreign key (entitlement_id) references ohrm_leave_entitlement (id) on delete cascade,
            add constraint foreign key (adjustment_id) references ohrm_leave_adjustment (id) on delete cascade;";

        $sql[] = "alter table ohrm_leave_comment
        add constraint foreign key (leave_id) references ohrm_leave(id) on delete cascade,
        add constraint foreign key (created_by_id) references ohrm_user(`id`) on delete set NULL,
        add constraint foreign key (created_by_emp_number) references hs_hr_employee(emp_number) on delete cascade;";

        $sql[] = "alter table ohrm_leave_request_comment
        add constraint foreign key (leave_request_id) references ohrm_leave_request(id) on delete cascade,
        add constraint foreign key (created_by_id) references ohrm_user(`id`) on delete set NULL,
        add constraint foreign key (created_by_emp_number) references hs_hr_employee(emp_number) on delete cascade;";

        $sql[] = "alter table ohrm_menu_item 
        add constraint foreign key (screen_id) references ohrm_screen(id) on delete cascade;";


        $sql[] = "alter table ohrm_email_template
        add foreign key (email_id) references ohrm_email(id) on delete cascade;";

        $sql[] = "alter table ohrm_email_processor
        add foreign key (email_id) references ohrm_email(id) on delete cascade;";


        /* ------------ Modifications to existing tables ---------------- */
        $sql[] = "alter table ohrm_data_group modify column `name` varchar(255) NOT NULL UNIQUE";

        /* ------------ Data Changes ---------------- */
        $sql[] = "INSERT INTO `hs_hr_config`(`key`, `value`) VALUES 
                    ('themeName', 'default'),
                    ('leave.entitlement_consumption_algorithm', 'FIFOEntitlementConsumptionStrategy'),
                    ('leave.work_schedule_implementation', 'BasicWorkSchedule'),
                    ('leave.leavePeriodStatus', 1),
                    ('leave.include_pending_leave_in_balance', 1)";

        /* ------------ Inserts to new tables -------------- */
        $sql[] = "INSERT INTO `ohrm_leave_entitlement_type` (`id`, `name`, `is_editable`) VALUES
                    (1, 'Added', 1);";
        
        $sql[] = "INSERT INTO `ohrm_advanced_report` (`id`, `name`, `definition`) VALUES
            (1, 'Leave Entitlements and Usage Report', '{$this->getReportForEmployee()}'),
            (2, 'Leave Entitlements and Usage Report', '{$this->getReportForLeaveType()}');";


        $sql[] = "INSERT INTO `ohrm_email` (`id`, `name`) VALUES
            (1, 'leave.apply'),
            (3, 'leave.approve'),
            (2, 'leave.assign'),
            (4, 'leave.cancel'),
            (6, 'leave.change'),
            (5, 'leave.reject');";

        $sql[] = "INSERT INTO `ohrm_email_processor` (`id`, `email_id`, `class_name`) VALUES
            (1, 1, 'LeaveEmailProcessor'),
            (2, 2, 'LeaveEmailProcessor'),
            (3, 3, 'LeaveEmailProcessor'),
            (4, 4, 'LeaveEmailProcessor'),
            (5, 5, 'LeaveEmailProcessor'),
            (6, 6, 'LeaveChangeMailProcessor');";

        $sql[] = "INSERT INTO `ohrm_email_template` (`id`, `email_id`, `locale`, `performer_role`, `recipient_role`, `subject`, `body`) VALUES
        (1, 1, 'en_US', NULL, 'supervisor', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/apply/leaveApplicationSubject.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/apply/leaveApplicationBody.txt'),
        (2, 1, 'en_US', NULL, 'subscriber', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/apply/leaveApplicationSubject.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/apply/leaveApplicationSubscriberBody.txt'),
        (3, 3, 'en_US', NULL, 'ess', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/approve/leaveApprovalSubject.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/approve/leaveApprovalBody.txt'),
        (4, 3, 'en_US', NULL, 'subscriber', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/approve/leaveApprovalSubscriberSubject.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/approve/leaveApprovalSubscriberBody.txt'),
        (5, 2, 'en_US', NULL, 'ess', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/assign/leaveAssignmentSubject.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/assign/leaveAssignmentBody.txt'),
        (6, 2, 'en_US', NULL, 'supervisor', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/assign/leaveAssignmentSubjectForSupervisors.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/assign/leaveAssignmentBodyForSupervisors.txt'),
        (7, 2, 'en_US', NULL, 'subscriber', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/assign/leaveAssignmentSubscriberSubject.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/assign/leaveAssignmentSubscriberBody.txt'),
        (8, 4, 'en_US', 'ess', 'supervisor', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/cancel/leaveEmployeeCancellationSubject.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/cancel/leaveEmployeeCancellationBody.txt'),
        (9, 4, 'en_US', 'ess', 'subscriber', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/cancel/leaveEmployeeCancellationSubscriberSubject.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/cancel/leaveEmployeeCancellationSubscriberBody.txt'),
        (10, 4, 'en_US', NULL, 'ess', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/cancel/leaveCancellationSubject.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/cancel/leaveCancellationBody.txt'),
        (11, 4, 'en_US', NULL, 'subscriber', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/cancel/leaveCancellationSubscriberSubject.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/cancel/leaveCancellationSubscriberBody.txt'),
        (12, 5, 'en_US', NULL, 'ess', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/reject/leaveRejectionSubject.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/reject/leaveRejectionBody.txt'),
        (13, 5, 'en_US', NULL, 'subscriber', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/reject/leaveRejectionSubscriberSubject.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/reject/leaveRejectionSubscriberBody.txt'),
        (14, 6, 'en_US', NULL, 'ess', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/change/leaveChangeSubject.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/change/leaveChangeBody.txt'),
        (15, 6, 'en_US', NULL, 'subscriber', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/change/leaveChangeSubscriberSubject.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/change/leaveChangeSubscriberBody.txt');";

        $sql[] = "INSERT INTO `ohrm_leave_status` (`id`, `status`, `name`) VALUES
                    (1, -1, 'REJECTED'),
                    (2, 0, 'CANCELLED'),
                    (3, 1, 'PENDING APPROVAL'),
                    (4, 2, 'SCHEDULED'),
                    (5, 3, 'TAKEN'),
                    (6, 4, 'WEEKEND'),
                    (7, 5, 'HOLIDAY');";        
        
        /*-------- Workflow State machine entries -------------*/        
        $sql[] = "alter table `ohrm_workflow_state_machine` 
            add column `roles_to_notify` text,
            add column `priority` int(11) NOT NULL DEFAULT '0' COMMENT 'lowest priority 0';";

        // make id field auto_increment for easier data insert
        $sql[] = "ALTER TABLE ohrm_workflow_state_machine MODIFY COLUMN `id` bigint(20) NOT NULL AUTO_INCREMENT;";
        
        $sql[] = "INSERT INTO `ohrm_workflow_state_machine` 
            (`workflow`, `state`, `role`, `action`, `resulting_state`, `roles_to_notify`, `priority`) VALUES
            ('4', 'INITIAL', 'ESS', 'APPLY', 'PENDING APPROVAL', 'supervisor,subscriber', 0),
            ('4', 'INITIAL', 'ADMIN', 'ASSIGN', 'SCHEDULED', 'ess,supervisor,subscriber', 0),
            ('4', 'INITIAL', 'SUPERVISOR', 'ASSIGN', 'SCHEDULED', 'ess,supervisor,subscriber', 0),
            ('4', 'PENDING APPROVAL', 'ADMIN', 'APPROVE', 'SCHEDULED', 'ess,subscriber', 0),
            ('4', 'PENDING APPROVAL', 'SUPERVISOR', 'APPROVE', 'SCHEDULED', 'ess,subscriber', 0),
            ('4', 'PENDING APPROVAL', 'ESS', 'CANCEL', 'CANCELLED', 'supervisor,subscriber', 0),
            ('4', 'PENDING APPROVAL', 'ADMIN', 'CANCEL', 'CANCELLED', 'ess,subscriber', 0),
            ('4', 'PENDING APPROVAL', 'SUPERVISOR', 'CANCEL', 'CANCELLED', 'ess,subscriber', 0),
            ('4', 'PENDING APPROVAL', 'ADMIN', 'REJECT', 'REJECTED', 'ess,subscriber', 0),
            ('4', 'PENDING APPROVAL', 'SUPERVISOR', 'REJECT', 'REJECTED', 'ess,subscriber', 0),
            ('4', 'SCHEDULED', 'ESS', 'CANCEL', 'CANCELLED', 'supervisor,subscriber', 0),
            ('4', 'SCHEDULED', 'ADMIN', 'CANCEL', 'CANCELLED', 'ess,subscriber', 0),
            ('4', 'SCHEDULED', 'SUPERVISOR', 'CANCEL', 'CANCELLED', 'ess,subscriber', 0),
            ('4', 'TAKEN', 'ADMIN', 'CANCEL', 'CANCELLED', 'ess,subscriber', 0),
            ('4', 'LEAVE TYPE DELETED PENDING APPROVAL', 'ESS', 'CANCEL', 'CANCELLED', 'supervisor,subscriber', 0),
            ('4', 'LEAVE TYPE DELETED PENDING APPROVAL', 'ADMIN', 'CANCEL', 'CANCELLED', 'ess,subscriber', 0),
            ('4', 'LEAVE TYPE DELETED PENDING APPROVAL', 'SUPERVISOR', 'CANCEL', 'CANCELLED', 'ess,subscriber', 0),
            ('4', 'LEAVE TYPE DELETED SCHEDULED', 'ESS', 'CANCEL', 'CANCELLED', 'supervisor,subscriber', 0),
            ('4', 'LEAVE TYPE DELETED SCHEDULED', 'ADMIN', 'CANCEL', 'CANCELLED', 'ess,subscriber', 0),
            ('4', 'LEAVE TYPE DELETED SCHEDULED', 'SUPERVISOR', 'CANCEL', 'CANCELLED', 'ess,subscriber', 0),
            ('4', 'LEAVE TYPE DELETED TAKEN', 'ADMIN', 'CANCEL', 'CANCELLED', 'ess,subscriber', 0);";

        // update last id
        $sql[] = "UPDATE `hs_hr_unique_id` SET
            last_id = (select MAX(`id`) FROM ohrm_workflow_state_machine) 
            WHERE table_name = 'ohrm_workflow_state_machine' AND `field_name` = 'id';";
        
        // Remove auto increment since this is not part of 3.0
        $sql[] = "ALTER TABLE ohrm_workflow_state_machine MODIFY COLUMN `id` bigint(20) NOT NULL;";
        
        /*-------- END Workflow State machine entries -------------*/  
        
        /*--------- Needs improvement for compatibility if data group, screen entries modified ----*/
        $sql[] = "DELETE FROM `ohrm_user_role` WHERE id = 6 AND name = 'Offerer';";

        $hiringManagerId = $this->getNextUserRoleId(); // 6
        $reviewerId = $hiringManagerId + 1; // 7
        
        $sql[] = "INSERT INTO `ohrm_user_role` 
                (`id`, `name`, `display_name`, `is_assignable`, `is_predefined`) VALUES
                ($hiringManagerId, 'HiringManager', 'HiringManager', 0, 1),
                ($reviewerId, 'Reviewer', 'Reviewer', 0, 1);";
        
        $startScreenId = $this->getNextScreenId(); //20
        $id = $startScreenId; 
        
        /*-------- Screen Entries -------*/
        $sql[] = "INSERT INTO ohrm_screen 
            (`id`, `name`, `module_id`, `action_url`) VALUES
            (" . ($id) . ", 'General Information', 2, 'viewOrganizationGeneralInformation'),
            (" . ($id+1) . ", 'Location List', 2, 'viewLocations'),
            (" . ($id+2) . ", 'View Company Structure', 2, 'viewCompanyStructure'),
            (" . ($id+3) . ", 'Job Title List', 2, 'viewJobTitleList'),
            (" . ($id+4) . ", 'Pay Grade List', 2, 'viewPayGrades'),
            (" . ($id+5) . ", 'Employment Status List', 2, 'employmentStatus'),
            (" . ($id+6) . ", 'Job Category List', 2, 'jobCategory'),
            (" . ($id+7) . ", 'Work Shift List', 2, 'workShift'),
            (" . ($id+8) . ", 'Skill List', 2, 'viewSkills'),
            (" . ($id+9) . ", 'Education List', 2, 'viewEducation'),
            (" . ($id+10) . ", 'License List', 2, 'viewLicenses'),
            (" . ($id+11) . ", 'Language List', 2, 'viewLanguages'),
            (" . ($id+12) . ", 'Membership List', 2, 'membership'),
            (" . ($id+13) . ", 'Nationality List', 2, 'nationality'),
            (" . ($id+14) . ", 'Add/Edit Mail Configuration', 2, 'listMailConfiguration'),
            (" . ($id+15) . ", 'Notification List', 2, 'viewEmailNotification'),
            (" . ($id+16) . ", 'Customer List', 2, 'viewCustomers'),
            (" . ($id+17) . ", 'Project List', 2, 'viewProjects'),
            (" . ($id+18) . ", 'Localization', 2, 'localization'),
            (" . ($id+19) . ", 'Module Configuration', 2, 'viewModules'),
            (" . ($id+20) . ", 'Configure PIM', 3, 'configurePim'),
            (" . ($id+21) . ", 'Custom Field List', 3, 'listCustomFields'),
            (" . ($id+22) . ", 'Data Import', 2, 'pimCsvImport'),
            (" . ($id+23) . ", 'Reporting Method List', 3, 'viewReportingMethods'),
            (" . ($id+24) . ", 'Termination Reason List', 3, 'viewTerminationReasons'),
            (" . ($id+25) . ", 'PIM Reports List', 1, 'viewDefinedPredefinedReports'),
            (" . ($id+26) . ", 'View MyInfo', 3, 'viewMyDetails'),
            (" . ($id+27) . ", 'Define Leave Period', 4, 'defineLeavePeriod'),
            (" . ($id+28) . ", 'View My Leave List', 4, 'viewMyLeaveList'),
            (" . ($id+29) . ", 'Apply Leave', 4, 'applyLeave'),
            (" . ($id+30) . ", 'Define Timesheet Start Date', 5, 'defineTimesheetPeriod'),
            (" . ($id+31) . ", 'View My Timesheet', 5, 'viewMyTimesheet'),
            (" . ($id+32) . ", 'View Employee Timesheet', 5, 'viewEmployeeTimesheet'),
            (" . ($id+33) . ", 'View My Attendance', 6, 'viewMyAttendanceRecord'),
            (" . ($id+34) . ", 'Punch In/Out', 6, 'punchIn'),
            (" . ($id+35) . ", 'View Employee Attendance', 6, 'viewAttendanceRecord'),
            (" . ($id+36) . ", 'Attendance Configuration', 6, 'configure'),
            (" . ($id+37) . ", 'View Employee Report Criteria', 5, 'displayProjectReportCriteria'),
            (" . ($id+38) . ", 'View Project Report Criteria', 5, 'displayEmployeeReportCriteria'),
            (" . ($id+39) . ", 'View Attendance Report Criteria', 5, 'displayAttendanceSummaryReportCriteria'),
            (" . ($id+40) . ", 'Candidate List', 7, 'viewCandidates'),
            (" . ($id+41) . ", 'Vacancy List', 7, 'viewJobVacancy'),
            (" . ($id+42) . ", 'KPI List', 9, 'listDefineKpi'),
            (" . ($id+43) . ", 'Add/Edit KPI', 9, 'saveKpi'),
            (" . ($id+44) . ", 'Copy KPI', 9, 'copyKpi'),
            (" . ($id+45) . ", 'Add Review', 9, 'saveReview'),
            (" . ($id+46) . ", 'Review List', 9, 'viewReview'),
            (" . ($id+47) . ", 'View Time Module', 5, 'viewTimeModule'),
            (" . ($id+48) . ", 'View Leave Module', 4, 'viewLeaveModule'),
            (" . ($id+49) . ", 'Leave Entitlements', 4, 'viewLeaveEntitlements'),
            (" . ($id+50) . ", 'My Leave Entitlements', 4, 'viewMyLeaveEntitlements'),
            (" . ($id+51) . ", 'Delete Leave Entitlements', 4, 'deleteLeaveEntitlements'),
            (" . ($id+52) . ", 'Add Leave Entitlement', 4, 'addLeaveEntitlement'),
            (" . ($id+53) . ", 'Edit Leave Entitlement', 4, 'editLeaveEntitlement'),
            (" . ($id+54) . ", 'View Admin Module', 2, 'viewAdminModule'),
            (" . ($id+55) . ", 'View PIM Module', 3, 'viewPimModule'),
            (" . ($id+56) . ", 'View Recruitment Module', 7, 'viewRecruitmentModule'),
            (" . ($id+57) . ", 'View Performance Module', 9, 'viewPerformanceModule'),
            (" . ($id+58) . ", 'Leave Balance Report', 4, 'viewLeaveBalanceReport'),
            (" . ($id+59) . ", 'My Leave Balance Report', 4, 'viewMyLeaveBalanceReport');";


        // Menu entries relative to the start id
        // Generated by: 
        // select concat('(', id, ", '",  menu_title, "', ", IFNULL(concat('" . ', '$id + ', (screen_id - 20), ' . "'), 
        // 'NULL'), ", ", IFNULL(parent_id, 'NULL'), ", ", level, ", ", order_hint, ", ", 
        // IFNULL(CONCAT('\'', url_extras, '\''), 'NULL'), ", ", status, "),") as row from ohrm_menu_item;
        $sql[] = "INSERT INTO ohrm_menu_item 
            (`id`, `menu_title`, `screen_id`, `parent_id`, `level`, `order_hint`, `url_extras`, `status`) VALUES
            (1, 'Admin', " . ($id + 54) . ", NULL, 1, 100, NULL, 1),                                     
            (2, 'User Management', NULL, 1, 2, 100, NULL, 1),                                          
            (3, 'Project Info', NULL, 52, 2, 400, NULL, 0),                                            
            (4, 'Customers', " . ($id + 16) . ", 3, 3, 100, NULL, 0),                                    
            (5, 'Projects', " . ($id + 17) . ", 3, 3, 200, NULL, 0),                                     
            (6, 'Job', NULL, 1, 2, 300, NULL, 1),                                                      
            (7, 'Job Titles', " . ($id + 3) . ", 6, 3, 100, NULL, 1),                                    
            (8, 'Pay Grades', " . ($id + 4) . ", 6, 3, 200, NULL, 1),                                    
            (9, 'Employment Status', " . ($id + 5) . ", 6, 3, 300, NULL, 1),                             
            (10, 'Job Categories', " . ($id + 6) . ", 6, 3, 400, NULL, 1),                               
            (11, 'Work Shifts', " . ($id + 7) . ", 6, 3, 500, NULL, 1),                                  
            (12, 'Organization', NULL, 1, 2, 400, NULL, 1),                                            
            (13, 'General Information', " . ($id + 0) . ", 12, 3, 100, NULL, 1),                         
            (14, 'Locations', " . ($id + 1) . ", 12, 3, 200, NULL, 1),                                   
            (15, 'Structure', " . ($id + 2) . ", 12, 3, 300, NULL, 1),                                   
            (16, 'Qualifications', NULL, 1, 2, 500, NULL, 1),                                          
            (17, 'Skills', " . ($id + 8) . ", 16, 3, 100, NULL, 1),                                      
            (18, 'Education', " . ($id + 9) . ", 16, 3, 200, NULL, 1),                                   
            (19, 'Licenses', " . ($id + 10) . ", 16, 3, 300, NULL, 1),                                   
            (20, 'Languages', " . ($id + 11) . ", 16, 3, 400, NULL, 1),                                  
            (21, 'Memberships', " . ($id + 12) . ", 16, 3, 500, NULL, 1),                                
            (22, 'Nationalities', " . ($id + 13) . ", 1, 2, 700, NULL, 1),                               
            (23, 'Configuration', NULL, 1, 2, 900, NULL, 1),                                           
            (24, 'Email Configuration', " . ($id + 14) . ", 23, 3, 100, NULL, 1),                        
            (25, 'Email Subscriptions', " . ($id + 15) . ", 23, 3, 200, NULL, 1),                        
            (27, 'Localization', " . ($id + 18) . ", 23, 3, 300, NULL, 1),                               
            (28, 'Modules', " . ($id + 19) . ", 23, 3, 400, NULL, 1),                                    
            (30, 'PIM', " . ($id + 55) . ", NULL, 1, 200, NULL, 1),                                      
            (31, 'Configuration', NULL, 30, 2, 100, NULL, 1),                                          
            (32, 'Optional Fields', " . ($id + 20) . ", 31, 3, 100, NULL, 1),                            
            (33, 'Custom Fields', " . ($id + 21) . ", 31, 3, 200, NULL, 1),                              
            (34, 'Data Import', " . ($id + 22) . ", 31, 3, 300, NULL, 1),                                
            (35, 'Reporting Methods', " . ($id + 23) . ", 31, 3, 400, NULL, 1),                          
            (36, 'Termination Reasons', " . ($id + 24) . ", 31, 3, 500, NULL, 1),                        
            (37, 'Employee List', 5, 30, 2, 200, '/reset/1', 1),                       
            (38, 'Add Employee', 4, 30, 2, 300, NULL, 1),                              
            (39, 'Reports', " . ($id + 25) . ", 30, 2, 400, '/reportGroup/3/reportType/PIM_DEFINED', 1), 
            (40, 'My Info', " . ($id + 26) . ", NULL, 1, 700, NULL, 1),                                  
            (41, 'Leave', " . ($id + 48) . ", NULL, 1, 300, NULL, 1),                                    
            (42, 'Configure', NULL, 41, 2, 500, NULL, 0),                                              
            (43, 'Leave Period', " . ($id + 27) . ", 42, 3, 100, NULL, 0),                               
            (44, 'Leave Types', 7, 42, 3, 200, NULL, 0),                               
            (45, 'Work Week', 14, 42, 3, 300, NULL, 0),                                  
            (46, 'Holidays', 11, 42, 3, 400, NULL, 0),                                   
            (48, 'Leave List', 16, 41, 2, 600, '/reset/1', 0),                           
            (49, 'Assign Leave', 17, 41, 2, 700, NULL, 0),                               
            (50, 'My Leave', " . ($id + 28) . ", 41, 2, 200, '/reset/1', 0),                             
            (51, 'Apply', " . ($id + 29) . ", 41, 2, 100, NULL, 0),                                      
            (52, 'Time', " . ($id + 47) . ", NULL, 1, 400, NULL, 1),                                     
            (53, 'Timesheets', NULL, 52, 2, 100, NULL, 1),                                             
            (54, 'My Timesheets', " . ($id + 31) . ", 53, 3, 100, NULL, 0),                              
            (55, 'Employee Timesheets', " . ($id + 32) . ", 53, 3, 200, NULL, 0),                        
            (56, 'Attendance', NULL, 52, 2, 200, NULL, 1),                                             
            (57, 'My Records', " . ($id + 33) . ", 56, 3, 100, NULL, 0),                                 
            (58, 'Punch In/Out', " . ($id + 34) . ", 56, 3, 200, NULL, 0),                               
            (59, 'Employee Records', " . ($id + 35) . ", 56, 3, 300, NULL, 0),                           
            (60, 'Configuration', " . ($id + 36) . ", 56, 3, 400, NULL, 0),                              
            (61, 'Reports', NULL, 52, 2, 300, NULL, 1),                                                
            (62, 'Project Reports', " . ($id + 37) . ", 61, 3, 100, '?reportId=1', 0),                   
            (63, 'Employee Reports', " . ($id + 38) . ", 61, 3, 200, '?reportId=2', 0),                  
            (64, 'Attendance Summary', " . ($id + 39) . ", 61, 3, 300, '?reportId=4', 0),                
            (65, 'Recruitment', " . ($id + 56) . ", NULL, 1, 500, NULL, 1),                              
            (66, 'Candidates', " . ($id + 40) . ", 65, 2, 100, NULL, 1),                                 
            (67, 'Vacancies', " . ($id + 41) . ", 65, 2, 200, NULL, 1),                                  
            (68, 'Performance', " . ($id + 57) . ", NULL, 1, 600, NULL, 1),                              
            (69, 'KPI List', " . ($id + 42) . ", 68, 2, 100, NULL, 1),                                   
            (70, 'Add KPI', " . ($id + 43) . ", 68, 2, 200, NULL, 1),                                    
            (71, 'Copy KPI', " . ($id + 44) . ", 68, 2, 300, NULL, 1),                                   
            (72, 'Add Review', " . ($id + 45) . ", 68, 2, 400, NULL, 1),                                 
            (73, 'Reviews', " . ($id + 46) . ", 68, 2, 500, '/mode/new', 1),                             
            (74, 'Entitlements', NULL, 41, 2, 300, NULL, 0),                                           
            (75, 'Add Entitlements', " . ($id + 52) . ", 74, 3, 100, NULL, 0),                           
            (76, 'My Entitlements', " . ($id + 50) . ", 74, 3, 300, '/reset/1', 0),                      
            (77, 'Employee Entitlements', " . ($id + 49) . ", 74, 3, 200, '/reset/1', 0),                
            (78, 'Reports', NULL, 41, 2, 400, NULL, 0),                                                
            (79, 'Leave Entitlements and Usage Report', " . ($id + 58) . ", 78, 3, 100, NULL, 0),        
            (80, 'My Leave Entitlements and Usage Report', " . ($id + 59) . ", 78, 3, 200, NULL, 0),     
            (81, 'Users', 1, 2, 3, 100, NULL, 1);";

        /** TODO: Improve here to support upgrading installs with modified user role tables. */
        $sql[] = "DELETE FROM ohrm_user_role_screen WHERE 
            (user_role_id = 2 AND screen_id = 1) OR 
            (user_role_id = 3 AND screen_id = 1)";
        
        $sql[] = "UPDATE ohrm_user_role_screen SET user_role_id = 3 WHERE 
            user_role_id = 2 AND (screen_id = 16 OR screen_id = 17)";
        
        $sql[] = "ALTER TABLE ohrm_user_role_screen AUTO_INCREMENT = 0";

        $sql[] = "INSERT INTO ohrm_user_role_screen (user_role_id, screen_id, can_read, can_create, can_update, can_delete) VALUES
            (1, " . ($id + 0) . ", 1, 1, 1, 1),  
            (1, " . ($id + 1) . ", 1, 1, 1, 1),  
            (1, " . ($id + 2) . ", 1, 1, 1, 1),  
            (1, " . ($id + 3) . ", 1, 1, 1, 1),  
            (1, " . ($id + 4) . ", 1, 1, 1, 1),  
            (1, " . ($id + 5) . ", 1, 1, 1, 1),  
            (1, " . ($id + 6) . ", 1, 1, 1, 1),  
            (1, " . ($id + 7) . ", 1, 1, 1, 1),  
            (1, " . ($id + 8) . ", 1, 1, 1, 1),  
            (1, " . ($id + 9) . ", 1, 1, 1, 1),  
            (1, " . ($id + 10) . ", 1, 1, 1, 1), 
            (1, " . ($id + 11) . ", 1, 1, 1, 1), 
            (1, " . ($id + 12) . ", 1, 1, 1, 1), 
            (1, " . ($id + 13) . ", 1, 1, 1, 1), 
            (1, " . ($id + 14) . ", 1, 1, 1, 1), 
            (1, " . ($id + 15) . ", 1, 1, 1, 1), 
            (1, " . ($id + 16) . ", 1, 1, 1, 1), 
            (1, " . ($id + 17) . ", 1, 1, 1, 1), 
            (4, " . ($id + 17) . ", 1, 0, 0, 0), 
            (1, " . ($id + 18) . ", 1, 1, 1, 1), 
            (1, " . ($id + 19) . ", 1, 1, 1, 1), 
            (1, " . ($id + 20) . ", 1, 1, 1, 1), 
            (1, " . ($id + 21) . ", 1, 1, 1, 1), 
            (1, " . ($id + 22) . ", 1, 1, 1, 1), 
            (1, " . ($id + 23) . ", 1, 1, 1, 1), 
            (1, " . ($id + 24) . ", 1, 1, 1, 1), 
            (1, " . ($id + 25) . ", 1, 1, 1, 1), 
            (2, " . ($id + 26) . ", 1, 1, 1, 1), 
            (1, " . ($id + 27) . ", 1, 1, 1, 1), 
            (2, " . ($id + 28) . ", 1, 1, 1, 0), 
            (2, " . ($id + 29) . ", 1, 1, 1, 1), 
            (1, " . ($id + 30) . ", 1, 1, 1, 1), 
            (2, " . ($id + 30) . ", 1, 0, 0, 0), 
            (2, " . ($id + 31) . ", 1, 1, 1, 1), 
            (1, " . ($id + 32) . ", 1, 1, 1, 1), 
            (3, " . ($id + 32) . ", 1, 1, 1, 1), 
            (2, " . ($id + 33) . ", 1, 1, 0, 0), 
            (2, " . ($id + 34) . ", 1, 1, 1, 1), 
            (1, " . ($id + 35) . ", 1, 1, 0, 1), 
            (3, " . ($id + 35) . ", 1, 1, 0, 0), 
            (1, " . ($id + 36) . ", 1, 1, 1, 1), 
            (1, " . ($id + 37) . ", 1, 1, 1, 1), 
            (4, " . ($id + 37) . ", 1, 1, 1, 1), 
            (1, " . ($id + 38) . ", 1, 1, 1, 1), 
            (3, " . ($id + 38) . ", 1, 1, 1, 1), 
            (1, " . ($id + 39) . ", 1, 1, 1, 1), 
            (3, " . ($id + 39) . ", 1, 1, 1, 1), 
            (1, " . ($id + 40) . ", 1, 1, 1, 1), 
            ($hiringManagerId, " . ($id + 40) . ", 1, 1, 1, 1), 
            (5, " . ($id + 40) . ", 1, 0, 1, 0), 
            (1, " . ($id + 41) . ", 1, 1, 1, 1), 
            (1, " . ($id + 42) . ", 1, 1, 1, 1), 
            (1, " . ($id + 43) . ", 1, 1, 1, 1), 
            (1, " . ($id + 44) . ", 1, 1, 1, 1), 
            (1, " . ($id + 45) . ", 1, 1, 1, 1), 
            (1, " . ($id + 46) . ", 1, 1, 1, 1), 
            (2, " . ($id + 46) . ", 1, 0, 1, 0), 
            ($reviewerId, " . ($id + 46) . ", 1, 0, 1, 0), 
            (1, " . ($id + 47) . ", 1, 1, 1, 1), 
            (2, " . ($id + 47) . ", 1, 0, 1, 0), 
            (3, " . ($id + 47) . ", 1, 0, 1, 0), 
            (1, " . ($id + 48) . ", 1, 1, 1, 1), 
            (2, " . ($id + 48) . ", 1, 0, 1, 0), 
            (3, " . ($id + 48) . ", 1, 0, 1, 0), 
            (1, " . ($id + 49) . ", 1, 1, 1, 1), 
            (3, " . ($id + 49) . ", 1, 0, 0, 0), 
            (2, " . ($id + 50) . ", 1, 0, 0, 0), 
            (1, " . ($id + 51) . ", 1, 0, 0, 1), 
            (1, " . ($id + 52) . ", 1, 1, 1, 0), 
            (1, " . ($id + 53) . ", 1, 0, 1, 0), 
            (1, " . ($id + 54) . ", 1, 1, 1, 1), 
            (1, " . ($id + 55) . ", 1, 1, 1, 1), 
            (3, " . ($id + 55) . ", 1, 1, 1, 1), 
            (1, " . ($id + 56) . ", 1, 1, 1, 1), 
            (5, " . ($id + 56) . ", 1, 1, 1, 1), 
            ($hiringManagerId, " . ($id + 56) . ", 1, 1, 1, 1), 
            (1, " . ($id + 57) . ", 1, 1, 1, 1), 
            (2, " . ($id + 57) . ", 1, 1, 1, 1), 
            ($reviewerId, " . ($id + 57) . ", 1, 1, 1, 1), 
            (1, " . ($id + 58) . ", 1, 0, 0, 0), 
            (3, " . ($id + 58) . ", 1, 0, 0, 0), 
            (2, " . ($id + 59) . ", 1, 0, 0, 0);";

        /* -- Enable time module menu items if timesheet period is defined -- */
        $sql[] = "UPDATE `ohrm_menu_item` menu
            SET `status` = (select if(value = 'Yes', 1, 0)  from hs_hr_config where `key` = 'timesheet_period_set') 
            WHERE parent_id is not null and screen_id in 
            (select s.id from ohrm_screen s left join ohrm_module m on s.module_id = m.id 
            where m.`name` IN ('time', 'attendance'))
            OR (menu.menu_title in ('Project Info', 'Customers', 'Projects'));";
     

        /* -- Enable leave module menu items if leave period is defined */
        $sql[] = "UPDATE `ohrm_menu_item` 
            SET `status` = (select if(value = 'Yes', 1, 0)  from hs_hr_config where `key` = 'leave_period_defined') 
            WHERE parent_id is not null and screen_id in 
            (select s.id from ohrm_screen s left join ohrm_module m on s.module_id = m.id where m.`name` = 'leave')";
                
        /* ----------- Start Data group related data ---------- */

        $sql[] = "DELETE FROM ohrm_data_group WHERE name = 'leave_summary';";
        $nextDataGroupId = $this->getNextDataGroupId();
        $entitlementsDataGroupId = $nextDataGroupId;
        $reportDataGroupId = $nextDataGroupId + 1;

        $sql[] = "INSERT INTO `ohrm_data_group` (`id`, `name`, `description`, `can_read`, `can_create`, `can_update`, `can_delete`) VALUES
            ($entitlementsDataGroupId, 'leave_entitlements', 'Leave - Leave Entitlements', 1, 1, 1, 1),
            ($reportDataGroupId, 'leave_entitlements_usage_report', 'Leave - Leave Entitlements and Usage Report', 1, NULL, NULL, NULL);";

        $sql[] = "INSERT INTO `ohrm_user_role_data_group` 
            (`user_role_id`, `data_group_id`, `can_read`, `can_create`, `can_update`, `can_delete`, `self`) VALUES

            (1, $entitlementsDataGroupId, 1, 1, 1, 1, 0),
            (1, $reportDataGroupId, 1, NULL, NULL, NULL, 0),
 
            (1, $entitlementsDataGroupId, 1, 1, 1, 1, 1),
            (1, $reportDataGroupId, 1, NULL, NULL, NULL, 1),
         
            (2, $entitlementsDataGroupId, 1, 0, 0, 0, 1),
            (2, $reportDataGroupId, 1, NULL, NULL, NULL, 1),
            
            (3, $entitlementsDataGroupId, 1, 0, 0, 0, 0),
            (3, $reportDataGroupId, 1, NULL, NULL, NULL, 0),
            
            (3, $entitlementsDataGroupId, 1, 0, 0, 0, 1),
            (3, $reportDataGroupId, 1, NULL, NULL, NULL, 1);";

        /* ----------- End Data group related data ---------- */        
        
        /* insert leave period data to ohrm_leave_period_history */
        $oldRecords = $this->getOldLeavePeriodRecords();
        $leavePeriodHistory = $this->getLeavePeriodHistoryRecords($oldRecords);
        
        $leavePeriodHistorySql = "INSERT INTO `ohrm_leave_period_history` 
            (`leave_period_start_day`, `leave_period_start_month`, `created_at`) VALUES ";
        
        for ($i = 0; $i < count($leavePeriodHistory); $i++) {
            $comma = ($i == 0) ? '' : ', ';
            $leavePeriodHistorySql .= "{$comma}({$leavePeriodHistory[$i][0]}, {$leavePeriodHistory[$i][1]}, '{$leavePeriodHistory[$i][2]}')";
        }

        $sql[] = $leavePeriodHistorySql;
        
        /* ------------ Importing leave type and entitlement data -------------- */        
        $sql[] = "alter table `hs_hr_leavetype` add column int_id int not null auto_increment unique key;";

        $sql[] = "INSERT INTO `ohrm_leave_type` (`id`, `name`, `deleted`, `operational_country_id`)
                            SELECT old_lt.`int_id`, old_lt.`leave_type_name`, 
                            IF(old_lt.`available_flag` = 1, 0, 1) , old_lt.`operational_country_id`
                            FROM `hs_hr_leavetype` old_lt;";

        $sql[] = "INSERT INTO `ohrm_leave_entitlement`(emp_number, no_of_days, leave_type_id, from_date, to_date, 
                                        credited_date, note, entitlement_type, `deleted`)
                    SELECT q.employee_id, q.no_of_days_allotted, lt.int_id, p.leave_period_start_date, p.leave_period_end_date, 
                    p.leave_period_start_date, 'Author not tracked prior to v3.0', 1, 0
                    FROM `hs_hr_employee_leave_quota` q, `hs_hr_leavetype` lt, hs_hr_leave_period p WHERE lt.leave_type_id = q.leave_type_id AND p.leave_period_id = q.leave_period_id
                    AND ((q.no_of_days_allotted <> 0) or (q.leave_brought_forward <> 0) or (q.leave_carried_forward <> 0));";

        /* insert data to ohrm_leave_request */
        $sql[] = "INSERT INTO `ohrm_leave_request` (`id`, `leave_type_id`, `date_applied`, `emp_number`, `comments`)
		SELECT old_lr.`leave_request_id`, (SELECT old_lt.int_id FROM `hs_hr_leavetype` old_lt WHERE old_lt.leave_type_id = old_lr.leave_type_id), old_lr.`date_applied`, old_lr.`employee_id`, old_lr.`leave_comments` FROM  hs_hr_leave_requests old_lr;";





        $sql[] = "SET foreign_key_checks = 0;";

        $sql[] = "UPDATE `hs_hr_employee_leave_quota` lq SET lq.`leave_type_id` = (SELECT lt.`int_id` FROM `hs_hr_leavetype` lt WHERE lt.`leave_type_id` = lq.`leave_type_id`);";

        //$sql[] = "alter table `hs_hr_employee_leave_quota` add column new_leave_start_date date not null;";
        //$sql[] = "alter table `hs_hr_employee_leave_quota` add column new_leave_end_date date not null;";
//$sql[] = "UPDATE `hs_hr_employee_leave_quota` lq SET lq.new_leave_start_date = (SELECT olp.leave_period_start_date FROM ohrm_leave_period olp WHERE olp.leave_period_id = lq.leave_period_id), lq.new_leave_end_date = (SELECT olp.leave_period_end_date FROM ohrm_leave_period olp WHERE olp.leave_period_id = lq.leave_period_id);";
//taking leave_taken is NOT correct, should take SUM(leave_length_days) 
//$sql[] = "UPDATE `ohrm_leave_entitlement` le SET le.`days_used` = (SELECT old_lq.`leave_taken` FROM `hs_hr_employee_leave_quota` old_lq WHERE old_lq.`employee_id` = le.`emp_number` AND old_lq.`leave_type_id` = le.leave_type_id AND old_lq.new_leave_start_date = DATE(le.from_date) AND old_lq.new_leave_end_date = DATE(le.to_date));";



        $sql[] = "UPDATE `hs_hr_leave` lq SET lq.`leave_type_id` = (SELECT lt.`int_id` FROM `hs_hr_leavetype` lt WHERE lt.leave_type_id = lq.leave_type_id);";


        //$sql[] = "alter table `hs_hr_leave` add column int_leave_type_id int not null;";


        $sql[] = "INSERT INTO `ohrm_leave` (`id`, `date`, `length_hours`, `length_days`, `status`, `comments`, `leave_request_id`, `leave_type_id`, `emp_number`, `start_time`, `end_time`) 
    SELECT old_l.leave_id, old_l.leave_date, old_l.leave_length_hours, old_l.leave_length_days, old_l.leave_status, old_l.leave_comments, old_l.leave_request_id, old_l.`leave_type_id`, old_l.employee_id, old_l.`start_time`, old_l.`end_time` FROM `hs_hr_leave` old_l;";

        //newly added - must be changed again, days used must be updated
        $sql[] = "UPDATE `ohrm_leave_entitlement` le SET le.`days_used` = (SELECT SUM(l.`length_days`) FROM `ohrm_leave` l WHERE l.`emp_number` = le.`emp_number` AND l.`leave_type_id` = le.leave_type_id AND l.date BETWEEN le.from_date AND le.to_date);";


        $sql[] = "alter table `ohrm_leave` add column new_entitlement_id int not null;";

        //this is incorrect
        //$sql[] = "UPDATE `ohrm_leave` l SET l.`new_entitlement_id` = (SELECT lp.leave_period_id FROM ohrm_leave_period lp WHERE l.date BETWEEN lp.leave_period_start_date AND lp.leave_period_end_date);";
        //$sql[] = "UPDATE `ohrm_leave` l SET l.`new_entitlement_id` = (SELECT le.id FROM ohrm_leave_entitlement le WHERE le.emp_number = l.emp_number AND le.leave_type_id = l.leave_type_id  AND (l.date BETWEEN le.from_date AND le.to_date));";
        $sql[] = "UPDATE `ohrm_leave` l SET l.`new_entitlement_id` = (SELECT le.id FROM ohrm_leave_entitlement le WHERE le.emp_number = l.emp_number AND le.leave_type_id = l.leave_type_id  AND (l.date >= le.from_date AND l.date <= le.to_date));";


        //instead of inserting from here, insert from the php script
        //$sql[] = "INSERT INTO `ohrm_leave_leave_entitlement` (`leave_id`, `entitlement_id`, `length_days`) 
        //SELECT l.`id`, l.`new_entitlement_id`, l.length_days FROM `ohrm_leave` l WHERE l.status<>4 AND l.status<>5;";
        //add leave request comments
        $sql[] = "INSERT INTO `ohrm_leave_request_comment` (`leave_request_id`, `created`, `created_by_name`, `created_by_id`, `created_by_emp_number`, `comments`)
    SELECT l.`id`, now(), 'Author not tracked prior to v3.0', 1, (SELECT u.`emp_number` FROM `ohrm_user` u WHERE u.`id` = 1), l.`comments` FROM `ohrm_leave_request` l;";

        //add leave comments
        $sql[] = "INSERT INTO `ohrm_leave_comment` (`leave_id`, `created`, `created_by_name`, `created_by_id`, `created_by_emp_number`, `comments`)
SELECT l.`id`, now(), 'Author not tracked prior to v3.0', 1, (SELECT u.`emp_number` FROM `ohrm_user` u WHERE u.`id` = 1), l.`comments` FROM `ohrm_leave` l;";

        //Update Time (hours) to Time (Hours) - labels should be consistent for gettin the total
        $sql[] = "UPDATE `ohrm_summary_display_field` SET `label` = 'Time (Hours)' WHERE `summary_display_field_id` = 2;";

       // keep since useful for add-on upgraders
       // $sql[] = "alter table `hs_hr_leavetype` drop column int_id;";


//        $sql[] = "DROP TABLE `hs_hr_employee_leave_quota`;";
//        $sql[] = "DROP TABLE `hs_hr_empreport`;";
//        $sql[] = "DROP TABLE `hs_hr_emprep_usergroup`;";
//        $sql[] = "DROP TABLE `hs_hr_hsp`;";
//        $sql[] = "DROP TABLE `hs_hr_hsp_payment_request`;";
//        $sql[] = "DROP TABLE `hs_hr_hsp_summary`;";
//        $sql[] = "DROP TABLE `hs_hr_leave`;";
//        $sql[] = "DROP TABLE `hs_hr_leavetype`;";
//        $sql[] = "DROP TABLE `hs_hr_leave_period`;";
//        $sql[] = "DROP TABLE `hs_hr_leave_requests`;";
//        $sql[] = "DROP TABLE `hs_hr_rights`;";
//        $sql[] = "DROP TABLE `hs_hr_user_group`;";

        $sql[] = "SET foreign_key_checks = 1;";


        $this->sql = $sql;
    }
    
    protected function addLeaveEntitlement() {
        $result = $this->upgradeUtility->executeSql("SELECT * FROM `ohrm_leave`");

        if (!$result) {
            throw new Exception("SELECT * FROM ohrm_leave failed");
        }
        
        while ($leave = mysqli_fetch_array($result)) {

            $status = $leave['status'];
            $new_entitlement_id = $leave['new_entitlement_id'];
            $length_days = $leave['length_days'];
            $leave_id = $leave['id'];
            $emp_number = $leave['emp_number'];
            $leave_type_id = $leave['leave_type_id'];
            $date = $leave['date'];

            if (($status != 4) && ($status != 5)) {
                $lengthCountRes = $this->upgradeUtility->executeSql("SELECT SUM(`length_days`) FROM `ohrm_leave` WHERE `new_entitlement_id` = " . $new_entitlement_id);
                if (!$lengthCountRes) {
                    throw new Exception("lengthCountRes failed" . mysqli_e);
                }
                $lengthCountRow = mysqli_fetch_row($lengthCountRes);
                $leave_sum = $lengthCountRow[0];

                $daysUsedRes = $this->upgradeUtility->executeSql("SELECT `no_of_days` - `days_used` FROM `ohrm_leave_entitlement` WHERE `id` = " . $new_entitlement_id);
                if (!$daysUsedRes) {
                    throw new Exception("query failed");
                }

                $daysUsedResRow = mysqli_fetch_row($daysUsedRes);
                $curr_bal = $daysUsedResRow[0];

                //if no matching leave quota is there, you need to add to the ohrm_leave_entitlement, & take the leave entitlement id
                if ($new_entitlement_id == 0) {

                    //$insert_ohrm_leave_entitlement_sql = "INSERT INTO `ohrm_leave_entitlement` (emp_number, no_of_days, leave_type_id, from_date, to_date, credited_date, note, entitlement_type, `deleted`) VALUES (" . $emp_number . ", 0.00, ". $leave_type_id . ", CONCAT(YEAR(" . $date . "), '-01-01'), CONCAT(YEAR(" . $date . "), '-12-31'), CONCAT(YEAR(" . $date . "), '-01-01'), 'added by the script', 1, 0) ;";
                    $leavePeriodForDate = $this->getLeavePeriodForDate($date);
                    $from_date = $leavePeriodForDate[0];
                    $to_date = $leavePeriodForDate[1];

                    $insertOhrmLeaveEntitlementSql = "INSERT INTO `ohrm_leave_entitlement` (emp_number, no_of_days, leave_type_id, from_date, to_date, credited_date, note, entitlement_type, `deleted`) VALUES (" . $emp_number . ", 0.00, " . $leave_type_id . ", '" . $from_date . "', '" . $to_date . "', '" . $from_date . "', 'added by the script', 1, 0) ;";

                    $added1 = $this->upgradeUtility->executeSql($insertOhrmLeaveEntitlementSql);

                    if ($added1) {
                        $lastIdRes = $this->upgradeUtility->executeSql("SELECT LAST_INSERT_ID()");
                        $lastIdRow = mysqli_fetch_row($lastIdRes);
                        $new_entitlement_id = $lastIdRow[0];
                        
                    } else {
                        UpgradeLogger::writeErrorMessage("Could not add: " . $new_entitlement_id . "!\nError: " . mysql_error());
                        throw new Exception("Upgrade Failed");
                    }
                }



                if ($leave_sum <= $curr_bal) {
                    //insert length_days
                    $insert_leave_leave_entitlement_sql = "INSERT INTO `ohrm_leave_leave_entitlement` (`leave_id`, `entitlement_id`, `length_days`) VALUES (" . $leave_id . ", " . $new_entitlement_id . ", " . $length_days . ");";
                } elseif ($curr_bal > 0) {
                    //insert curr_bal
                    $insert_leave_leave_entitlement_sql = "INSERT INTO `ohrm_leave_leave_entitlement` (`leave_id`, `entitlement_id`, `length_days`) VALUES (" . $leave_id . ", " . $new_entitlement_id . ", " . $curr_bal . ");";
                }

                if ($insert_leave_leave_entitlement_sql != NULL) {

                    $added = $this->upgradeUtility->executeSql($insert_leave_leave_entitlement_sql);

                    if (!$added) {
                        UpgradeLogger::writeErrorMessage("Could not add: " . $new_entitlement_id . "!\nError: " . mysql_error());
                        throw new Exception("Upgrade Failed");                        
                    }
                }
                $insert_leave_leave_entitlement_sql = NULL;
            }
        }


        //drop unwanted column
        $drop_new_entitlement_id_sql = "alter table `ohrm_leave` drop column new_entitlement_id;";
        $dropped = $this->upgradeUtility->executeSql($drop_new_entitlement_id_sql);

        if (!$dropped) {
            UpgradeLogger::writeErrorMessage("Could not drop column: " . $drop_new_entitlement_id_sql . "!\nError: " . mysql_error());
            throw new Exception("Upgrade Failed");   
        }

        //set days_used
        $set_days_used_sql = "UPDATE `ohrm_leave_entitlement` le SET le.`days_used` = (SELECT SUM(l.`length_days`) FROM `ohrm_leave` l WHERE l.`emp_number` = le.`emp_number` AND l.`leave_type_id` = le.leave_type_id AND l.date BETWEEN le.from_date AND le.to_date);";
        $saved = $this->upgradeUtility->executeSql($set_days_used_sql);

        if (!$saved) {
            UpgradeLogger::writeErrorMessage("Could not save leave entitlement!\nError: " . mysql_error());
            throw new Exception("Upgrade Failed");  
        }
    }

    public function getNotes() {
        $notes = array();
        $notes[] = "If you have enabled data encryption, you have to re-enter the smtp password at Admin > Configuration > Email Configuration and save. (after copying the key.ohrm file to the new version)";
        return $notes;
    }
    
    public function getGeneratedLeavePeriodList($leavePeriodHistoryList){
        $leavePeriodList = array();
        $result = array();

        if (count($leavePeriodHistoryList) > 0){
        
            $endDate = new DateTime();
            $endDate->add(new DateInterval('P1Y'));
            
            $firstHistoryItem = $leavePeriodHistoryList[0];
            UpgradeLogger::writeLogMessage('$firstHistoryItem:' . print_r($firstHistoryItem, true));

            $firstCreatedDate = new DateTime($firstHistoryItem['created_at']);
            $startDate = new DateTime($firstCreatedDate->format('Y')."-".$firstHistoryItem['leave_period_start_month']."-".
                    $firstHistoryItem['leave_period_start_day']);
            if($firstCreatedDate < $startDate){
                $startDate->sub(new DateInterval('P1Y'));
            }
            $tempDate = $startDate;
            $i= 0;
            while( $tempDate <=  $endDate){

               $projectedSatrtDate = ($i==0)?$tempDate:new DateTime(date('Y-m-d',  strtotime($tempDate->format('Y-m-d')."+1 day")));
               $projectedEndDate = new DateTime(date('Y-m-d',  strtotime($projectedSatrtDate->format('Y-m-d')." +1 year -1 day")));

                foreach( $leavePeriodHistoryList as $leavePeriodHistory){

                    $createdDate = new DateTime( $leavePeriodHistory['created_at']);

                    if( ($projectedSatrtDate < $createdDate) && ($createdDate < $projectedEndDate)) {
                        $newSatrtDate = new DateTime($createdDate->format('Y')."-".$leavePeriodHistory['leave_period_start_month']."-".$leavePeriodHistory['leave_period_start_day']);
                        if($createdDate <  $newSatrtDate){
                            $newSatrtDate->sub(new DateInterval('P1Y'));
                        }
                        $projectedEndDate = $newSatrtDate->add(DateInterval::createFromDateString('+1 year -1 day'));

                    }

                }

               $tempDate = $projectedEndDate;

                $leavePeriodList[] = array($projectedSatrtDate->format('Y-m-d') , $projectedEndDate->format('Y-m-d'));
                $i++;
            }
            $result = $leavePeriodList;
        }
        
        return $result;
    }
    
    protected function getLeavePeriodForDate($date) {

        $matchLeavePeriod = null;
        $leavePeriodList = $this->getLeavePeriodList();
        $currentDate = new DateTime($date);
        foreach ($leavePeriodList as $leavePeriod) {
            $startDate = new DateTime($leavePeriod[0]);
            $endDate = new DateTime($leavePeriod[1]);
            if (($startDate <= $currentDate) && ($currentDate <= $endDate)) {
                $matchLeavePeriod = $leavePeriod;
                break;
            }
        }
        return $matchLeavePeriod;
    }
    
    protected function getLeavePeriodList() {
        
        if (is_null($this->leavePeriodList)) {
            
            $leavePeriods = array();
            $result = $this->upgradeUtility->executeSql('select * from ohrm_leave_period_history order by created_at, id');
            
            if (!$result) {
                throw new Exception("query failed");
            }            
            while ($row = mysqli_fetch_array($result)) {
                $leavePeriods[] = $row;
            }
            
            $this->leavePeriodList = $this->getGeneratedLeavePeriodList($leavePeriods);

            UpgradeLogger::writeLogMessage("Leave Period List: " . print_r($this->leavePeriodList, true));
        }
        
        return $this->leavePeriodList;
    }
    
    protected function getReportForEmployee() {
        $report = '<report>
    <settings>
        <csv>
            <include_group_header>1</include_group_header>
            <include_header>1</include_header>
        </csv>
    </settings>
<filter_fields>
	<input_field type="text" name="empNumber" label="Employee Number"></input_field>
	<input_field type="text" name="fromDate" label="From"></input_field>
        <input_field type="text" name="toDate" label="To"></input_field>
        <input_field type="text" name="asOfDate" label="AsOf"></input_field>
</filter_fields> 

<sub_report type="sql" name="mainTable">       
    <query>FROM ohrm_leave_type WHERE (deleted = 0) OR (SELECT count(l.id) FROM ohrm_leave l WHERE l.status = 3 AND l.leave_type_id = ohrm_leave_type.id) > 0 ORDER BY ohrm_leave_type.id</query>
    <id_field>leaveTypeId</id_field>
    <display_groups>
        <display_group name="leavetype" type="one" display="true">
            <group_header></group_header>
            <fields>
                <field display="false">
                    <field_name>ohrm_leave_type.id</field_name>
                    <field_alias>leaveTypeId</field_alias>
                    <display_name>Leave Type ID</display_name>
                    <width>1</width>	
                </field>   
                <field display="false">
                    <field_name>ohrm_leave_type.exclude_in_reports_if_no_entitlement</field_name>
                    <field_alias>exclude_if_no_entitlement</field_alias>
                    <display_name>Exclude</display_name>
                    <width>1</width>	
                </field>  
                <field display="false">
                    <field_name>ohrm_leave_type.deleted</field_name>
                    <field_alias>leave_type_deleted</field_alias>
                    <display_name>Leave Type Deleted</display_name>
                    <width>1</width>	
                </field>  
                <field display="true">
                    <field_name>ohrm_leave_type.name</field_name>
                    <field_alias>leaveType</field_alias>
                    <display_name>Leave Type</display_name>
                    <width>160</width>	
                </field>s                                                                                                     
            </fields>
        </display_group>
    </display_groups> 
</sub_report>

<sub_report type="sql" name="entitlementsTotal">
                    <query>

FROM (
SELECT ohrm_leave_entitlement.id as id, 
       ohrm_leave_entitlement.leave_type_id as leave_type_id,
       ohrm_leave_entitlement.no_of_days as no_of_days,
       sum(IF(ohrm_leave.status = 2, ohrm_leave_leave_entitlement.length_days, 0)) AS scheduled,
       sum(IF(ohrm_leave.status = 3, ohrm_leave_leave_entitlement.length_days, 0)) AS taken
       
FROM ohrm_leave_entitlement LEFT JOIN ohrm_leave_leave_entitlement ON
    ohrm_leave_entitlement.id = ohrm_leave_leave_entitlement.entitlement_id
    LEFT JOIN ohrm_leave ON ohrm_leave.id = ohrm_leave_leave_entitlement.leave_id AND 
    ( $X{&gt;,ohrm_leave.date,toDate} OR $X{&lt;,ohrm_leave.date,fromDate} )

WHERE ohrm_leave_entitlement.deleted=0 AND $X{=,ohrm_leave_entitlement.emp_number,empNumber} AND 
    $X{IN,ohrm_leave_entitlement.leave_type_id,leaveTypeId} AND
    (
      ( $X{&lt;=,ohrm_leave_entitlement.from_date,fromDate} AND $X{&gt;=,ohrm_leave_entitlement.to_date,fromDate} ) OR
      ( $X{&lt;=,ohrm_leave_entitlement.from_date,toDate} AND $X{&gt;=,ohrm_leave_entitlement.to_date,toDate} ) OR 
      ( $X{&gt;=,ohrm_leave_entitlement.from_date,fromDate} AND $X{&lt;=,ohrm_leave_entitlement.to_date,toDate} ) 
    )
    
GROUP BY ohrm_leave_entitlement.id
) AS A

GROUP BY A.leave_type_id
ORDER BY A.leave_type_id

</query>
    <id_field>leaveTypeId</id_field>
    <display_groups>
            <display_group name="g2" type="one" display="true">
                <group_header></group_header>
                <fields>
                    <field display="false">
                        <field_name>A.leave_type_id</field_name>
                        <field_alias>leaveTypeId</field_alias>
                        <display_name>Leave Type ID</display_name>
                        <width>1</width>
                    </field>                                
                    <field display="true">
                        <field_name>sum(A.no_of_days) - sum(A.scheduled) - sum(A.taken)</field_name>
                        <field_alias>entitlement_total</field_alias>
                        <display_name>Leave Entitlements (Days)</display_name>
                        <width>120</width>
                        <align>right</align>
                        <link>leave/viewLeaveEntitlements?empNumber=$P{empNumber}&amp;fromDate=$P{fromDate}&amp;toDate=$P{toDate}&amp;leaveTypeId=$P{leaveTypeId}&amp;stddate=1</link>
                    </field>                                
                </fields>
            </display_group>
    </display_groups>
</sub_report>

<sub_report type="sql" name="pendingQuery">
<query>
FROM ohrm_leave_type LEFT JOIN 
ohrm_leave ON ohrm_leave_type.id = ohrm_leave.leave_type_id AND
$X{=,ohrm_leave.emp_number,empNumber} AND
ohrm_leave.status = 1 AND
$X{&gt;=,ohrm_leave.date,fromDate} AND $X{&lt;=,ohrm_leave.date,toDate}
WHERE
ohrm_leave_type.deleted = 0 AND
$X{IN,ohrm_leave_type.id,leaveTypeId}

GROUP BY ohrm_leave_type.id
ORDER BY ohrm_leave_type.id
</query>
    <id_field>leaveTypeId</id_field>
    <display_groups>
            <display_group name="g6" type="one" display="true">
                <group_header></group_header>
                <fields>
                    <field display="false">
                        <field_name>ohrm_leave_type.id</field_name>
                        <field_alias>leaveTypeId</field_alias>
                        <display_name>Leave Type ID</display_name>
                        <width>1</width>
                    </field>                                
                    <field display="true">
                        <field_name>sum(length_days)</field_name>
                        <field_alias>pending</field_alias>
                        <display_name>Leave Pending Approval (Days)</display_name>
                        <width>120</width>
                        <align>right</align>
                        <link>leave/viewLeaveList?empNumber=$P{empNumber}&amp;fromDate=$P{fromDate}&amp;toDate=$P{toDate}&amp;leaveTypeId=$P{leaveTypeId}&amp;status=1&amp;stddate=1</link>
                    </field>                                
                </fields>
            </display_group>
    </display_groups>
    </sub_report>

<sub_report type="sql" name="scheduledQuery">
<query>
FROM ohrm_leave_type LEFT JOIN 
ohrm_leave ON ohrm_leave_type.id = ohrm_leave.leave_type_id AND
$X{=,ohrm_leave.emp_number,empNumber} AND
ohrm_leave.status = 2 AND
$X{&gt;=,ohrm_leave.date,fromDate} AND $X{&lt;=,ohrm_leave.date,toDate}
WHERE
ohrm_leave_type.deleted = 0 AND
$X{IN,ohrm_leave_type.id,leaveTypeId}

GROUP BY ohrm_leave_type.id
ORDER BY ohrm_leave_type.id
</query>
    <id_field>leaveTypeId</id_field>
    <display_groups>
            <display_group name="g5" type="one" display="true">
                <group_header></group_header>
                <fields>
                    <field display="false">
                        <field_name>ohrm_leave_type.id</field_name>
                        <field_alias>leaveTypeId</field_alias>
                        <display_name>Leave Type ID</display_name>
                        <width>1</width>
                    </field>                                
                    <field display="true">
                        <field_name>sum(length_days)</field_name>
                        <field_alias>scheduled</field_alias>
                        <display_name>Leave Scheduled (Days)</display_name>
                        <width>120</width>
                        <align>right</align>
                        <link>leave/viewLeaveList?empNumber=$P{empNumber}&amp;fromDate=$P{fromDate}&amp;toDate=$P{toDate}&amp;leaveTypeId=$P{leaveTypeId}&amp;status=2&amp;stddate=1</link>
                    </field>                                
                </fields>
            </display_group>
    </display_groups>
    </sub_report>

<sub_report type="sql" name="takenQuery">
<query>
FROM ohrm_leave WHERE $X{=,emp_number,empNumber} AND
status = 3 AND
$X{IN,ohrm_leave.leave_type_id,leaveTypeId} AND
$X{&gt;=,ohrm_leave.date,fromDate} AND $X{&lt;=,ohrm_leave.date,toDate}
GROUP BY leave_type_id
ORDER BY ohrm_leave.leave_type_id
</query>
    <id_field>leaveTypeId</id_field>
    <display_groups>
            <display_group name="g4" type="one" display="true">
                <group_header></group_header>
                <fields>
                    <field display="false">
                        <field_name>ohrm_leave.leave_type_id</field_name>
                        <field_alias>leaveTypeId</field_alias>
                        <display_name>Leave Type ID</display_name>
                        <width>1</width>
                    </field>                                
                    <field display="true">
                        <field_name>sum(length_days)</field_name>
                        <field_alias>taken</field_alias>
                        <display_name>Leave Taken (Days)</display_name>
                        <width>120</width>
                        <align>right</align>
                        <link>leave/viewLeaveList?empNumber=$P{empNumber}&amp;fromDate=$P{fromDate}&amp;toDate=$P{toDate}&amp;leaveTypeId=$P{leaveTypeId}&amp;status=3&amp;stddate=1</link>
                    </field>                                
                </fields>
            </display_group>
    </display_groups>
    </sub_report>

<sub_report type="sql" name="unused">       
    <query>FROM ohrm_leave_type WHERE deleted = 0 AND $X{IN,ohrm_leave_type.id,leaveTypeId} ORDER BY ohrm_leave_type.id</query>
    <id_field>leaveTypeId</id_field>
    <display_groups>
        <display_group name="unused" type="one" display="true">
            <group_header></group_header>
            <fields>
                <field display="false">
                    <field_name>ohrm_leave_type.id</field_name>
                    <field_alias>leaveTypeId</field_alias>
                    <display_name>Leave Type ID</display_name>
                    <width>1</width>	
                </field>   
                <field display="true">
                    <field_name>ohrm_leave_type.name</field_name>
                    <field_alias>unused</field_alias>
                    <display_name>Leave Balance (Days)</display_name>
                    <width>160</width>	
                    <align>right</align>
                </field>                                                                                                     
            </fields>
        </display_group>
    </display_groups> 
</sub_report>


    <join>             
        <join_by sub_report="mainTable" id="leaveTypeId"></join_by>              
        <join_by sub_report="entitlementsTotal" id="leaveTypeId"></join_by> 
        <join_by sub_report="pendingQuery" id="leaveTypeId"></join_by>  
        <join_by sub_report="scheduledQuery" id="leaveTypeId"></join_by>  
        <join_by sub_report="takenQuery" id="leaveTypeId"></join_by>  
        <join_by sub_report="unused" id="leaveTypeId"></join_by>  

    </join>
    <page_limit>100</page_limit>        
</report>';
        return $report;
    }
    
    protected function getReportForLeaveType() {
        $report = '<report>
    <settings>
        <csv>
            <include_group_header>1</include_group_header>
            <include_header>1</include_header>
        </csv>
    </settings>
<filter_fields>
	<input_field type="text" name="leaveType" label="Leave Type"></input_field>
	<input_field type="text" name="fromDate" label="From"></input_field>
        <input_field type="text" name="toDate" label="To"></input_field>
        <input_field type="text" name="asOfDate" label="AsOf"></input_field>
        <input_field type="text" name="emp_numbers" label="employees"></input_field>
        <input_field type="text" name="job_title" label="Job Title"></input_field>
        <input_field type="text" name="location" label="Location"></input_field>
        <input_field type="text" name="sub_unit" label="Sub Unit"></input_field>
        <input_field type="text" name="terminated" label="Terminated"></input_field>
</filter_fields> 

<sub_report type="sql" name="mainTable">       
    <query>FROM hs_hr_employee 
    LEFT JOIN hs_hr_emp_locations ON hs_hr_employee.emp_number = hs_hr_emp_locations.emp_number
    WHERE $X{IN,hs_hr_employee.emp_number,emp_numbers} 
    AND $X{=,hs_hr_employee.job_title_code,job_title}
    AND $X{IN,hs_hr_employee.work_station,sub_unit}
    AND $X{IN,hs_hr_emp_locations.location_id,location}
    AND $X{IS NULL,hs_hr_employee.termination_id,terminated}
    ORDER BY hs_hr_employee.emp_lastname</query>
    <id_field>empNumber</id_field>
    <display_groups>
        <display_group name="personalDetails" type="one" display="true">
            <group_header></group_header>
            <fields>
                <field display="false">
                    <field_name>hs_hr_employee.emp_number</field_name>
                    <field_alias>empNumber</field_alias>
                    <display_name>Employee Number</display_name>
                    <width>1</width>	
                </field>                
                <field display="false">
                    <field_name>hs_hr_employee.termination_id</field_name>
                    <field_alias>termination_id</field_alias>
                    <display_name>Termination ID</display_name>
                    <width>1</width>	
                </field>   
                <field display="true">
                    <field_name>CONCAT(hs_hr_employee.emp_firstname, \\\' \\\', hs_hr_employee.emp_lastname)</field_name>
                    <field_alias>employeeName</field_alias>
                    <display_name>Employee</display_name>
                    <width>150</width>
                </field>                                                                                               
            </fields>
        </display_group>
    </display_groups> 
</sub_report>

<sub_report type="sql" name="entitlementsTotal">
                    <query>

FROM (
SELECT ohrm_leave_entitlement.id as id, 
       ohrm_leave_entitlement.emp_number as emp_number,
       ohrm_leave_entitlement.no_of_days as no_of_days,
       sum(IF(ohrm_leave.status = 2, ohrm_leave_leave_entitlement.length_days, 0)) AS scheduled,
       sum(IF(ohrm_leave.status = 3, ohrm_leave_leave_entitlement.length_days, 0)) AS taken
       
FROM ohrm_leave_entitlement LEFT JOIN ohrm_leave_leave_entitlement ON
    ohrm_leave_entitlement.id = ohrm_leave_leave_entitlement.entitlement_id
    LEFT JOIN ohrm_leave ON ohrm_leave.id = ohrm_leave_leave_entitlement.leave_id AND 
    ( $X{&gt;,ohrm_leave.date,toDate} OR $X{&lt;,ohrm_leave.date,fromDate} )

WHERE ohrm_leave_entitlement.deleted=0 AND $X{=,ohrm_leave_entitlement.leave_type_id,leaveType}
    AND $X{IN,ohrm_leave_entitlement.emp_number,empNumber} AND
    (
      ( $X{&lt;=,ohrm_leave_entitlement.from_date,fromDate} AND $X{&gt;=,ohrm_leave_entitlement.to_date,fromDate} ) OR
      ( $X{&lt;=,ohrm_leave_entitlement.from_date,toDate} AND $X{&gt;=,ohrm_leave_entitlement.to_date,toDate} ) OR 
      ( $X{&gt;=,ohrm_leave_entitlement.from_date,fromDate} AND $X{&lt;=,ohrm_leave_entitlement.to_date,toDate} ) 
    )
    
GROUP BY ohrm_leave_entitlement.id
) AS A

GROUP BY A.emp_number
ORDER BY A.emp_number

</query>
    <id_field>empNumber</id_field>
    <display_groups>
            <display_group name="g2" type="one" display="true">
                <group_header></group_header>
                <fields>
                    <field display="false">
                        <field_name>A.emp_number</field_name>
                        <field_alias>empNumber</field_alias>
                        <display_name>Emp Number</display_name>
                        <width>1</width>
                    </field>                                
                    <field display="true">
                        <field_name>sum(A.no_of_days) - sum(A.scheduled) - sum(A.taken)</field_name>
                        <field_alias>entitlement_total</field_alias>
                        <display_name>Leave Entitlements (Days)</display_name>
                        <width>120</width>
                        <align>right</align>
                        <link>leave/viewLeaveEntitlements?empNumber=$P{empNumber}&amp;fromDate=$P{fromDate}&amp;toDate=$P{toDate}&amp;leaveTypeId=$P{leaveTypeId}&amp;stddate=1</link>
                    </field>                                
                </fields>
            </display_group>
    </display_groups>
</sub_report>

<sub_report type="sql" name="pendingQuery">
<query>
FROM ohrm_leave WHERE $X{=,ohrm_leave.leave_type_id,leaveType} AND
status = 1 AND
$X{IN,ohrm_leave.emp_number,empNumber} AND
$X{&gt;=,ohrm_leave.date,fromDate} AND $X{&lt;=,ohrm_leave.date,toDate}
GROUP BY emp_number
ORDER BY ohrm_leave.emp_number
</query>
    <id_field>empNumber</id_field>
    <display_groups>
            <display_group name="g6" type="one" display="true">
                <group_header></group_header>
                <fields>
                    <field display="false">
                        <field_name>ohrm_leave.emp_number</field_name>
                        <field_alias>empNumber</field_alias>
                        <display_name>Emp Number</display_name>
                        <width>1</width>
                    </field>                                
                    <field display="true">
                        <field_name>sum(length_days)</field_name>
                        <field_alias>pending</field_alias>
                        <display_name>Leave Pending Approval (Days)</display_name>
                        <width>121</width>
                        <align>right</align>
                        <link>leave/viewLeaveList?empNumber=$P{empNumber}&amp;fromDate=$P{fromDate}&amp;toDate=$P{toDate}&amp;leaveTypeId=$P{leaveTypeId}&amp;status=1&amp;stddate=1</link>
                    </field>                                
                </fields>
            </display_group>
    </display_groups>
</sub_report>


<sub_report type="sql" name="scheduledQuery">
<query>
FROM ohrm_leave WHERE $X{=,ohrm_leave.leave_type_id,leaveType} AND
status = 2 AND
$X{IN,ohrm_leave.emp_number,empNumber} AND
$X{&gt;=,ohrm_leave.date,fromDate} AND $X{&lt;=,ohrm_leave.date,toDate}
GROUP BY emp_number
ORDER BY ohrm_leave.emp_number
</query>
    <id_field>empNumber</id_field>
    <display_groups>
            <display_group name="g5" type="one" display="true">
                <group_header></group_header>
                <fields>
                    <field display="false">
                        <field_name>ohrm_leave.emp_number</field_name>
                        <field_alias>empNumber</field_alias>
                        <display_name>Emp Number</display_name>
                        <width>1</width>
                    </field>                                
                    <field display="true">
                        <field_name>sum(length_days)</field_name>
                        <field_alias>scheduled</field_alias>
                        <display_name>Leave Scheduled (Days)</display_name>
                        <width>121</width>
                        <align>right</align>
                        <link>leave/viewLeaveList?empNumber=$P{empNumber}&amp;fromDate=$P{fromDate}&amp;toDate=$P{toDate}&amp;leaveTypeId=$P{leaveTypeId}&amp;status=2&amp;stddate=1</link>
                    </field>                                
                </fields>
            </display_group>
    </display_groups>
</sub_report>

<sub_report type="sql" name="takenQuery">
<query>
FROM ohrm_leave WHERE $X{=,ohrm_leave.leave_type_id,leaveType} AND
status = 3 AND
$X{IN,ohrm_leave.emp_number,empNumber} AND
$X{&gt;=,ohrm_leave.date,fromDate} AND $X{&lt;=,ohrm_leave.date,toDate}
GROUP BY emp_number
ORDER BY ohrm_leave.emp_number
</query>
    <id_field>empNumber</id_field>
    <display_groups>
            <display_group name="g4" type="one" display="true">
                <group_header></group_header>
                <fields>
                    <field display="false">
                        <field_name>ohrm_leave.emp_number</field_name>
                        <field_alias>empNumber</field_alias>
                        <display_name>Emp Number</display_name>
                        <width>1</width>
                    </field>                                
                    <field display="true">
                        <field_name>sum(length_days)</field_name>
                        <field_alias>taken</field_alias>
                        <display_name>Leave Taken (Days)</display_name>
                        <width>120</width>
                        <align>right</align>
                        <link>leave/viewLeaveList?empNumber=$P{empNumber}&amp;fromDate=$P{fromDate}&amp;toDate=$P{toDate}&amp;leaveTypeId=$P{leaveTypeId}&amp;status=3&amp;stddate=1</link>
                    </field>                                
                </fields>
            </display_group>
    </display_groups>
</sub_report>
<sub_report type="sql" name="unused">       
    <query>FROM hs_hr_employee WHERE $X{IN,hs_hr_employee.emp_number,empNumber} ORDER BY hs_hr_employee.emp_number</query>
    <id_field>empNumber</id_field>
    <display_groups>
        <display_group name="unused" type="one" display="true">
            <group_header></group_header>
            <fields>    
                <field display="false">
                    <field_name>hs_hr_employee.emp_number</field_name>
                    <field_alias>empNumber</field_alias>
                    <display_name>Employee Number</display_name>
                    <width>1</width>	
                </field>                
                <field display="true">
                    <field_name>hs_hr_employee.emp_firstname</field_name>
                    <field_alias>unused</field_alias>
                    <display_name>Leave Balance (Days)</display_name>
                    <width>150</width>
                    <align>right</align>
                </field> 
                                                                                               
            </fields>
        </display_group>
    </display_groups> 
</sub_report>
    <join>             
        <join_by sub_report="mainTable" id="empNumber"></join_by>            
        <join_by sub_report="entitlementsTotal" id="empNumber"></join_by> 
        <join_by sub_report="pendingQuery" id="empNumber"></join_by>
        <join_by sub_report="scheduledQuery" id="empNumber"></join_by>
        <join_by sub_report="takenQuery" id="empNumber"></join_by> 
        <join_by sub_report="unused" id="empNumber"></join_by>  
    </join>
    <page_limit>20</page_limit>       
</report>';
        return $report;
    }
}

