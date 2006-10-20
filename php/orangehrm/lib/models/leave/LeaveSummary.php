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

class LeaveSummary extends LeaveQuota {
	
	/*
	 *
	 *	Class atributes
	 *
	 **/
	
	private $leaveTaken;
	private $leaveAvailable;	
	
	
	/*
	 *	Setter method followed by getter method for each
	 *	attribute
	 *
	 **/
	
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
	
	public function fetchLeaveSummary($employeeId) {
		
		$this->setEmployeeId($employeeId);
		
		$leaveQuotas = $this->fetchLeaveQuota($this->getEmployeeId());
		
		return $leaveQuotas;		
	}
	
	protected function _buildObjArr($result) {
		
		$leaveObj = new Leave();		
		$leaveObj->setEmployeeId($this->getEmployeeId());
		
		$objArr = null;
		
		while ($row = mysql_fetch_row($result)) {
			
			$tmpLeaveSummary = new LeaveSummary();
						
			$tmpLeaveSummary->setLeaveTypeId($row[0]);
			$tmpLeaveSummary->setLeaveTypeName($row[1]);
			$tmpLeaveSummary->setNoOfDaysAllotted($row[2]);

			$taken = $leaveObj->countLeave($tmpLeaveSummary->getLeaveTypeId());
			$alloted = $tmpLeaveSummary->getNoOfDaysAllotted();
			
			$tmpLeaveSummary->setLeaveTaken($taken);
			$tmpLeaveSummary->setLeaveAvailable($alloted-$taken);		
			
			$objArr[] = $tmpLeaveSummary;
		}
		
		return $objArr;
	}
}

?>