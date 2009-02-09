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
require_once ROOT_PATH . '/lib/common/UniqueIDGenerator.php';

class AttendanceRecord {

	const DB_TABLE = "hs_hr_attendance";
	const DB_FIELD_ATTENDANCE_ID = "attendance_id";
	const DB_FIELD_EMPLOYEE_ID = "employee_id";
	const DB_FIELD_PUNCHIN_TIME = "punchin_time";
	const DB_FIELD_PUNCHOUT_TIME = "punchout_time";
	const DB_FIELD_IN_NOTE = "in_note";
	const DB_FIELD_OUT_NOTE = "out_note";
	const DB_FIELD_STATUS = "status";
	const STATUS_ACTIVE = 1;
	const STATUS_DELETED = 0;

	private $attendanceId;
	private $employeeId;
	private $inDate;
	private $inTime;
	private $outDate;
	private $outTime;
	private $inNote;
	private $outNote;
	private $status; // Whether active or deleted (0 or 1)

	public function setAttendanceId($attendanceId) {
	    $this->attendanceId = $attendanceId;
	}

	public function getAttendanceId() {
	    return $this->attendanceId;
	}

	public function setEmployeeId($employeeId) {
	    $this->employeeId = $employeeId;
	}

	public function getEmployeeId() {
	    return $this->employeeId;
	}

	public function setInDate($inDate) {
	    $this->inDate = $inDate;
	}

	public function getInDate() {
	    return $this->inDate;
	}

	public function setInTime($inTime) {
	    $this->inTime = $inTime;
	}

	public function getInTime() {
	    return $this->inTime;
	}

	public function setOutDate($outDate) {
	    $this->outDate = $outDate;
	}

	public function getOutDate() {
	    return $this->outDate;
	}

	public function setOutTime($outTime) {
	    $this->outTime = $outTime;
	}

	public function getOutTime() {
	    return $this->outTime;
	}

	public function setInNote($inNote) {
	    $this->inNote = $inNote;
	}

	public function getInNote() {
	    return $this->inNote;
	}

	public function setOutNote($outNote) {
	    $this->outNote = $outNote;
	}

	public function getOutNote() {
	    return $this->outNote;
	}

	public function setStatus($status) {
	    $this->status = $status;
	}

	public function getStatus() {
	    return $this->status;
	}

	/**
	 *
	 */

	public function addRecord() {

		if (!isset($this->employeeId) || !isset($this->inDate) || !isset($this->inTime)) {
			throw new AttendanceRecordException('Adding record: Required values are missing',
												AttendanceRecordException::ADDING_REQUIRED_VALUES_MISSING);
		}

		$insertTable = "`".self::DB_TABLE."`";

		$insertFields[] = "`".self::DB_FIELD_ATTENDANCE_ID."`";
		$insertValues[] = UniqueIDGenerator::getInstance()->getNextID(self::DB_TABLE, self::DB_FIELD_ATTENDANCE_ID);

		$insertFields[] = "`".self::DB_FIELD_EMPLOYEE_ID."`";
		$insertValues[] = "'{$this->employeeId}'";

		$insertFields[] = "`".self::DB_FIELD_PUNCHIN_TIME."`";
		$insertValues[] = "'{$this->inDate} {$this->inTime}'";

		if (isset($this->inNote)) {
			$insertFields[] = "`".self::DB_FIELD_IN_NOTE."`";
			$insertValues[] = "'{$this->inNote}'";
		}

		$insertFields[] = "`".self::DB_FIELD_STATUS."`";
		$insertValues[] = "'".self::STATUS_ACTIVE."'";

		$sqlBuilder = new SQLQBuilder();
		$query = $sqlBuilder->simpleInsert($insertTable, $insertValues, $insertFields);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($query);

		if ($result) {
		    return true;
		} else {
		    return false;
		}

	}

	/**
	 *
	 */

	public function updateRecord() {

		if (!isset($this->attendanceId)) {
			throw new AttendanceRecordException('Attendance ID is not set',
												AttendanceRecordException::ERROR_ID_NOT_SET);
		}

		$updateTable = "`".self::DB_TABLE."`";

		if (isset($this->inDate) && isset($this->inTime)) {
		    $updateFields[] = "`".self::DB_FIELD_PUNCHIN_TIME."`";
		    $updateValues[] = "{$this->inDate} {$this->inTime}";
		}

		if (isset($this->outDate) && isset($this->outTime)) {
		    $updateFields[] = "`".self::DB_FIELD_PUNCHOUT_TIME."`";
		    $updateValues[] = "{$this->outDate} {$this->outTime}";
		}

		if (isset($this->inNote)) {
		    $updateFields[] = "`".self::DB_FIELD_IN_NOTE."`";
		    $updateValues[] = $this->inNote;
		}

		if (isset($this->outNote)) {
		    $updateFields[] = "`".self::DB_FIELD_OUT_NOTE."`";
		    $updateValues[] = $this->outNote;
		}

		if (isset($this->status)) {
		    $updateFields[] = "`".self::DB_FIELD_STATUS."`";
		    $updateValues[] = $this->status;
		}

		$updateConditions[] = "`".self::DB_FIELD_ATTENDANCE_ID."` = {$this->attendanceId}";

		$sqlBuilder = new SQLQBuilder();
		$query = $sqlBuilder->simpleUpdate($updateTable, $updateFields, $updateValues, $updateConditions);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($query);

		if ($result) {
		    return true;
		} else {
		    return false;
		}

	}

	/**
	 *
	 */

	public function fetchRecords($employeeId, $from=null, $to=null, $status=null, $orderBy=null, $order=null, $limit=null) {

		$selectTable = "`".self::DB_TABLE."`";

		$selectFields[] = "`".self::DB_FIELD_ATTENDANCE_ID."`";
		$selectFields[] = "`".self::DB_FIELD_EMPLOYEE_ID."`";
		$selectFields[] = "`".self::DB_FIELD_PUNCHIN_TIME."`";
		$selectFields[] = "`".self::DB_FIELD_PUNCHOUT_TIME."`";
		$selectFields[] = "`".self::DB_FIELD_IN_NOTE."`";
		$selectFields[] = "`".self::DB_FIELD_OUT_NOTE."`";
		$selectFields[] = "`".self::DB_FIELD_STATUS."`";

		$selectConditions[] = "`".self::DB_FIELD_EMPLOYEE_ID."` = '$employeeId'";

		if ($from != null) {
			$selectConditions[] = "`".self::DB_FIELD_PUNCHIN_TIME."` >= '$from'";
		}

		if ($to != null) {
			$selectConditions[] = "`".self::DB_FIELD_PUNCHIN_TIME."` <= '$to'"; // PUNCHIN is used since it is allowed PUNCHOUT to be out of upper limit
		} else {
			$selectConditions[] = "`".self::DB_FIELD_PUNCHOUT_TIME."` IS NULL";
		}

		if ($status != null) {
			$selectConditions[] = "`".self::DB_FIELD_STATUS."` = '$status'";
		}

		$sqlBuilder = new SQLQBuilder();
		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions, $orderBy, $order, $limit);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($query);

		if (mysql_num_rows($result) > 0) {
			return $this->_buildRecordObjects($result);
		} else {
			return null;
		}

	}

	private function _buildRecordObjects($result) {

		while ($row = mysql_fetch_array($result)) {

			$attendanceObj = new AttendanceRecord();

			$attendanceObj->setAttendanceId($row['attendance_id']);
			$attendanceObj->setEmployeeId($row['employee_id']);

			/* $row['punchin_time'] comes in '0000-00-00 00:00:00' format.
			 * We want date in '0000-00-00' format and time in '00:00' format.
			 */
			$tmpArr = explode(' ', $row['punchin_time']);
			$attendanceObj->setInDate($tmpArr[0]);
			$attendanceObj->setInTime(substr($tmpArr[1], 0, 5));

			if ($row['punchout_time'] != null) {
				$tmpArr = explode(' ', $row['punchout_time']);
				$attendanceObj->setOutDate($tmpArr[0]);
				$attendanceObj->setOutTime(substr($tmpArr[1], 0, 5));
			}

			if ($row['in_note'] != null) {
				$attendanceObj->setInNote($row['in_note']);
			}

			if ($row['out_note'] != null) {
				$attendanceObj->setOutNote($row['out_note']);
			}

			$attendanceObj->setStatus($row['status']);

			$attendanceArr[] = $attendanceObj;

		}

		return $attendanceArr;

	}

}

class AttendanceRecordException extends Exception {

	const ERROR_ID_NO_SET = 1;
	const ADDING_REQUIRED_VALUES_MISSING = 2;

}