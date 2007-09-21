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

class EXTRACTOR_Leave {

	private $parent_Leave;

	function __construct() {
		$this->parent_Leave = new LeaveRequests();
	}

	public function parseAddData($postArr) {

		// Extract dates
		$postArr['txtLeaveFromDate'] = LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtLeaveFromDate']);
		$postArr['txtLeaveToDate'] = LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtLeaveToDate']);

		$this->parent_Leave->setEmployeeId($_SESSION['empID']);
		$this->parent_Leave->setLeaveTypeId($postArr['sltLeaveType']);
		$this->parent_Leave->setLeaveFromDate($postArr['txtLeaveFromDate']);

		if (isset($postArr['txtLeaveToDate']) && !empty($postArr['txtLeaveToDate'])) {
			$this->parent_Leave->setLeaveToDate($postArr['txtLeaveToDate']);
		} else {
			$this->parent_Leave->setLeaveToDate($postArr['txtLeaveFromDate']);
		}

		$this->parent_Leave->setLeaveLength($postArr['sltLeaveLength']);
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
				$tmpObj = new Leave();
				$tmpObj->setLeaveId($postArr['id'][$i]);
				$tmpObj->setLeaveStatus($postArr['cmbStatus'][$i]);
				$tmpObj->setLeaveComments($postArr['txtComment'][$i]);

				if (isset($postArr['txtEmployeeId'][$i])) {
					$tmpObj->setEmployeeId($postArr['txtEmployeeId'][$i]);
					$tmpObj->setEmployeeName($postArr['txtEmployeeName'][$i]);
				}

				$tmpObj->setLeaveTypeName($postArr['txtLeaveTypeName'][$i]);
				$tmpObj->setLeaveRequestId($postArr['txtLeaveRequestId'][$i]);

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
				if ($postArr['cmbStatus'][$i] == 0) {
					$tmpObj = new Leave();
					$tmpObj->setLeaveId($postArr['id'][$i]);
					$tmpObj->setLeaveComments($postArr['txtComment'][$i]);

					$tmpObj->setEmployeeId($postArr['txtEmployeeId'][$i]);

					$tmpObj->setLeaveRequestId($postArr['txtLeaveRequestId'][$i]);

					$objLeave[] = $tmpObj;
				}
			}
		}

		return $objLeave;
	}

}
?>