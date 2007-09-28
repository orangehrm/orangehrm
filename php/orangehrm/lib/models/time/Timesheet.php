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

require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH . '/lib/dao/SQLQBuilder.php';
require_once ROOT_PATH . '/lib/logs/LogFileWriter.php';
require_once ROOT_PATH . '/lib/confs/sysConf.php';

require_once ROOT_PATH . '/lib/models/time/TimesheetSubmissionPeriod.php';
require_once ROOT_PATH . '/lib/common/UniqueIDGenerator.php';
/**
 *
 */
class Timesheet {

	/**
	 * Class constants
	 */
	const TIMESHEET_DB_TABLE_TIMESHEET = "hs_hr_timesheet";

	const TIMESHEET_DB_FIELD_TIMESHEET_ID = "timesheet_id";
	const TIMESHEET_DB_FIELD_EMPLOYEE_ID = "employee_id";
	const TIMESHEET_DB_FIELD_TIMESHEET_PERIOD_ID = "timesheet_period_id";
	const TIMESHEET_DB_FIELD_START_DATE = "start_date";
	const TIMESHEET_DB_FIELD_END_DATE = "end_date";
	const TIMESHEET_DB_FIELD_STATUS = "status";
	const TIMESHEET_DB_FIELD_COMMENT = "comment";

	const TIMESHEET_DIRECTION_NEXT = 1;
	const TIMESHEET_DIRECTION_PREV = -1;

	const TIMESHEET_STATUS_NOT_SUBMITTED=0;
	const TIMESHEET_STATUS_SUBMITTED=10;
	const TIMESHEET_STATUS_APPROVED=20;
	const TIMESHEET_STATUS_REJECTED=30;

	/**
	 * Class atributes
	 */
	private $timesheetId;
	private $employeeId;
	private $timesheetPeriodId;
	private $startDate;
	private $endDate;
	private $status;
	private $comment;

	private $statuses;

	/**
	 * Class atribute setters and getters
	 */
	public function setTimesheetId($timesheetId) {
		$this->timesheetId=$timesheetId;
	}

	public function getTimesheetId() {
		return $this->timesheetId;
	}

	public function setEmployeeId($employeeId) {
		$this->employeeId=$employeeId;
	}

	public function getEmployeeId() {
		return $this->employeeId;
	}

	public function setTimesheetPeriodId($timesheetPeriodId) {
		$this->timesheetPeriodId=$timesheetPeriodId;
	}

	public function getTimesheetPeriodId() {
		return $this->timesheetPeriodId;
	}

	public function setStartDate($startDate) {
		$this->startDate=$startDate;
	}

	public function getStartDate() {
		return $this->startDate;
	}

	public function setEndDate($endDate) {
		$this->endDate=$endDate;
	}

	public function getEndDate() {
		return $this->endDate;
	}

	public function setStatus($status) {
		$this->status=$status;
	}

	public function getStatus() {
		return $this->status;
	}

	public function setComment($comment) {
		$this->comment=$comment;
	}

	public function getComment() {
		return $this->comment;
	}

	/**
	 * Special atribute setters
	 *
	 * For searching for multiple statuses
	 */
	public function setStatuses($statuses) {
		$this->statuses=$statuses;
	}

	public function getStatuses() {
		return $this->statuses;
	}

	public function __construct() {
		//nothing to do
	}

	public function __distruct() {
		//nothing to do
	}

	/**
	 * Generates the current timesheet start date and end date
	 *
	 * This will be called if start date of a time sheet is not set
	 */
	private function _getNewDates() {
		$timesheetSubmissionPeriodObj = new TimesheetSubmissionPeriod();

		$timesheetSubmissionPeriods = $timesheetSubmissionPeriodObj->fetchTimesheetSubmissionPeriods();

		if ($this->getStartDate() == null) {
			$day=date('w');

			$diff=$timesheetSubmissionPeriods[0]->getStartDay()-$day;
			if ($diff > 0) {
				$diff-=7;
			}
			$this->setStartDate(date('Y-m-d', time()+($diff*3600*24)));

			$diff1=$timesheetSubmissionPeriods[0]->getEndDay()-$day;

			if (6 >= ($diff1-$diff)) {
				$diff1+=6-($diff1-$diff);
			}

			$this->setEndDate(date('Y-m-d', time()+($diff1*3600*24)));

			$this->setTimesheetPeriodId($timesheetSubmissionPeriods[0]->getTimesheetPeriodId());
		}
	}

	/**
	 * Add a new timesheet
	 *
	 * Status will be overwritten
	 */
	public function addTimesheet() {

		$newId = UniqueIDGenerator::getInstance()->getNextID(self::TIMESHEET_DB_TABLE_TIMESHEET, self::TIMESHEET_DB_FIELD_TIMESHEET_ID);
		$this->setTimesheetId($newId);

		$this->_getNewDates();

		$this->setStatus(self::TIMESHEET_STATUS_NOT_SUBMITTED);

		$sql_builder = new SQLQBuilder();

		$insertTable = self::TIMESHEET_DB_TABLE_TIMESHEET;

		$insertFields[0] = "`".self::TIMESHEET_DB_FIELD_TIMESHEET_ID."`";
		$insertFields[1] = "`".self::TIMESHEET_DB_FIELD_EMPLOYEE_ID."`";
		$insertFields[2] = "`".self::TIMESHEET_DB_FIELD_TIMESHEET_PERIOD_ID."`";
		$insertFields[3] = "`".self::TIMESHEET_DB_FIELD_START_DATE."`";
		$insertFields[4] = "`".self::TIMESHEET_DB_FIELD_END_DATE."`";
		$insertFields[5] = "`".self::TIMESHEET_DB_FIELD_STATUS."`";

		$insertValues[0] = $this->getTimesheetId();
		$insertValues[1] = $this->getEmployeeId();
		$insertValues[2] = $this->getTimesheetPeriodId();
		$insertValues[3] = "'".$this->getStartDate()."'";
		$insertValues[4] = "'".$this->getEndDate()."'";
		$insertValues[5] = $this->getStatus();

		$insertValues = $sql_builder->quoteCorrect($insertValues);

		$query = $sql_builder->simpleInsert($insertTable, $insertValues, $insertFields);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		if ($result && (mysql_affected_rows() > 0)) {
			return true;
		}
		return false;
	}

	/**
	 * Submit timesheet
	 *
	 * @param boolean superior	Whether the request is coming from  a supervisor or a HR Admin
	 * @return boolean Submitted/Not
	 */
	public function submitTimesheet($superior=false) {

		if (!($superior || ($this->getStatus() == self::TIMESHEET_STATUS_NOT_SUBMITTED) || ($this->getStatus() == self::TIMESHEET_STATUS_REJECTED))) {
			return false;
		}

		$this->setStatus(self::TIMESHEET_STATUS_SUBMITTED);

		return $this->_changeTimesheetStatus();
	}

	/**
	 * Approve timesheet
	 */
	public function approveTimesheet() {

		if ($this->getStatus() != self::TIMESHEET_STATUS_SUBMITTED) {
			return false;
		}

		$this->setStatus(self::TIMESHEET_STATUS_APPROVED);
		$this->setComment($this->getComment());

		return $this->_changeTimesheetStatus();
	}

	/**
	 * Cancel timesheet
	 */
	public function cancelTimesheet() {

		if (($this->getStatus() != self::TIMESHEET_STATUS_SUBMITTED) && ($this->getStatus() != self::TIMESHEET_STATUS_REJECTED)) {
			return false;
		}

		$this->setStatus(self::TIMESHEET_STATUS_NOT_SUBMITTED);
		$this->setComment($this->getComment());

		return $this->_changeTimesheetStatus();
	}

	/**
	 * Reject timesheet
	 */
	public function rejectTimesheet() {

		if ($this->getStatus() != self::TIMESHEET_STATUS_SUBMITTED) {
			return false;
		}

		$this->setStatus(self::TIMESHEET_STATUS_REJECTED);
		$this->setComment($this->getComment());

		return $this->_changeTimesheetStatus();
	}

	/**
	 * Change the status of the filled timesheet
	 */
	private function _changeTimesheetStatus() {
		$sql_builder = new SQLQBuilder();

		$updateTable = self::TIMESHEET_DB_TABLE_TIMESHEET;

		$updateFields[0] = "`".self::TIMESHEET_DB_FIELD_STATUS."`";

		$updateValues[0] = $this->getStatus();

		if ($this->getComment() != null) {
			$updateFields[] = "`".self::TIMESHEET_DB_FIELD_COMMENT."`";
			$updateValues[] = "'".$this->getComment()."'";
		}

		$updateConditions[] = "`".self::TIMESHEET_DB_FIELD_TIMESHEET_ID."` = {$this->getTimesheetId()}";

		$query = $sql_builder->simpleUpdate($updateTable, $updateFields, $updateValues, $updateConditions);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);

		if ($result) {
			return true;
		}

		return false;
	}

	/**
	 * Fetch the next/previous Timesheet Id
	 *
	 * This will fetch the next or previous timesheet id of the current
	 * timesheet (start date and end date of the current timesheet)
	 *
	 * @param int direction
	 */
	public function fetchTimesheetId($direction) {
		$sql_builder = new SQLQBuilder();

		$selectTable = self::TIMESHEET_DB_TABLE_TIMESHEET." a ";

		$selectFields[0] = "a.`".self::TIMESHEET_DB_FIELD_TIMESHEET_ID."`";

		$selectConditions[] = "a.`".self::TIMESHEET_DB_FIELD_EMPLOYEE_ID."` = {$this->getEmployeeId()}";

		switch ($direction) {
			case self::TIMESHEET_DIRECTION_NEXT :
													$selectConditions[] = "a.`".self::TIMESHEET_DB_FIELD_START_DATE."` > '{$this->getEndDate()}'";
													break;
			case self::TIMESHEET_DIRECTION_PREV :
													$selectConditions[] = "a.`".self::TIMESHEET_DB_FIELD_START_DATE."` < '{$this->getStartDate()}'";
													break;
		}

		if ($this->getStatuses() != null) {
			$selectConditions[] = "a.`".self::TIMESHEET_DB_FIELD_STATUS."` IN(".implode(", ", $this->getStatuses()).")";
		} else if ($this->getStatus() != null) {
			$selectConditions[] = "a.`".self::TIMESHEET_DB_FIELD_STATUS."` = '{$this->getStatus()}'";
		}

		$query = $sql_builder->simpleSelect($selectTable, $selectFields, $selectConditions, $selectFields[0], 'ASC', 1);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		if ($result) {
			if ($row = mysql_fetch_assoc($result)) {
				return $row[self::TIMESHEET_DB_FIELD_TIMESHEET_ID];
			}
		}

		return false;
	}

	/**
	 * Retrieve timesheets in bulk
	 *
	 * Introduced for printing timesheets
	 *
	 * @param Integer page Page number
	 * @param String[] employeeIds Array of employee ids
	 */
	public function fetchTimesheetsBulk($page, $employeeIds) {
		$sql_builder = new SQLQBuilder();

		$selectTable = self::TIMESHEET_DB_TABLE_TIMESHEET." a ";

		$selectFields[0] = "a.`".self::TIMESHEET_DB_FIELD_TIMESHEET_ID."`";
		$selectFields[1] = "a.`".self::TIMESHEET_DB_FIELD_EMPLOYEE_ID."`";
		$selectFields[2] = "a.`".self::TIMESHEET_DB_FIELD_TIMESHEET_PERIOD_ID."`";
		$selectFields[3] = "a.`".self::TIMESHEET_DB_FIELD_START_DATE."`";
		$selectFields[4] = "a.`".self::TIMESHEET_DB_FIELD_END_DATE."`";
		$selectFields[5] = "a.`".self::TIMESHEET_DB_FIELD_STATUS."`";
		$selectFields[6] = "a.`".self::TIMESHEET_DB_FIELD_COMMENT."`";

        $selectConditions = null;

        $selectConditions[] = "a.`".self::TIMESHEET_DB_FIELD_EMPLOYEE_ID."` IN('".implode("', '", $employeeIds)."')";

		if ($this->getTimesheetPeriodId() != null) {
			$selectConditions[] = "a.`".self::TIMESHEET_DB_FIELD_TIMESHEET_PERIOD_ID."` = {$this->getTimesheetPeriodId()}";
		}
		if ($this->getStartDate() != null) {
			$selectConditions[] = "a.`".self::TIMESHEET_DB_FIELD_START_DATE."` >= '{$this->getStartDate()}'";
		}
		if ($this->getEndDate() != null) {
			$selectConditions[] = "a.`".self::TIMESHEET_DB_FIELD_END_DATE."` <= '{$this->getEndDate()}'";
		}
		if ($this->getStatuses() != null) {
			$selectConditions[] = "a.`".self::TIMESHEET_DB_FIELD_STATUS."` IN('".implode("', '", $this->getStatuses())."')";
		}

		$sysConfObj = new sysConf();

		if ($page == 0) {
			$selectLimit=null;
		} else {
			$selectLimit = (($page-1)*$sysConfObj->itemsPerPage).", $sysConfObj->itemsPerPage";
		}

		$query = $sql_builder->simpleSelect($selectTable, $selectFields, $selectConditions, "{$selectFields[1]}, {$selectFields[3]}", 'ASC', $selectLimit);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		$objArr = $this->_buildObjArr($result);

		return $objArr;
	}

	/**
	 * Count timesheets in bulk
	 *
	 * Introduced for printing timesheets
	 *
	 * @param String[] employeeIds Array of employee ids
	 */
	public function countTimesheetsBulk($employeeIds) {
		$sql_builder = new SQLQBuilder();

		$selectTable = self::TIMESHEET_DB_TABLE_TIMESHEET." a ";

		$selectFields[0] = "COUNT(a.`".self::TIMESHEET_DB_FIELD_TIMESHEET_ID."`)";

        $selectConditions = null;

        $selectConditions[] = "a.`".self::TIMESHEET_DB_FIELD_EMPLOYEE_ID."` IN('".implode("', '", $employeeIds)."')";

		if ($this->getTimesheetPeriodId() != null) {
			$selectConditions[] = "a.`".self::TIMESHEET_DB_FIELD_TIMESHEET_PERIOD_ID."` = {$this->getTimesheetPeriodId()}";
		}
		if ($this->getStartDate() != null) {
			$selectConditions[] = "a.`".self::TIMESHEET_DB_FIELD_START_DATE."` >= '{$this->getStartDate()}'";
		}
		if ($this->getEndDate() != null) {
			$selectConditions[] = "a.`".self::TIMESHEET_DB_FIELD_END_DATE."` <= '{$this->getEndDate()}'";
		}
		if ($this->getStatuses() != null) {
			$selectConditions[] = "a.`".self::TIMESHEET_DB_FIELD_STATUS."` IN('".implode("', '", $this->getStatuses())."')";
		}

		$query = $sql_builder->simpleSelect($selectTable, $selectFields, $selectConditions);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		if ($row = mysql_fetch_row($result)) {
			return $row[0];
		}

		return 0;
	}

	/**
	 * Fetch timesheets
	 *
	 * If any atributes are set records will searched against them
	 *
	 * @return Timesheet[] array of timesheets
	 */
	public function fetchTimesheets($current=false) {
		$sql_builder = new SQLQBuilder();

		if ($current) {
			$this->_getNewDates();
		}

		$selectTable = self::TIMESHEET_DB_TABLE_TIMESHEET." a ";

		$selectFields[0] = "a.`".self::TIMESHEET_DB_FIELD_TIMESHEET_ID."`";
		$selectFields[1] = "a.`".self::TIMESHEET_DB_FIELD_EMPLOYEE_ID."`";
		$selectFields[2] = "a.`".self::TIMESHEET_DB_FIELD_TIMESHEET_PERIOD_ID."`";
		$selectFields[3] = "a.`".self::TIMESHEET_DB_FIELD_START_DATE."`";
		$selectFields[4] = "a.`".self::TIMESHEET_DB_FIELD_END_DATE."`";
		$selectFields[5] = "a.`".self::TIMESHEET_DB_FIELD_STATUS."`";
		$selectFields[6] = "a.`".self::TIMESHEET_DB_FIELD_COMMENT."`";

        $selectConditions = null;

		if ($this->getTimesheetId() != null) {
			$selectConditions[] = "a.`".self::TIMESHEET_DB_FIELD_TIMESHEET_ID."` = {$this->getTimesheetId()}";
		}
		if ($this->getEmployeeId() != null) {
			$selectConditions[] = "a.`".self::TIMESHEET_DB_FIELD_EMPLOYEE_ID."` = {$this->getEmployeeId()}";
		}
		if ($this->getTimesheetPeriodId() != null) {
			$selectConditions[] = "a.`".self::TIMESHEET_DB_FIELD_TIMESHEET_PERIOD_ID."` = {$this->getTimesheetPeriodId()}";
		}
		if ($this->getStartDate() != null) {
			$selectConditions[] = "a.`".self::TIMESHEET_DB_FIELD_START_DATE."` = '{$this->getStartDate()}'";
		}
		if ($this->getEndDate() != null) {
			$selectConditions[] = "a.`".self::TIMESHEET_DB_FIELD_END_DATE."` = '{$this->getEndDate()}'";
		}
		if ($this->getStatuses() != null) {
			$selectConditions[] = "a.`".self::TIMESHEET_DB_FIELD_STATUS."` IN('".implode("', '", $this->getStatuses())."')";
		} else if ($this->getStatus() != null) {
			$selectConditions[] = "a.`".self::TIMESHEET_DB_FIELD_STATUS."` = '{$this->getStatus()}'";
		}

		$query = $sql_builder->simpleSelect($selectTable, $selectFields, $selectConditions, $selectFields[0], 'ASC');

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		$objArr = $this->_buildObjArr($result);

		return $objArr;
	}

	/**
	 * Build the object with fetched records
	 *
	 * @access private
	 * @return Timesheet[] array of timesheets
	 */
	private function _buildObjArr($result) {
		$objArr = null;

		while ($row = mysql_fetch_assoc($result)) {
			$tmpTimeArr = new Timesheet();

			$tmpTimeArr->setTimesheetId($row[self::TIMESHEET_DB_FIELD_TIMESHEET_ID]);
			$tmpTimeArr->setEmployeeId($row[self::TIMESHEET_DB_FIELD_EMPLOYEE_ID]);
			$tmpTimeArr->setTimesheetPeriodId($row[self::TIMESHEET_DB_FIELD_TIMESHEET_PERIOD_ID]);
			$tmpTimeArr->setStartDate(date('Y-m-d', strtotime($row[self::TIMESHEET_DB_FIELD_START_DATE])));
			$tmpTimeArr->setEndDate(date('Y-m-d', strtotime($row[self::TIMESHEET_DB_FIELD_END_DATE])));
			$tmpTimeArr->setStatus($row[self::TIMESHEET_DB_FIELD_STATUS]);
			$tmpTimeArr->setComment($row[self::TIMESHEET_DB_FIELD_COMMENT]);

			$objArr[] = $tmpTimeArr;
		}

		return $objArr;
	}
}
?>
