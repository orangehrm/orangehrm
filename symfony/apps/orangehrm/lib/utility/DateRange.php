<?php
class DateRange {
    private $fromDate;
    private $toDate;

    public function __construct($fromDate = null, $toDate = null) {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function setFromDate($fromDate) {
        $this->fromDate = $fromDate;
    }

    public function setToDate($toDate) {
        $this->toDate = $toDate;
    }

    public function getFromDate() {
        return $this->fromDate;
    }

    public function getToDate() {
		return $this->toDate;
    }
        
}