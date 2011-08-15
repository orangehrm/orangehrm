<?php



class DependentsEditPane extends EditPane
{
    private $txtName = null;
    private $txtDOB = null;
    private $lstRelationship = null;
    private $txtRelatioship = null;
    

    function  __construct($selenium) {
        $this->txtName = "dependent_name";
        $this->lstRelationship = "dependent_relationshipType";
        $this->txtDOB = "dependent_dateOfBirth";
        $this->txtRelatioship = "dependent_relationship";
        parent::__construct($selenium, "btnSaveDependent", "btnCancel");
        
    }

    /**
     *
     * @param <type> $depName
     * @param <type> $depLabel
     * @param <type> $depDateOfBirth
     * @return DependentsListView 
     */
   public function saveDetails($depName,$relationship,$depDateOfBirth){

        $this->selenium->type($this->txtName,$depName);
        if ($relationship!= "Child"){
            $this->selenium->select($this->lstRelationship,"label=Other");
            $this->selenium->type($this->txtRelatioship, $relationship);
        }else
            $this->selenium->select($this->lstRelationship,"label=$relationship");
        $this->selenium->type($this->txtDOB,$depDateOfBirth);
        $this->save();
        return new DependentsListView($this->selenium);
   }




}
?>
