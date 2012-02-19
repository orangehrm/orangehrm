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
 * Form object for holiday list search
 *
 */
class HolidayListSearchForm extends sfForm {
    
    private $leavePeriodService;
    
    /**
     * Returns Leave Period
     * @return LeavePeriodService
     */
    public function getLeavePeriodService() {

        if (is_null($this->leavePeriodService)) {
            $leavePeriodService = new LeavePeriodService();
            $leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());
            $this->leavePeriodService = $leavePeriodService;
        }

        return $this->leavePeriodService;
    }
    
    /**
     * Returns Leave Period
     * @return LeavePeriodService
     */
    public function setLeavePeriodService($leavePeriodService) {
        $this->leavePeriodService = $leavePeriodService;
    } 
    
    /**
     * Configuring WorkWeek form widget
     */
    public function configure() {

        sfContext::getInstance()->getConfiguration()->loadHelpers(array('I18N', 'OrangeDate'));
        
        $leavePeriodChoices = $this->getLeavePeriodChoices();                           
                            
        $this->setWidget('leave_period', new sfWidgetFormSelect(
                array('choices' => $leavePeriodChoices),
                array('class' => 'formSelect')));   
        
        // Clear the 0 option since it is not a valid choice.
        unset($leavePeriodChoices[0]);
        
        $this->setValidator('leave_period', 
                new sfValidatorChoice(array('choices' => array_keys($leavePeriodChoices)), 
                                      array('invalid' => __(ValidationMessages::INVALID))));        

        $this->widgetSchema->setLabels(array('leave_period' => __("Leave Period")));
        
        $this->widgetSchema->setNameFormat('holidayList[%s]');        
        $this->widgetSchema->setFormFormatterName('BreakTags');
    }    
    
    /**
     * Get Leave Period choices as an array.
     * @return array Array of leave periods
     */
    protected function getLeavePeriodChoices() {
        $leavePeriodChoices = array();

        $leavePeriods = $this->getLeavePeriodService()->getLeavePeriodList();
        if (empty($leavePeriods)) {  
            $leavePeriodChoices[0] = __('No Leave Periods');
        } else {
            foreach($leavePeriods as $leavePeriod) {
                $id = $leavePeriod->getLeavePeriodId();
                $label = set_datepicker_date_format($leavePeriod->getStartDate()) 
                        . " " . __("to") . " " 
                        . set_datepicker_date_format($leavePeriod->getEndDate());
                
                $leavePeriodChoices[$id] = $label;
            }
        } 
        
        return $leavePeriodChoices;
    }
    
    public function getJavaScripts() {
        $javaScripts = parent::getJavaScripts();
        $javaScripts[] = '/orangehrmCoreLeavePlugin/js/viewHolidayListSuccessSearch.js';
        
        return $javaScripts;
    }    
    
    public function getStylesheets() {
        parent::getStylesheets();
        
        $styleSheets = parent::getStylesheets();
        $styleSheets['/orangehrmCoreLeavePlugin/css/viewHolidayListSuccessSearch.css'] = 'screen';
        
        return $styleSheets;        
    }    
}

