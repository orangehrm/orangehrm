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
        
        $workflowId = $this->getNextWorkflowId();
        
        UpgradeLogger::writeLogMessage('next workflow id:' . $workflowId);
        
        $sql[4] = "INSERT INTO `ohrm_workflow_state_machine`(`id`, workflow, state, role, action, resulting_state) VALUES
                    ('" . ($workflowId)     . "','3','NOT_EXIST', 'ADMIN','1','ACTIVE'),
                    ('" . ($workflowId + 1) . "','3','ACTIVE',    'ADMIN','2','NOT_EXIST'),
                    ('" . ($workflowId + 2) . "','3','ACTIVE',    'ADMIN','3','TERMINATED'),
                    ('" . ($workflowId + 3) . "','3','TERMINATED','ADMIN','4','ACTIVE'),
                    ('" . ($workflowId + 4) . "','3','TERMINATED','ADMIN','5','NOT_EXIST')"; 
        
        // update last id
        $sql[5] = "UPDATE `hs_hr_unique_id` SET
            last_id = (select MAX(`id`) FROM ohrm_workflow_state_machine) 
            WHERE table_name = 'ohrm_workflow_state_machine' AND `field_name` = 'id';";
        
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
        
        /* pim report missed display fields */
        $sql[8] = "INSERT INTO `ohrm_display_field` (`display_field_id`, `report_group_id`, `name`, `label`, `field_alias`, `is_sortable`, `sort_order`, `sort_field`, `element_type`, `element_property`, `width`, `is_exportable`, `text_alignment_style`, `is_value_list`, `display_field_group_id`, `default_value`, `is_encrypted`, `is_meta`) VALUES
                            (115, 3, 'ohrm_emp_education.institute', 'Institute', 'getInstitute',  'false', null, null, 'label', '<xml><getter>getInstitute</getter></xml>', 80, '0', null, true, 11, '---', false, false),
                            (116, 3, 'ohrm_emp_education.major', 'Major/Specialization', 'getMajor',  'false', null, null, 'label', '<xml><getter>getMajor</getter></xml>', 80, '0', null, true, 11, '---', false, false),
                            (117, 3, 'ohrm_emp_education.start_date', 'Start Date', 'getStartDate',  'false', null, null, 'labelDate', '<xml><getter>getStartDate</getter></xml>', 80, '0', null, true, 11, '---', false, false),
                            (118, 3, 'ohrm_emp_education.end_date', 'End Date', 'getEndDate',  'false', null, null, 'labelDate', '<xml><getter>getEndDate</getter></xml>', 80, '0', null, true, 11, '---', false, false),
                            (119, 3, 'ohrm_emp_license.license_no', 'License Number', 'getLicenseNo',  'false', null, null, 'label', '<xml><getter>getLicenseNo</getter></xml>', 200, '0', null, true, 14, '---', false, false),
                            (120, 3, 'ohrm_emp_termination.note', 'Termination Note', 'getNote',  'false', null, null, 'label', '<xml><getter>getNote</getter></xml>', 100, '0', null, true, 6, '---', false, false)";
        
        /* job, salary, report to and tax custom fields + attachments read only for ESS and Supervisor */
        $sql[9]  = "UPDATE `ohrm_user_role_data_group` SET `can_create` = '0', `can_update` = '0', `can_delete` = '0' WHERE `user_role_id` = '2' AND `data_group_id` = '17'";
        $sql[10] = "UPDATE `ohrm_user_role_data_group` SET `can_create` = '0', `can_update` = '0', `can_delete` = '0' WHERE `user_role_id` = '2' AND `data_group_id` = '18'";
        $sql[11] = "UPDATE `ohrm_user_role_data_group` SET `can_create` = '0', `can_update` = '0', `can_delete` = '0' WHERE `user_role_id` = '2' AND `data_group_id` = '20'";
        $sql[12] = "UPDATE `ohrm_user_role_data_group` SET `can_create` = '0', `can_update` = '0', `can_delete` = '0' WHERE `user_role_id` = '2' AND `data_group_id` = '21'";
        $sql[13] = "UPDATE `ohrm_user_role_data_group` SET `can_create` = '0', `can_update` = '0', `can_delete` = '0' WHERE `user_role_id` = '2' AND `data_group_id` = '23'";
        $sql[14] = "UPDATE `ohrm_user_role_data_group` SET `can_create` = '0', `can_update` = '0', `can_delete` = '0' WHERE `user_role_id` = '2' AND `data_group_id` = '24'";
        $sql[15] = "UPDATE `ohrm_user_role_data_group` SET `can_create` = '0', `can_update` = '0', `can_delete` = '0' WHERE `user_role_id` = '2' AND `data_group_id` = '27'";
        $sql[16] = "UPDATE `ohrm_user_role_data_group` SET `can_create` = '0', `can_update` = '0', `can_delete` = '0' WHERE `user_role_id` = '2' AND `data_group_id` = '28'";
        
        $sql[17] = "UPDATE `ohrm_user_role_data_group` SET `can_create` = '0', `can_update` = '0', `can_delete` = '0' WHERE `user_role_id` = '3' AND `data_group_id` = '17'";
        $sql[18] = "UPDATE `ohrm_user_role_data_group` SET `can_create` = '0', `can_update` = '0', `can_delete` = '0' WHERE `user_role_id` = '3' AND `data_group_id` = '18'";
        $sql[19] = "UPDATE `ohrm_user_role_data_group` SET `can_create` = '0', `can_update` = '0', `can_delete` = '0' WHERE `user_role_id` = '3' AND `data_group_id` = '19'";
        $sql[20] = "UPDATE `ohrm_user_role_data_group` SET `can_create` = '0', `can_update` = '0', `can_delete` = '0' WHERE `user_role_id` = '3' AND `data_group_id` = '20'";
        $sql[21] = "UPDATE `ohrm_user_role_data_group` SET `can_create` = '0', `can_update` = '0', `can_delete` = '0' WHERE `user_role_id` = '3' AND `data_group_id` = '21'";
        $sql[22] = "UPDATE `ohrm_user_role_data_group` SET `can_read` = '0' WHERE `user_role_id` = '3' AND `data_group_id` = '19' AND `self`='0'";
        $sql[23] = "UPDATE `ohrm_user_role_data_group` SET `can_read` = '0' WHERE `user_role_id` = '3' AND `data_group_id` = '20' AND `self`='0'";
        $sql[24] = "UPDATE `ohrm_user_role_data_group` SET `can_read` = '0' WHERE `user_role_id` = '3' AND `data_group_id` = '21' AND `self`='0'";
        $sql[25] = "UPDATE `ohrm_user_role_data_group` SET `can_create` = '0', `can_update` = '0', `can_delete` = '0' WHERE `user_role_id` = '3' AND `data_group_id` = '23'";
        $sql[26] = "UPDATE `ohrm_user_role_data_group` SET `can_create` = '0', `can_update` = '0', `can_delete` = '0' WHERE `user_role_id` = '3' AND `data_group_id` = '24'";
        $sql[27] = "UPDATE `ohrm_user_role_data_group` SET `can_create` = '0', `can_update` = '0', `can_delete` = '0' WHERE `user_role_id` = '3' AND `data_group_id` = '27'";
        $sql[28] = "UPDATE `ohrm_user_role_data_group` SET `can_create` = '0', `can_update` = '0', `can_delete` = '0' WHERE `user_role_id` = '3' AND `data_group_id` = '28'";
        
        $sql[29] = "UPDATE `ohrm_user_role_data_group` SET `can_create` = '0', `can_update` = '0', `can_delete` = '0' WHERE `user_role_id` = '1' AND `data_group_id` = '17' AND `self`='1'";
        $sql[30] = "UPDATE `ohrm_user_role_data_group` SET `can_create` = '0', `can_update` = '0', `can_delete` = '0' WHERE `user_role_id` = '1' AND `data_group_id` = '18' AND `self`='1'";
        $sql[31] = "UPDATE `ohrm_user_role_data_group` SET `can_create` = '0', `can_update` = '0', `can_delete` = '0' WHERE `user_role_id` = '1' AND `data_group_id` = '20' AND `self`='1'";
        $sql[32] = "UPDATE `ohrm_user_role_data_group` SET `can_create` = '0', `can_update` = '0', `can_delete` = '0' WHERE `user_role_id` = '1' AND `data_group_id` = '21' AND `self`='1'";
        $sql[33] = "UPDATE `ohrm_user_role_data_group` SET `can_create` = '0', `can_update` = '0', `can_delete` = '0' WHERE `user_role_id` = '1' AND `data_group_id` = '23' AND `self`='1'";
        $sql[34] = "UPDATE `ohrm_user_role_data_group` SET `can_create` = '0', `can_update` = '0', `can_delete` = '0' WHERE `user_role_id` = '1' AND `data_group_id` = '24' AND `self`='1'";
        $sql[35] = "UPDATE `ohrm_user_role_data_group` SET `can_create` = '0', `can_update` = '0', `can_delete` = '0' WHERE `user_role_id` = '1' AND `data_group_id` = '27' AND `self`='1'";
        $sql[36] = "UPDATE `ohrm_user_role_data_group` SET `can_create` = '0', `can_update` = '0', `can_delete` = '0' WHERE `user_role_id` = '1' AND `data_group_id` = '28' AND `self`='1'";
        
        //label changes
        $sql[37] = "UPDATE `ohrm_display_field` SET `label` = 'Eligibility Status' WHERE `display_field_id` = 87";
        $sql[38] = "UPDATE `ohrm_display_field` SET `label` = 'Eligibility Review Date' WHERE `display_field_id` = 89";
        
        /* Fix for attendance summary total */
        $sql[39] = "UPDATE `ohrm_summary_display_field` SET `label` = 'Time (Hours)' WHERE `summary_display_field_id` = 2";
        
        $this->sql = $sql;
        
    }
    
    protected function getNextWorkflowId() {
        return $this->getScalarValueFromQuery('SELECT MAX(id) FROM ohrm_workflow_state_machine');
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
    
    public function getNotes() {        
        return array();
    }
    
}