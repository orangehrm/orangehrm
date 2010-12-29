<?php
/**
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
 * @copyright 2006 OrangeHRM Inc., http://www.orangehrm.com
 */

//require_once ROOT_PATH . '/lib/models/leave/Leave.php';
//require_once ROOT_PATH . '/lib/models/leave/Holidays.php';
//require_once ROOT_PATH . '/lib/models/leave/Weekends.php';
//require_once ROOT_PATH . '/lib/common/UniqueIDGenerator.php';

class LeaveTakenRequests {

	private $leaveId;
	private $leaveDate;
	private $leaveYear;
	private $employeeName;
	private $noHours;
	private $leaveStatus = 3;
	private $leaveComments;
	private $leaveTypeId;
	private $leaveTypeName;
	private $employeeId;

	public function setLeaveId($leaveId) {
		$this->leaveId = $leaveId;
	}

	public function getLeaveId() {
		return $this->leaveId;
	}

	public function setLeaveDate($leaveDate) {
		$this->leaveDate = $leaveDate;
	}

	public function getLeaveDate() {
		return $this->leaveDate;
	}

	public function setLeaveYear($leaveYear) {
		$this->leaveYear = $leaveYear;
	}

	public function getLeaveYear() {
		return $this->leaveYear;
	}

	public function setEmployeeName ($employeeName) {
		$this->employeeName = $employeeName;
	}

	public function getEmployeeName () {
		return $this->employeeName;
	}

	public function setNoHours ($noHours) {
		$this->noHours = $noHours;
	}

	public function getNoHours() {
		return $this->noHours;
	}

	public function setLeaveStatus($leaveStatus) {
		$this->leaveStatus = $leaveStatus;
	}

	public function getLeaveStatus() {
		return $this->leaveStatus;
	}

	public function setLeaveComments ($leaveComments) {
		$this->leaveComments = $leaveComments;
	}

	public function getLeaveComments () {
		return $this->leaveComments;
	}

	public function setLeaveTypeId ($leaveTypeId) {
		$this->leaveTypeId = $leaveTypeId;
	}

	public function getLeaveTypeId() {
		return $this->leaveTypeId;
	}

	public function setLeaveTypeName ($leaveTypeName) {
		$this->leaveTypeName = $leaveTypeName;
	}

	public function getLeaveTypeName () {
		return $this->leaveTypeName;
	}

	public function setEmployeeId ($employeeId) {
		$this->employeeId = $employeeId;
	}

	public function getEmployeeId () {
		return $this->employeeId;
	}



/**
 * This retrives alreadyt taken leaves.
 */

	public function retriveLeaveTaken() {

		$sqlBuilder = new SQLQBuilder();

		$arrFields[0] = 'a.`leave_id`';
		$arrFields[1] = 'a.`leave_date`';
		$arrFields[2] = 'b.`emp_firstname`';
		$arrFields[3] = 'b.`emp_lastname`';
		$arrFields[4] = 'a.`leave_length_hours`';
		$arrFields[5] = 'a.`leave_comments`';
		$arrFields[6] = 'a.`leave_type_id`';
		$arrFields[7] = 'c.`leave_type_name`';
		$arrFields[8] = 'a.`employee_id`';

		$arrTables[0] = "`hs_hr_leave` a";
		$arrTables[1] = "`hs_hr_employee` b";
		$arrTables[2] = "`hs_hr_leavetype` c";

		$joinConditions[1] = "a.`employee_id` = b.`emp_number`";
		$joinConditions[2] = "a.`leave_type_id` = c.`leave_type_id`";

		$selectConditions[1] = "a.`leave_status` = '3'";

		$query = $sqlBuilder->selectFromMultipleTable($arrFields, $arrTables, $joinConditions, $selectConditions);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection -> executeQuery($query);

		$leaveArr = $this->_buildObjArr($result);

		return $leaveArr;
	}

	public function cancelLeaveTaken($obj) {

		$sqlBuilder = new SQLQBuilder();

		$updateTable = "`hs_hr_leave`";

		$updateFileds[0] = "`leave_status`";
		$updateFileds[1] = "`leave_comments`";

		$updateValues[0] = "'" . $obj->getLeaveStatus() . "'";
		$updateValues[1] = "'" . $obj->getLeaveComments() . "'";

		$updateConditions[0] = "`leave_id` = '".$obj->getLeaveId()."'";

		$query = $sqlBuilder->simpleUpdate($updateTable, $updateFileds, $updateValues, $updateConditions);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		if ($result) {
			return true;
		} else {
			return false;
		}

	}

	public function changeTakenLeaveQuota($obj) {

        $sql = "UPDATE `hs_hr_employee_leave_quota` q, `hs_hr_leave` l SET " .
               "q.`leave_taken` = q.`leave_taken` - l.`leave_length_days` WHERE " .
               "q.`year` = '".$obj->getLeaveYear()."' AND " .
               "q.`leave_type_id` = '".$obj->getLeaveTypeId()."' AND " .
               "q.`employee_id` = '".$obj->getEmployeeId()."' AND " .
               "l.`leave_id` = '".$obj->getLeaveId()."'" ;

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($sql);

		if ($result) {
			return true;
		} else {
			return false;
		}

	}

	protected function _buildObjArr($result) {

		if (!$result) {
			return false;
		}

		$objArr = null;

		while ($row = mysql_fetch_array($result)) {

			$leaveTakenArray = new LeaveTakenRequests();

			$leaveTakenArray->setLeaveId($row['leave_id']);
			$leaveTakenArray->setLeaveDate($row['leave_date']);
			$leaveTakenArray->setEmployeeName($row['emp_firstname'] . " " . $row['emp_lastname']);
			$leaveTakenArray->setNoHours($row['leave_length_hours']);
			$leaveTakenArray->setLeaveComments($row['leave_comments']);
			$leaveTakenArray->setLeaveTypeId($row['leave_type_id']);
			$leaveTakenArray->setLeaveTypeName($row['leave_type_name']);
			$leaveTakenArray->setEmployeeId($row['employee_id']);

			$objArr[] = $leaveTakenArray;

		}

		return $objArr;

	}

}
?>