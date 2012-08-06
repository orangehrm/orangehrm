<?php

/*
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

include_once 'SchemaIncrementTask.php';

class SchemaIncrementTask54 extends SchemaIncrementTask {
    
    public $userInputs;
    
    public function execute() {
        $this->incrementNumber = 54;
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
    
    public function loadSql() {
        
        $sql[0] = "CREATE TABLE ohrm_data_group (
                        `id` int AUTO_INCREMENT, 
                        `name` VARCHAR(255), description VARCHAR(255), 
                        `can_read` TINYINT, can_create TINYINT, 
                        `can_update` TINYINT, 
                        `can_delete` TINYINT, 
                        PRIMARY KEY(`id`)
                    ) ENGINE = INNODB DEFAULT CHARSET=utf8";
        
        $sql[1] = "CREATE TABLE ohrm_user_role_data_group (
                        id int AUTO_INCREMENT, 
                        user_role_id int, 
                        data_group_id int, 
                        can_read TINYINT, 
                        can_create TINYINT, 
                        can_update TINYINT, 
                        can_delete TINYINT, 
                        self TINYINT, 
                        PRIMARY KEY(id)
                    ) ENGINE = INNODB DEFAULT CHARSET=utf8";
        
        $sql[2] = "alter table ohrm_user_role_data_group 
                        add constraint foreign key (user_role_id)
                             references ohrm_user_role(id) on delete cascade";
        
        $sql[3] = "alter table ohrm_user_role_data_group 
                        add constraint foreign key (data_group_id)
                             references ohrm_data_group(id) on delete cascade";
        
        
        $sql[4] = "INSERT INTO `ohrm_workflow_state_machine` VALUES
                                    ('81','3','NOT_EXIST','ADMIN','1','ACTIVE'),
                                    ('82','3','ACTIVE','ADMIN','2','NOT_EXIST'),
                                    ('83','3','ACTIVE','ADMIN','3','TERMINATED'),
                                    ('84','3','TERMINATED','ADMIN','4','ACTIVE'),
                                    ('85','3','TERMINATED','ADMIN','5','NOT_EXIST')"; 
        
        $sql[5] = "UPDATE `hs_hr_unique_id` SET `last_id` = 85 WHERE `table_name` = 'ohrm_workflow_state_machine' AND `field_name` = 'id'";
    
        $sql[6] = "INSERT INTO `ohrm_data_group` (`id`, `name`, `description`, `can_read`, `can_create`, `can_update`, `can_delete`) VALUES
                                                    (1, 'personal_information', 'Personal Details', 1, NULL, 1, NULL),
                                                    (2, 'personal_attachment', 'Attachments in Personal Details', 1, 1, 1, 1),
                                                    (3, 'personal_custom_fields', 'Custom Fields in Personal Details', 1, NULL, 1, NULL),
                                                    (4, 'contact_details', 'Contact Details', 1, NULL, 1, NULL),
                                                    (5, 'contact_attachment', 'Attachments in Contact Details', 1, 1, 1, 1),
                                                    (6, 'contact_custom_fields', 'Custom Fields in Contact Details', 1, NULL, 1, NULL),
                                                    (7, 'emergency_contacts', 'Emergency Contacts', 1, 1, 1, 1),
                                                    (8, 'emergency_attachment', 'Attachments in Emergency Contacts', 1, 1, 1, 1),
                                                    (9, 'emergency_custom_fields', 'Custom Fields in Emergency Contacts', 1, NULL, 1, NULL),
                                                    (10, 'dependents', 'Dependents', 1, 1, 1, 1),
                                                    (11, 'dependents_attachment', 'Attachments in Dependents', 1, 1, 1, 1),
                                                    (12, 'dependents_custom_fields', 'Custom Fields in Dependents', 1, NULL, 1, NULL),
                                                    (13, 'immigration', 'Immigration', 1, 1, 1, 1),
                                                    (14, 'immigration_attachment', 'Attachments in Immigration', 1, 1, 1, 1),
                                                    (15, 'immigration_custom_fields', 'Custom Fields in Immigration', 1, NULL, 1, NULL),
                                                    (16, 'job_details', 'Job', 1, NULL, 1, NULL),
                                                    (17, 'job_attachment', 'Attachments in Job', 1, 1, 1, 1),
                                                    (18, 'job_custom_fields', 'Custom Fields in Job', 1, NULL, 1, NULL),
                                                    (19, 'salary_details', 'Salary', 1, 1, 1, 1),
                                                    (20, 'salary_attachment', 'Attachments in Salary', 1, 1, 1, 1),
                                                    (21, 'salary_custom_fields', 'Custom Fields in Salary', 1, NULL, 1, NULL),
                                                    (22, 'tax_exemptions', 'Tax Exemptions', 1, NULL, 1, NULL),
                                                    (23, 'tax_attachment', 'Attachments in Tax Exemptions', 1, 1, 1, 1),
                                                    (24, 'tax_custom_fields', 'Custom Fields in Tax Exemptions', 1, NULL, 1, NULL),
                                                    (25, 'supervisor', 'Employee Supervisors', 1, 1, 1, 1),
                                                    (26, 'subordinates', 'Employee Subordinates', 1, 1, 1, 1),
                                                    (27, 'report-to_attachment', 'Attachment in Report To', 1, 1, 1, 1),
                                                    (28, 'report-to_custom_fields', 'Custom Fields in Report To', 1, NULL, 1, NULL),
                                                    (29, 'qualification_work', 'Work Experience', 1, 1, 1, 1),
                                                    (30, 'qualification_education', 'Education', 1, 1, 1, 1),
                                                    (31, 'qualification_skills', 'Skills', 1, 1, 1, 1),
                                                    (32, 'qualification_languages', 'Languages', 1, 1, 1, 1),
                                                    (33, 'qualification_license', 'License', 1, 1, 1, 1),
                                                    (34, 'qualifications_attachment', 'Attachments in Qualifications', 1, 1, 1, 1),
                                                    (35, 'qualifications_custom_fields', 'Custom Fields in Qualifications', 1, NULL, 1, NULL),
                                                    (36, 'membership', 'Membership', 1, 1, 1, 1),
                                                    (37, 'membership_attachment', 'Attachments in Membership', 1, 1, 1, 1),
                                                    (38, 'membership_custom_fields', 'Custom Fields in Membership', 1, NULL, 1, NULL),
                                                    (39, 'photograph', 'Employee Photograph', 1, NULL, 1, 1),
                                                    (40, 'leave_summary', 'Leave Summary', 1, NULL, 1, NULL)";
        
        $sql[7] = "INSERT INTO `ohrm_user_role_data_group` (`user_role_id`, `data_group_id`, `can_read`, `can_create`, `can_update`, `can_delete`, `self`) VALUES
                                                            (1, 1, 1, NULL, 1, NULL, 0),
                                                            (1, 2, 1, 1, 1, 1, 0),
                                                            (1, 3, 1, NULL, 1, NULL, 0),
                                                            (1, 4, 1, NULL, 1, NULL, 0),
                                                            (1, 5, 1, 1, 1, 1, 0),
                                                            (1, 6, 1, NULL, 1, NULL, 0),
                                                            (1, 7, 1, 1, 1, 1, 0),
                                                            (1, 8, 1, 1, 1, 1, 0),
                                                            (1, 9, 1, NULL, 1, NULL, 0),
                                                            (1, 10, 1, 1, 1, 1, 0),
                                                            (1, 11, 1, 1, 1, 1, 0),
                                                            (1, 12, 1, NULL, 1, NULL, 0),
                                                            (1, 13, 1, 1, 1, 1, 0),
                                                            (1, 14, 1, 1, 1, 1, 0),
                                                            (1, 15, 1, NULL, 1, NULL, 0),
                                                            (1, 16, 1, NULL, 1, NULL, 0),
                                                            (1, 17, 1, 1, 1, 1, 0),
                                                            (1, 18, 1, NULL, 1, NULL, 0),
                                                            (1, 19, 1, 1, 1, 1, 0),
                                                            (1, 20, 1, 1, 1, 1, 0),
                                                            (1, 21, 1, NULL, 1, NULL, 0),
                                                            (1, 22, 1, NULL, 1, NULL, 0),
                                                            (1, 23, 1, 1, 1, 1, 0),
                                                            (1, 24, 1, NULL, 1, NULL, 0),
                                                            (1, 25, 1, 1, 1, 1, 0),
                                                            (1, 26, 1, 1, 1, 1, 0),
                                                            (1, 27, 1, 1, 1, 1, 0),
                                                            (1, 28, 1, NULL, 1, NULL, 0),
                                                            (1, 29, 1, 1, 1, 1, 0),
                                                            (1, 30, 1, 1, 1, 1, 0),
                                                            (1, 31, 1, 1, 1, 1, 0),
                                                            (1, 32, 1, 1, 1, 1, 0),
                                                            (1, 33, 1, 1, 1, 1, 0),
                                                            (1, 34, 1, 1, 1, 1, 0),
                                                            (1, 35, 1, NULL, 1, NULL, 0),
                                                            (1, 36, 1, 1, 1, 1, 0),
                                                            (1, 37, 1, 1, 1, 1, 0),
                                                            (1, 38, 1, NULL, 1, NULL, 0),
                                                            (1, 39, 1, NULL, 1, 1, 0),
                                                            (1, 40, 1, NULL, 1, NULL, 0),
                                                            (1, 1, 1, NULL, 1, NULL, 1),
                                                            (1, 2, 1, 1, 1, 1, 1),
                                                            (1, 3, 1, NULL, 1, NULL, 1),
                                                            (1, 4, 1, NULL, 1, NULL, 1),
                                                            (1, 5, 1, 1, 1, 1, 1),
                                                            (1, 6, 1, NULL, 1, NULL, 1),
                                                            (1, 7, 1, 1, 1, 1, 1),
                                                            (1, 8, 1, 1, 1, 1, 1),
                                                            (1, 9, 1, NULL, 1, NULL, 1),
                                                            (1, 10, 1, 1, 1, 1, 1),
                                                            (1, 11, 1, 1, 1, 1, 1),
                                                            (1, 12, 1, NULL, 1, NULL, 1),
                                                            (1, 13, 1, 1, 1, 1, 1),
                                                            (1, 14, 1, 1, 1, 1, 1),
                                                            (1, 15, 1, NULL, 1, NULL, 1),
                                                            (1, 16, 1, NULL, NULL, NULL, 1),
                                                            (1, 17, 1, 1, 1, 1, 1),
                                                            (1, 18, 1, NULL, 1, NULL, 1),
                                                            (1, 19, 1, NULL, NULL, NULL, 1),
                                                            (1, 20, 1, 1, 1, 1, 1),
                                                            (1, 21, 1, NULL, 1, NULL, 1),
                                                            (1, 22, 1, NULL, NULL, NULL, 1),
                                                            (1, 23, 1, 1, 1, 1, 1),
                                                            (1, 24, 1, NULL, 1, NULL, 1),
                                                            (1, 25, 1, NULL, NULL, NULL, 1),
                                                            (1, 26, 1, NULL, NULL, NULL, 1),
                                                            (1, 27, 1, 1, 1, 1, 1),
                                                            (1, 28, 1, NULL, 1, NULL, 1),
                                                            (1, 29, 1, 1, 1, 1, 1),
                                                            (1, 30, 1, 1, 1, 1, 1),
                                                            (1, 31, 1, 1, 1, 1, 1),
                                                            (1, 32, 1, 1, 1, 1, 1),
                                                            (1, 33, 1, 1, 1, 1, 1),
                                                            (1, 34, 1, 1, 1, 1, 1),
                                                            (1, 35, 1, NULL, 1, NULL, 1),
                                                            (1, 36, 1, 1, 1, 1, 1),
                                                            (1, 37, 1, 1, 1, 1, 1),
                                                            (1, 38, 1, NULL, 1, NULL, 1),
                                                            (1, 39, 1, NULL, 1, 1, 1),
                                                            (1, 40, 1, NULL, 1, NULL, 1),
                                                            (2, 1, 1, NULL, 1, NULL, 1),
                                                            (2, 2, 1, 1, 1, 1, 1),
                                                            (2, 3, 1, NULL, 1, NULL, 1),
                                                            (2, 4, 1, NULL, 1, NULL, 1),
                                                            (2, 5, 1, 1, 1, 1, 1),
                                                            (2, 6, 1, NULL, 1, NULL, 1),
                                                            (2, 7, 1, 1, 1, 1, 1),
                                                            (2, 8, 1, 1, 1, 1, 1),
                                                            (2, 9, 1, NULL, 1, NULL, 1),
                                                            (2, 10, 1, 1, 1, 1, 1),
                                                            (2, 11, 1, 1, 1, 1, 1),
                                                            (2, 12, 1, NULL, 1, NULL, 1),
                                                            (2, 13, 1, 1, 1, 1, 1),
                                                            (2, 14, 1, 1, 1, 1, 1),
                                                            (2, 15, 1, NULL, 1, NULL, 1),
                                                            (2, 16, 1, NULL, NULL, NULL, 1),
                                                            (2, 17, 1, 1, 1, 1, 1),
                                                            (2, 18, 1, NULL, 1, NULL, 1),
                                                            (2, 19, 1, NULL, NULL, NULL, 1),
                                                            (2, 20, 1, 1, 1, 1, 1),
                                                            (2, 21, 1, NULL, 1, NULL, 1),
                                                            (2, 22, 1, NULL, NULL, NULL, 1),
                                                            (2, 23, 1, 1, 1, 1, 1),
                                                            (2, 24, 1, NULL, 1, NULL, 1),
                                                            (2, 25, 1, NULL, NULL, NULL, 1),
                                                            (2, 26, 1, NULL, NULL, NULL, 1),
                                                            (2, 27, 1, 1, 1, 1, 1),
                                                            (2, 28, 1, NULL, 1, NULL, 1),
                                                            (2, 29, 1, 1, 1, 1, 1),
                                                            (2, 30, 1, 1, 1, 1, 1),
                                                            (2, 31, 1, 1, 1, 1, 1),
                                                            (2, 32, 1, 1, 1, 1, 1),
                                                            (2, 33, 1, 1, 1, 1, 1),
                                                            (2, 34, 1, 1, 1, 1, 1),
                                                            (2, 35, 1, NULL, 1, NULL, 1),
                                                            (2, 36, 1, 1, 1, 1, 1),
                                                            (2, 37, 1, 1, 1, 1, 1),
                                                            (2, 38, 1, NULL, 1, NULL, 1),
                                                            (2, 39, 1, NULL, 1, 1, 1),
                                                            (2, 40, 1, NULL, NULL, NULL, 1),
                                                            (3, 1, 1, NULL, 1, NULL, 0),
                                                            (3, 2, 1, 1, 1, 1, 0),
                                                            (3, 3, 1, NULL, 1, NULL, 0),
                                                            (3, 4, 1, NULL, 1, NULL, 0),
                                                            (3, 5, 1, 1, 1, 1, 0),
                                                            (3, 6, 1, NULL, 1, NULL, 0),
                                                            (3, 7, 1, 1, 1, 1, 0),
                                                            (3, 8, 1, 1, 1, 1, 0),
                                                            (3, 9, 1, NULL, 1, NULL, 0),
                                                            (3, 10, 1, 1, 1, 1, 0),
                                                            (3, 11, 1, 1, 1, 1, 0),
                                                            (3, 12, 1, NULL, 1, NULL, 0),
                                                            (3, 13, 1, 1, 1, 1, 0),
                                                            (3, 14, 1, 1, 1, 1, 0),
                                                            (3, 15, 1, NULL, 1, NULL, 0),
                                                            (3, 16, 1, NULL, NULL, NULL, 0),
                                                            (3, 17, 1, 1, 1, 1, 0),
                                                            (3, 18, 1, NULL, 1, NULL, 0),
                                                            (3, 19, 1, NULL, NULL, NULL, 0),
                                                            (3, 20, 1, 1, 1, 1, 0),
                                                            (3, 21, 1, NULL, 1, NULL, 0),
                                                            (3, 22, 1, NULL, NULL, NULL, 0),
                                                            (3, 23, 1, 1, 1, 1, 0),
                                                            (3, 24, 1, NULL, 1, NULL, 0),
                                                            (3, 25, 1, NULL, NULL, NULL, 0),
                                                            (3, 26, 1, NULL, NULL, NULL, 0),
                                                            (3, 27, 1, 1, 1, 1, 0),
                                                            (3, 28, 1, NULL, 1, NULL, 0),
                                                            (3, 29, 1, 1, 1, 1, 0),
                                                            (3, 30, 1, 1, 1, 1, 0),
                                                            (3, 31, 1, 1, 1, 1, 0),
                                                            (3, 32, 1, 1, 1, 1, 0),
                                                            (3, 33, 1, 1, 1, 1, 0),
                                                            (3, 34, 1, 1, 1, 1, 0),
                                                            (3, 35, 1, NULL, 1, NULL, 0),
                                                            (3, 36, 1, 1, 1, 1, 0),
                                                            (3, 37, 1, 1, 1, 1, 0),
                                                            (3, 38, 1, NULL, 1, NULL, 0),
                                                            (3, 39, 1, NULL, 1, 1, 0),
                                                            (3, 40, 1, NULL, NULL, NULL, 0),
                                                            (3, 1, 1, NULL, 1, NULL, 1),
                                                            (3, 2, 1, 1, 1, 1, 1),
                                                            (3, 3, 1, NULL, 1, NULL, 1),
                                                            (3, 4, 1, NULL, 1, NULL, 1),
                                                            (3, 5, 1, 1, 1, 1, 1),
                                                            (3, 6, 1, NULL, 1, NULL, 1),
                                                            (3, 7, 1, 1, 1, 1, 1),
                                                            (3, 8, 1, 1, 1, 1, 1),
                                                            (3, 9, 1, NULL, 1, NULL, 1),
                                                            (3, 10, 1, 1, 1, 1, 1),
                                                            (3, 11, 1, 1, 1, 1, 1),
                                                            (3, 12, 1, NULL, 1, NULL, 1),
                                                            (3, 13, 1, 1, 1, 1, 1),
                                                            (3, 14, 1, 1, 1, 1, 1),
                                                            (3, 15, 1, NULL, 1, NULL, 1),
                                                            (3, 16, 1, NULL, NULL, NULL, 1),
                                                            (3, 17, 1, 1, 1, 1, 1),
                                                            (3, 18, 1, NULL, 1, NULL, 1),
                                                            (3, 19, 1, NULL, NULL, NULL, 1),
                                                            (3, 20, 1, 1, 1, 1, 1),
                                                            (3, 21, 1, NULL, 1, NULL, 1),
                                                            (3, 22, 1, NULL, NULL, NULL, 1),
                                                            (3, 23, 1, 1, 1, 1, 1),
                                                            (3, 24, 1, NULL, 1, NULL, 1),
                                                            (3, 25, 1, NULL, NULL, NULL, 1),
                                                            (3, 26, 1, NULL, NULL, NULL, 1),
                                                            (3, 27, 1, 1, 1, 1, 1),
                                                            (3, 28, 1, NULL, 1, NULL, 1),
                                                            (3, 29, 1, 1, 1, 1, 1),
                                                            (3, 30, 1, 1, 1, 1, 1),
                                                            (3, 31, 1, 1, 1, 1, 1),
                                                            (3, 32, 1, 1, 1, 1, 1),
                                                            (3, 33, 1, 1, 1, 1, 1),
                                                            (3, 34, 1, 1, 1, 1, 1),
                                                            (3, 35, 1, NULL, 1, NULL, 1),
                                                            (3, 36, 1, 1, 1, 1, 1),
                                                            (3, 37, 1, 1, 1, 1, 1),
                                                            (3, 38, 1, NULL, 1, NULL, 1),
                                                            (3, 39, 1, NULL, 1, 1, 1),
                                                            (3, 40, 1, NULL, NULL, NULL, 1)";
        
        $this->sql = $sql;
        
    }
    
    public function getNotes() {        
        return array();
    }
    
}