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
	
	public function addLeaveQuota() {
		
	}
	
	public function editLeaveQuota() {
		
	}
	
	public function deleteLeaveQuota() {
		
	}
	
	/*
	 *	Retrieves Leave Quota Details of all Leave Quota 
	 *	avaliable to the employee.
	 *
	 *	Returns
	 *	-------
	 *
	 *	A 2D array of the leave quotas
	 *
	 **/

	public function fetchLeaveQuota($employeeId) {
		$sqlBuilder = new SQLQBuilder();
		
		$arrFields[0] = 'a.`Leave_Type_ID`';
		$arrFields[1] = 'b.`Leave_Type_Name`';
		$arrFields[2] = 'a.`No_of_days_alotted`';		
		
		$arrTables[0] = "`hs_hr_employee_leave_quota` a";		
		$arrTables[1] = "`hs_hr_leavetype` b";			
		
		$joinConditions[1] = "a.`Leave_Type_ID` = b.`Leave_Type_ID`";		
		
		$selectConditions[1] = "a.`Employee_ID` = '".$employeeId."'";

		$query = $sqlBuilder->selectFromMultipleTable($arrFields, $arrTables, $joinConditions, $selectConditions);
		
		//echo $query;
		
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