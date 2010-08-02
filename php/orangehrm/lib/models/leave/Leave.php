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

require_once ROOT_PATH . '/lib/models/leave/LeaveType.php';
require_once ROOT_PATH . '/lib/models/leave/Holidays.php';
require_once ROOT_PATH . '/lib/models/leave/Weekends.php';

require_once ROOT_PATH . '/lib/models/time/Workshift.php';

require_once ROOT_PATH . '/lib/common/UniqueIDGenerator.php';

/**
 * Leave Class
 *
 * This class handles taking/request/approve of leave, and list leaves
 *
 */
class Leave {

	/**
	 * Leave Status Constants
	 *
	 */
    const LEAVE_LENGTH_FULL_DAY = 8;
    const LEAVE_LENGTH_HALF_DAY_MORNING = -4;
    const LEAVE_LENGTH_HALF_DAY_AFTERNOON = 4;
    const LEAVE_LENGTH_HALF_DAY = 4;

    const LEAVE_STATUS_LEAVE_REJECTED = -1;
    const LEAVE_STATUS_LEAVE_CANCELLED = 0;
    const LEAVE_STATUS_LEAVE_PENDING_APPROVAL = 1;
    const LEAVE_STATUS_LEAVE_APPROVED = 2;
    const LEAVE_STATUS_LEAVE_TAKEN = 3;
    const LEAVE_STATUS_LEAVE_WEEKEND = 4;
    const LEAVE_STATUS_LEAVE_HOLIDAY = 5;

    public $statusLeaveRejected = -1;
    public $statusLeaveCancelled = 0;
    public $statusLeavePendingApproval = 1;
    public $statusLeaveApproved = 2;
    public $statusLeaveTaken = 3;
    public $statusLeaveWeekend = 4;
    public $statusLeaveHoliday = 5;

    /**
     *  Leave Length Constants
     *
     */
    public $lengthFullDay = 8;
    public $lengthHalfDayMorning = -4;
    public $lengthHalfDayAfternoon = 4;

	/**
	 *	Class Attributes
	 *
	 */
	private $leaveId;
	private $leaveRequestId;
	private $employeeId;
	private $leaveTypeId;
	private $leaveTypeName;
	private $dateApplied;
	private $leaveDate;
	private $leaveLengthHours;
	private $leaveLengthDays;
	private $leaveStatus;
	private $leaveComments;
	private $employeeName;
	private $startTime;
	private $endTime;

	protected $weekends;

	/**
	 * Class Constructor
	 *
	 */
	public function __construct() {
		$weekendObj = new Weekends();
		$this->weekends = $weekendObj->fetchWeek();
	}

	/**
	 * Setter method followed by getter method for each
	 * attribute
	 *
	 */
	public function setLeaveId($leaveId) {
		$this->leaveId = $leaveId;
	}

	public function getLeaveId() {
		return $this->leaveId;
	}


	public function setLeaveRequestId($leaveId) {
		$this->leaveRequestId = $leaveId;
	}

	public function getLeaveRequestId() {
		return $this->leaveRequestId;
	}

	public function setEmployeeId($employeeId) {
		$this->employeeId = $employeeId;
	}

	public function getEmployeeId() {
		return $this->employeeId;
	}

	public function setLeaveTypeId($leaveTypeId) {
		$this->leaveTypeId = $leaveTypeId;
	}

	public function getLeaveTypeId() {
		return $this->leaveTypeId;
	}

	public function setLeaveTypeName($leaveTypeName) {
		$this->leaveTypeName = $leaveTypeName;
	}

	public function getLeaveTypeName() {
		return $this->leaveTypeName;
	}

	public function setDateApplied($dateApplied) {
		$this->dateApplied = $dateApplied;
	}

	public function getDateApplied() {
		return $this->dateApplied;
	}

	public function setLeaveDate($leaveDate) {
		$this->leaveDate = $leaveDate;
	}

	public function getLeaveDate() {
		return $this->leaveDate;
	}

	public function setLeaveLengthHours($leaveLengthHours) {
		$this->leaveLengthHours = $leaveLengthHours;
	}

	public function getLeaveLengthHours() {
		return $this->leaveLengthHours;
	}

	public function setLeaveLengthDays($leaveLengthDays) {
		$this->leaveLengthDays = $leaveLengthDays;
	}

	public function getLeaveLengthDays() {
		return $this->leaveLengthDays;
	}

	public function setLeaveStatus($leaveStatus) {
		$this->leaveStatus = $leaveStatus;
	}

	public function getLeaveStatus() {
		return $this->leaveStatus;
	}

	public function setLeaveComments($leaveComments) {
		$this->leaveComments = $leaveComments;
	}

	public function getLeaveComments() {
		return $this->leaveComments;
	}

	public function setStartTime($startTime) {
		$this->startTime = $startTime;
	}

	public function getStartTime() {
		return $this->startTime;
	}

	public function setEndTime($endTime) {
		$this->endTime = $endTime;
	}

	public function getEndTime() {
		return $this->endTime;
	}

	public function setEmployeeName($employeeName) {
		$this->employeeName = $employeeName;
	}

	public function  getEmployeeName() {
		return $this->employeeName;
	}

	/**
	 * Retrieves leave taken for supervisors and
	 * HRAdmin
	 *
	 * @param String $year, String $employeeId
	 * @return Leave[][] $leaveArr A 2D array of the leaves
	 */
	public function retrieveTakenLeave($year, $employeeId) {

		$this->setEmployeeId($employeeId);

		$sqlBuilder = new SQLQBuilder();

		$arrFields[0] = 'a.`leave_date`';
		$arrFields[1] = 'a.`leave_status`';
		$arrFields[2] = 'a.`leave_length_hours`';
		$arrFields[3] = 'a.`leave_length_days`';
		$arrFields[4] = 'a.`leave_comments`';
		$arrFields[5] = 'a.`leave_id`';
		$arrFields[6] = 'd.`emp_firstname`';
		$arrFields[7] = 'd.`emp_lastname`';
		$arrFields[8] = 'a.`employee_id`';
		$arrFields[9] = 'b.`leave_type_name` as leave_type_name';
		$arrFields[10] = 'a.`start_time`';
		$arrFields[11] = 'a.`end_time`';

		$arrTables[0] = "`hs_hr_leave` a";
		$arrTables[1] = "`hs_hr_employee` d";
		$arrTables[2] = "`hs_hr_leavetype` b";

		$joinConditions[1] = "a.`employee_id` = d.`emp_number`";
		$joinConditions[2] = "a.`leave_type_id` = b.`leave_type_id`";

		$selectConditions[1] = "a.`employee_id` = '".$employeeId."'";
		$selectConditions[2] = "a.`leave_status` = ".$this->statusLeaveTaken;
		$selectConditions[3] = "a.`leave_date` >= '".$year."-01-01'";
		$selectConditions[4] = "a.`leave_date` <= '".$year."-12-31'";

		$query = $sqlBuilder->selectFromMultipleTable($arrFields, $arrTables, $joinConditions, $selectConditions);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);

		$leaveArr = $this->_buildObjArr($result, true);

		return $leaveArr;
	}

	public function retrieveLeave($requestId) {

		$this->setLeaveRequestId($requestId);

		$sqlBuilder = new SQLQBuilder();

		$arrFields[0] = 'a.`leave_date` as leave_date';
		$arrFields[1] = 'a.`leave_status` as leave_status';
		$arrFields[2] = 'a.`leave_length_hours` as leave_length_hours';
		$arrFields[3] = 'a.`leave_length_days` as leave_length_days';
		$arrFields[4] = 'a.`leave_comments` as leave_comments';
		$arrFields[5] = 'a.`leave_id` as leave_id';
		$arrFields[6] = 'd.`leave_type_name` as leave_type_name';
		$arrFields[7] = 'c.`emp_firstname` as emp_firstname';
		$arrFields[8] = 'c.`emp_lastname` as emp_lastname';
		$arrFields[9] = 'a.`employee_id` as employee_id';
		$arrFields[10] = 'a.`leave_request_id` as leave_request_id';
		$arrFields[11] = 'a.`start_time` as start_time';
		$arrFields[12] = 'a.`end_time` as end_time';

		$arrTables[0] = "`hs_hr_leave` a";
		$arrTables[1] = "`hs_hr_leave_requests` b";
		$arrTables[2] = "`hs_hr_employee` c";
		$arrTables[3] = "`hs_hr_leavetype` d";

		$selectConditions[1] = "a.`leave_request_id` = '".$requestId."'";

		$joinConditions[1] = "a.`leave_request_id` = b.`leave_request_id`";
		$joinConditions[2] = "a.`employee_id` = c.`emp_number`";
		$joinConditions[3] = "b.`leave_type_id` = d.`leave_type_id`";

		$query = $sqlBuilder->selectFromMultipleTable($arrFields, $arrTables, $joinConditions, $selectConditions);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		$leaveArr = $this->_buildObjArr($result, true);

		return $leaveArr;
	}

	/**
	 *	Retrieves Leave Details of all leave that have been applied for but
	 *	not yet taken of the employee.
	 *
	 * @return Leave[][] $leaveArr A 2D array of the leaves
	 */
	public function retrieveLeaveEmployee($employeeId) {

		$sqlBuilder = new SQLQBuilder();

		$arrFields[0] = '`leave_date`';
		$arrFields[1] = '`leave_status`';
		$arrFields[2] = '`leave_length_hours`';
		$arrFields[3] = '`leave_length_days`';
		$arrFields[4] = '`leave_comments`';
		$arrFields[5] = '`leave_id`';
		$arrFields[6] = '`start_time`';
		$arrFields[7] = '`end_time`';

		$arrTable = "`hs_hr_leave`";

		$selectConditions[1] = "`employee_id` = '".$employeeId."'";
		$selectConditions[2] = "`leave_date` >= '".date('Y')."-01-01'";

		$query = $sqlBuilder->simpleSelect($arrTable, $arrFields, $selectConditions, $arrFields[0], 'ASC');

		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);

		$leaveArr = $this->_buildObjArr($result);

		return $leaveArr;
	}

	public function retrieveIndividualLeave($leaveId) {
		$sqlBuilder = new SQLQBuilder();

		$arrFields[0] = '`leave_date`';
		$arrFields[1] = '`leave_status`';
		$arrFields[2] = '`leave_length_hours`';
		$arrFields[3] = '`leave_length_days`';
		$arrFields[4] = '`leave_comments`';
		$arrFields[5] = '`leave_id`';
		$arrFields[6] = '`start_time`';
		$arrFields[7] = '`end_time`';
		$arrFields[8] = '`leave_request_id`';
		$arrFields[9] = 'a.`leave_type_id`';
		$arrFields[10] = '`employee_id`';
		$arrFields[11] = 'b.`leave_type_name`';

		$arrTables[0] = "`hs_hr_leave` a";
		$arrTables[1] = "`hs_hr_leavetype` b";

		$joinConditions[1] = "a.`leave_type_id` = b.`leave_type_id`";

		$selectConditions[1] = "`leave_id` = '".$leaveId."'";

		$query = $sqlBuilder->selectFromMultipleTable($arrFields, $arrTables, $joinConditions, $selectConditions);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);

		$leaveArr = $this->_buildObjArr($result);

		return $leaveArr;
	}

	/**
	 * Retrieves leave for the given employee between the given two dates.
	 * If the given dates are the same and startTime and endTime are given
	 * looks for leave on that date between the given times.
	 *
	 *
	 */
	public function retrieveDuplicateLeave($employeeNum, $fromDate, $toDate) {
		$sqlBuilder = new SQLQBuilder();

		$arrFields[0] = 'a.`leave_id`';
		$arrFields[1] = 'a.`leave_date`';
		$arrFields[2] = 'a.`leave_length_hours`';
		$arrFields[3] = 'a.`leave_length_days`';
		$arrFields[4] = 'a.`leave_status`';
		$arrFields[5] = 'a.`leave_comments`';
		$arrFields[6] = 'a.`leave_request_id`';
		$arrFields[7] = 'a.`leave_type_id`';
		$arrFields[8] = 'a.`employee_id`';
		$arrFields[9] = 'a.`start_time`';
		$arrFields[10] = 'a.`end_time`';
		$arrFields[11] = 'b.`leave_type_name`';
		$arrFields[12] = 'c.`emp_firstname`';
		$arrFields[13] = 'c.`emp_lastname`';

		$arrTable = "`hs_hr_leave`";

		$arrTables[0] = "`hs_hr_leave` a";
		$arrTables[1] = "`hs_hr_leavetype` b";
		$arrTables[2] = "`hs_hr_employee` c";

		$joinConditions[1] = "a.`leave_type_id` = b.`leave_type_id`";
		$joinConditions[2] = "a.`employee_id` = c.`emp_number`";

		$dbConnection = new DMLFunctions();
		$selectConditions[1] = "a.`employee_id` = '". mysql_real_escape_string($employeeNum) . "'";
		$selectConditions[2] = "a.`leave_date` >='". mysql_real_escape_string($fromDate) . "'";
		$selectConditions[3] = "a.`leave_date` <='". mysql_real_escape_string($toDate) . "'";
		$selectConditions[4] = "a.`leave_status` <>'". self::LEAVE_STATUS_LEAVE_CANCELLED . "'";
                $selectConditions[5] = "a.`leave_status` <>'". self::LEAVE_STATUS_LEAVE_REJECTED . "'";
                $selectConditions[6] = "a.`leave_status` <>'". self::LEAVE_STATUS_LEAVE_WEEKEND . "'";
                $selectConditions[7] = "a.`leave_status` <>'". self::LEAVE_STATUS_LEAVE_HOLIDAY . "'";

		$query = $sqlBuilder->selectFromMultipleTable($arrFields, $arrTables, $joinConditions, $selectConditions);
		$result = $dbConnection->executeQuery($query);
        
		$leaveArr = $this->_buildObjArr($result, true);

		return $leaveArr;
	}

	/**
	 * Add Leave record to for a employee.
	 *
	 * @access public
	 * @return void
	 */
	public function applyLeave() {
		$this->_addLeave();
	}

	public function cancelLeave($id = null, $comments = '') {
		return $this->changeLeaveStatus($id, $comments);
	}

	public function changeLeaveStatus($id = null, $comments = '') {
		if (isset($id)) {
			$this->setLeaveId($id);
		}

		if (isset($comment)) {
			$this->setLeaveComments($comments);
		}

		$leaveObjs = $this->retrieveIndividualLeave($this->leaveId);

		if (!isset($leaveObjs)) {
			return false;
		}
		$leave = $leaveObjs[0];

		$newStatus = $this->getLeaveStatus();

		/** Check if no change */
		if ($newStatus == $leave->getLeaveStatus() && $comments == $leave->getLeaveComments()) {
			return false;
		}

		$taken = ($leave->getLeaveStatus() == self::LEAVE_STATUS_LEAVE_TAKEN);

		$this->setLeaveStatus($newStatus);
		$res = $this->_changeLeaveStatus();

		if ($res && $taken) {
			$this->setLeaveTypeId($leave->getLeaveTypeId());
			$this->setLeaveDate($leave->getLeaveDate());
			$this->setLeaveLengthDays($leave->leaveLengthDays*-1);
			$this->setLeaveLengthHours($leave->leaveLengthHours*-1);
			$res = $this->storeLeaveTaken();
		}

		return $res;
	}

	/**
	 * Counts Leaves taken of particular Leave type
	 *
	 * @return int
	 * @param String LeaveTypeId, [int status]
	 *
	 */
	public function countLeave($leaveTypeId, $year=null, $status=null) {
		if ($year == null) {
			$year = date('Y');
		}

		if ($status == null) {
			$status = $this->statusLeaveTaken;
		}

		$totalLeaveLength = 0;

		$leaveLengths = array($this->lengthFullDay, $this->lengthHalfDayAfternoon, $this->lengthHalfDayMorning);

		$sqlBuilder = new SQLQBuilder();

		$arrFields[0] = 'SUM(ABS(`leave_length_days`))';

		$arrTable = "`hs_hr_leave`";

		$selectConditions[1] = "`employee_id` = '".$this->getEmployeeId()."'";
		$selectConditions[2] = "`leave_status` = ".$status;
		$selectConditions[3] = "`leave_type_id` = '".$leaveTypeId."'";
		$selectConditions[4] = "`leave_date` BETWEEN DATE('".$year."-01-01') AND DATE('".$year."-12-31')";

		$query = $sqlBuilder->simpleSelect($arrTable, $arrFields, $selectConditions);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		$count = mysql_fetch_row($result);

		$totalLeaveLength = $count[0];

		return $totalLeaveLength;
	}

	/**
	 * Checks whether the leave table is empty.
	 *
	 * @return bool
	 *
	 */
	public static function isLeaveTableEmpty() {

		$sqlBuilder = new SQLQBuilder();

		$arrFields[0] = 'count(*)';
		$arrTable = "`hs_hr_leave`";

		$query = $sqlBuilder->simpleSelect($arrTable, $arrFields);
		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);
		$count = mysql_fetch_row($result);

		if ($count[0] > 0) {
			return false;
		} else {
			return true;
		}
	}
	
	protected function _adjustLeaveLength() {
		$timeOff = $this->_timeOffLength($this->getLeaveDate());
		$shift = Leave::LEAVE_LENGTH_FULL_DAY;
		$workShift = Workshift::getWorkshiftForEmployee($this->getEmployeeId());

		if (isset($workShift)) {
			$shift = $workShift->getHoursPerDay();
		}

		$hours=$shift;
		$days=1;

		if ($this->getLeaveLengthHours() != null) {
			$hours = $this->getLeaveLengthHours() - ($timeOff * $shift);
			/* For handling leaves applied in half days: Begins
			 * This assumes that employee request the leave in available working time
			 * */
			if ($timeOff > 0 && $timeOff < 1) {
				if($hours <= 0) {
			    	$hours = $this->getLeaveLengthHours();
				} else {
				    $hours = ($timeOff * $shift);
				}
			}
			/* For handling leaves applied in half days: Ends */
			$days = round(($hours/$shift), 2);
		} else if ($this->getLeaveLengthDays() != null) {
			$hours = ($this->getLeaveLengthDays() - $timeOff) * $shift;
			$days = round(($hours/$shift), 2);
		}

		if (0 > $hours) {
			$hours=0;
		}
		if (0 > $days) {
			$days=0;
		}

		$this->setLeaveLengthHours($hours);
		$this->setLeaveLengthDays($days);

	}

	/**
	 * Adds Leave
	 *
	 * @access protected
	 */
    protected function _addLeave() {

		$this->leaveId = UniqueIDGenerator::getInstance()->getNextID('hs_hr_leave', 'leave_id');

		$this->_getLeaveTypeName();
		$this->setDateApplied(date('Y-m-d'));

		$this->_adjustLeaveLength();

		$insertFields[0] = '`leave_id`';
		$insertFields[1] = '`leave_date`';
		$insertFields[2] = '`leave_length_hours`';
		$insertFields[3] = '`leave_length_days`';
		$insertFields[4] = '`leave_status`';
		$insertFields[5] = '`leave_comments`';
		$insertFields[6] = '`leave_request_id`';
		$insertFields[7] = '`leave_type_id`';
		$insertFields[8] = '`employee_id`';

		$arrRecordsList[0] = $this->getLeaveId();
		$arrRecordsList[1] = "'". $this->getLeaveDate()."'";
		$arrRecordsList[2] = "'". $this->getLeaveLengthHours()."'";
		$arrRecordsList[3] = "'". $this->getLeaveLengthDays()."'";
		$hours = $this->getLeaveLengthHours();
		$days = $this->getLeaveLengthDays();
		if ($hours == 0 && $days == 0) {
		    $arrRecordsList[4] = self::LEAVE_STATUS_LEAVE_CANCELLED;
		} else {
		    $arrRecordsList[4] = $this->statusLeavePendingApproval;
		}
        $holidays = new Holidays();
        $weekends = new Weekends();
        if ($weekends->isWeekend($this->getLeaveDate())) {
        	$arrRecordsList[4] = self::LEAVE_STATUS_LEAVE_WEEKEND;
        } elseif ($holidays->isHoliday($this->getLeaveDate())) {
        	$arrRecordsList[4] = self::LEAVE_STATUS_LEAVE_HOLIDAY;
        }
		$arrRecordsList[5] = "'".$this->getLeaveComments()."'";
		$arrRecordsList[6] = "'". $this->getLeaveRequestId(). "'";
		$arrRecordsList[7] = "'".$this->getLeaveTypeId()."'";
		$arrRecordsList[8] = "'". $this->getEmployeeId() . "'";

		if (($this->getStartTime() != null) && ($this->getEndTime() != null)) {
			$insertFields[9] = '`start_time`';
			$arrRecordsList[9] = "'". $this->getStartTime() . "'";

			$insertFields[10] = '`end_time`';
			$arrRecordsList[10] = "'". $this->getEndTime() . "'";
		}

		$arrTable = "`hs_hr_leave`";

		$sqlBuilder = new SQLQBuilder();

		$query = $sqlBuilder->simpleInsert($arrTable, $arrRecordsList, $insertFields);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);
		return $result;
	}

	/**
	 * function _changeLeaveStatus, access is private, will not be documented
	 *
	 * @access private
	 */
	protected function _changeLeaveStatus() {

		$sqlBuilder = new SQLQBuilder();

		$table = "`hs_hr_leave`";

		$changeFields[0] = "`leave_status`";
		$changeFields[1] = "`leave_comments`";

		$changeValues[0] = $this->getLeaveStatus();
		$changeValues[1] = "'".$this->getLeaveComments()."'";

		$updateConditions[0] = "`leave_id` = ".$this->getLeaveId();

		$query = $sqlBuilder->simpleUpdate($table, $changeFields, $changeValues, $updateConditions);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		if (isset($result) && (mysql_affected_rows() > 0)) {
			return true;
		};

		return false;
	}

	/**
	 *
	 * function _getLeaveTypeName, access is private, will not be documented
	 *
	 * @access private
	 *
	 */
	protected function _getLeaveTypeName() {

		$sqlBuilder = new SQLQBuilder();
		$leave_Type  = new LeaveType();

		$selectTable = "`hs_hr_leavetype`";
		$selectFields[0] = '`leave_type_name`';
    	$updateConditions[1] = "`leave_type_id` = '".$this->getLeaveTypeId()."'";

    	$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $updateConditions, null, null, null);
		//echo $query;
		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);

		$row = mysql_fetch_row($result);

		$this->setLeaveTypeName($row[0]);
	}

	/**
	 * Calculates the time off for a particular date
	 *
	 * Return values:
	 * Eg: Returns 1 for full day off, 0.5 for half day off, 0 for no off time
	 *
	 * @param String $date
	 * @return integer $timeOff
	 */
	protected function _timeOffLength($date) {
		$timeOff = 0;

		if (isset($this->weekends[date('N', strtotime($date))-1])) {
			$timeOff = $this->weekends[date('N', strtotime($date))-1]->getLength();
		}

		$holidaysObj = new Holidays();

		$length = $holidaysObj->isHoliday($date);

		if ($length > $timeOff) {
			$timeOff = $length;
		}

		return $timeOff / 8;
	}

	/**
	 * Calculates required length of leave.
	 *
	 * @param integer $length - leave lenth
	 * @param integer $timeOff - time off for that day
	 * @return integer $reqiredLength - length of leave required.
	 */
	protected function _leaveLength($length, $timeOff) {
		$factor = 1;
		if ($length < 0) {
			$factor = -1;
		}

		$length = abs($length);
		if ($timeOff > $length) {
			return 0;
		}
		$requiredLength = $length-$timeOff;

		return $requiredLength*$factor;
	}

	/**
	 *
	 * function _buildObjArr, access is private, will not be documented
	 *
	 * @access protected
	 */
	protected function _buildObjArr($result, $supervisor=false) {

		$objArr = null;

		while ($row = mysql_fetch_assoc($result)) {

			$tmpLeaveArr = new Leave();

			$tmpLeaveArr->setLeaveDate($row['leave_date']);
			$tmpLeaveArr->setLeaveStatus($row['leave_status']);

			$leaveLengthHours = $row['leave_length_hours'];
			$leaveLengthDays = $row['leave_length_days'];

			$tmpLeaveArr->setLeaveLengthHours($leaveLengthHours);
			$tmpLeaveArr->setLeaveLengthDays($leaveLengthDays);
			$tmpLeaveArr->setLeaveComments($row['leave_comments']);
			$tmpLeaveArr->setLeaveId($row['leave_id']);

			if (isset($row['employee_id'])) {
				$tmpLeaveArr->setEmployeeId($row['employee_id']);
			}

			if (isset($row['leave_type_name'])) {
				$tmpLeaveArr->setLeaveTypeName($row['leave_type_name']);
			}

			if (isset($row['leave_type_id'])) {
				$tmpLeaveArr->setLeaveTypeId($row['leave_type_id']);
			}

			if (isset($row['leave_request_id'])) {
				$tmpLeaveArr->setLeaveRequestId($row['leave_request_id']);
			}

			if (!empty($row['start_time']) && !empty($row['start_time'])) {
				$tmpLeaveArr->setStartTime(date("H:i", strtotime($row['start_time'])));
				$tmpLeaveArr->setEndTime(date("H:i", strtotime($row['end_time'])));
			}

			if ($supervisor && isset($row['employee_id'])) {
				$tmpLeaveArr->setEmployeeName("{$row['emp_firstname']} {$row['emp_lastname']}");
			}

			$objArr[] = $tmpLeaveArr;
		}

		return $objArr;
	}

	/**
	 * Retrieve the years where there are any leave records
	 * returns at least current year
	 *
	 * @return String[]
	 * @access public
	 */
	public function getLeaveYears() {

		$sqlBuilder = new SQLQBuilder();

		$selectTable = "`hs_hr_leave`";

		$selectFields[] = "DISTINCT YEAR(`leave_date`) ";

		$selectConditions[] = "`leave_date` < '".date('Y')."-01-01'";

		$selectOrder = "ASC";

		$selectOrderBy = "`leave_date`";

		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions, $selectOrderBy, $selectOrder, 1);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);

		if ($row = mysql_fetch_row($result)) {
			$firstYears = $row[0];

			for ($i=$firstYears; $i<date('Y'); $i++) {
				$years[] = $i;
			}
		}

		$years[] = date('Y');

		$years[] = date('Y')+1;

		$years = array_unique($years);

		rsort($years);

		return $years;

	}

	/**
	 * Changes the leave status to taken if the date is before
	 * or on today
	 *
	 * @access public
	 */
	 public function takeLeave() {

		$sqlBuilder = new SQLQBuilder();

		$selectFields[0] = '`leave_date`';
		$selectFields[1] = '`leave_status`';
		$selectFields[2] = '`leave_length_hours`';
		$selectFields[3] = '`leave_length_days`';
		$selectFields[4] = '`leave_comments`';
		$selectFields[5] = '`leave_id`';
		$selectFields[6] = '`employee_id`';
		$selectFields[7] = '`leave_type_id`';

		$selectTable = '`hs_hr_leave`';

		$selectConditions[] = "`leave_status` = ".$this->statusLeaveApproved;
		$selectConditions[] = "`leave_date` <= NOW()";

		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);

		if (isset($result) && !empty($result)) {
			if (mysql_num_rows($result) > 0) {

				$leaveObjs = $this->_buildObjArr($result);

				foreach ($leaveObjs as $leaveObj) {
					$leaveObj->setLeaveStatus(self::LEAVE_STATUS_LEAVE_TAKEN);
					$leaveObj->changeLeaveToTaken();
					$leaveObj->storeLeaveTaken();
				}

				return true;
			}
		}

		return false;
	 }

	 /**
	  * This is the workhorse function for takeLeave() function.
	  * This needs to be publicly accessible, still this is expected
	  * to be called from takeLeave()
	  *
	  * @return boolean
	  */
	 public function changeLeaveToTaken() {
	 	$sqlBuilder = new SQLQBuilder();

		$table = "`hs_hr_leave`";

		$changeFields[0] = "`leave_status`";
		$changeFields[1] = "`leave_length_hours`";

		$changeValues[0] = $this->getLeaveStatus();
		$changeValues[1] = "'".$this->getLeaveLengthHours()."'";

		$updateConditions[0] = "`leave_id` = ".$this->getLeaveId();

		$query = $sqlBuilder->simpleUpdate($table, $changeFields, $changeValues, $updateConditions);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		if (isset($result) && (mysql_affected_rows() > 0)) {
			return true;
		};

		return false;
	 }

	public function storeLeaveTaken() {

	 	$sqlBuilder = new SQLQBuilder();

	 	$updateTable = '`hs_hr_employee_leave_quota`';

	 	$updateFields[] = '`leave_taken`';

	 	$updateValues[] = "`leave_taken`+{$this->getLeaveLengthDays()}";

		$year = substr($this->getLeaveDate(), 0, 4); // To get the year from the date.
	 	$updateConditions[] = "`year` = {$year}";
	 	$updateConditions[] = "`leave_type_id` = '" . $this->getLeaveTypeId() . "'";
	 	$updateConditions[] = "`employee_id` = {$this->getEmployeeId()}";

		$query = $sqlBuilder->simpleUpdate($updateTable, $updateFields, $updateValues, $updateConditions, false);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		if (isset($result) && (mysql_affected_rows() > 0)) {
			return true;
		};

		return false;
	}

	/**
	 * This function will delete leave records for the given date. This will only for leave status other than 'taken'
	 * @param $date - string date for delete records
	 */
	public static function updateLeavesForDate($date, $length) {

		$sql_builder = new SQLQBuilder();

		$updateTable = "`hs_hr_leave`";

		$changeFields[] = "`leave_length_days`";
		$changeFields[] = "`leave_length_hours`";

		$changeValues[] = "IF((`leave_length_days` - ($length / " . self::LEAVE_LENGTH_FULL_DAY . ") < 0), 0,`leave_length_days` - ($length / " . self::LEAVE_LENGTH_FULL_DAY . "))";
		$changeValues[] = "IF((`leave_length_hours` - $length) < 0, 0, `leave_length_hours` - $length) ";

		$updateConditions[] = "`leave_date` = '" . $date . "'";
		$updateConditions[] = "`leave_status` <> '" . self::LEAVE_STATUS_LEAVE_TAKEN . "'";

		$query = $sql_builder->simpleUpdate($updateTable, $changeFields, $changeValues, $updateConditions, false);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);
	}
	
	public function adjustLeaveToWorkshift($duration, $empIdList) {
		
		$empIdList = implode(',', $empIdList);
		
		$query = "UPDATE `hs_hr_leave` SET `leave_length_hours` = IF (`leave_length_hours` < $duration, `leave_length_hours`, $duration), ";
		$query .= "`leave_length_days` = IF (`leave_length_hours` < $duration, `leave_length_hours`/$duration, 1) ";
		$query .= "WHERE `leave_status` IN (1, 2) AND `employee_id` IN ($empIdList)";
	    
		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($query);
		
		if ($result) {
		    return true;
		} else {
		    return false;
		}	
	
	}
	
}

?>
