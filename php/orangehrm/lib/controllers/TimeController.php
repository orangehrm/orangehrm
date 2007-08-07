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

require_once ROOT_PATH . '/lib/models/time/Timesheet.php';
require_once ROOT_PATH . '/lib/models/time/TimeEvent.php';

require_once ROOT_PATH . '/lib/models/eimadmin/Customer.php';
require_once ROOT_PATH . '/lib/models/eimadmin/Projects.php';
require_once ROOT_PATH . '/lib/models/eimadmin/EmployStat.php';

require_once ROOT_PATH . '/lib/common/TemplateMerger.php';
require_once ROOT_PATH . '/lib/common/authorize.php';

require_once ROOT_PATH . '/lib/models/hrfunct/EmpInfo.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpRepTo.php';

require_once ROOT_PATH . '/lib/models/hrfunct/EmpInfo.php';

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

	public function punchTime($punchIn) {
		$tmpObj  = $this->getObjTime();

		if ($tmpObj == null) {
			$this->redirect('INVALID_TIME_FAILURE', "?timecode=Time&action=Show_Punch_Time");
		}

		$tmpObj->resolveTimesheet();

		$_GET['message'] = 'SUBMIT_SUCCESS';

		try {
			if ($punchIn) {
				$res = $tmpObj->addTimeEvent();

				if (!$res) {
					throw new TimeEventException("Failed to add time event", 0);
				}

			} else {
				$startTimeStr = $tmpObj->getStartTime();
				$endTimeStr = $tmpObj->getEndTime();

				$startTime = strtotime($startTimeStr);
				$endTime = strtotime($endTimeStr);

				$dateEndTime = strtotime(date("Y-m-d 23:59", strtotime($startTimeStr)));
				$dateEndTimeStr = date("Y-m-d H:i", $dateEndTime);

				if ($endTime > $dateEndTime) {
					$tmpObj->setEndTime($dateEndTimeStr);
					$tmpObj->setDuration($dateEndTime-$startTime);
					$res = $tmpObj->editTimeEvent();

					if (!$res) {
						throw new TimeEventException("Failed to update time event", 0);
					}

					$tmpObj->setTimeEventId(null);
					$tmpObj->setTimesheetId(null);

					$tmpObj->setStartTime(date("Y-m-d H:i", $dateEndTime+60));

					$dateEndTime+=3600*24;
					$dateEndTimeStr = date("Y-m-d H:i", $dateEndTime);

					while ($endTime > $dateEndTime) {
						$tmpObj->setEndTime($dateEndTimeStr);
						$tmpObj->setDuration($dateEndTime-strtotime($tmpObj->getStartTime()));

						$tmpObj->resolveTimesheet();

						$res = $tmpObj->addTimeEvent();

						if (!$res) {
							throw new TimeEventException("Failed to add time event", 0);
						}

						$tmpObj->setStartTime(date("Y-m-d H:i", $dateEndTime+60));

						$dateEndTime+=3600*24;
						$dateEndTimeStr = date("Y-m-d H:i", $dateEndTime);

						$tmpObj->setTimesheetId(null);
					}

					$tmpObj->setEndTime($endTimeStr);
					$tmpObj->setDuration($endTime-strtotime($tmpObj->getStartTime()));

					$tmpObj->resolveTimesheet();

					$res = $tmpObj->addTimeEvent();

					if (!$res) {
						throw new TimeEventException("Failed to add time event", 0);
					}
				} else {
					$res = $tmpObj->editTimeEvent();

					if (!$res) {
						throw new TimeEventException("Failed to update time event", 0);
					}
				}
			}
		} catch (TimeEventException $exception) {
			if ($exception->getCode() == 0) {
				$_GET['message'] = 'SUBMIT_FAILURE';
			} else {
				$_GET['message'] = 'EXCEPTION_THROWN_WARNING';
			}
		}

		$this->redirect($_GET['message'], "?timecode=Time&action=Show_Punch_Time");

		return $res;
	}

	public function timeEventHome() {
		$path = "/templates/time/submitTimeHome.php";

		if (!isset($_SESSION['empID'])) {
			$this->redirect('UNAUTHORIZED_FAILURE');
		}

		$tmpObj = new TimeEvent();
		$tmpObj->setEmployeeId($_SESSION['empID']);

		$dataArr[0] = $tmpObj->pendingTimeEvents();

		$template = new TemplateMerger($dataArr, $path);
		$template->display();
	}

	public function timeEventEditView($new) {
		$path = "/templates/time/timeEventEdit.php";

		if (!isset($_SESSION['empID'])) {
			$this->redirect('UNAUTHORIZED_FAILURE');
		}

		$customerObj = new Customer();
		$projectObj = new Projects();

		$customers = $customerObj->fetchCustomers();

		$projects = $projectObj->fetchProjects();

		$dataArr[0] = $projects;

		if (!$new) {
			$timeEventObj = new TimeEvent();
			$timeEventObj->setTimeEventId($_GET['id']);

			$timeEvents = $timeEventObj->fetchTimeEvents();

			$dataArr[1] = $timeEvents[0];
		}

		$template = new TemplateMerger($dataArr, $path);
		$template->display();
	}

	public function showPunchTime() {
		$path = "/templates/time/punchTime.php";

		if (!isset($_SESSION['empID'])) {
			$this->redirect('UNAUTHORIZED_FAILURE');
		}

		$tmpObj = new TimeEvent();
		$tmpObj->setEmployeeId($_SESSION['empID']);
		$tmpObj->setProjectId(TimeEvent::TIME_EVENT_PUNCH_PROJECT_ID);
		$tmpObj->setActivityId(TimeEvent::TIME_EVENT_PUNCH_ACTIVITY_ID);

		$tmpTimeObj=$tmpObj->pendingTimeEvents(true);

		if (!$tmpTimeObj) {
			$tmpTimeObj=$tmpObj->fetchTimeEvents(true);
		}

		if (!isset($tmpTimeObj)) {
			$dataArr[0]=TimeEvent::TIME_EVENT_PUNCH_IN;
			$dataArr[1]=null;
		} else {
			if ($tmpTimeObj[0]->getEndTime() != null) {
				$dataArr[0]=TimeEvent::TIME_EVENT_PUNCH_IN;
			} else {
				$dataArr[0]=TimeEvent::TIME_EVENT_PUNCH_OUT;
			}
			$dataArr[1]=$tmpTimeObj[0];
		}

		$employeeObj = new EmpInfo();

		$employee = $employeeObj->filterEmpMain($_SESSION['empID']);

		$dataArr[2]=$employee[0];

		$template = new TemplateMerger($dataArr, $path);
		$template->display();
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

	public function fetchProjectActivities($projectId=0) {
		if (0 > $projectId) {
			return null;
		}

		$projectActivityObj = new ProjectActivity();
		$projectActivities = $projectActivityObj->getActivityList($projectId);

		$projectActivityArr = null;

		if (isset($projectActivities)) {
			foreach ($projectActivities as $projectActivity) {
				$tmpArr[0] = $projectActivity->getId();
				$tmpArr[1] = $projectActivity->getName();

				$projectActivityArr[] = $tmpArr;
			}
		}

		return $projectActivityArr;
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

	public function editTimesheet($nextAction) {
		$timeEvents = $this->getObjTime();

		$roles = array(authorize::AUTHORIZE_ROLE_ADMIN, authorize::AUTHORIZE_ROLE_SUPERVISOR);
		$role = $this->authorizeObj->firstRole($roles);

		if ($timeEvents == null) {
			$_GET['message'] = 'UPDATE_FAILURE';
			$this->redirect($_GET['message'], "?timecode=Time&action={$nextAction}&id={$_GET['id']}");
			return false;
		}

		if ($_SESSION['empID'] != $timeEvents[0]->getEmployeeId()) {
			if (!$role || (($role == authorize::AUTHORIZE_ROLE_SUPERVISOR) && (!$this->authorizeObj->isTheSupervisor($timeEvents[0]->getEmployeeId())))) {
				$this->redirect('UNAUTHORIZED_FAILURE');
			}
		}

		$_GET['message'] = 'NO_RECORDS_CHANGED_WARNING';

		foreach ($timeEvents as $timeEvent) {
			if ($timeEvent->getTimeEventId() == null) {
				$res=$timeEvent->addTimeEvent();
			} else {
				$res=$timeEvent->editTimeEvent();
			}

			if ($res) {
				echo $res;
				if ($res == 1) {
					$_GET['message'] = 'UPDATE_SUCCESS';
				}
			} else {
				$_GET['message'] = 'UPDATE_FAILURE';
				break;
			}
		}

		$this->redirect($_GET['message'], "?timecode=Time&action={$nextAction}&id={$timeEvent->getTimesheetId()}");

		return $res;
	}

	public function saveTimeEvent() {
		$timeEvent = $this->getObjTime();

		if ($timeEvent == null) {
			$this->redirect('INVALID_TIME_FAILURE', "?timecode=Time&action=Show_Punch_Time");
		}

		$timeEvent->resolveTimesheet();

		if ($timeEvent->getTimeEventId() == null) {
			$res=$timeEvent->addTimeEvent();
		} else {
			$res=$timeEvent->editTimeEvent();
		}

		if ($res) {
			$_GET['message'] = 'UPDATE_SUCCESS';
		} else {
			$_GET['message'] = 'UPDATE_FAILURE';
		}

		$this->redirect($_GET['message'], "?timecode=Time&action=Time_Event_Home");

		return $res;
	}

	public function defineWorkWeekView() {
		$path = "/templates/time/defineWorkWeek.php";

		$timesheetSubmissionPeriodObj = new TimesheetSubmissionPeriod();

		$timesheetSubmissionPeriodObj->setTimesheetPeriodId(1);
		$tmpArr = $timesheetSubmissionPeriodObj->fetchTimesheetSubmissionPeriods();

		$dataArr[0] = $tmpArr[0];

		$template = new TemplateMerger($dataArr, $path);
		$template->display();
	}

	public function saveWorkWeek() {
		$timesheetSubmissionPeriod = $this->getObjTime();
		$timesheetSubmissionPeriod->setFrequency(TimesheetSubmissionPeriod::TIMESHEET_SUBMISSION_PERIOD_FREQUENCY_WEEK);

		try {
			$res = $timesheetSubmissionPeriod->saveTimesheetSubmissionPeriod();
		} catch (TimesheetSubmissionPeriodException $err) {
    		$_GET['message'] = 'EXCEPTION_THROWN_WARNING';

    		$this->redirect($_GET['message'], "?timecode=Time&action=Work_Week_Edit_View");
    	}

    	if ($res) {
    		$_GET['message'] = 'UPDATE_SUCCESS';
    	} else {
    		$_GET['message'] = 'UPDATE_FAILURE';
    	}

    	$this->redirect($_GET['message'], "?timecode=Time&action=Work_Week_Edit_View");
	}

	public function deleteTimesheet($nextAction) {
		$timeEvents = $this->getObjTime();

		$roles = array(authorize::AUTHORIZE_ROLE_ADMIN, authorize::AUTHORIZE_ROLE_SUPERVISOR);
		$role = $this->authorizeObj->firstRole($roles);

		if ($timeEvents == null) {
			$_GET['message'] = 'UPDATE_FAILURE';
			$this->redirect($_GET['message'], "?timecode=Time&action={$nextAction}&id={$_GET['id']}");
			return false;
		}

		if ($_SESSION['empID'] != $timeEvents[0]->getEmployeeId()) {
			if (!$role || (($role == authorize::AUTHORIZE_ROLE_SUPERVISOR) && (!$this->authorizeObj->isTheSupervisor($timeEvents[0]->getEmployeeId())))) {
				$this->redirect('UNAUTHORIZED_FAILURE');
			}
		}

		foreach ($timeEvents as $timeEvent) {
			if ($timeEvent->getTimeEventId() != null) {
				$timeEventObjs = $timeEvent->fetchTimeEvents();
				$timeEvent = $timeEventObjs[0];
				$res=$timeEvent->deleteTimeEvent();
			}

			if ($res) {
				$_GET['message'] = 'DELETE_SUCCESS';
			} else {
				$_GET['message'] = 'DELETE_FAILURE';
				break;
			}
		}

		$this->redirect($_GET['message'], "?timecode=Time&action={$nextAction}&id={$timeEvent->getTimesheetId()}");

		return $res;
	}

	public function viewEditTimesheet($return="View_Timesheet") {
		$timesheetObj = $this->objTime;

		$roles = array(authorize::AUTHORIZE_ROLE_ADMIN, authorize::AUTHORIZE_ROLE_SUPERVISOR);
		$role = $this->authorizeObj->firstRole($roles);

		if ($timesheetObj->getTimesheetId() != null) {
			$timesheetObj->setEmployeeId(null);
		} else if ($_SESSION['empID'] != $timesheetObj->getEmployeeId()) {
			if (!$role || (($role == authorize::AUTHORIZE_ROLE_SUPERVISOR) && (!$this->authorizeObj->isTheSupervisor($timesheetObj->getEmployeeId())))) {
				$this->redirect('UNAUTHORIZED_FAILURE');
			}
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
		$dataArr[8]=$return;

		$template = new TemplateMerger($dataArr, $path);
		$template->display();
	}

	public function viewDetailedTimesheet() {
		$timesheetObj = $this->objTime;

		$roles = array(authorize::AUTHORIZE_ROLE_ADMIN, authorize::AUTHORIZE_ROLE_SUPERVISOR);
		$role = $this->authorizeObj->firstRole($roles);

		if ($timesheetObj->getTimesheetId() != null) {
			$timesheetObj->setEmployeeId(null);
		} else if ($_SESSION['empID'] != $timesheetObj->getEmployeeId()) {
			if (!$role || (($role == authorize::AUTHORIZE_ROLE_SUPERVISOR) && (!$this->authorizeObj->isTheSupervisor($timesheetObj->getEmployeeId())))) {
				$this->redirect('UNAUTHORIZED_FAILURE');
			}
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

		$path="/templates/time/timesheetDetailedView.php";


		$employeeObj = new EmpInfo();
		$employee = $employeeObj->filterEmpMain($timesheet->getEmployeeId());

		$self=false;
		if ($timesheet->getEmployeeId() == $_SESSION['empID']) {
			$self=true;
		}

		$dataArr[0]=$timesheet;
		$dataArr[1]=$timesheetSubmissionPeriod[0];
		$dataArr[2]=$timeEvents;
		$dataArr[3]=$employee[0];
		$dataArr[4]=$self;

		$template = new TemplateMerger($dataArr, $path);
		$template->display();
	}

	public function viewTimesheet($current) {

		$timesheetObj = $this->objTime;

		if ($timesheetObj->getTimesheetId() != null) {
			$timesheetObj->setEmployeeId(null);
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

		$roles = array(authorize::AUTHORIZE_ROLE_ADMIN);

		if ($timesheet && $this->authorizeObj->isTheSupervisor($timesheet->getEmployeeId())) {
			$roles[] = authorize::AUTHORIZE_ROLE_SUPERVISOR;
		}

		$role = $this->authorizeObj->firstRole($roles);

		if ($_SESSION['empID'] != $timesheet->getEmployeeId()) {
			if (!$role) {
				$this->redirect('UNAUTHORIZED_FAILURE');
			}
		}

		$timesheetSubmissionPeriodObj = new TimesheetSubmissionPeriod();
		$timesheetSubmissionPeriodObj->setTimesheetPeriodId($timesheet->getTimesheetPeriodId());
		$timesheetSubmissionPeriod = $timesheetSubmissionPeriodObj->fetchTimesheetSubmissionPeriods();

		list($durationArr, $dailySum, $activitySum, $totalTime) = $this->_generateTimesheet($timesheet);

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
		$dataArr[9]=$activitySum;
		$dataArr[10]=$totalTime;

		$template = new TemplateMerger($dataArr, $path);
		$template->display();
	}

	public function viewDefineEmployeeTimeReport() {
		$path="/templates/time/defineEmployeeTimeReport.php";

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

		$customerObj = new Customer();
		$projectObj = new Projects();

		$customers = $customerObj->fetchCustomers();

		$projects = $projectObj->fetchProjects();

		$dataArr[0] = $role;
		$dataArr[1] = $employees;
		$dataArr[2] = $projects;

		$template = new TemplateMerger($dataArr, $path);
		$template->display();
	}

	public function viewEmployeeTimeReport($startDate, $endDate) {

		$path="/templates/time/employeeTimeReport.php";

		$timeEventObj = $this->objTime;

		$roles = array(authorize::AUTHORIZE_ROLE_ADMIN);

		if ($timeEventObj && $this->authorizeObj->isTheSupervisor($timeEventObj->getEmployeeId())) {
			$roles[] = authorize::AUTHORIZE_ROLE_SUPERVISOR;
		}

		$role = $this->authorizeObj->firstRole($roles);

		if (!$role) {
				$this->redirect('UNAUTHORIZED_FAILURE');
		}

		$employeeObj = new EmpInfo();

		$employee = $employeeObj->filterEmpMain($timeEventObj->getEmployeeId());

		$report = $timeEventObj->timeReport($startDate, $endDate);

		$dataArr[0] = $role;
		$dataArr[1] = $employee[0];
		$dataArr[2] = $report;

		$template = new TemplateMerger($dataArr, $path);
		$template->display();
	}

	public function viewSelectTimesheet() {
		$path="/templates/time/selectTimesheets.php";

		$dataArr = null;

		$employmentStatusObj = new EmploymentStatus();

		$dataArr[0] = $employmentStatusObj->getListofEmpStat(0, '', -1);

		$template = new TemplateMerger($dataArr, $path);
		$template->display();
	}

	public function viewTimesheetPrintPreview($filterValues) {
		$path = "/templates/time/timesheetPrintPreview.php";

		$employeeObj = new EmpInfo();
		$timesheetObj = $this->getObjTime();
		$sysConfObj = new sysConf();

		$dataArr[0] = $filterValues;
		$dataArr[0][4] = $timesheetObj->getStartDate();
		$dataArr[0][5] = $timesheetObj->getEndDate();

		$employeeIds = $employeeObj->getEmployeeIdsFilterMultiParams($filterValues);

		$timesheetsCount = 0;
		if (isset($employeeIds)) {
			$timesheetsCount = $timesheetObj->countTimesheetsBulk($employeeIds);
		}

		$dataArr[1] = $timesheetsCount;
		$dataArr[2] = $sysConfObj->itemsPerPage;

		$template = new TemplateMerger($dataArr, $path);
		$template->display();
	}

	public function showPrint() {
		$path = "/templates/time/printPop.php";

		$template = new TemplateMerger(null, $path);
		$template->display();
	}

	/**
	 * View timesheets in bulk
	 *
	 * Introduced for printing timesheets.
	 * $fileterValues can optionally contain the following. Order is important.
	 *	1. Employee Id
	 * 	2. Division Id
	 *	3. Supervisor Id
	 *	4. Employment Status
	 *
	 * From and To date should be set as the timeobj
	 *
	 * @param String[] filterValues Filter timesheets with the values
	 */
	public function viewTimesheelBulk($filterValues, $page=1) {
		$path = "/templates/time/printTimesheetPage.php";

		$employeeObj = new EmpInfo();
		$timesheetObj = $this->getObjTime();

		$employeeIds = $employeeObj->getEmployeeIdsFilterMultiParams($filterValues);
		$timesheets = $timesheetObj->fetchTimesheetsBulk($page, $employeeIds);

		$dataArr=null;

		$timesheetSubmissionPeriodObj = new TimesheetSubmissionPeriod();

		for($i=0; $i<count($timesheets); $i++) {
			list($dataArr[0][$i]['durationArr'],
				 $dataArr[0][$i]['dailySum'],
				 $dataArr[0][$i]['activitySum'],
				 $dataArr[0][$i]['totalTime']) = $this->_generateTimesheet($timesheets[$i]);

			$employees = $employeeObj->filterEmpMain($timesheets[$i]->getEmployeeId());

			$dataArr[0][$i]['employee'] = $employees[0];
			$dataArr[0][$i]['timesheet'] = $timesheets[$i];

			$timesheetSubmissionPeriodObj->setTimesheetPeriodId($timesheets[$i]->getTimesheetPeriodId());
			$timesheetSubmissionPeriod = $timesheetSubmissionPeriodObj->fetchTimesheetSubmissionPeriods();

			$dataArr[0][$i]['timesheetSubmissionPeriod']=$timesheetSubmissionPeriod[0];
		}

		$dataArr[1]=$page;

		$template = new TemplateMerger($dataArr, $path, "stubHeader.php", "stubFooter.php");
		$template->display();
	}

	/**
	 * Parse time events and generate the information for timesheets
	 *
	 * @param Timesheet timesheet
	 */
	private function _generateTimesheet($timesheet) {

		$timeEventObj = new TimeEvent();

		$timeEventObj->setTimesheetId($timesheet->getTimesheetId());

		$timeEvents = $timeEventObj->fetchTimeEvents();

		$durationArr = null;
		$dailySum = null;
		$activitySum = null;
		$totalTime = 0;

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
			if (!isset($activitySum[$projectId])) {
				$activitySum[$projectId]=0;
			}

			$durationArr[$projectId][$expenseDate]+=$timeEvents[$i]->getDuration();
			$dailySum[$expenseDate]+=$timeEvents[$i]->getDuration();
			$activitySum[$projectId]+=$timeEvents[$i]->getDuration();
			$totalTime+=$timeEvents[$i]->getDuration();
		}

		return array($durationArr, $dailySum, $activitySum, $totalTime);
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
