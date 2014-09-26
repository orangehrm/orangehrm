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
 * delete employees list action
 */
class deleteEmployeesAction extends basePimAction {

    /**
     * Delete action. Deletes the employees with the given ids
     */
    public function execute($request) {
        
        $allowedToDeleteActive = $this->getContext()->getUserRoleManager()->isActionAllowed(PluginWorkflowStateMachine::FLOW_EMPLOYEE, 
                Employee::STATE_ACTIVE, PluginWorkflowStateMachine::EMPLOYEE_ACTION_DELETE_ACTIVE);
        $allowedToDeleteTerminated = $this->getContext()->getUserRoleManager()->isActionAllowed(PluginWorkflowStateMachine::FLOW_EMPLOYEE, 
                Employee::STATE_TERMINATED, PluginWorkflowStateMachine::EMPLOYEE_ACTION_DELETE_TERMINATED);
        

        if ($allowedToDeleteActive || $allowedToDeleteTerminated) {
            $form = new DefaultListForm() ;
            $form->bind($request->getParameter($form->getName()));
            if ($form->isValid()) {
                $ids = $request->getParameter('chkSelectRow');

                $userRoleManager = $this->getContext()->getUserRoleManager();
                if (!$userRoleManager->areEntitiesAccessible('Employee', $ids)) {
                    $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
                }

                $this->_checkLastAdminDeletion($ids);

                $employeeService = $this->getEmployeeService();               
                $count = $employeeService->deleteEmployees($ids);

                if ($count == count($ids)) {
                    $this->dispatcher->notify(new sfEvent($this, EmployeeEvents::EMPLOYEES_DELETED,
                                array('emp_numbers'=> $ids)));
                    $this->getUser()->setFlash('success', __(TopLevelMessages::DELETE_SUCCESS));
                } else {
                    $this->getUser()->setFlash('failure', __('A Problem Occured When Deleting The Selected Employees'));
                }
            }
                $this->redirect('pim/viewEmployeeList');
        } else {
            $this->getUser()->setFlash('warning', __('Contact Admin for delete Credentials'));
            $this->redirect('pim/viewEmployeeList');
        }
    }
    
    /**
     * Restricts deleting employees when there is only one admin
     * and the admin is assigned to an employee to be deleted
     */
    protected function _checkLastAdminDeletion($empNumbers) {
        
        $searchClues['userType']    = SystemUser::ADMIN_USER_ROLE_ID;
        $searchClues['status']      = SystemUser::ENABLED;
        
        $systemUserService  = new SystemUserService();        
        $adminUsers         = $systemUserService->searchSystemUsers($searchClues);
        $adminEmpNumbers    = array();
        $defaultAdminExists = false;
        
        foreach ($adminUsers as $adminUser) {
            
            $adminEmpNumber = $adminUser->getEmployee()->getEmpNumber();
            
            if (!empty($adminEmpNumber)) {
                $adminEmpNumbers[] = $adminEmpNumber;
            } else {
                $defaultAdminExists = true;
            }
            
        }
        
        if ($defaultAdminExists) {
            return;
        }        
        
        $adminUserDiff = array_diff($adminEmpNumbers, $empNumbers);
        
        if (empty($adminUserDiff)) {
            
            $this->getUser()->setFlash('templateMessage', array('failure', __('Failed to Delete: At Least One Admin Should Exist')));
            $this->redirect('pim/viewEmployeeList');            
            
        }
        
    }

}
