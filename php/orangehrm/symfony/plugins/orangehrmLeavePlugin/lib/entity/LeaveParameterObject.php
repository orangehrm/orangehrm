<?php

class LeaveParameterObject {

    protected $employeeNumber;
    protected $fromDate;
    protected $toDate;
    protected $fromTime = '';
    protected $toTime = '';
    protected $leaveType;
    protected $leaveTotalTime;
    protected $comment;
    protected $workShiftLength;

    public function __construct(array $formParameters) {
        $this->employeeNumber = $formParameters['txtEmpID']; // TODO: Make this employee number
        $this->fromDate = $formParameters['txtFromDate'];
        $this->toDate = $formParameters['txtToDate'];
        $this->fromTime = $formParameters['txtFromTime'];
        $this->toTime = $formParameters['txtToTime'];
        $this->leaveType = $formParameters['txtLeaveType'];
        $this->leaveTotalTime = $formParameters['txtLeaveTotalTime'];
        $this->comment = $formParameters['txtComment'];
        $this->workShiftLength = $formParameters['txtEmpWorkShift'];
    }

    public function getEmployeeNumber() {
        return $this->employeeNumber;
    }

    public function setEmployeeNumber($employeeNumber) {
        $this->employeeNumber = $employeeNumber;
    }

    public function getFromDate() {
        return $this->fromDate;
    }

    public function setFromDate($fromDate) {
        $this->fromDate = $fromDate;
    }

    public function getToDate() {
        return $this->toDate;
    }

    public function setToDate($toDate) {
        $this->toDate = $toDate;
    }

    public function getFromTime() {
        return $this->fromTime;
    }

    public function setFromTime($fromTime) {
        $this->fromTime = $fromTime;
    }

    public function getToTime() {
        return $this->toTime;
    }

    public function setToTime($toTime) {
        $this->toTime = $toTime;
    }

    public function getLeaveType() {
        return $this->leaveType;
    }

    public function setLeaveType($leaveType) {
        $this->leaveType = $leaveType;
    }

    public function getLeaveTotalTime() {
        return $this->leaveTotalTime;
    }

    public function setLeaveTotalTime($leaveTotalTime) {
        $this->leaveTotalTime = $leaveTotalTime;
    }

    public function getComment() {
        return $this->comment;
    }

    public function setComment($comment) {
        $this->comment = $comment;
    }

    public function getWorkShiftLength() {
        return $this->workShiftLength;
    }

    public function setWorkShiftLength($workShiftLength) {
        $this->workShiftLength = $workShiftLength;
    }

}
