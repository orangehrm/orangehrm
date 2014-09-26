<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of createTimesheetForSubourdinateAction
 *
 * @author nirmal
 */
class createTimesheetForSubourdinateAction extends baseTimeAction{
    public function execute($request) {
        $request->setParameter('initialActionName', 'viewEmployeeTimesheet');
        
        $this->employeeId = $request->getParameter('employeeId');
        $this->permission = $this->getDataGroupPermissions('time_employee_timesheets', $this->employeeId);

        $userRoleManager = $this->getContext()->getUserRoleManager();
        
        $rolesToExclude = array('ESS');
        $actions = $userRoleManager->getAllowedActions(WorkflowStateMachine::FLOW_TIME_TIMESHEET, PluginTimesheet::STATE_INITIAL, $rolesToExclude);

        $this->canCreateTimesheets = isset($actions[WorkflowStateMachine::TIMESHEET_ACTION_CREATE]);
                
        $this->createTimesheetForm = new CreateTimesheetForm();
        $this->currentDate = date('Y-m-d');
        
        if ($this->getContext()->getUser()->hasFlash('errorMessage')) {
            $this->messageData = array('error', __($this->getContext()->getUser()->getFlash('errorMessage')));
        }
    }
}

