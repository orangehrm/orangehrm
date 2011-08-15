<?php



//

class EmergencyContactsEditView extends EditView{

    public function  __construct($selenium) {
         $list = new TitledList($selenium, "//table[@id='emgcontact_list']", FALSE);
         $editPane=new EmergencyContactEditPane($selenium);
         parent::__construct($selenium, $list, $editPane);
        
    }
    
   public function saveDetails($name, $relationship, $homeTelephone, $workTelephone=NULL, $mobile=NULL){
   	return $this->editPane->saveDetails($name, $relationship, $homeTelephone, $workTelephone=NULL, $mobile=NULL);
   }
}
?>


