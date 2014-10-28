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
 * Component generating PIM Left Menu
 *
 */
class pimLeftMenuComponent extends sfComponent {

    private $employeeService;
    private $leftMenuService;

    /**
     * Get EmployeeService
     * @returns EmployeeService
     */
    public function getEmployeeService() {
        if (is_null($this->employeeService)) {
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

    /**
     * Get PIMLeftMenuService
     * @returns PIMLeftMenuService
     */
    public function getLeftMenuService() {
        if (is_null($this->leftMenuService)) {
            $this->leftMenuService = new PIMLeftMenuService();
        }
        return $this->leftMenuService;
    }

    /**
     * Set PIMLeftMenuService
     * @param PIMLeftMenuService $leftMenuService
     */
    public function setLeftMenuService(PIMLeftMenuService $leftMenuService) {
        $this->leftMenuService = $leftMenuService;
    }
    
    public function execute($request) {
        $userRoleManager = $this->getContext()->getUserRoleManager();
        $self = $this->empNumber == $this->getUser()->getAttribute('auth.empNumber');
        $entities = array('Employee' => $this->empNumber);
        
        $this->photographPermissions = $userRoleManager->getDataGroupPermissions(
                array('photograph'), array(), array(), $self, $entities);
 
        $this->menuItems = $this->getLeftMenuService()->getMenuItems($this->empNumber, $self);

        if (!isset($this->currentAction)) {
            $this->currentAction = $this->getContext()->getActionName();
        }
        $this->empPicture = $this->getEmployeeService()->getEmployeePicture($this->empNumber);
    }
}

