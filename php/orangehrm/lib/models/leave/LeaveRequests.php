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

/**
 * Leave Request Class
 * 
 * Mainly involved in displaying leave and populating leave of
 * multiple days
 * 
 * @author S.H.Mohanjith <mohanjith@orangehrm.com>, <moha@mohanjith.net>
 */
class LeaveRequests extends Leave {
	
	public $multipleStatuses = 5;
	
	private $leaveFromDate;
	private $leaveToDate;
	
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
	
	protected function _buildObjArr($result, $supervisor=false) {
		$objArr = null;
		
		while ($row = mysql_fetch_assoc($result)) {
			
			$tmpLeaveRequestArr = new LeaveRequests();					
			
			$tmpLeaveRequestArr->setLeaveTypeName($row['leave_type_name']);			
			$tmpLeaveRequestArr->setLeaveRequestId($row['leave_request_id']);
			
			$tmpLeave = new Leave();
			
			$tmpLeaveArr = $tmpLeave->retrieveLeave($row['leave_request_id']);
			
			$totalLeaves = count($tmpLeaveArr);
			
			$tmpLeaveRequestArr->setLeaveFromDate($tmpLeaveArr[0]->getLeaveDate());
			
			if ($totalLeaves > 1) {
				$tmpLeaveRequestArr->setLeaveToDate($tmpLeaveArr[$totalLeaves-1]->getLeaveDate());
				$tmpLeaveRequestArr->setLeaveLength($totalLeaves);
				
				$status = $tmpLeaveArr[0]->getLeaveStatus();
				$comments = $tmpLeaveArr[0]->getLeaveComments();
							
				for ($i=1; $i<$totalLeaves; $i++) {
					if ($status != $tmpLeaveArr[$i]->getLeaveStatus()) {
						$status = $this->multipleStatuses;
						break;
					}
				}
			} else {
				$tmpLeaveRequestArr->setLeaveLength($tmpLeaveArr[0]->getLeaveLength());
				$tmpLeaveRequestArr->setLeaveStatus($tmpLeaveArr[0]->getLeaveStatus());			
				$tmpLeaveRequestArr->setLeaveComments($tmpLeaveArr[0]->getLeaveComments());
			}
			
			if ($supervisor) {
				$tmpLeaveRequestArr->setEmployeeName($row[6]);
				$tmpLeaveRequestArr->setEmployeeId($row[7]);
			}
			
			$objArr[] = $tmpLeaveRequestArr;
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
		$from = $this->_parseDateToStamp($this->getLeaveFromDate());
		$to = $this->_parseDateToStamp($this->getLeaveToDate());
				
		for ($timeStamp=$from; $timeStamp<=$to; $timeStamp=$this->_incDate($timeStamp)) {
			$this->setLeaveDate(date('Y-m-d', $timeStamp));			
			$this->_addLeave();
		}
	}
	
	/**
	 * Parses Date of format Y-m-d into a Unix Timestamp
	 *
	 * @param String $date
	 * @return int
	 */
	private function _parseDateToStamp($date) {
		list($year, $month, $day) = explode('-', $date);
		
		return mktime ( 0, 0, 0, $month, $day, $year);
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
		
		//print_r($arrRecordsList);	
		
		$query = $sqlBuilder->simpleInsert($arrTable, $arrRecordsList);
		
		//echo  $query;
		
		$dbConnection = new DMLFunctions();	

		$result = $dbConnection -> executeQuery($query);
		
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