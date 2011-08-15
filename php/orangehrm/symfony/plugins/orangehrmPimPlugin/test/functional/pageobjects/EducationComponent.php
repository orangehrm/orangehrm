<?php




class EducationComponent extends Component{
    
    public $btnAddEducation;
    public $cmbEducation_code;
    public $txtEducation_major;
    public $txtEducation_year;
    public $txtEducation_gpa;
    public $txtStartDate;
    public $txtEndDate;
    public $btnEducationSave;
    public $btnEducationCancel;
    public $btnDelEducation;
    public $list;
    
    public function __construct(FunctionalTestCase $selenium) {
        parent::__construct($selenium, "Education");

        $this->btnAddEducation = "addEducation";
        $this->cmbEducation_code = "education_code";
        $this->txtEducation_major = "education_major";       
        $this->txtEducation_year = "education_year"; 
        $this->txtEducation_gpa = "education_gpa";
        $this->txtStartDate = "education_start_date";
        $this->txtEndDate ="education_end_date";
        $this->btnEducationSave = "btnEducationSave";
        $this->btnEducationCancel="btnEducationCancel";
        $this->btnDelEducation="delEducation";
        $this->list = new BasicList($this->selenium, "//div[@id='sectionEducation']", true);
       
    }

    
    public function firstTimeAddEducation($program,$Major=null,$year=null,$gpa=null,$startDate=null,$endDate=null){
        
        $this->selenium->selectFrame("relative=top");
        $this->selenium->click($this->btnAddEducation);  
        $this->selenium->select($this->cmbEducation_code, $program);
        $this->selenium->type($this->txtEducation_major, $Major);
        $this->selenium->type($this->txtEducation_year, $year);
        $this->selenium->type($this->txtEducation_gpa, $gpa);
        $this->selenium->type($this->txtStartDate, $startDate);
        $this->selenium->type($this->txtEndDate, $endDate);
        $this->selenium->clickAndWait($this->btnEducationSave);
       return $this;
    
    }
}

?>
