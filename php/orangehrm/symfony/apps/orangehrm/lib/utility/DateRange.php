<?php
class DateRange {
    private $fromDate;
    private $toDate;

    public function __construct($fromDate = null, $toDate = null) {
        $this->fromDate = $this->_formatDate($fromDate);
        $this->toDate = $this->_formatDate($toDate);
    }

    public function setFromDate($fromDate) {
        $this->fromDate = $this->_formatDate($fromDate);
    }

    public function setToDate($toDate) {
        $this->toDate = $this->_formatDate($toDate);
    }

    public function getFromDate() {
        return $this->fromDate;
    }

    public function getToDate() {
		return $this->toDate;
    }
     
    public function _formatDate($dateString) {
        $pattern = sfContext::getInstance()->getUser()->getDateFormat();
        $localizationService = new LocalizationService();
        $result = $localizationService->convertPHPFormatDateToISOFormatDate($pattern, trim($dateString));        

        return $result;
    }
}