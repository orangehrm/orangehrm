<?
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


//the model objects are included here

require_once ROOT_PATH . '/lib/models/leave/Leave.php';
require_once ROOT_PATH . '/lib/models/leave/LeaveType.php';
require_once ROOT_PATH . '/lib/models/leave/LeaveQuota.php';
require_once ROOT_PATH . '/lib/models/leave/LeaveSummary.php';

require_once ROOT_PATH . '/lib/models/hrfunct/EmpRepTo.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpInfo.php';

require_once ROOT_PATH . '/lib/common/TemplateMerger.php';

class LeaveController {
	
	private $indexCode;
	private $id;
	private $objLeave;
	
	public function setId($id) {
		$this->id = $id;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function setObjLeave($obj) {
		$this->objLeave = $obj;
	}
	
	public function getObjLeave() {
		return $this->objLeave;
	}


	public function __construct() {
		//nothing to do
	}
	
	//public function

	public function viewLeaves($modifier="employee") {
					
		switch ($modifier) {
			case "employee": $this->setObjLeave(new Leave());
							 $this->_viewLeavesEmployee();
							 break;
			case "suprevisor": $this->setObjLeave(new Leave());
							 $this->_viewLeavesSupervisor();
							 break;
			case "summary" : $this->setObjLeave(new LeaveSummary());
							 $this->_displayLeaveSummary();
							 break;
		}
	}
	
	/**
	 * Changes the status of the leave
	 * 
	 * @param [String $modifier]
	 * @return String
	 */
	public function changeStatus($modifier="cancel") {
				
		switch ($modifier) {
			case "cancel": $res = $this->_cancelLeave();
						   break;
			case "change": $res = $this->_changeLeaveStatus();
						   break;
		}
		
		if ($res) {
			$message="SUCCESS";
		} else {
			$message="FAILURE";
		}
		
		return $message;
	}
	
	private function _changeLeaveStatus() {
		$this->_authenticateChangeLeaveStatus();
		
		$tmpObj = $this->getObjLeave();
		
		echo $tmpObj->getLeaveStatus();
		
		return $tmpObj->changeLeaveStatus($this->getId());		
	}
	
	private function _viewLeavesEmployee() {
		$tmpObj = $this->getObjLeave();
		$tmpObj = $tmpObj->retriveLeaveEmployee($this->getId());
		
		$path = "/templates/leave/leaveList.php";
		
		$template = new TemplateMerger($tmpObj, $path);
		
		$template->display();		
	}
	
	/**
	 * Suprevisor's view of the leaves of subordinates
	 * 
	 * @return void
	 */
	private function _viewLeavesSupervisor() {
		$tmpObj = $this->getObjLeave();
		$tmpObj = $tmpObj->retriveLeaveSupervisor($this->getId());
		
		$path = "/templates/leave/leaveList.php";
		
		$template = new TemplateMerger($tmpObj, $path);
		
		$template->display("SUP");		
	}
	
	private function _cancelLeave() {
		$tmpObj = $this->getObjLeave();
		
		return $tmpObj->cancelLeave($this->getId());		
	}

	public function redirect($message=null) {
		if (isset($message)) {
			if (preg_match('/&/', $_SERVER['HTTP_REFERER']) > 0) {
				$message = "&message=".$message;
				$url = preg_split('/(&||\?)message=[A-Za-z0-9]*/', $_SERVER['HTTP_REFERER']);				
			} else {
				$message = "?message=".$message;
			}
		}		
		
		header("Location: ".$url[0].$message);
	}
	
	public function addLeave() {
		$tmpObj = $this->getObjLeave();
		$res = $tmpObj->applyLeave();
		
		if ($res) {
			$message="SUCCESS";
		} else {
			$message="FAILURE";
		}
	}
	public function displayLeaveInfo() {
		$tmpObjs[0] = new Leave();
				
		$tmpObj = new LeaveQuota();
		$this->setId($_SESSION['empID']);		
		$tmpObjs[1] = $tmpObj->fetchLeaveQuota($this->getId());
		
		$this->setObjLeave($tmpObjs);
		
		$path = "/templates/leave/leaveApply.php";
		
		$template = new TemplateMerger($tmpObjs, $path);
		
		$template->display();
	}
	
	/**
	 * Displays the Leave Summary
	 *
	 */
	private function _displayLeaveSummary() {
		$this->_authenticateViewLeaveSummary();
		
		$empInfoObj = new EmpInfo();
		
		$tmpObj = $this->getObjLeave();
		$tmpObjX[] = $tmpObj->fetchLeaveSummary($this->getId());
		$tmpObjX[] = $empInfoObj->filterEmpMain($this->getId());
		//print_r($tmpObjX);
		$path = "/templates/leave/leaveSummary.php";
		
		$template = new TemplateMerger($tmpObjX, $path);
		
		$template->display();
	}
	
	/**
	 * Checks whether the user is allowed to
	 * view the particular employee's Leave Summary
	 *
	 */
	private function _authenticateViewLeaveSummary() {
		$id = $this->getId();
		if ($id !== $_SESSION['empID']) {
			
			$objReportTo = new EmpRepTo();
			
			$subordinates = $objReportTo->getEmpSub($_SESSION['empID']);
					
			if (!array_search($id, $subordinates[0])) {
				trigger_error("Unauthorized access", E_USER_NOTICE);
			}
		}
	}
	
	/**
	 * Checks whether the user is allowed to
	 * change the particular employee's Leave status
	 *
	 */
	private function _authenticateChangeLeaveStatus() {
		$id = $this->getId();
		if ($id !== $_SESSION['empID']) {
			
			$objReportTo = new EmpRepTo();
			
			$subordinates = $objReportTo->getEmpSub($_SESSION['empID']);
			
			if (!array_search($id, $subordinates[0])) {
				//trigger_error("Unauthorized access", E_USER_NOTICE);
			}
		}
	}
	
	public function displayLeaveTypeDefine () {
		
		$tmpObj = new LeaveType();
				
		$this->setObjLeave($tmpObj);
		
		$path = "/templates/leave/leaveTypeDefine.php";
		
		$template = new TemplateMerger($tmpObj, $path);
		
		$template->display();
	}
	
	
	public function addLeaveType() {
				
		$tmpObj = $this->getObjLeave();
		$res = $tmpObj->addLeaveType();
		
		if ($res) {
			$message="SUCCESS";
		} else {
			$message="FAILURE";
		}
	}
	
	public function displayLeaveTypeSummary(){
		
		$tmpObj = new LeaveType();
		
		$this->setObjLeave($tmpObj);
	    
		$tmpObjArr = $tmpObj->fetchLeaveTypes();		
		
		$path = "/templates/leave/leaveTypeSummary.php";
		
		$template = new TemplateMerger($tmpObjArr, $path);
		
		$template->display();
	}
}
?>