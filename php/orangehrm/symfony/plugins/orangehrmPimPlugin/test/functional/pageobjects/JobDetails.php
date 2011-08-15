<?php



class JobDetails extends PIMPage {
    
        public $cmbJobTitle;
        public $cmbEmpStatus;
        public $txtJobSpec;
        public $txtJobDesc;
        public $txtJobDuties;
        public $cmbJobCat;
        public $txtJoinedDate;
        public $cmbSubUnit;
        public $cmbLocation;
        public $txtStartDate;
        public $txtEndDate;
        public $fileContratDetails;
        public $lnkContratDetails;
        public $rdbtnKeepcurrent;
        public $rdbtnDeletecurrent;
        public $rdbtncurrent;
        public $btnSave;
        
    public function __construct($selenium) {
        parent::__construct($selenium);
        $this->cmbJobTitle = "job_job_title";
        $this->cmbEmpStatus = "job_emp_status";
        $this->txtJobSpec = "";
        $this->txtJobDesc = "";
        $this->txtJobDuties = "";
        $this->cmbJobCat = "job_eeo_category";
        $this->txtJoinedDate = "job_joined_date";
        $this->cmbSubUnit = "job_sub_unit";
        $this->cmbLocation = "job_location";    
        $this->txtStartDate = "job_contract_start_date";
        $this->txtEndDate = "job_contract_end_date";
        $this->fileContratDetails = "job_contract_file";
        $this->lnkContratDetails = "//div[@id='contractReadMode']/a[@class='fileLink']";
        $this->rdbtnKeepcurrent = "job_contract_update_1";
        $this->rdbtnDeletecurrent = "job_contract_update_2";
        $this->rdbtnReplacecurrent = "job_contract_update_3";
        $this->btnSave = "btnSave";
    }
    
    public function saveJobDetails($JobTitle, $EmpStatus=null, $JobCat=null, $JoinedDate=null, $SubUnit=null, $Location=null, $StartDate=null, $EndDate=null, $ContractFile=null){
    $this->selenium->click($this->btnSave);
    $this->selenium->select($this->cmbJobTitle, $JobTitle);
    $this->selenium->select($this->cmbEmpStatus, $EmpStatus);
    $this->selenium->select($this->cmbJobCat, $JobCat);
    $this->selenium->type($this->txtJoinedDate, $JoinedDate);
    $this->selenium->select($this->cmbSubUnit, $SubUnit);
    $this->selenium->select($this->cmbLocation, $Location);
    $this->selenium->type($this->txtStartDate, $StartDate);
    $this->selenium->type($this->txtEndDate, $EndDate);
    $this->selenium->type($this->fileContratDetails,$ContractFile);
    $this->selenium->click($this->btnSave);
    $this->selenium->waitForPageToLoad(Config::$timeoutValue);
    
    return new JobDetails($this->selenium);
    }
    
    public function replaceContractfile($ContractFile){
    $this->selenium->click($this->btnSave);
    $this->selenium->click($this->rdbtnReplacecurrent);
    $this->selenium->type($this->fileContratDetails,$ContractFile);
    $this->selenium->click($this->btnSave);
    $this->selenium->waitForPageToLoad(Config::$timeoutValue);
    
    return new JobDetails($this->selenium);
    }
    
    public function DeleteContractfile(){
    $this->selenium->click($this->btnSave);
    $this->selenium->click($this->rdbtnDeletecurrent);
    $this->selenium->click($this->btnSave);
    $this->selenium->waitForPageToLoad(Config::$timeoutValue);
    
    return new JobDetails($this->selenium);
    }
    
    public function KeepCurrentContractfile(){
    $this->selenium->click($this->btnSave);
    $this->selenium->click($this->rdbtnKeepcurrent);
    $this->selenium->click($this->btnSave);
    $this->selenium->waitForPageToLoad(Config::$timeoutValue);
    
    return new JobDetails($this->selenium);
    }
    
    public function getSavedJobDetails()
      {      
        $arrJobDetails = array();
        
        $this->arrJobDetails[0]= $this->selenium->getSelectedLabel($this->cmbJobTitle);
        $this->arrJobDetails[1]= $this->selenium->getSelectedLabel($this->cmbEmpStatus);
        $this->arrJobDetails[2]= $this->selenium->getSelectedLabel($this->cmbJobCat);
        
        if($this->selenium->getValue($this->txtJoinedDate)== "YYYY-MM-DD"){
          $this->arrJobDetails[3]=NULL;
       }else{
          $this->arrJobDetails[3]=$this->selenium->getValue($this->txtJoinedDate); 
       }
       $this->arrJobDetails[4]= $this->selenium->getSelectedLabel($this->cmbSubUnit);
       $this->arrJobDetails[5]= $this->selenium->getSelectedLabel($this->cmbLocation);
       
       if($this->selenium->getValue($this->txtStartDate)== "YYYY-MM-DD"){
          $this->arrJobDetails[6]=NULL;
       }else{
          $this->arrJobDetails[6]=$this->selenium->getValue($this->txtStartDate); 
       }
        
       if($this->selenium->getValue($this->txtEndDate)== "YYYY-MM-DD"){
          $this->arrJobDetails[7]=NULL;
       }else{
          $this->arrJobDetails[7]=$this->selenium->getValue($this->txtEndDate); 
       }
       
       if($this->selenium->getText($this->lnkContratDetails)){
          $this->arrJobDetails[8]= $this->selenium->getText($this->lnkContratDetails);
       }else{
          $this->arrJobDetails[8]= NULL;
       }
       
       return $this->arrJobDetails;
         
      } 
    
    
    }
?>
