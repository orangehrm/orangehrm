<?php





class PIMPage extends Page {

    /**
     *
     * @var AttachmentComponent attachment
     */
    private $leftMenu;
    private $photo;
    public $attachment;

    function __construct($selenium) {
        parent::__construct($selenium);
        $this->leftMenu = new LeftMenu($selenium);
        $this->photo = "//div[@id='currentImage']/center/a/img";
        $this->attachment = new AttachmentComponent($selenium);

    }

    public function verifyPhotographPresent() {
        return $this->selenium->isElementPresent($this->photo);
    }

    public function getSavedSuccessfullyMessage() {
        return $this->selenium->getText("//div[@id='messagebar']/span");
    }

    public function viewPersonalDetails() {
        $this->leftMenu->clickOnLInk("Personal Details");
        return new PersonalDetails($this->selenium);
    }

    public function viewContactDetails() {
        $this->leftMenu->clickOnLInk("Contact Details");
        return new ContactDetails($this->selenium);
    }

    public function viewEmergencyContactDetails() {
        $this->leftMenu->clickOnLInk("Emergency Contacts");
        return new EmergencyContactsListView($this->selenium);
    }

    /**
     *
     * @return Qualification 
     */
    public function viewQualifications(){
        $this->leftMenu->clickOnLInk("Qualifications");
        return new Qualification($this->selenium);
    }

    /**
     *
     * @return PIMPage 
     */
    public function viewDependents() {
        $this->leftMenu->clickOnLInk("Dependents");
        return $this;
    }

    public function viewImmigration() {
        $this->leftMenu->clickOnLInk("Immigration");
        return new ImmigrationListView($this->selenium);
    }

    public function viewPhotograph() {
        $this->leftMenu->clickOnLInk("Photograph");
        
        return new Photograph($this->selenium);
    }

    /**
     *
     * @return PIMPage
     */
    public function viewMembership(){
        $this->leftMenu->clickOnLInk("Membership");
        return $this;
    }

    /**
     *
     * @return PIMPage
     */
   public function viewJob() {
        $this->leftMenu->clickOnLInk("Job");
        return new JobDetails($this->selenium);
    }

    public function viewSalary() {
        $this->leftMenu->clickOnLInk("Salary");
        return new FirstTimeSalaryAddPage($this->selenium);

        }
  public function viewTaxExemptions() {
        $this->leftMenu->clickOnLInk("Tax Exemptions");
        return new TaxExcemptions($this->selenium);

        }
        /**
         * @return AttachmentComponent
         */
        public function getAttachmentComponent(){
            return $this->attachment;
        }

}

