<?php
    


class LicenseComponent extends Component{
    
    public $btnAdd;
    public $cmbLicType;
    public $txtLicNo;
    public $txtIssuedDate;
    public $txtExpiryDate;
    public $btnSave;
    public $btnCancel;
    public $btnDelete;
    
    function __construct(FunctionalTestcase $selenium) {
        parent::__construct($selenium, "License");
        
        $this->btnAdd = "addLicense";
        $this->cmbLicType = "license_code";
        $this->txtLicNo = "license_license_no";
        $this->txtIssuedDate = "license_date";
        $this->txtExpiryDate = "license_renewal_date";
        $this->btnSave = "btnLicenseSave";
        $this->btnCancel = "btnLicenseCancel";
        $this->btnDelete = "delLicense";
        $this->list = new BasicList($this->selenium, "//div[@id='sectionLicense']", true);
        
    }
   
    public function firstTimeAdd($licType, $licNo=null, $issuedDate=null, $expiryDate=null){
        $this->selenium->selectFrame("relative=top");
        $this->selenium->click($this->btnAdd);
        $this->selenium->select($this->cmbLicType, $licType);
        $this->selenium->type($this->txtLicNo, $licNo);
        $this->selenium->type($this->txtIssuedDate, $issuedDate);
        $this->selenium->type($this->txtExpiryDate, $expiryDate);
        $this->selenium->click($this->btnSave);
        $this->selenium->waitForPageToLoad(Config::$timeoutValue);
        
    } 
   
    public function add($licType, $licNo=null, $issuedDate=null, $expiryDate=null){
        return $this->firstTimeAdd($licType, $licNo, $issuedDate, $expiryDate);
    }
    
   public function getStatusMessage(){
        return $this->selenium->getText("licenseMessagebar");
    }
    
}

?>
