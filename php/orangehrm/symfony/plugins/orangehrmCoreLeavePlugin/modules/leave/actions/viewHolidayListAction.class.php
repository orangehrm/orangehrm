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
 * view list of holidays
 */
class viewHolidayListAction extends sfAction {

    private $holidayService;
    private $leavePeriodService;
    private $workWeekEntity;
    
    /**
     * get Method for WorkWeekEntity
     *
     * @return WorkWeek $workWeekEntity
     */
    public function getWorkWeekEntity() {
        $this->workWeekEntity = new WorkWeek();
        return $this->workWeekEntity;
    }    
           

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
     * view Holiday list
     * @param sfWebRequest $request
     */ 
    public function execute($request) {

        $this->searchForm = $this->getSearchForm();
        $leavePeriodService = $this->getLeavePeriodService();

        //retrieve current leave period id
        $leavePeriodId = (!$leavePeriodService->getCurrentLeavePeriod() instanceof LeavePeriod)?0:$leavePeriodService->getCurrentLeavePeriod()->getLeavePeriodId();

        $startDate = date("Y-m-d");
        $endDate = date("Y-m-d");
        if($leavePeriodService->getCurrentLeavePeriod() instanceof LeavePeriod) {
            $startDate = $leavePeriodService->getCurrentLeavePeriod()->getStartDate();
            $endDate = $leavePeriodService->getCurrentLeavePeriod()->getEndDate();
        }

        if($request->isMethod('post')) {
            
            $this->searchForm->bind($request->getParameter($this->searchForm->getName()));
            
            if ($this->searchForm->isValid()) {
                $leavePeriodId = $this->searchForm->getValue('leave_period');
                $leavePeriod = $leavePeriodService->readLeavePeriod($leavePeriodId);
                if($leavePeriod instanceof LeavePeriod) {
                    $startDate = $leavePeriod->getStartDate();
                    $endDate = $leavePeriod->getEndDate();
                }
            }
        }

        $this->leavePeriodId = $leavePeriodId;
        $this->daysLenthList = WorkWeek::getDaysLengthList();
        $this->yesNoList = WorkWeek::getYesNoList();
        $this->holidayList = $this->getHolidayService()->searchHolidays($startDate, $endDate);

        $this->setListComponent($this->holidayList);

        $message = $this->getUser()->getFlash('templateMessage');        
        $this->messageType = (isset($message[0]))?strtolower($message[0]):"";
        $this->message = (isset($message[1]))?$message[1]:"";
        

        if ($this->getUser()->hasFlash('templateMessage')) {
            $this->templateMessage = $this->getUser()->getFlash('templateMessage');
            $this->getUser()->setFlash('templateMessage', array());
        }
    }
    
    protected function getSearchForm() {
        return new HolidayListSearchForm(array(), array(), true);
    }
    
    protected function setListComponent($holidayList) {

        $configurationFactory = $this->getListConfigurationFactory();
        
        ohrmListComponent::setActivePlugin('orangehrmCoreLeavePlugin');
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setListData($holidayList);
        ohrmListComponent::setPageNumber(0);
        $numRecords = count($holidayList);
        ohrmListComponent::setItemsPerPage($numRecords);
        ohrmListComponent::setNumberOfRecords($numRecords);
    }
    
    protected function getListConfigurationFactory() {
        return new HolidayListConfigurationFactory();
    }    

}
