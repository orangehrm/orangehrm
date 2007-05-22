<?php
/**
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

require_once ROOT_PATH . '/lib/models/time/Timesheet.php';
require_once ROOT_PATH . '/lib/models/time/TimeEvent.php';

require_once ROOT_PATH . '/lib/models/eimadmin/Customer.php';
require_once ROOT_PATH . '/lib/models/eimadmin/Projects.php';

require_once ROOT_PATH . '/lib/common/TemplateMerger.php';
require_once ROOT_PATH . '/lib/common/authorize.php';

require_once ROOT_PATH . '/lib/models/hrfunct/EmpInfo.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpRepTo.php';

class TimeController {

	private $objTime;
	private $id;
	private $authorizeObj;

	public function setObjTime($objTime) {
		$this->objTime=$objTime;
	}

	public function getObjTime() {
		return $this->objTime;
	}

	public function setId($id) {
		$this->id=$id;
	}

	public function getId() {
		return $this->id;
	}

	public function __construct() {
		$this->authorizeObj = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);
	}

	public function __distruct() {

	}

	public function nextEmployeeTimesheet($redirect=true) {
		$timesheetObj = $this->objTime;

		$timesheetObj->setStatuses(array(Timesheet::TIMESHEET_STATUS_SUBMITTED, Timesheet::TIMESHEET_STATUS_APPROVED, Timesheet::TIMESHEET_STATUS_REJECTED));
		$timesheetId = $timesheetObj->fetchTimesheetId(Timesheet::TIMESHEET_DIRECTION_NEXT);

		if (!$redirect) {
			return $timesheetId;
		}

		if (!$timesheetId) {
			$timesheetId=$timesheetObj->getTimesheetId();
		}

		$this->_redirectToTimesheet($timesheetId, null);
	}

	public function previousEmployeeTimesheet($redirect=true) {
		$timesheetObj = $this->objTime;

		$timesheetObj->setStatuses(array(Timesheet::TIMESHEET_STATUS_SUBMITTED, Timesheet::TIMESHEET_STATUS_APPROVED, Timesheet::TIMESHEET_STATUS_REJECTED));
		$timesheetId = $timesheetObj->fetchTimesheetId(Timesheet::TIMESHEET_DIRECTION_PREV);

		if (!$redirect) {
			return $timesheetId;
		}

		if (!$timesheetId) {
			$timesheetId=$timesheetObj->getTimesheetId();
		}

		$this->_redirectToTimesheet($timesheetId, null);
	}

	private function _redirectToTimesheet($timesheetId, $message) {
		$this->redirect($message, "?timecode=Time&action=View_Timesheet&id={$timesheetId}");
	}

	public function submitTimesheet() {
		$timesheetObj = $this->objTime;

		if ($_SESSION['empID'] != $timesheetObj->getEmployeeId()) {
			$this->redirect('UNAUTHORIZED_FAILURE', '?timecode=Time&action=View_Timesheet');
		}

		$res=$timesheetObj->submitTimesheet();
		if ($res) {
			$_GET['message'] = 'SUBMIT_SUCCESS';
		} else {
			$_GET['message'] = 'SUBMIT_FAILURE';
		}

		$this->_redirectToTimesheet($timesheetObj->getTimesheetId(), $_GET['message']);

		return $res;
	}

	public function cancelTimesheet() {
		$timesheetObj = $this->objTime;

		if ($_SESSION['empID'] != $timesheetObj->getEmployeeId()) {
			$this->redirect('UNAUTHORIZED_FAILURE', '?timecode=Time&action=View_Timesheet');
		}

		$res=$timesheetObj->cancelTimesheet();
		if ($res) {
			$_GET['message'] = 'CANCEL_SUCCESS';
		} else {
			$_GET['message'] = 'CANCEL_FAILURE';
		}

		$this->_redirectToTimesheet($timesheetObj->getTimesheetId(), $_GET['message']);

		return $res;
	}

	public function approveTimesheet() {
		$timesheetObj = $this->objTime;

		$roles = array(authorize::AUTHORIZE_ROLE_ADMIN, authorize::AUTHORIZE_ROLE_SUPERVISOR);
		$role = $this->authorizeObj->firstRole($roles);

		if (!$role || (($role == authorize::AUTHORIZE_ROLE_SUPERVISOR) && (!$this->authorizeObj->isTheSupervisor($timesheetObj->getEmployeeId())))) {
			$this->redirect('UNAUTHORIZED_FAILURE');
		}

		$res=$timesheetObj->approveTimesheet();
		if ($res) {
			$_GET['message'] = 'APPROVE_SUCCESS';
		} else {
			$_GET['message'] = 'APPROVE_FAILURE';
		}

		$this->_redirectToTimesheet($timesheetObj->getTimesheetId(), $_GET['message']);

		return $res;
	}

	public function rejectTimesheet() {
		$timesheetObj = $this->objTime;

		$roles = array(authorize::AUTHORIZE_ROLE_ADMIN, authorize::AUTHORIZE_ROLE_SUPERVISOR);
		$role = $this->authorizeObj->firstRole($roles);

		if (!$role || (($role == authorize::AUTHORIZE_ROLE_SUPERVISOR) && (!$this->authorizeObj->isTheSupervisor($timesheetObj->getEmployeeId())))) {
			$this->redirect('UNAUTHORIZED_FAILURE');
		}

		$res=$timesheetObj->rejectTimesheet();
		if ($res) {
			$_GET['message'] = 'REJECT_SUCCESS';
		} else {
			$_GET['message'] = 'REJECT_FAILURE';
		}

		$this->_redirectToTimesheet($timesheetObj->getTimesheetId(), $_GET['message']);

		return $res;
	}

	public function fetchCustomersProjects($customerId=0) {
		$projectObj = new Projects();

		if ($customerId != 0) {
			$projectObj->setCustomerId($customerId);
		}

		$projects = $projectObj->fetchProjects();

		$projectArr = null;

		if (isset($projects)) {
			foreach ($projects as $project) {
				$tmpArr[0] = $project->getProjectId();
				$tmpArr[1] = $project->getProjectName();

				$projectArr[] = $tmpArr;
			}
		}

		return $projectArr;
	}

	public function viewSelectEmployee() {
		$path = "/templates/time/selectEmployee.php";

		$roles = array(authorize::AUTHORIZE_ROLE_ADMIN, authorize::AUTHORIZE_ROLE_SUPERVISOR);
		$role = $this->authorizeObj->firstRole($roles);

		if (!$role) {
			$this->redirect('UNAUTHORIZED_FAILURE', '?timecode=Time&action=View_Timesheet');
		}

		$employees = null;
		$pendingTimesheets = null;
		$pending=false;
		if ($this->authorizeObj->isSupervisor()) {
			$empRepObj = new EmpRepTo();

			$employees = $empRepObj->getEmpSubDetails($_SESSION['empID']);
			$timesheetObj = new Timesheet();
			$timesheetObj->setStatus(Timesheet::TIMESHEET_STATUS_SUBMITTED);
			for ($i=0; $i<count($employees); $i++) {
				$timesheetObj->setEmployeeId($employees[$i][0]);
				$newTimesheets=$timesheetObj->fetchTimesheets();
				$pendingTimesheets[$employees[$i][0]]=$newTimesheets;
				if (isset($newTimesheets) && $newTimesheets) {
					$pending=true;
				}
			}
		}

		$dataArr[0] = $role;
		$dataArr[1] = $employees;
		$dataArr[2] = $pendingTimesheets;
		$dataArr[3] = $pending;

		$template = new TemplateMerger($dataArr, $path);
		$template->display();
	}

	public function editTimesheet() {
		$timeEvents = $this->getObjTime();

		$roles = array(authorize::AUTHORIZE_ROLE_ADMIN, authorize::AUTHORIZE_ROLE_SUPERVISOR);
		$role = $this->authorizeObj->firstRole($roles);

		if ($timeEvents == null) {
			$_GET['message'] = 'UPDATE_FAILURE';
			$this->redirect($_GET['message'], "?timecode=Time&action=View_Timesheet&id={$_GET['id']}");
			return false;
		}

		if ($_SESSION['empID'] != $timeEvents[0]->getEmployeeId()) {
			if (!$role || (($role == authorize::AUTHORIZE_ROLE_SUPERVISOR) && (!$this->authorizeObj->isTheSupervisor($timeEvents[0]->getEmployeeId())))) {
				$this->redirect('UNAUTHORIZED_FAILURE');
			}
		}

		foreach ($timeEvents as $timeEvent) {
			if ($timeEvent->getTimeEventId() == null) {
				$res=$timeEvent->addTimeEvent();
			} else {
				$res=$timeEvent->editTimeEvent();
			}

			if ($res) {
				$_GET['message'] = 'UPDATE_SUCCESS';
			} else {
				$_GET['message'] = 'UPDATE_FAILURE';
				break;
			}
		}

		$this->redirect($_GET['message'], "?timecode=Time&action=View_Timesheet&id={$timeEvent->getTimesheetId()}");

		return $res;
	}

	public function viewEditTimesheet() {
		$timesheetObj = $this->objTime;

		$roles = array(authorize::AUTHORIZE_ROLE_ADMIN, authorize::AUTHORIZE_ROLE_SUPERVISOR);
		$role = $this->authorizeObj->firstRole($roles);

		if ($timesheetObj->getTimesheetId() != null) {
			$timesheetObj->setEmployeeId(null);
		} else if ($_SESSION['empID'] != $timesheetObj->getEmployeeId()) {
			if (!$role || (($role == authorize::AUTHORIZE_ROLE_SUPERVISOR) && (!$this->authorizeObj->isTheSupervisor($timesheetObj->getEmployeeId())))) {
				$this->redirect('UNAUTHORIZED_FAILURE');
			}
			$timesheetObj->setStatuses(array(Timesheet::TIMESHEET_STATUS_SUBMITTED, Timesheet::TIMESHEET_STATUS_APPROVED, Timesheet::TIMESHEET_STATUS_REJECTED));
		}

		$timesheets = $timesheetObj->fetchTimesheets();

		if ($timesheets == null) {
			if ($_SESSION['empID'] == $timesheetObj->getTimesheetId()) {
				$timesheetObj->addTimesheet();
				$timesheets = $timesheetObj->fetchTimesheets();
			}
		}

		$timesheet = $timesheets[0];

		$timeEventObj = new TimeEvent();

		$timesheetSubmissionPeriodObj = new TimesheetSubmissionPeriod();
		$timesheetSubmissionPeriodObj->setTimesheetPeriodId($timesheet->getTimesheetPeriodId());
		$timesheetSubmissionPeriod = $timesheetSubmissionPeriodObj->fetchTimesheetSubmissionPeriods();

		$timeEventObj->setTimesheetId($timesheet->getTimesheetId());

		$timeEvents = $timeEventObj->fetchTimeEvents();

		$path="/templates/time/timesheetEdit.php";

		$customerObj = new Customer();
		$projectObj = new Projects();

		$customers = $customerObj->fetchCustomers();

		$projects = $projectObj->fetchProjects();

		$employeeObj = new EmpInfo();

		$employee = $employeeObj->filterEmpMain($timesheet->getEmployeeId());

		$self=false;
		if ($timesheet->getEmployeeId() == $_SESSION['empID']) {
			$self=true;
		}

		$dataArr[0]=$timesheet;
		$dataArr[1]=$timesheetSubmissionPeriod[0];
		$dataArr[2]=$timeEvents;
		$dataArr[3]=$customers;
		$dataArr[4]=$projects;
		$dataArr[5]=$employee[0];
		$dataArr[6]=$self;
		$dataArr[7]=$roles;

		$template = new TemplateMerger($dataArr, $path);
		$template->display();
	}

	public function viewTimesheet($current) {

		$timesheetObj = $this->objTime;

		$roles = array(authorize::AUTHORIZE_ROLE_ADMIN, authorize::AUTHORIZE_ROLE_SUPERVISOR);
		$role = $this->authorizeObj->firstRole($roles);

		if ($timesheetObj->getTimesheetId() != null) {
			$timesheetObj->setEmployeeId(null);
		} else if ($_SESSION['empID'] != $timesheetObj->getEmployeeId()) {
			if (!$role || (($role == authorize::AUTHORIZE_ROLE_SUPERVISOR) && (!$this->authorizeObj->isTheSupervisor($timesheetObj->getEmployeeId())))) {
				$this->redirect('UNAUTHORIZED_FAILURE');
			}
			$timesheetObj->setStatuses(array(Timesheet::TIMESHEET_STATUS_SUBMITTED, Timesheet::TIMESHEET_STATUS_APPROVED, Timesheet::TIMESHEET_STATUS_REJECTED));
		}

		$timesheets = $timesheetObj->fetchTimesheets($current);

		if (!is_object($timesheets[0])) {
			if (($_SESSION['empID'] == $timesheetObj->getEmployeeId()) && (($timesheetObj->getEmployeeId() != null) && !empty($_SESSION['empID']))) {
				$timesheetObj->addTimesheet();
				$timesheets = $timesheetObj->fetchTimesheets();
			} else {
				$this->redirect('NO_TIMESHEET_FAILURE', '?timecode=Time&action=View_Select_Employee');
			}
		}

		$timesheet = $timesheets[0];

		$timeEventObj = new TimeEvent();

		$timesheetSubmissionPeriodObj = new TimesheetSubmissionPeriod();
		$timesheetSubmissionPeriodObj->setTimesheetPeriodId($timesheet->getTimesheetPeriodId());
		$timesheetSubmissionPeriod = $timesheetSubmissionPeriodObj->fetchTimesheetSubmissionPeriods();

		$timeEventObj->setTimesheetId($timesheet->getTimesheetId());

		$timeEvents = $timeEventObj->fetchTimeEvents();

		$durationArr = null;
		$dailySum = null;

		for ($i=0; $i<count($timeEvents); $i++) {
			$projectId=$timeEvents[$i]->getProjectId();
			if ($timeEvents[$i]->getStartTime() != null) {
				$expenseDate=strtotime(date('Y-m-d', strtotime($timeEvents[$i]->getStartTime())));
			} else {
				$expenseDate=strtotime(date('Y-m-d', strtotime($timeEvents[$i]->getReportedDate())));
			}
			if (!isset($durationArr[$projectId][$expenseDate])) {
				$durationArr[$projectId][$expenseDate]=0;
			}
			if (!isset($dailySum[$expenseDate])) {
				$dailySum[$expenseDate]=0;
			}

			$durationArr[$projectId][$expenseDate]+=$timeEvents[$i]->getDuration();
			$dailySum[$expenseDate]+=$timeEvents[$i]->getDuration();
		}

		$self=false;
		if ($timesheet->getEmployeeId() == $_SESSION['empID']) {
			$self=true;
		}

		$employeeObj = new EmpInfo();

		$employee = $employeeObj->filterEmpMain($timesheet->getEmployeeId());

		$path="/templates/time/timesheetView.php";

		$this->objTime->setEmployeeId($timesheet->getEmployeeId());
		$this->objTime->setStartDate($timesheet->getStartDate());
		$this->objTime->setEndDate($timesheet->getEndDate());

		$next=$this->nextEmployeeTimesheet(false);
		$prev=$this->previousEmployeeTimesheet(false);

		$dataArr[0]=$durationArr;
		$dataArr[1]=$timesheet;
		$dataArr[2]=$timesheetSubmissionPeriod[0];
		$dataArr[3]=$dailySum;
		$dataArr[4]=$employee[0];
		$dataArr[5]=$self;
		$dataArr[6]=$next;
		$dataArr[7]=$prev;
		$dataArr[8]=$role;

		$template = new TemplateMerger($dataArr, $path);
		$template->display();
	}

	public function redirect($message=null, $url = null) {

		if (isset($url)) {
			$mes = "";
			if (isset($message)) {
				$mes = "&message=";
			}
			$url=array($url.$mes);
			$id="";
		} else if (isset($message)) {
			preg_replace('/[&|?]+id=[A-Za-z0-9]*/', "", $_SERVER['HTTP_REFERER']);

			if (preg_match('/&/', $_SERVER['HTTP_REFERER']) > 0) {
				$message = "&message=".$message;
				$url = preg_split('/(&||\?)message=[A-Za-z0-9]*/', $_SERVER['HTTP_REFERER']);
			} else {
				$message = "?message=".$message;
			}

			if (isset($_REQUEST['id']) && !empty($_REQUEST['id']) && !is_array($_REQUEST['id'])) {
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
}
?>
