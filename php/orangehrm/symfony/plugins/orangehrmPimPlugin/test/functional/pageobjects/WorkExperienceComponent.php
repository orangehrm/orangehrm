<?php



class WorkExperienceComponent extends Component {

    public $BtnAddWorkExperiance;
    public $txtEmployer;
    public $txtJobTitle;
    public $txtFromDate;
    public $txtToDate;
    public $txtComments;
    public $btnWorkExpSave;
    public $btnWorkExpCancel;
    public $btnDelWorkExperience;
    public $list;
    
    public function __construct(FunctionalTestCase $selenium) {
        parent::__construct($selenium, "Work Experience");

        $this->btnAddWorkExperiance = "addWorkExperience";
        $this->btnWorkExpSave = "btnWorkExpSave";
        $this->btnWorkExpCancel = "btnWorkExpCancel";
        $this->btnDelWorkExperience = "delWorkExperience";
        $this->txtEmployer = "experience_employer";
        $this->txtJobTitle = "experience_jobtitle";
        $this->txtFromDate = "experience_from_date";
        $this->txtToDate = "experience_to_date";
        $this->txtComments = "experience_comments";
        $this->list = new BasicList($this->selenium, "//div[@id='sectionWorkExperience']", true);
    }

    public function firstTimeAdd($company, $jobTitle, $fromDate=null, $toDate=null, $comments=null) {

        $this->selenium->selectFrame("relative=top");
        $this->selenium->click($this->btnAddWorkExperiance);
        $this->selenium->type($this->txtEmployer, $company);
        $this->selenium->type($this->txtJobTitle, $jobTitle);
        $this->selenium->type($this->txtFromDate, $fromDate);
        $this->selenium->type($this->txtToDate, $toDate);
        $this->selenium->type($this->txtComments, $comments);
        $this->selenium->clickAndWait($this->btnWorkExpSave);
        return $this;
    }

    public function add($company, $jobTitle, $fromDate=null, $toDate=null, $comments=null){
        return $this->firstTimeAdd($company, $jobTitle, $fromDate, $toDate, $comments);

    }

    public function delete($company){
        $this->selenium->selectFrame("relative=top");
        $this->list->select("Company", $company);
        $this->selenium->clickAndWait($this->btnDelWorkExperience);
    }

    public function deleteAll(){
        $this->selenium->selectFrame("relative=top");
        $this->list->selectAllInTheList();
        $this->selenium->clickAndWait($this->btnDelWorkExperience);

    }

    public function getStatusMessage(){
        return $this->selenium->getText("workExpMessagebar");
    }

    public function edit($company, $jobTitle, $fromDate=null, $toDate=null, $comments=null){
        $this->selenium->selectFrame("relative=top");
        $this->list->clickOntheItem("Company", $company);
        $this->selenium->type($this->txtEmployer, $company);
        $this->selenium->type($this->txtJobTitle, $jobTitle);
        $this->selenium->type($this->txtFromDate, $fromDate);
        $this->selenium->type($this->txtToDate, $toDate);
        $this->selenium->type($this->txtComments, $comments);
        $this->selenium->clickAndWait($this->btnWorkExpSave);
        return $this;
    }

}

?>
