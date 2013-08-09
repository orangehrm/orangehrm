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
 * Entry point for PIM employee details.
 * Checks permissions for available PIM tabs and redirects user to the
 * first accessible PIM tab.
 * 
 * If none is accessible shows a page with message.
 */
class viewEmployeeAction extends basePimAction {
    
    const TEMPLATE_DENIED = "Denied";
    const TEMPLATE_NOT_FOUND = "NotFound";
    
    private $leftMenuService;
    
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
        
        $loggedInEmpNum = $this->getUser()->getEmployeeNumber();
        $this->empNumber = $request->getParameter('empNumber');
        
        $self = $loggedInEmpNum == $this->empNumber;
        
        // Check if currently logged in user has access to at least one pim left menu
        $menuService = $this->getLeftMenuService();
        $menuService->clearCachedMenu($this->empNumber);
        $menu = $menuService->getMenuItems($this->empNumber, $self);
        
        if (count($menu) > 0) {
            
            // Get first item key
            reset($menu);
            $action = key($menu);
            $properties = $menu[$action];

            $this->forward($properties['module'], $action);
        } else {
            $employee = $this->getEmployeeService()->getEmployee($this->empNumber);
            
            if ($employee instanceof Employee) {
               $this->employeeFullName = $employee->getFullName();
               
               return self::TEMPLATE_DENIED;
            } else {
                return self::TEMPLATE_NOT_FOUND;
            }
        }
    }
}

