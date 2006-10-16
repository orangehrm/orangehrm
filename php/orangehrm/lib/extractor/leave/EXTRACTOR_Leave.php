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

require_once ROOT_PATH . '/lib/models/leave/Leave.php';

class EXTRACTOR_Leave {
	
	private $parent_Leave;
	
	function __construct() {
		$this->parent_Leave = new Leave();
	}

	public function parseAddData($postArr) {	
		
		$this->parent_Leave->setEmployeeId($postArr['txtEmployeeId']);
		$this->parent_Leave->setLeaveDate($postArr['txtLeaveTypeId']);
		$this->parent_Leave->setLeaveDate($postArr['txtLeaveDate']);
		
		return $this->parent_Leave;
	}
			
	public function parseEditData($postArr) {	
			
		$this->parent_Leave->setEmployeeId($postArr['txtEmployeeId']);
		$this->parent_Leave->setLeaveDate($postArr['txtLeaveTypeId']);			
		$this->parent_Leave->setLeaveDate($postArr['txtLeaveDate']);
		
		return $this->parent_Leave;
	}
	
	public function parseDeleteData($postArr) {
		
		for ($i=0; $i < count($postArr['cmbStatus']); $i++) {
			if ($postArr['cmbStatus'][$i] == 0) {
				$tmpObj = new Leave();
				$tmpObj->setLeaveId($postArr['id'][$i]);
				
				$objLeave[] = $tmpObj;
			}
		}
		
		return $objLeave;		
	}
	
}
?>