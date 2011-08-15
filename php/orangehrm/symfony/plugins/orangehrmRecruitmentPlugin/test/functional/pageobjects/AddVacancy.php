<?php


class AddVacancy extends Page{
    
    public $cmbJobTitle;
    public $txtVacancyName;
    public $txtHiringManager;
    public $txtNoOfPositions;
    public $txtDesc;
    public $chkActive;
    public $btnSave;
    public $list;
    public $pageURL;


    public function __construct($selenium) {
        parent::__construct($selenium);
        $this->pageURL = Config::$loginURL . "symfony/web/index.php/recruitment/addJobVacancy";
        $this->cmbJobTitle = "addJobVacancy_jobTitle";
        $this->txtVacancyName = "addJobVacancy_name";
        $this->txtHiringManager = "addJobVacancy_hiringManager";
        $this->txtNoOfPositions = "addJobVacancy_noOfPositions";
        $this->txtDesc = "addJobVacancy_description";
        $this->chkActive = "addJobVacancy_status";
        $this->btnSave = "btnSave";
        $this->list = "";
    }
    
    public function saveVacancy($inputDataArray){
        
        //$this->selenium->open($this->pageURL);
        $this->selenium->selectFrame("relative=top");
        $this->selenium->select($this->cmbJobTitle, $inputDataArray[$this->cmbJobTitle]);
        $this->selenium->type($this->txtVacancyName, $inputDataArray[$this->txtVacancyName]);
        $this->selenium->type($this->txtHiringManager, $inputDataArray[$this->txtHiringManager]);
        $this->selenium->type($this->txtNoOfPositions, $inputDataArray[$this->txtNoOfPositions]);
        $this->selenium->type($this->txtDesc, $inputDataArray[$this->txtDesc]);
        if($inputDataArray[$this->chkActive] == 'yes'){
            //print_r ($inputDataArray[$this->chkActive]);
                $this->selenium->check($this->chkActive);}
        if($inputDataArray[$this->chkActive] == 'no'){
            //print_r ($inputDataArray[$this->chkActive]);
            $this->selenium->uncheck($this->chkActive);
           }
        $this->selenium->click($this->btnSave);
        if($this->selenium->isElementPresent("//label[@class='error']"))
                return $this;
        else{
            $this->selenium->waitForPageToLoad(Config::$timeoutValue);        
            return $this;
        }
        
    }
   
   public function getSavedSuccessfullyMessage() {
        return $this->selenium->getText("//div[@id='messagebar']/span");
    }
    
    public function getSavedVacancyDetails(){
        return True;
    }

    public function getArrayOfValidationMessages(){
        $error[$this->cmbJobTitle] = null;
        $error[$this->txtVacancyName] = null;
        $error[$this->txtHiringManager] = null;
        $error[$this->txtNoOfPositions] = null;
        $error[$this->txtDesc] = null;

       foreach ($error as $key => $value){
           //echo "xpath is: ". "//label[@class='error']/.[@for='". $key. "']" ."\n";
           //echo "value is: ". $this->selenium->getText("//label[@class='error']/.[@for='". $key. "']") ."\n";

           $error[$key]= $this->selenium->getText("//label[@class='error']/.[@for='". $key. "']");
       }
       return $error;
    }
    
}

?>
