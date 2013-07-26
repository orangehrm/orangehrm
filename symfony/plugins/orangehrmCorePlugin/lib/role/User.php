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
class User {

	private $topMenuItemsArray = array();
	private $employeeList = array();
	private $activeProjectList = array();
	private $empNumber;
	private $allowedActions = array();
	private $nextState;
	private $userId;
	private $userTimeZoneOffset;
	private $canDefineTimesheetPeriod = false;
	private $actionableStates = array();
	private $previousStates = array();
	private $applicationStates = array();
	private $actionableTimesheets = null;
	private $candidateList = array();
	private $projectList = array();
	private $candidateListToDelete = array();
	private $vacancyList = array();
	private $candidateHistoryList = array();
	private $isAdmin = false;	
	private $isProjectAdmin = false;	
	private $isHiringManager = false;
	private $isInterviewer = false;
	private $employeeListForAttendanceTotalSummaryReport;
	private $employeeNameList = null;

        // Not in use
	public function getEmployeeListForAttendanceTotalSummaryReport() {
		return $this->employeeListForAttendanceTotalSummaryReport;
	}

        // Not in use
	public function getAccessibleTimeMenus() {

		return $this->topMenuItemsArray;
	}

        // Not in use
	public function getActionableTimesheets() {

		return $this->actionableTimesheets;
	}
	
    public function getEmployeeNameList() {
        return $this->employeeNameList;
    }

        // Not in use
	public function getAccessibleTimeSubMenus() {

		return $this->topMenuItemsArray;
	}

	public function getPreviousStates($workFlow, $state) {

		return $this->previousStates;
	}

	public function getAllAlowedRecruitmentApplicationStates($workFlow) {

		return $this->applicationStates;
	}

        // Not in use
	public function getAccessibleConfigurationSubMenus() {
		return $this->topMenuItemsArray;
	}
        // Not in use
	public function getAccessibleReportSubMenus() {
		return $this->topMenuItemsArray;
	}
        // Not in use
	public function getAccessibleAttendanceSubMenus() {

		return $this->topMenuItemsArray;
	}
        // Not in use
	public function getAccessibleRecruitmentMenus() {
		return $this->topMenuItemsArray;
	}

	/** Employee List depends on the decoration order * */
	public function getEmployeeList() {

		return $this->employeeList;
	}

	public function getEmployeeNumber() {

		return $this->empNumber;
	}

	public function setEmployeeNumber($empNumber) {

		$this->empNumber = $empNumber;
	}

	public function getAllowedActions($workFlow, $state) {

		return $this->allowedActions;
	}

	public function getNextState($workFlow, $state, $action) {

		return $this->nextState;
	}

	public function getUserId() {

		return $this->userId;
	}

	public function setUserId($userId) {

		$this->userId = $userId;
	}

        // Not needed
	public function setUserTimeZoneOffset($timeZoneOffset) {
		$this->userTimeZoneOffset = $timeZoneOffset;
	}

        // Not in use
	public function getUserTimeZoneOffset() {
		return $this->userTimeZoneOffset;
	}

        // Not in use
	public function isAllowedToDefineTimeheetPeriod() {
		return $this->canDefineTimesheetPeriod;
	}

	public function getActiveProjectList() {
		return $this->projectList;
	}

	public function setActiveProjectList($activeProjectList) {
		$this->activeProjectList = $activeProjectList;
	}

	public function getActionableAttendanceStates($actions) {
		return $this->actionableStates;
	}

	public function getAllowedCandidateList() {
		return $this->candidateList;
	}
	
	public function getAllowedProjectList() {
		return $this->projectList;
	}

	public function getAllowedCandidateListToDelete() {
		return $this->candidateListToDelete;
	}

	public function getAllowedVacancyList() {
		return $this->vacancyList;
	}

	public function getAllowedCandidateHistoryList($candidateId) {
		return $this->candidateHistoryList;
	}

	public function isAdmin() {
		return $this->isAdmin;
	}

	public function isHiringManager() {
		return $this->isHiringManager;
	}

	public function isProjectAdmin() {
		return $this->isProjectAdmin;
	}

	public function isInterviewer() {
		return $this->isInterviewer;
	}

}