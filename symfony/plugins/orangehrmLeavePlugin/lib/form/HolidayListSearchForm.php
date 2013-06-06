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
        
        
        $this->setWidget('calFromDate',new ohrmWidgetDatePicker(array(), array('id' => 'calFromDate')));
        $this->setWidget('calToDate',new ohrmWidgetDatePicker(array(), array('id' => 'calToDate')));
        
        //$this->widgetSchema->setLabels(array('date' => __("Date Range")));
        
        $this->widgetSchema->setLabels(array('calFromDate' => __('From'),'calToDate' => __('To')));
       
        $this->setvalidators(array(
            'calFromDate' => new ohrmDateValidator(array('required' => true)),
            'calToDate' => new ohrmDateValidator(array('required' => true))
        ));
        
        $this->widgetSchema->setNameFormat('holidayList[%s]'); 
        
        $this->setDefaultDates();
    }    
    
    
    
    public function getJavaScripts() {
        $javaScripts = parent::getJavaScripts();
        $javaScripts[] = plugin_web_path('orangehrmLeavePlugin', 'js/viewHolidayListSuccessSearch.js');
        
        return $javaScripts;
    }    
    
    public function getStylesheets() {
        parent::getStylesheets();
        
        $styleSheets = parent::getStylesheets();
      //  $styleSheets['/orangehrmLeavePlugin/css/viewHolidayListSuccessSearch.css'] = 'screen';
        
        return $styleSheets;        
    }    
    
    protected function setDefaultDates() {
        $now = time();
        
        // If leave period defined, use leave period start and end date
        $calenderYear = $this->getLeavePeriodService()->getCalenderYearByDate($now);        
                
 
        
        $this->setDefaults(array('calFromDate' => set_datepicker_date_format($calenderYear[0]),
            'calToDate' => set_datepicker_date_format($calenderYear[1])));
    }
}

