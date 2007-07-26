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
 *
 */

class TimesheetSubmissionPeriod {

	/**
	 * Class constants
	 */
	const TIMESHEET_SUBMISSION_PERIOD_DB_TABLE_TIMESHEET_SUBMISSION_PERIOD = 'hs_hr_timesheet_submission_period';

	const TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_TIMESHEET_PERIOD_ID = 'timesheet_period_id';
	const TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_NAME = 'name';
	const TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_FREQUENCY = 'frequency';
	const TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_PERIOD = 'period';
	const TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_START_DAY = 'start_day';
	const TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_END_DAY = 'end_day';
	const TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_DESCRIPTION = 'description';

	const TIMESHEET_SUBMISSION_PERIOD_FREQUENCY_DAY = 1;
	const TIMESHEET_SUBMISSION_PERIOD_FREQUENCY_WEEK = 7;
	const TIMESHEET_SUBMISSION_PERIOD_FREQUENCY_MONTH = 31;

	/**
	 * Class atributes
	 */
	private $timesheetPeriodId;
	private $name;
	private $frequency;
	private $period;
	private $startDay;
	private $endDay;
	private $description;

	public function setTimesheetPeriodId($timesheetPeriodId) {
		$this->timesheetPeriodId=$timesheetPeriodId;
	}

	public function getTimesheetPeriodId() {
		return $this->timesheetPeriodId;
	}

	public function setName($name) {
		$this->name=$name;
	}

	public function getName() {
		return $this->name;
	}

	public function setFrequency($frequency) {
		$this->frequency=$frequency;
	}

	public function getFrequency() {
		return $this->frequency;
	}

	public function setPeriod($period) {
		$this->period=$period;
	}

	public function getPeriod() {
		return $this->period;
	}

	public function setStartDay($startDay) {
		$this->startDay=$startDay;
	}

	public function getStartDay() {
		return $this->startDay;
	}

	public function setEndDay($endDay) {
		$this->endDay=$endDay;
	}

	public function getEndDay() {
		return $this->endDay;
	}

	public function setDescription($description) {
		$this->description=$description;
	}

	public function getDescription() {
		return $this->description;
	}

	public function __construct() {
		//nothing to do
	}

	public function saveTimesheetSubmissionPeriod() {
		$this->_findEndDay();

		$sql_builder = new SQLQBuilder();

		$updateTable = self::TIMESHEET_SUBMISSION_PERIOD_DB_TABLE_TIMESHEET_SUBMISSION_PERIOD;

		if ($this->getName() != null) {
			$updateFields[] = "`".self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_NAME."`";
			$updateValues[] = "'{$this->getName()}'";
		}

		if ($this->getFrequency() != null) {
			$updateFields[] = "`".self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_FREQUENCY."`";
			$updateValues[] = $this->getFrequency();
		}

		if ($this->getPeriod() != null) {
			$updateFields[] = "`".self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_PERIOD."`";
			$updateValues[] = $this->getPeriod();
		}

		if ($this->getStartDay() != null) {
			$updateFields[] = "`".self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_START_DAY."`";
			$updateValues[] = $this->getStartDay();
		}

		if ($this->getEndDay() != null) {
			$updateFields[] = "`".self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_END_DAY."`";
			$updateValues[] = $this->getEndDay();
		}

		if ($this->getDescription() != null) {
			$updateFields[] = "`".self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_DESCRIPTION."`";
			$updateValues[] = "'{$this->getDescription()}'";
		}

		$updateConditions[] = "`".self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_TIMESHEET_PERIOD_ID."` = {$this->getTimesheetPeriodId()}";

		$query = $sql_builder->simpleUpdate($updateTable, $updateFields, $updateValues, $updateConditions);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		if ($result) {
			if (mysql_affected_rows() > 0) {
				return true;
			}
		}

		return false;
	}

	public function fetchTimesheetSubmissionPeriods() {
		$sql_builder = new SQLQBuilder();

		$selectTable = self::TIMESHEET_SUBMISSION_PERIOD_DB_TABLE_TIMESHEET_SUBMISSION_PERIOD." a ";

		$selectFields[0] = "a.`".self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_TIMESHEET_PERIOD_ID."`";
		$selectFields[1] = "a.`".self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_NAME."`";
		$selectFields[2] = "a.`".self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_FREQUENCY."`";
		$selectFields[3] = "a.`".self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_PERIOD."`";
		$selectFields[4] = "a.`".self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_START_DAY."`";
		$selectFields[5] = "a.`".self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_END_DAY."`";
		$selectFields[6] = "a.`".self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_DESCRIPTION."`";

		$selectConditions=null;

		if ($this->getTimesheetPeriodId() != null) {
			$selectConditions[] = "a.`".self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_TIMESHEET_PERIOD_ID."` = {$this->getTimesheetPeriodId()}";
		}

		if ($this->getName() != null) {
			$selectConditions[] = "a.`".self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_NAME."` = {$this->getName()}";
		}

		if ($this->getFrequency() != null) {
			$selectConditions[] = "a.`".self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_FREQUENCY."` = {$this->getFrequency()}";
		}

		if ($this->getPeriod() != null) {
			$selectConditions[] = "a.`".self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_PERIOD."` = {$this->getPeriod()}";
		}

		if ($this->getStartDay() != null) {
			$selectConditions[] = "a.`".self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_START_DAY."` = {$this->getStartDay()}";
		}

		if ($this->getEndDay() != null) {
			$selectConditions[] = "a.`".self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_END_DAY."` = {$this->getEndDay()}";
		}

		if ($this->getDescription() != null) {
			$selectConditions[] = "a.`".self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_DESCRIPTION."` = {$this->getDescription()}";
		}

		$query = $sql_builder->simpleSelect($selectTable, $selectFields, $selectConditions, $selectFields[0], 'ASC');

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		$objArr = $this->_buildObjArr($result);

		return $objArr;
	}

	private function _findEndDay() {
		if (($this->getFrequency() == null) || ($this->getStartDay() == null)) {
			throw new TimesheetSubmissionPeriodException("Unable to determine the end date", -2);
		}

		$tmpEndDate = $this->getStartDay()+$this->getFrequency()-1;
		$tmpEndDate = $tmpEndDate%self::TIMESHEET_SUBMISSION_PERIOD_FREQUENCY_WEEK;

		if ($tmpEndDate == 0) {
			$tmpEndDate = self::TIMESHEET_SUBMISSION_PERIOD_FREQUENCY_WEEK;
		}

		$this->setEndDay($tmpEndDate);

		return true;
	}

	private function _buildObjArr($result) {
		$objArr = null;

		while ($row = mysql_fetch_assoc($result)) {
			$tmpTimeArr = new TimesheetSubmissionPeriod();

			$tmpTimeArr->setTimesheetPeriodId($row[self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_TIMESHEET_PERIOD_ID]);
			$tmpTimeArr->setName($row[self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_NAME]);
			$tmpTimeArr->setFrequency($row[self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_FREQUENCY]);
			$tmpTimeArr->setPeriod($row[self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_PERIOD]);
			$tmpTimeArr->setStartDay($row[self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_START_DAY]);
			$tmpTimeArr->setEndDay($row[self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_END_DAY]);
			$tmpTimeArr->setDescription($row[self::TIMESHEET_SUBMISSION_PERIOD_DB_FIELD_DESCRIPTION]);

			$objArr[] = $tmpTimeArr;
		}

		return $objArr;
	}
}

class TimesheetSubmissionPeriodException extends Exception {
}
?>
