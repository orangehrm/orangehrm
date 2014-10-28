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
 * View leave entitlement list
 *
 */
class viewLeaveEntitlementsAction extends sfAction {
    
    const FILTERS_ATTRIBUTE_NAME = 'entitlementlist.filters';
    
    protected $leaveEntitlementService;
    protected $employeeService;
    
    public function getLeaveEntitlementService() {
        if (empty($this->leaveEntitlementService)) {
            $this->leaveEntitlementService = new LeaveEntitlementService();
        }
        return $this->leaveEntitlementService;
    }

    public function setLeaveEntitlementService($leaveEntitlementService) {
        $this->leaveEntitlementService = $leaveEntitlementService;
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
    
    protected function getForm() {
        return new LeaveEntitlementSearchForm();
    }
    
    protected function showResultTableByDefault() {
        return false;
    }
    
    protected function getTitle() {
        return 'Leave Entitlements';
    }
    
    protected function getDataGroupPermissions() {
        $self = false;
        if (!empty($this->empNumber) && ($this->empNumber == $this->getUser()->getAttribute('auth.empNumber'))) {
            $self = true;
        }

        return $this->getContext()->getUserRoleManager()->getDataGroupPermissions(array('leave_entitlements'), array(), array(), $self);
    }
    
    protected function getDefaultFilters() {
        $defaults = $this->form->getDefaults();
        
        // Form defaults are in the user date format, convert to standard date format
        $pattern = sfContext::getInstance()->getUser()->getDateFormat();
        $localizationService = new LocalizationService();
        
        $defaults['date']['from'] = $localizationService->convertPHPFormatDateToISOFormatDate($pattern, $defaults['date']['from']);
        $defaults['date']['to'] = $localizationService->convertPHPFormatDateToISOFormatDate($pattern, $defaults['date']['to']);  

        return $defaults;
    }
    
    protected function setFormDefaults($filters) {
        
        // convert back to localized format before setting in form
        if (isset($filters['date']['from'])) {
            $filters['date']['from'] = set_datepicker_date_format($filters['date']['from']);
        }
        if (isset($filters['date']['to'])) {
            $filters['date']['to'] = set_datepicker_date_format($filters['date']['to']);  
        }
        
        $this->form->setDefaults($filters);
    }
    
    public function execute($request) {        
        
        $this->title = $this->getTitle();
        $this->form = $this->getForm();

        $this->showResultTable = $this->showResultTableByDefault();
        
        $filters = $this->getDefaultFilters();
        
        if ($request->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {
                $this->showResultTable = true;
                $filters = $this->form->getValues();                
                $this->saveFilters($filters);                       
            }
        } else if ($request->hasParameter('savedsearch')) {
            $filters = $this->getFilters();            
            $this->showResultTable = true;

            $this->setFormDefaults($filters);
        } else if ($request->hasParameter('empNumber') 
                && $request->hasParameter('fromDate')
                && $request->hasParameter('toDate')
                && $request->hasParameter('leaveTypeId')) {
            
            // Parameters in GET request
            $filters = $this->getFiltersFromGetParameters($request);  
            $this->showResultTable = true;
            $this->setFormDefaults($filters);
        } else {
            $this->saveFilters(array());
        }
        
        if ($this->showResultTable) {
            $searchParameters = $this->getSearchParameterObject($filters);
            $this->empNumber = $searchParameters->getEmpNumber();
            if (empty($this->empNumber)) {
                $this->showResultTable = false;
            } else {
                $results = $this->searchLeaveEntitlements($searchParameters);
                
                // Show leave Type column if displaying all leave types
                $showLeaveType = empty($filters['leave_type']);
                $this->setListComponent($results, 0, 0, $showLeaveType);        
            }
        }
    }
    
    protected function searchLeaveEntitlements($searchParameters) {
        return $this->getLeaveEntitlementService()->searchLeaveEntitlements($searchParameters);
    }
    
    protected function getSearchParameterObject($filters) {
        $searchParameters = new LeaveEntitlementSearchParameterHolder();
        $employeeName = $filters['employee'];
        $id = $employeeName['empId'];
        
        $userRoleManager = $this->getContext()->getUserRoleManager();
        $isAccessible = $userRoleManager->isEntityAccessible('Employee', $id);
        if (!empty($id)) {
            if ($isAccessible || ($this->getUser()->getAttribute('auth.empNumber') == $id)) {        
                $searchParameters->setEmpNumber($id);
            } else {
                $this->getUser()->setFlash('warning', 'Access Denied to Selected Employee');
                $this->redirect('leave/viewLeaveEntitlements');
            }
        }
        $searchParameters->setLeaveTypeId($filters['leave_type']);
        $searchParameters->setFromDate($filters['date']['from']);
        $searchParameters->setToDate($filters['date']['to']);
        return $searchParameters;
    }
    
    protected function setListComponent($leaveList, $count, $page, $showLeaveType = false) {
        
        $configurationFactory = $this->getListConfigurationFactory($showLeaveType);

        $permissions = $this->getDataGroupPermissions();
                
        $runtimeDefinitions = array();
        $buttons = array();

        if ($permissions->canCreate()) {
            $buttons['Add'] = array('label' => 'Add');
        }
        if (!$permissions->canDelete()) {
            $runtimeDefinitions['hasSelectableRows'] = false;
        } else {
            $buttons['Delete'] = array(
                'label' => 'Delete',
                'type' => 'submit',
                'data-toggle' => 'modal',
                'data-target' => '#deleteConfModal',
                'class' => 'delete');
        }
        
        $configurationFactory->setAllowEdit($permissions->canUpdate());

        $runtimeDefinitions['buttons'] = $buttons;
        $configurationFactory->setRuntimeDefinitions($runtimeDefinitions);
        
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setActivePlugin('orangehrmLeavePlugin');
        ohrmListComponent::setListData($leaveList);
        ohrmListComponent::setItemsPerPage(sfConfig::get('app_items_per_page'));
        ohrmListComponent::setNumberOfRecords($count);      
        ohrmListComponent::setPageNumber($page);
    }    
    
    protected function getListConfigurationFactory($showLeaveType = false) {
        LeaveEntitlementListConfigurationFactory::$displayLeaveType = $showLeaveType;
        $configurationFactory = new LeaveEntitlementListConfigurationFactory();
        
        return $configurationFactory;
    }    
    
    /**
     * Save search filters as user attribute
     * @param array $filters
     */
    protected function saveFilters(array $filters) {
        $this->getUser()->setAttribute(self::FILTERS_ATTRIBUTE_NAME, $filters, 'leave');
    }    
    
    /**
     * Get search filters from user attribute
     * @param array $filters
     * @return array
     */
    protected function getFilters() {
        return $this->getUser()->getAttribute(self::FILTERS_ATTRIBUTE_NAME, null, 'leave');
    }        
    
    protected function getFiltersFromGetParameters($request) {
        $filters = array();
        
        $empNumber = $request->getParameter('empNumber');
        $fromDate = $request->getParameter('fromDate');
        $toDate = $request->getParameter('toDate');
        $leaveTypeId = $request->getParameter('leaveTypeId');
        
        if (!empty($empNumber) && !empty($fromDate) && !empty($toDate) && !empty($leaveTypeId)) {
            
            $employee = $this->getEmployeeService()->getEmployee($empNumber);
            if ($employee instanceof Employee) {
                $employeeName = $employee->getFullName();
                $filters['employee'] = array('empId' => $empNumber, 'empName' => $employeeName);
                $filters['leave_type'] = $leaveTypeId;
                $filters['date'] = array('from' => $fromDate, 'to' => $toDate);
            }
        }
        return $filters;
    }
}

