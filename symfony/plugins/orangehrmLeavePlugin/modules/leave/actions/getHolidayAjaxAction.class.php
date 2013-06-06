<?php

/*
 *
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
 * Displaying ApplyLeave UI and saving data
 *
 * @author Samantha Jayasinghe
 */
class getHolidayAjaxAction extends sfAction {

   /**
     * get Method for Holiday Service
     *
     * @return HolidayService $holidayService
     */
    public function getHolidayService() {
        if (is_null($this->holidayService)) {
            $this->holidayService = new HolidayService();
        }
        return $this->holidayService;
    }

    /**
     * Set HolidayService
     * @param HolidayService $holidayService
     */
    public function setHolidayService(HolidayService $holidayService) {
        $this->holidayService = $holidayService;
    }
    
    /**
     *
     * @param type $request 
     */
    public function execute( $request ){
        sfConfig::set('sf_web_debug', false);
        sfConfig::set('sf_debug', false);
        

        $holidayList = $this->getHolidayList();

        $dateList =  array();
        
        foreach ($holidayList as $holiday) {
            $htype = $holiday->getLength() == 0 ? 'f' : 'h';
            
            $dateList[] = array(date('Y',  strtotime($holiday->getdate())), date('m',  strtotime($holiday->getdate())),date('d',  strtotime($holiday->getdate())),$htype, $holiday->getRecurring() ) ;
        }

      
        $response = $this->getResponse();
        $response->setHttpHeader('Expires', '0');
        $response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0");
        $response->setHttpHeader("Cache-Control", "private", false);
        
        
        
        echo json_encode($dateList);
        return sfView::NONE;
    }
    
    /**
     * 
     * @return Holiday List 
     */
    public function getHolidayList(){
        return $this->getHolidayService()->getFullHolidayList();
    }
    

}

?>
