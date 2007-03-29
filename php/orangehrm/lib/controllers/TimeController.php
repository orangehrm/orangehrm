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

class TimeController {

	private $objTime;
	private $id;

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

	}

	public function __distruct() {

	}

	public function submitTimesheet() {
		$timesheetObj = $this->objTime;

		$res=$timesheetObj->submitTimesheet();
		if ($res) {
			$_GET['message'] = 'SUBMIT_SUCCESS';
		} else {
			$_GET['message'] = 'SUBMIT_FAILURE';
		}

		return $res;
	}

	public function viewTimesheet() {

		$timesheetObj = $this->objTime;

		$timesheets = $timesheetObj->fetchTimesheets();

		if ($timesheets == null) {
			$timesheetObj->addTimesheet();

			$timesheets = $timesheetObj->fetchTimesheets();
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
			$expenseDate=strtotime(date('Y-m-d', strtotime($timeEvents[$i]->getStartTime())));
			if (!isset($durationArr[$projectId][$expenseDate])) {
				$durationArr[$projectId][$expenseDate]=0;
			}
			if (!isset($dailySum[$expenseDate])) {
				$dailySum[$expenseDate]=0;
			}
			$durationArr[$projectId][$expenseDate]+=$timeEvents[$i]->getDuration();
			$dailySum[$expenseDate]+=$timeEvents[$i]->getDuration();
		}

		$path="/templates/time/timesheetView.php";

		$dataArr[0]=$durationArr;
		$dataArr[1]=$timesheet;
		$dataArr[2]=$timesheet;
		$dataArr[3]=$timesheetSubmissionPeriod[0];
		$dataArr[4]=$dailySum;

		$template = new TemplateMerger($dataArr, $path);
		$template->display();
	}

}
?>
