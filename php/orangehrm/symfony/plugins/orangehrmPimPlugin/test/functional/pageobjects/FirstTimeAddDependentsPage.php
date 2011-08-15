<?php



class FirstTimeAddDependentsPage extends PIMPage
{
    private $editPane;

    public function  __construct($selenium) {
        parent::__construct($selenium);
         $this->editPane=new DependentsEditPane($selenium);
    }
    

    public function saveDetails($depName,$relationship ,$depDateOfBirth){
        return $this->editPane->saveDetails($depName, $relationship, $depDateOfBirth);

    }


   
}

?>
