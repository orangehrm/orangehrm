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
 *
 */

/**
 * Description of getFilteredEmployeeCountAjaxAction
 */
class getFilteredEmployeesAjaxAction  extends sfAction {
    
    protected $employeeService;
    
    public function getEmployeeService() { 
        if (empty($this->employeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    public function setEmployeeService($employeeService) {
        $this->employeeService = $employeeService;
    }
    
    protected function getMatchingEmployees($parameters) {

        $parameterHolder = new EmployeeSearchParameterHolder();
        $filters = array('location' => $parameters['location'],
            'sub_unit' => $parameters['subunit']);

        $parameterHolder->setFilters($filters);
        $parameterHolder->setLimit(NULL);
        $employees = $this->getEmployeeService()->searchEmployees($parameterHolder);


        $names = array();
        foreach($employees as $employee) {
            $names[] = $employee->getFullName();
        }        

        return $names;
    }
    
    public function execute($request) {
        sfConfig::set('sf_web_debug', false);
        sfConfig::set('sf_debug', false);
        
        $employees = $this->getMatchingEmployees($request->getGetParameters());


        $response = $this->getResponse();
        $response->setHttpHeader('Expires', '0');
        $response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0, max-age=0");
        $response->setHttpHeader("Cache-Control", "private", false);

        
        return $this->renderText(json_encode($employees))  ; 
               
    }
}
