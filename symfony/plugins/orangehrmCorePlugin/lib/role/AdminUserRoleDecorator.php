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
	const ADD_VACANCY = "./symfony/web/index.php/recruitment/addJobVacancy";
	const VIEW_VACANCIES = "./symfony/web/index.php/recruitment/viewJobVacancy";
	const ADD_CANDIDATE = "./symfony/web/index.php/recruitment/addCandidate";
	const VIEW_CANDIDATES = "./symfony/web/index.php/recruitment/viewCandidates";
        const CSV_TIMESHEET_EXPORT ="./symfony/web/index.php/csvExport/viewTimesheetCsvExtract";
        const CSV_ATTENDANCE_EXPORT ="./symfony/web/index.php/time/viewAttendanceDataExtract";

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
		$topMenuItem->setDisplayName("Timesheets");
		$topMenuItem->setLink(AdminUserRoleDecorator::VIEW_EMPLOYEE_TIMESHEET);

		$topMenuItemArray = $this->__chkAndPutItemsToArray($topMenuItemArray, $topMenuItem);

		$topMenuItem = new TopMenuItem();
		$topMenuItem->setDisplayName("Attendance");
		$topMenuItem->setLink(AdminUserRoleDecorator::VIEW_ATTENDANCE_RECORD_LINK);

		$topMenuItemArray = $this->__chkAndPutItemsToArray($topMenuItemArray, $topMenuItem);

		$topMenuItem = new TopMenuItem();
		$topMenuItem->setDisplayName("Reports");
		$topMenuItem->setLink(AdminUserRoleDecorator::PROJECT_REPORT_LINK);
        
		$topMenuItemArray = $this->__chkAndPutItemsToArray($topMenuItemArray, $topMenuItem);

		return $topMenuItemArray;
	}

	public function getAccessibleTimeSubMenus() {
		$topMenuItemArray = $this->user->getAccessibleTimeSubMenus();
		$topMenuItem = new TopMenuItem();
		$topMenuItem->setDisplayName(__("Employee Timesheets"));
		$topMenuItem->setLink(AdminUserRoleDecorator::VIEW_EMPLOYEE_TIMESHEET);
		$topMenuItemArray = $this->__chkAndPutItemsToArray($topMenuItemArray, $topMenuItem);
         
        if ($this->isPluginAvailable('orangehrmTimesheetCsvExtractorPlugin')){            
            $topMenuItem = new TopMenuItem();
            $topMenuItem->setDisplayName(__("Export To CSV"));
            $topMenuItem->setLink(AdminUserRoleDecorator::CSV_TIMESHEET_EXPORT);
        }

        $topMenuItemArray = $this->__chkAndPutItemsToArray($topMenuItemArray, $topMenuItem);

        return $topMenuItemArray;
    }

	public function getAccessibleAttendanceSubMenus() {

		$topMenuItemArray = $this->user->getAccessibleAttendanceSubMenus();
		$topMenuItem = new TopMenuItem();
		$topMenuItem->setDisplayName(__("Employee Records"));
		$topMenuItem->setLink(AdminUserRoleDecorator::VIEW_ATTENDANCE_RECORD_LINK);
		$topMenuItemArray = $this->__chkAndPutItemsToArray($topMenuItemArray, $topMenuItem);

		$topMenuItem = new TopMenuItem();
		$topMenuItem->setDisplayName(__("Configuration"));
		$topMenuItem->setLink(AdminUserRoleDecorator::CONFIGURE_LINK);
		$topMenuItemArray = $this->__chkAndPutItemsToArray($topMenuItemArray, $topMenuItem);

		if ($this->isPluginAvailable('orangehrmAttendanceDataExtractorPlugin')){
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
		$topMenuItem->setDisplayName(__("Project Reports"));
		$topMenuItem->setLink(AdminUserRoleDecorator::PROJECT_REPORT_LINK);

		$topMenuItemArray = $this->__chkAndPutItemsToArray($topMenuItemArray, $topMenuItem);

		$topMenuItem = new TopMenuItem();
		$topMenuItem->setDisplayName(__("Employee Reports"));
		$topMenuItem->setLink(AdminUserRoleDecorator::EMPLOYEE_REPORT_LINK);

		$topMenuItemArray = $this->__chkAndPutItemsToArray($topMenuItemArray, $topMenuItem);

		$topMenuItem = new TopMenuItem();
		$topMenuItem->setDisplayName(__("Attendance Summary"));
		$topMenuItem->setLink(AdminUserRoleDecorator::ATTENDANCE_TOTAL_SUMMARY_REPORT_LINK);

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

	/**
	 * Get the employee list ( whole employees )
	 * @return Employee[]
	 */
	public function getEmployeeList() {

		$employeeList = $this->getEmployeeService()->getEmployeeList('empNumber', 'ASC', true);


		if ($employeeList[0]->getEmpNumber() == null) {
			return null;
		} else {
			return $employeeList;
		}
	}

	public function getEmployeeListForAttendanceTotalSummaryReport() {

		$employeeList = $this->getEmployeeService()->getEmployeeList('empNumber', 'ASC', true);

		$employee = new Employee();
		$employee->setEmpNumber('-1');
		$employee->setFirstName("All");

		if ($employeeList[0]->getEmpNumber() == null) {
			$employeeList->add($employee);
			return $employeeList;
		} else {
			$employeeList->add($employee);
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

	/**
	 * Get previous states given workflow, action for this user
	 * @param int $workFlow
	 * @param int $action
	 * @return string
	 */
	public function getAllAlowedRecruitmentApplicationStates($flow) {

	    $accessFlowStateMachineService = new AccessFlowStateMachineService();
	    $applicationStates = $accessFlowStateMachineService->getAllAlowedRecruitmentApplicationStates($flow, AdminUserRoleDecorator::ADMIN_USER);
	    $existingStates = $this->user->getAllAlowedRecruitmentApplicationStates($flow);
	    if (is_null($applicationStates)) {
	        return $existingStates;
	    } else {
	        $applicationStates = array_unique(array_merge($applicationStates, $existingStates));
	        return $applicationStates;
	    }
	}

    public function getActionableTimesheets() {
    
        $accessFlowStateMachinService = new AccessFlowStateMachineService();
        $action = array(PluginWorkflowStateMachine::TIMESHEET_ACTION_APPROVE, PluginWorkflowStateMachine::TIMESHEET_ACTION_REJECT);
        $actionableStatesList = $accessFlowStateMachinService->getActionableStates(PluginWorkflowStateMachine::FLOW_TIME_TIMESHEET, AdminUserRoleDecorator::ADMIN_USER, $action);
        
        $subordinateIdList = $this->getEmployeeService()->getSubordinateIdListBySupervisorId($this->getEmployeeNumber());
        $employeeIdList = $this->getEmployeeService()->getEmployeeIdList(true);
        $fullIdList = array_merge($subordinateIdList, $employeeIdList);
        
        if ($actionableStatesList != null) {
            $timesheetList = $this->getTimesheetService()->getTimesheetListByEmployeeIdAndState($fullIdList, $actionableStatesList, 100);
        }
        
        if ($timesheetList != null) {
            return $timesheetList;
        } else {
            return $this->user->getActionableTimesheets();
        }
    }
	
    public function getEmployeeNameList() {
        $properties = array("empNumber","firstName", "middleName", "lastName", "termination_id");
        return $this->getEmployeeService()->getEmployeePropertyList($properties, 'lastName', 'ASC', false);
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

	public function getActionableStates() {

		return $this->user->getActionableStates();
	}

	public function getAccessibleConfigurationSubMenus() {

		return $this->user->getAccessibleConfigurationSubMenus();
	}

	public function getAllowedCandidateList() {

		$accessFlowStateMachineService = new AccessFlowStateMachineService();
		$allowedCandidateIdList = $accessFlowStateMachineService->getAllowedCandidateList(AdminUserRoleDecorator::ADMIN_USER, null);
		$existingIdList = $this->user->getAllowedCandidateList();
		if (is_null($allowedCandidateIdList)) {
			return $existingIdList;
		} else {
			$allowedCandidateIdList = array_unique(array_merge($allowedCandidateIdList, $existingIdList));
			return $allowedCandidateIdList;
		}
	}

	public function getAllowedProjectList() {
		$accessFlowStateMachineService = new AccessFlowStateMachineService();
		$allowedProjectIdList = $accessFlowStateMachineService->getAllowedProjectList(AdminUserRoleDecorator::ADMIN_USER, null);
		$existingIdList = $this->user->getAllowedProjectList();
		if (is_null($allowedProjectIdList)) {
			return $existingIdList;
		} else {
			$allowedProjectIdList = array_unique(array_merge($allowedProjectIdList, $existingIdList));
			return $allowedProjectIdList;
		}
	}

	public function getAllowedCandidateListToDelete() {

		$accessFlowStateMachineService = new AccessFlowStateMachineService();
		$allowedCandidateIdListToDelete = $accessFlowStateMachineService->getAllowedCandidateList(AdminUserRoleDecorator::ADMIN_USER, null);
		$existingIdList = $this->user->getAllowedCandidateListToDelete();
		if (is_null($allowedCandidateIdListToDelete)) {
			return $existingIdList;
		} else {
			$allowedCandidateIdListToDelete = array_unique(array_merge($allowedCandidateIdListToDelete, $existingIdList));
			return $allowedCandidateIdListToDelete;
		}
	}

	public function getAllowedVacancyList() {

		$accessFlowStateMachineService = new AccessFlowStateMachineService();
		$allowedVacancyIdList = $accessFlowStateMachineService->getAllowedVacancyList(AdminUserRoleDecorator::ADMIN_USER, null);
		$existingIdList = $this->user->getAllowedVacancyList();
		if (is_null($allowedVacancyIdList)) {
			return $existingIdList;
		} else {
			$allowedVacancyIdList = array_unique(array_merge($allowedVacancyIdList, $existingIdList));
			return $allowedVacancyIdList;
		}
	}

	public function getAllowedCandidateHistoryList($candidateId) {

		$accessFlowStateMachineService = new AccessFlowStateMachineService();
		$allowedCandidateHistoryIdList = $accessFlowStateMachineService->getAllowedCandidateHistoryList(AdminUserRoleDecorator::ADMIN_USER, null, $candidateId);
		$existingIdList = $this->user->getAllowedCandidateHistoryList($candidateId);
		if (is_null($allowedCandidateHistoryIdList)) {
			return $existingIdList;
		} else {
			$allowedCandidateHistoryIdList = array_unique(array_merge($allowedCandidateHistoryIdList, $existingIdList));
			return $allowedCandidateHistoryIdList;
		}
	}

	public function getAccessibleRecruitmentMenus() {

		$topMenuItem = new TopMenuItem();
		$topMenuItem->setDisplayName(__("Candidates"));
		$topMenuItem->setLink(AdminUserRoleDecorator::VIEW_CANDIDATES);
		$tempArray = $this->user->getAccessibleRecruitmentMenus();
		$tempArray = $this->__chkAndPutItemsToArray($tempArray, $topMenuItem);

		$topMenuItem = new TopMenuItem();
		$topMenuItem->setDisplayName(__("Vacancies"));
		$topMenuItem->setLink(AdminUserRoleDecorator::VIEW_VACANCIES);
		$tempArray = $this->__chkAndPutItemsToArray($tempArray, $topMenuItem);

		return $tempArray;
	}

	public function isAdmin() {
		return true;
	}

	public function isHiringManager() {
		return $this->user->isHiringManager();
	}

	public function isProjectAdmin() {
		return $this->user->isProjectAdmin();
	}

	public function isInterviewer() {
		return $this->user->isInterviewer();
	}

}
