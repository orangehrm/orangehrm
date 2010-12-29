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

require_once ROOT_PATH."/lib/models/leave/Leave.php";
require_once ROOT_PATH."/lib/models/leave/LeaveQuota.php";
require_once ROOT_PATH.'/lib/logs/LogFileWriter.php';

//require_once "LeaveType.php";

class LeaveSummary extends LeaveQuota {

	const LEAVESUMMARY_CRITERIA_ALL = '0';

	/**
	 *	Class atributes
	 *
	 */

	private $leaveTaken;
	private $leaveAvailable;
	private $year;
	private $leaveTypeAvailable;


	/**
	 *	Setter method followed by getter method for each
	 *	attribute
	 *
	 */

	public function setLeaveTaken($leaveTaken) {
		$this->leaveTaken = $leaveTaken;
	}

	public function getLeaveTaken() {
		return $this->leaveTaken;
	}

	public function setLeaveAvailable($leaveAvailable) {
		$this->leaveAvailable = $leaveAvailable;
	}

	public function getLeaveAvailable() {
		return $this->leaveAvailable;
	}

	public function setYear($year) {
		$this->year = $year;
	}

	public function getYear() {
		return $this->year;
	}

	public function getLeaveTypeAvailable () {
		return $this->leaveTypeAvailable;
	}

	public function setLeaveTypeAvailable($flag) {
		$this->leaveTypeAvailable = $flag;
	}

	/**
	 * Leave summary of all employees
	 */
	public function fetchAllEmployeeLeaveSummary($employeeId, $year, $leaveTypeId = self::LEAVESUMMARY_CRITERIA_ALL, $searchBy="employee", $sortField=null, $sortOrder=null, $hideDeleted=false, $pageNO=1, $itemPerPage=0, $leaveCount = FALSE) {

		$selectFields[0] = "a.`emp_number` as emp_number";
		$selectFields[1] = "CONCAT(a.`emp_firstname`, ' ', a.`emp_lastname`) as employee_name";
		$selectFields[2] = "c.`leave_type_name` as leave_type_name";
		$selectFields[3] = "COALESCE(b.`no_of_days_allotted`, 0) as no_of_days_allotted";
		$selectFields[4] = "COALESCE(b.`leave_taken`, 0) as leave_taken";
		$sumOfApproved = "SUM( IF( d.`leave_status` = " . Leave::LEAVE_STATUS_LEAVE_APPROVED . ", ABS(COALESCE(d.`leave_length_days`, 0)), 0) )";
		$selectFields[5] = "{$sumOfApproved} as leave_scheduled";
		$selectFields[6] = "COALESCE(b.`no_of_days_allotted`, 0) + COALESCE(b.`leave_brought_forward`, 0) - COALESCE(b.`leave_taken`, 0) - {$sumOfApproved} as leave_available";
		$selectFields[7] = "c.`leave_type_id` as leave_type_id";
		$selectFields[8] = "c.`available_flag` as available_flag";

		$arrTables[0] = '(`hs_hr_employee` a, `hs_hr_leavetype` c)';
		$arrTables[1] = '`hs_hr_employee_leave_quota` b';
		$arrTables[2] = '`hs_hr_leave` d';

		$joinConditions[1] = "a.`emp_number` = b.`employee_id` AND c.`leave_type_id` = b.`leave_type_id` AND b.`year` = '{$year}'";
		$joinConditions[2] = "d.`employee_id` = a.`emp_number` AND c.`leave_type_id` = d.`leave_type_id` AND (d.`leave_status` = " . Leave::LEAVE_STATUS_LEAVE_TAKEN . " OR d.`leave_status` = " . Leave::LEAVE_STATUS_LEAVE_APPROVED . ") AND d.`leave_date` BETWEEN DATE('".$year."-01-01') AND DATE('".$year."-12-31')";

		$groupBy = "emp_number, employee_name, leave_type_id, leave_type_name, no_of_days_allotted, available_flag";

		$selectConditions = null;

		if ( ($searchBy == "employee" || $searchBy == "both") && !empty($employeeId) && ($employeeId != self::LEAVESUMMARY_CRITERIA_ALL)) {
			$selectConditions[] = "a.`emp_number` = {$employeeId}";
		}

		$selectConditions[]  = "(a.`emp_status` IS  NULL OR a.`emp_status` != 'EST000')" ;
		$selectConditions[] = "c.`available_flag` = 1";

		if ($sortField == null) {
			$sortField = 0;
		}
		if ($sortOrder == null) {
			$sortOrder = "ASC";
		}

		/* Get the alias name (the last word) in the field definition */
		$tmpFieldDefWords = explode(" ", $selectFields[$sortField]);
		$orderBy = array_pop($tmpFieldDefWords);

		$sqlBuilder = new SQLQBuilder();
        
		$query = $sqlBuilder->selectFromMultipleTable($selectFields, $arrTables, $joinConditions, $selectConditions, null, $orderBy, $sortOrder, null, $groupBy);

		$objLeaveType = new LeaveType();

		if($leaveCount){
			if ( ($searchBy == "leaveType" || $searchBy == "both") && !empty($leaveTypeId) && ($leaveTypeId != self::LEAVESUMMARY_CRITERIA_ALL)) {
				$query = "SELECT COUNT(*) AS leaveCount FROM ( $query ) subsel WHERE leave_type_id = '$leaveTypeId' AND available_flag = {$objLeaveType->availableStatusFlag}";
			} else {
				$query = "SELECT COUNT(*) AS leaveCount FROM ( $query ) subsel WHERE available_flag = {$objLeaveType->availableStatusFlag}";
			}
		}else{	
			if ( ($searchBy == "leaveType" || $searchBy == "both") && !empty($leaveTypeId) && ($leaveTypeId != self::LEAVESUMMARY_CRITERIA_ALL)) {
				$query = "SELECT * FROM ( $query ) subsel WHERE leave_type_id = '$leaveTypeId' AND available_flag = {$objLeaveType->availableStatusFlag}";
			} else {
				$query = "SELECT * FROM ( $query ) subsel WHERE available_flag = {$objLeaveType->availableStatusFlag}";
			}
		}

		if (!$hideDeleted) {
			$query = $query . " OR leave_taken > 0 OR leave_scheduled > 0";
		}
		
		/* Setting limit */
		
		if (!$leaveCount) {

	        $limit = '0, 50';
	        if ($pageNO > 0) {
	            $pageNO--;
	            $pageNO *= $itemPerPage;
	            $limit = "$pageNO, $itemPerPage";
	        }

			$query = $query." LIMIT ".$limit;	
		}
		
		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($query);

		$resultArr = null;

		while ($row = mysql_fetch_assoc($result)) {
			$resultArr[] = $row;
		}

		return $resultArr;
		
	}

	private function _fetchSumOfLeavesAll() {
		$year = $this->getYear();

		$selectFields[0] = '`employee_id`';
		$selectFields[1] = '`leave_type_id`';
		$selectFields[2] = 'SUM(ABS(`leave_length_days`)) as leave_length_days';

		$selectTable = "`hs_hr_leave`";

		$employeeId = $this->getEmployeeId();
		if (!empty($employeeId) && ($this->getEmployeeId() != self::LEAVESUMMARY_CRITERIA_ALL)) {
			$selectConditions[] = "`employee_id` = {$this->getEmployeeId()}";
		}
		$leaveTypeId = $this->getLeaveTypeId();
		if (!empty($leaveTypeId) && ($this->getLeaveTypeId() != self::LEAVESUMMARY_CRITERIA_ALL)) {
			$selectConditions[] = "`leave_type_id` = '{$this->getLeaveTypeId()}'";
		}

		$selectConditions[] = "`leave_status` = ".Leave::LEAVE_STATUS_LEAVE_TAKEN;
		$selectConditions[] = "`leave_date` BETWEEN DATE('".$year."-01-01') AND DATE('".$year."-12-31') GROUP BY `employee_id`, `leave_type_id`";

		$selectOrderBy = "`employee_id`";

		$sqlBuilder = new SQLQBuilder();

		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions, $selectOrderBy);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		$resultArr = null;

		while ($row = mysql_fetch_assoc($result)) {
			$resultArr[$row['employee_id']][$row['leave_type_id']]['leave_length_days'] = $row['leave_length'];
		}

		return $resultArr;
	}

	private function _fetchEmployeesAndLeaveTypes($searchBy="employee") {
		$selectFields[0] = "c.`emp_number` as emp_number";
		$selectFields[1] = "CONCAT(c.`emp_firstname`, ' ', c.`emp_lastname`) as employee_name";
		$selectFields[2] = "d.`leave_type_id` as leave_type_id";
		$selectFields[3] = "d.`leave_type_name` as leave_type_name";
		$selectFields[4] = "d.`available_flag` as available_flag";

		$selectTable = "`hs_hr_employee` c, `hs_hr_leavetype` d ";

		$selectConditions = null;

		$employeeId = $this->getEmployeeId();
		if (!empty($employeeId) && ($this->getEmployeeId() != self::LEAVESUMMARY_CRITERIA_ALL)) {
			$selectConditions[] = "c.`emp_number` = {$this->getEmployeeId()}";
		}
		$leaveTypeId = $this->getLeaveTypeId();
		if (!empty($leaveTypeId) && ($this->getLeaveTypeId() != self::LEAVESUMMARY_CRITERIA_ALL)) {
			$selectConditions[] = "d.`leave_type_id` = '{$this->getLeaveTypeId()}'";
		}

		$sqlBuilder = new SQLQBuilder();

		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions);

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		$resultArr = null;

		while ($row = mysql_fetch_assoc($result)) {
			$row['no_of_days_allotted'] = 0;
			$row['leave_taken'] = 0;
			$row['leave_available'] = 0;

			if ($searchBy == "leaveType") {
				$resultArr[$row['leave_type_id']][$row['emp_number']] = $row;
			} else {
				$resultArr[$row['emp_number']][$row['leave_type_id']] = $row;
			}
		}

		return $resultArr;
	}

	/**
	 * Overrides _buildObjArr of LeaveQuota
	 * Builds the Leave Summary from the resource
	 *
	 * @param resource $result
	 * @return Array[][] LeaveSummary
	 * @access protected
	 * @author S.H.Mohanjith
	 *
	 */
	protected function _buildObjArr($result) {

		$leaveObj = new Leave();
		$leaveObj->setEmployeeId($this->getEmployeeId());

		$objArr = null;

		$leveTypeObj = new LeaveType();

		$leaveTypes = $leveTypeObj->fetchLeaveTypes(true);

		$objLeaveType = new LeaveType();

		if (is_array($leaveTypes)) {
			foreach ($leaveTypes as $leaveType) {
				$tmpLeaveSummary = new LeaveSummary();

				$tmpLeaveSummary->setLeaveTypeId($leaveType->getLeaveTypeId());
				$tmpLeaveSummary->setLeaveTypeName($leaveType->getLeaveTypeName());
				$tmpLeaveSummary->setNoOfDaysAllotted(0);

				$taken = $leaveObj->countLeave($tmpLeaveSummary->getLeaveTypeId(), $this->getYear());

				$tmpLeaveSummary->setLeaveTaken($taken);
				$tmpLeaveSummary->setLeaveAvailable(0);

				$tmpLeaveSummary->setYear($this->getYear());

				$tmpLeaveSummary->setLeaveTypeAvailable($leaveType->getLeaveTypeAvailable());

				if (($tmpLeaveSummary->getLeaveTypeAvailable() == $objLeaveType->availableStatusFlag) || ($tmpLeaveSummary->getLeaveTaken() > 0)) {
					$leaveTypeList[$leaveType->getLeaveTypeId()] = $tmpLeaveSummary;
				}
			}

			$objLeaveType = new LeaveType();

			while ($row = mysql_fetch_row($result)) {

				if (isset($leaveTypeList[$row[1]])) {
					$tmpLeaveSummary = $leaveTypeList[$row[1]];

					$leaveTypeAvailable = $tmpLeaveSummary->getLeaveTypeAvailable();

					$tmpLeaveSummary->setNoOfDaysAllotted($row[3]);

					$taken = $tmpLeaveSummary->getLeaveTaken();
					$alloted = $tmpLeaveSummary->getNoOfDaysAllotted();

					$tmpLeaveSummary->setLeaveAvailable($alloted-$taken);

					$leaveTypeList[$row[1]] = $tmpLeaveSummary;
				}
			}

			if (isset($leaveTypeList)) {
				$objArr = $leaveTypeList;

				sort($objArr);
			}
		}
		return $objArr;
	}
}

?>
