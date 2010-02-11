<?php

class AttendanceReportRow {
	
	public $reportType;
	public $employeeName;
	public $employeeId;
	public $duration;
	public $inTime;
	public $outTime;
    
	public function __construct($type) {
		$this->reportType = $type;
	}	
	
}

?>