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

require_once "Leave.php";
require_once "LeaveQuota.php";
//require_once "LeaveType.php";

/**
 * Leave Summary Operations
 *
 * @package OrangeHRM
 * @author S.H.Mohanjith
 * @copyright hSenid Software International
 * 
 */
class LeaveSummary extends LeaveQuota {
	
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
	public function fetchLeaveSummary($employeeId, $year) {
		
		$this->setYear($year);
		
		$this->setEmployeeId($employeeId);
		
		$leaveTypeArr = $this->fetchLeaveQuota($employeeId);	
		
		return $leaveTypeArr;				
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