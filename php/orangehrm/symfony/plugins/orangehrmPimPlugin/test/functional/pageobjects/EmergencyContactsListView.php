<?php



class EmergencyContactsListView extends ListView
{

    function  __construct($selenium) {
        $list = new BasicList($selenium, "//form[@id='frmEmpDelEmgContacts']", TRUE);
        parent::__construct($selenium, "btnAddContact", "delContactsBtn", $list);

   
        }
        
    public function  clickOnAddButton() {
    parent::clickOnAddButton();
    return new EmergencyContactAddView($this->selenium);
    }
   
       public function getBrowserInstance(){
        return $this->selenium;
    }
    
 }


 
?>

    