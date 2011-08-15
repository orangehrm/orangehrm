<?php






class PersonalDetails extends PIMPage
{

   public $txtFirstName;
    public $txtLastName;
    public $txtMiddleName;
    public $txtSSNNo;
    public $cmbNationality;
    public $txtSINNo;
    public $txtOtherID;
    public $txtLicenseNo;
    public $cmbMaritalStatus;
    public $txtDateOfBirth;
    public $male;
    public $female;
    public $expDate;
    public $ethnicRace;
    public $btnSave;

    
function __construct($selenium) {
    parent::__construct($selenium);

    $this->txtFirstName = "personal_txtEmpFirstName";
    $this->txtLastName="personal_txtEmpLastName";
    $this->txtMiddleName="personal_txtEmpMiddleName";
    $this->txtSSNNo="personal_txtNICNo";
    $this->cmbNationality="personal_cmbNation";
    $this->txtSINNo="personal_txtSINNo";
    $this->txtOtherID="personal_txtOtherID";
    $this->txtLicenseNo="personal_txtLicenNo";
    $this->cmbMaritalStatus="personal_cmbMarital";
    $this->txtDateOfBirth="personal_DOB";
    $this->male="personal_optGender_1";
    $this->female="personal_optGender_2";
    $this->expDate="personal_txtLicExpDate";
    $this->ethnicRace="personal_cmbEthnicRace";
    $this->btnSave = "btnSave";
}

/**
 *
 * @return EmployeeInformation 
 */




 
/**
 *
 * @param <type> $empFirstName
 * @param <type> $empLastName
 * @param <type> $empMiddleName
 * @param <type> $NICNo
 * @param <type> $cmbNation
 * @param <type> $SINNo
 * @param <type> $OtherID
 * @param <type> $LicenNo
 * @param <type> $dateOfbirth
 * @param <type> $maritalStatus
 * @param <type> $licenExpDate
 * @param <type> $EthnicRace
 * @return PersonalDetails 
 */
   public function saveDetails($empFirstName,$empLastName,$empMiddleName,$gender,$NICNo=NULL,$cmbNation=NULL,$SINNo=NULL,$OtherID=NULL,$LicenNo=NULL,$dateOfbirth=NULL,$maritalStatus=NULL,$licenExpDate=NULL,$EthnicRace=NULL)
      {
         $this->selenium->selectFrame("relative=up");
         $this->selenium->click("btnSave");
         
         $this->selenium->type($this->txtFirstName, $empFirstName);
         $this->selenium->type($this->txtLastName, $empLastName);
         $this->selenium->type($this->txtMiddleName, $empMiddleName);
         
         if ($gender == "Male" )
             $this->selenium->click($this->male);
         else if($gender == "Female")
             $this->selenium->click($this->female);
         
         if($NICNo)
            $this->selenium->type($this->txtSSNNo,$NICNo);
         if($cmbNation)
            $this->selenium->select($this->cmbNationality,$cmbNation);
         if($SINNo)
            $this->selenium->type($this->txtSINNo,$SINNo);
         if($OtherID)
            $this->selenium->type($this->txtOtherID,$OtherID);
         if($LicenNo)
            $this->selenium->type($this->txtLicenseNo,$LicenNo);
         if($dateOfbirth)
            $this->selenium->type($this->txtDateOfBirth,$dateOfbirth);
         if($maritalStatus)
            $this->selenium->select($this->cmbMaritalStatus,$maritalStatus);
         if($licenExpDate)
            $this->selenium->type($this->expDate,$licenExpDate);
         if($EthnicRace)
         $this->selenium->select($this->ethnicRace,$EthnicRace);  
         
         //$this->selenium->type("personal_cmbMarital",$Military);
         //$this->selenium->click("chkSmokeFlag");
         //$this->selenium->click($this->male);
         
         $this->selenium->click($this->btnSave);      
         $this->selenium->waitForPageToLoad(Config::$timeoutValue);
         
/*         if ($gender == "Male" )
             $this->selenium->click($this->male);
         else if($gender == "Female")
             $this->selenium->click($this->female);
         */
         return new PersonalDetails($this->selenium);
      }
/**
 *
 * @param <type> $empFirstName
 * @param <type> $empLastName
 * @param <type> $empMiddleName
 * @param <type> $NICNo
 * @param <type> $cmbNation
 * @param <type> $SINNo
 * @param <type> $OtherID
 * @param <type> $LicenNo
 * @param <type> $dateOfbirth
 * @param <type> $maritalStatus
 * @param <type> $licenExpDate
 * @param <type> $EthnicRace
 * @return Boolean
 */
     public function getSavedPersonalDetails()
      {      
        $arrPersonalDetails = array();
        
        $this->arrPersonalDetails[0]= $this->selenium->getValue($this->txtFirstName);
        $this->arrPersonalDetails[1]= $this->selenium->getValue($this->txtLastName);
        $this->arrPersonalDetails[2]= $this->selenium->getValue($this->txtMiddleName);
        
        if($this->selenium->getValue($this->male) == "on"){
            
          $this->arrPersonalDetails[3]= "Male"; 
          
        }else if($this->selenium->getValue($this->female)=="on"){
            
          $this->arrPersonalDetails[3]= "Female";   
          
        }else if(($this->selenium->getValue($this->male)!="on")& ($this->selenium->getValue($this->female)!="on")){
            
          $this->arrPersonalDetails[3]= Null;    
        }
        
       $this->arrPersonalDetails[4]=$this->selenium->getValue($this->txtSSNNo);
       $this->arrPersonalDetails[5]=$this->selenium->getSelectedLabel($this->cmbNationality);
       $this->arrPersonalDetails[6]=$this->selenium->getValue($this->txtSINNo);
       $this->arrPersonalDetails[7]=$this->selenium->getValue($this->txtOtherID);
       $this->arrPersonalDetails[8]=$this->selenium->getValue($this->txtLicenseNo);
       
       if($this->selenium->getValue($this->txtDateOfBirth)== "YYYY-MM-DD"){
          $this->arrPersonalDetails[9]=NULL;
       }else{
          $this->arrPersonalDetails[9]=$this->selenium->getValue($this->txtDateOfBirth); 
       }
       $this->arrPersonalDetails[10]=$this->selenium->getValue($this->cmbMaritalStatus);
       
       if($this->selenium->getValue($this->expDate)== "YYYY-MM-DD"){
          $this->arrPersonalDetails[11]=NULL;
       }else{
          $this->arrPersonalDetails[11]=$this->selenium->getValue($this->expDate);
       } 
       $this->arrPersonalDetails[12]=$this->selenium->getSelectedLabel($this->ethnicRace);

       return $this->arrPersonalDetails;
         
      } 
      
}
?>
