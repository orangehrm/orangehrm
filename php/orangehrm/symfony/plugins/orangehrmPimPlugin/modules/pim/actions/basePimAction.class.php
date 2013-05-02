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
abstract class basePimAction extends sfAction {
    
    private $employeeService;
    
    public function preExecute() {
        $sessionVariableManager = new DatabaseSessionManager();
        $sessionVariableManager->setSessionVariables(array(
            'orangehrm_user' => Auth::instance()->getLoggedInUserId(),
        ));
        $sessionVariableManager->registerVariables();
        $this->setOperationName(OrangeActionHelper::getActionDescriptor($this->getModuleName(), $this->getActionName()));  
        
            
        /* For highlighting corresponding menu item */
        $request = $this->getRequest();        
        $initialActionName = $request->getParameter('initialActionName', '');

        if (empty($initialActionName)) {
            $loggedInEmpNum = $this->getUser()->getEmployeeNumber();
            $empNumber = $request->getParameter('empNumber');
            
            if (!empty($loggedInEmpNum) && $loggedInEmpNum == $empNumber) {
                $request->setParameter('initialActionName', 'viewMyDetails');
            } else {
                $request->setParameter('initialActionName', 'viewEmployeeList');
            }
        }        
        
    }
    
    public function getDataGroupPermissions($dataGroups, $empNumber) { 
        $loggedInEmpNum = $this->getUser()->getEmployeeNumber();
        
        $entities = array('Employee' => $empNumber);        
        $self = $empNumber == $loggedInEmpNum;
        
         return $this->getContext()->getUserRoleManager()->getDataGroupPermissions($dataGroups, array(), array(), $self, $entities);
    }

    /**
     * Get EmployeeService
     * @returns EmployeeService
     */
    public function getEmployeeService() {
        if(is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }

    /**
     * Set EmployeeService
     * @param EmployeeService $employeeService
     */
    public function setEmployeeService(EmployeeService $employeeService) {
        $this->employeeService = $employeeService;
    }
    
    protected function isSupervisor($loggedInEmpNum, $empNumber) {

        if(isset($_SESSION['isSupervisor']) && $_SESSION['isSupervisor']) {

            $empService = $this->getEmployeeService();
            $subordinates = $empService->getSubordinateList($loggedInEmpNum, true);

            foreach($subordinates as $employee) {
                if($employee->getEmpNumber() == $empNumber) {
                    return true;
                }
            }
        }
        return false;
    }
    
    protected function IsActionAccessible($empNumber) {
        
        $isValidUser = true;
        
        $loggedInEmpNum = $this->getUser()->getEmployeeNumber();     
        
        $userRoleManager = $this->getContext()->getUserRoleManager();            
        $accessible = $userRoleManager->isEntityAccessible('Employee', $empNumber);
            
        if ($empNumber != $loggedInEmpNum && (!$accessible)) {
            $isValidUser = false;
        }      
        
        return $isValidUser;
    }

    protected function isAllowedAdminOnlyActions($loggedInEmpNumber, $empNumber) {

        if ($loggedInEmpNumber == $empNumber) {
            return false;
        }

        $userRoleManager = $this->getContext()->getUserRoleManager();   
        $excludeRoles = array('Supervisor');
        
        $accessible = $userRoleManager->isEntityAccessible('Employee', $empNumber, null, $excludeRoles);
        
        if ($accessible) {
            return true;
        }

        return false;

    }

    protected function setOperationName($actionName) {
        $sessionVariableManager = new DatabaseSessionManager();
        $sessionVariableManager->setSessionVariables(array(
            'orangehrm_action_name' => $actionName,
        ));
        $sessionVariableManager->registerVariables();
    }

}