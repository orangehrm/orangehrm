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

require_once ROOT_PATH . '/lib/models/time/TimeEvent.php';
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';
require_once ROOT_PATH . '/lib/common/LocaleUtil.php';

class EXTRACTOR_TimeEvent {

	public function __construct() {
		//nothing to do
	}

	public function parseEditData($postArr) {
		$tmpArr = null;

		for ($i=0; $i<count($postArr['cmbActivity']); $i++) {
				$tmpObj = new TimeEvent();

				$projectId = $postArr['cmbProject'][$i];
				if (!CommonFunctions::isValidId($projectId)) {
					continue;
				}
				$tmpObj->setActivityId($postArr['cmbActivity'][$i]);

				$tmpObj->setProjectId($projectId);

				$txtStartTime = trim($postArr['txtStartTime'][$i]);
				if (!empty($txtStartTime)) {
					$tmpObj->setStartTime(LocaleUtil::getInstance()->convertToStandardDateTimeFormat($txtStartTime));
				}

				$txtEndTime = trim($postArr['txtEndTime'][$i]);
				if (!empty($txtEndTime)) {
					$tmpObj->setEndTime(LocaleUtil::getInstance()->convertToStandardDateTimeFormat($txtEndTime));
				}

				$txtReportedDate = trim($postArr['txtReportedDate'][$i]);
				$tmpObj->setReportedDate(LocaleUtil::getInstance()->convertToStandardDateFormat($txtReportedDate));

				if (isset($postArr['txtDuration'][$i])) {

					$txtDuration = trim($postArr['txtDuration'][$i]);
					if (!empty($txtDuration)) {
						$tmpObj->setDuration($txtDuration*3600);
					}
				}

				$tmpObj->setDescription(stripslashes($postArr['txtDescription'][$i]));

				if (isset($postArr['txtTimeEventId'][$i])) {
					$tmpObj->setTimeEventId(trim($postArr['txtTimeEventId'][$i]));
				}
				$tmpObj->setEmployeeId(trim($postArr['txtEmployeeId']));
				$tmpObj->setTimesheetId(trim($postArr['txtTimesheetId']));

				$tmpArr[] = $tmpObj;
		}

		return $tmpArr;
	}

	public function parseDeleteData($postArr) {
		$tmpArr = null;

		for ($i=0; $i<count($postArr['deleteEvent']); $i++) {
			$tmpObj = new TimeEvent();

			$tmpObj->setTimeEventId($postArr['deleteEvent'][$i]);

			$tmpArr[] = $tmpObj;
		}

		return $tmpArr;
	}

	public function parsePunch($postArr, $punchIn) {
		$tmpObj = new TimeEvent();

		$tmpObj->setProjectId(0);
		$tmpObj->setActivityId(TimeEvent::TIME_EVENT_PUNCH_ACTIVITY_ID);
		$tmpObj->setEmployeeId($_SESSION['empID']);

		$txtDate = LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtDate']);
		$txtTime = LocaleUtil::getInstance()->convertToStandardTimeFormat($postArr['txtTime'], null, true);

		if ($punchIn) {
			$tmpObj->setStartTime("{$txtDate} {$txtTime}");
			$tmpObj->setDuration(0);
		} else {
			$txtStartTime = LocaleUtil::getInstance()->convertToStandardDateTimeFormat($postArr['startTime']);
			$startTime = strtotime($txtStartTime);
			$endTime = strtotime("{$txtDate} {$txtTime}");

			if ($startTime >= $endTime) {
				return null;
			}
			$tmpObj->setStartTime($txtStartTime);
			$tmpObj->setEndTime("{$txtDate} {$txtTime}");
			$tmpObj->setDuration($endTime-$startTime);
			$tmpObj->setTimeEventId($postArr['timeEventId']);
		}
		$tmpObj->setReportedDate($txtDate);
		$tmpObj->setDescription($postArr['txtNote']);

		return $tmpObj;
	}

	public function parseSingleEvent($postArr) {
		$tmpObj = new TimeEvent();

		$tmpObj->setProjectId($postArr['cmbProject']);
		$tmpObj->setActivityId($postArr['cmbActivity']);

		if (!empty($postArr['txtStartTime'])) {
			$tmpObj->setStartTime(LocaleUtil::getInstance()->convertToStandardDateTimeFormat($postArr['txtStartTime']));
		}

		if (!empty($postArr['txtEndTime'])) {
			$tmpObj->setEndTime(LocaleUtil::getInstance()->convertToStandardDateTimeFormat($postArr['txtEndTime']));
		}

		$tmpObj->setReportedDate(LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtReportedDate']));

		if (isset($postArr['txtDuration']) && !empty($postArr['txtDuration'])) {
			$tmpObj->setDuration(trim($postArr['txtDuration'])*3600);
		} else if (isset($postArr['txtStartTime']) && isset($postArr['txtEndTime'])){
			$startTime=strtotime($tmpObj->getStartTime());
			$endTime=strtotime($tmpObj->getEndTime());
			if ($endTime > $startTime) {
				$tmpObj->setDuration($endTime-$startTime);
			} else {
				$tmpObj->setDuration(0);
			}
		}

		$tmpObj->setDescription(stripslashes($postArr['txtDescription']));

		if (isset($postArr['txtTimeEventId'])) {
			$tmpObj->setTimeEventId($postArr['txtTimeEventId']);
		}

		$tmpObj->setEmployeeId($_SESSION['empID']);

		return $tmpObj;
	}

	public function parseReportParams($postArr) {
		$tmpObj = new TimeEvent();

		$tmpObj->setEmployeeId($postArr['txtRepEmpID']);

		if ($postArr['cmbProject'] > -1) {
			$tmpObj->setProjectId($postArr['cmbProject']);
		}

		if ($postArr['cmbActivity'] > -1) {
			$tmpObj->setActivityId($postArr['cmbActivity']);
		}

		$fromDate = LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtFromDate']);
		$toDate = LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtToDate']);

		return array($tmpObj, $fromDate, $toDate);
	}

	public function parseProjectReportParams($postArr) {
		$tmpObj = new TimeEvent();
		$tmpObj->setProjectId($postArr['cmbProject']);

		$fromDate = LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtFromDate']);
		$toDate = LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtToDate']);

		return array($tmpObj, $fromDate, $toDate);
	}

	public function parseActivityReportParams($postArr) {
		$tmpObj = new TimeEvent();

		$tmpObj->setProjectId($postArr['cmbProject']);
		$tmpObj->setActivityId($postArr['activityId']);
		$tmpObj->setDuration($postArr['time']);

		$fromDate  = LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtFromDate']);
		$toDate = LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtToDate']);
		$pageNo = isset($_POST['pageNo']) ? $postArr['pageNo']:1;

		return array($tmpObj, $fromDate, $toDate, $pageNo);
	}











	
	public function parseEditTimegrid($postArr) {
	 
		$gridCount = $postArr['hdnGridCount'];
		$datesCount = $postArr['hdnDatesCount'];
		$employeeId = $postArr['txtEmployeeId'];
		$timesheetId = $postArr['txtTimesheetId'];
		$updateList = array();
		$addList = array();
		 
		for ($i=0; $i<$gridCount; $i++) {
		     
		    for ($j=0; $j<$datesCount; $j++) {
		         
		        if (isset($postArr["hdnTimeEventId-$i-$j"]) ) { // An exsiting time event

			        if (($postArr["txtDuration-$i-$j"] != $postArr["hdnDuration-$i-$j"]) ||
			        	($postArr["cmbProject-$i"] != $postArr["hdnProject-$i"]) || 
			        	($postArr["cmbActivity-$i"] != $postArr["hdnActivity-$i"])) {
			        // If there is no change from previous value, no need to update the time event
			        // This check can only be done if $postArr["hdnTimeEventId-$i-$j"] is set
			         
			        	$timeEvent = new TimeEvent();
			         	
			         	$timeEvent->setTimeEventId($postArr["hdnTimeEventId-$i-$j"]);
			         	$timeEvent->setEmployeeId($employeeId);
			         	$timeEvent->setProjectId($postArr["cmbProject-$i"]);
			         	$timeEvent->setActivityId($postArr["cmbActivity-$i"]);
			         	$timeEvent->setDuration($postArr["txtDuration-$i-$j"] * 3600);
			         	$timeEvent->setReportedDate($postArr["hdnReportedDate-$j"]);
			         
			         	$updateList[] = $timeEvent; //var_dump($updateList); die;
			         	
			        } 	
		             
				} else { // A new time event
		         
					if ($postArr["txtDuration-$i-$j"] != '') { // If no value has been put, no need to add a new time event

			        	$timeEvent = new TimeEvent();
			         	
			         	$timeEvent->setTimesheetId($timesheetId);
			         	$timeEvent->setEmployeeId($employeeId);
			         	$timeEvent->setProjectId($postArr["cmbProject-$i"]);
			         	$timeEvent->setActivityId($postArr["cmbActivity-$i"]);
			         	$timeEvent->setDuration($postArr["txtDuration-$i-$j"] * 3600);
			         	$timeEvent->setReportedDate($postArr["hdnReportedDate-$j"]);
			         
			         	$addList[] = $timeEvent;
		         	    
		         	}
		             
				}
		         
			}
		     
		}
		
		$eventsList[0] = $updateList;
		$eventsList[1] = $addList;
	 
		return $eventsList;	 
	 
	}













}
?>
