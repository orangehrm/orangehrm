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

require_once ROOT_PATH . '/lib/models/leave/LeaveTakenRequests.php';

class EXTRACTOR_LeaveTakenRequests {

	public function parseUpdateData ($postArr) {

		$leaveId 		= $postArr['leaveId'];
		$leaveStatus	= $postArr['leaveStatus'];
		$leaveComments 	= $postArr['leaveComments'];
		$leaveYear		= $postArr['leaveYear'];
		$noHours		= $postArr['noHours'];
		$leaveTypeId 	= $postArr['leaveTypeId'];
		$employeeId 	= $postArr['employeeId'];

		$objArr 		= null;

		for ($i = 0; $i < count($leaveId); $i++) {
			if ($leaveStatus[$i] == 0) {
				$updateValues = new LeaveTakenRequests();
				$updateValues->setLeaveId($leaveId[$i]);
				$updateValues->setLeaveStatus($leaveStatus[$i]);
				$updateValues->setLeaveComments($leaveComments[$i]);
				$updateValues->setLeaveYear($leaveYear[$i]);
				$updateValues->setNoHours($noHours[$i]);
				$updateValues->setLeaveTypeId($leaveTypeId[$i]);
				$updateValues->setEmployeeID($employeeId[$i]);
				$objArr[] = $updateValues;
			}
		}

		return $objArr;

	}

}
?>