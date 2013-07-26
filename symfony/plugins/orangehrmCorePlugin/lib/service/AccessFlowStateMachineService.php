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

class AccessFlowStateMachineService {

    private $accessFlowStateMachineDao;
    
    private static $allowedWorkflowItemCache = array();

    public function getAccessFlowStateMachineDao() {


        if (is_null($this->accessFlowStateMachineDao)) {
            $this->accessFlowStateMachineDao = new AccessFlowStateMachineDao();
        }

        return $this->accessFlowStateMachineDao;
    }

    public function setAccessFlowStateMachineDao(AccessFlowStateMachineDao $acessFlowStateDao) {

        $this->accessFlowStateMachineDao = $acessFlowStateDao;
    }

    public function getAccessibleFlowStateMachineDao() {

        if (is_null($this->accessFlowStateMachineDao)) {
            $this->accessFlowStateMachineDao = new AccessFlowStateMachineDao();
        }

        return $this->accessFlowStateMachineDao;
    }

    public function getAllowedActions($workflow, $state, $role) {

        $results = $this->getAccessibleFlowStateMachineDao()->getAllowedActions($workflow, $state, $role);

        if (is_null($results)) {

            return null;
        } else {

            foreach ($results as $allowedAction) {

                $allowedActionArray[] = $allowedAction->getAction();
            }
            
            return $allowedActionArray;
        }
    }
    
    public function getAllowedWorkflowItems($workflow, $state, $role) {
        $key = $workflow . '-' . $state . '-' . $role;
        if (!isset(self::$allowedWorkflowItemCache[$key])) {
            self::$allowedWorkflowItemCache[$key] = $this->getAccessibleFlowStateMachineDao()->getAllowedWorkflowItems($workflow, $state, $role);
        }
        return self::$allowedWorkflowItemCache[$key];
    }
    
    /**
     * check State Transition is possible with UserRole
     * 
     * @param type $workflow
     * @param type $state
     * @param type $role
     * @param type $action
     * @return boolean 
     */
    public function isActionAllowed($workflow, $state, $role, $action) {
        return $this->getAccessibleFlowStateMachineDao()->isActionAllowed($workflow, $state, $role, $action);
    }

    public function getNextState($flow, $state, $role, $action) {

        $result = $this->getAccessibleFlowStateMachineDao()->getNextState($flow, $state, $role, $action);
        if (is_null($result)) {

            return null;
        } else {

            return $result->getResultingState();
        }
    }

    public function getAllAlowedRecruitmentApplicationStates($flow, $role) {

        $result = $this->getAccessibleFlowStateMachineDao()->getAllAlowedRecruitmentApplicationStates($flow, $role);
        if (is_null($result)) {

            return null;
        } else {
            $resultingStateList = array();
            $stateList = array();
            foreach ($result as $rslt) {
                $stateList[] = $rslt->getState();
                $resultingStateList[] = $rslt->getResultingState();
            }
            return array_merge($stateList, $resultingStateList);
        }
    }

    public function getActionableStates($flow, $role, $actions) {

        $records = $this->getAccessFlowStateMachineDao()->getActionableStates($flow, $role, $actions);

        if($records==null){
            
            return null;
        }
        
        foreach ($records as $record) {

            $tempArray[] = $record->getState();
        }
        
        return $tempArray;
    }
    
    /**
     * 
     * @param type $workFlowId
     * @return type Doctrine collections
     */
    public function getWorkFlowStateMachineRecords($workFlowId, $role){
        return $this->accessFlowStateMachineDao->getWorkFlowStateMachineRecords($workFlowId, $role);
    }

    public function saveWorkflowStateMachineRecord(WorkflowStateMachine $workflowStateMachineRecord) {

        return $this->getAccessFlowStateMachineDao()->saveWorkflowStateMachineRecord($workflowStateMachineRecord);
    }
    
    /**
     * set workflow records form array
     * @param type $workflowStateMachineRecordArray 
     */
    public function saveWorkflowStateMachineRecordAsArray($workflowStateMachineRecordArray) {
        if (count($workflowStateMachineRecordArray) > 0) {
            foreach ($workflowStateMachineRecordArray as $workflowStateMachineRecord) {
                $this->saveWorkflowStateMachineRecord($workflowStateMachineRecord);
            }
        }
    }
	/*
    public function deleteWorkflowStateMachineRecord($flow, $state, $role, $action, $resultingState) {
		$this->getAccessFlowStateMachineDao()->deleteWorkflowStateMachinerecord($flow, $state, $role, $action, $resultingState);
	}
	*/

    public function deleteWorkflowStateMachineRecord($flow, $state, $role, $action, $resultingState){
       return  $this->getAccessFlowStateMachineDao()->deleteWorkflowStateMachinerecord($flow, $state, $role, $action, $resultingState);
    }

    public function getAllowedCandidateList($role, $empNumber) {
        $candidateService = new CandidateService();
        return $candidateService->getCandidateListForUserRole($role, $empNumber);
    }
    
    public function getAllowedProjectList($role, $empNumber) {
        $projetService = new ProjectService();
        return $projetService->getProjectListForUserRole($role, $empNumber);
    }

    public function getAllowedVacancyList($role, $empNumber) {
        $vacancyService = new VacancyService();
        return $vacancyService->getVacancyListForUserRole($role, $empNumber);
    }

    public function getAllowedCandidateHistoryList($role, $empNumber, $candidateId) {
        $candidateService = new CandidateService();
        return $candidateService->getCanidateHistoryForUserRole($role, $empNumber, $candidateId);
    }
    
    public function getWorkflowItem($id) {
        return $this->getAccessFlowStateMachineDao()->getWorkflowItem($id);
    }    
    
    public function getWorkflowItemByStateActionAndRole($workFlow, $state, $action, $role) {
         return $this->getAccessFlowStateMachineDao()->getWorkflowItemByStateActionAndRole($workFlow, $state, $action, $role);
    }
    
    public function deleteWorkflowRecordsForUserRole($flow, $role) {
        return $this->getAccessFlowStateMachineDao()->deleteWorkflowRecordsForUserRole($flow, $role);
    }    
    
    public function handleUserRoleRename($oldName, $newName) {
        return $this->getAccessFlowStateMachineDao()->handleUserRoleRename($oldName, $newName);
    }    

}
