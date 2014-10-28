<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of baseTimeAction
 *
 * @author nirmal
 */
abstract class baseTimeAction extends sfAction {

    public function getDataGroupPermissions($dataGroups, $empNumber = null) {
        $loggedInEmpNum = $this->getUser()->getEmployeeNumber();

        $entities = array();
        $self = false;
        if (isset($empNumber)) {
            $entities = array('Employee' => $empNumber);
            if ($empNumber == $loggedInEmpNum) {
                $self = true;
            }
        }

        return $this->getContext()->getUserRoleManager()->getDataGroupPermissions($dataGroups, array(), array(), $self, $entities);
    }
    
    /**
     * Get resulting state when given action is performed on the given timesheet
     * 
     * @param Timesheet $timesheet
     * @param int $action Action
     * @param bool $self true if operating on own timesheet
     * @return string
     */
    protected function getResultingState($timesheet, $action, $self) {
        
        $resultingState = $timesheet->getState();
        
        $excludeRoles = array();
        $includeRoles = array();
        $entities = array('Employee' => $timesheet->getEmployeeId());

        if ($self) {
            $includeRoles[] = 'ESS';
        }
        
        $userRoleManager = $this->getContext()->getUserRoleManager();
        $allowedActions = $userRoleManager->getAllowedActions(PluginWorkflowStateMachine::FLOW_TIME_TIMESHEET, 
                $timesheet->getState(), $excludeRoles, $includeRoles, $entities);

        if (isset($allowedActions[$action])) {
            $resultingState = $allowedActions[$action]->getResultingState();
        }         
        
        return $resultingState;
    }    

}

