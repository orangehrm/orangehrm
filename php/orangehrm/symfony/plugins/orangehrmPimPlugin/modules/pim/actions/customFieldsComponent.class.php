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

class customFieldsComponent extends sfComponent {

    private $employeeService;
    private $customFieldsService;

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
    
    /**
     * Get EmployeeService
     * @returns EmployeeService
     */
    public function getCustomFieldsService() {
        if(is_null($this->customFieldsService)) {
            $this->customFieldsService = new CustomFieldsService();
        }
        return $this->customFieldsService;
    }

    /**
     * Set EmployeeService
     * @param EmployeeService $employeeService
     */
    public function setCustomFieldsService(CustomFieldsService $customFieldsService) {
        $this->customFieldsService = $customFieldsService;
    }
    
    /**
     * Execute method of component
     * 
     * @param type $request 
     */
    public function execute($request) {       

        $this->customFieldsMessageType = '';
        $this->customFieldsMessage = '';
        
        if ($this->getUser()->hasFlash('customFieldsMessage')) {  
            list($this->customFieldsMessageType, $this->customFieldsMessage) = $this->getUser()->getFlash('customFieldsMessage');
        }
        
        $this->employee = $this->getEmployeeService()->getEmployee($this->empNumber);
        $this->customFieldList = $this->getCustomFieldsService()->getCustomFieldList($this->screen);          
        $this->form = new EmployeeCustomFieldsForm(array(),  array('customFields'=>$this->customFieldList), true);  
    }

}

