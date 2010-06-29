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
require_once ROOT_PATH . '/lib/models/time/ProjectReport.php';

require_once ROOT_PATH . '/lib/models/eimadmin/Customer.php';
require_once ROOT_PATH . '/lib/models/eimadmin/Projects.php';
require_once ROOT_PATH . '/lib/models/eimadmin/EmployStat.php';
require_once ROOT_PATH . '/lib/models/eimadmin/ProjectAdminGateway.php';

require_once ROOT_PATH . '/lib/common/TemplateMerger.php';
require_once ROOT_PATH . '/lib/common/authorize.php';

require_once ROOT_PATH . '/lib/models/hrfunct/EmpInfo.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpRepTo.php';

require_once ROOT_PATH . '/lib/models/time/Workshift.php';

require_once ROOT_PATH . '/lib/models/time/AttendanceRecord.php';
require_once ROOT_PATH . '/lib/extractor/time/EXTRACTOR_AttendanceRecord.php';
require_once ROOT_PATH . '/lib/utils/CSRFTokenGenerator.php';

class TimeController {

	const INVALID_TIMESHEET_PERIOD_ERROR = "INVALID_TIMESHEET_PERIOD_ERROR";
	const EVENT_OUTSIDE_PERIOD_FAILURE = "EVENT_OUTSIDE_PERIOD_FAILURE";
	const NO_TIMESHEET_FAILURE = "NO_TIMESHEET_FAILURE";
	const ZeroOrNegativeIntervalSpecified_ERROR = "ZeroOrNegativeIntervalSpecified_ERROR";
	const ProjectNotSpecified_ERROR = "ProjectNotSpecified_ERROR";
	const ActivityNotSpecified_ERROR = "ActivityNotSpecified_ERROR";
	const InvalidStartTime_ERROR = "InvalidStartTime_ERROR";
	const InvalidEndTime_ERROR = "InvalidEndTime_ERROR";
	const ReportedDateNotSpecified_ERROR = "ReportedDateNotSpecified_ERROR";
	const InvalidReportedDate_ERROR = "InvalidReportedDate_ERROR";
	const InvalidDuration_ERROR = "InvalidDuration_ERROR";
	const NoValidDurationOrInterval_ERROR = "NoValidDurationOrInterval_ERROR";
	const NotAllowedToSpecifyDurationAndInterval_ERROR = "NotAllowedToSpecifyDurationAndInterval_ERROR";

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
                                if(Timesheet::checkDateInApprovedTimesheet($tmpObj->getReportedDate(), $tmpObj->getEmployeeId())){
                                    throw new TimeEventException("Failed to add time event", 1);
                                }
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
			} elseif($exception->getCode() == 1){
				$_GET['message'] = 'APPROVED_TIMESHEET_FAILURE';
			}else{
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

		$projectObj->setDeleted(Projects::PROJECT_NOT_DELETED);
		$projects = $projectObj->fetchProjects();

		$dataArr[0] = $projects;

		if (!$new) {
			$timeEventObj = new TimeEvent();
			$timeEventObj->setTimeEventId($_GET['id']);

			$timeEvents = $timeEventObj->fetchTimeEvents();

			$dataArr[1] = $timeEvents[0];
		} else {
		    $dataArr[1] = null;
		}

		/* For setting current timesheet: Begins */
		$timesheetObj = new Timesheet();
		$timesheets = $timesheetObj->fetchTimesheets(true);

		if (isset($timesheets[0])) {
			$dataArr[2] = $timesheets[0];
		} else {
		    $dataArr[2] = null;
		}
		/* For setting current timesheet: Ends */

		$template = new TemplateMerger($dataArr, $path);
		$template->display();
	}


	/* Attendance Methods: Begin */

	public function showPunchView($messageType = null, $message = null) {

      $screenParam = array('timecode' => $_GET['timecode'], 'action' => $_GET['action']);
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
      
		$attendanceObj = new AttendanceRecord();
		$records['attRecord'] = $attendanceObj->fetchRecords($_SESSION['empID'], null, null, AttendanceRecord::STATUS_ACTIVE,
																AttendanceRecord::DB_FIELD_PUNCHIN_TIME, 'DESC', '0, 1', true);
		$records['editMode'] = Config::getAttendanceEmpChangeTime();
		$records['empId'] = $_SESSION['empID'];
		$timeStampDiff = ($_SESSION['userTimeZoneOffset'] - round(date('Z')/3600, 1))*3600;
		$records['currentDate'] = date('Y-m-d', time()+$timeStampDiff);
		$records['currentTime'] = date('H:i', time()+$timeStampDiff);
		$records['messageType'] = $messageType;
		$records['message']  = $message;
      $records['token']    = $token;

		$sysConfObj = new sysConf();
		$records['timeInputHint'] = $sysConfObj->getTimeInputHint();

		$path = "/templates/time/punchView.php";
		$template = new TemplateMerger($records, $path);
		$template->display();
	}

	public function savePunch() {

      $screenParam = array('timecode' => $_GET['timecode'], 'action' => 'Show_Punch_View');
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

		$extractor = new EXTRACTOR_AttendanceRecord($_SESSION['userTimeZoneOffset'], round(date('Z')/3600, 1));
		$attendanceObj = $extractor->parsePunchData($_POST);

		$attendanceId = $attendanceObj->getAttendanceId();

		if ($attendanceId) {

			try {

				$attendanceObj->isOverlapping(); // Would throw an exception on overlapping

				if (($token == $_POST['token']) && $attendanceObj->updateRecord()) {
					$messageType = 'SUCCESS';
					$message = 'save-success';
				} else {
					$messageType = 'FAILURE';
					$message = 'save-failure';
				}

			} catch (AttendanceRecordException $e) {
					$messageType = 'FAILURE';
					$message = 'overlapping-failure';
			}

		} else {

			try {

				$attendanceObj->isOverlappingInTime(); // Would throw an exception on overlapping

				if (($token == $_POST['token']) && $attendanceObj->addRecord()) {
					$messageType = 'SUCCESS';
					$message = 'save-success';
				} else {
					$messageType = 'FAILURE';
					$message = 'save-failure';
				}

			} catch (AttendanceRecordException $e) {
					$messageType = 'FAILURE';
					$message = 'overlapping-failure';
			}

		}

		$this->showPunchView($messageType, $message);

	}

	public function showAttendanceReportForm($reportType) {

		$records['fromDate'] = 'YYYY-MM-DD';
		$records['toDate'] = 'YYYY-MM-DD';
		$records['reportType'] = $reportType;
		$records['reportView'] = 'none';
		$records['empName'] = '';

		/* Setting employee list for Auto-Complete */
		if ($reportType == 'Emp' && $this->authorizeObj->isAdmin()) {
			$records['empList'] = EmpInfo::getEmployeeMainDetails();
		} elseif ($reportType == 'Emp' && $this->authorizeObj->isSupervisor()) {
			$records['empList'] = $this->_getSubsForAutoComplete($_SESSION['empID']);
		}

		/* Setting Employee ID */
		if ($reportType == 'My') {
			$records['empId'] = $_SESSION['empID'];
		} else {
			$records['empId'] = '';
		}

		$path = '/templates/time/attendanceReport.php';
		$template = new TemplateMerger($records, $path);
		$template->display();

	}

	public function generateAttendanceSummary($empId, $from, $to, $summary = false) {

		$reportType = $_POST['hdnReportType'];

		$records['fromDate'] = $_POST['txtFromDate'];
		$records['toDate'] = $_POST['txtToDate'];
		$records['reportType'] = $reportType;
		$records['reportView'] = $_POST['optReportView'];
		$records['empId'] = $empId;
		$records['empName'] = $_POST['hdnEmpName'];
		
		if ($empId > 0) {
			$empInfo = new EmpInfo();
			$empInfo = $empInfo->filterEmpMain($empId);
			$records['empName'] = $empInfo[0][2]." ".$empInfo[0][1];
		}

		$records['noReports'] = false;
        $subordinateIds = null;

		/* Setting employee list for Auto-Complete */
		if ($reportType == 'Emp' && $this->authorizeObj->isAdmin()) {
			$records['empList'] = EmpInfo::getEmployeeMainDetails();
		} elseif ($reportType == 'Emp' && $this->authorizeObj->isSupervisor()) {
			$records['empList'] = $this->_getSubsForAutoComplete($_SESSION['empID']);
			foreach($records['empList'] as $subordinate){
				$subordinateIds [] = $subordinate [0];
			}
		}

		/* Setting summay records: Begins */

		/* If the criteria is same use the records array saved in $_SESSION
		 * rather than retrieving from database
		 */

		$criteria = array($empId, $from, $to);
		$sameQuery = false;

		/*if (isset($_SESSION['attCriteria'])) {
		    if ($criteria == $_SESSION['attCriteria']) {
		        $sameQuery = true;
		    } else {
		        $_SESSION['attCriteria'] = $criteria;
		    }
		} else {
		    $_SESSION['attCriteria'] = $criteria;
		}*/

		if (isset($_POST['pageNo']) && $_POST['hdnFromPaging'] == 'Yes') { // If it's from Generate button, it should always display page 1.
		    $pageNo = $_POST['pageNo'];
		    $records['pageNo'] = $pageNo;
		} else {
		    $pageNo = 1;
			$records['pageNo'] = $pageNo;
		}

		if ($sameQuery) {
		    $records['recordsArr'] = $this->_getAttendanceSummaryForPage($_SESSION['attSummary'], $pageNo);
		    $records['recordsCount'] = count($_SESSION['attSummary']);
		} else {
			$attendanceObj = new AttendanceRecord();
			$attSummary = $attendanceObj->fetchSummary($empId, $from, $to, AttendanceRecord::STATUS_ACTIVE,
														AttendanceRecord::DB_FIELD_PUNCHIN_TIME, 'ASC', null, false , $subordinateIds);
			if($empId != -1) {
				$attSummary = $attendanceObj->populateDataRangeArrayForSummary( $from, $to , $attSummary);
			}
						
			$_SESSION['attSummary'] = (empty($attSummary))?array():$attSummary; // We should alway pass an array to _getAttendanceSummaryForPage()
			$records['recordsArr'] = $this->_getAttendanceSummaryForPage($_SESSION['attSummary'], $pageNo);
			$records['recordsCount'] = count($_SESSION['attSummary']);
		}

		/* Setting summay records: Ends */

		if (empty($records['recordsArr'])) {
			$records['noReports'] = true;
		}

		$path = '/templates/time/attendanceReport.php';
		$template = new TemplateMerger($records, $path);
		$template->display();

	}

	private function _getAttendanceSummaryForPage($attSummary, $pageNo, $recordsPerPage = 50) {

	    $start = ($pageNo*$recordsPerPage) - $recordsPerPage;
	    return array_slice($attSummary, $start, $recordsPerPage);

	}

	public function generateAttendanceReport($empId, $from, $to, $messageType=null, $message=null, $summary = false) {

		$reportType = $_POST['hdnReportType'];

		$records['fromDate'] = $_POST['txtFromDate'];
		$records['toDate'] = $_POST['txtToDate'];
		$records['reportType'] = $reportType;
		$records['reportView'] = $_POST['optReportView'];
		$records['messageType'] = $messageType;
		$records['message'] = $message;
		$records['empId'] = $empId;
		$records['empName'] = $_POST['hdnEmpName'];
		$records['noReports'] = false;
		$records['userTimeZoneOffset'] = $_SESSION['userTimeZoneOffset'];
		$records['serverTimeZoneOffset'] = round(date('Z')/3600, 1);

		/* Setting 'Back' button to summary view */
		if (isset($_POST['hdnFromSummary'])) {
			$records['hdnFromSummary'] = true;
			$records['orgFromDate'] = $_POST['orgFromDate'];
			$records['orgToDate'] = $_POST['orgToDate'];
		}

		/* Setting Edit Mode */
		if ($this->authorizeObj->isAdmin()) {
			$records['editMode'] = true;
		} elseif ($reportType == 'Emp' && $this->authorizeObj->isSupervisor() &&
				  Config::getAttendanceSupEditSubmitted()) {
			$records['editMode'] = true;
		} elseif ($reportType == 'My' && Config::getAttendanceEmpEditSubmitted()) {
			$records['editMode'] = true;
		} else {
			$records['editMode'] = false;
		}

		$subordinateIds = null;
		/* Setting employee list for Auto-Complete */
		if ($reportType == 'Emp' && $this->authorizeObj->isAdmin()) {
			$records['empList'] = EmpInfo::getEmployeeMainDetails();
		} elseif ($reportType == 'Emp' && $this->authorizeObj->isSupervisor()) {
			$records['empList'] = $this->_getSubsForAutoComplete($_SESSION['empID']);
		    foreach($records['empList'] as $subordinate){
                $subordinateIds [] = $subordinate [0];
            }
		}

		/* Setting AttendanceRecord array */
		$attendanceObj = new AttendanceRecord();

		if (isset($_POST['pageNo']) && $_POST['hdnFromPaging'] == 'Yes') { // If it's from Generate button, it should always display page 1.
		    $pageNo = $_POST['pageNo'];
		} else {
		    $pageNo = 1;
		}

		$limit = ($pageNo*50-50).', 50';

		$records['recordsArr'] = $attendanceObj->fetchRecords($empId, $from, $to, AttendanceRecord::STATUS_ACTIVE,
													AttendanceRecord::DB_FIELD_PUNCHIN_TIME, 'ASC', $limit, false, $subordinateIds);
												
		if (empty($records['recordsArr'])) {
			$records['noReports'] = true;
		}

		$records['recordsCount'] = $attendanceObj->countRecords($empId, $from, $to, AttendanceRecord::STATUS_ACTIVE);
		$records['pageNo'] = $pageNo;

		$path = '/templates/time/attendanceReport.php';
		$template = new TemplateMerger($records, $path);
		$template->display();

	}

	public function saveAttendanceReport() {
		
		$extractor = new EXTRACTOR_AttendanceRecord($_SESSION['userTimeZoneOffset'], round(date('Z')/3600, 1));
		$attendanceArr = $extractor->parseReportData($_POST);
		$updated = true;
		$message = null;
		$messageType = null;

		if (!empty($attendanceArr)) {
			
			/* $_SESSION['attCriteria'] is used in Attendance Summary
			 * and needs to reset when saving records. Otherwise summary
			 * can look wrong
			 */
			
			$_SESSION['attCriteria'] = null;
			
			try {

				foreach ($attendanceArr as $attendanceObj) {
					$attendanceObj->isOverlapping(); // Would throw an exception on overlapping
				}

				foreach ($attendanceArr as $attendanceObj) { // TODO: Better if can use a transaction here to avoid partial updates
					if (!$attendanceObj->updateRecord()) {
						$updated = false;
					}
				}

				if ($updated) {
					$message = 'update-success';
					$messageType = 'SUCCESS';
				} else {
					$message = 'update-failure';
					$messageType = 'FAILURE';
				}

			} catch (AttendanceRecordException $e) {

				if ($e->getCode() == AttendanceRecordException::OVERLAPPING_RECORD) {
					$message = 'overlapping-failure';
					$messageType = 'FAILURE';
				} else {
					die('Coding Error: Required values for checking overlapping are not set'); // TODO: throwing $e didn't work. Need to investigate why.
				}

			}

		} else {
			$message = 'nochange-failure';
			$messageType = 'FAILURE';
		}

		$from = $_POST['txtFromDate'].' 00:00:00';
		$to = $_POST['txtToDate'].' 23:59:59';
		$this->generateAttendanceReport($_POST['hdnEmployeeId'], $from, $to, $messageType, $message);

	}

	public function showAttendanceConfig($messageType = null) {

      $screenParam = array('timecode' => $_GET['timecode'], 'action' => $_GET['action']);
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
      
		if (Config::getAttendanceEmpChangeTime()) {
			$records['empChangeTime'] = true;
		} else {
			$records['empChangeTime'] = false;
		}

		if (Config::getAttendanceEmpEditSubmitted()) {
			$records['empEditSubmitted'] = true;
		} else {
			$records['empEditSubmitted'] = false;
		}

		if (Config::getAttendanceSupEditSubmitted()) {
			$records['supEditSubmitted'] = true;
		} else {
			$records['supEditSubmitted'] = false;
		}

		$records['messageType'] = $messageType;
      $records['token'] = $token;
		$path = '/templates/time/attendanceConfig.php';
		$template = new TemplateMerger($records, $path);
		$template->display();

	}

	public function saveAttendanceConfig() {

		$errorFlag = true;
      $screenParam = array('timecode' => $_GET['timecode'], 'action' => 'Show_Attendance_Config');
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

      if($token == $_POST['token']) {
         /* Employee can change displayed current time when he punches in/out */
         if (isset($_POST['chkEmpChangeTime'])) {
            try {
               Config::setAttendanceEmpChangeTime('Yes');
            } catch (Exception $e) {
               $errorFlag = false;
            }
         } else {
            try {
               Config::setAttendanceEmpChangeTime('No');
            } catch (Exception $e) {
               $errorFlag = false;
            }
         }

         /* Employee can edit submitted attendance records */
         if (isset($_POST['chkEmpEditSubmitted'])) {
            try {
               Config::setAttendanceEmpEditSubmitted('Yes');
            } catch (Exception $e) {
               $errorFlag = false;
            }
         } else {
            try {
               Config::setAttendanceEmpEditSubmitted('No');
            } catch (Exception $e) {
               $errorFlag = false;
            }
         }

         /* Supervisor can edit submitted attendance records of subordinates */
         if (isset($_POST['chkSupEditSubmitted'])) {
            try {
               Config::setAttendanceSupEditSubmitted('Yes');
            } catch (Exception $e) {
               $errorFlag = false;
            }
         } else {
            try {
               Config::setAttendanceSupEditSubmitted('No');
            } catch (Exception $e) {
               $errorFlag = false;
            }
         }
      } else {
         $errorFlag = false;
      }

		if ($errorFlag) {
			$messageType = 'SUCCESS';
		} else {
			$messageType = 'FAILURE';
		}

		$this->showAttendanceConfig($messageType);

	}

	private function _getSubsForAutoComplete($empId) {

		$reportToObj = new EmpRepTo();
		$empList = $reportToObj->getEmpSubDetails($empId);

		/*
		 * Here $empList comes as below,
		 * 0 => string '001' (emp_number)
		 * 1 => string 'Kayla Abbey' (Full Name)
		 * 2 => string '001' (employee_id)
		 *
		 * We want it in following format,
		 * 0 => string '001' (emp_number)
		 * 1 => string 'Kayla' (First Name)
		 * 2 => string 'Abbey' (Last Name)
		 */

		$newEmpList = array();
		$count = count($empList);

		for ($i=0; $i<$count; $i++) {

			$newEmpList[$i][0] = $empList[$i][0];

			$tmpArr = explode(' ', $empList[$i][1]);

			$newEmpList[$i][1] = $tmpArr[0];
			$newEmpList[$i][2] = $tmpArr[1];

		}

		return $newEmpList;

	}

	/* Attendance Methods: End */

	public function submitTimesheet() {

      $screenParam = array('timecode' => $_GET['timecode'], 'action' => 'View_Current_Timesheet');
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
      
		$timesheetObj = $this->objTime;

		/* For checking unfinished timesheets */
		if (TimeEvent::isUnfinishedTimesheet($timesheetObj->getTimesheetId())) {
			$_GET['message'] = 'UNFINISHED_TIMESHEET_FAILURE';
		    $this->_redirectToTimesheet($timesheetObj->getTimesheetId(), $_GET['message']);
		    return false;
		}

		$timesheets = $timesheetObj->fetchTimesheets();
		if (isset($timesheets) && isset($timesheets[0])) {
			$timesheet = $timesheets[0];
		} else {
			$this->redirect('NO_TIMESHEET_FAILURE', '?timecode=Time&action=View_Timesheet');
		}

		if ($this->authorizeObj->isAdmin() || ($this->authorizeObj->isSupervisor() && $this->authorizeObj->isTheSupervisor($timesheet->getEmployeeId()))) {
			$superior = true;
		} else {
			if ($_SESSION['empID'] != $timesheet->getEmployeeId()) {
				$this->redirect('UNAUTHORIZED_FAILURE', '?timecode=Time&action=View_Timesheet');
			}
			$superior = false;
		}

      $res = false;
      if($token == $_POST['token']) {
         $res=$timesheet->submitTimesheet($superior);
      }
      
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
		$timesheets = $timesheetObj->fetchTimesheets();

		if (isset($timesheets) && isset($timesheets[0])) {
			$timesheet = $timesheets[0];
		} else {
			$this->redirect('NO_TIMESHEET_FAILURE', '?timecode=Time&action=View_Timesheet');
		}

		if ($_SESSION['empID'] != $timesheet->getEmployeeId()) {
			$this->redirect('UNAUTHORIZED_FAILURE', '?timecode=Time&action=View_Timesheet');
		}

		$res=$timesheet->cancelTimesheet();
		if ($res) {
			$_GET['message'] = 'CANCEL_SUCCESS';
		} else {
			$_GET['message'] = 'CANCEL_FAILURE';
		}

		$this->_redirectToTimesheet($timesheet->getTimesheetId(), $_GET['message']);

		return $res;
	}

	public function approveTimesheet() {
		$timesheetObj = $this->objTime;

                /* For checking unfinished timesheets */
		if (TimeEvent::isUnfinishedTimesheet($timesheetObj->getTimesheetId())) {
			$_GET['message'] = 'UNFINISHED_TIMESHEET_FAILURE';
		    $this->_redirectToTimesheet($timesheetObj->getTimesheetId(), $_GET['message']);
		    return false;
		}

		$timesheets = $timesheetObj->fetchTimesheets();

		if (isset($timesheets) && isset($timesheets[0])) {
			$timesheet = $timesheets[0];
		} else {
			$this->redirect('NO_TIMESHEET_FAILURE', '?timecode=Time&action=View_Timesheet');
		}

		$roles = array(authorize::AUTHORIZE_ROLE_ADMIN, authorize::AUTHORIZE_ROLE_SUPERVISOR);
		$role = $this->authorizeObj->firstRole($roles);

		if (!$role || (($role == authorize::AUTHORIZE_ROLE_SUPERVISOR) && (!$this->authorizeObj->isTheSupervisor($timesheet->getEmployeeId())))) {
			$this->redirect('UNAUTHORIZED_FAILURE');
		}

		$timesheet->setComment($timesheetObj->getComment());

      $screenParam = array('timecode' => $_GET['timecode'], 'action' => 'View_Current_Timesheet');
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
      
		$res = false;
      if($_POST['token'] == $token) {
         $res = $timesheet->approveTimesheet();
      }
      
		if ($res) {
			$_GET['message'] = 'APPROVE_SUCCESS';
		} else {
			$_GET['message'] = 'APPROVE_FAILURE';
		}

		$this->_redirectToTimesheet($timesheet->getTimesheetId(), $_GET['message']);

		return $res;
	}

	public function rejectTimesheet() {
		$timesheetObj = $this->objTime;
		$timesheets = $timesheetObj->fetchTimesheets();

		if (isset($timesheets) && isset($timesheets[0])) {
			$timesheet = $timesheets[0];
		} else {
			$this->redirect('NO_TIMESHEET_FAILURE', '?timecode=Time&action=View_Timesheet');
		}

		$roles = array(authorize::AUTHORIZE_ROLE_ADMIN, authorize::AUTHORIZE_ROLE_SUPERVISOR);
		$role = $this->authorizeObj->firstRole($roles);

		if (!$role || (($role == authorize::AUTHORIZE_ROLE_SUPERVISOR) && (!$this->authorizeObj->isTheSupervisor($timesheet->getEmployeeId())))) {
			$this->redirect('UNAUTHORIZED_FAILURE');
		}

		$timesheet->setComment($timesheetObj->getComment());

      $screenParam = array('timecode' => $_GET['timecode'], 'action' => 'View_Current_Timesheet');
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
      
		$res = false;
      if($_POST['token'] == $token) {
         $res = $timesheet->rejectTimesheet();
      }

		if ($res) {
			$_GET['message'] = 'REJECT_SUCCESS';
		} else {
			$_GET['message'] = 'REJECT_FAILURE';
		}

		$this->_redirectToTimesheet($timesheet->getTimesheetId(), $_GET['message']);

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

		$projectObj->setDeleted(Projects::PROJECT_NOT_DELETED);
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

        public function fetchIncludingDeletedProjects($includeDeleted) {
                
                $lan = new Language();
                require ($lan->getLangPath("full.php"));

                $projectsObj = new Projects();
                $customerObj = new Customer();
                $projectArr[]= array(0=>"-1",1=>"{$lang_Time_Common_All}"); //this is the first element

                if ($includeDeleted == 1) {
                    $projectsObj->setDeleted(1);
                    $DeletedProj = $projectsObj->fetchProjects();

                    if (isset($DeletedProj)) {
                        foreach ($DeletedProj as $DeProj) {
                            $customerDet = $customerObj->fetchCustomer($DeProj->getCustomerId(), true);
                            $tmpArr[0] = $DeProj->getProjectId();
                            $tmpArr[1] = $customerDet->getCustomerName() . ' - ' . $DeProj->getProjectName() . ' (Deleted)';
                            $projectArr[] = $tmpArr;                           
                        }
                    }

                    $projectsObj->setDeleted(0);
                    $ActiveProj = $projectsObj->fetchProjects();

                    if (isset($ActiveProj)) {
                        foreach ($ActiveProj as $DeProj) {
                            $customerDet = $customerObj->fetchCustomer($DeProj->getCustomerId(), true);
                            $tmpArr[0] = $DeProj->getProjectId();
                            $tmpArr[1] = $customerDet->getCustomerName() . ' - ' . $DeProj->getProjectName();
                            $projectArr[] = $tmpArr;
                        }
                    }
                    
                } else {
                    $projectsObj->setDeleted(0);
                    $ActiveProj = $projectsObj->fetchProjects();

                    if (isset($ActiveProj)) {
                        foreach ($ActiveProj as $DeProj) {
                            $customerDet = $customerObj->fetchCustomer($DeProj->getCustomerId(), true);
                            $tmpArr[0] = $DeProj->getProjectId();
                            $tmpArr[1] = $customerDet->getCustomerName() . ' - ' . $DeProj->getProjectName();
                            $projectArr[] = $tmpArr;                           
                        }
                    }
                }

                return $projectArr;
        }

	public function viewSelectEmployee() {

		if ($_SESSION['isAdmin'] == 'No' && !$_SESSION['isSupervisor']) {
		    die('You are not authorized to view this page');
		}

		$path = "/templates/time/selectEmployee.php";

		$roles = array(authorize::AUTHORIZE_ROLE_ADMIN, authorize::AUTHORIZE_ROLE_SUPERVISOR);
		$role = $this->authorizeObj->firstRole($roles);

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
		$dataArr['empList'] = EmpInfo::getEmployeeMainDetails();

		$template = new TemplateMerger($dataArr, $path);
		$template->display();
	}

	public function editTimesheet($nextAction, $duplicateRows = false, $invalidDuration = false) {

		$timeEvents = $this->getObjTime();

		$roles = array(authorize::AUTHORIZE_ROLE_ADMIN, authorize::AUTHORIZE_ROLE_SUPERVISOR);
		$role = $this->authorizeObj->firstRole($roles);

		if ($timeEvents == null) {
			$_GET['message'] = 'NO_EVENTS_WARNING';
			$this->redirect($_GET['message'], "?timecode=Time&action={$nextAction}&id={$_GET['id']}");
			return false;
		}

		if ($duplicateRows) {
			$_GET['message'] = 'DUPLICATE_ROWS';
			$this->redirect($_GET['message'], "?timecode=Time&action={$nextAction}&id={$_GET['id']}");
			return false;
		}

		if ($invalidDuration) {
			$_GET['message'] = 'MaxTotalDuration';
			$this->redirect($_GET['message'], "?timecode=Time&action={$nextAction}&id={$_GET['id']}");
			return false;
		}

		if ($_SESSION['empID'] != $timeEvents[0]->getEmployeeId()) {
			if (!$role || (($role == authorize::AUTHORIZE_ROLE_SUPERVISOR) && (!$this->authorizeObj->isTheSupervisor($timeEvents[0]->getEmployeeId())))) {
				$this->redirect('UNAUTHORIZED_FAILURE');
			}
		}

		$_GET['message'] = 'NO_RECORDS_CHANGED_WARNING';
		$result = $this->validateTimeEvents($timeEvents);
		if ($result !== true) {
			$_GET['message'] = $result;
			$res = false;
		} else {

			foreach ($timeEvents as $timeEvent) {
				try {
					if ($timeEvent->getTimeEventId() == null) {
						$res=$timeEvent->addTimeEvent();
					} else {
						$res=$timeEvent->editTimeEvent();
					}

					if ($res) {
						if ($res == 1) {
							$_GET['message'] = 'UPDATE_SUCCESS';
						}
					} else {
						$_GET['message'] = 'UPDATE_FAILURE';
						break;
					}
				} catch (TimeEventException $e) {
					$res=false;
					switch ($e->getCode()) {
						case 2: $_GET['message'] = 'OVERLAPPING_TIME_PERIOD_FAILURE';
								break;
						default:
								$_GET['message'] = 'UPDATE_FAILURE';
								break;
					}
					break;
				}
			}
		}

		if ($res) {
			$this->redirect($_GET['message'], "?timecode=Time&action={$nextAction}&id={$timeEvents[0]->getTimesheetId()}");
		} else {
			$this->redirect($_GET['message'], "?timecode=Time&action=View_Edit_Timesheet&id={$timeEvents[0]->getTimesheetId()}&return={$nextAction}");
		}

		return $res;
	}

	public function saveTimeEvent() {
		$timeEvent = $this->getObjTime();

		if ($timeEvent == null) {
			$this->redirect('INVALID_TIME_FAILURE', "?timecode=Time&action=Show_Punch_Time");
		}

		$timeEvent->resolveTimesheet();

		/* Check whether the timesheet is an approved or rejected one: Begins */

		$timesheetId = $timeEvent->getTimesheetId();

		if (Timesheet::checkTimesheetStatus($timesheetId, Timesheet::TIMESHEET_STATUS_APPROVED)) {
			$_GET['message'] = 'APPROVED_TIMESHEET_FAILURE';
			$this->redirect($_GET['message'], "?timecode=Time&action=Time_Event_Home");
			return;
		}

		/* Check whether the timesheet is an approved or rejected one: Ends */

		try {
			if ($timeEvent->getTimeEventId() == null) {
				$res=$timeEvent->addTimeEvent();
			} else {
				$res=$timeEvent->editTimeEvent();
			}
		} catch (TimeEventException $e) {
			switch ($e->getCode()) {
				case TimeEventException::OVERLAPPING_TIME_PERIOD : $_GET['message'] = 'OVERLAPPING_TIME_PERIOD_FAILURE';
																   break;
				default : $_GET['message'] = 'UNKNOWN_ERROR_FAILURE';
						  break;
			}
		}


		if (isset($res)) {
			if ($res) {
				$_GET['message'] = 'UPDATE_SUCCESS';
			} else {
				$_GET['message'] = 'UPDATE_FAILURE';
			}
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
    		Config::setTimePeriodSet('Yes');
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

		foreach ($timeEvents as $timeEvent) {
			if ($timeEvent->getTimeEventId() != null) {
				$timeEventObjs = $timeEvent->fetchTimeEvents();
				$timeEvent = $timeEventObjs[0];
				if ($_SESSION['empID'] != $timeEvent->getEmployeeId()) {
					if (!$role || (($role == authorize::AUTHORIZE_ROLE_SUPERVISOR) && (!$this->authorizeObj->isTheSupervisor($timeEvent->getEmployeeId())))) {
						$this->redirect('UNAUTHORIZED_FAILURE');
					}
				}
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
        $timeEventObj->setEmployeeId($timesheet->getEmployeeId());
        $timeEventObj->setStartTime($timesheet->getStartDate());
        $timeEventObj->setEndTime($timesheet->getEndDate());

		$timeEvents = $timeEventObj->fetchTimeEvents();

		$path="/templates/time/timesheetEdit.php";

		$customerObj = new Customer();
		$projectObj = new Projects();

		$customers = $customerObj->fetchCustomers();

		// Only fetch non-deleted projects
		$projectObj->setDeleted(Projects::PROJECT_NOT_DELETED);
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
        $timeEventObj->setEmployeeId($timesheet->getEmployeeId());
        $timeEventObj->setStartTime($timesheet->getStartDate());
        $timeEventObj->setEndTime($timesheet->getEndDate());

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

      $screenParam = array('timecode' => $_GET['timecode'], 'action' => 'View_Current_Timesheet');
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
      
		$timesheetObj = $this->objTime;

		if ($timesheetObj->getTimesheetId() != null) {
			$timesheetObj->setEmployeeId(null);
		}

		$timesheets = $timesheetObj->fetchTimesheets($current,Timesheet::TIMESHEET_DB_FIELD_START_DATE,"DESC");

		if (!is_object($timesheets[0])) {
			if (($_SESSION['empID'] == $timesheetObj->getEmployeeId()) && (($timesheetObj->getEmployeeId() != null) && !empty($_SESSION['empID']))) {
				$timesheetObj->addTimesheet();
				$timesheets = $timesheetObj->fetchTimesheets(false,Timesheet::TIMESHEET_DB_FIELD_START_DATE,"DESC");
			} else {
				$this->redirect(self::NO_TIMESHEET_FAILURE, '?timecode=Time&action=View_Select_Employee');
			}
		}

		$timesheet = $timesheets[0];

		$roles = array(authorize::AUTHORIZE_ROLE_ADMIN);
		$dataArr['supView'] = false;
		
		if ($timesheet && $this->authorizeObj->isTheSupervisor($timesheet->getEmployeeId())) {
			$roles[] = authorize::AUTHORIZE_ROLE_SUPERVISOR;
			$dataArr['supView'] = true;
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
		$dataArr['rights'] = $_SESSION['localRights'];
      $dataArr['token'] = $token;

		$template = new TemplateMerger($dataArr, $path);
		$template->display();
	}

	/* Timegrid methods: Begin */

	public function editTimesheetGrid($messageType=null, $message=null, $showComments = 'No') {

      $screenParam = array('timecode' => $_GET['timecode'], 'action' => $_GET['action']);
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
      
		$timesheet = $this->objTime;

		/* Setting Grid array: Begins */
		$timeEventObj = new TimeEvent();
		$timeEventObj->setTimesheetId($timesheet->getTimesheetId());
        $timeEventObj->setEmployeeId($timesheet->getEmployeeId());
        $timeEventObj->setStartTime($timesheet->getStartDate());
        $timeEventObj->setEndTime($timesheet->getEndDate());
        $timeEvents = $timeEventObj->fetchTimeEvents();

        $eventsCount = count($timeEvents);
        $grid = array();
        $activityObj = new ProjectActivity();

        if ($eventsCount > 0) {

	        for ($i=0; $i<$eventsCount; $i++) {

	            $projectId = $timeEvents[$i]->getProjectId();
	            $activityId = $timeEvents[$i]->getActivityId();
	            $gridKey = $timeEvents[$i]->getEmployeeId().'-'.$projectId.'-'.$activityId;
	            $dateKey = strtotime($timeEvents[$i]->getReportedDate());

	            if (!isset($grid[$gridKey])) {
	            	$activityObj->setId($activityId);
	            	$activityObj->fetch();
	            	$projectObj = new Projects();
					$projectObj->setProjectId($projectId);
	            	$projectObj->fetch();

	                $grid[$gridKey]['projectId'] = $projectId; // TODO: Remove this and use project object in the template
	                $grid[$gridKey]['projectObj'] = $projectObj;
	                $grid[$gridKey]['activityId'] = $activityId; // TODO: Remove this and use activity object in the template
					$grid[$gridKey]['activityName'] = $activityObj->getName();
					$grid[$gridKey]['isActivityDeleted'] = $activityObj->isDeleted();
	                $grid[$gridKey]['activityList'] = $activityObj->getActivityList($projectId);

	            }

	            $grid[$gridKey][$dateKey]['duration'] = round($timeEvents[$i]->getDuration()/3600, 2);
	            $grid[$gridKey][$dateKey]['eventId'] = $timeEvents[$i]->getTimeEventId();
	            $grid[$gridKey][$dateKey]['comment'] = $timeEvents[$i]->getDescription();

	        }

	        $records['grid'] = $grid;

        } else {
            $records['grid'] = null;
        }
        /* Setting Grid array: Ends */

		/* Setting Projects List: Begins */
		$projectObj = new Projects();
		$projectObj->setDeleted(Projects::PROJECT_NOT_DELETED);
		$projects = $projectObj->fetchProjects(false);
		$projectsCount = count($projects);

		if ($projectsCount > 0) {

		    for ($i=0; $i<$projectsCount; $i++) {

		        $projectId = $projects[$i]->getProjectId();
		        $projectsList[$i]['name'] = $projects[$i]->retrieveCustomerName($projectId).
		        							' - '.$projects[$i]->getProjectName();
		        $projectsList[$i]['id'] = $projectId;
		        $projectsList[$i]['deleted'] = $projects[$i]->getDeleted();

		    }

		    $records['projectsList'] = $projectsList;

		} else {
		    $records['projectsList'] = null;
		}
		/* Setting Projects List: Ends */

		$records['employeeId'] = $timesheet->getEmployeeId();
		$records['timesheetId'] = $timesheet->getTimesheetId();
		$records['timesheetPeriodId'] = $timesheet->getTimesheetPeriodId();
		$records['startDateStamp'] = strtotime($timesheet->getStartDate());
		$records['endDateStamp'] = strtotime($timesheet->getEndDate());
		if (isset($messageType)) {
		    $records['messageType'] = $messageType;
		    $records['message'] = $message;
		}
		$records['showComments'] = $showComments;
      $records['token'] = $token;

		$path='/templates/time/editTimesheetGrid.php';
		$template = new TemplateMerger($records, $path);
		$template->display();

	}

	public function updateTimegrid($eventsList) {

      $screenParam = array('timecode' => $_GET['timecode'], 'action' => 'Edit_Timesheet_Grid');
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

		$updateList = $eventsList[0];
		$addList = $eventsList[1];
		$updateCount = count($updateList);
		$addCount = count($addList);

		if ($updateCount == 0 && $addCount == 0) { // If there in nothing to update

		    $this->editTimesheetGrid('FAILURE', 'no-changes');

		} else {

		    /* Updating time events */

		    $updateFlag = true;
          $addFlag = true;

          if($token == $_POST['token']) {
             foreach ($updateList as $update) {

                 if (!$update->editTimeEvent()) {
                     $updateFlag = false;
                 }

             }

             /* Adding time events */

             foreach ($addList as $add) {

                 if (!$add->addTimeEvent()) {
                     $addFlag = false;
                 }

             }
          } else {
            $updateFlag = false;
            $addFlag    = false;
          }
		    /* Sending the result back to the UI */

		    if ($updateFlag && $addFlag) {

		        $this->editTimesheetGrid('SUCCESS', 'update-success', $_POST['hdnShowComments']);

		    } else {

		        $this->editTimesheetGrid('FAILURE', 'update-failure', $_POST['hdnShowComments']);

		    }

		}

	}
















	public function prepareProjectActivitiesResponse($projectId=0) {

	    if ($projectId < 0) {
	        return null;
	    }

		$projectActivityObj = new ProjectActivity();
		$projectActivities = $projectActivityObj->getActivityList($projectId);
		$response = '';
		$count = count($projectActivities);

		if (isset($projectActivities)) {

		    for ($i=0; $i<$count; $i++) {

		        if ($i == ($count-1)) {
		            $response .= trim($projectActivities[$i]->getName()).'%'.trim($projectActivities[$i]->getId());
		        } else {
		            $response .= trim($projectActivities[$i]->getName()).'%'.trim($projectActivities[$i]->getId()).';';
		        }

		    }

		}

	    return $response;

	}

	/* Timegrid methods: End */

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

		$customers = $customerObj->fetchCustomers(0, '', -1 , 1);
                $projectObj->setDeleted(0); // choose only not deleted records
		$projects = $projectObj->fetchProjects();

		$dataArr[0] = $role;
		$dataArr[1] = $employees;
		$dataArr[2] = $projects;

		$dataArr['empList'] = array();
		if ($role == authorize::AUTHORIZE_ROLE_ADMIN) {
			$dataArr['empList'] = EmpInfo::getEmployeeMainDetails();
		} elseif ($role == authorize::AUTHORIZE_ROLE_SUPERVISOR) {
			$dataArr['empList'] = $this->_getSubsForAutoComplete($_SESSION['empID']);
		}

		$template = new TemplateMerger($dataArr, $path);
		$template->display();
	}

	/**
	 * View project report
	 */
	public function viewProjectReport() {

		$path="/templates/time/projectReport.php";

		$timeEventObj = $this->objTime[0];
		$startDate = $this->objTime[1];
		$endDate = $this->objTime[2];

		$projectId = $timeEventObj->getProjectId();

		if ((!$this->authorizeObj->isAdmin()) && (!$this->authorizeObj->isProjectAdminOf($projectId))) {
			$this->redirect('UNAUTHORIZED_FAILURE');
		}

		$projectObj = new Projects();
		$project = $projectObj->fetchProject($projectId);

		if (empty($project)) {
			$this->redirect('PROJECT_NOT_FOUND_FAILURE', '?timecode=Time&action=Project_Report_Define');
		}

		$report = new ProjectReport();
		$activityTimeArray = $report->getProjectActivityTime($projectId, $startDate, $endDate);

		$dataArr[0] = $project;
		$dataArr[1] = $startDate;
		$dataArr[2] = $endDate;
		$dataArr[3] = $activityTimeArray;

		$template = new TemplateMerger($dataArr, $path);
		$template->display();
	}

	/**
	 * View activity report
	 */
	public function viewActivityReport() {

		$path="/templates/time/activityReport.php";

		$timeEventObj = $this->objTime[0];
		$startDate = $this->objTime[1];
		$endDate = $this->objTime[2];
		$pageNo = $this->objTime[3];

		$projectId = $timeEventObj->getProjectId();
		$activityId = $timeEventObj->getActivityId();
		$time = $timeEventObj->getDuration();

		$returnUrl = '?timecode=Time&action=Project_Report_Define';

		if ((!$this->authorizeObj->isAdmin()) && (!$this->authorizeObj->isProjectAdminOf($projectId))) {
			$this->redirect('UNAUTHORIZED_FAILURE', $returnUrl);
		}

		$projectObj = new Projects();
		$project = $projectObj->fetchProject($projectId);
		if (empty($project)) {
			$this->redirect('PROJECT_NOT_FOUND_FAILURE', $returnUrl);
		}

		$activity = ProjectActivity::getActivity($activityId);
		if (empty($activity)) {
			$this->redirect('ACTIVITY_NOT_FOUND_FAILURE', $returnUrl);
		}

		if ($projectId != $activity->getProjectId()) {
			$this->redirect('UNAUTHORIZED_FAILURE', $returnUrl);
		}

		$report = new ProjectReport();
		$count = $report->countEmployeesInActivity($projectId, $activityId, $startDate, $endDate);
		$empTimeArray = $report->getEmployeeActivityTime($projectId, $activityId, $startDate, $endDate, $pageNo);

		$dataArr[0] = $project;
		$dataArr[1] = $activity;
		$dataArr[2] = $startDate;
		$dataArr[3] = $endDate;
		$dataArr[4] = $empTimeArray;
		$dataArr[5] = $count;
		$dataArr[6] = $time;
		$dataArr[7] = $pageNo;

		$template = new TemplateMerger($dataArr, $path);
		$template->display();
	}

	/**
	 * View the define page for detailed project reports in time module
	 */
	public function viewDefineProjectReport() {

		$path="/templates/time/defineProjectReport.php";

		/* If a HR admin, show all projects. Otherwise only show projects for which
		 * user is an admin
		 */
		if ($this->authorizeObj->isAdmin()) {

			$projects = new Projects();

			/* Filter only not deleted projects */
			$projectList = $projects->fetchProjects();
		} else if ($this->authorizeObj->isProjectAdmin()) {

			$gw = new ProjectAdminGateway();
			$projectList = $gw->getProjectsForAdmin($_SESSION['empID'] , TRUE);
		} else {
			die('You are not authorized to view this page');
		}


		$dataArr[0] = $projectList;

		$template = new TemplateMerger($dataArr, $path);
		$template->display();
	}

	public function viewEmployeeTimeReport() {

		$path="/templates/time/employeeTimeReport.php";

		$timeEventObj = $this->objTime[0];
		$startDate = $this->objTime[1];
		$endDate = $this->objTime[2];

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

		if ($_SESSION['isAdmin'] == 'No' && !$_SESSION['isSupervisor']) {
		    die('You are not authorized to view this page');
		}

		$path="/templates/time/selectTimesheets.php";

		$dataArr = null;

		$employmentStatusObj = new EmploymentStatus();

		$dataArr[0] = $employmentStatusObj->getListofEmpStat(0, '', -1, 1);

		if ($_SESSION['isSupervisor']) {
			$repObj = new EmpRepTo();
			$dataArr[1] = $repObj->getEmpSubDetails($_SESSION['empID']);
		}

		$dataArr['empList'] = array();
		/* Setting employee list for Auto-Complete */
		if ($this->authorizeObj->isAdmin()) {
			$dataArr['empList'] = EmpInfo::getEmployeeMainDetails();
		} elseif ($this->authorizeObj->isSupervisor()) {
			$dataArr['empList'] = $this->_getSubsForAutoComplete($_SESSION['empID']);
		}

      $screenParam = array('timecode' => $_GET['timecode'], 'action' => $_GET['action']);
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
      $dataArr['token'] = $token;

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

			$timsheetIds = $this->_getTimesheetIds($employeeIds , $timesheetObj);
			$timesheetsCount =count($timsheetIds);

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

		$timsheetIds = $this->_getTimesheetIds($employeeIds , $timesheetObj);
		$timesheets = $timesheetObj->fetchTimesheetsByTimesheetIdBulk($page, $timsheetIds);

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
		$dataArr[2]=$timesheetObj->getStartDate();
		$dataArr[3]=$timesheetObj->getEndDate();

		$template = new TemplateMerger($dataArr, $path, "stubHeader.php", "stubFooter.php");
		$template->display();
	}

	/**
	 * Generate timesheetIds for startDate , endDate and employeeIds
	 *
	 * @param Array $employeeIds
	 * @param Object $timesheetObj
	 * @return Array $timsheetIds
	 */

	private function _getTimesheetIds($employeeIds ,  $timesheetObj){

		$timsheetIds = NULL;
		$timeEventObj  = new TimeEvent();
		$timeEventObj->setStartTime($timesheetObj->getStartDate());
		$timeEventObj->setEndTime($timesheetObj->getEndDate());
		$timsheetIds = $timeEventObj->fetchTimeSheetIds($employeeIds);

		if(count($timsheetIds)){

			foreach($timsheetIds as $key=>$timeSheetId){

				$dateFound = FALSE;
				$timeSheetObj  = new Timesheet();
				$timeSheetObj->setTimesheetId($timeSheetId);
				list($tempTimeSheetObj) = $timeSheetObj->fetchTimesheets();
				$timeSheetStartDate = strtotime(date('Y-m-d', strtotime($tempTimeSheetObj->getStartDate())));
				$timeSheetEndDate = strtotime(date('Y-m-d', strtotime($tempTimeSheetObj->getEndDate())));
				for($i=strtotime($timesheetObj->getStartDate()); $i<=strtotime($timesheetObj->getEndDate()); $i+=3600*24) {
					if($i >= $timeSheetStartDate && $i <= $timeSheetEndDate){
						$dateFound = TRUE;
						break;
					}
				}
				if(!$dateFound){
					unset($timsheetIds[$key]);
				}
			}
		}

		return $timsheetIds;

	}

	/**
	 * Parse time events and generate the information for timesheets
	 *
	 * @param Timesheet timesheet
	 */
	private function _generateTimesheet($timesheet) {

		$timeEventObj = new TimeEvent();

		$timeEventObj->setTimesheetId($timesheet->getTimesheetId());
		$timeEventObj->setEmployeeId($timesheet->getEmployeeId());
		$timeEventObj->setStartTime($timesheet->getStartDate());
		$timeEventObj->setEndTime($timesheet->getEndDate()) ;
		$timeEvents = $timeEventObj->fetchTimeEvents();

		$durationArr = null;
		$dailySum = null;
		$activitySum = null;
		$totalTime = 0;

		for ($i=0; $i<count($timeEvents); $i++) {
			$projectId=$timeEvents[$i]->getProjectId();
			$activityId=$timeEvents[$i]->getActivityId();

			if ($timeEvents[$i]->getStartTime() != null) {
				$expenseDate=strtotime(date('Y-m-d', strtotime($timeEvents[$i]->getStartTime())));
			} else {
				$expenseDate=strtotime(date('Y-m-d', strtotime($timeEvents[$i]->getReportedDate())));
			}
			if (!isset($durationArr[$projectId][$activityId][$expenseDate])) {
				$durationArr[$projectId][$activityId][$expenseDate]=0;
			}
			if (!isset($dailySum[$expenseDate])) {
				$dailySum[$expenseDate]=0;
			}
			if (!isset($activitySum[$projectId][$activityId])) {
				$activitySum[$projectId][$activityId]=0;
			}

			$durationArr[$projectId][$activityId][$expenseDate]+=$timeEvents[$i]->getDuration();
			$dailySum[$expenseDate]+=$timeEvents[$i]->getDuration();
			$activitySum[$projectId][$activityId]+=$timeEvents[$i]->getDuration();
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

	/**
	 * Validates given array of time events.
	 * This function acts as a second level of validation. The time events
	 * should have been validated in client side javascript as well.
	 *
	 * @param array $timeEventArray Array of time event objects to validate.
	 * @return mixed true if validate success, error string if not.
	 */
	public function validateTimeEvents($timeEventArray) {

		$timesheetArray = array();

		foreach($timeEventArray as $timeEvent) {

			$timesheetId = $timeEvent->getTimesheetId();

			if (empty($timesheetId)) {
				return  self::NO_TIMESHEET_FAILURE;
			}

			if (isset($timesheetArray[$timesheetId])) {
				$timesheet = $timesheetArray[$timesheetId];
			} else {
				$tmpSheet = new Timesheet();
				$tmpSheet->setTimesheetId($timesheetId);
				$sheets = $tmpSheet->fetchTimesheets();

				if (empty($sheets) || !is_object($sheets[0])) {
					return  self::NO_TIMESHEET_FAILURE;
				}
				$timesheet = $sheets[0];
			}

			$timesheetArray[$timesheetId] = $timesheet;
			$result = $this->validateTimeEvent($timeEvent, $timesheet);

			if (!($result === true)) {
				return $result;
			}
		}

		return true;
	}

	/**
	 * Validates the timeevent against the given timesheet.
	 *
	 * @param TimeEvent $timeEvent Time event to validate
	 * @param Timesheet $timesheet Time sheet
	 * @return mixed true if validate success, error string if not.
	 */
	public function validateTimeEvent($timeEvent, $timesheet) {

		$eventStartTime = $timeEvent->getStartTime();
		$eventEndTime = $timeEvent->getEndTime();
		$eventStart = strtotime($eventStartTime);
		$eventEnd = strtotime($eventEndTime);

		$periodStartDate = $timesheet->getStartDate();
		$periodEndDate = $timesheet->getEndDate();
		$periodStart = strtotime($periodStartDate);
		$periodEnd = strtotime($periodEndDate);

		$periodEnd = strtotime("+1 day", $periodEnd);
		// strtotime returns false (-1 before php 5.1.0) on error
		if (!($periodStart > 0) || !($periodEnd > 0) || ($periodStart >= $periodEnd)) {
			return self::INVALID_TIMESHEET_PERIOD_ERROR;
		}

		$reportedDate = $timeEvent->getReportedDate();
		$reported = strtotime($reportedDate);

		$eventId = $timeEvent->getTimeEventId();
		$newEvent = empty($eventId);

		if (!CommonFunctions::IsValidId($timeEvent->getProjectId())) {
			return self::ProjectNotSpecified_ERROR;
		}

		if (!CommonFunctions::IsValidId($timeEvent->getActivityId())) {
			return self::ActivityNotSpecified_ERROR;
		}

		if (!empty($eventStartTime) && !($eventStart > 0)) {
			return self::InvalidStartTime_ERROR;
		}

		if (!empty($eventEndTime) && !($eventEnd > 0)) {
			return self::InvalidEndTime_ERROR;
		}

		if (empty($reportedDate)) {
			return self::ReportedDateNotSpecified_ERROR;
		} else if (!($reported > 0)) {
			return self::InvalidReportedDate_ERROR;
		}

		$duration = $timeEvent->getDuration();
		$duration = ($duration === "") ? null : $duration;

		// 0 not allowed for duration in last row.
		if (!is_null($duration) && (($duration < 0) || ($newEvent && $duration == 0))) {
			return self::InvalidDuration_ERROR;
		}

		// Validate period/interval
		if (empty($eventStartTime) && empty($eventEndTime) && !empty($duration)) {

			// reported date + duration
			if (($reported < $periodStart) || (($reported + $duration) > $periodEnd)) {
				return self::EVENT_OUTSIDE_PERIOD_FAILURE;
			}
		} else if (!empty($eventStartTime) && empty($eventEndTime) && is_null($duration)) {

			// start time only
			if (($eventStart < $periodStart) || ($eventStart > $periodEnd)) {
				return self::EVENT_OUTSIDE_PERIOD_FAILURE;
			}
		} else if (!empty($eventStartTime) && !empty($eventEndTime)) {

			if (!empty($duration) && $newEvent) {
				return self::NotAllowedToSpecifyDurationAndInterval_ERROR;
			}

			// start and end time
			if ($eventStart >= $eventEnd) {
				return self::ZeroOrNegativeIntervalSpecified_ERROR;
			}

			if (($eventStart < $periodStart) || ($eventEnd > $periodEnd)) {
				return self::EVENT_OUTSIDE_PERIOD_FAILURE;
			}

			$timeEvent->setDuration($eventEnd - $eventStart);

		} else if (!empty($eventStartTime) && !empty($duration) && empty($eventEndTime)) {

			// start time and duration
			if (($eventStart < $periodStart) || (($eventStart + $duration) > $periodEnd)) {
				return self::EVENT_OUTSIDE_PERIOD_FAILURE;
			}

			$timeEvent->setEndTime(date("Y-m-d H:i", $eventStart + $duration));
		} else {
			return self::NoValidDurationOrInterval_ERROR;
		}

		return true;
	}

	public function viewShifts() {

		if ($_SESSION['isAdmin'] == 'No') {
		    die('You are not authorized to view this page');
		}

      $screenParam = array('timecode' => $_GET['timecode'], 'action' => $_GET['action']);
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

		$path = "/templates/time/workShifts.php";

		$objs[] = Workshift::getWorkshifts();
		$objs['rights']   = $_SESSION['localRights'];
      $objs['token']    = $token;

		$template = new TemplateMerger($objs, $path);
		$template->display();
	}

	/**
	 * View the worksheet edit page
	 * @param int $id The workshift Id
	 */
	public function viewEditWorkShift($id) {
		$path = "/templates/time/editWorkShift.php";

      $screenParam = array('timecode' => $_GET['timecode'], 'action' => $_GET['action'], 'id' => $id);
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

		try {
			$workshift = Workshift::getWorkshift($id);

			$objs[] = $workshift;
			$objs[] = $workshift->getAssignedEmployees();
			$objs[] = $workshift->getEmployeesWithoutWorkshift();
			$objs['rights']   = $_SESSION['localRights'];
         $objs['token']    = $token;

			$template = new TemplateMerger($objs, $path);
			$template->display();

		} catch (WorkshiftException $e) {

			switch ($e->getCode()) {
				case WorkshiftException::WORKSHIFT_NOT_FOUND:
					$msg = 'INVALID_WORK_SHIFT_FAILURE';
					break;
				default:
					$msg = 'UNKNOWN_ERROR_FAILURE';
					break;
			}
			$this->redirect($msg, '?timecode=Time&action=View_Work_Shifts');
		}
	}

	public function saveWorkShift() {
		$workShift = $this->getObjTime();

      $screenParam = array('timecode' => $_GET['timecode'], 'action' => 'View_Work_Shifts');
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));
      
		try {
         $res = false;
         if($token == $_POST['token']) {
            $res = $workShift->save();
         }
		} catch (WorkshiftException $exception) {
			$this->redirect('INVALID_WORK_SHIFT_FAILURE', '?timecode=Time&action=View_Work_Shifts');
		}

		if ($res) {
			$this->redirect('UPDATE_SUCCESS', '?timecode=Time&action=View_Work_Shifts');
		} else {
			$this->redirect('UPDATE_FAILURE', '?timecode=Time&action=View_Work_Shifts');
		}
	}

	public function updateWorkShift() {
		
		$obj = $this->getObjTime();
		$workShift = $obj[0];
		$assignedEmployees = $obj[1];
		$id = $workShift->getWorkshiftId();

      $screenParam = array('timecode' => $_GET['timecode'], 'action' => "View_Edit_Work_Shift", 'id' => $id);
      $tokenGenerator = CSRFTokenGenerator::getInstance();
      $tokenGenerator->setKeyGenerationInput($screenParam);
      $token = $tokenGenerator->getCSRFToken(array_keys($screenParam));

		try {
         if($token == $_POST['token']) {
            $workShift->save();
            $workShift->removeAssignedEmployees();
            $workShift->assignEmployees($assignedEmployees);
         }
			/* Updating pending leaves accordingly: Begins */
			
			$empList = $workShift->getAssignedEmployees();
			
			if (!empty($empList)) {
				
				foreach ($empList as $emp) {
				    $empIdList[] = $emp[Workshift::DB_FIELD_EMP_NUMBER];
				}
				
			    $duration = $workShift->getHoursPerDay();
			    $leaveObj = new Leave();
			    
			    if (!$leaveObj->adjustLeaveToWorkshift($duration, $empIdList)) {
			        throw new Exception('Updating pending leaves failed for new workshift value');
			    }
			    
			}
			
			/* Updating pending leaves accordingly: Ends */

		} catch (WorkshiftException $exception) {
			$this->redirect('UPDATE_FAILURE', '?timecode=Time&action=View_Edit_Work_Shift&id='.$id);
		}

		$this->redirect('UPDATE_SUCCESS', '?timecode=Time&action=View_Edit_Work_Shift&id='.$id);
	}

	public function deleteWorkShifts() {
		$workShifts = $this->getObjTime();

		try {
			foreach ($workShifts as $workShift) {
				$res = $workShift->delete();
			}
			$mes = 'DELETE_SUCCESS';
		} catch (WorkshiftException $exception) {
			switch ($exception->getCode()) {
				case WorkshiftException::ERROR_IN_DB_QUERY : // fall through
				case WorkshiftException::INVALID_ROW_COUNT :
					$mes = 'DELETE_FAILURE';
					break;
				case WorkshiftException::INVALID_ID :
					$mes = 'INVALID_ID_FAILURE';
                    break;
				default:
				    $mes = 'UNKNOWN_ERROR_FAILURE';
					break;
			}
		}

		$this->redirect($mes, '?timecode=Time&action=View_Work_Shifts');
	}
	
	public function deleteTimeGridRows() {
		$timeEvent = new TimeEvent();
		$ids = $_POST['deletionIds'];
		
		$deletedRowCount = 0;
		$totalRowCount = count($ids);		
		
		foreach ($ids as $id) {
			$timeEvent->setTimeEventId($id);
			$success = (bool) $timeEvent->deleteTimeEvent();
			if ($success) {
				$deletedRowCount++;
			}
		}
		
		if ($totalRowCount == $deletedRowCount) {
			$messageType = 'SUCCESS';
			$message = 'row-delete-success';
		} elseif ($deletedRowCount == 0) {
			$messageType = 'FAILURE';
			$message = 'row-delete-failure';
		} else {
			$messageType = 'WARNING';
			$message = 'row-delete-partial-success';
		}

		$this->editTimesheetGrid($messageType, $message);
	}
}
?>
