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
	const ATTENDANCE_TOTAL_SUMMARY_REPORT_LINK="./symfony/web/index.php/time/displayAttendanceSummaryReportCriteria?reportId=4";
        const CSV_TIMESHEET_EXPORT ="./symfony/web/index.php/csvExport/viewTimesheetCsvExtract";
        const CSV_ATTENDANCE_EXPORT ="./symfony/web/index.php/time/viewAttendanceDataExtract";

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

	/**
	 * Set Timesheet Data Access Object
	 * @param TimesheetService $timesheetService
	 * @return void
	 */
	public function setTimesheetService(TimesheetService $timesheetService) {

		$this->timesheetService = $timesheetService;
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

	public function getAccessibleTimeMenus() {

		$topMenuItemArray = $this->user->getAccessibleTimeMenus();

		$topMenuItem = new TopMenuItem();
		$topMenuItem->setDisplayName("Reports");
		$topMenuItem->setLink(SupervisorUserRoleDecorator::EMPLOYEE_REPORT_LINK);

		$topMenuItemArray = $this->__chkAndPutItemsToArray($topMenuItemArray, $topMenuItem);

		return $topMenuItemArray;
	}

	private function __chkAndPutItemsToArray($topMenuItemArray, $topMenuItem) {
		$itemIsInArray = false;
		foreach ($topMenuItemArray as $item) {
			if ($topMenuItem->getDisplayName() == $item->getDisplayName()) {
				$itemIsInArray = true;
				break;
			}
		}
		if (!$itemIsInArray) {
			array_push($topMenuItemArray, $topMenuItem);
		}

		return $topMenuItemArray;
	}

	public function getAccessibleTimeSubMenus() {

		$topMenuItemArray = $this->user->getAccessibleTimeSubMenus();
		$topMenuItem = new TopMenuItem();
		$topMenuItem->setDisplayName(__("Employee Timesheets"));
		$topMenuItem->setLink(SupervisorUserRoleDecorator::VIEW_EMPLOYEE_TIMESHEET);
		$topMenuItemArray = $this->__chkAndPutItemsToArray($topMenuItemArray, $topMenuItem);
        
		if ($this->isPluginAvailable('orangehrmTimesheetCsvExtractorPlugin')){       
		    $topMenuItem = new TopMenuItem();
		    $topMenuItem->setDisplayName(__("Export To CSV"));
		    $topMenuItem->setLink(SupervisorUserRoleDecorator::CSV_TIMESHEET_EXPORT);
		    $topMenuItemArray = $this->__chkAndPutItemsToArray($topMenuItemArray, $topMenuItem);

		}
		
                $topMenuItemArray = $this->__chkAndPutItemsToArray($topMenuItemArray, $topMenuItem);

                return $topMenuItemArray;
        }

	public function getAccessibleAttendanceSubMenus() {
            $topMenuItemArray = $this->user->getAccessibleAttendanceSubMenus();
            
            $topMenuItem = new TopMenuItem();
            $topMenuItem->setDisplayName(__("Employee Records"));
            $topMenuItem->setLink(SupervisorUserRoleDecorator::VIEW_ATTENDANCE_RECORD_LINK);
            $topMenuItemArray = $this->__chkAndPutItemsToArray($topMenuItemArray, $topMenuItem);

            if ($this->isPluginAvailable('orangehrmAttendanceDataExtractorPlugin')) {
                $topMenuItem = new TopMenuItem();
                $topMenuItem->setDisplayName(__("Export To CSV"));
                $topMenuItem->setLink(AdminUserRoleDecorator::CSV_ATTENDANCE_EXPORT);
                $topMenuItemArray = $this->__chkAndPutItemsToArray($topMenuItemArray, $topMenuItem);                
            }
            
            return $topMenuItemArray;
        }

	public function getAccessibleReportSubMenus() {

		$topMenuItemArray = $this->user->getAccessibleReportSubMenus();

		$topMenuItem = new TopMenuItem();
		$topMenuItem->setDisplayName(__("Employee Reports"));
		$topMenuItem->setLink(SupervisorUserRoleDecorator::EMPLOYEE_REPORT_LINK);

		$topMenuItemArray = $this->__chkAndPutItemsToArray($topMenuItemArray, $topMenuItem);

		$topMenuItem = new TopMenuItem();
		$topMenuItem->setDisplayName(__("Attendance Summary"));
		$topMenuItem->setLink(AdminUserRoleDecorator::ATTENDANCE_TOTAL_SUMMARY_REPORT_LINK);

		$topMenuItemArray = $this->__chkAndPutItemsToArray($topMenuItemArray, $topMenuItem);

		return $topMenuItemArray;
	}

	public function getEmployeeList() {

		$employeeList = $this->getEmployeeService()->getSubordinateList($this->getEmployeeNumber(), true);
		return $employeeList;
	}
	
    public function getEmployeeNameList() {
        $properties = array("empNumber","firstName", "middleName", "lastName", "termination_id");
        return $this->getEmployeeService()->getSubordinatePropertyListBySupervisorId($this->getEmployeeNumber(), $properties, 'lastName', 'ASC', false);
    }

	public function getEmployeeListForAttendanceTotalSummaryReport() {

		$employeeList = $this->getEmployeeService()->getSubordinateList($this->getEmployeeNumber(), true);
		return $employeeList;
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

	/**
	 * Get previous states given workflow, action for this user
	 * @param int $workFlow
	 * @param int $action
	 * @return string
	 */
	public function getAllAlowedRecruitmentApplicationStates($flow) {
		return $this->user->getAllAlowedRecruitmentApplicationStates($flow);
	}

	public function getActionableTimesheets() {
		$pendingApprovelTimesheets = null;
		$accessFlowStateMachinService = new AccessFlowStateMachineService();
		$action = array(PluginWorkflowStateMachine::TIMESHEET_ACTION_APPROVE, PluginWorkflowStateMachine::TIMESHEET_ACTION_REJECT);
		$actionableStatesList = $accessFlowStateMachinService->getActionableStates(PluginWorkflowStateMachine::FLOW_TIME_TIMESHEET, SupervisorUserRoleDecorator::SUPERVISOR_USER, $action);

        $subordinateIdList = $this->getEmployeeService()->getSubordinateIdListBySupervisorId($this->getEmployeeNumber());

		if ($actionableStatesList != null) {
		    $timesheetList = $this->getTimesheetService()->getTimesheetListByEmployeeIdAndState($subordinateIdList, $actionableStatesList, 100);
		}
        
        if ($timesheetList != null) {
            return $timesheetList;
		} else {
            return $this->user->getActionableTimesheets();
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

	public function isAllowedToDefineTimeheetPeriod() {
		return $this->user->isAllowedToDefineTimeheetPeriod();
	}

	public function getActiveProjectList() {
		$activeProjectList = $this->user->getActiveProjectList();
		return $activeProjectList;
	}

	public function getActionableStates() {

		return $this->user->getActionableStates();
	}

	public function getAccessibleConfigurationSubMenus() {

		return $this->user->getAccessibleConfigurationSubMenus();
	}

	public function getAllowedCandidateList() {

		return $this->user->getAllowedCandidateList();
	}

	public function getAllowedCandidateListToDelete() {

		return $this->user->getAllowedCandidateListToDelete();
	}

	public function getAllowedVacancyList() {
		return $this->user->getAllowedVacancyList();
	}

	public function getAllowedCandidateHistoryList($candidateId) {

		return $this->user->getAllowedCandidateHistoryList($candidateId);
	}

	public function getAccessibleRecruitmentMenus() {
		return $this->user->getAccessibleRecruitmentMenus();
	}

	public function getAllowedProjectList() {
		return $this->user->getAllowedProjectList();
	}

	public function isAdmin() {
		return $this->user->isAdmin();
	}

	public function isProjectAdmin() {
		return $this->user->isProjectAdmin();
	}

	public function isHiringManager() {
		return $this->user->isHiringManager();
	}

	public function isInterviewer() {
		return $this->user->isInterviewer();
	}

}
