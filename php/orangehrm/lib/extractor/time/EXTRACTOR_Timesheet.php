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
require_once ROOT_PATH . '/lib/models/time/TimesheetSubmissionPeriod.php';

class EXTRACTOR_Timesheet {

	private $parent_Timesheet;

	public function __construct() {
		//nothing to do
	}

	public function parseViewData($postArr) {

		$this->parent_Timesheet = new Timesheet();

		if (isset($postArr['txtStartDate'])) {
			$this->parent_Timesheet->setStartDate(date('Y-m-d', strtotime($postArr['txtStartDate'])));
		}

		if (isset($postArr['txtEndDate'])) {
			$this->parent_Timesheet->setEndDate(date('Y-m-d', strtotime($postArr['txtEndDate'])));
		}

		if (isset($postArr['txtTimesheetPeriodId'])) {
			$this->parent_Timesheet->setTimesheetPeriodId($postArr['txtTimesheetPeriodId']);
		}

		if (isset($postArr['txtTimesheetId'])) {
			$this->parent_Timesheet->setTimesheetId($postArr['txtTimesheetId']);
		}

		if (isset($postArr['txtEmployeeId'])) {
			$this->parent_Timesheet->setEmployeeId($postArr['employeeId']);
		} else if (isset($_SESSION['empID'])){
			$this->parent_Timesheet->setEmployeeId($_SESSION['empID']);
		}

		return $this->parent_Timesheet;
	}

	public function parseChangeStatusData($postArr) {
		$this->parent_Timesheet = new Timesheet();

		$this->parent_Timesheet->setTimesheetId($postArr['txtTimesheetId']);

		return $this->parent_Timesheet;
	}
}
?>
