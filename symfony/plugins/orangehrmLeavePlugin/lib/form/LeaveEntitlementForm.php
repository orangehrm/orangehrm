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
class LeaveEntitlementForm extends BaseForm {

    protected $leaveTypeService;
    
    protected $leavePeriodService;
    
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

    
    
    public function configure() {

        $this->setWidget('employee', new ohrmWidgetEmployeeNameAutoFill(array('loadingMethod'=>'ajax')));

        $this->setValidator('employee', new ohrmValidatorEmployeeNameAutoFill());

        $this->setLeaveTypeWidget();
        
        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
        
        $this->setWidget('date', new ohrmWidgetFormLeavePeriod(array()));
        

        $this->setValidator('date', new sfValidatorDateRange(array(
            'from_date' => new ohrmDateValidator(array('required' => true)),
            'to_date' => new ohrmDateValidator(array('required' => true))
        )));
        
        //$this->setWidget('date_from', new ohrmWidgetDatePicker(array(), array('id' => 'date_from')));
        //$this->setValidator('date_from', new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => true)));

        //$this->setWidget('date_to', new ohrmWidgetDatePicker(array(), array('id' => 'date_to')));
        //$this->setValidator('date_to', new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => true)));
        
        $this->setDefaultDates();    
        
                
        $formExtension = PluginFormMergeManager::instance();
        $formExtension->mergeForms($this, 'viewLeaveEntitlements','LeaveEntitlementsForm');
  
        $this->widgetSchema->setNameFormat('entitlements[%s]');
        $this->getWidgetSchema()->setLabels($this->getFormLabels());

    }

    protected function setLeaveTypeWidget() {

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

    /**
     *
     * @return array
     */
    protected function getFormLabels() {

        $labels = array(
            'employee' => __('Employee'),
            'leave_type' => __('Leave Type')
            
            
        );
        if( LeavePeriodService::getLeavePeriodStatus() == LeavePeriodService::LEAVE_PERIOD_STATUS_FORCED){
             $labels['date'] = __('Leave Period');
        }else{
            $labels['date'] = __('Earned Between');
        }
        return $labels;
    }
    
    protected function setDefaultDates() {
        $now = time();
        
        // If leave period defined, use leave period start and end date
        $leavePeriod = $this->getLeavePeriodService()->getCurrentLeavePeriodByDate(date('Y-m-d', $now));        
        if (!empty($leavePeriod)) {
            $fromDate   = $leavePeriod[0];
            $toDate     = $leavePeriod[1];
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

