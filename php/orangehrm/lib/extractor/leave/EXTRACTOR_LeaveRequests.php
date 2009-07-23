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

require_once ROOT_PATH . '/lib/models/leave/LeaveRequests.php';

class EXTRACTOR_LeaveRequests {

	private $parent_Leave;

	function __construct() {
		$this->parent_Leave = new LeaveRequests();
	}

	public function parseAddData($postArr, $admin=false) {

		// Extract dates
		$postArr['txtLeaveFromDate'] = LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtLeaveFromDate']);
		$postArr['txtLeaveToDate'] = LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtLeaveToDate']);

		// Extract time
		$postArr['sltLeaveFromTime'] = LocaleUtil::getInstance()->convertToStandardTimeFormat($postArr['sltLeaveFromTime']);
		$postArr['sltLeaveToTime'] = LocaleUtil::getInstance()->convertToStandardTimeFormat($postArr['sltLeaveToTime']);

		if ($admin) {
			$this->parent_Leave->setEmployeeId($postArr['cmbEmployeeId']);
		} else {
			$this->parent_Leave->setEmployeeId($_SESSION['empID']);
		}

		$this->parent_Leave->setLeaveTypeId($postArr['sltLeaveType']);
		$this->parent_Leave->setLeaveFromDate($postArr['txtLeaveFromDate']);

		if (isset($postArr['txtLeaveToDate']) && !empty($postArr['txtLeaveToDate'])) {
			$this->parent_Leave->setLeaveToDate($postArr['txtLeaveToDate']);
		} else {
			$this->parent_Leave->setLeaveToDate($postArr['txtLeaveFromDate']);
		}

		if (($this->parent_Leave->getLeaveFromDate() == $this->parent_Leave->getLeaveToDate()) && ($this->parent_Leave->getLeaveFromDate() != null)) {

			$lengthHours = $postArr['txtLeaveTotalTime'];

			if (!empty($postArr['sltLeaveFromTime']) && !empty($postArr['sltLeaveToTime'])) {
				$this->parent_Leave->setStartTime($postArr['sltLeaveFromTime']);
				$this->parent_Leave->setEndTime($postArr['sltLeaveToTime']);
			}

			$this->parent_Leave->setLeaveLengthHours($lengthHours);
		} else {
			$lengthDays = 1;
			$this->parent_Leave->setLeaveLengthDays($lengthDays);
		}

		$this->parent_Leave->setLeaveComments($postArr['txtComments']);

		return $this->parent_Leave;
	}


	/**
	 * Pares edit data in the UI form
	 *
	 * @param mixed $postArr
	 * @return Leave[]
	 */
	public function parseEditData($postArr) {

		$objLeave = null;

		if (isset($postArr['cmbStatus'])) {
			for ($i=0; $i < count($postArr['cmbStatus']); $i++) {
				$tmpObj = new LeaveRequests();
				$tmpObj->setLeaveRequestId($postArr['id'][$i]);
				$tmpObj->setLeaveStatus($postArr['cmbStatus'][$i]);
				$tmpObj->setLeaveComments($postArr['txtComment'][$i]);

				if (isset($postArr['txtEmployeeId'][$i])) {
					$tmpObj->setEmployeeId($postArr['txtEmployeeId'][$i]);
				}

				$objLeave[] = $tmpObj;
			}
		}

		return $objLeave;
	}

	/**
	 * Pares delete data in the UI form
	 *
	 * @param mixed $postArr
	 * @return Leave[]
	 */
	public function parseDeleteData($postArr) {
		$objLeave = null;

		if (isset($postArr['cmbStatus'])) {
			for ($i=0; $i < count($postArr['cmbStatus']); $i++) {				
					$tmpObj = new LeaveRequests();
					$tmpObj->setLeaveRequestId($postArr['id'][$i]);
					$tmpObj->setLeaveComments($postArr['txtComment'][$i]);
					$tmpObj->setLeaveStatus($postArr['cmbStatus'][$i]);

					$objLeave[] = $tmpObj;
				
			}
		}

		return $objLeave;
	}

}
?>
