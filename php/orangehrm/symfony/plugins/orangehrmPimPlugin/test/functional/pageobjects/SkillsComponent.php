<?php



class SkillsComponent extends Component{
   
    public $btnAdd ;
    public $cmbSkill ;
    public $txtYOE ;
    public $txtComments ;
    public $btnSave ;
    public $btnCancel ;
    public $btnDelete ;

    function __construct(FunctionalTestcase $selenium) {
        parent::__construct($selenium, "Skills");
        
        $this->btnAdd = "addSkill";
        $this->cmbSkill = "skill_code";
        $this->txtYOE = "skill_years_of_exp";
        $this->txtComments = "skill_comments";
        $this->btnSave = "btnSkillSave";
        $this->btnCancel = "";
        $this->btnDelete = "";
        $this->list = new BasicList($this->selenium, "//div[@id='sectionSkill']", true);
        
    }
    /**
     *
     * @param type $skill
     * @param type $yoe
     * @param type $comments
     * @return Qualification 
     */
    public function firstTimeAdd($skill, $yoe=null, $comments=null){
        $this->selenium->selectFrame("relative=top");
        $this->selenium->click($this->btnAdd);
        $this->selenium->select($this->cmbSkill, $skill);
        $this->selenium->type($this->txtYOE, $yoe);
        $this->selenium->type($this->txtComments, $comments);
        $this->selenium->click($this->btnSave);
        $this->selenium->waitForPageToLoad(Config::$timeoutValue);
        
        return new Qualification($this->selenium);
    }
    
    public function add($skill, $yoe=null, $comments=null){
        return $this->firstTimeAdd($skill, $yoe, $comments);
    }


    public function getStatusMessage(){
        return $this->selenium->getText("skillMessagebar");
    }
}

?>
