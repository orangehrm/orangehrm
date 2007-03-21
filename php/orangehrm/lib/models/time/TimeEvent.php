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
			$selectConditions[] = "a.`time_event_id` = {$this->getTimeEventId()}";
		}
		if ($this->getProjectId() != null) {
			$selectConditions[] = "a.`project_id` = {$this->getProjectId()}";
		}
		if ($this->getEmployeeId() != null) {
			$selectConditions[] = "a.`employee_id` = {$this->getEmployeeId()}";
		}
		if ($this->getTimesheetId() != null) {
			$selectConditions[] = "a.`timesheet_id` = {$this->getTimesheetId()}";
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
	 * @access protected
	 */
	protected function _buildObjArr($result) {
		$objArr = null;

		while ($row = mysql_fetch_assoc($result)) {
			$tmpEventArr = new TimeEvent();

			$tmpEventArr->setTimeEventId($row[self::TIME_EVENT_DB_FIELD_TIME_EVENT_ID]);
			$tmpEventArr->setProjectId($row[self::TIME_EVENT_DB_FIELD_PROJECT_ID]);
			$tmpEventArr->setEmployeeId($row[self::TIME_EVENT_DB_FIELD_EMPLOYEE_ID]);
			$tmpEventArr->setTimesheetId($row[self::TIME_EVENT_DB_FIELD_TIMESHEET_ID]);
			$tmpEventArr->setStartTime($row[self::TIME_EVENT_DB_FIELD_START_TIME]);
			$tmpEventArr->setEndTime($row[self::TIME_EVENT_DB_FIELD_END_TIME]);
			$tmpEventArr->setReportedDate(date('Y-m-d', strtotime($row[self::TIME_EVENT_DB_FIELD_REPORTED_DATE])));
			$tmpEventArr->setDuration($row[self::TIME_EVENT_DB_FIELD_DURATION]);
			$tmpEventArr->setDescription($row[self::TIME_EVENT_DB_FIELD_DESCRIPTION]);

			$objArr[] = $tmpEventArr;
		}

		return $objArr;
	}
}

?>
