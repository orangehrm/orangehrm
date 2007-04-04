<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com
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

/**
 * Handles all function related to Time Event
 */
class TimeEvent {

	/**
	 * Class constants
	 */
	const TIME_EVENT_DB_TABLE_TIME_EVENT = "hs_hr_time_event";

	const TIME_EVENT_DB_FIELD_TIME_EVENT_ID = "time_event_id";
	const TIME_EVENT_DB_FIELD_PROJECT_ID = "project_id";
	const TIME_EVENT_DB_FIELD_EMPLOYEE_ID = "employee_id";
	const TIME_EVENT_DB_FIELD_TIMESHEET_ID = "timesheet_id";
	const TIME_EVENT_DB_FIELD_START_TIME = "start_time";
	const TIME_EVENT_DB_FIELD_END_TIME = "end_time";
	const TIME_EVENT_DB_FIELD_REPORTED_DATE = "reported_date";
	const TIME_EVENT_DB_FIELD_DURATION = "duration";
	const TIME_EVENT_DB_FIELD_DESCRIPTION = "description";

	/**
	 * Class atributes
	 */
	private $timeEventId;
	private $projectId;
	private $employeeId;
	private $timesheetId;
	private $startTime;
	private $endTime;
	private $reportedDate;
	private $duration;
	private $description;

	private $selectFields;

	/**
	 * Class atribute getters and setters
	 */
	public function setTimeEventId($timeEventId) {
		$this->timeEventId=$timeEventId;
	}

	public function getTimeEventId() {
		return $this->timeEventId;
	}

	public function setProjectId($projectId) {
		$this->projectId=$projectId;
	}

	public function getProjectId() {
		return $this->projectId;
	}

	public function setEmployeeId($employeeId) {
		$this->employeeId=$employeeId;
	}

	public function getEmployeeId() {
		return $this->employeeId;
	}

	public function setTimesheetId($timesheetId) {
		$this->timesheetId=$timesheetId;
	}

	public function getTimesheetId() {
		return $this->timesheetId;
	}

	public function setStartTime($startTime) {
		$this->startTime=$startTime;
	}

	public function getStartTime() {
		return $this->startTime;
	}

	public function setEndTime($endTime) {
		$this->endTime=$endTime;
	}

	public function getEndTime() {
		return $this->endTime;
	}

	public function setReportedDate($reportedDate) {
		$this->reportedDate=$reportedDate;
	}

	public function getReportedDate() {
		return $this->reportedDate;
	}

	public function setDuration($duration) {
		$this->duration=$duration;
	}

	public function getDuration() {
		return $this->duration;
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

	public function __distruct() {
		//nothing to do
	}

	/**
	 * Compute the new Time event id
	 */
	private function _getNewTimeEventId() {
		$sql_builder = new SQLQBuilder();

		$selectTable = self::TIME_EVENT_DB_TABLE_TIME_EVENT;
		$selectFields[0] = self::TIME_EVENT_DB_FIELD_TIME_EVENT_ID;
		$selectOrder = "DESC";
		$selectLimit = 1;
		$sortingField = self::TIME_EVENT_DB_FIELD_TIME_EVENT_ID;

		$query = $sql_builder->simpleSelect($selectTable, $selectFields, null, $sortingField, $selectOrder, $selectLimit);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		$row = mysql_fetch_row($result);

		$this->setTimeEventId($row[0]+1);
	}

	/**
	 * Add new time event
	 *
	 * Time event id will be over written
	 */
	public function addTimeEvent() {
		$this->_getNewTimeEventId();

		$sqlBuilder = new SQLQBuilder();

		$insertTable = "`".self::TIME_EVENT_DB_TABLE_TIME_EVENT."`";

		$insertFields[0] = "`".self::TIME_EVENT_DB_FIELD_TIME_EVENT_ID."`";
		$insertFields[1] = "`".self::TIME_EVENT_DB_FIELD_PROJECT_ID."`";
		$insertFields[2] = "`".self::TIME_EVENT_DB_FIELD_EMPLOYEE_ID."`";
		$insertFields[3] = "`".self::TIME_EVENT_DB_FIELD_TIMESHEET_ID."`";

		$insertValues[0] = $this->getTimeEventId();
		$insertValues[1] = $this->getProjectId();
		$insertValues[2] = $this->getEmployeeId();
		$insertValues[3] = $this->getTimesheetId();

		if ($this->getStartTime() != null) {
			$insertFields[] = "`".self::TIME_EVENT_DB_FIELD_START_TIME."`";
			$insertValues[] = "'".$this->getStartTime()."'";
		}

		if ($this->getEndTime() != null) {
			$insertFields[] = "`".self::TIME_EVENT_DB_FIELD_END_TIME."`";
			$insertValues[] = "'".$this->getEndTime()."'";
		}

		if ($this->getReportedDate() != null) {
			$insertFields[] = "`".self::TIME_EVENT_DB_FIELD_REPORTED_DATE."`";
			$insertValues[] = "'".$this->getReportedDate()."'";
		}

		if ($this->getDuration() != null) {
			$insertFields[] = "`".self::TIME_EVENT_DB_FIELD_DURATION."`";
			$insertValues[] = $this->getDuration();
		}

		if ($this->getDescription() != null) {
			$insertFields[] = "`".self::TIME_EVENT_DB_FIELD_DESCRIPTION."`";
			$insertValues[] = "'".$this->getDescription()."'";
		}

		$query = $sqlBuilder->simpleInsert($insertTable, $insertValues, $insertFields);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection -> executeQuery($query);

		if ($result && (mysql_affected_rows() > 0)) {
			return true;
		}

		return false;
	}

	/**
	 * Editing time event
	 *
	 * All except time event id is editable
	 */
	public function editTimeEvent() {
		$sqlBuilder = new SQLQBuilder();

		$updateTable = "`".self::TIME_EVENT_DB_TABLE_TIME_EVENT."`";

		if ($this->getProjectId() != null) {
			$updateFields[] = "`".self::TIME_EVENT_DB_FIELD_PROJECT_ID."`";
			$updateValues[] = $this->getProjectId();
		}

		if ($this->getEmployeeId() != null) {
			$updateFields[] = "`".self::TIME_EVENT_DB_FIELD_EMPLOYEE_ID."`";
			$updateValues[] = $this->getEmployeeId();
		}

		if ($this->getTimesheetId() != null) {
			$updateFields[] = "`".self::TIME_EVENT_DB_FIELD_TIMESHEET_ID."`";
			$updateValues[] = $this->getTimesheetId();
		}

		if ($this->getStartTime() != null) {
			$updateFields[] = "`".self::TIME_EVENT_DB_FIELD_START_TIME."`";
			$updateValues[] = "'".$this->getStartTime()."'";
		}

		if ($this->getEndTime() != null) {
			$updateFields[] = "`".self::TIME_EVENT_DB_FIELD_END_TIME."`";
			$updateValues[] = "'".$this->getEndTime()."'";
		}

		if ($this->getReportedDate() != null) {
			$updateFields[] = "`".self::TIME_EVENT_DB_FIELD_REPORTED_DATE."`";
			$updateValues[] = "'".$this->getReportedDate()."'";
		}

		if ($this->getDuration() != null) {
			$updateFields[] = "`".self::TIME_EVENT_DB_FIELD_DURATION."`";
			$updateValues[] = $this->getDuration();
		}

		if ($this->getDescription() != null) {
			$updateFields[] = "`".self::TIME_EVENT_DB_FIELD_DESCRIPTION."`";
			$updateValues[] = "'".$this->getDescription()."'";
		}

		$updateConditions[] = "`".self::TIME_EVENT_DB_FIELD_TIME_EVENT_ID."` = {$this->getTimeEventId()}";

		$query = $sqlBuilder->simpleUpdate($updateTable, $updateFields, $updateValues, $updateConditions);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection -> executeQuery($query);

		if ($result) {
			return true;
		}

		return false;
	}

	/**
	 * Fetch time event records and build objects
	 *
	 * If any atributes are set records will searched against them
	 *
	 * @return TimeEvent[] array of time events
	 */
	public function fetchTimeEvents() {
		$sqlBuilder = new SQLQBuilder();

		$selectTable = "`".self::TIME_EVENT_DB_TABLE_TIME_EVENT."` a ";

		$selectFields[0] = "a.`".self::TIME_EVENT_DB_FIELD_TIME_EVENT_ID."`";
		$selectFields[1] = "a.`".self::TIME_EVENT_DB_FIELD_PROJECT_ID."`";
		$selectFields[2] = "a.`".self::TIME_EVENT_DB_FIELD_EMPLOYEE_ID."`";
		$selectFields[3] = "a.`".self::TIME_EVENT_DB_FIELD_TIMESHEET_ID."`";
		$selectFields[4] = "a.`".self::TIME_EVENT_DB_FIELD_START_TIME."`";
		$selectFields[5] = "a.`".self::TIME_EVENT_DB_FIELD_END_TIME."`";
		$selectFields[6] = "a.`".self::TIME_EVENT_DB_FIELD_REPORTED_DATE."`";
		$selectFields[7] = "a.`".self::TIME_EVENT_DB_FIELD_DURATION."`";
		$selectFields[8] = "a.`".self::TIME_EVENT_DB_FIELD_DESCRIPTION."`";

		if ($this->getTimeEventId() != null) {
			$selectConditions[] = "a.`".self::TIME_EVENT_DB_FIELD_TIME_EVENT_ID."` = {$this->getTimeEventId()}";
		}
		if ($this->getProjectId() != null) {
			$selectConditions[] = "a.`".self::TIME_EVENT_DB_FIELD_PROJECT_ID."` = {$this->getProjectId()}";
		}
		if ($this->getEmployeeId() != null) {
			$selectConditions[] = "a.`".self::TIME_EVENT_DB_FIELD_EMPLOYEE_ID."` = {$this->getEmployeeId()}";
		}
		if ($this->getTimesheetId() != null) {
			$selectConditions[] = "a.`".self::TIME_EVENT_DB_FIELD_TIMESHEET_ID."` = {$this->getTimesheetId()}";
		}

		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions, $selectFields[0], 'ASC');

		$dbConnection = new DMLFunctions();
		$result = $dbConnection -> executeQuery($query);

		$eventArr = $this->_buildObjArr($result);

		return $eventArr;
	}

	/**
	 * Build the object with fetched records
	 *
	 * @access private
	 * @return TimeEvent[] array of time events
	 */
	private function _buildObjArr($result) {
		$objArr = null;

		while ($row = mysql_fetch_assoc($result)) {
			$tmpEventArr = new TimeEvent();

			$tmpEventArr->setTimeEventId($row[self::TIME_EVENT_DB_FIELD_TIME_EVENT_ID]);
			$tmpEventArr->setProjectId($row[self::TIME_EVENT_DB_FIELD_PROJECT_ID]);
			$tmpEventArr->setEmployeeId($row[self::TIME_EVENT_DB_FIELD_EMPLOYEE_ID]);
			$tmpEventArr->setTimesheetId($row[self::TIME_EVENT_DB_FIELD_TIMESHEET_ID]);
			$tmpEventArr->setStartTime(date('Y-m-d H:i', strtotime($row[self::TIME_EVENT_DB_FIELD_START_TIME])));
			$tmpEventArr->setEndTime(date('Y-m-d H:i', strtotime($row[self::TIME_EVENT_DB_FIELD_END_TIME])));
			$tmpEventArr->setReportedDate(date('Y-m-d', strtotime($row[self::TIME_EVENT_DB_FIELD_REPORTED_DATE])));
			$tmpEventArr->setDuration($row[self::TIME_EVENT_DB_FIELD_DURATION]);
			$tmpEventArr->setDescription($row[self::TIME_EVENT_DB_FIELD_DESCRIPTION]);

			$objArr[] = $tmpEventArr;
		}

		return $objArr;
	}
}

?>
