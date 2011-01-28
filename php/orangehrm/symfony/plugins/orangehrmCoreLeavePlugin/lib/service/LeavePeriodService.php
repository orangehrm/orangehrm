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
	private $leavePeriodDao;

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

        if ($this->leavePeriodDao instanceof LeavePeriodDao) {
            return $this->leavePeriodDao;
        } else {
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
        sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');
		$monthNames = array();
		for ($i = 1; $i <= 12; $i ++) {
			$monthNames[] = __(date('F', mktime(0, 0, 0, ($i + 1), 0, 0)));
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
        sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');
		$year = empty($year) ? date('Y') : $year;

		/* TODO: Add validations of paramerter combinations creating invalid dates */

		$startDateTimestamp = strtotime("{$year}-{$month}-{$date}");

		$currentTimestamp = strtotime(date('Y-m-d'), true);
		$timeCalculationString = ($startDateTimestamp > $currentTimestamp) ? '-1 day' : '+1 year, -1 day';

		$endDateTimestamp = strtotime($timeCalculationString, $startDateTimestamp);
        if($format == "F d") {
            return __(date("F", $endDateTimestamp)) . " " . date("d", $endDateTimestamp);
        }
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
        $startDate = ($isLeavePeriodStartOnFeb29th == 'Yes')?$nonLeapYearLeavePeriodStartDate:$startDate;

        $currentDate  = $leavePeriodDataHolder->getCurrentDate();
        $currentDateTimestamp = strtotime($currentDate);

        $currentYear = date('Y', strtotime($currentDate));
        $startDate = (($currentYear % 4) == 0 && $isLeavePeriodStartOnFeb29th == 'Yes')?'02-29':$startDate;

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
	 * Saves the leave period passed as the paramerter
	 *
	 * @param LeavePeriod $leavePeriod
	 * @return bool Returns true if leave period is saved successfully
	 */
	public function saveLeavePeriod(LeavePeriod $leavePeriod) {
        $this->getLeavePeriodDao()->saveLeavePeriod($leavePeriod);
        OrangeConfig::getInstance()->loadAppConf();
        OrangeConfig::getInstance()->setAppConfValue(Config::KEY_LEAVE_PERIOD_DEFINED, 'Yes');
        return true;
	}

	/**
	 * Return the current leave period
	 *
	 * @return LeavePeriod Current leave period
	 */
	public function getCurrentLeavePeriod() {
        $currentTimestamp = strtotime(date('Y-m-d'), true);
		$leavePeriod = $this->getLeavePeriod($currentTimestamp);

		if (is_null($leavePeriod)) {
			$leavePeriod = $this->createNextLeavePeriod();
		}

		return $leavePeriod;
	}

	/**
	 * Gets the leave period of which includes a particular timestamp
	 *
	 * @param int $timestamp Timestamp
	 * @return LeavePeriod Instance of LeavePeriod class of which the timestamp belongs to
	 */
	public function getLeavePeriod($timestamp) {
        
		$leavePeriod = null;

      $leavePeriod = $this->getLeavePeriodDao()->filterByTimestamp($timestamp);

		return $leavePeriod;
        
	}

	/**
	 * Adjusts the end date of the current leave period, if the start date is changed
	 *
	 * @param string $mode
	 * @param Date $newEndDate End date of the new leave period
	 * @return bool True if the opertaion is successful
	 */
	public function adjustCurrentLeavePeriod($newEndDate) {

        $currentLeavePeriod = $this->getCurrentLeavePeriod();

        /* Checking "old next leave period" should happen
         * before saving new dates for current leave period
         */
        $oldNextLeavePeriod = $this->getNextLeavePeriodByCurrentEndDate($currentLeavePeriod->getEndDate());

		$currentLeavePeriod->setEndDate($newEndDate);
        $leavePeriodDao = $this->getLeavePeriodDao();
		$result = $leavePeriodDao->saveLeavePeriod($currentLeavePeriod);

		if ($result && !is_null($oldNextLeavePeriod)) {
			$this->_adjustNextLeavePeriod($oldNextLeavePeriod->getLeavePeriodId());
		}

		return $result;
	}

    public function getNextLeavePeriodByCurrentEndDate($currentLeavePeriodEndDate) {

		$timestampInOldNextLeavePeriod = strtotime('+2 day', strtotime($currentLeavePeriodEndDate));
		$nextLeavePeriod = $this->getLeavePeriodDao()->filterByTimestamp($timestampInOldNextLeavePeriod);

        if ($nextLeavePeriod instanceof LeavePeriod) {
            return $nextLeavePeriod;
        } else {
            return null;
        }

    }

	/**
	 * Creates the next leave period
	 *
	 * @return LeavePeriod Newly create leave period object
	 */
	public function createNextLeavePeriod($date = null) {

		$lastLeavePeriod = $this->getLeavePeriodDao()->findLastLeavePeriod($date);


		if (!is_null($lastLeavePeriod)) {

            $newLeavePeriod = $this->getNextLeavePeriodByCurrentEndDate($lastLeavePeriod->getEndDate());

            if (!empty($newLeavePeriod)) {
              
                return $newLeavePeriod;

            } else {

                $lastEndDateTimestamp = strtotime($lastLeavePeriod->getEndDate());
                $dateInNextLeavePeriodTimestamp = strtotime('+1 day', $lastEndDateTimestamp);
                $dateInNextLeavePeriod = date('Y-m-d', $dateInNextLeavePeriodTimestamp);
                
                $leavePeriodDataHolder = $this->_getPopulatedLeavePeriodDataHolder();
                $leavePeriodDataHolder->setCurrentDate($dateInNextLeavePeriod);
                $nextStartDate = $this->generateStartDate($leavePeriodDataHolder);

                $leavePeriodDataHolder->setLeavePeriodStartDate($nextStartDate);
                $nextEndDate = $this->generateEndDate($leavePeriodDataHolder);


                $newLeavePeriod = new LeavePeriod();
                $newLeavePeriod->setStartDate($nextStartDate);
                $newLeavePeriod->setEndDate($nextEndDate);

                try {
                    $this->getLeavePeriodDao()->saveLeavePeriod($newLeavePeriod);
                } catch (Exception $e) {
                    // TODO: Warn
                }

                return $newLeavePeriod;

            }

		}
		
		return null;
        
	}

    private function _getPopulatedLeavePeriodDataHolder() {

        $leavePeriodDataHolder = new LeavePeriodDataHolder();

        $isLeavePeriodStartOnFeb29th = ParameterService::getParameter('isLeavePeriodStartOnFeb29th');
        $nonLeapYearLeavePeriodStartDate = ParameterService::getParameter('nonLeapYearLeavePeriodStartDate');
        $leavePeriodStartDate = ParameterService::getParameter('leavePeriodStartDate');


        $leavePeriodDataHolder->setIsLeavePeriodStartOnFeb29th($isLeavePeriodStartOnFeb29th);
        $leavePeriodDataHolder->setNonLeapYearLeavePeriodStartDate($nonLeapYearLeavePeriodStartDate);
        $leavePeriodDataHolder->setStartDate($leavePeriodStartDate);
        $leavePeriodDataHolder->setDateFormat('Y-m-d');

        return $leavePeriodDataHolder;

    }


	/**
	 *
	 * @return LeavePeriod Collection
	 */
	public function getLeavePeriodList() {

        return $this->getLeavePeriodDao()->getLeavePeriodList();
        
	}

	/**
	 * Checks whether a given timestamp falls withing the next leave period
	 * 
	 * @param int $timestamp
	 * @return boolean True if $timestamp falls within the start date and end date of the next leave period
	 */
	public function isWithinNextLeavePeriod($timestamp) {
		$currentEndDate = strtotime($this->getCurrentLeavePeriod()->getEndDate());
		$nextStartDate = strtotime('+1 day', $currentEndDate);
		$nextEndDate = strtotime('+1 year, -1 day', $nextStartDate);
		
		return ($timestamp >= $nextStartDate && $timestamp <= $nextEndDate);
	}
	
	private function _adjustNextLeavePeriod($oldNextLeavePeriodId) {

        if (!empty($oldNextLeavePeriodId)) {

            $oldNextLeavePeriod = $this->getLeavePeriodDao()->readLeavePeriod($oldNextLeavePeriodId);

	    	$nextPeriodStartDate = strtotime('+1 day', strtotime($this->getCurrentLeavePeriod()->getEndDate()));
	    	$nextPeriodEndDate = strtotime('+1 year, -1 day', $nextPeriodStartDate);

	    	$oldNextLeavePeriod->setStartDate(date('Y-m-d', $nextPeriodStartDate));
	    	$oldNextLeavePeriod->setEndDate(date('Y-m-d', $nextPeriodEndDate));
	    	$this->getLeavePeriodDao()->saveLeavePeriod($oldNextLeavePeriod);

		}

	}

    public function readLeavePeriod($leavePeriodId) {
        return $this->getLeavePeriodDao()->readLeavePeriod($leavePeriodId);
    }
}

