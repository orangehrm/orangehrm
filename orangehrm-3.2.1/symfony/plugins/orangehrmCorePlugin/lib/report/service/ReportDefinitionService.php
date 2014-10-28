<?php

class ReportDefinitionService {

    private $reportDefinitionDao;

    /**
     * Gets ReportDefinitionDao object.
     * @return ReportDefinitionDao
     */
    public function getReportDefinitionDao() {
        if (!($this->reportDefinitionDao instanceof ReportDefinitionDao)) {
            $this->reportDefinitionDao = new ReportDefinitionDao();
        }

        return $this->reportDefinitionDao;
    }

    /**
     * Sets ReportDefinitionDao object.
     * @param ReportDefinitionDao $reportDefinitionDao
     */
    public function setReportDefinitionDao($reportDefinitionDao) {
        $this->reportDefinitionDao = $reportDefinitionDao;
    }

    /**
     * Gets report for a given report id.
     * @param int $reportId
     * @return AdvanceReport
     */
    public function getReport($reportId){
        $reportId = 1;
        $dao = $this->getReportDefinitionDao();
        $report = $dao->getReport($reportId);
        return $report;
    }
    
    /**
     * Gets report name for a given report id.
     * @param int $reportId
     * @return string
     */
    public function getReportName($reportId) {        
        $reportId = 1;
        $report = $this->getReport($reportId);
        $reportName = $report->getName();
        return $reportName;
    }    
    
    /**
     * Gets all the defined reports with their ids.
     * @return array
     */
    public function getAllReportNamesWithIds(){
        $dao = $this->getReportDefinitionDao();
        $allReports = $dao->getAllReportNamesWithIds();
        return $allReports;
    }
}

