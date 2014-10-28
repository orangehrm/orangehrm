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
class terminateEmployementAction extends basePimAction {

    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    public function execute($request) {
        $empNumber = $request->getParameter('empNumber');
        $terminatedId = $request->getParameter('terminatedId');
        $employee = $this->getEmployeeService()->getEmployee($empNumber);
        
        $allowedActions = $this->getContext()->getUserRoleManager()->getAllowedActions(WorkflowStateMachine::FLOW_EMPLOYEE, $employee->getState());
        
        $this->allowActivate = isset($allowedActions[WorkflowStateMachine::EMPLOYEE_ACTION_REACTIVE]);
        $this->allowTerminate = isset($allowedActions[WorkflowStateMachine::EMPLOYEE_ACTION_TERMINATE]);

        $paramForTerminationForm = array('empNumber' => $empNumber, 
                                                                 'employee' => $employee, 
                                                                 'allowTerminate' => $this->allowTerminate,
                                                                 'allowActivate' => $this->allowActivate);
        

        $this->form = new EmployeeTerminateForm(array(), $paramForTerminationForm, true);

        $loggedInEmpNum = $this->getUser()->getEmployeeNumber();
        
        if (!$this->isAllowedAdminOnlyActions($loggedInEmpNum, $empNumber)) {
            $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
        }
        
        if ($this->getRequest()->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->form->terminateEmployement($empNumber, $terminatedId);
                $this->getUser()->setFlash('jobdetails.success', __(TopLevelMessages::UPDATE_SUCCESS));
            }

            $this->redirect('pim/viewJobDetails?empNumber=' . $empNumber);
        }
    }

}

