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
class getFilteredEmployeesEntitlementAjaxAction  extends sfAction {
    
    protected $employeeService;
    protected $entitlementService ;
    
    public function getEmployeeService() { 
        if (empty($this->employeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    public function setEmployeeService($employeeService) {
        $this->employeeService = $employeeService;
    }
    
    public function getEntitlementService() { 
        if (empty($this->entitlementService)) {
            $this->entitlementService = new LeaveEntitlementService();
        }
        return $this->entitlementService;
    }

    public function setEntitlementService($entitlementService) {
        $this->entitlementService = $entitlementService;
    }
    
    protected function getMatchingEmployees($parameters) {

        $parameterHolder = new EmployeeSearchParameterHolder();
        $filters = array('location' => $parameters['location'],
            'sub_unit' => $parameters['subunit']);

        $fromDate = isset($parameters['fd']) ? $parameters['fd'] : null;
        $toDate =  isset($parameters['td']) ? $parameters['td'] : null;
        $leaveType =  isset($parameters['lt']) ? $parameters['lt'] : null;
        $newValue =  isset($parameters['ent']) ? $parameters['ent'] : null;
        $offset = isset($parameters['offset']) ? $parameters['offset'] : 0;
        $pageSize = sfConfig::get('app_items_per_page');

        $parameterHolder->setFilters($filters);
        $parameterHolder->setOffset($offset);
        $parameterHolder->setLimit($pageSize);
        $parameterHolder->setReturnType(EmployeeSearchParameterHolder::RETURN_TYPE_ARRAY);
        
        $employees = $this->getEmployeeService()->searchEmployees($parameterHolder);
            
        $names = array();
       
        foreach($employees as $employee) {
                                    
            $leaveEntitlementSearchParameterHolder = new LeaveEntitlementSearchParameterHolder();
            $leaveEntitlementSearchParameterHolder->setEmpNumber($employee['empNumber']);
            $leaveEntitlementSearchParameterHolder->setFromDate($fromDate);
            $leaveEntitlementSearchParameterHolder->setLeaveTypeId($leaveType);
            $leaveEntitlementSearchParameterHolder->setToDate($toDate);
            
            $entitlementList = $this->getEntitlementService()->searchLeaveEntitlements( $leaveEntitlementSearchParameterHolder );
            $oldValue = 0;

            if(count($entitlementList) > 0){
                $existingLeaveEntitlement = $entitlementList->getFirst();
                $oldValue = $existingLeaveEntitlement->getNoOfDays();
                
            } 

            $names[] = array($employee['firstName'] . ' ' . $employee['middleName'] . ' ' . $employee['lastName'],$oldValue,$newValue+$oldValue);
        }        

        $data = array(
            'offset' => $offset,
            'pageSize' => $pageSize,
            'data' => $names
        );
        
        return $data;
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
