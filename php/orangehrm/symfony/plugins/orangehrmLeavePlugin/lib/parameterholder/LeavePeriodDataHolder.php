<?php

class LeavePeriodDataHolder {

    public $startDate; // 'm-d'
    public $leavePeriodStartDate; // 'Y-m-d'
    public $dateFormat;
    public $currentDate; // 'Y-m-d'
    public $isLeavePeriodStartOnFeb29th; // 'Yes' || 'No'
    public $nonLeapYearLeavePeriodStartDate; // 'm-d'

    public function getStartDate() { return $this->startDate; }
    public function getLeavePeriodStartDate() { return $this->leavePeriodStartDate; }
    public function getDateFormat() { return $this->dateFormat; }
    public function getCurrentDate() { return $this->currentDate; }
    public function getIsLeavePeriodStartOnFeb29th() { return $this->isLeavePeriodStartOnFeb29th; }
    public function getNonLeapYearLeavePeriodStartDate() { return $this->nonLeapYearLeavePeriodStartDate; }
    public function setStartDate($x) { $this->startDate = $x; }
    public function setLeavePeriodStartDate($x) { $this->leavePeriodStartDate = $x; }
    public function setDateFormat($x) { $this->dateFormat = $x; }
    public function setCurrentDate($x) { $this->currentDate = $x; }
    public function setIsLeavePeriodStartOnFeb29th($x) { $this->isLeavePeriodStartOnFeb29th = $x; }
    public function setNonLeapYearLeavePeriodStartDate($x) { $this->nonLeapYearLeavePeriodStartDate = $x; }

}