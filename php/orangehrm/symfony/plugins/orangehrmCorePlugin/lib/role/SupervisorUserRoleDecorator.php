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
class SupervisorUserRoleDecorator extends UserRoleDecorator {
    const SUPERVISOR_USER = "SUPERVISOR";
    const VIEW_EMPLOYEE_TIMESHEET = "./symfony/web/index.php/time/viewEmployeeTimesheet";
    const EMPLOYEE_REPORT_LINK="./symfony/web/index.php/time/displayEmployeeReportCriteria?reportId=2";
    const VIEW_ATTENDANCE_RECORD_LINK="./symfony/web/index.php/attendance/viewAttendanceRecord";
    private $user;
    private $employeeService;
    private $timesheetService;

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

    public function getAccessibleTimeMenus() {



        $topMenuItem = new TopMenuItem();
        $topMenuItem->setDisplayName(__("Reports"));
        $topMenuItem->setLink(SupervisorUserRoleDecorator::EMPLOYEE_REPORT_LINK);
        $tempArray = $this->user->getAccessibleTimeMenus();

        array_push($tempArray, $topMenuItem);

        return $tempArray;
    }

    public function getAccessibleTimeSubMenus() {

        $topMenuItem = new TopMenuItem();
        $topMenuItem->setDisplayName(__("Employee Timesheets"));
        $topMenuItem->setLink(SupervisorUserRoleDecorator::VIEW_EMPLOYEE_TIMESHEET);
        $tempArray = $this->user->getAccessibleTimeSubMenus();
        array_push($tempArray, $topMenuItem);

        return $tempArray;
    }

    public function getAccessibleAttendanceSubMenus() {
        $topMenuItem = new TopMenuItem();
        $topMenuItem->setDisplayName(__("Employee Records"));
        $topMenuItem->setLink(SupervisorUserRoleDecorator::VIEW_ATTENDANCE_RECORD_LINK);
        $tempArray = $this->user->getAccessibleAttendanceSubMenus();
        array_push($tempArray, $topMenuItem);

        return $tempArray;
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

    public function getEmployeeList() {

        $employeeArray = $this->getEmployeeService()->getSupervisorEmployeeChain($this->getEmployeeNumber());
        return array_unique($employeeArray);
    }

    public function getAllowedActions($workFlow, $state) {

        $accessFlowStateMachineService = new AccessFlowStateMachineService();
        $allowedActionsForSupervisorUser = $accessFlowStateMachineService->getAllowedActions($workFlow, $state, SupervisorUserRoleDecorator::SUPERVISOR_USER);

        $existingAllowedActions = $this->user->getAllowedActions($workFlow, $state);

        if (is_null($allowedActionsForSupervisorUser)) {
            return $existingAllowedActions;
        }

        $allowedActionsList = array_unique(array_merge($allowedActionsForSupervisorUser, $existingAllowedActions));

        return $allowedActionsList;
    }

    public function getNextState($workFlow, $state, $action) {

        $accessFlowStateMachineService = new AccessFlowStateMachineService();
        $tempNextState = $accessFlowStateMachineService->getNextState($workFlow, $state, SupervisorUserRoleDecorator::SUPERVISOR_USER, $action);

        $temp = $this->user->getNextState($workFlow, $state, $action);

        if (is_null($tempNextState)) {
            return $temp;
        }

        return $tempNextState;
    }

    public function getActionableTimesheets() {
        $pendingApprovelTimesheets = null;
        $accessFlowStateMachinService = new AccessFlowStateMachineService();
        $action = PluginWorkflowStateMachine::TIMESHEET_ACTION_APPROVE;
        $actionableStatesList = $accessFlowStateMachinService->getActionableStates(PluginWorkflowStateMachine::FLOW_TIME_TIMESHEET, SupervisorUserRoleDecorator::SUPERVISOR_USER, $action);
        $employeeList = $this->getEmployeeList();

        foreach ($employeeList as $employee) {

            $timesheetList = $this->getTimesheetService()->getTimesheetByEmployeeIdAndState($employee->getEmpNumber(), $actionableStatesList);

            if ($timesheetList != null) {

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
    }

    public function getActionableAttendanceStates($actions) {


        $accessFlowStateMachinService = new AccessFlowStateMachineService();
        $actionableAttendanceStatesForSupervisorUser = $accessFlowStateMachinService->getActionableStates(PluginWorkflowStateMachine::FLOW_ATTENDANCE, SupervisorUserRoleDecorator::SUPERVISOR_USER, $actions);


        $actionableAttendanceStates = $this->user->getActionableAttendanceStates($actions);

        if (is_null($actionableAttendanceStatesForSupervisorUser)) {
            return $actionableAttendanceStates;
        }

        $actionableAttendanceStatesList = array_unique(array_merge($actionableAttendanceStatesForSupervisorUser, $actionableAttendanceStates));
        return $actionableAttendanceStatesList;
    }

    public function getAccessibleProjectSubMenus() {

        $topMenuItem = new TopMenuItem();
        $topMenuItem->setDisplayName(__(" Employee Reports"));
        $topMenuItem->setLink(SupervisorUserRoleDecorator::EMPLOYEE_REPORT_LINK);
        $tempArray = $this->user->getAccessibleProjectSubMenus();
        array_push($tempArray, $topMenuItem);

        return $tempArray;
    }

    public function getActionableStates() {

        $accessFlowStateMachinService = new AccessFlowStateMachineService();
        $actions = array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_PUNCH_IN, PluginWorkflowStateMachine::ATTENDANCE_ACTION_PUNCH_OUT);
        return $accessFlowStateMachinService->getActionableStates(PluginWorkflowStateMachine::FLOW_ATTENDANCE, EssUserRoleDecorator::ESS_USER, $actions);
    }

    public function isAllowedToDefineTimeheetPeriod() {
        return $this->user->isAllowedToDefineTimeheetPeriod();
    }

}