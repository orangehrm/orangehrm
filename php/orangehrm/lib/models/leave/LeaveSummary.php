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

require_once "Leave.php";
require_once "LeaveQuota.php";
//require_once "LeaveType.php";

/**
 * Leave Summary Operations
 *
 * @package OrangeHRM
 * @author S.H.Mohanjith
 * @copyright OrangeHRM Inc. International
 *
 */
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
	 * Leave summary of the employee
	 *
	 * @param String $employeeId e.g. 001
	 * @return Array[][] LeaveSummary
	 * @access public
	 * @author S.H.Mohanjith
	 *
	 */
	public function fetchLeaveSummary($employeeId, $year, $leaveTypeId = self::LEAVESUMMARY_CRITERIA_ALL) {

		$this->setYear($year);

		$this->setEmployeeId($employeeId);

		$this->setLeaveTypeId($leaveTypeId);

		$leaveTypeArr = $this->fetchLeaveQuota($employeeId);

		return $leaveTypeArr;
	}

	/**
	 * Leave summary of all employees
	 */
	public function fetchAllEmployeeLeaveSummary($employeeId, $year, $leaveTypeId = self::LEAVESUMMARY_CRITERIA_ALL, $searchBy="employee") {

		$this->setYear($year);
		$this->setEmployeeId($employeeId);
		$this->setLeaveTypeId($leaveTypeId);

		$selectFields[0] = '`employee_id`';
		$selectFields[1] = '`leave_type_id`';
		$selectFields[2] = '`no_of_days_allotted`';

		$selectTable = "`hs_hr_employee_leave_quota`";

		$selectConditions = null;

		$employeeId = $this->getEmployeeId();
		if (!empty($employeeId) && ($this->getEmployeeId() != self::LEAVESUMMARY_CRITERIA_ALL)) {
			$selectConditions[] = "`employee_id` = {$this->getEmployeeId()}";
		}
		$leaveTypeId = $this->getLeaveTypeId();
		if (!empty($leaveTypeId) && ($this->getLeaveTypeId() != self::LEAVESUMMARY_CRITERIA_ALL)) {
			$selectConditions[] = "`leave_type_id` = '{$this->getLeaveTypeId()}'";
		}

		$selectOrderBy = "`employee_id`";

		$sqlBuilder = new SQLQBuilder();

		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions, $selectOrderBy);

		//echo $query;

		$resultArr = $this->_fetchEmployeesAndLeaveTypes($searchBy);
		$resultArr1 = $this->_fetchSumOfLeavesAll();

		$dbConnection = new DMLFunctions();

		$result = $dbConnection->executeQuery($query);

		while ($row = mysql_fetch_assoc($result)) {

			if ($searchBy == "leaveType") {
				$tmp = $resultArr[$row['leave_type_id']][$row['employee_id']];
			} else {
				$tmp = $resultArr[$row['employee_id']][$row['leave_type_id']];
			}
			$tmp['no_of_days_allotted'] = $row['no_of_days_allotted'];

			$tmp['leave_available'] = $tmp['no_of_days_allotted']-$tmp['leave_taken'];

			if ($searchBy == "leaveType") {
				$resultArr[$row['leave_type_id']][$row['employee_id']] = $tmp;
			} else {
				$resultArr[$row['employee_id']][$row['leave_type_id']] = $tmp;
			}
		}

		if (is_array($resultArr1)) {
			foreach ($resultArr1 as $employeeId=>$leaveSumArr) {
				foreach ($leaveSumArr as $leaveTypeId=>$leaveSum) {
					if ($searchBy == "leaveType") {
						$resultArr[$leaveTypeId][$employeeId]['leave_taken']=round(($leaveSum['leave_length']/Leave::LEAVE_LENGTH_FULL_DAY)*10)/10;
						$resultArr[$leaveTypeId][$employeeId]['leave_available']=$resultArr[$leaveTypeId][$employeeId]['no_of_days_allotted']-$resultArr[$leaveTypeId][$employeeId]['leave_taken'];
					} else {
						$resultArr[$employeeId][$leaveTypeId]['leave_taken']=round(($leaveSum['leave_length']/Leave::LEAVE_LENGTH_FULL_DAY)*10)/10;
						$resultArr[$employeeId][$leaveTypeId]['leave_available']=$resultArr[$employeeId][$leaveTypeId]['no_of_days_allotted']-$resultArr[$employeeId][$leaveTypeId]['leave_taken'];
					}
				}
			}
		}

		$objLeaveType = new LeaveType();

		$resultArrX = null;

		if (is_array($resultArr)) {
			foreach ($resultArr as $key1=>$level1Arr) {
				foreach ($level1Arr as $key2=>$row) {

					$leveTypeObj = new LeaveType();

					$leaveType = $leveTypeObj->retriveLeaveType($row['leave_type_id']);

					if (($leaveType[0]->getLeaveTypeAvailable() == $objLeaveType->availableStatusFlag) || ($row['leave_taken'] > 0)) {
						$resultArrX[$key1][$key2]=$row;
					}
				}
			}
		}

		return $resultArrX;
	}

	private function _fetchSumOfLeavesAll() {
		$year = $this->getYear();

		$selectFields[0] = '`employee_id`';
		$selectFields[1] = '`leave_type_id`';
		$selectFields[2] = 'SUM(ABS(`leave_length`)) as leave_length';

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
			$resultArr[$row['employee_id']][$row['leave_type_id']]['leave_length'] = $row['leave_length'];
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

				if (isset($leaveTypeList[$row[0]])) {
					$tmpLeaveSummary = $leaveTypeList[$row[0]];

					$leaveTypeAvailable = $tmpLeaveSummary->getLeaveTypeAvailable();

					$tmpLeaveSummary->setNoOfDaysAllotted($row[2]);

					$taken = $tmpLeaveSummary->getLeaveTaken();
					$alloted = $tmpLeaveSummary->getNoOfDaysAllotted();

					$tmpLeaveSummary->setLeaveAvailable($alloted-$taken);

					$leaveTypeList[$row[0]] = $tmpLeaveSummary;
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