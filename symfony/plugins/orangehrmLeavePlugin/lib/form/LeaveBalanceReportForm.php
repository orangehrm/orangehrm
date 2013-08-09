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
 * Form class for leave entitlements screen
 *
 */
class LeaveBalanceReportForm extends BaseForm {
    
    const REPORT_TYPE_LEAVE_TYPE = 1;    
    const REPORT_TYPE_EMPLOYEE = 2;
    
    protected $leaveTypeService;    
    
    protected $leavePeriodService;
    protected $locationService;    
    protected $jobTitleService;
    
    protected $locationChoices = null;
    protected $jobTitleChoices = null;    
    
    public function getLeaveTypeService() {
        if (!isset($this->leaveTypeService)) {
            $this->leaveTypeService = new LeaveTypeService();
        }
        return $this->leaveTypeService;
    }

    public function setLeaveTypeService(LeaveTypeService $leaveTypeService) {
        $this->leaveTypeService = $leaveTypeService;
    }    
    
    public function getLeavePeriodService() {
        if (!isset($this->leavePeriodService)) {
            $this->leavePeriodService = new LeavePeriodService();
        }        
        return $this->leavePeriodService;
    }

    public function setLeavePeriodService($leavePeriodService) {
        $this->leavePeriodService = $leavePeriodService;
    }
    
    public function getLocationService() {
        if (!($this->locationService instanceof LocationService)) {
            $this->locationService = new LocationService();
        }
        return $this->locationService;
    }    

    public function setLocationService($locationService) {
        $this->locationService = $locationService;
    }
    
    public function getJobTitleService() {
        if (!($this->jobTitleService instanceof JobTitleService)) {
            $this->jobTitleService = new JobTitleService();
        }
        return $this->jobTitleService;
    }
    
    public function setJobTitleService($jobTitleService) {
        $this->jobTitleService = $jobTitleService;
    }    
    
    public function configure() {

        $reportTypes = array(0 => '-- ' . __('Select') . ' --', 
                            self::REPORT_TYPE_LEAVE_TYPE => 'Leave Type', 
                            self::REPORT_TYPE_EMPLOYEE => 'Employee');
        
        // Valid report types, skip 0 option
        $validReportTypes = array_slice(array_keys($reportTypes), 1);
        
        $this->setWidget('report_type', new sfWidgetFormChoice(array('choices' => $reportTypes)));
        $this->setValidator('report_type', new sfValidatorChoice(array('choices' => $validReportTypes),
                array('invalid' => CommonMessages::REQUIRED_FIELD,
                      'required' => true)));        
           
        $this->setWidget('employee', new ohrmWidgetEmployeeNameAutoFill(array('loadingMethod'=>'ajax')));
        $this->setValidator('employee', new ohrmValidatorEmployeeNameAutoFill());
        
        $this->_setLeaveTypeWidget();        
                
        $this->setWidget('date', new ohrmWidgetFormLeavePeriod(array()));
         
        $this->setWidget('job_title',  new sfWidgetFormChoice(array('choices' => $this->getJobTitleChoices())));
        
        $locationOptions = array('set_all_option_value' => true, 'all_option_value' => NULL, 
            'show_all_locations' => false);

        $this->setWidget('location', new ohrmReportWidgetOperationalCountryLocationDropDown($locationOptions));
        $this->setWidget('sub_unit', new ohrmWidgetSubDivisionList());
        $this->setWidget('include_terminated', new sfWidgetFormInputCheckbox(array('value_attribute_value' => 'on')));           
        
        $this->setValidator('location', new sfValidatorString(array('required' => false)));
        $this->setValidator('job_title', new sfValidatorChoice(array('choices' => array_keys($this->getJobTitleChoices()))));
        $this->setValidator('sub_unit', new sfValidatorString(array('required' => false)));
        $this->setValidator('include_terminated', new sfValidatorString(array('required' => false)));
            
        $this->setValidator('date', new sfValidatorDateRange(array(
            'from_date' => new ohrmDateValidator(array('required' => true)),
            'to_date' => new ohrmDateValidator(array('required' => true))
        )));
        
        $this->setDefaultDates();
        
        $formExtension = PluginFormMergeManager::instance();
        $formExtension->mergeForms($this, 'viewLeaveBalanceReport', 'LeaveBalanceReportForm');
  
        $this->widgetSchema->setNameFormat('leave_balance[%s]');
        $this->getWidgetSchema()->setLabels($this->getFormLabels());

    }
    
    private function getJobTitleChoices() {

        if (is_null($this->jobTitleChoices)) {
            $jobTitleList = $this->getJobTitleService()->getJobTitleList();

            $this->jobTitleChoices = array('0' => __('All'));

            foreach ($jobTitleList as $jobTitle) {
                $this->jobTitleChoices[$jobTitle->getId()] = $jobTitle->getJobTitleName();
            }
        }

        return $this->jobTitleChoices;
    }    
    
    protected function getLocationChoices() {

        if (is_null($this->locationChoices)) {
            $locationList = $this->getLocationService()->getLocationList();

            $this->locationChoices = array('0' => __('All'));

            foreach ($locationList as $location) {
                $this->locationChoices[$location->getId()] = $location->getName();
            }
        }

        return $this->locationChoices;
    }    
    
    private function _setLeaveTypeWidget() {

        $choices = array();
        
        $leaveTypeList = $this->getLeaveTypeService()->getLeaveTypeList();
        $defaultLeaveTypeId = NULL;
        
        if (count($leaveTypeList) == 0) {
            $choices[''] = __('No leave types defined');
        } else {
            foreach ($leaveTypeList as $leaveType) {
                if (is_null($defaultLeaveTypeId)) {
                    $defaultLeaveTypeId = $leaveType->getId();
                }
                $choices[$leaveType->getId()] = $leaveType->getName();            
            }
        }

        $this->setWidget('leave_type', new sfWidgetFormChoice(array('choices' => $choices)));
        $this->setValidator('leave_type', new sfValidatorChoice(array('choices' => array_keys($choices))));
        
        if (!is_null($defaultLeaveTypeId)) {
            $this->setDefault('leave_type', $defaultLeaveTypeId);
        }        
        
    }
    
    protected function getFormLabels() {

        $labels = array(
            'report_type' => __('Generate For')  . '<em> *</em>',
            'employee' => __('Employee') . '<em> *</em>',
            'leave_type' => 'Leave Type',
            'date' => 'From',
            'location' => __('Location'),
            'job_title' => __('Job Title'),
            'sub_unit' => __('Sub Unit'),
            'include_terminated' => __('Include Past Employees')
        );
        
        return $labels;
    }
    
    protected function setDefaultDates() {
        $now = time();
        
        // If leave period defined, use leave period start and end date
        $leavePeriod = $this->getLeavePeriodService()->getCurrentLeavePeriodByDate(date('Y-m-d', $now));        
        if (!empty($leavePeriod)) {
            $fromDate = $leavePeriod[0];
            $toDate = $leavePeriod[1];
        } else {
            // else use this year as the period
            $year = date('Y', $now);
            $fromDate = $year . '-1-1';
            $toDate = $year . '-12-31';
        }        
        
        $this->setDefault('date', array(
            'from' => set_datepicker_date_format($fromDate),
            'to' => set_datepicker_date_format($toDate)));

    }    
    
}

