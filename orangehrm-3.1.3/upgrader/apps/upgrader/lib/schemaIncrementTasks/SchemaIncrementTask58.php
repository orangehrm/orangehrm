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
 * Changes from 3.1 to 3.1.1
 * 
 * 1) data group, screen changes
 * 2) new table ohrm_data_group_screen
 */
class SchemaIncrementTask58 extends SchemaIncrementTask {

    public $userInputs;

    public function execute() {
        $this->incrementNumber = 58;
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

        $screenId = $this->getNextScreenId();
        $dataGroupId = $this->getNextDataGroupId();
        $userRoleIds = $this->getUserRoleIds();
        $dataGroupIds = $this->getDataGroupIds();
        $screenIds = $this->getScreenIds();

        $sql = array();

        $sql[1] = "CREATE TABLE ohrm_data_group_screen (
                    `id` int AUTO_INCREMENT, 
                    `data_group_id` int, 
                    `screen_id` int, 
                    `permission` int,
                    PRIMARY KEY(`id`)
                ) ENGINE = INNODB DEFAULT CHARSET=utf8;";

        $sql[2] = "alter table ohrm_data_group_screen
                    add foreign key (data_group_id) references ohrm_data_group(id) on delete cascade;";

        $sql[3] = "alter table ohrm_data_group_screen
                    add foreign key (screen_id) references ohrm_screen(id) on delete cascade;";

        $sql[4] = "UPDATE ohrm_screen SET name='View Project Report Criteria'
                    WHERE id={$screenIds['displayProjectReportCriteria']};";

        $sql[5] = "UPDATE ohrm_screen SET name='View Employee Report Criteria'
                    WHERE id={$screenIds['displayEmployeeReportCriteria']};";

        $sql[6] = "INSERT INTO ohrm_screen (`id`, `name`, `module_id`, `action_url`) VALUES
                    (" . ($screenId) . ", 'Save Job Title', 2, 'saveJobTitle'),
                    (" . ($screenId+1) . ", 'Delete Job Title', 2, 'deleteJobTitle'),
                    (" . ($screenId+2) . ", 'Save Pay Grade', 2, 'payGrade'),
                    (" . ($screenId+3) . ", 'Delete Pay Grade', 2, 'deletePayGrades'),
                    (" . ($screenId+4) . ", 'Save Pay Grade Currency', 2, 'savePayGradeCurrency'),
                    (" . ($screenId+5) . ", 'Delete Pay Grade Currency', 2, 'deletePayGradeCurrency'),
                    (" . ($screenId+6) . ", 'Add Customer', 2, 'addCustomer'),
                    (" . ($screenId+7) . ", 'Delete Customer', 2, 'deleteCustomer'),
                    (" . ($screenId+8) . ", 'Save Project', 2, 'saveProject'),
                    (" . ($screenId+9) . ", 'Delete Project', 2, 'deleteProject'),
                    (" . ($screenId+10) . ", 'Add Project Adtivity', 2, 'addProjectActivity'),
                    (" . ($screenId+11) . ", 'Delete Project Adtivity', 2, 'deleteProjectActivity'),
                    (" . ($screenId+12) . ", 'Define PIM reports', 1, 'definePredefinedReport'),
                    (" . ($screenId+13) . ", 'Display PIM reports', 1, 'displayPredefinedReport'),
                    (" . ($screenId+14) . ", 'Add Job Vacancy', 7, 'addJobVacancy'),
                    (" . ($screenId+15) . ", 'Delete Job Vacancy', 7, 'deleteJobVacancy'),
                    (" . ($screenId+16) . ", 'Add Candidate', 7, 'addCandidate'),
                    (" . ($screenId+17) . ", 'Delete Candidate', 7, 'deleteCandidateVacancies'),
                    (" . ($screenId+18) . ", 'View Leave Request', 4, 'viewLeaveRequest'),
                    (" . ($screenId+19) . ", 'Change Leave Status', 4, 'changeLeaveStatus'),
                    (" . ($screenId+20) . ", 'Terminate Employment', 3, 'terminateEmployement'),
                    (" . ($screenId+21) . ", 'View Attendance Summary Report', 5, 'displayAttendanceSummaryReport'),
                    (" . ($screenId+22) . ", 'View Project Activity Details Report', 5, 'displayProjectActivityDetailsReport');";

        $sql[7] = "INSERT INTO ohrm_user_role_screen (user_role_id, screen_id, can_read, can_create, can_update, can_delete) VALUES
                    ({$userRoleIds['Admin']}, " . ($screenId) . ", 1, 1, 1, 1),
                    ({$userRoleIds['Admin']}, " . ($screenId+1) . ", 1, 1, 1, 1),
                    ({$userRoleIds['Admin']}, " . ($screenId+2) . ", 1, 1, 1, 1),
                    ({$userRoleIds['Admin']}, " . ($screenId+3) . ", 1, 1, 1, 1),
                    ({$userRoleIds['Admin']}, " . ($screenId+4) . ", 1, 1, 1, 1),
                    ({$userRoleIds['Admin']}, " . ($screenId+5) . ", 1, 1, 1, 1),
                    ({$userRoleIds['Admin']}, " . ($screenId+6) . ", 1, 1, 1, 1),
                    ({$userRoleIds['Admin']}, " . ($screenId+7) . ", 1, 1, 1, 1),
                    ({$userRoleIds['Admin']}, " . ($screenId+8) . ", 1, 1, 1, 1),
                    ({$userRoleIds['ProjectAdmin']}, " . ($screenId+8) . ", 1, 1, 1, 1),
                    ({$userRoleIds['Admin']}, " . ($screenId+9) . ", 1, 1, 1, 1),
                    ({$userRoleIds['Admin']}, " . ($screenId+10) . ", 1, 1, 1, 1),
                    ({$userRoleIds['ProjectAdmin']}, " . ($screenId+10) . ", 1, 1, 1, 1),
                    ({$userRoleIds['Admin']}, " . ($screenId+11) . ", 1, 1, 1, 1),
                    ({$userRoleIds['ProjectAdmin']}, " . ($screenId+11) . ", 1, 1, 1, 1),
                    ({$userRoleIds['Admin']}, " . ($screenId+12) . ", 1, 1, 1, 1),
                    ({$userRoleIds['Admin']}, " . ($screenId+13) . ", 1, 1, 1, 1),
                    ({$userRoleIds['Admin']}, " . ($screenId+14)  . ", 1, 1, 1, 1),
                    ({$userRoleIds['Admin']}, " . ($screenId+15) . ", 1, 1, 1, 1),
                    ({$userRoleIds['Admin']}, " . ($screenId+16) . ", 1, 1, 1, 1),
                    ({$userRoleIds['Interviewer']}, " . ($screenId+16) . ", 1, 1, 1, 1),
                    ({$userRoleIds['HiringManager']}, " . ($screenId+16) . ", 1, 1, 1, 1),
                    ({$userRoleIds['Admin']}, " . ($screenId+17) . ", 1, 1, 1, 1),
                    ({$userRoleIds['HiringManager']}, " . ($screenId+17) . ", 1, 1, 1, 1),
                    ({$userRoleIds['Admin']}, " . ($screenId+18) . ", 1, 1, 1, 1),
                    ({$userRoleIds['ESS']}, " . ($screenId+18) . ", 1, 1, 1, 1),
                    ({$userRoleIds['Supervisor']}, " . ($screenId+18) . ", 1, 1, 1, 1),
                    ({$userRoleIds['Admin']}, " . ($screenId+19) . ", 1, 0, 1, 0),
                    ({$userRoleIds['ESS']}, " . ($screenId+19) . ", 1, 0, 1, 0),
                    ({$userRoleIds['Supervisor']}, " . ($screenId+19) . ", 1, 0, 1, 0),
                    ({$userRoleIds['Admin']}, " . ($screenId+20) . ", 1, 0, 0, 0),    
                    ({$userRoleIds['Admin']}, " . ($screenId+21) . ", 1, 1, 1, 1),
                    ({$userRoleIds['Supervisor']}, " . ($screenId+21) . ", 1, 1, 1, 1),
                    ({$userRoleIds['Admin']}, " . ($screenId+22) . ", 1, 1, 1, 1),
                    ({$userRoleIds['ProjectAdmin']}, " . ($screenId+22) . ", 1, 1, 1, 1);";

        $sql[8] = "INSERT INTO `ohrm_data_group` (`id`, `name`, `description`, `can_read`, `can_create`, `can_update`, `can_delete`) VALUES
                    (" . ($dataGroupId) . ", 'job_titles', 'Admin - Job Titles', 1, 1, 1, 1),
                    (" . ($dataGroupId+1) . ", 'pay_grades', 'Admin - Pay Grades', 1, 1, 1, 1),
                    (" . ($dataGroupId+2) . ", 'time_customers', 'Time - Project Info - Customers', 1, 1, 1, 1),
                    (" . ($dataGroupId+3) . ", 'time_projects', 'Time - Project Info - Projects', 1, 1, 1, 1),
                    (" . ($dataGroupId+4) . ", 'pim_reports', 'PIM - Reports', 1, 1, 1, 1),
                    (" . ($dataGroupId+5) . ", 'attendance_configuration', 'Time - Attendance Configuration', 1, 0, 1, 0),
                    (" . ($dataGroupId+6) . ", 'attendance_records', 'Time - Attendance Records', 1, 0, 0, 0),
                    (" . ($dataGroupId+7) . ", 'time_project_reports', 'Time - Project Reports', 1, 0, 0, 0),
                    (" . ($dataGroupId+8) . ", 'time_employee_reports', 'Time - Employee Reports', 1, 0, 0, 0),
                    (" . ($dataGroupId+9) . ", 'attendance_summary', 'Time - Attendance Summary', 1, 0, 0, 0),
                    (" . ($dataGroupId+10) . ", 'leave_period', 'Leave - Leave Period', 1, 0, 1, 0),
                    (" . ($dataGroupId+11) . ", 'leave_types', 'Leave - Leave Types', 1, 1, 1, 1),
                    (" . ($dataGroupId+12) . ", 'work_week', 'Leave - Work Week', 1, 0, 1, 0),
                    (" . ($dataGroupId+13) . ", 'holidays', 'Leave - Holidays', 1, 1, 1, 1),
                    (" . ($dataGroupId+14) . ", 'recruitment_vacancies', 'Recruitment - Vacancies', 1, 1, 1, 1),
                    (" . ($dataGroupId+15) . ", 'recruitment_candidates', 'Recruitment - Candidates', 1, 1, 1, 1),
                    (" . ($dataGroupId+16) . ", 'time_employee_timesheets', 'Time - Employee Timesheets', 1, 0, 0, 0),
                    (" . ($dataGroupId+17) . ", 'leave_list', 'Leave - Leave List', 1, 0, 0, 0),
                    (" . ($dataGroupId+18) . ", 'leave_list_comments', 'Leave - Leave List - Comments', 0, 1, 0, 0);";

        // Delete all Admin self permissions (since they duplicate ESS self permission), except for leave_entitlements
        // Admin can add leave_entitlements for himself.
        
        $sql[9] = "DELETE FROM `ohrm_user_role_data_group` WHERE user_role_id = {$userRoleIds['Admin']} AND self = 1 AND data_group_id != {$dataGroupIds['leave_entitlements']}";
        
        $sql[10] = "INSERT INTO `ohrm_user_role_data_group` (`user_role_id`, `data_group_id`, `can_read`, `can_create`, `can_update`, `can_delete`, `self`) VALUES
                    ({$userRoleIds['Admin']}, " . ($dataGroupId) . ", 1, 1, 1, 1, 0),
                    ({$userRoleIds['ESS']}, " . ($dataGroupId) . ", 0, 0, 0, 0, 0),
                    ({$userRoleIds['Supervisor']}, " . ($dataGroupId) . ", 0, 0, 0, 0, 0),

                    ({$userRoleIds['Admin']}, " . ($dataGroupId+1) . ", 1, 1, 1, 1, 0),
                    ({$userRoleIds['ESS']}, " . ($dataGroupId+1) . ", 0, 0, 0, 0, 0),
                    ({$userRoleIds['Supervisor']}, " . ($dataGroupId+1) . ", 0, 0, 0, 0, 0),

                    ({$userRoleIds['Admin']}, " . ($dataGroupId+2) . ", 1, 1, 1, 1, 0),
                    ({$userRoleIds['ESS']}, " . ($dataGroupId+2) . ", 0, 0, 0, 0, 0),
                    ({$userRoleIds['Supervisor']}, " . ($dataGroupId+2) . ", 0, 0, 0, 0, 0),

                    ({$userRoleIds['Admin']}, " . ($dataGroupId+3) . ", 1, 1, 1, 1, 0),
                    ({$userRoleIds['ESS']}, " . ($dataGroupId+3) . ", 0, 0, 0, 0, 0),
                    ({$userRoleIds['Supervisor']}, " . ($dataGroupId+3) . ", 0, 0, 0, 0, 0),
                    ({$userRoleIds['ProjectAdmin']}, " . ($dataGroupId+3) . ", 1, 0, 1, 0, 0),

                    ({$userRoleIds['Admin']}, " . ($dataGroupId+4) . ", 1, 1, 1, 1, 0),
                    ({$userRoleIds['ESS']}, " . ($dataGroupId+4) . ", 0, 0, 0, 0, 0),
                    ({$userRoleIds['Supervisor']}, " . ($dataGroupId+4) . ", 0, 0, 0, 0, 0),

                    ({$userRoleIds['Admin']}, " . ($dataGroupId+5) . ", 1, NULL, 1, NULL, 0),
                    ({$userRoleIds['ESS']}, " . ($dataGroupId+5) . ", 0, 0, 0, 0, 0),
                    ({$userRoleIds['Supervisor']}, " . ($dataGroupId+5) . ", 0, 0, 0, 0, 0),

                    ({$userRoleIds['Admin']}, " . ($dataGroupId+6) . ", 1, 0, 0, 0, 0),
                    ({$userRoleIds['ESS']}, " . ($dataGroupId+6) . ", 0, 0, 0, 0, 0),
                    ({$userRoleIds['ESS']}, " . ($dataGroupId+6) . ", 1, 0, 0, 0, 1),
                    ({$userRoleIds['Supervisor']}, " . ($dataGroupId+6) . ", 1, 0, 0, 0, 0),

                    ({$userRoleIds['Admin']}, " . ($dataGroupId+7) . ", 1, 0, 0, 0, 0),
                    ({$userRoleIds['ESS']}, " . ($dataGroupId+7) . ", 0, 0, 0, 0, 0),
                    ({$userRoleIds['Supervisor']}, " . ($dataGroupId+7) . ", 0, 0, 0, 0, 0),
                    ({$userRoleIds['ProjectAdmin']}, " . ($dataGroupId+7) . ", 1, 0, 0, 0, 0),

                    ({$userRoleIds['Admin']}, " . ($dataGroupId+8) . ", 1, 0, 0, 0, 0),
                    ({$userRoleIds['ESS']}, " . ($dataGroupId+8) . ", 0, 0, 0, 0, 0),
                    ({$userRoleIds['Supervisor']}, " . ($dataGroupId+8) . ", 1, 0, 0, 0, 0),

                    ({$userRoleIds['Admin']}, " . ($dataGroupId+9) . ", 1, 0, 0, 0, 0),
                    ({$userRoleIds['ESS']}, " . ($dataGroupId+9) . ", 0, 0, 0, 0, 0),
                    ({$userRoleIds['Supervisor']}, " . ($dataGroupId+9) . ", 1, 0, 0, 0, 0),

                    ({$userRoleIds['Admin']}, " . ($dataGroupId+10) . ", 1, NULL, 1, NULL, 0),
                    ({$userRoleIds['ESS']}, " . ($dataGroupId+10) . ", 0, 0, 0, 0, 0),
                    ({$userRoleIds['Supervisor']}, " . ($dataGroupId+10) . ", 0, 0, 0, 0, 0),

                    ({$userRoleIds['Admin']}, " . ($dataGroupId+11) . ", 1, 1, 1, 1, 0),
                    ({$userRoleIds['ESS']}, " . ($dataGroupId+11) . ", 0, 0, 0, 0, 0),
                    ({$userRoleIds['Supervisor']}, " . ($dataGroupId+11) . ", 0, 0, 0, 0, 0),

                    ({$userRoleIds['Admin']}, " . ($dataGroupId+12) . ", 1, 0, 1, 0, 0),
                    ({$userRoleIds['ESS']}, " . ($dataGroupId+12) . ", 0, 0, 0, 0, 0),
                    ({$userRoleIds['Supervisor']}, " . ($dataGroupId+12) . ", 0, 0, 0, 0, 0),

                    ({$userRoleIds['Admin']}, " . ($dataGroupId+13) . ", 1, 1, 1, 1, 0),
                    ({$userRoleIds['ESS']}, " . ($dataGroupId+13) . ", 0, 0, 0, 0, 0),
                    ({$userRoleIds['Supervisor']}, " . ($dataGroupId+13) . ", 0, 0, 0, 0, 0),

                    ({$userRoleIds['Admin']}, " . ($dataGroupId+14) . ", 1, 1, 1, 1, 0),
                    ({$userRoleIds['ESS']}, " . ($dataGroupId+14) . ", 0, 0, 0, 0, 0),
                    ({$userRoleIds['Supervisor']}, " . ($dataGroupId+14) . ", 0, 0, 0, 0, 0),

                    ({$userRoleIds['Admin']}, " . ($dataGroupId+15) . ", 1, 1, 1, 1, 0),
                    ({$userRoleIds['HiringManager']}, " . ($dataGroupId+15) . ", 1, 1, 1, 1, 0),
                    ({$userRoleIds['Interviewer']}, " . ($dataGroupId+15) . ", 1, 0, 1, 0, 0),

                    ({$userRoleIds['Admin']}, " . ($dataGroupId+16) . ", 1, 0, 0, 0, 0),
                    ({$userRoleIds['ESS']}, " . ($dataGroupId+16) . ", 0, 0, 0, 0, 0),
                    ({$userRoleIds['ESS']}, " . ($dataGroupId+16) . ", 1, 0, 0, 0, 1),
                    ({$userRoleIds['Supervisor']}, " . ($dataGroupId+16) . ", 1, 0, 0, 0, 0),

                    ({$userRoleIds['Admin']}, " . ($dataGroupId+17) . ", 1, 0, 0, 0, 0),
                    ({$userRoleIds['ESS']}, " . ($dataGroupId+17) . ", 1, 0, 0, 0, 1),
                    ({$userRoleIds['Supervisor']}, " . ($dataGroupId+17) . ", 1, 0, 0, 0, 0),
                    ({$userRoleIds['Admin']}, " . ($dataGroupId+18) . ", 0, 1, 0, 0, 0),
                    ({$userRoleIds['ESS']}, " . ($dataGroupId+18) . ", 0, 1, 0, 0, 1),
                    ({$userRoleIds['Supervisor']}, " . ($dataGroupId+18) . ", 0, 1, 0, 0, 0);";
                        
        $sql[11] = "INSERT INTO `ohrm_data_group_screen`(`data_group_id`, `screen_id`, `permission`) VALUES
                    ({$dataGroupIds['leave_entitlements']}, {$screenIds['viewLeaveEntitlements']}, 1),
                    ({$dataGroupIds['leave_entitlements']}, {$screenIds['addLeaveEntitlement']}, 2),
                    ({$dataGroupIds['leave_entitlements']}, {$screenIds['editLeaveEntitlement']}, 3),
                    ({$dataGroupIds['leave_entitlements']}, {$screenIds['deleteLeaveEntitlements']}, 4),

                    ({$dataGroupIds['leave_entitlements_usage_report']}, {$screenIds['viewLeaveBalanceReport']}, 1),

                    (" . ($dataGroupId) . ", {$screenIds['viewJobTitleList']}, 1),
                    (" . ($dataGroupId) . ", " . ($screenId) . ", 1),
                    (" . ($dataGroupId) . ", " . ($screenId) . ", 2),
                    (" . ($dataGroupId) . ", " . ($screenId) . ", 3),
                    (" . ($dataGroupId) . ", " . ($screenId+1) . ", 4),

                    (" . ($dataGroupId+1) . ", {$screenIds['viewPayGrades']}, 1),
                    (" . ($dataGroupId+1) . ", " . ($screenId+2) . ", 1),
                    (" . ($dataGroupId+1) . ", " . ($screenId+2) . ", 2),
                    (" . ($dataGroupId+1) . ", " . ($screenId+2) . ", 3),
                    (" . ($dataGroupId+1) . ", " . ($screenId+3) . ", 4),
                    (" . ($dataGroupId+1) . ", " . ($screenId+4) . ", 3),
                    (" . ($dataGroupId+1) . ", " . ($screenId+5) . ", 3),

                    (" . $dataGroupId . ", {$screenIds['viewAdminModule']}, 1),
                    (" . ($dataGroupId+1) . ", {$screenIds['viewAdminModule']}, 1),

                    (" . ($dataGroupId+2) . ", {$screenIds['viewCustomers']}, 1),
                    (" . ($dataGroupId+2) . ", " . ($screenId+6) . ", 2),
                    (" . ($dataGroupId+2) . ", " . ($screenId+6) . ", 3),
                    (" . ($dataGroupId+2) . ", " . ($screenId+7) . ", 4),

                    (" . ($dataGroupId+3) . ", {$screenIds['viewProjects']}, 1),
                    (" . ($dataGroupId+3) . ", " . ($screenId+8) . ", 1),
                    (" . ($dataGroupId+3) . ", " . ($screenId+8) . ", 2),
                    (" . ($dataGroupId+3) . ", " . ($screenId+8) . ", 3),
                    (" . ($dataGroupId+3) . ", " . ($screenId+9) . ", 4),
                    (" . ($dataGroupId+3) . ", " . ($screenId+10) . ", 2),
                    (" . ($dataGroupId+3) . ", " . ($screenId+10) . ", 3),
                    (" . ($dataGroupId+3) . ", " . ($screenId+11) . ", 2),
                    (" . ($dataGroupId+3) . ", " . ($screenId+11) . ", 3),

                    (" . ($dataGroupId+4) . ", {$screenIds['viewDefinedPredefinedReports']}, 1),
                    (" . ($dataGroupId+4) . ", {$screenIds['viewDefinedPredefinedReports']}, 4),
                    (" . ($dataGroupId+4) . ", " . ($screenId+12) . ", 2),
                    (" . ($dataGroupId+4) . ", " . ($screenId+12) . ", 3),
                    (" . ($dataGroupId+4) . ", " . ($screenId+13) . ", 1),

                    (" . ($dataGroupId+5) . ", {$screenIds['configure']}, 1),
                    (" . ($dataGroupId+5) . ", {$screenIds['configure']}, 3),

                    (" . ($dataGroupId+6) . ", {$screenIds['viewAttendanceRecord']}, 1),

                    (" . ($dataGroupId+7) . ", {$screenIds['displayProjectReportCriteria']}, 1),
                    (" . ($dataGroupId+7) . ", " . ($screenId+22) . ", 1),

                    (" . ($dataGroupId+8) . ", {$screenIds['displayEmployeeReportCriteria']}, 1),

                    (" . ($dataGroupId+9) . ", {$screenIds['displayAttendanceSummaryReportCriteria']}, 1),
                    (" . ($dataGroupId+9) . ", " . ($screenId+21) . ", 1),

                    (" . ($dataGroupId+10) . ", {$screenIds['defineLeavePeriod']}, 1),
                    (" . ($dataGroupId+10) . ", {$screenIds['defineLeavePeriod']}, 3),

                    (" . ($dataGroupId+11) . ", {$screenIds['leaveTypeList']}, 1),
                    (" . ($dataGroupId+11) . ", {$screenIds['defineLeaveType']}, 1),
                    (" . ($dataGroupId+11) . ", {$screenIds['defineLeaveType']}, 2),
                    (" . ($dataGroupId+11) . ", {$screenIds['defineLeaveType']}, 3),
                    (" . ($dataGroupId+11) . ", {$screenIds['undeleteLeaveType']}, 2),
                    (" . ($dataGroupId+11) . ", {$screenIds['deleteLeaveType']}, 4),

                    (" . ($dataGroupId+12) . ", {$screenIds['defineWorkWeek']}, 1),
                    (" . ($dataGroupId+12) . ", {$screenIds['defineWorkWeek']}, 3),

                    (" . ($dataGroupId+13) . ", {$screenIds['viewHolidayList']}, 1),
                    (" . ($dataGroupId+13) . ", {$screenIds['defineHoliday']}, 2),
                    (" . ($dataGroupId+13) . ", {$screenIds['defineHoliday']}, 3),
                    (" . ($dataGroupId+13) . ", {$screenIds['deleteHoliday']}, 4),

                    (" . ($dataGroupId+14) . ", {$screenIds['viewJobVacancy']}, 1),
                    (" . ($dataGroupId+14) . ", " . ($screenId+14) . ", 1),
                    (" . ($dataGroupId+14) . ", " . ($screenId+14) . ", 2),
                    (" . ($dataGroupId+14) . ", " . ($screenId+14) . ", 3),
                    (" . ($dataGroupId+14) . ", " . ($screenId+15) . ", 4),

                    (" . ($dataGroupId+15) . ", {$screenIds['viewCandidates']}, 1),
                    (" . ($dataGroupId+15) . ", " . ($screenId+16) . ", 1),
                    (" . ($dataGroupId+15) . ", " . ($screenId+16) . ", 2),
                    (" . ($dataGroupId+15) . ", " . ($screenId+16) . ", 3),
                    (" . ($dataGroupId+15) . ", " . ($screenId+17) . ", 4),

                    (" . ($dataGroupId+14) . ", {$screenIds['viewRecruitmentModule']}, 1),
                    (" . ($dataGroupId+15) . ", {$screenIds['viewRecruitmentModule']}, 1),

                    (" . ($dataGroupId+16) . ", {$screenIds['viewEmployeeTimesheet']}, 1),

                    (" . ($dataGroupId+17) . ", {$screenIds['viewLeaveList']}, 1),
                    (" . ($dataGroupId+17) . ", " . ($screenId+18) . ", 1),
                    (" . ($dataGroupId+17) . ", " . ($screenId+19) . ", 1);";

        $sql[12] = "UPDATE ohrm_module_default_page SET action='time/timesheetPeriodNotDefined'
                    WHERE module_id=5 AND user_role_id={$userRoleIds['ESS']};";


        // Allow null in reviewer_id
        $sql[13] = "ALTER TABLE hs_hr_performance_review 
                        CHANGE reviewer_id reviewer_id int(13) null;";
        
        // Delete records with invalid employee_id (linked to deleted ids)        
        $sql[14] = "delete from hs_hr_performance_review where employee_id not in (select emp_number from hs_hr_employee);";
        
        // Set reviewer_id = null where reviewer employee is deleted
        $sql[15] = "update hs_hr_performance_review set reviewer_id = null where reviewer_id not in (select emp_number from hs_hr_employee);";
        
        // Add constraints
        $sql[16] = "alter table hs_hr_performance_review
                        add constraint foreign key (employee_id)
                            references hs_hr_employee (emp_number) on delete cascade;";

        $sql[17] = "alter table hs_hr_performance_review
                        add constraint foreign key (reviewer_id)
                            references hs_hr_employee (emp_number) on delete set null;";

        // Deleting action SAVE from Time work flow since it is not in use
        $sql[18] = "DELETE FROM ohrm_workflow_state_machine WHERE workflow = 0 and action = '6'";
        
        $sql[19] = "CREATE TABLE ohrm_plugin (
                    `id` int not null AUTO_INCREMENT, 
                    `name` varchar(100) not null,
                    `version` varchar(32),
                    primary key (`id`),
                    key (`name`)
                ) ENGINE = INNODB DEFAULT CHARSET=utf8;";        
    
        $this->sql = $sql;
    }

    public function getNotes() {
        
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

    public function getNextScreenId() {
        return $this->getScalarValueFromQuery('SELECT MAX(id) FROM ohrm_screen');
    }

    public function getNextDataGroupId() {
        return $this->getScalarValueFromQuery('SELECT MAX(id) FROM ohrm_data_group');
    }
    
    /**
     * Get user role ids into an array indexed by user role name
     * Eg:
     *  array('Admin' => 1, 'ESS' => 2, 'Supervisor' => 3 ....);
     * 
     * @return array Array of user role ids
     */
    public function getUserRoleIds() {
        
        $userRoleIds = array();
        $query = 'SELECT id, name FROM ohrm_user_role';
        $result = $this->upgradeUtility->executeSql($query);
        
        if ($result) {
            while ($row = mysqli_fetch_row($result)) {
                $userRoleIds[$row[1]] = $row[0];
            }

            /* free result set */
            mysqli_free_result($result);
        }
        
        return $userRoleIds;
    }
    
    /**
     * Get data group ids into an array indexed by data group name
     * Eg:
     *  array('Admin' => 1, 'ESS' => 2, 'Supervisor' => 3 ....);
     * 
     * @return array Array of user role ids
     */
    public function getDataGroupIds() {
        
        $dataGroupIds = array();
        $query = 'SELECT id, name FROM ohrm_data_group';
        $result = $this->upgradeUtility->executeSql($query);
        
        if ($result) {
            while ($row = mysqli_fetch_row($result)) {
                $dataGroupIds[$row[1]] = $row[0];
            }

            /* free result set */
            mysqli_free_result($result);
        }
        
        return $dataGroupIds;
    }   
    
    /**
     * Get screen ids into an array indexed by action_url name
     * Eg:
     *  array('Admin' => 1, 'ESS' => 2, 'Supervisor' => 3 ....);
     * 
     * @return array Array of user role ids
     */
    public function getScreenIds() {
        
        $screenIds = array();
        $query = 'SELECT id, action_url FROM ohrm_screen';
        $result = $this->upgradeUtility->executeSql($query);
        
        if ($result) {
            while ($row = mysqli_fetch_row($result)) {
                $screenIds[$row[1]] = $row[0];
            }

            /* free result set */
            mysqli_free_result($result);
        }
        
        return $screenIds;
    }       

}