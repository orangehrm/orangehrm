<?php
/**
 * 2011-06-23
 */



class ViewCandidates extends Page {

    /**
     *
     * @var BasicList $list
     */
    public $cmbJobTitle = "candidateSearch_jobTitle";
    public $cmbVacancy = "candidateSearch_jobVacancy";
    public $cmbHiringManager = "candidateSearch_hiringManager";
    public $cmbCandidateName = "candidateSearch_candidateName";
    public $txtKeywords = "candidateSearch_keywords";
    public $cmbStatus = "candidateSearch_status";
    public $cmbMethodofApp = "candidateSearch_modeOfApplication";
    public $txtFromDate = "candidateSearch_fromDate";
    public $txtToDate = "candidateSearch_toDate";
    public $btnSearch = "btnSrch";
    public $pageUrl ;
    public $list ;

    public function __construct($selenium) {

        parent::__construct($selenium);
       $this->pageUrl = Config::$loginURL . "/symfony/web/index.php/recruitment/viewCandidates";
       $this->list = new BasicList($selenium, "//div[@id='candidatesSrchResults']", true);
    }

    public function search($searchCriteriaArray) {
        
        $this->selenium->selectFrame("relative=top");
        if ($searchCriteriaArray[$this->cmbJobTitle])
                $this->selenium->select($this->cmbJobTitle, "label=". $searchCriteriaArray[$this->cmbJobTitle]);
                $this->selenium->select($this->cmbVacancy, "label=". $searchCriteriaArray[$this->cmbVacancy]);
                $this->selenium->type($this->cmbCandidateName, $searchCriteriaArray[$this->cmbCandidateName]);
                $this->selenium->select($this->cmbHiringManager, "label=". $searchCriteriaArray[$this->cmbHiringManager]);
                $this->selenium->type($this->txtKeywords, $searchCriteriaArray[$this->txtKeywords]);
                $this->selenium->select($this->cmbStatus, "label=". $searchCriteriaArray[$this->cmbStatus]);
                $this->selenium->select($this->cmbMethodofApp, "label=". $searchCriteriaArray[$this->cmbMethodofApp]);
                $this->selenium->type($this->txtFromDate, $searchCriteriaArray[$this->txtFromDate]);
                $this->selenium->type($this->txtToDate, $searchCriteriaArray[$this->txtToDate]);
                
        $this->selenium->clickAndWait($this->btnSearch);

    }







}

