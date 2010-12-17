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

require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH . '/lib/dao/SQLQBuilder.php';
require_once ROOT_PATH . '/lib/common/UniqueIDGenerator.php';
require_once ROOT_PATH . '/lib/models/time/AttendanceReportRow.php';

class AttendanceRecord {

	const DB_TABLE = 'hs_hr_attendance';
	const DB_FIELD_ATTENDANCE_ID = 'attendance_id';
	const DB_FIELD_EMPLOYEE_ID = 'employee_id';
	const DB_FIELD_PUNCHIN_TIME = 'punchin_time';
	const DB_FIELD_PUNCHOUT_TIME = 'punchout_time';
	const DB_FIELD_IN_NOTE = 'in_note';
	const DB_FIELD_OUT_NOTE = 'out_note';
	const DB_FIELD_STATUS = 'status';
	const DB_FIELD_TIMESTAMP_DIFF = 'timestamp_diff';
	const STATUS_ACTIVE = 1;
	const STATUS_DELETED = 0;

	private $attendanceId;
	private $employeeId;
	private $inDate;
	private $inTime;
	private $outDate;
	private $outTime;
	private $inNote;
	private $outNote;
	private $timestampDiff;
	private $status; // Whether active or deleted (0 or 1)
	private $employeeName;
	private $duration;

	public function setAttendanceId($attendanceId) {
	    $this->attendanceId = $attendanceId;
	}

	public function getAttendanceId() {
	    return $this->attendanceId;
	}

	public function setEmployeeId($employeeId) {
	    $this->employeeId = $employeeId;
	}

	public function getEmployeeId() {
	    return $this->employeeId;
	}

	public function setInDate($inDate) {
	    $this->inDate = $inDate;
	}

	public function getInDate() {
	    return $this->inDate;
	}

	public function setInTime($inTime) {
	    $this->inTime = $inTime;
	}

	public function getInTime() {
	    return $this->inTime;
	}

	public function setOutDate($outDate) {
	    $this->outDate = $outDate;
	}

	public function getOutDate() {
	    return $this->outDate;
	}

	public function setOutTime($outTime) {
	    $this->outTime = $outTime;
	}

	public function getOutTime() {
	    return $this->outTime;
	}

	public function setInNote($inNote) {
	    $this->inNote = $inNote;
	}

	public function getInNote() {
	    return $this->inNote;
	}

	public function setOutNote($outNote) {
	    $this->outNote = $outNote;
	}

	public function getOutNote() {
	    return $this->outNote;
	}

	public function setTimestampDiff($timestampDiff) {
	    $this->timestampDiff = $timestampDiff;
	}

	public function getTimestampDiff() {
	    return $this->timestampDiff;
	}

	public function setStatus($status) {
	    $this->status = $status;
	}

	public function getStatus() {
	    return $this->status;
	}
	public function setEmployeeName($employeeName) {
		$this->employeeName = $employeeName;
	}

	public function getEmployeeName() {
		return $this->employeeName;
	}
	public function setDuration($duration) {
		$this->duration = $duration;
	}

	public function getDuration() {
		return $this->duration;
	}
	
	
	

	/**
	 *
	 */

	public function addRecord() {

		if (!isset($this->employeeId) || !isset($this->inDate) || !isset($this->inTime)) {
			throw new AttendanceRecordException('Adding record: Required values are missing',
												AttendanceRecordException::ADDING_REQUIRED_VALUES_MISSING);
		}

		$insertTable = "`".self::DB_TABLE."`";

		$insertFields[] = "`".self::DB_FIELD_ATTENDANCE_ID."`";
		$insertValues[] = UniqueIDGenerator::getInstance()->getNextID(self::DB_TABLE, self::DB_FIELD_ATTENDANCE_ID);

		$insertFields[] = "`".self::DB_FIELD_EMPLOYEE_ID."`";
		$insertValues[] = "'{$this->employeeId}'";

		$insertFields[] = "`".self::DB_FIELD_PUNCHIN_TIME."`";
		$insertValues[] = "'{$this->inDate} {$this->inTime}'";

		if (isset($this->inNote)) {
			$insertFields[] = "`".self::DB_FIELD_IN_NOTE."`";
			$insertValues[] = "'{$this->inNote}'";
		}

		$insertFields[] = "`".self::DB_FIELD_TIMESTAMP_DIFF."`";
		$insertValues[] = "'{$this->timestampDiff}'";

		$insertFields[] = "`".self::DB_FIELD_STATUS."`";
		$insertValues[] = "'".self::STATUS_ACTIVE."'";

		$sqlBuilder = new SQLQBuilder();
		$query = $sqlBuilder->simpleInsert($insertTable, $insertValues, $insertFields);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($query);

		if ($result) {
		    return true;
		} else {
		    return false;
		}

	}

	/**
	 *
	 */

	public function updateRecord() {

		if (!isset($this->attendanceId)) {
			throw new AttendanceRecordException('Attendance ID is not set',
												AttendanceRecordException::ERROR_ID_NOT_SET);
		}

		$updateTable = "`".self::DB_TABLE."`";

		if (isset($this->inDate) && isset($this->inTime)) {
		    $updateFields[] = "`".self::DB_FIELD_PUNCHIN_TIME."`";
		    $updateValues[] = "{$this->inDate} {$this->inTime}";
		}

		if (isset($this->outDate) && isset($this->outTime)) {
		    $updateFields[] = "`".self::DB_FIELD_PUNCHOUT_TIME."`";
		    $updateValues[] = "{$this->outDate} {$this->outTime}";
		}

		if (isset($this->inNote)) {
		    $updateFields[] = "`".self::DB_FIELD_IN_NOTE."`";
		    $updateValues[] = "'".$this->inNote."'";
		}

		if (isset($this->outNote)) {
		    $updateFields[] = "`".self::DB_FIELD_OUT_NOTE."`";
		    $updateValues[] = "'".$this->outNote."'";
		}

		if (isset($this->status)) {
		    $updateFields[] = "`".self::DB_FIELD_STATUS."`";
		    $updateValues[] = "'".$this->status."'";
		}

		$updateConditions[] = "`".self::DB_FIELD_ATTENDANCE_ID."` = {$this->attendanceId}";

		$sqlBuilder = new SQLQBuilder();
		$query = $sqlBuilder->simpleUpdate($updateTable, $updateFields, $updateValues, $updateConditions);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($query);

		if ($result) {
		    return true;
		} else {
		    return false;
		}

	}

	/**
	 *
	 */

	public function fetchRecords($employeeId, $from=null, $to=null, $status=null, $orderBy=null, $order=null, $limit=null, $punch=false,  $subordinateIds = null) {

		$result = self::_fetchResult($employeeId, $from, $to, $status, $orderBy, $order, $limit, $punch , false, $subordinateIds);

		if (mysql_num_rows($result) > 0) {
			return $this->_buildRecordObjects($result);
		} else {
			return null;
		}

	}
	
	public function populateDataRangeArrayForSummary($from=null, $to=null, $reportData = null){
				
		$i = strtotime($from);

		$tempDateArray = array();

		if(LocaleUtil::getInstance()->formatDate($from) != null && LocaleUtil::getInstance()->formatDate($to) != null) {
			
			while($i<= strtotime($to)) {
				
				$tempDateArray [date("Y-m-d", $i)] = null;
							
				if(is_array($reportData)) {
					foreach($reportData as $reportRow) {
						if($reportRow->inTime == date("Y-m-d", $i)){
							$tempDateArray[date("Y-m-d", $i)] = $reportRow;
						}
					}
				}
				if(! ($tempDateArray[date("Y-m-d", $i)] instanceof AttendanceReportRow)) {
					$tempObject = new AttendanceReportRow('summary');
					$tempObject->inTime = date("Y-m-d", $i);
					$tempObject->duration = '0.00';
					$tempDateArray[date("Y-m-d", $i)] = $tempObject;				
				} 
				$i = strtotime("+1 day",$i);
			}
			return $tempDateArray;
		} else {
			return $reportData;
		}
	}
	
	public function fetchSummary($employeeId, $from=null, $to=null, $status=null, $orderBy=null, $order=null, $limit=null, $punch=false, $subordinateIds = null) {

		$result = self::_fetchResult($employeeId, $from, $to, $status, $orderBy, $order, $limit, $punch, false , $subordinateIds);

		$rows = array();
    	while ($row = mysql_fetch_array($result)) {
                    $row['punchin_time'] = date('Y-m-d H:i', strtotime($row['punchin_time'])+$row['timestamp_diff']);
                    
                    if ($row['punchout_time'] != null) {                        
                             
                        $row['punchout_time'] = date('Y-m-d H:i', strtotime($row['punchout_time'])+$row['timestamp_diff']);   
                    $rows [] =  $row;
} 
            
    	}

		if (mysql_num_rows($result) == 0) {
			return null;
		}

		$reportRows = array ();

		foreach ($rows as $row) {	

        if($row['duration']/60/60  > 24 || (date("Y-m-d",strtotime($row[self::DB_FIELD_PUNCHIN_TIME])) != date("Y-m-d",strtotime($row[self::DB_FIELD_PUNCHOUT_TIME])))) {
				
				$durationArray  = array();
				
				$startTimeToStamp = strtotime(date("Y-m-d H:i:s",strtotime($row[self::DB_FIELD_PUNCHIN_TIME])));
                $endimeToStamp = strtotime(date("Y-m-d H:i:s",strtotime($row[self::DB_FIELD_PUNCHOUT_TIME])));  
				
                $totalDuration = $endimeToStamp - $startTimeToStamp;
                
				$nextDatOfPunchInDate = strtotime(date("Y-m-d",strtotime($row[self::DB_FIELD_PUNCHIN_TIME]))."next day");
				$previousDateOfPunchOutDate = strtotime(date("Y-m-d",strtotime($row[self::DB_FIELD_PUNCHOUT_TIME])));				
				
				$startDateDuration = $nextDatOfPunchInDate - $startTimeToStamp;
				$durationArray [date(LocaleUtil::STANDARD_DATE_FORMAT, strtotime($row[self::DB_FIELD_PUNCHIN_TIME]))] = $startDateDuration/3600;
				$endDateDuration = $endimeToStamp - $previousDateOfPunchOutDate;								
				
				$remainingDuration = $totalDuration - ($startDateDuration + $endDateDuration);
				
				$max = ($remainingDuration / 3600 / 24 );
				$next = strtotime(date(LocaleUtil::STANDARD_DATE_FORMAT, strtotime($row[self::DB_FIELD_PUNCHIN_TIME]))."next day");
				
				for($i=0 ; $i < $max; $i++) {					
					$durationArray [date("Y-m-d",$next)] = 24;
					$next = strtotime(date("Y-m-d",$next)."next day");
				}
				
				$durationArray [date(LocaleUtil::STANDARD_DATE_FORMAT, strtotime($row[self::DB_FIELD_PUNCHOUT_TIME]))] = $endDateDuration/3600;
				
				foreach($durationArray as $date => $duration) {
					$object = new AttendanceReportRow('summary');
					$object->employeeId = $row[self::DB_FIELD_EMPLOYEE_ID];
                    $object->employeeName = $row[EmpInfo::EMPLOYEE_FIELD_FIRST_NAME]." ".$row[EmpInfo::EMPLOYEE_FIELD_LAST_NAME];
                    $object->inTime = $date;
              
                    $duration = (intval ($duration)).".".str_pad(round(((strstr( $duration, '.' ))*60)), 2, "0", STR_PAD_LEFT);
                    $object->duration = $duration;
                    
                    $object->multipleDayPunch = true;
                    $object->mutipleDayPunchStartTime = date(LocaleUtil::STANDARD_DATE_FORMAT, strtotime($row[self::DB_FIELD_PUNCHIN_TIME]));
                    $reportRows [] = $object;   
				}
					
			} else {
			    
			    $startTimeToStamp = strtotime(date("Y-m-d H:i:s",strtotime($row[self::DB_FIELD_PUNCHIN_TIME])));
                $endimeToStamp = strtotime(date("Y-m-d H:i:s",strtotime($row[self::DB_FIELD_PUNCHOUT_TIME])));  
                
                 $row['duration'] = $endimeToStamp - $startTimeToStamp;
				$duration = number_format(((intval($row['duration']/60/60)) + (($row['duration']/60/60) - intval($row['duration']/60/60))/100*60),2);

				$minutesPart =  str_replace(".","",strstr( $duration, '.' ));				
				$object = new AttendanceReportRow('summary');            
                $object->employeeId = $row[self::DB_FIELD_EMPLOYEE_ID];     
				$object->duration = $duration;
                $object->inTime = date(LocaleUtil::STANDARD_DATE_FORMAT,strtotime($row[self::DB_FIELD_PUNCHIN_TIME]));
                $object->outTime = date(LocaleUtil::STANDARD_DATE_FORMAT, strtotime($row[self::DB_FIELD_PUNCHOUT_TIME]));
                $object->employeeName = $row[EmpInfo::EMPLOYEE_FIELD_FIRST_NAME]." ".$row[EmpInfo::EMPLOYEE_FIELD_LAST_NAME];    
            
                $reportRows [] = $object; 				
			}
		}

		foreach ($reportRows as $key=>$reportRow){
    		foreach ($reportRows as $key1=>$reportRow1){
                if( ($reportRow1->getInTime() == $reportRow->getInTime()) && ($reportRow1->getEmployeeId() == $reportRow->getEmployeeId()) && ($key != $key1) ){                    
                    if(isset($reportRows [$key])){                        
                    $reportRows [$key]->setDuration(number_format(LocaleUtil::getTheCorrectTimeTotalValueFromDecimal($reportRow1->getDuration(), $reportRow->getDuration()),2));                    
                        unset($reportRows[$key1]);
                     }
                }
            }
		}

	   return $reportRows;
	}
	
	private function _fetchResult($employeeId, $from=null, $to=null, $status=null, $orderBy=null, $order=null, $limit=null, $punch=false, $forSummary = false, $subordinateIds = null) {
		
		$selectTable = "`".self::DB_TABLE."`";

		$selectFields[] = "a.`".self::DB_FIELD_ATTENDANCE_ID."`";
		$selectFields[] = "a.`".self::DB_FIELD_EMPLOYEE_ID."`";
		$selectFields[] = "a.`".self::DB_FIELD_PUNCHIN_TIME."`";
		$selectFields[] = "a.`".self::DB_FIELD_PUNCHOUT_TIME."`";
		$selectFields[] = "a.`".self::DB_FIELD_IN_NOTE."`";
		$selectFields[] = "a.`".self::DB_FIELD_OUT_NOTE."`";
		$selectFields[] = "a.`".self::DB_FIELD_TIMESTAMP_DIFF."`";
		$selectFields[] = "a.`".self::DB_FIELD_STATUS."`";
		$selectFields[] = "e.`".EmpInfo::EMPLOYEE_FIELD_FIRST_NAME."`";
		$selectFields[] = "e.`".EmpInfo::EMPLOYEE_FIELD_LAST_NAME."`";
		
		$tables [0] = "`".self::DB_TABLE."` a";
		$tables [1] = EmpInfo::EMPLOYEE_TABLE_NAME." e";
		
		$joinConditions [1] = "a.".self::DB_FIELD_EMPLOYEE_ID."= e.".EmpInfo::EMPLOYEE_FIELD_EMP_NUMBER; 

		if($employeeId > 0 ) {
		    $selectConditions[] = "a.`".self::DB_FIELD_EMPLOYEE_ID."` = '$employeeId'";
		}

		if ($from != null) {
			$selectConditions[] = "a.`".self::DB_FIELD_PUNCHIN_TIME."` >= '$from'";
		}

		if ($to != null) {
			$selectConditions[] = "a.`".self::DB_FIELD_PUNCHIN_TIME."` <= '$to'"; // PUNCHIN is used since it is allowed PUNCHOUT to be out of upper limit
		}
		
		if ($punch) {
			$selectConditions[] = "a.`".self::DB_FIELD_PUNCHOUT_TIME."` IS NULL";
		} else {
			$selectConditions[] = "a.`".self::DB_FIELD_PUNCHOUT_TIME."` IS NOT NULL";
		}

		if ($status != null) {
			$selectConditions[] = "a.`".self::DB_FIELD_STATUS."` = '$status'";
		}
		if($subordinateIds != null) {
			$selectConditions[] = "a.".self::DB_FIELD_EMPLOYEE_ID." IN (".implode(",",$subordinateIds).")";
		}

		$sqlBuilder = new SQLQBuilder();
		
		$groupBy = null;
		if($forSummary) {

			$groupBy  = self::DB_FIELD_EMPLOYEE_ID.",  DATE_FORMAT(".self::DB_FIELD_PUNCHIN_TIME.",'%Y %c %d'), DATE_FORMAT(".self::DB_FIELD_PUNCHOUT_TIME.",'%Y %c %d') " ;
			$selectFields[] = " (SUM(TIME_TO_SEC(".self::DB_FIELD_PUNCHOUT_TIME.") - TIME_TO_SEC(".self::DB_FIELD_PUNCHIN_TIME."))) AS duration";
		} else  {
			$selectFields[] = " TIMEDIFF(".self::DB_FIELD_PUNCHOUT_TIME.",".self::DB_FIELD_PUNCHIN_TIME.") AS duration";
		}
		
		$query = $sqlBuilder->selectFromMultipleTable($selectFields, $tables, $joinConditions, $selectConditions, null, $orderBy, $order, $limit, $groupBy);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($query);
		
		return $result;
		
	}
	
	public function countRecords($employeeId, $from=null, $to=null, $status=null, $punch=false) {
		
		$selectTable = "`".self::DB_TABLE."`";

		$selectFields[] = "COUNT(".self::DB_FIELD_ATTENDANCE_ID.")";

		$selectConditions[] = "`".self::DB_FIELD_EMPLOYEE_ID."` = '$employeeId'";

		if ($from != null) {
			$selectConditions[] = "`".self::DB_FIELD_PUNCHIN_TIME."` >= '$from'";
		}

		if ($to != null) {
			$selectConditions[] = "`".self::DB_FIELD_PUNCHIN_TIME."` <= '$to'"; // PUNCHIN is used since it is allowed PUNCHOUT to be out of upper limit
		}
		
		if ($punch) {
			$selectConditions[] = "`".self::DB_FIELD_PUNCHOUT_TIME."` IS NULL";
		} else {
			$selectConditions[] = "`".self::DB_FIELD_PUNCHOUT_TIME."` IS NOT NULL";
		}

		if ($status != null) {
			$selectConditions[] = "`".self::DB_FIELD_STATUS."` = '$status'";
		}

		$sqlBuilder = new SQLQBuilder();
		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($query);
		$row = $dbConnection->dbObject->getArray($result);
		
		return $row[0];	    
	}

	private function _buildRecordObjects($result, $adjustTime = true) {

		while ($row = mysql_fetch_array($result)) {

			$attendanceObj = new AttendanceRecord();

			$attendanceObj->setAttendanceId($row['attendance_id']);
			$attendanceObj->setEmployeeId($row['employee_id']);

			/* $row['punchin_time'] comes in '0000-00-00 00:00:00' format.
			 * We want date in '0000-00-00' format and time in '00:00' format.
			 */
			$tmpArr = explode(' ', $row['punchin_time']);
			$attendanceObj->setInDate($tmpArr[0]);
			$attendanceObj->setInTime(substr($tmpArr[1], 0, 5));

			if ($row['punchout_time'] != null) {
				$tmpArr = explode(' ', $row['punchout_time']);
				$attendanceObj->setOutDate($tmpArr[0]);
				$attendanceObj->setOutTime(substr($tmpArr[1], 0, 5)); // Omiting 'seconds' part is ok since it is always zero
			}

			if ($row['in_note'] != null) {
				$attendanceObj->setInNote($row['in_note']);
			}

			if ($row['out_note'] != null) {
				$attendanceObj->setOutNote($row['out_note']);
			}
			
			if($row[EmpInfo::EMPLOYEE_FIELD_FIRST_NAME] && $row[EmpInfo::EMPLOYEE_FIELD_LAST_NAME]) {
				$attendanceObj->setEmployeeName($row[EmpInfo::EMPLOYEE_FIELD_FIRST_NAME]." ".$row[EmpInfo::EMPLOYEE_FIELD_LAST_NAME]);
			}

			$attendanceObj->setTimestampDiff($row['timestamp_diff']);
			$attendanceObj->setStatus($row['status']);			
			
			$durationAsArray = explode(":",$row['duration']);
			$dur = isset($durationAsArray[1])?$durationAsArray[1]:'00';
            $attendanceObj->setDuration($durationAsArray[0].":".$dur);
			
			/* Adjusting time according to the timezone of the place 
			 * where the record was first entered.
			 */
			
			if ($adjustTime) {
				
				/* When saving in the database, timestampDiff is calculated by substracting
				 * server timezone offset from client timezone offset. When showing records
				 * to user this timestampDiff should be added to each date and time shown.
				 */
				
				$value = $attendanceObj->getInDate().' '.$attendanceObj->getInTime();			    
			    $date = date('Y-m-d', strtotime($value)+$row['timestamp_diff']);
			    $time = date('H:i', strtotime($value)+$row['timestamp_diff']);
			    
			    $attendanceObj->setInDate($date);
			    $attendanceObj->setInTime($time);
			    
			    if ($row['punchout_time'] != null) {
			        
					$value = $attendanceObj->getOutDate().' '.$attendanceObj->getOutTime();			    
				    $date = date('Y-m-d', strtotime($value)+$row['timestamp_diff']);
				    $time = date('H:i', strtotime($value)+$row['timestamp_diff']);
				    
				    $attendanceObj->setOutDate($date);
				    $attendanceObj->setOutTime($time);			        
			    }			    
			}
			$attendanceArr[] = $attendanceObj;
		}
		return $attendanceArr;
	}
	
	/**
	 * Used in editing of Attendance Reports
	 */

	public function isOverlapping() {
		
		if (!isset($this->attendanceId) || !isset($this->employeeId) || 
			!isset($this->inDate) || !isset($this->inTime) ||
			!isset($this->outDate) || !isset($this->outTime)) {
				
			throw new AttendanceRecordException('Required values for checking overlapping are not set',
												AttendanceRecordException::OVERLAPPING_REQUIRED_VALUES_MISSING);		
		}
		
		$selectTable = "`".self::DB_TABLE."`";
		$selectFields[] = "`".self::DB_FIELD_ATTENDANCE_ID."`";
		
		$selectConditions[] = "`".self::DB_FIELD_ATTENDANCE_ID."` != '{$this->attendanceId}'";
		$selectConditions[] = "`".self::DB_FIELD_EMPLOYEE_ID."` = '{$this->employeeId}'";
		$selectConditions[] = "`".self::DB_FIELD_STATUS."` = '".self::STATUS_ACTIVE."'";
		
		$in = $this->inDate.' '.$this->inTime.':00';
		$out = $this->outDate.' '.$this->outTime.':00';
		
		$condition = "((`".self::DB_FIELD_PUNCHIN_TIME."` >= '$in' AND `".self::DB_FIELD_PUNCHIN_TIME."` <= '$out')";
		$condition .= " OR (`".self::DB_FIELD_PUNCHOUT_TIME."` >= '$in' AND `".self::DB_FIELD_PUNCHOUT_TIME."` <= '$out')";
		$condition .= " OR (`".self::DB_FIELD_PUNCHIN_TIME."` <= '$in' AND `".self::DB_FIELD_PUNCHOUT_TIME."` >= '$out')";
		$condition .= " OR (`".self::DB_FIELD_PUNCHIN_TIME."` > '$in' AND `".self::DB_FIELD_PUNCHOUT_TIME."` < '$out'))";
		
		$selectConditions[] = $condition;
		
		$sqlBuilder = new SQLQBuilder();
		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($query);
		
		if (mysql_num_rows($result) > 0) {
			throw new AttendanceRecordException('Overlapping record',
												AttendanceRecordException::OVERLAPPING_RECORD);
		} else {
			return false;
		}		
	}
	
	/**
	 * 
	 */

	public function isOverlappingInTime() {
		
		if (!isset($this->employeeId) || !isset($this->inDate) || !isset($this->inTime)) {
				
			throw new AttendanceRecordException('Required values for checking overlapping are not set',
												AttendanceRecordException::OVERLAPPING_REQUIRED_VALUES_MISSING);		
		}
		
		$selectTable = "`".self::DB_TABLE."`";
		$selectFields[] = "`".self::DB_FIELD_ATTENDANCE_ID."`";
		
		$selectConditions[] = "`".self::DB_FIELD_EMPLOYEE_ID."` = '{$this->employeeId}'";
		$selectConditions[] = "`".self::DB_FIELD_STATUS."` = '".self::STATUS_ACTIVE."'";
		
		$in = $this->inDate.' '.$this->inTime.':00';
		$selectConditions[] = "(`".self::DB_FIELD_PUNCHIN_TIME."` <= '$in' AND `".self::DB_FIELD_PUNCHOUT_TIME."` >= '$in')";
		
		$sqlBuilder = new SQLQBuilder();
		$query = $sqlBuilder->simpleSelect($selectTable, $selectFields, $selectConditions);

		$dbConnection = new DMLFunctions();
		$result = $dbConnection->executeQuery($query);
		
		if (mysql_num_rows($result) > 0) {
			throw new AttendanceRecordException('Overlapping record',
												AttendanceRecordException::OVERLAPPING_RECORD);
		} else {
			return false;
		}
		
	}
	
	/**
	 * 
	 */
	
}

class AttendanceRecordException extends Exception {

	const ERROR_ID_NO_SET = 1;
	const ADDING_REQUIRED_VALUES_MISSING = 2;
	const OVERLAPPING_REQUIRED_VALUES_MISSING = 3;
	const OVERLAPPING_RECORD = 4;

}
