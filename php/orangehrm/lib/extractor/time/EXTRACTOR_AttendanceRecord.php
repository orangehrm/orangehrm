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


		if (isset($postArr['hdnTimestampDiff'])) {
			$timestampDiffToPass = $postArr['hdnTimestampDiff']; // Means the corresponding timestampDiff has been already saved in the database.
		} else {
			$timestampDiffToPass = $this->_getTimestampDiff(); // Means a new record. Need to calculate timestampDiff
		}

		if (isset($postArr['hdnAttendanceId'])) {
			$attendanceObj->setAttendanceId($postArr['hdnAttendanceId']);
		}

		$attendanceObj->setEmployeeId($postArr['hdnEmployeeId']);

		if (isset($postArr['txtInDate']) && isset($postArr['txtInTime'])) {

			$value = trim($postArr['txtInDate']).' '.trim($postArr['txtInTime']);

			$attendanceObj->setInDate($this->_adjustToServerTime('date', $timestampDiffToPass, $value));
			$attendanceObj->setInTime($this->_adjustToServerTime('time', $timestampDiffToPass, $value));

		}

		if (isset($postArr['txtInNote'])) {
			$attendanceObj->setInNote($postArr['txtInNote']);
		}

		if (isset($postArr['txtOutDate']) && isset($postArr['txtOutTime'])) {

			$value = trim($postArr['txtOutDate']).' '.trim($postArr['txtOutTime']);

			$attendanceObj->setOutDate($this->_adjustToServerTime('date', $timestampDiffToPass, $value));
			$attendanceObj->setOutTime($this->_adjustToServerTime('time', $timestampDiffToPass, $value));

		}

		if (!isset($postArr['hdnAttendanceId'])) {
			$attendanceObj->setTimestampDiff($this->_getTimestampDiff());
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

			if (trim($postArr['txtNewInDate-'.$i]) != $postArr['hdnOldInDate-'.$i]) {
				$changed = true;
			} elseif (trim($postArr['txtNewInTime-'.$i]) != $postArr['hdnOldInTime-'.$i]) {
				$changed = true;
			} elseif (trim($postArr['txtNewInNote-'.$i]) != $postArr['hdnOldInNote-'.$i]) {
				$changed = true;
			} elseif (trim($postArr['txtNewOutDate-'.$i]) != $postArr['hdnOldOutDate-'.$i]) {
				$changed = true;
			} elseif (trim($postArr['txtNewOutTime-'.$i]) != $postArr['hdnOldOutTime-'.$i]) {
				$changed = true;
			} elseif (trim($postArr['txtNewOutNote-'.$i]) != $postArr['hdnOldOutNote-'.$i]) {
				$changed = true;
			} elseif (isset($postArr['chkDeleteStatus-'.$i])) {
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
				$attendanceRecordObj->setInDate($this->_adjustToServerTime('date', $postArr['hdnTimestampDiff-'.$i], $value));
				$attendanceRecordObj->setInTime($this->_adjustToServerTime('time', $postArr['hdnTimestampDiff-'.$i], $value));
				$attendanceRecordObj->setInNote(trim($postArr['txtNewInNote-'.$i]));
				$value = trim($postArr['txtNewOutDate-'.$i]).' '.trim($postArr['txtNewOutTime-'.$i]);
				$attendanceRecordObj->setOutDate($this->_adjustToServerTime('date', $postArr['hdnTimestampDiff-'.$i], $value));
				$attendanceRecordObj->setOutTime($this->_adjustToServerTime('time', $postArr['hdnTimestampDiff-'.$i], $value));
				$attendanceRecordObj->setOutNote(trim($postArr['txtNewOutNote-'.$i]));
			    $parsedObjs[] = $attendanceRecordObj;
			}

		}

		return $parsedObjs;

	}

	private function _adjustToServerTime($type, $timestampDiff, $value) {

	    if ($type != 'date' && $type != 'time') {
	        throw new Exception('Wrong type');
	    }

		if ($type == 'date') {
			return date('Y-m-d', strtotime($value)-$timestampDiff);
 		} elseif ($type == 'time') {
			return date('H:i', strtotime($value)-$timestampDiff);
		}

	}
	
	/* When saving in the database, timestampDiff is calculated by substracting
	 * server timezone offset from client timezone offset. When showing records
	 * to user this timestampDiff should be added to each date and time shown.
	 */

	private function _getTimestampDiff() {

		return ($this->userTimeZoneOffset - $this->serverTimeZoneOffset)*3600;

	}

}

?>
