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


//the model objects are included here

require_once ROOT_PATH . '/lib/models/leave/Leave.php';
require_once ROOT_PATH . '/lib/models/leave/LeaveType.php';
require_once ROOT_PATH . '/lib/models/leave/LeaveQuota.php';
require_once ROOT_PATH . '/lib/models/leave/LeaveSummary.php';
require_once ROOT_PATH . '/lib/models/leave/Holidays.php';
require_once ROOT_PATH . '/lib/models/leave/Weekends.php';

require_once ROOT_PATH . '/lib/models/hrfunct/EmpRepTo.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpInfo.php';

require_once ROOT_PATH . '/lib/common/TemplateMerger.php';
require_once ROOT_PATH . '/lib/common/authorize.php';

class LeaveController {
	
	private $indexCode;
	private $id;
	private $objLeave;
	private $authorize;
	
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
	
	public function setAuthorize($obj) {
		$this->authorize = $obj;
	}
	
	public function getAuthorize() {
		return $this->authorize;
	}


	public function __construct() {
		$authorizeObj = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);
		
		$this->setAuthorize($authorizeObj);
		
		$tmpLeaveObj = new Leave();
		
		$tmpLeaveObj->takeLeave();
	}	
	
	//public function

	public function viewLeaves($modifier="employee", $year=null, $details=false) {
			
		if ($details) {	
			switch ($modifier) {
				case "employee": $this->setObjLeave(new Leave());
								 $this->_viewLeavesEmployee($details);
								 break;
				case "suprevisor": $this->setObjLeave(new Leave());
								 $this->_viewLeavesSupervisor($details);
								 break;
				case "taken"	: $this->setObjLeave(new Leave());
								 $this->_viewLeavesTaken($year, $details);
								 break;
				case "summary" : $this->setObjLeave(new LeaveSummary());
								 $this->_displayLeaveSummary("display", $year);							 
								 break;
			}
		} else {
			switch ($modifier) {
				case "employee": $this->setObjLeave(new LeaveRequests());
								 $this->_viewLeavesEmployee($details);
								 break;
				case "suprevisor": 	$this->setObjLeave(new LeaveRequests());
								 	$this->_viewLeavesSupervisor($details);
								 	break;
				case "taken"	: $this->setObjLeave(new LeaveRequests());
								 $this->_viewLeavesTaken($year, $details);
								 break;
				case "summary" : $this->setObjLeave(new LeaveSummary());
								 $this->_displayLeaveSummary("display", $year);							 
								 break;
			}
		}
	}	
	
	public function editLeaves($modifier="summary", $year=null) {
		switch ($modifier) {			
			case "summary" : $this->setObjLeave(new LeaveSummary());
							 $this->_displayLeaveSummary("edit", $year);
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
			$message="";
		} else {
			$message="";
		}
		
		return $message;
	}
	
	private function _changeLeaveStatus() {
		$this->_authenticateChangeLeaveStatus();
		
		$tmpObj = $this->getObjLeave();		
		
		return $tmpObj->changeLeaveStatus($this->getId());		
	}
	
	private function _viewLeavesEmployee($details) {
		$tmpObj = $this->getObjLeave();
		
		if (!$details) {
			$tmpObj = $tmpObj->retriveLeaveRequestsEmployee($this->getId());		
			$path = "/templates/leave/leaveRequestList.php";
		} else {
			$tmpObj = $tmpObj->retriveLeaveEmployee($this->getId());		
			$path = "/templates/leave/leaveList.php";
		}
		
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
		$tmpObj = $tmpObj->retriveLeaveRequestsSupervisor($this->getId());
		
		$path = "/templates/leave/leaveList.php";
		
		$template = new TemplateMerger($tmpObj, $path);
		
		$modifiers[] = "SUP";
		
		$template->display($modifiers);		
	}
	
	private function _cancelLeave() {
		$tmpObj = $this->getObjLeave();
		
		return $tmpObj->cancelLeave($this->getId());		
	}

	public function redirect($message=null, $url = null) {
		if (isset($message)) {
			
			preg_replace('/[&|?]+id=[A-Za-z0-9]*/', "", $_SERVER['HTTP_REFERER']);
			
			if (preg_match('/&/', $_SERVER['HTTP_REFERER']) > 0) {
				$message = "&message=".$message;
				$url = preg_split('/(&||\?)message=[A-Za-z0-9]*/', $_SERVER['HTTP_REFERER']);				
			} else {
				$message = "?message=".$message;
			}
			
			if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
				$id = "&id=".$_REQUEST['id'];
			} else {
				$id="";
			}
		} else {
			if (isset($_REQUEST['id']) && !empty($_REQUEST['id']) && (preg_match('/&/', $_SERVER['HTTP_REFERER']) > 0)) {
				$id = "&id=".$_REQUEST['id'];
			} else if (preg_match('/&/', $_SERVER['HTTP_REFERER']) == 0){
				$id = "?id=".$_REQUEST['id'];			
			} else {
				$id="";
			}
		}
		
		header("Location: ".$url[0].$message.$id);
	}
	
	public function addLeave() {
		$tmpObj = $this->getObjLeave();
		$res = $tmpObj->applyLeaveRequest();
		
		if ($res) {
			$message="";
		} else {
			$message="FAILURE";
		}
		
		return $message;
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
	private function _displayLeaveSummary($modifier='display', $year = null) {		
		if (!isset($year)) {
			$year = date('Y');
		}		
		
		$auth = $this->_authenticateViewLeaveSummary();
		
		$modifier = array($modifier, $auth, $year);
		
		$empInfoObj = new EmpInfo();
		
		$tmpObj = $this->getObjLeave();
		$tmpObjX[] = $tmpObj->fetchLeaveSummary($this->getId(), $year);
		$tmpObjX[] = $empInfoObj->filterEmpMain($this->getId());
			
		$path = "/templates/leave/leaveSummary.php";
		
		$template = new TemplateMerger($tmpObjX, $path);
		
		$template->display($modifier);
	}
	
	/**
	 * Checks whether the user is allowed to
	 * view the particular employee's Leave Summary
	 *
	 */
	private function _authenticateViewLeaveSummary() {
		$id = $this->getId();
		
		if (($_SESSION['isAdmin'] !== 'Yes') && ($id !== $_SESSION['empID'])){
			
			$objReportTo = new EmpRepTo();
			
			$subordinates = $objReportTo->getEmpSub($_SESSION['empID']);
					
			if (!array_search($id, $subordinates[0])) {
				trigger_error("Unauthorized access", E_USER_NOTICE);
			} else {
				return "supervisor";
			}
		} else if ($_SESSION['isAdmin'] === 'Yes') {
			return "admin";
		} else if ($id === $_SESSION['empID']) {
			return "self";
		}
		
		trigger_error("Unauthorized access", E_USER_NOTICE);
	}
	
	/**
	 * Checks whether the user is allowed to
	 * change the particular employee's Leave status
	 *
	 */
	private function _authenticateChangeLeaveStatus() {
		$status = $this->getObjLeave()->getLeaveStatus();
		
		if ($status != $this->getObjLeave()->statusLeaveCancelled) {
			$id = $this->getObjLeave()->getEmployeeId();
		}		
		
		if (isset($id)) {
			
			$objReportTo = new EmpRepTo();
			
			$subordinates = $objReportTo->getEmpSub($_SESSION['empID']);			
			
			$subordinate = false;
			
			for ($i=0; $i < count($subordinates); $i++) {
				if (in_array($id, $subordinates[$i])) {
					$subordinate = true;
					break;
				}
			}			
			
			if (!$subordinate) {
				trigger_error("Unauthorized access", E_USER_NOTICE);				
			}
		} else if (isset($id) && ($id === $_SESSION['empID'])) {
			trigger_error("Unauthorized access1", E_USER_NOTICE);
		}
	}
	
	public function displayLeaveTypeDefine() {
		
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
			$message="";
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

	
	public function displayLeaveEditTypeDefine(){
		
		$tmpObj = new LeaveType();
				
		$this->setObjLeave($tmpObj);
		
		$tmpOb = $tmpObj->retriveLeaveType($this->getId());
		
		$path = "/templates/leave/leaveTypeDefine.php";
		
		$template = new TemplateMerger($tmpOb, $path);
		
		$template->display();
	}
	
	public function editLeaveType() {
		
		$tmpObj = $this->getObjLeave();
		
		$res = $tmpObj->editLeaveType();
		
		if ($res) {
			$message="FAILURE";
		} else {
			$message="";
		}
		return $message;
	}
	
	public function LeaveTypeDelete() {
		
		$tmpObj = $this->getObjLeave();

		$res = $tmpObj->deleteLeaveType();
		
		if ($res) {
			$message="";
		} else {
			$message="FAILURE";
		}
		
		return $message;
	}

	
	public function saveLeaveQuota() {
		$tmpObj = $this->getObjLeave();
		
		$res = $tmpObj->editLeaveQuota();
		
		if ($res) {
			$message="";
		} else {
			$message="FAILURE";
		}
		
		return $message;
	}
	
	/**
	 * Display select employee
	 *
	 * @param String $action
	 */
	public function viewSelectEmployee($action) {
		$tmpObj = new Leave();		
		$this->setObjLeave($tmpObj);
		
		$tmpOb[] = $tmpObj->getLeaveYears();
				
		if ($this->getAuthorize()->isAdmin()) {
			$empObj = new EmpInfo();
			$tmpOb[] = $empObj->getListofEmployee();
		} else {
			$repObj = new EmpRepTo();
			$tmpOb[] = $repObj->getEmpSubDetails($_SESSION['empID']);
		}
			
		$path = "/templates/leave/leaveSelectEmployeeAndYear.php";
		
		$template = new TemplateMerger($tmpOb, $path);
		
		$template->display($action);
	}
	
	private function _viewLeavesTaken($year = null) {
		$authorizeObj  = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);
		
			
		if ($authorizeObj->isAdmin()) {
			
			$employeeId = $this->getId();
			$tmpObj = $this->getObjLeave();
			
			$empInfoObj = new EmpInfo();

			$res[] = $tmpObj->retrieveTakenLeave($year, $employeeId);
			$res[] = $empInfoObj->filterEmpMain($this->getId());
			
			$path = "/templates/leave/leaveList.php";
		
			$template = new TemplateMerger($res, $path);
			
			$modifiers[] = "Taken";
			$modifiers[] = $year;
			
			$template->display($modifiers);
			
		} else {
			trigger_error("Unauthorized access1", E_USER_NOTICE);
		}
	}
	
	
	/**
	 * Holidays and week end list viewing
	 *
	 * @param String $modifier
	 */
	public function viewHoliday($modifier="specific") {
		$this->_authenticateViewHoliday();
		switch ($modifier) {
			case "specific" : $this->_displaySpecificHoliday($modifier);
							 break;
			case "weekend" : $this->_displayWeekend();
							 break;
		}
	}
	
	public function addHoliday() {
		$this->_authenticateViewHoliday();
		
		$this->getObjLeave()->add();	
	}
	
	/**
	 * Wrpper to edit holidays
	 *
	 * @param unknown_type $modifier
	 */
	public function editHoliday($modifier="specific") {
		$this->_authenticateViewHoliday();
		
		switch ($modifier) {
			case "specific" : $this->getObjLeave()->edit();
							  break;
			case "weekend" 	: $this->getObjLeave()->editDay();
							  break;
		}			
	}	
	
	private function _displaySpecificHoliday($modifier) {		
		if (!isset($year)) {
			$year = date('Y');
		}		
		
		$modifier = array($modifier, $year);
				
		$tmpObj = new Holidays();
		
		$tmpObjX = $tmpObj->listHolidays();
					
		$path = "/templates/leave/specificHolidaysList.php";
		
		$template = new TemplateMerger($tmpObjX, $path);
		
		$template->display($modifier);
		
	}
	
	private function _displayWeekend() {
		
	}
	
	private function _authenticateViewHoliday() {		
		$res = $this->getAuthorize()->isAdmin();
		
		if ($res) {			
			return $res;
		}
		
		trigger_error("Unauthorized access", E_USER_NOTICE);
	}
	
	public function holidaysDelete() {
		$this->getObjLeave()->delete();
		
		return "";
	}
	
	public function displayDefineHolidays($modifier="specific", $edit=false) {
		$this->_authenticateViewHoliday();
		
		$record = null;
		if ($edit) {
			$holidayObj = new Holidays();
			
			$record = $holidayObj->fetchHoliday($this->getId());
		}
		
		switch ($modifier) {
			case "specific"	:	$path = "/templates/leave/specificHolidaysDefine.php";
								break;
			case "weekend"	:	$path = "/templates/leave/weekendHolidaysDefine.php";
								$weekendsObj = new Weekends();
								$record = $weekendsObj->fetchWeek();
								break;
		}
		
		$template = new TemplateMerger($record, $path);
		
		$modifier = $edit;
		
		$template->display($modifier);
	}
	
}
?>