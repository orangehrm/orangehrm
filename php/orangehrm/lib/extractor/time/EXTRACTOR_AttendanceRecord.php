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

require_once ROOT_PATH . '/lib/models/time/AttendanceRecord.php';

class EXTRACTOR_AttendanceRecord {

	private $userTimeZoneOffset;
	private $serverTimeZoneOffset;

	public function setUserTimeZoneOffset($userTimeZoneOffset) {
	    $this->userTimeZoneOffset = $userTimeZoneOffset;
	}

	public function getUserTimeZoneOffset() {
	    return $this->userTimeZoneOffset;
	}

	public function setServerTimeZoneOffset($serverTimeZoneOffset) {
	    $this->serverTimeZoneOffset = $serverTimeZoneOffset;
	}

	public function getServerTimeZoneOffset() {
	    return $this->serverTimeZoneOffset;
	}

	public function __construct($userTimeZoneOffset=null, $serverTimeZoneOffset=null) {
	    $this->userTimeZoneOffset = $userTimeZoneOffset;
	    $this->serverTimeZoneOffset = $serverTimeZoneOffset;
	}

	public function parsePunchData($postArr) {

		$attendanceObj = new AttendanceRecord();

		if (isset($postArr['hdnAttendanceId'])) {
			$attendanceObj->setAttendanceId($postArr['hdnAttendanceId']);
		}

		$attendanceObj->setEmployeeId($postArr['hdnEmployeeId']);

		if (isset($postArr['txtInDate']) && isset($postArr['txtInTime'])) {

			$value = trim($postArr['txtInDate']).' '.trim($postArr['txtInTime']);

			$attendanceObj->setInDate($this->adjustToServerTime('date', 'subtract', $value));
			$attendanceObj->setInTime($this->adjustToServerTime('time', 'subtract', $value));

		}

		if (isset($postArr['txtInNote'])) {
			$attendanceObj->setInNote($postArr['txtInNote']);
		}

		if (isset($postArr['txtOutDate']) && isset($postArr['txtOutTime'])) {

			$value = trim($postArr['txtOutDate']).' '.trim($postArr['txtOutTime']);

			$attendanceObj->setOutDate($this->adjustToServerTime('date', 'subtract', $value));
			$attendanceObj->setOutTime($this->adjustToServerTime('time', 'subtract', $value));

		}

		if (isset($postArr['txtOutNote'])) {
			$attendanceObj->setOutNote($postArr['txtOutNote']);
		}

		return $attendanceObj;

	}

	public function parseReportData($postArr) {

		$parsedObjs = array();

		for ($i=0; $i<$postArr['recordsCount']; $i++)	{

			$attendanceRecordObj = new AttendanceRecord();
			$changed = false;

			//TODO: If one condition gets true, stop checking other conditions

			if (trim($postArr['txtNewInDate-'.$i]) != $postArr['hdnOldInDate-'.$i]) {
				$changed = true;
			}

			if (trim($postArr['txtNewInTime-'.$i]) != $postArr['hdnOldInTime-'.$i]) {
				$changed = true;
			}

			if (trim($postArr['txtNewInNote-'.$i]) != $postArr['hdnOldInNote-'.$i]) {
				$changed = true;
			}

			if (trim($postArr['txtNewOutDate-'.$i]) != $postArr['hdnOldOutDate-'.$i]) {
				$changed = true;
			}

			if (trim($postArr['txtNewOutTime-'.$i]) != $postArr['hdnOldOutTime-'.$i]) {
				$changed = true;
			}

			if (trim($postArr['txtNewOutNote-'.$i]) != $postArr['hdnOldOutNote-'.$i]) {
				$changed = true;
			}

			if (isset($postArr['chkDeleteStatus-'.$i])) {
				$attendanceRecordObj->setStatus(AttendanceRecord::STATUS_DELETED);
				$changed = true;
			}

			if ($changed) {
				/* Even if only one value is changed, setting other properties
				 * is required to carry out functions like checking overlapping
				 */
				$attendanceRecordObj->setAttendanceId($postArr['hdnAttendanceId-'.$i]);
				$attendanceRecordObj->setEmployeeId($postArr['hdnEmployeeId']);
				$value = trim($postArr['txtNewInDate-'.$i]).' '.trim($postArr['txtNewInTime-'.$i]);
				$attendanceRecordObj->setInDate($this->adjustToServerTime('date', 'subtract', $value));
				$attendanceRecordObj->setInTime($this->adjustToServerTime('time', 'subtract', $value));
				$attendanceRecordObj->setInNote(trim($postArr['txtNewInNote-'.$i]));
				$value = trim($postArr['txtNewOutDate-'.$i]).' '.trim($postArr['txtNewOutTime-'.$i]);
				$attendanceRecordObj->setOutDate($this->adjustToServerTime('date', 'subtract', $value));
				$attendanceRecordObj->setOutTime($this->adjustToServerTime('time', 'subtract', $value));
				$attendanceRecordObj->setOutNote(trim($postArr['txtNewOutNote-'.$i]));
			    $parsedObjs[] = $attendanceRecordObj;
			}

		}

		return $parsedObjs;

	}

	public function adjustToServerTime($type, $operation, $value) {

	    if (!isset($this->userTimeZoneOffset)) {
	        throw new Exception('User time zone is not set');
	    }

	    if (!isset($this->serverTimeZoneOffset)) {
	        throw new Exception('Server time zone is not set');
	    }

	    if ($type != 'date' && $type != 'time') {
	        throw new Exception('Wrong type');
	    }

	    if ($operation != 'add' && $operation != 'subtract') {
	        throw new Exception('Wrong operation');
	    }

    	$hourDiff = $this->userTimeZoneOffset - $this->serverTimeZoneOffset;
		$timeStampDiff = $hourDiff*3600;

		if ($type == 'date') {

			if ($operation == 'add') {
			    return date('Y-m-d', strtotime($value)+$timeStampDiff);
			} elseif ($operation == 'subtract') {
			    return date('Y-m-d', strtotime($value)-$timeStampDiff);
			}

 		} elseif ($type == 'time') {

			if ($operation == 'add') {
			    return date('H:i', strtotime($value)+$timeStampDiff);
			} elseif ($operation == 'subtract') {
			    return date('H:i', strtotime($value)-$timeStampDiff);
			}

		}

	}

}

?>
