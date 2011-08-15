<?php










class Qualification extends PIMPage {
   
    /**
     *
     * @var WorkExperienceComponent  $workExperience
     * @var SkillsComponent $skills
     */
    public $workExperience;
    public $skills;
    public $education;
    public $Language;
    public $license;



    function __construct($selenium) {
        parent::__construct($selenium);
        
        $this->workExperience = new WorkExperienceComponent($selenium);
        $this->skills = new SkillsComponent($selenium);
        $this->education = new EducationComponent($selenium);
        $this->Language = new LanguageComponent($selenium);
        $this->license = new LicenseComponent($selenium);
    }
        
    
}

?>
