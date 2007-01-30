<?php
/**
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
 * @copyright 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com
 */

require_once ROOT_PATH . '/lib/models/leave/Leave.php';
require_once ROOT_PATH . '/lib/models/leave/Holidays.php';
require_once ROOT_PATH . '/lib/models/leave/Weekends.php';

/**
 * Leave Request Class
 * 
 * Mainly involved in displaying leave and populating leave of
 * multiple days
 * 
 * @author S.H.Mohanjith <mohanjith@orangehrm.com>, <moha@mohanjith.net>
 */
class LeaveRequests extends Leave {
	
	const LEAVEREQUESTS_LEAVELENGTH_RANGE = 9;
	
	const LEAVEREQUESTS_MULTIPLESTATUSES = 5;
	
	private $leaveFromDate;
	private $leaveToDate;
	
	private $noDays;
	private $commentsDiffer;
	
	public function setLeaveFromDate($leaveFromDate) {
		$this->leaveFromDate = trim($leaveFromDate);
	}
		
	public function getLeaveFromDate() {
		return $this->leaveFromDate;
	}
	
	public function setLeaveToDate($leaveToDate) {
		$this->leaveToDate = trim($leaveToDate);
	}
		
	public function getLeaveToDate() {
		return $this->leaveToDate;
	}
	
	public function setNoDays($noDays) {
		$this->noDays = trim($noDays);
	}
		
	public function getNoDays() {
		return $this->noDays;
	}
	
	public function setCommentsDiffer($differ) {
		$this->commentsDiffer = $differ;
	}
	
	public function getCommentsDiffer() {
		return $this->commentsDiffer;
	}
	
	public function __construct() {
		$weekendObj = new Weekends();		
		$this->weekends = $weekendObj->fetchWeek();		
	}
	
	/**
	 *	Retrieves Leave Request Details of all leave that have been applied for but
	 *	not yet taken of the employee.
	 *
	 * @return LeaveRequests[][] $leaveArr A 2D array of the leaves
	 */	
	public function retriveLeaveRequestsEmployee($employeeId) {
		
		$this->setEmployeeId($employeeId);
		
		$sqlBuilder = new SQLQBuilder();		
		
		$arrFields[0] = '`leave_type_name`';
		$arrFields[1] = '`leave_request_id`';				
		
		$arrTable = "`hs_hr_leave_requests`";

		$selectConditions[1] = "`employee_id` = '".$employeeId."'";		
						
		$query = $sqlBuilder->simpleSelect($arrTable, $arrFields, $selectConditions, $arrFields[1], 'ASC');
		
		//echo $query."\n";
						
		$dbConnection = new DMLFunctions();	

		$result = $dbConnection -> executeQuery($query);
		
		$leaveArr = $this->_buildObjArr($result);
		
		return $leaveArr; 
	}
	
	/**
	 * Retrieves Leave Request Details of all leave that have been applied for but
	 * not yet taken by all supervisor's subordinates.
	 *
	 * @return LeaveRequests[][] $leaveArr A 2D array of the leaves
	 */
	public function retriveLeaveRequestsSupervisor($supervisorId) {
		
		$sqlBuilder = new SQLQBuilder();		
		
		$arrFields[0] = 'a.`leave_type_name`';
		$arrFields[1] = 'a.`leave_request_id`';		
		$arrFields[2] = 'd.`emp_firstname`';
		$arrFields[3] = 'a.`employee_id`';
		
		$arrTables[0] = "`hs_hr_leave_requests` a";		
		$arrTables[1] = "`hs_hr_emp_reportto` c";
		$arrTables[2] = "`hs_hr_employee` d";		
		
		$joinConditions[1] = "a.`employee_id` = c.`erep_sub_emp_number`";
		$joinConditions[2] = "a.`employee_id` = d.`emp_number`";		
		
		$selectConditions[1] = "c.`erep_sup_emp_number` = '".$supervisorId."'";		
		
		$query = $sqlBuilder->selectFromMultipleTable($arrFields, $arrTables, $joinConditions, $selectConditions);
		
		//echo $query."\n";
				
		$dbConnection = new DMLFunctions();	

		$result = $dbConnection -> executeQuery($query);
				
		$leaveArr = $this->_buildObjArr($result, true);
		
		return $leaveArr; 
	}
	
	protected function _buildObjArr($result, $supervisor=false) {		
		
		$objArr = null;
		
		while ($row = mysql_fetch_row($result)) {
			
			$tmpLeaveRequestArr = new LeaveRequests();					
			
			$tmpLeaveRequestArr->setLeaveTypeName($row[0]);			
			$tmpLeaveRequestArr->setLeaveRequestId($row[1]);
			
			$tmpLeave = new Leave();
			
			$tmpLeaveArr = $tmpLeave->retrieveLeave($row[1]);
			
			if (isset($tmpLeaveArr) && !empty($tmpLeaveArr)) {
			
				$totalLeaves = count($tmpLeaveArr);
				
				$tmpLeaveRequestArr->setLeaveFromDate($tmpLeaveArr[0]->getLeaveDate());
				
				$noOfDays = $this->_leaveLength($tmpLeaveArr[0]->getLeaveLength(), $this->_timeOffLength($tmpLeaveArr[0]->getLeaveDate()));
				
				if ($totalLeaves > 1) {
					$tmpLeaveRequestArr->setLeaveToDate($tmpLeaveArr[$totalLeaves-1]->getLeaveDate());				
					
					$status = $tmpLeaveArr[0]->getLeaveStatus();
					$comments = $tmpLeaveArr[0]->getLeaveComments();
					$commentsDiffer = false;
								
					for ($i=1; $i<$totalLeaves; $i++) {									
						
						if ($tmpLeaveArr[$i]->getLeaveStatus() != Leave::LEAVE_STATUS_LEAVE_CANCELLED) {		
							//echo $tmpLeaveArr[$i]->getLeaveLength()."<br>";
							$noOfDays += $tmpLeaveArr[$i]->getLeaveLength();	
						
							if ($status != $tmpLeaveArr[$i]->getLeaveStatus()) {
								$status = self::LEAVEREQUESTS_MULTIPLESTATUSES;						
							}
						}
						
						if ($comments != $tmpLeaveArr[$i]->getLeaveComments()) {
							$commentsDiffer = true;						
						}							
					}
					
					$tmpLeaveRequestArr->setLeaveComments($comments);
					$tmpLeaveRequestArr->setCommentsDiffer($commentsDiffer);
					
					$tmpLeaveRequestArr->setLeaveStatus($status);
					$tmpLeaveRequestArr->setLeaveLength(self::LEAVEREQUESTS_LEAVELENGTH_RANGE);				
				} else {
					$tmpLeaveRequestArr->setLeaveLength($tmpLeaveArr[0]->getLeaveLength());
					$tmpLeaveRequestArr->setLeaveStatus($tmpLeaveArr[0]->getLeaveStatus());			
					$tmpLeaveRequestArr->setLeaveComments($tmpLeaveArr[0]->getLeaveComments());
				}
						
				$tmpLeaveRequestArr->setNoDays($noOfDays/self::LEAVE_LENGTH_FULL_DAY);
				
				if ($supervisor) {
					$tmpLeaveRequestArr->setEmployeeName($row[2]);
					$tmpLeaveRequestArr->setEmployeeId($row[3]);
					if ($tmpLeaveRequestArr->getLeaveStatus() != self::LEAVE_STATUS_LEAVE_TAKEN) {				
						$objArr[] = $tmpLeaveRequestArr;
					}
				} else {						
					$objArr[] = $tmpLeaveRequestArr;
				}
			}
		}
		
		return $objArr;
	}
	
	/**
	 * Apply leave for multiple days
	 *
	 */
	public function applyLeaveRequest() {
		$this->_addLeaveRequest();
		$this->_applyLeaves();	
	}
	
	/**
	 * Does actual leave applying
	 *
	 */
	private function _applyLeaves() {
		$from = strtotime($this->getLeaveFromDate());
		$to = strtotime($this->getLeaveToDate());
				
		for ($timeStamp=$from; $timeStamp<=$to; $timeStamp=$this->_incDate($timeStamp)) {
			$this->setLeaveDate(date('Y-m-d', $timeStamp));			
			$this->_addLeave();
		}
	}
	
	/**
	 * Date increment
	 *
	 * @param int $timestamp
	 */
	private function _incDate($timestamp) {
		$timestamp+=60*60*24;	
		
		return $timestamp;	
	}
	
	/**
	 * Adds Record to Leave Request
	 * 
	 * @access private
	 */
	private function _addLeaveRequest() {		

		$this->_getNewLeaveRequestId();
		$this->_getLeaveTypeName();
		$this->setDateApplied(date('Y-m-d'));
				
		$arrRecordsList[0] = $this->getLeaveRequestId();
		$arrRecordsList[1] = "'".$this->getLeaveTypeId()."'";		
		$arrRecordsList[2] = "'".$this->getLeaveTypeName()."'";
		$arrRecordsList[3] = "'". $this->getDateApplied()."'";
		$arrRecordsList[4] = "'". $this->getEmployeeId() . "'";		
					
		$arrTable = "`hs_hr_leave_requests`";
		
		$sqlBuilder = new SQLQBuilder();
				
		$query = $sqlBuilder->simpleInsert($arrTable, $arrRecordsList);
		
		$dbConnection = new DMLFunctions();	

		$result = $dbConnection -> executeQuery($query);
		
	}
	
	/**
	 *
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
		
		//print_r($changeValues);		
		$updateConditions[0] = "`leave_request_id` = ".$this->getLeaveRequestId();

		$query = $sqlBuilder->simpleUpdate($table, $changeFields, $changeValues, $updateConditions);

		//echo $query."\n";

		$dbConnection = new DMLFunctions(); 

		$result = $dbConnection->executeQuery($query);

		if (isset($result) && (mysql_affected_rows() > 0)) {
			return true;
		};

		return false; 
	}
	
	private function _getNewLeaveRequestId() {
		$sql_builder = new SQLQBuilder();
		
		$selectTable = "`hs_hr_leave_requests`";		
		$selectFields[0] = '`leave_request_id`';
		$selectOrder = "DESC";
		$selectLimit = 1;
		$sortingField = '`leave_request_id`';
		
		$query = $sql_builder->simpleSelect($selectTable, $selectFields, null, $sortingField, $selectOrder, $selectLimit);
		//echo $query;
		$dbConnection = new DMLFunctions();	

		$result = $dbConnection -> executeQuery($query);
		
		$row = mysql_fetch_row($result);
		
		$this->setLeaveRequestId($row[0]+1);
	}	
	
}
?>