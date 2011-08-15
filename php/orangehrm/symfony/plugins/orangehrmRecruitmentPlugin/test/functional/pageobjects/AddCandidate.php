<?php



class AddCandidate extends Page{
    
    public $txtFirstName;
    public $txtMiddleName;
    public $txtLastName;
    public $txtEmail;
    public $txtContactNo;
    public $cmbJobVacancy;
    public $txtresume;
    public $txtKeywords;
    public $txtComment;
    public $txtDateOfApplication;
    public $btnSave;
    public $list;
    public $pageURL;
    
    
    public function __construct($selenium) {
        parent::__construct($selenium);
        $this->txtFirstName = "addCandidate_firstName";
        $this->txtMiddleName = "addCandidate_middleName";
        $this->txtLastName = "addCandidate_lastName";
        $this->txtEmail = "addCandidate_email";
        $this->txtContactNo = "addCandidate_contactNo";
        $this->cmbJobVacancy = "jobDropDown0";
        $this->txtresume = "addCandidate_resume";
        $this->txtKeywords = "addCandidate_keyWords";
        $this->txtComment = "addCandidate_comment";
        $this->txtDateOfApplication = "addCandidate_appliedDate";
        $this->btnSave = "btnSave";
        $this->list = "";
        $this->pageURL = Config::$loginURL . "/symfony/web/orangehrm_dev.php/recruitment/addCandidate";
        
    }
    /**
     *
     * @param <type> $inputDataArray
     * @return  ViewCandidates
     */
    public function saveCandidate($inputDataArray){
    
        //$this->selenium->open($this->pageURL);
        
        $this->selenium->selectFrame("relative=top");
        $this->selenium->type($this->txtFirstName, $inputDataArray[$this->txtFirstName]);
        $this->selenium->type($this->txtMiddleName, $inputDataArray[$this->txtMiddleName]);
        $this->selenium->type($this->txtLastName, $inputDataArray[$this->txtLastName]);
        $this->selenium->type($this->txtEmail, $inputDataArray[$this->txtEmail]);
        $this->selenium->type($this->txtContactNo, $inputDataArray[$this->txtContactNo]);
        $this->selenium->select($this->cmbJobVacancy, $inputDataArray[$this->cmbJobVacancy]);
        $this->selenium->type($this->txtresume, $inputDataArray[$this->txtresume]);
        $this->selenium->type($this->txtKeywords, $inputDataArray[$this->txtKeywords]);
        $this->selenium->type($this->txtComment, $inputDataArray[$this->txtComment]);
        $this->selenium->type($this->txtDateOfApplication, $inputDataArray[$this->txtDateOfApplication]);
        $this->selenium->click($this->btnSave);
        if($this->selenium->isElementPresent("//label[@class='error']")){
            return $this;
        }
        else{
            $this->selenium->waitForPageToLoad(Config::$timeoutValue);
            //echo "successfully saved";
            return new ViewCandidates($this->selenium);
        }
         
        
        
    }
    
   public function getSavedSuccessfullyMessage() {
        return $this->selenium->getText("//div[@id='messagebar']/span");
    }

    public function getArrayOfValidationMessages(){
        $error[$this->txtFirstName] = null;
        $error[$this->txtLastName] = null;
        $error[$this->txtEmail] = null;
        $error[$this->txtContactNo] = null;
        $error[$this->txtDateOfApplication] = null;

       foreach ($error as $key => $value){
           //echo "xpath is: ". "//label[@class='error']/.[@for='". $key. "']" ."\n";
           //echo "value is: ". $this->selenium->getText("//label[@class='error']/.[@for='". $key. "']") ."\n";

           $error[$key]= $this->selenium->getText("//label[@class='error']/.[@for='". $key. "']");
       }
       return $error;
       
    }
    
    public function getSavedCandidateDetails(){
        return True; 
    }
}



?>
