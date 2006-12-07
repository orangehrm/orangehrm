<?php

/*
 *
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

class LeaveQuota {
	
	/*
	 *
	 *	Class atributes
	 *
	 **/
	
	private $leaveTypeId;	
	private $employeeId;
	private $noOfDaysAllotted;
	private $leaveTypeName;
	
	/*
	 *
	 *	Class contructor
	 *
	 **/
	
	public function __construct() {
		//nothing to do
	}
	
	/*
	 *	Getter method followed by setter method for each
	 *	attribute
	 *
	 **/
	
	public function getLeaveTypeId() {
		return $this->leaveTypeId;
	}

	public function setLeaveTypeId($leaveTypeId) {
		$this->leaveTypeId = $leaveTypeId;
	}
		
	public function getEmployeeId() {
		return $this->employeeId;
	}

	public function setEmployeeId($employeeId) {
		$this->employeeId = $employeeId;
	}
	
	public function getNoOfDaysAllotted() {
		return $this->noOfDaysAllotted;
	}

	public function setNoOfDaysAllotted($noOfDaysAlotted) {
		$this->noOfDaysAllotted = $noOfDaysAlotted;
	}
	
	public function getLeaveTypeName() {
		return $this->leaveTypeName;
	}

	public function setLeaveTypeName($leaveTypeName) {
		$this->leaveTypeName = $leaveTypeName;
	}
	
	/**
	 * Add Leave Quota of an employee
	 *
	 * @param String $employeeId
	 * @return boolean
	 * @access public
	 */
	public function addLeaveQuota($employeeId) {
		
		$this->setEmployeeId($employeeId);
		
		$sqlBuilder = new SQLQBuilder();

		$insertTable = '`hs_hr_employee_leave_quota`';
		
		$insertValues[] = "'".$this->getLeaveTypeId()."'";
		$insertValues[] = "'".$this->getEmployeeId()."'";
		$insertValues[] = $this->getNoOfDaysAllotted();
		
		$query = $sqlBuilder->simpleInsert($insertTable, $insertValues);
		
		$dbConnection = new DMLFunctions();	

		$result = $dbConnection -> executeQuery($query);
		
		if ($result) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Edit leave quota of an employee
	 * 
	 * @return boolean
	 * @access public
	 */
	public function editLeaveQuota() {
		if ($this->checkRecordExsist()) {
			return $this->updateLeaveQuota();
		}
		
		return $this->addLeaveQuota($this->getEmployeeId());
	}
	
	/**
	 * Update leave quota of an employee
	 * 
	 * @return boolean
	 * @access public
	 */
	private function updateLeaveQuota() {
		$sqlBuilder = new SQLQBuilder();
		
		$updateTable = "`hs_hr_employee_leave_quota`";

		$updateFileds[] = "`no_of_days_allotted`";	
		
		$updateValues[] = "'".$this->getNoOfDaysAllotted()."'";
		
		$updateConditions[] = "`leave_type_id` = '".$this->getLeaveTypeId()."'";
		$updateConditions[] = "`employee_id` = '".$this->getEmployeeId()."'";
		
		$query = $sqlBuilder->simpleUpdate($updateTable, $updateFileds, $updateValues, $updateConditions);
		
		//echo $query."\n";
		
		$dbConnection = new DMLFunctions();	

		$result = $dbConnection->executeQuery($query);
				
		if ($result) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Checks whether an employee has a quota record
	 * already for particular leave type to decide whether
	 * to add or edit the quota.
	 * 
	 * @access private
	 * @return boolean
	 */
	private function checkRecordExsist() {
		$sqlBuilder = new SQLQBuilder();
		
		$selectTable = "`hs_hr_employee_leave_quota`";
		
		$selectFields[] = "COUNT(*)";
		
		$selectConditions[] = "`leave_type_id` = '".$this->getLeaveTypeId()."'";
		$selectConditions[] = "`employee_id` = '".$this->getEmployeeId()."'";
		
		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions);		
		
		$dbConnection = new DMLFunctions();	

		$result = $dbConnection->executeQuery($query);
		
		$count = mysql_fetch_row($result);
		
		if ($count[0] > 0) {
			return true;
		}
		
		return false;
	}
	
	/**
	 *	Retrieves Leave Quota Details of all Leave Quota 
	 *	available to the employee.
	 * 	
	 * 	@param String $employeeId
	 * 	@return LeaveQuota[][]
	 * 	@access public
	 */
	public function fetchLeaveQuota($employeeId) {
		$sqlBuilder = new SQLQBuilder();
		
		$arrFields[0] = 'a.`leave_type_id`';
		$arrFields[1] = 'b.`leave_type_name`';
		$arrFields[2] = 'a.`no_of_days_allotted`';					
		
		$arrTables[0] = "`hs_hr_employee_leave_quota` a";		
		$arrTables[1] = "`hs_hr_leavetype` b";			
		
		$joinConditions[1] = "a.`leave_type_id` = b.`leave_type_id`";		
		
		$selectConditions = null;
		
		$selectConditions[0] = "a.`employee_id` = '".$employeeId."'";
		$selectConditions[1] = "a.`no_of_days_allotted` > 0";
				
		$selectOrderBy = $arrFields[1];
		$selectOrder   = "DESC";
		
		$joinTypes[1] = "LEFT";

		$query = $sqlBuilder->selectFromMultipleTable($arrFields, $arrTables, $joinConditions, $selectConditions, $joinTypes, $selectOrderBy, $selectOrder);
		
		//echo $query."\n";
		
		$dbConnection = new DMLFunctions();	

		$result = $dbConnection -> executeQuery($query);
		
		$leaveTypeArr = $this->_buildObjArr($result);	
		
		return $leaveTypeArr;
	}
	
	protected function _buildObjArr($result) {
		
		$objArr = null;
		
		while ($row = mysql_fetch_row($result)) {
			
			$tmpLeaveArr = new LeaveQuota();
						
			$tmpLeaveArr->setLeaveTypeId($row[0]);
			$tmpLeaveArr->setLeaveTypeName($row[1]);
			$tmpLeaveArr->setNoOfDaysAllotted($row[2]);						
			
			$objArr[] = $tmpLeaveArr;
		}	
		
		return $objArr;
	}
}
?>