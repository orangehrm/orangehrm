<?php

/*
  // OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
  // all the essential functionalities required for any enterprise.
  // Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com

  // OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
  // the GNU General Public License as published by the Free Software Foundation; either
  // version 2 of the License, or (at your option) any later version.

  // OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
  // without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
  // See the GNU General Public License for more details.

  // You should have received a copy of the GNU General Public License along with this program;
  // if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
  // Boston, MA  02110-1301, USA
 */

/**
 * Form class for employee membership detail
 */
class EmployeeReportToForm extends BaseForm {

    public $fullName;
    public $empNumber;
    private $employeeService;
    private $reportingMethodConfigurationService;
    private $employeeList;
    private $reportingMethodType;

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
    
    public function getReportingMethodConfigurationService() {

        if (is_null($this->reportingMethodConfigurationService)) {
            $this->reportingMethodConfigurationService = new ReportingMethodConfigurationService();
        }
        
        return $this->reportingMethodConfigurationService;
        
    }    
    
    public function setReportingMethodConfigurationService($reportingMethodConfigurationService) {
        $this->reportingMethodConfigurationService = $reportingMethodConfigurationService;
    }

    public function configure() {
        $this->reportToPermissions = $this->getOption('reportToPermissions');

        $this->setEmployeeList();
        $this->reportingMethodType = $this->getReportingMethodType();

        $this->empNumber = $this->getOption('empNumber');
        $employee = $this->getEmployeeService()->getEmployee($this->empNumber);
        $this->fullName = $employee->getFullName();
        
        $widgets = array('empNumber' => new sfWidgetFormInputHidden(array(), array('value' => $this->empNumber)));
        $validators = array('empNumber' => new sfValidatorString(array('required' => true)));
        
        if ($this->reportToPermissions->canRead()) {

            $reportToWidgets = $this->getRepoertToWidgets();
            $reportToValidators = $this->getRepoertToValidators();

            if (!($this->reportToPermissions->canUpdate() || $this->reportToPermissions->canCreate()) ) {
                foreach ($dependentWidgets as $widgetName => $widget) {
                    $widget->setAttribute('disabled', 'disabled');
                }
            }
            $widgets = array_merge($widgets, $reportToWidgets);
            $validators = array_merge($validators, $reportToValidators);
        }

        $this->setWidgets($widgets);
        $this->setValidators($validators);
        
        $this->widgetSchema->setNameFormat('reportto[%s]');
    }
    
    /*
     * Tis fuction will return the widgets of the form
     */
    public function getRepoertToWidgets(){
        $widgets = array();
        
        //creating widgets
        $widgets['type_flag'] = new sfWidgetFormChoice(array('expanded' => true, 'choices' => array(
                    ReportTo::SUPERVISOR => __('Supervisor'), ReportTo::SUBORDINATE => __('Subordinate')), 'default' => ReportTo::SUPERVISOR));        
        $widgets['supervisorName'] = new ohrmWidgetEmployeeNameAutoFill(array('employeeList' => $this->getEmployeeListForSupervisor()));
        $widgets['subordinateName'] = new ohrmWidgetEmployeeNameAutoFill(array('employeeList' => $this->getEmployeeListForSubordinate()));                    
        $widgets['previousRecord'] = new sfWidgetFormInputHidden();
        $widgets['reportingMethodType'] = new sfWidgetFormSelect(array('choices' => $this->reportingMethodType));
        $widgets['reportingMethod'] = new sfWidgetFormInputText();
        
        return $widgets;
    }
    
    
    /*
     * Tis fuction will return the form validators
     */
    public function getRepoertToValidators(){
        
        $validators = array(
            'type_flag' => new sfValidatorChoice(array('required' => true,
                'choices' => array(ReportTo::SUPERVISOR, ReportTo::SUBORDINATE))),
            'supervisorName' => new ohrmValidatorEmployeeNameAutoFill(),
            'subordinateName' => new ohrmValidatorEmployeeNameAutoFill(),
            'name_id' => new sfValidatorString(array('required' => false)),
            'previousRecord' => new sfValidatorString(array('required' => false)),
            'reportingMethodType' => new sfValidatorString(array('required' => true), array('required' => 'Select reporting method')),
            'reportingMethod' => new sfValidatorString(array('required' => false, 'max_length' => 80)),
        );
        
        return $validators;
    }

    private function setEmployeeList() {
        
        $employeeService = $this->getEmployeeService();
       
        $properties = array("empNumber","firstName", "middleName", "lastName", "termination_id");
        $this->employeeList = $employeeService->getEmployeePropertyList($properties, 'lastName', 'ASC', true);
    }
    /**
     * Returns Reporting method Type
     * @return array
     */
    private function getReportingMethodType() {

        $list = array("" => "-- " . __('Select') . " --");

        $reportingMethodTypes = $this->getReportingMethodConfigurationService()->getReportingMethodList();

        foreach ($reportingMethodTypes as $reportingMethodType) {
            $list[$reportingMethodType->id] = $reportingMethodType->name;
        }
        $list[-1] = __('Other');
        return $list;
    }

    protected function getEmployeeListForSupervisor() {

        $employeeService = $this->getEmployeeService();
        
        $filteredEmployeeList = array();

        /* Populating already assigned sup & sub */
        $assignedReportTo = array();
        $supervisors = $employeeService->getImmediateSupervisors($this->empNumber);
        $subordinateIdList = $employeeService->getSubordinateIdListBySupervisorId($this->empNumber, true);

        foreach ($subordinateIdList as $id) {
            $assignedReportTo[$id] = true;
        }
        
        foreach ($supervisors as $supervisor) {
            $assignedReportTo[$supervisor->getSupervisorId()] = true;
        }
        
        /* Populating final list */
        foreach ($this->employeeList as $employee) {

            if (!isset($assignedReportTo[$employee['empNumber']]) && 
                $employee['empNumber'] != $this->empNumber) {
                $filteredEmployeeList[] = $employee;
            }
        }
        
        return $filteredEmployeeList;
        
    }
    
    protected function getEmployeeListForSubordinate() {

        $employeeService = $this->getEmployeeService();
        
        $filteredEmployeeList = array();

        /* Populating already assigned sup & sub */
        $assignedReportTo = array();
        $supervisorIdList = $employeeService->getSupervisorIdListBySubordinateId($this->empNumber, true);
        $subordinates = $employeeService->getSubordinateListForEmployee($this->empNumber);

        foreach ($subordinates as $subordinate) {
            $assignedReportTo[$subordinate->getSubordinateId()] = true;
        }
        
        foreach ($supervisorIdList as $id) {
            $assignedReportTo[$id] = true;
        }
        
        /* Populating final list */
        foreach ($this->employeeList as $employee) {

            if (!isset($assignedReportTo[$employee['empNumber']]) && 
                $employee['empNumber'] != $this->empNumber) {
                $filteredEmployeeList[] = $employee;
            }
        }
        
        return $filteredEmployeeList;
        
    }
    
    /**
     * Save supervisors and subordinates
     */
    public function save() {

        $message = 'failed';
        $updated = FALSE;
        $empNumber = $this->getValue('empNumber');
        $supOrSub = $this->getValue('type_flag');
        $supervisorName = $this->getValue('supervisorName');
        $subordinateName = $this->getValue('subordinateName');
        if ($supervisorName['empId'] != '') {
            $name = $supervisorName['empName'];
            $selectedEmployee = $supervisorName['empId'];
        } else if ($subordinateName['empId'] != '') {
            $name = $subordinateName['empName'];
            $selectedEmployee = $subordinateName['empId'];
        }
        
        $reportingType = $this->getValue('reportingMethodType');
        $reportingMethod = $this->getValue('reportingMethod');
        
        $previousRecord = $this->getValue('previousRecord');

        if ($reportingMethod != null) {

            $newReportingMethod = new ReportingMethod();
            $newReportingMethod->name = $reportingMethod;
            $savedReportingMethod = $this->getReportingMethodConfigurationService()->saveReportingMethod($newReportingMethod);
            $reportingType = $savedReportingMethod->id;
        }

        if ($supOrSub == ReportTo::SUPERVISOR) {
            $existingReportToObject = $this->getEmployeeService()->getReportToObject($selectedEmployee, $empNumber);

            if ($existingReportToObject != null) {
                if ($this->getOption('reportToSupervisorPermission')->canUpdate()) {
                    $existingReportToObject->setReportingMethodId($reportingType);
                    $existingReportToObject->save();
                    $updated = TRUE;
                    $message = 'updated';
                }
            } else {
                if ($this->getOption('reportToSupervisorPermission')->canCreate()) {
                    $newReportToObject = new ReportTo();
                    $newReportToObject->setSupervisorId($selectedEmployee);
                    $newReportToObject->setSubordinateId($empNumber);
                    $newReportToObject->setReportingMethodId($reportingType);
                    $newReportToObject->save();
                    $updated = TRUE;
                    $message = 'saved';
                }
            }
        }

        if ($supOrSub == ReportTo::SUBORDINATE) {
            $existingReportToObject = $this->getEmployeeService()->getReportToObject($empNumber, $selectedEmployee);

            if ($existingReportToObject != null) {
                if ($this->getOption('reportToSubordinatePermission')->canUpdate()) {
                    $existingReportToObject->setReportingMethodId($reportingType);
                    $existingReportToObject->save();
                    $updated = TRUE;
                    $message = 'updated';
                }
            } else {
                if ($this->getOption('reportToSubordinatePermission')->canCreate()) {
                    $newReportToObject = new ReportTo();
                    $newReportToObject->setSupervisorId($empNumber);
                    $newReportToObject->setSubordinateId($selectedEmployee);
                    $newReportToObject->setReportingMethodId($reportingType);
                    $newReportToObject->save();
                    $updated = TRUE;
                    $message = 'saved';
                }
            }
        }
        $returnValue = array($supOrSub, $updated, $message);
        return $returnValue;
    }

}

