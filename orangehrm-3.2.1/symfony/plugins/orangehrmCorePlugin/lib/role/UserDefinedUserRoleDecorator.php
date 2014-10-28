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
 * Description of UserDefinedUserRoleDecorator
 *
 */
class UserDefinedUserRoleDecorator extends UserRoleDecorator {

    private $user;
    private $userRoleName;

    public function __construct(User $user) {

        $this->user = $user;
        parent::setEmployeeNumber($user->getEmployeeNumber());
        parent::setUserId($user->getUserId());
        parent::setUserTimeZoneOffset($user->getUserTimeZoneOffset());
    }

    public function getUserRoleName() {
        return $this->userRoleName;
    }

    public function setUserRoleName($userRoleName) {
        $this->userRoleName = $userRoleName;
    }
    public function getAccessibleTimeMenus() {
        return $this->user->getAccessibleTimeMenus();
    }
    public function getAccessibleTimeSubMenus() {
        return $this->user->getAccessibleTimeSubMenus();
    }
    public function getAccessibleAttendanceSubMenus() {
        return $this->user->getAccessibleAttendanceSubMenus();
    }
    public function getAccessibleReportSubMenus() {
        return $this->user->getAccessibleReportSubMenus();
    }
    public function getEmployeeList() {
        return $this->user->getEmployeeList();
    }
    public function getEmployeeListForAttendanceTotalSummaryReport() {
        return $this->user->getEmployeeListForAttendanceTotalSummaryReport();
    }
    public function getActionableTimesheets() {
        return $this->user->getActionableTimesheets();
    }
    public function getEmployeeNameList() {
        return $this->user->getEmployeeNameList();
    }
    public function getActionableAttendanceStates($actions) {
        return $this->user->getActionableAttendanceStates($actions);
    }
    public function isAllowedToDefineTimeheetPeriod() {
        return $this->user->isAllowedToDefineTimeheetPeriod();
    }
    public function getActiveProjectList() {
        return $this->user->getActiveProjectList();
    }
    public function getActionableStates() {
        return $this->user->getActionableStates();
    }
    public function getAccessibleConfigurationSubMenus() {
        return $this->user->getAccessibleConfigurationSubMenus();
    }
    public function getAllowedProjectList() {
        return $this->user->getAllowedProjectList();
    }
    public function getAccessibleRecruitmentMenus() {
        return $this->user->getAccessibleRecruitmentMenus();
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


    public function isAdmin() {
        return $this->user->isAdmin();
    }
    
    /**
     * Get actions that this user can perform on a perticular workflow with the current state
     * @param int $workFlow
     * @param string $state
     * @return string[]
     */
    public function getAllowedActions($workFlow, $state) {

        $accessFlowStateMachineService = new AccessFlowStateMachineService();
        $allowedActionsForAdminUser = $accessFlowStateMachineService->getAllowedActions($workFlow, $state, $this->getUserRoleName());
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
        $tempNextState = $accessFlowStateMachineService->getNextState($workFlow, $state, $this->getUserRoleName(), $action);

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
        $applicationStates = $accessFlowStateMachineService->getAllAlowedRecruitmentApplicationStates($flow, $this->getUserRoleName());
        $existingStates = $this->user->getAllAlowedRecruitmentApplicationStates($flow);
        if (is_null($applicationStates)) {
            return $existingStates;
        } else {
            $applicationStates = array_unique(array_merge($applicationStates, $existingStates));
            return $applicationStates;
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

}

