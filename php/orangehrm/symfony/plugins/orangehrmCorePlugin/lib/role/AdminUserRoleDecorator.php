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
class AdminUserRoleDecorator extends UserRoleDecorator {
    const ADMIN_USER = "ADMIN";
    const VIEW_EMPLOYEE_TIMESHEET = "./symfony/web/index.php/time/viewEmployeeTimesheet";
    const ATTENDANCE_CONFIGURATION="./symfony/web/index.php/attendance/configure";
    const CONFIGURE_LINK="./symfony/web/index.php/attendance/configure";
    const PROJECT_REPORT_LINK="./symfony/web/index.php/time/displayProjectReportCriteria?reportId=1";
    const EMPLOYEE_REPORT_LINK="./symfony/web/index.php/time/displayEmployeeReportCriteria?reportId=2";
    const ATTENDANCE_TOTAL_SUMMARY_REPORT_LINK="./symfony/web/index.php/time/displayAttendanceSummaryReportCriteria?reportId=4";
    const VIEW_ATTENDANCE_RECORD_LINK="./symfony/web/index.php/attendance/viewAttendanceRecord";
    private $user;
    private $employeeService;
    private $timesheetService;
    private $projectService;
    private $timesheetPeriodService;

    public function __construct(User $user) {

        $this->user = $user;
        parent::setEmployeeNumber($user->getEmployeeNumber());
        parent::setUserId($user->getUserId());
        parent::setUserTimeZoneOffset($user->getUserTimeZoneOffset());
    }

    public function getTimesheetService() {

        if (is_null($this->timesheetService)) {

            $this->timesheetService = new TimesheetService();
        }

        return $this->timesheetService;
    }

    /**
     * Get the Employee Data Access Object
     * @return EmployeeService
     */
    public function getEmployeeService() {

        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
        }

        return $this->employeeService;
    }

    /**
     * Set Employee Data Access Object
     * @param EmployeeService $employeeService
     * @return void
     */
    public function setEmployeeService(EmployeeService $employeeService) {

        $this->EmployeeService = $employeeService;
    }

    /**
     * Get the Project Data Access Object
     * @return ProjectService
     */
    public function getProjectService() {

        if (is_null($this->projectService)) {
            $this->projectService = new ProjectService();
        }

        return $this->projectService;
    }

    /**
     * Set Project Data Access Object
     * @param ProjectService $projectService
     * @return void
     */
    public function setProjectService(ProjectService $projectService) {

        $this->projectService = $projectService;
    }

    public function getTimesheetPeriodService() {

        if (is_null($this->timesheetPeriodService)) {

            $this->timesheetPeriodService = new TimesheetPeriodService();
        }

        return $this->timesheetPeriodService;
    }

    public function getAccessibleTimeMenus() {
        $topMenuItemArray = $this->user->getAccessibleTimeMenus();

        $topMenuItem = new TopMenuItem();
        $topMenuItem->setDisplayName(__("Timesheets"));
        $topMenuItem->setLink(AdminUserRoleDecorator::VIEW_EMPLOYEE_TIMESHEET);

        if (!in_array($topMenuItem, $topMenuItemArray)) {
            array_push($topMenuItemArray, $topMenuItem);
        }

        $topMenuItem = new TopMenuItem();
        $topMenuItem->setDisplayName(__("Attendance"));
        $topMenuItem->setLink(AdminUserRoleDecorator::VIEW_ATTENDANCE_RECORD_LINK);

        if (!in_array($topMenuItem, $topMenuItemArray)) {
            array_push($topMenuItemArray, $topMenuItem);
        }

        $topMenuItem = new TopMenuItem();
        $topMenuItem->setDisplayName(__("Reports"));
        $topMenuItem->setLink(AdminUserRoleDecorator::PROJECT_REPORT_LINK);

        if (!in_array($topMenuItem, $topMenuItemArray)) {
            array_push($topMenuItemArray, $topMenuItem);
        }

        return $topMenuItemArray;
    }

    public function getAccessibleTimeSubMenus() {
        //$topMenuItem = new TopMenuItem();
        //$topMenuItem->setDisplayName(__("Time"));
        //set the link for timesheet configration
        //$topMenuItem->setLink(AdminUserRoleDecorator::VIEW_EMPLOYEE_TIMESHEET);
        //array_push($tempArray, $topMenuItem);
        $topMenuItem = new TopMenuItem();
        $topMenuItem->setDisplayName(__("Employee Timesheets"));
        $topMenuItem->setLink(AdminUserRoleDecorator::VIEW_EMPLOYEE_TIMESHEET);
        $tempArray = $this->user->getAccessibleTimeSubMenus();
        array_push($tempArray, $topMenuItem);

        return $tempArray;
    }

    public function getAccessibleAttendanceSubMenus() {
        $topMenuItem = new TopMenuItem();
        $topMenuItem->setDisplayName(__("Employee Records"));
        $topMenuItem->setLink(AdminUserRoleDecorator::VIEW_ATTENDANCE_RECORD_LINK);
        $tempArray = $this->user->getAccessibleAttendanceSubMenus();
        array_push($tempArray, $topMenuItem);

        $topMenuItem = new TopMenuItem();
        $topMenuItem->setDisplayName(__("Configuration"));
        $topMenuItem->setLink(AdminUserRoleDecorator::CONFIGURE_LINK);

        array_push($tempArray, $topMenuItem);
        return $tempArray;
    }


    public function getAccessibleReportSubMenus() {

        $topMenuItemArray = $this->user->getAccessibleReportSubMenus();

        $topMenuItem = new TopMenuItem();
        $topMenuItem->setDisplayName(__(" Project Reports"));
        $topMenuItem->setLink(AdminUserRoleDecorator::PROJECT_REPORT_LINK);

        if (!in_array($topMenuItem, $topMenuItemArray)) {
            array_push($topMenuItemArray, $topMenuItem);
        }

        $topMenuItem = new TopMenuItem();
        $topMenuItem->setDisplayName(__(" Employee Reports"));
        $topMenuItem->setLink(AdminUserRoleDecorator::EMPLOYEE_REPORT_LINK);

        if (!in_array($topMenuItem, $topMenuItemArray)) {
            array_push($topMenuItemArray, $topMenuItem);
        }

        $topMenuItem = new TopMenuItem();
        $topMenuItem->setDisplayName(__(" Attendance Total Summary Report"));
        $topMenuItem->setLink(AdminUserRoleDecorator::ATTENDANCE_TOTAL_SUMMARY_REPORT_LINK);

        if (!in_array($topMenuItem, $topMenuItemArray)) {
            array_push($topMenuItemArray, $topMenuItem);
        }

        return $topMenuItemArray;
    }

    /**
     * Get the employee list ( whole employees )
     * @return Employee[]
     */
    public function getEmployeeList() {

        $employeeList = $this->getEmployeeService()->getEmployeeList();

        if ($employeeList[0]->getEmpNumber() == null) {
            return null;
        } else {
            return $employeeList;
        }
    }

    /**
     * Get actions that this user can perform on a perticular workflow with the current state
     * @param int $workFlow
     * @param string $state
     * @return string[]
     */
    public function getAllowedActions($workFlow, $state) {

        $accessFlowStateMachineService = new AccessFlowStateMachineService();
        $allowedActionsForAdminUser = $accessFlowStateMachineService->getAllowedActions($workFlow, $state, AdminUserRoleDecorator::ADMIN_USER);
        $existingAllowedActions = $this->user->getAllowedActions($workFlow, $state);


        if (is_null($allowedActionsForAdminUser)) {


            return $existingAllowedActions;
        } else {

            $allowedActionsList = array_unique(array_merge($allowedActionsForAdminUser, $existingAllowedActions));

            return $allowedActionsList;
        }
    }

    /**
     * Get next state given workflow, state and action for this user
     * @param int $workFlow
     * @param string $state
     * @param int $action
     * @return string
     */
    public function getNextState($workFlow, $state, $action) {

        $accessFlowStateMachineService = new AccessFlowStateMachineService();
        $tempNextState = $accessFlowStateMachineService->getNextState($workFlow, $state, AdminUserRoleDecorator::ADMIN_USER, $action);

        $temp = $this->user->getNextState($workFlow, $state, $action);

        if (is_null($tempNextState)) {
            return $temp;
        }

        return $tempNextState;
    }

    public function getActionableTimesheets() {

        $accessFlowStateMachinService = new AccessFlowStateMachineService();
        $action = PluginWorkflowStateMachine::TIMESHEET_ACTION_APPROVE;
        $actionableStatesList = $accessFlowStateMachinService->getActionableStates(PluginWorkflowStateMachine::FLOW_TIME_TIMESHEET, AdminUserRoleDecorator::ADMIN_USER, $action);

        $employeeList = $this->getEmployeeList();
        foreach ($employeeList as $employee) {

            $timesheetList = $this->getTimesheetService()->getTimesheetByEmployeeIdAndState($employee->getEmpNumber(), $actionableStatesList);

            if (!is_null($timesheetList)) {
                foreach ($timesheetList as $timesheet) {

                    $pendingApprovelTimesheetArray["timesheetId"] = $timesheet->getTimesheetId();
                    $pendingApprovelTimesheetArray["employeeFirstName"] = $employee->getFirstName();
                    $pendingApprovelTimesheetArray["employeeLastName"] = $employee->getLastName();
                    $pendingApprovelTimesheetArray["timesheetStartday"] = $timesheet->getStartDate();
                    $pendingApprovelTimesheetArray["timesheetEndDate"] = $timesheet->getEndDate();
                    $pendingApprovelTimesheetArray["employeeId"] = $employee->getEmpNumber();
                    $pendingApprovelTimesheets[] = $pendingApprovelTimesheetArray;
                }
            }
        }

        if ($pendingApprovelTimesheets[0] != null) {

            return $pendingApprovelTimesheets;
        } else {

            return null;
        }
    }

    public function getActionableAttendanceStates($actions) {

        $accessFlowStateMachinService = new AccessFlowStateMachineService();
        $actionableAttendanceStatesForAdminUser = $accessFlowStateMachinService->getActionableStates(PluginWorkflowStateMachine::FLOW_ATTENDANCE, AdminUserRoleDecorator::ADMIN_USER, $actions);


        $actionableAttendanceStates = $this->user->getActionableAttendanceStates($actions);

        if (is_null($actionableAttendanceStatesForAdminUser)) {
            return $actionableAttendanceStates;
        }

        $actionableAttendanceStatesList = array_unique(array_merge($actionableAttendanceStatesForAdminUser, $actionableAttendanceStates));
        return $actionableAttendanceStatesList;
    }

    public function isAllowedToDefineTimeheetPeriod() {
        $isAllowed = $this->user->isAllowedToDefineTimeheetPeriod();
        $isAllowed = true;
        return $isAllowed;
    }

    /* Retrieves all the active projects */

    public function getActiveProjectList() {

        $activeProjectList = $this->getProjectService()->getActiveProjectList();
        return $activeProjectList;
    }

}