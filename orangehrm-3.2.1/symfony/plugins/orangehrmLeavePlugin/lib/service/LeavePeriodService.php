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
 * Leave Period Service
 */
class LeavePeriodService extends BaseService {

    const LEAVE_PERIOD_STATUS_FORCED = 1;
    const LEAVE_PERIOD_STATUS_NOT_FORCED = 2;
    const LEAVE_PERIOD_STATUS_NOT_APPLICABLE = 3;
    
    private $leavePeriodDao;
    private $leavePeriodList = null;
    protected $leaveEntitlementService = null;
    
    private static $leavePeriodStatus = null;
    private static $currentLeavePeriodStartDateAndMonth = null;
    private static $leavePeriodHistoryList = null;
    
    
    /**
     * Sets the instance of LeaveEntitlementService class
     *
     * @param LeaveEntitlementService $leaveEntitlementService
     * @return void
     */
    public function setLeaveEntitlementService(LeaveEntitlementService $leaveEntitlementService) {
        $this->leaveEntitlementService = $leaveEntitlementService;
    }

    /**
     * Returns the instance of LeaveEntitlementService
     *
     * @return LeaveEntitlementService LeaveEntitlementService object
     */
    public function getLeaveEntitlementService() {

        if (!($this->leaveEntitlementService instanceof LeaveEntitlementService)) {
            $this->leaveEntitlementService = new LeaveEntitlementService();
        }

        return $this->leaveEntitlementService;
    }
    
    /**
     * Sets the instance of LeavePeriodDao class
     *
     * @param LeavePeriodDao $leavePeriodDao
     * @return void
     */
    public function setLeavePeriodDao(LeavePeriodDao $leavePeriodDao) {
        $this->leavePeriodDao = $leavePeriodDao;
    }

    /**
     * Returns the instance of LeavePeriodDao class of LeavePeriodService
     *
     * @return LeavePeriodDao LeavePeriodDao object
     */
    public function getLeavePeriodDao() {

        if (!($this->leavePeriodDao instanceof LeavePeriodDao)) {
            $this->leavePeriodDao = new LeavePeriodDao();
        }

        return $this->leavePeriodDao;
    }

    /**
     * Returns the list of month names in year
     *
     * @return array Array of month names
     */
    public function getListOfMonths() {
        $monthNames = array();
        for ($i = 1; $i <= 12; $i++) {
            $monthNames[] = date('F', mktime(0, 0, 0, ($i + 1), 0, 0));
        }

        return $monthNames;
    }

    /**
     * Returns the array of dates that can have for the given month
     *
     * @param int $month Month to which the list of dates be created
     *
     * @return array Array of dates that can fall in the given month
     */
    public function getListOfDates($month, $isLeapYear = true) {
        switch ($month) {
            case 1 :
            case 3 :
            case 5 :
            case 7 :
            case 8 :
            case 10 :
            case 12 :
                return range(1, 31);
                break;

            case 4:
            case 6:
            case 9:
            case 11:
                return range(1, 30);
                break;

            case 2 :
                $lastDayOfFebruary = ($isLeapYear) ? 29 : 28;
                return range(1, $lastDayOfFebruary);
                break;

            default :
                throw new LeaveServiceException('Invalid value passed for month in LeavePeriodService::getListOfDates()');
                break;
        }
    }

    /**
     * Calculates the end date of the leave period, given the start date
     *
     * @param int $month Start month
     * @param int $date Start date
     * @param int $year Start year (Default: current year)
     *
     * @return string End date of the leave period in the pre-defined format
     */
    public function calculateEndDate($month, $date, $year = null, $format = 'Y-m-d') {
        $year = empty($year) ? date('Y') : $year;

        /* TODO: Add validations of paramerter combinations creating invalid dates */

        $startDateTimestamp = strtotime("{$year}-{$month}-{$date}");

        $currentTimestamp = strtotime(date('Y-m-d'), true);
        $timeCalculationString = ($startDateTimestamp > $currentTimestamp) ? '-1 day' : '+1 year, -1 day';

        $endDateTimestamp = strtotime($timeCalculationString, $startDateTimestamp);
        return date($format, $endDateTimestamp);
    }

    public function generateEndDate(LeavePeriodDataHolder $leavePeriodDataHolder) {

        $isLeavePeriodStartOnFeb29th = $leavePeriodDataHolder->getIsLeavePeriodStartOnFeb29th();
        $nonLeapYearLeavePeriodStartDate = $leavePeriodDataHolder->getNonLeapYearLeavePeriodStartDate();
        $dateFormat = $leavePeriodDataHolder->getDateFormat();
        $leavePeriodStartDate = $leavePeriodDataHolder->getLeavePeriodStartDate();
        $leavePeriodStartDateTimestamp = strtotime($leavePeriodStartDate);

        if ($isLeavePeriodStartOnFeb29th == 'Yes') {

            $nextYear = date('Y', strtotime('+1 year', $leavePeriodStartDateTimestamp));

            if (($nextYear % 4) == 0) {

                return $nextYear . '-02-28';
            } else {

                $nextLeavePeriodStartDate = $nextYear . '-' . $nonLeapYearLeavePeriodStartDate;
                $leavePeriodEndDateTimestamp = strtotime('-1 day', strtotime($nextLeavePeriodStartDate));

                return date($dateFormat, $leavePeriodEndDateTimestamp);
            }
        } else {

            return date($dateFormat, strtotime('+1 year, -1 day', $leavePeriodStartDateTimestamp));
        }
    }

    /**
     *
     * @param int $month Start month
     * @param int $date Start date
     * @param int $year Start year (Default: current year)
     *
     * @return string Start date of the leave period in the pre-defined format
     */
    public function calculateStartDate($month, $date, $year = null, $format = 'Y-m-d') {
        $year = empty($year) ? date('Y') : $year;
        $startDateTimestamp = strtotime("{$year}-{$month}-{$date}");
        $currentTimestamp = strtotime(date('Y-m-d'), true);
        if ($startDateTimestamp > $currentTimestamp) {
            $startDateTimestamp = strtotime('-1 year', $startDateTimestamp);
        }

        return date($format, $startDateTimestamp);
    }

    public function generateStartDate(LeavePeriodDataHolder $leavePeriodDataHolder) {

        $dateFormat = $leavePeriodDataHolder->getDateFormat();
        $isLeavePeriodStartOnFeb29th = $leavePeriodDataHolder->getIsLeavePeriodStartOnFeb29th();
        $nonLeapYearLeavePeriodStartDate = $leavePeriodDataHolder->getNonLeapYearLeavePeriodStartDate();
        $startDate = $leavePeriodDataHolder->getStartDate();
        $startDate = ($isLeavePeriodStartOnFeb29th == 'Yes') ? $nonLeapYearLeavePeriodStartDate : $startDate;

        $currentDate = $leavePeriodDataHolder->getCurrentDate();
        $currentDateTimestamp = strtotime($currentDate);

        $currentYear = date('Y', strtotime($currentDate));
        $startDate = (($currentYear % 4) == 0 && $isLeavePeriodStartOnFeb29th == 'Yes') ? '02-29' : $startDate;

        $leavePeriodStartDate = $currentYear . '-' . $startDate;
        $leavePeriodStartDateTimestamp = strtotime($leavePeriodStartDate);

        if ($leavePeriodStartDateTimestamp > $currentDateTimestamp) {
            $leavePeriodStartDateTimestamp = strtotime('-1 year', $leavePeriodStartDateTimestamp);
        }

        $year = date('Y', $leavePeriodStartDateTimestamp);

        if ($isLeavePeriodStartOnFeb29th == 'Yes' && ($year % 4) == 0) {

            return $year . '-' . '02-29';
        }

        return date($dateFormat, $leavePeriodStartDateTimestamp);
    }

    
    
    
    /**
     * 
     * @param LeavePeriodHistory $leavePeriodHistory
     * @return LeavePeriodHistory
     */
    public function saveLeavePeriodHistory(LeavePeriodHistory $leavePeriodHistory) {

        $conn = Doctrine_Manager::connection();
        $conn->beginTransaction();

        try {
            $currentLeavePeriod = $this->getCurrentLeavePeriodStartDateAndMonth();

            $strategy = $this->getLeaveEntitlementService()->getLeaveEntitlementStrategy();

            $leavePeriodHistory = $this->getLeavePeriodDao()->saveLeavePeriodHistory($leavePeriodHistory);
            $isLeavePeriodDefined = OrangeConfig::getInstance()->getAppConfValue(ConfigService::KEY_LEAVE_PERIOD_DEFINED);
            OrangeConfig::getInstance()->setAppConfValue(ConfigService::KEY_LEAVE_PERIOD_DEFINED, 'Yes');

            if ($isLeavePeriodDefined && !empty($currentLeavePeriod)) {
                $leavePeriodForToday = $this->getCurrentLeavePeriodByDate(date('Y-m-d'), true);
                $oldStartMonth = $currentLeavePeriod->getLeavePeriodStartMonth();
                $oldStartDay = $currentLeavePeriod->getLeavePeriodStartDay();
                $newStartMonth = $leavePeriodHistory->getLeavePeriodStartMonth();
                $newStartDay = $leavePeriodHistory->getLeavePeriodStartDay();
                
                $strategy->handleLeavePeriodChange($leavePeriodForToday, $oldStartMonth, $oldStartDay, $newStartMonth, $newStartDay);
            }
            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollback();
            throw new DaoException($e->getMessage());
        }
        
        return $leavePeriodHistory;
    }
    
    /**
     * Get Latest Leave period start date and month 
     * @param bool $forceReload (if false, will use cached value from previous method call)
     * value is cached in a static constant.
     * @return type
     */
    public function getCurrentLeavePeriodStartDateAndMonth($forceReload = false){
        
        if ($forceReload || is_null(self::$currentLeavePeriodStartDateAndMonth)) {
            self::$currentLeavePeriodStartDateAndMonth = $this->getLeavePeriodDao()->getCurrentLeavePeriodStartDateAndMonth();
        }
        
        return self::$currentLeavePeriodStartDateAndMonth;
    }
    
    protected function _getLeavePeriodHistoryList($forceReload = false) {
        
        if ($forceReload || is_null(self::$leavePeriodHistoryList)) {
            self::$leavePeriodHistoryList = $this->getLeavePeriodDao()->getLeavePeriodHistoryList();
        }
        
        return self::$leavePeriodHistoryList;
    }
    
    /**
     * Get Generated Leave Period List
     * @return type
     */
    public function getGeneratedLeavePeriodList($toDate = null, $forceReload = false){
        $leavePeriodList = array();
        $leavePeriodHistoryList = $this->_getLeavePeriodHistoryList($forceReload);
        
        if(count($leavePeriodHistoryList) == 0)
            throw new ServiceException("Leave Period Start date is not defined");
        
        if(empty($this->leavePeriodList)){
        
            $endDate = ($toDate != null)? new DateTime($toDate): new DateTime();
            //If To Date is not specified return leave type till next leave period 
            if(is_null( $toDate)){
                $endDate->add(new DateInterval('P1Y'));
            }
            

            $firstCreatedDate = new DateTime($leavePeriodHistoryList->getFirst()->getCreatedAt());
            $startDate = new DateTime($firstCreatedDate->format('Y')."-".$leavePeriodHistoryList->getFirst()->getLeavePeriodStartMonth()."-".$leavePeriodHistoryList->getFirst()->getLeavePeriodStartDay());
            if($firstCreatedDate < $startDate){
                $startDate->sub(new DateInterval('P1Y'));
            }
            $tempDate = $startDate;
            $i= 0;
            while( $tempDate <=  $endDate){

               $projectedSatrtDate = ($i==0)?$tempDate:new DateTime(date('Y-m-d',  strtotime($tempDate->format('Y-m-d')."+1 day")));
               $projectedEndDate = new DateTime(date('Y-m-d',  strtotime($projectedSatrtDate->format('Y-m-d')." +1 year -1 day")));

                foreach( $leavePeriodHistoryList as $leavePeriodHistory){

                    $createdDate = new DateTime( $leavePeriodHistory->getCreatedAt());

                    if( ($projectedSatrtDate < $createdDate) && ($createdDate < $projectedEndDate)) {
                        $newSatrtDate = new DateTime($createdDate->format('Y')."-".$leavePeriodHistory->getLeavePeriodStartMonth()."-".$leavePeriodHistory->getLeavePeriodStartDay());
                        if($createdDate <  $newSatrtDate){
                            $newSatrtDate->sub(new DateInterval('P1Y'));
                        }
                        $projectedEndDate = $newSatrtDate->add(DateInterval::createFromDateString('+1 year -1 day'));

                    }

                }

               $tempDate = $projectedEndDate;

                $leavePeriodList[] = array($projectedSatrtDate->format('Y-m-d') , $projectedEndDate->format('Y-m-d'));
                $i++;
            }
            $this->leavePeriodList = $leavePeriodList;
        }
        return $this->leavePeriodList;
    }
    
    /**
     * Get Current Leave Period a
     * @param type $date
     */
    public function getCurrentLeavePeriodByDate($date, $forceReload = false){
        $matchLeavePeriod = null;
        $this->leavePeriodList = $this->getGeneratedLeavePeriodList(null, $forceReload);
        $currentDate = new DateTime($date);
        foreach( $this->leavePeriodList as $leavePeriod){
            $startDate = new DateTime($leavePeriod[0]);
            $endDate = new DateTime($leavePeriod[1]);
            if(($startDate <= $currentDate) && ($currentDate <= $endDate)){
                $matchLeavePeriod = $leavePeriod;
                break;
                
            }
            
        }
        return $matchLeavePeriod;
    }
    
    public static function getLeavePeriodStatus($forceReload = false) {
        
        if ($forceReload || is_null(self::$leavePeriodStatus)) {
            self::$leavePeriodStatus = OrangeConfig::getInstance()->getAppConfValue(ConfigService::KEY_LEAVE_PERIOD_STATUS);
        }
        return self::$leavePeriodStatus;
    }

    /**
     * Get Calender Year By Date
     * @param type $time
     */
    public function getCalenderYearByDate( $time ){
            $year = date('Y', $time);
            $fromDate = $year . '-1-1';
            $toDate = $year . '-12-31';
            
            return array($fromDate,$toDate);
    }
}

