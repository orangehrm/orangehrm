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

//the model objects are included here

require_once ROOT_PATH . '/lib/models/leave/Leave.php';
require_once ROOT_PATH . '/lib/models/leave/LeaveType.php';
require_once ROOT_PATH . '/lib/models/leave/LeaveQuota.php';
require_once ROOT_PATH . '/lib/models/leave/LeaveSummary.php';
require_once ROOT_PATH . '/lib/models/leave/Holidays.php';
require_once ROOT_PATH . '/lib/models/leave/Weekends.php';
require_once ROOT_PATH . '/lib/models/leave/mail/MailNotifications.php';
require_once ROOT_PATH . '/lib/models/leave/LeaveTakenRequests.php';
require_once ROOT_PATH . '/lib/models/leave/LeaveRequests.php';

require_once ROOT_PATH . '/lib/models/hrfunct/EmpRepTo.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpInfo.php';

require_once ROOT_PATH . '/lib/common/TemplateMerger.php';
require_once ROOT_PATH . '/lib/common/authorize.php';
require_once ROOT_PATH . '/lib/common/Config.php';
require_once ROOT_PATH . '/lib/utils/CSRFTokenGenerator.php';

class LeaveController {

	private $indexCode;
	private $id;
	private $leaveTypeId;
	private $objLeave;
	private $authorize;

	public function setId($id) {
		$this->id = $id;
	}

	public function getId() {
		return $this->id;
	}

	public function setLeaveTypeId($leaveTypeId) {
		$this->leaveTypeId = $leaveTypeId;
	}

	public function getLeaveTypeId() {
		return $this->leaveTypeId;
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

	public function viewLeaves($modifier="employee", $year=null, $details=false, $sortField = null, $sortOrder = null, $pageNO=null) {

		if ($details) {
			switch ($modifier) {
				case "employee": $this->setObjLeave(new Leave());
								 $this->_viewLeavesEmployee($details);
								 break;
				case "admin": 	$this->setObjLeave(new Leave());
								$this->_viewLeavesAdmin($details);
								break;
				case "suprevisor": $this->setObjLeave(new Leave());
								 $this->_viewLeavesSupervisor($details);
								 break;
				case "taken"	: $this->setObjLeave(new Leave());
								  $this->_viewLeavesTaken($year, $details);
								 break;
				case "summary" : $this->setObjLeave(new LeaveSummary());
								 $this->_displayLeaveSummary("display", $year, $details, $sortField, $sortOrder, $pageNO);
								 break;
			}
		} else {
			switch ($modifier) {
				case "employee": $this->setObjLeave(new LeaveRequests());
								 $this->_viewLeavesEmployee($details);
								 break;
				case "admin": 	$this->setObjLeave(new LeaveRequests());
								$this->_viewLeavesAdmin($details);
								break;

				case "suprevisor": 	$this->setObjLeave(new LeaveRequests());
								 	$this->_viewLeavesSupervisor($details);
								 	break;
				case "taken"	: $this->setObjLeave(new LeaveRequests());
								 $this->_viewLeavesTaken($year, $details);
								 break;
				case "summary" : $this->setObjLeave(new LeaveSummary());
								 $this->_displayLeaveSummary("display", $year, null, $sortField, $sortOrder);
								 break;
			}
		}
	}

	public function editLeaveTypes($leaveTypes) {

		$changedCount = 0;

		if (isset($leaveTypes)) {

			// Test for duplicate names
			$leaveTypeNames = array();
			foreach ($leaveTypes as $leaveType) {

				$name = $leaveType->getLeaveTypeName();
				if (in_array($name, $leaveTypeNames)) {
					$this->redirect("DUPLICATE_LEAVE_TYPE_ERROR");
					return;
				}
				$leaveTypeNames[] = $name;
			}

         $screenParam = array('leavecode' => $_GET['leavecode'], 'action' => 'Leave_Type_Summary');
         $tokenGenerator = CSRFTokenGenerator::getInstance();
         $tokenGenerator->setKeyGenerationInput($screenParam);
         $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
         
			foreach ($leaveTypes as $leaveType) {
				$this->setObjLeave($leaveType);
            if($token == $_POST['token']) {
               $res = $this->editLeaveType();
            } else {
               $res = false;
            }
				if ($res === false) {
					$this->redirect("LEAVE_TYPE_EDIT_ERROR");
					return;
				} else {
					$changedCount += $res;
				}
			}
		}

		if ($changedCount > 0) {
			$this->redirect("LEAVE_TYPE_EDIT_SUCCESS");
		} else {
			$this->redirect("NO_CHANGES_TO_SAVE_WARNING");
		}
	}

	public function editLeaves($modifier="summary", $year=null, $esp=null, $sortField = null, $sortOrder = null, $pageNO=null) {
      switch ($modifier) {
			case "summary" : $this->setObjLeave(new LeaveSummary());
							 $this->_displayLeaveSummary("edit", $year, $esp, $sortField, $sortOrder, $pageNO);
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
			case "cancel":
						$res = $this->_cancelLeave();
						break;
			case "change":
						$res = $this->_changeLeaveStatus();
						break;
		}

		if ($res) {
			$message=true;
		} else {
			$message=false;
		}

		return $message;
	}

	private function _changeLeaveStatus() {
		$this->_authenticateChangeLeaveStatus();

		$tmpObj = $this->getObjLeave();

		return $tmpObj->changeLeaveStatus($this->getId(), $this->getObjLeave()->getLeaveComments());
	}

	/**
	 * Checks whether the id is untampered
	 *
	 */
	private function _authenticateViewLeaveDetails() {

		if ($_REQUEST['digest'] != md5($this->getId().SALT)) {
			trigger_error("Unauthorized access", E_USER_NOTICE);
		}
	}

	private function _viewLeavesEmployee($details) {
		$tmpObj = $this->getObjLeave();
      $screenParam = array('leavecode' => $_GET['leavecode'], 'action' => $_GET['action']);
      if(isset($_GET['id'])) {
         $screenParam['id'] = $_GET['id'];
      }
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
      
		if (!$details) {
			$tmpObj = $tmpObj->retriveLeaveRequestsEmployee($this->getId());
			$path = "/templates/leave/leaveRequestList.php";
		} else {
			$this->_authenticateViewLeaveDetails();
			$tmpObj = $tmpObj->retrieveLeave($this->getId());
			$path = "/templates/leave/leaveList.php";
		}
      $tmpObj['token'] = $token;
		$template = new TemplateMerger($tmpObj, $path);

		$modifiers[] = "MY";
		$template->display($modifiers);
	}

	/**
	 * Cancelled leave notification
	 *
	 * @param mixed $obj
	 * @param boolean $request
	 */
	public function sendCancelledLeaveNotification($obj, $request=false) {
		$this->_sendChangedLeaveNotification($obj, $request, MailNotifications::MAILNOTIFICATIONS_ACTION_CANCEL);
	}

	/**
	 * Workhorse function for sendChangedLeaveNotification and sendCancelledLeaveNotification
	 *
	 * @param mixed $obj
	 * @param boolean $request
	 * @param String $action
	 */
	private function _sendChangedLeaveNotification($obj, $request=false, $action) {
		$mailNotificaton = new MailNotifications();

		if ($request) {
			$mailNotificaton->setLeaveRequestObj($obj);
		} else {
			$mailNotificaton->setLeaveObjs($obj);
		}

		$mailNotificaton->setAction($action);
		$mailNotificaton->send();
	}

	/**
	 * Sending mail notification when leave status change
	 *
	 * @param mixed $objs
	 * @param boolean $request
	 * @return boolean
	 */
	public function sendChangedLeaveNotification($objs, $request=false) {
		if (!isset($objs)) {
			return false;
		}

		$approveObj = null;
		$rejectedObj = null;
		$cancelledObj = null;

		if ($request) {
			switch ($objs->getLeaveStatus()) {
				case Leave::LEAVE_STATUS_LEAVE_APPROVED : $approveObj = $objs;
														  break;
				case Leave::LEAVE_STATUS_LEAVE_REJECTED : $rejectedObj = $objs;
														  break;
				case Leave::LEAVE_STATUS_LEAVE_CANCELLED : $cancelledObj = $objs;
														  break;

			}
		} else {
			if (!is_array($objs)) {
				return false;
			}
			foreach ($objs as $obj) {
				if ($obj && is_a($obj, 'Leave')) {
					switch ($obj->getLeaveStatus()) {
						case Leave::LEAVE_STATUS_LEAVE_APPROVED : $approveObj[] = $obj;
																  break;
						case Leave::LEAVE_STATUS_LEAVE_REJECTED : $rejectedObj[] = $obj;
																  break;
						case Leave::LEAVE_STATUS_LEAVE_CANCELLED : $cancelledObj[] = $obj;
																  break;

					}
				}
			}
		}

		if (!empty($approveObj)) {
			$this->_sendChangedLeaveNotification($approveObj, $request, MailNotifications::MAILNOTIFICATIONS_ACTION_APPROVE);
		}

		if (!empty($rejectedObj)) {
			$this->_sendChangedLeaveNotification($rejectedObj, $request, MailNotifications::MAILNOTIFICATIONS_ACTION_REJECT);
		}

		if (!empty($cancelledObj)) {

			// check and see if supervisor is doing the cancellation.
			$authorize = $this->authorize;
			if ($request) {
				$empId = $cancelledObj->getEmployeeId();
			} else if (is_array($cancelledObj) && count($cancelledObj) > 0) {
				$empId = $cancelledObj[0]->getEmployeeId();
			}

			$loggedInEmpId = $authorize->getEmployeeId();

			if ($authorize->isAdmin() || (!empty($empId) && !empty($loggedInEmpId) && ($empId != $authorize->getEmployeeId()) &&
					$authorize->isSupervisor())) {

				$action = MailNotifications::MAILNOTIFICATIONS_ACTION_SUPERVISOR_CANCEL;
			} else {
				$action = MailNotifications::MAILNOTIFICATIONS_ACTION_CANCEL;
			}

			$this->_sendChangedLeaveNotification($cancelledObj, $request, $action);
		}

		return true;
	}

	public function sendAssignedLeaveNotification($obj, $action) {

		$mailObj = new LeaveRequests();

		$mailObj->setLeaveRequestId($obj->getLeaveRequestId());
		$mailObj->setLeaveStatus($obj->getLeaveStatus());
		$mailObj->setLeaveComments($obj->getLeaveComments());
		$mailObj->setEmployeeId($obj->getEmployeeId());

		$mailNotificaton = new MailNotifications();
		$mailNotificaton->setLeaveRequestObj($mailObj);
		$mailNotificaton->setAction($action);
		$result = $mailNotificaton->send();
		return $result;
	}

	private function _viewLeavesAdmin($details) {

		if ($_SESSION['isAdmin'] == 'No') {
		    die('You are not authorized to view this page');
		}

      $screenParam = array('leavecode' => $_GET['leavecode'], 'action' => $_GET['action']);
      if(isset($_GET['id'])) {
         $screenParam['id'] = $_GET['id'];
      }
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

		$tmpObj = $this->getObjLeave();

		/* Show only leave with requested statuses, default to approved leave
		 * Save preferences in session.
		 *
		 * TODO: This huge manipulation of POST and SESSION is not that appropriate here.
		 */
		if (isset($_GET['NewQuery'])) {
			unset($_SESSION['leaveStatusFilters']);
			unset($_SESSION['leaveListFromDate']);
			unset($_SESSION['leaveListToDate']);
		}

		/* Setting leave status */
		
		if (isset($_POST['leaveStatus'])) {
			$leaveStatuses = $_POST['leaveStatus'];
		} else if (isset($_SESSION['leaveStatusFilters'])) {
			$leaveStatuses = $_SESSION['leaveStatusFilters'];
		} else {
			$leaveStatuses = array(Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL);
		}
		$_SESSION['leaveStatusFilters'] = $leaveStatuses;

		$fromDate = isset($_POST['txtFromDate'])?$_POST['txtFromDate']:
			(isset($_SESSION['leaveListFromDate']) ? $_SESSION['leaveListFromDate']:$this->_generateStartEndDate('start'));
		$toDate = isset($_POST['txtToDate'])?$_POST['txtToDate']:
			(isset($_SESSION['leaveListToDate']) ? $_SESSION['leaveListToDate']:$this->_generateStartEndDate('end'));

		$_SESSION['leaveListFromDate'] = $fromDate;
		$_SESSION['leaveListToDate'] = $toDate;
		
		if (isset($_POST['pageNo'])) {
		    $pageNo = $_POST['pageNo'];
		} else {
		    $pageNo = 1;
		}
		
		$modifiers['recordsCount'] = 0;
		$limit = ($pageNo*50-50).', 50';

		if (!$details) {
			$modifiers['recordsCount'] = $tmpObj->countLeaveRequestsAdmin($leaveStatuses, $fromDate, $toDate);
			$tmpObj = $tmpObj->retriveLeaveRequestsAdmin($leaveStatuses, $fromDate, $toDate, $limit);
			$path = "/templates/leave/leaveRequestList.php";
		} else {
			$this->_authenticateViewLeaveDetails();
			$tmpObj = $tmpObj->retrieveLeave($this->getId());
			$path = "/templates/leave/leaveList.php";
		}

		$template = new TemplateMerger($tmpObj, $path);

		$modifiers[] = "ADMIN";
		$modifiers['leave_statuses'] = $leaveStatuses;
      $modifiers['token'] = $token;
		$modifiers['from_date'] = $fromDate;
		$modifiers['to_date'] = $toDate;
		$modifiers['pageNo'] = $pageNo;

      if($_GET['action'] == "Leave_FetchDetailsAdmin") {
         $modifiers['actionFlag'] = "admin";
      }
		$template->display($modifiers);

	}
	
	private function _generateStartEndDate($state = 'start') {
	    
	    if ($state == 'start') {
	        return date('Y-m-d');
	    } else {
	        return date('Y-m-d', time()+30*24*3600);
	    }
	    
	}

	/**
	 * Suprevisor's view of the leaves of subordinates
	 *
	 * @return void
	 */
	private function _viewLeavesSupervisor($details) {

		if (isset($_POST['leaveStatus'])) {
			$leaveStatuses = $_POST['leaveStatus'];
		} else if (isset($_SESSION['leaveStatusFilters'])) {
			$leaveStatuses = $_SESSION['leaveStatusFilters'];
		} else {
			$leaveStatuses = array(Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL);
		}
		$_SESSION['leaveStatusFilters'] = $leaveStatuses;

      $screenParam = array('leavecode' => $_GET['leavecode'], 'action' => $_GET['action']);
      if(isset($_GET['id'])) {
         $screenParam['id'] = $_GET['id'];
      }
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));


		$fromDate = isset($_POST['txtFromDate'])?$_POST['txtFromDate']:
			(isset($_SESSION['leaveListFromDate']) ? $_SESSION['leaveListFromDate']:$this->_generateStartEndDate('start'));
		$toDate = isset($_POST['txtToDate'])?$_POST['txtToDate']:
			(isset($_SESSION['leaveListToDate']) ? $_SESSION['leaveListToDate']:$this->_generateStartEndDate('end'));

		$_SESSION['leaveListFromDate'] = $fromDate;
		$_SESSION['leaveListToDate'] = $toDate;
		 
		if (isset($_POST['pageNo'])) {
		    $pageNo = $_POST['pageNo'];
		} else {
		    $pageNo = 1;
		}
		
		$modifiers['recordsCount'] = 0;
		$limit = ($pageNo*50-50).', 50';
		 
		$tmpObj = $this->getObjLeave();

		if (!$details) {
			$modifiers['recordsCount'] = $tmpObj->countLeaveRequestsSupervisor($this->getId(), $leaveStatuses, $fromDate, $toDate);
			$tmpObj = $tmpObj->retriveLeaveRequestsSupervisor($this->getId(), $leaveStatuses, $fromDate, $toDate, $limit);
			$path = "/templates/leave/leaveRequestList.php";
		} else {
			$this->_authenticateViewLeaveDetails();
			$tmpObj = $tmpObj->retrieveLeave($this->getId());
			$path = "/templates/leave/leaveList.php";
		}

		$template = new TemplateMerger($tmpObj, $path);

		$modifiers[] = "SUP";

		$modifiers['leave_statuses'] = $leaveStatuses;
		$modifiers['from_date'] = $fromDate;
		$modifiers['to_date'] = $toDate;
		$modifiers['pageNo'] = $pageNo;
      $modifiers['token'] = $token;
      
      if($_GET['action'] == "Leave_FetchDetailsSupervisor") {
         $modifiers['actionFlag'] = "supervisor";
      }
		$template->display($modifiers);
	}

	private function _cancelLeave() {
		$tmpObj = $this->getObjLeave();
		$tmpObj->setLeaveStatus(Leave::LEAVE_STATUS_LEAVE_CANCELLED);
		return $tmpObj->cancelLeave($this->getId(), $tmpObj->getLeaveComments());
	}

	public function redirectToLeaveApplyPage($admin, $message = null, $id = null) {
		$action = ($admin) ? "Leave_Apply_Admin_view" : "Leave_Apply_view";
		$url = "./CentralController.php?leavecode=Leave&action=" . $action;

		if (isset($message)) {
			$url .= "&message=" . $message;
		}

		if (isset($_REQUEST['id']) && !empty($_REQUEST['id']) && !is_array($_REQUEST['id'])) {
			$id = $_REQUEST['id'];
		}

		if (isset($id)) {
			$url .= "&id=" . $id;
		}

		header("Location: {$url}");
	}

	public function redirect($message=null, $url = null, $id = null, $cust=null) {
		if (isset($message)) {

			preg_replace('/[&|?]+id=[A-Za-z0-9]*/', "", $_SERVER['HTTP_REFERER']);

			if (preg_match('/&/', $_SERVER['HTTP_REFERER']) > 0) {
				$message = "&message=".$message;
				$url = preg_split('/(&||\?)message=[A-Za-z0-9]*/', $_SERVER['HTTP_REFERER']);
			} else {
				$message = "?message=".$message;
			}

			if (isset($_REQUEST['id']) && !empty($_REQUEST['id']) && !is_array($_REQUEST['id'])) {
				$id = "&id=".$_REQUEST['id'];
			} else if (isset($id)) {
				$id="&id={$id}";
			} else {
				$id="";
			}
		} else {
			if (isset($_REQUEST['id']) && !empty($_REQUEST['id']) && (preg_match('/&/', $_SERVER['HTTP_REFERER']) > 0)) {
				$id = "&id=".$_REQUEST['id'];
			} else if (isset($_REQUEST['id']) && !empty($_REQUEST['id']) && (preg_match('/&/', $_SERVER['HTTP_REFERER']) == 0)) {
				$id = "?id=".$_REQUEST['id'];
			} else {
				$id="";
			}
		}

		if (isset($cust)) {
			$url[0].=$cust;
		}

		header("Location: {$url[0]}{$message}{$id}");
	}

	public function addLeave() {

		$tmpObj = $this->getObjLeave();
		$fromDate = $tmpObj->getLeaveFromDate();
		$toDate = $tmpObj->getLeaveToDate();
		$authorizeObj = $this->authorize;

		if($authorizeObj->isESS() && !$authorizeObj->isSupervisor()){

			$fromDateArray = explode("-" , $fromDate)  ;
			$toDateArray = explode("-" , $toDate);

			if($fromDateArray[0] == $toDateArray[0]){

				$tmpLeaveQuota = new LeaveQuota();
				$tmpLeaveQuota->setEmployeeId($tmpObj->getEmployeeId());
				$tmpLeaveQuota->setYear($fromDateArray[0]);
				$tmpLeaveQuota->setLeaveTypeId($tmpObj->getLeaveTypeId());

				if($tmpLeaveQuota->isBalanceZero()){
					$message = "BALANCE_ZERO";
					return $message;
				}

			}else{

				$tmpLeaveQuota = new LeaveQuota();
				$tmpLeaveQuota->setEmployeeId($tmpObj->getEmployeeId());
				$tmpLeaveQuota->setYear($fromDateArray[0]);
				$tmpLeaveQuota->setLeaveTypeId($tmpObj->getLeaveTypeId());

				$tmpLeaveQuota1 = new LeaveQuota();
				$tmpLeaveQuota1->setEmployeeId($tmpObj->getEmployeeId());
				$tmpLeaveQuota1->setYear($toDateArray[0]);
				$tmpLeaveQuota1->setLeaveTypeId($tmpObj->getLeaveTypeId());

				if($tmpLeaveQuota->isBalanceZero() && $tmpLeaveQuota1->isBalanceZero()){
					$message = "BALANCE_ZERO";
					return $message;
				}

			}
		}

		$tmpLeave = new Leave();
		$duplicateList = $tmpLeave->retrieveDuplicateLeave($tmpObj->getEmployeeId(), $fromDate, $toDate);

		$rejects = array();
		$duplicates = array();

		if (!empty($duplicateList)) {

			foreach($duplicateList as $dup) {
				if ($dup->getLeaveStatus() == Leave::LEAVE_STATUS_LEAVE_REJECTED) {
					$rejects[] = $dup;
				} else {
					$duplicates[] = $dup;
				}
			}

			/* Only rejected leave conflicts with current leave request */
			if (count($duplicates) == 0) {

				/* Change status of rejected leave requests to cancelled */
				foreach($rejects as $rej) {
					$rej->setLeaveStatus(Leave::LEAVE_STATUS_LEAVE_CANCELLED);
					$res = $rej->changeLeaveStatus();
					if (!$res) {
						return "APPLY_FAILURE";
					}
				}

			} else {

				/* If multiple day leave request, we don't need to check leave times.
				 * Just throw an exception.
				 */
				if ($fromDate != $toDate) { 
					throw new DuplicateLeaveException($duplicates);
				} else {
					/* A single day leave request. We need to check leave times. */

					// Count total hours and check for greater than workshift hours
					$shift = Leave::LEAVE_LENGTH_FULL_DAY;
					$workShift = Workshift::getWorkshiftForEmployee($tmpObj->getEmployeeId());
					if (isset($workShift)) {
						$shift = $workShift->getHoursPerDay();
					}

					$totalHours = 0;
					foreach ($duplicates as $dup) {
						$totalHours += $dup->getLeaveLengthHours();
					}

					if ($totalHours + $tmpObj->getLeaveLengthHours() > $shift) {
						throw new DuplicateLeaveException($duplicates);
					}

					/* Check for overlapping leave times*/
					$startTime = $tmpObj->getStartTime();
					$endTime = $tmpObj->getEndTime();
					if (!empty($startTime) && !empty($endTime)) {

						foreach ($duplicates as $dup) {
							$dupStartTime = $dup->getStartTime();
							$dupEndTime = $dup->getEndTime();
							if (!empty($dupStartTime) && !empty($dupEndTime)) {
								$overlap = CommonFunctions::checkTimeOverlap($startTime, $endTime, $dupStartTime, $dupEndTime);
								if ($overlap) {
									throw new DuplicateLeaveException($duplicates);
								}
							}
						}
					}

					/* Show warning (Only if we haven't shown the warning before (in previous requst) for this date')*/
					if (!isset($_POST['confirmDate']) || ($_POST['confirmDate'] != $fromDate)) {
						throw new DuplicateLeaveException($duplicates, true);
					}
				}
			}
		}

		$res = $tmpObj->applyLeaveRequest();

		$mailNotificaton = new MailNotifications();

		$mailNotificaton->setLeaveRequestObj($tmpObj);

		$mailNotificaton->setAction(MailNotifications::MAILNOTIFICATIONS_ACTION_APPLY);
		$mailNotificaton->send();
		$message = ($res) ? "APPLY_SUCCESS" : "APPLY_FAILURE";
		return $message;
	}

	public function adminApproveLeave() {
		$tmpObj = $this->getObjLeave();
		$tmpObj->setLeaveStatus(Leave::LEAVE_STATUS_LEAVE_APPROVED);
		$res = $tmpObj->changeLeaveStatus(null, true);
		$message = ($res) ? "APPROVE_SUCCESS" : "APPROVE_FAILURE";
		return $message;
	}

	/**
	 * Display leave information
	 *
	 * @param boolean $admin Show admin view or ess user view
	 * @param Exception $exception Exception class (used to display any errors from previous apply/assign)
	 */
	public function displayLeaveInfo($admin=false, $exception = null) {
		$authorizeObj = $this->authorize;
		$tmpObjs['isEss'] = false;

		if ($admin) {
			if ($authorizeObj->getIsAdmin() == 'Yes') {
                $empObj = new EmpInfo();
				$tmpObjs[0] = EmpInfo::getEmployeeSearchList();
			} else if ($authorizeObj->isSupervisor()) {
				$empRepToObj = new EmpRepTo();
				$tmpObjs[0] = $this->_prepareSubordinateList($empRepToObj->getEmpSubDetails($authorizeObj->getEmployeeId()));
			}

			$roles = array(authorize::AUTHORIZE_ROLE_ADMIN, authorize::AUTHORIZE_ROLE_SUPERVISOR);
			$role = $authorizeObj->firstRole($roles);

			$previousLeave = null;

			if (isset($_GET['id'])) {
				$leaveObj = new Leave();

				$previousLeaves = $leaveObj->retrieveLeave($_GET['id']);
				$previousLeave = $previousLeaves[0];

				if (($authorizeObj->getIsAdmin() != 'Yes') && $authorizeObj->isSupervisor() && !($authorizeObj->isTheSupervisor($previousLeave->getEmployeeId()))) {
					$previousLeave=null;
				}
			}

			$this->setId($_SESSION['empID']);
			$tmpObj = new LeaveType();
			$tmpObjs[1] = $tmpObj->fetchLeaveTypes();
			$tmpObjs[2] = $role;
			$tmpObjs[3] = $previousLeave;
			$tmpObjs['allEmpWorkshits'] = Workshift::getWorkshiftForAllEmployees();
		} else {

			$this->setId($_SESSION['empID']);
			$tmpObj = new LeaveQuota();
			$tmpObj->setYear(date('Y'));
			$tmpObjs[1] = $tmpObj->fetchLeaveQuota($this->getId());

			$workShift = Workshift::getWorkshiftForEmployee($this->getId());
			$shiftLength = isset($workShift) ? $workShift->getHoursPerDay() : Leave::LEAVE_LENGTH_FULL_DAY;
			$tmpObjs['shiftLength'] = $shiftLength;
			$tmpObjs['isEss'] = true;

		}

      $screenParam = array('leavecode' => $_GET['leavecode'], 'action' => $_GET['action']);
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $tmpObjs['token'] = $tokenGenerator->getCSRFToken(array_keys($screenParam));

		$this->setObjLeave($tmpObjs);

		$path = "/templates/leave/leaveApply.php";

		if (!empty($exception)) {
			$tmpObjs['exception'] = $exception;
		}

		$template = new TemplateMerger($tmpObjs, $path);
		$template->display();
	}

	public function gotoLeaveHomeSupervisor() {
		$tmpObj = new LeaveRequests();

		$tmpObj = $tmpObj->retriveLeaveRequestsSupervisor($this->getId());

		if ($tmpObj == null) {
			$this->displayLeaveTypeSummary();
			return true;
		}

		$this->viewLeaves("suprevisor");
		return true;
	}

	public function copyLeaveQuotaFromLastYear($currYear) {
		if ($_SESSION['isAdmin'] !== 'Yes') {
			trigger_error("Unauthorized access", E_USER_NOTICE);
		}

		$leaveQuotaObj = new LeaveQuota();

		$res = false;

		if ($this->_validToCopyQuotaFromLastYear($currYear)) {
			$res = $leaveQuotaObj->copyQuota($currYear-1, $currYear);
		}

		if ($res) {
			/*
			 * This part was changed to fix the bug 1927022 - Supervisor approve leave and apply
			 * leave, in same screen
			 *
			 * In case of broken functionality, this need to be changed. The old code was:
			 *
			 * $this->redirect("LEAVE_QUOTA_COPY_SUCCESS", null, null, "&year=$currYear&id=0");
			 *
			 */
			$this->redirect(null, array("?leavecode=Leave&action=Leave_Summary&year=$currYear&id=0&message=LEAVE_QUOTA_COPY_SUCCESS"));
		} else {
			/*
			 * This part was changed to fix the bug 2030001 - Leave:Copy leave quota misbehaves
			 * Seems like the redirect method of this controller is not working properly
			 * when called as in the earlier statement, causing errors in IE
			 *
			 * Earlier statement was:
			 * $this->redirect("LEAVE_QUOTA_COPY_FAILURE", null, null, "&year=$currYear&id=0");
			 *
			 */
			$this->redirect(null, array("?leavecode=Leave&action=Leave_Summary&year=$currYear&id=0&message=LEAVE_QUOTA_COPY_FAILURE"));
		}
	}

	public function copyLeaveBroughtForwardFromLastYear($currYear) {
		if ($_SESSION['isAdmin'] !== 'Yes') {
			trigger_error("Unauthorized access", E_USER_NOTICE);
		}

		$leaveQuotaObj = new LeaveQuota();

		$result = $leaveQuotaObj->copyLeaveBroughtForward($currYear-1, $currYear);

		$redirectionPrefix = "?leavecode=Leave&action=Leave_Summary&year=$currYear&id=0&message=";
		if ($result) {
			$this->redirect(null, array($redirectionPrefix . "LEAVE_BROUGHT_FORWARD_COPY_SUCCESS"));
		} else {
			$this->redirect(null, array($redirectionPrefix . "LEAVE_BROUGHT_FORWARD_COPY_FAILURE"));
		}

	}

	private function _validToCopyQuotaFromLastYear($currYear) {
		if ($_SESSION['isAdmin'] !== 'Yes') {
			return false;
		}

		$leaveQuotaObj = new LeaveQuota();

		$leaveQuotaObj->setYear($currYear);
		$currYearQuota = $leaveQuotaObj->fetchLeaveQuota(0);

		$leaveQuotaObj->setYear($currYear-1);
		$prevYearQuota = $leaveQuotaObj->fetchLeaveQuota(0);

		$copyQuota = false;

		if ((count($currYearQuota) == 0) && (count($prevYearQuota) > 0)) {
			$copyQuota = true;
		}

		return $copyQuota;
	}

	private function _validToCopyBroughtForwardFromLastYear() {
		if ($_SESSION['isAdmin'] !== 'Yes') {
			return false;
		}

		$broughtForward = new LeaveQuota();

		if ($broughtForward->checkBroughtForward(date('Y')-1) && !Config::getLeaveBroughtForward(date('Y'))) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Displays the Leave Summary
	 *
	 */
	private function _displayLeaveSummary($modifier='display', $year = null, $esp=null, $sortField = null, $sortOrder = null, $pageNO = 0) {
		if (!isset($year)) {
			$year = date('Y');
		}

		$auth = $this->_authenticateViewLeaveSummary();

		$copyQuota = $this->_validToCopyQuotaFromLastYear($year);

		$broughtForward = $this->_validToCopyBroughtForwardFromLastYear();

		$modifier = array($modifier, $auth, $year, $copyQuota, $broughtForward);

		$empInfoObj = new EmpInfo();

		$tmpObj = $this->getObjLeave();

        $eps = ($esp == null) ? 'employee' : $esp;

		$tmpObjX['leaveSummary'] = $tmpObj->fetchAllEmployeeLeaveSummary($this->getId(), $year, $this->getLeaveTypeId(), $esp, $sortField, $sortOrder, true ,$pageNO ,50);

		$empDetails = $empInfoObj->filterEmpMain($this->getId());
		if (is_array($empDetails)) {
			$tmpObjX['empDetails'] = $empDetails[0];
		} else {
			$tmpObjX['empDetails'] = $empDetails;
		}

		$tmpObjX['pageNo'] = $pageNO;

		list($leaveCount) = $tmpObj->fetchAllEmployeeLeaveSummary($this->getId(), $year, $this->getLeaveTypeId(), $esp, $sortField, $sortOrder ,FALSE ,0 ,0 ,TRUE);

		$tmpObjX['leaveCount'] = $leaveCount['leaveCount'];

      //we introduce token for the form here
      $screenParam = array('leavecode' => $_GET['leavecode'], 'action' => 'Leave_Summary');
      
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $tmpObjX['token'] = $tokenGenerator->getCSRFToken(array_keys($screenParam));

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

			for ($i=0; $i < count($subordinates); $i++) {
				if (in_array($id, $subordinates[$i])) {
					$subordinate = true;
					break;
				}
			}

			if (!$subordinate) {
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

		if ($this->authorize->isAdmin()) {
			return;
		}

		/*
		 * Removed checking for subordinates since ESS user will also access this method
		 * to change the leave status (to Cancel) and change comments. (This was implemented
		 * as the fix to bug 2825245 - pressing the save button in myleave make leave status cancel)
		 */
		if (isset($id) && ($id === $_SESSION['empID'])) {
			trigger_error("Unauthorized access1", E_USER_NOTICE);
		}
	}

	public function displayLeaveTypeDefine() {

		$leaveType = new LeaveType();

		$this->setObjLeave($leaveType);

      $screenParam = array('leavecode' => $_GET['leavecode'], 'action' => $_GET['action']);
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $tmpObj['token'] = $tokenGenerator->getCSRFToken(array_keys($screenParam));
      
		$path = "/templates/leave/leaveTypeDefine.php";

		$tmpObj[0] = $leaveType;
		$tmpObj[1] = $leaveType->fetchLeaveTypes(true);
		$tmpObj['rights'] = $_SESSION['localRights'];
		$template = new TemplateMerger($tmpObj, $path);

		$template->display();
	}


	public function addLeaveType() {

		$tmpObj = $this->getObjLeave();
		$newName = $tmpObj->getLeaveTypeName();

      //we introduce token for the form here
      $screenParam = array('leavecode' => $_GET['leavecode'], 'action' => 'Leave_Type_View_Define');
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

		$action = "Leave_Type_View_Define";

		if ($tmpObj->getLeaveTypeWithName($newName)) {
			$message = "NAME_IN_USE_ERROR";
		} else {
         if($token == $_POST['token']) {
            $res = $tmpObj->addLeaveType();
         } else {
            $res = false;
         }
         if ($res) {
            $action = "Leave_Type_Summary";
            $message="ADD_SUCCESS";
         } else {
            $message="ADD_FAILURE";
         }
		}

		$this->redirect(null, array("?leavecode=Leave&action={$action}&message={$message}"));
	}

	/**
	 * Undelete Leave type
	 */
	public function undeleteLeaveType() {

		$tmpObj = $this->getObjLeave();
		$newName = $tmpObj->getLeaveTypeName();

		$action = "Leave_Type_View_Define";

		$leaveTypes = $tmpObj->getLeaveTypeWithName($newName, true);
		if ($leaveTypes) {
			foreach($leaveTypes as $leaveType) {
				$leaveType->undeleteLeaveType();
			}
			$message = "UNDELETE_SUCCESS";
			$action = "Leave_Type_Summary";
		} else {
			$message="LEAVE_TYPE_NOT_FOUND";
		}

		$this->redirect(null, array("?leavecode=Leave&action={$action}&message={$message}"));
	}

	public function displayLeaveTypeSummary(){

		if ($_SESSION['isAdmin'] == 'No') {
		    die('You are not authorized to view this page');
		}

		$tmpObj = new LeaveType();

		$this->setObjLeave($tmpObj);

		$tmpObjArr = $tmpObj->fetchLeaveTypes(true);

      $screenParam = array('leavecode' => $_GET['leavecode'], 'action' => 'Leave_Type_Summary');
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $tmpObjArr['token'] = $tokenGenerator->getCSRFToken(array_keys($screenParam));

		$path = "/templates/leave/leaveTypeSummary.php";

		$template = new TemplateMerger($tmpObjArr, $path);

		$template->display();
	}


	/* TODO: Seems like this method is no longer used. It should probably be removed */
	public function displayLeaveEditTypeDefine(){

		$tmpObj = new LeaveType();

		$this->setObjLeave($tmpObj);

		$tmpOb[0] = $tmpObj->retriveLeaveType($this->getId());
		$tmpOb[1] = $leaveType->fetchLeaveTypes();
		$tmpOb['rights'] = $_SESSION['localRights'];

		$path = "/templates/leave/leaveTypeDefine.php";

		$template = new TemplateMerger($tmpOb, $path);

		$template->display();
	}

	public function editLeaveType() {

		$tmpObj = $this->getObjLeave();
		$res = $tmpObj->editLeaveType();
		return $res;
	}

	public function LeaveTypeDelete() {

		$tmpObj = $this->getObjLeave();
      $screenParam = array('leavecode' => $_GET['leavecode'], 'action' => 'Leave_Type_Summary');
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

      $res = false;
      if($token == $_POST['token']) {
         $res = $tmpObj->deleteLeaveType();
      }

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

		$tmpOb[0] = $tmpObj->getLeaveYears();

		$authorizeObj = $this->authorize;

		if ($this->getAuthorize()->isAdmin()) {
			$empObj = new EmpInfo();
			$tmpOb[1] = array(true);
			$leaveTypeObj = new LeaveType();
			$tmpOb[2] = $leaveTypeObj->fetchLeaveTypes();
		} else {
			$repObj = new EmpRepTo();
			$tmpOb[1] = $repObj->getEmpSubDetails($_SESSION['empID']);

		}

		$roles = array(authorize::AUTHORIZE_ROLE_ADMIN, authorize::AUTHORIZE_ROLE_SUPERVISOR);
		$role = $authorizeObj->firstRole($roles);

      $screenParam = array('leavecode' => $_GET['leavecode'], 'action' => $_GET['action']);
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

		$tmpOb[3] = $role;
      $tmpOb['token'] = $token;

		$path = "/templates/leave/leaveSelectEmployeeAndYear.php";

		$template = new TemplateMerger($tmpOb, $path);

		$template->display($action);
	}

	private function _viewLeavesTaken($year = null) {
		$authorizeObj  = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);


		if ($authorizeObj->isAdmin() || $authorizeObj->isSupervisor()) {

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

        $objLeave = $this->getObjLeave();
        $objLeave->add();
        Leave::updateLeavesForDate($objLeave->getDate(), $objLeave->getLength());
        Holidays::updateHolidaysForLeavesOnCreate($objLeave->getDate(), $objLeave->getLength());
    }

    /**
	 * Wrpper to edit holidays
	 *
	 * @param unknown_type $modifier
	 */
	public function editHoliday($modifier="specific") {
		$this->_authenticateViewHoliday();

		switch ($modifier) {
			case "specific" : $objLeave = $this->getObjLeave();
							  $this->getObjLeave()->edit();
							  Leave::updateLeavesForDate($objLeave->getDate(), $objLeave->getLength());
                              Holidays::updateHolidaysForLeavesOnUpdate($objLeave->getDate(), $objLeave->getLength());
                              break;
			case "weekend" 	: $this->getObjLeave()->editDay();
                              Weekends::updateWeekendsForLeaves();
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
        Holidays::updateHolidaysForLeavesOnDelete();
        return "";
    }

    public function displayDefineHolidays($modifier="specific", $edit=false) {
        $this->_authenticateViewHoliday();

		$record = null;
		if ($edit) {
			$holidayObj = new Holidays();

			$record = $holidayObj->fetchHoliday($this->getId());
		}
      
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $screenParam = array('leavecode' => $_GET['leavecode'], 'action' => $_GET['action']);
      if(isset($_GET['id'])) {
         $screenParam['id'] = $_GET['id'];
      }
      $tokenGenerator->setKeyGenerationInput($screenParam);

      
		switch ($modifier) {
			case "specific"	:	$holiday = new Holidays();
 								$record['holidayList'] = $holiday->listHolidays();
								$path = "/templates/leave/specificHolidaysDefine.php"; 
								break;
			case "weekend"	:	$path = "/templates/leave/weekendHolidaysDefine.php";                                         
								$weekendsObj = new Weekends();
								$record = $weekendsObj->fetchWeek();
								break;
		}
      
      $record['token'] = $tokenGenerator->getCSRFToken(array_keys($screenParam));
		$record['rights'] = $_SESSION['localRights'];
		$record['changeWeekends'] = Leave::isLeaveTableEmpty();
      
		$template = new TemplateMerger($record, $path);

		$modifier = $edit;

		$template->display($modifier);
	}

	public function viewTakenLeaves() {

		$tmpObj = new LeaveTakenRequests();

		$tmpObj = $tmpObj->retriveLeaveTaken();
		$path = "/templates/leave/leaveTakenList.php";

		$template = new TemplateMerger($tmpObj, $path);

		$template->display();

	}

	public function updateTakenLeaves($objArr) {

		if (count($objArr) == 0) {
			return false;
		}

		$failiure = 0;
		$noofexecutions = 0;

		foreach ($objArr as $obj) {
			$leaveObj = new LeaveTakenRequests();
			if (!$leaveObj->cancelLeaveTaken($obj) || !$leaveObj->changeTakenLeaveQuota($obj)) {
				$failiure++;
			}
		}

		if ($failiure > 0) {
			return false;
		} else {
			return true;
		}

	}

    private function _prepareSubordinateList($subs){
    	$subsForAutoComp = array();
        $count = count($subs);

        for ($i=0; $i<$count; $i++) {
            $subsForAutoComp[$i][] = $subs[$i][1];
            $subsForAutoComp[$i][] = '';
            $subsForAutoComp[$i][] = $subs[$i][0];
        }

        return $subsForAutoComp;
    }
}

class DuplicateLeaveException extends Exception {

	private $duplicateLeaveList;
	private $warn;

    public function __construct($duplicateList, $warn = false, $message = null, $code = 0) {

        $this->duplicateLeaveList = $duplicateList;
        $this->warn = $warn;
        parent::__construct($message, $code);
    }

    public function getDuplicateLeaveList() {
    	return $this->duplicateLeaveList;
    }

    public function isWarning() {
    	return $this->warn;
    }
}
?>
