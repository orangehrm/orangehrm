<?php



class LanguageComponent extends Component {
    

    public $btnAddLanguage;
    public $cmbLanguage;
    public $cmbFluency;
    public $cmbCompetency;
    public $txtComment;
    public $btnSave;
    public $btnCancel;
    public $btnDelete;
    public $list;
    
public function __construct(FunctionalTestCase $selenium) {
    
    parent::__construct($selenium, "Language");
    
        $this->btnAddLanguage = "addLanguage";
        $this->btnSave = "btnLanguageSave";
        $this->btnCancel = "btnLanguageCancel";
        $this->btnDelete = "delLanguage";
        $this->cmbLanguage = "language_code";
        $this->cmbFluency = "language_lang_type";
        $this->cmbCompetency = "language_competency";
        $this->txtComment = "language_comments";
        $this->list = new BasicList($this->selenium, "//div[@id='sectionLanguage']", true);
  
}

  public function firstTimeAddLanguage($language, $fluency, $Competency, $comments){
      
      $this->selenium->selectFrame("relative=top");
      $this->selenium->click($this->btnAddLanguage);
      $this->selenium->select($this->cmbLanguage, $language);
      $this->selenium->select($this->cmbFluency, $fluency);
      $this->selenium->select($this->cmbCompetency, $Competency);
      $this->selenium->type($this->txtComment, $comments);
      $this->selenium->clickAndWait($this->btnSave);
      return $this;
      
  }



//put your code here
}

?>
