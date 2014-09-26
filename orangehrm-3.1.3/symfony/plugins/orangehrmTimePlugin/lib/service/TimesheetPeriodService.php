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
class TimesheetPeriodService {

	private $timesheetPeriodDao;

	/**
	 * Get the TimesheetPeriod Data Access Object
	 * @return TimesheetPeriodDao
	 */
	public function getTimesheetPeriodDao() {


		if (is_null($this->timesheetPeriodDao)) {
			$this->timesheetPeriodDao = new TimesheetPeriodDao();
		}

		return $this->timesheetPeriodDao;
	}

	public function setTimesheetPeriodDao(TimesheetPeriodDao $timesheetPeriodDao) {

		$this->timesheetPeriodDao = $timesheetPeriodDao;
	}

	public function getDefinedTimesheetPeriod($currentDate) {

		$xmlString = $this->getTimesheetPeriodDao()->getDefinedTimesheetPeriod();
		$xml = simplexml_load_String($xmlString);
       

		return $this->getDaysOfTheTimesheetPeriod($xml, $currentDate);
	}

	public function getDaysOfTheTimesheetPeriod($xml, $currentDate) {

		$timesheetPeriodFactory = new TimesheetPeriodFactory();
		$timesheetPeriodObject = $timesheetPeriodFactory->createTimesheetPeriod($xml);
		return $timesheetPeriodObject->calculateDaysInTheTimesheetPeriod($currentDate, $xml);
	}

	public function isTimesheetPeriodDefined() {
		return $this->getTimesheetPeriodDao()->isTimesheetPeriodDefined();
	}

	public function setTimesheetPeriod($startDay) {

		$timesheetPeriodFactory = new TimesheetPeriodFactory();
		$timesheetPeriodObject = $timesheetPeriodFactory->setTimesheetPeriod();
		$xml = $timesheetPeriodObject->setTimesheetPeriodAndStartDate($startDay);
		$this->getTimesheetPeriodDao()->setTimesheetPeriod();
		return $this->getTimesheetPeriodDao()->setTimesheetPeriodAndStartDate($xml);
	}

    public function getTimesheetHeading(){
        
        $xmlString = $this->getTimesheetPeriodDao()->getDefinedTimesheetPeriod();
		$xml = simplexml_load_String($xmlString);
        
        return $xml->Heading;
       
        
    }


}

?>
