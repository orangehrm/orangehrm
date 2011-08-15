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
        $this->selenium->clickAndWait($this->btnSearch);

    }







}

