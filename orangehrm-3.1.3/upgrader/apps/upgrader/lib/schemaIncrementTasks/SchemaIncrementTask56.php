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
 */

/**
 * Changes from 3.0 to 3.0.1
 * 
 * 1) decimal points in leave entitlement related tables
 * 2) default home page tables
 *
 */
class SchemaIncrementTask56 extends SchemaIncrementTask {
    public $userInputs;

    public function execute() {
        $this->incrementNumber = 56;
        parent::execute();

        $result = array();

        foreach ($this->sql as $sql) {
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

    protected function getUserRoles() {
        $result = $this->upgradeUtility->executeSql('SELECT name, id FROM ohrm_user_role');
        $userRoles = array();
        
        while ($row = mysqli_fetch_array($result)) {
            $userRoles[$row['name']] = $row['id'];
        }            
        
        $logMessage = print_r($userRoles, true);
        UpgradeLogger::writeLogMessage($logMessage);
        return $userRoles;        
    }
    
    public function loadSql() {

        $sql = array();   
        $sql[] = "ALTER TABLE ohrm_workflow_state_machine MODIFY COLUMN `id` bigint(20) NOT NULL AUTO_INCREMENT;";
             
        $sql[] = "create table ohrm_home_page (
            `id` int(11) not null auto_increment,
            `user_role_id` int not null,
            `action` varchar(255),
            `enable_class` varchar(100) default null,
            `priority` int not null default 0 COMMENT 'lowest priority 0',
            primary key (`id`)
        ) engine=innodb default charset=utf8;";

        $sql[] = "create table ohrm_module_default_page (
            `id` int(11) not null auto_increment,
            `module_id` int not null,
            `user_role_id` int not null,
            `action` varchar(255),
            `enable_class` varchar(100) default null,
            `priority` int not null default 0 COMMENT 'lowest priority 0',
            primary key (`id`)
        ) engine=innodb default charset=utf8;";

        $sql[] = "alter table ohrm_home_page 
            add foreign key (user_role_id) references ohrm_user_role(id) on delete cascade;";

        $sql[] = "alter table ohrm_module_default_page 
            add foreign key (user_role_id) references ohrm_user_role(id) on delete cascade,
            add foreign key (module_id) references ohrm_module(id) on delete cascade;";
        
        $userRoles = $this->getUserRoles();
        
        $sql[] = "INSERT INTO ohrm_home_page (`user_role_id`, `action`, `enable_class`, `priority`) VALUES 
            ({$userRoles['Admin']}, 'pim/viewEmployeeList', NULL, 10),
            ({$userRoles['ESS']}, 'pim/viewMyDetails', NULL, 0);";

        $sql[] = "INSERT INTO ohrm_module_default_page (`module_id`, `user_role_id`, `action`, `enable_class`, `priority`) VALUES 
            (2, {$userRoles['Admin']}, 'admin/viewSystemUsers', NULL, 20),
            (3, {$userRoles['Admin']}, 'pim/viewEmployeeList', NULL, 20),
            (3, {$userRoles['Supervisor']}, 'pim/viewEmployeeList', NULL, 10),
            (3, {$userRoles['ESS']}, 'pim/viewMyDetails', NULL, 0),
            (4, {$userRoles['Admin']}, 'leave/viewLeaveList/reset/1', NULL, 20),
            (4, {$userRoles['Supervisor']}, 'leave/viewLeaveList/reset/1', NULL, 10),
            (4, {$userRoles['ESS']}, 'leave/viewMyLeaveList', NULL, 0),
            (4, {$userRoles['Admin']}, 'leave/defineLeavePeriod', 'LeavePeriodDefinedHomePageEnabler', 100),
            (4, {$userRoles['ESS']}, 'leave/showLeavePeriodNotDefinedWarning', 'LeavePeriodDefinedHomePageEnabler', 90),
            (5, {$userRoles['Admin']}, 'time/viewEmployeeTimesheet', NULL, 20),
            (5, {$userRoles['ESS']}, 'time/viewMyTimesheet', NULL, 0),
            (5, {$userRoles['Admin']}, 'time/defineTimesheetPeriod', 'TimesheetPeriodDefinedHomePageEnabler', 100),
            (5, {$userRoles['ESS']}, 'time/defineTimesheetPeriod', 'TimesheetPeriodDefinedHomePageEnabler', 100),
            (7, {$userRoles['Admin']}, 'recruitment/viewCandidates', NULL, 20),
            (7, {$userRoles['Interviewer']}, 'recruitment/viewCandidates', NULL, 10),
            (7, {$userRoles['HiringManager']}, 'recruitment/viewCandidates', NULL, 5),
            (9, {$userRoles['Admin']}, 'performance/viewReview', NULL, 20),
            (9, {$userRoles['ESS']}, 'performance/viewReview', NULL, 0);";

        $sql[] = "ALTER TABLE ohrm_leave_entitlement 
            MODIFY COLUMN no_of_days decimal(19,15) not null,
            MODIFY COLUMN days_used decimal(8,4) not null default 0;";
        
        $sql[] = "ALTER TABLE ohrm_leave_adjustment  
            MODIFY COLUMN no_of_days decimal(19,15) not null;";        
        
        $sql[] = "ALTER TABLE ohrm_leave  
            MODIFY COLUMN `length_days` decimal(6,4) unsigned default NULL;"; 
        
        $sql[] = "ALTER TABLE ohrm_leave_leave_entitlement  
            MODIFY COLUMN `length_days` decimal(6,4) unsigned default NULL;";     
        
        $this->sql = $sql;
    }
    
    public function getNotes() {
        return array();
    }
}


