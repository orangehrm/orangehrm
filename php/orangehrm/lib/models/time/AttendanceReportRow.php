<?php

class AttendanceReportRow {
	
	public $reportType;
	public $employeeName;
	public $employeeId;
	public $duration;
	public $inTime;
	public $outTime;
	public $multipleDayPunch;
	public $mutipleDayPunchStartTime;
    
	public function __construct($type) {
		$this->reportType = $type;
	}
	
	public function setMutipleDayPunchStartTime($mutipleDayPunchStartTime) {
        $this->mutipleDayPunchStartTime = $mutipleDayPunchStartTime;
    }

	public function setMultipleDayPunch($multipleDayPunch) {
        $this->multipleDayPunch = $multipleDayPunch;
    }

	public function setOutTime($outTime) {
        $this->outTime = $outTime;
    }

	public function setInTime($inTime) {
        $this->inTime = $inTime;
    }

	public function setDuration($duration) {
        $this->duration = $duration;
    }

	public function setEmployeeId($employeeId) {
        $this->employeeId = $employeeId;
    }

	public function setEmployeeName($employeeName) {
        $this->employeeName = $employeeName;
    }

	public function setReportType($reportType) {
        $this->reportType = $reportType;
    }

	public function getMutipleDayPunchStartTime() {
        return $this->mutipleDayPunchStartTime;
    }

	public function getMultipleDayPunch() {
        return $this->multipleDayPunch;
    }

	public function getOutTime() {
        return $this->outTime;
    }

	public function getInTime() {
        return $this->inTime;
    }

	public function getDuration() {
        return $this->duration;
    }

	public function getEmployeeId() {
        return $this->employeeId;
    }

	public function getEmployeeName() {
        return $this->employeeName;
    }

	public function getReportType() {
        return $this->reportType;
    }

	
	public function getPunchInTime(){
	   if($this->multipleDayPunch) {
            return $this->mutipleDayPunchStartTime;
       } else {
       	    return $this->inTime;
       }
	}	
}

?>