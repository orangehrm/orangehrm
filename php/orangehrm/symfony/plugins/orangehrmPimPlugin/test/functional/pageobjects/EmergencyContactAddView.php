<?php




class EmergencyContactAddView extends AddView
{
    public function  __construct($selenium) {
       
         $list = new TitledList($selenium, "//form[@id='frmEmpDelEmgContacts']");
         $editPane=new EmergencyContactEditPane($selenium);
         parent::__construct($selenium, $list, $editPane);



         //parent::__construct($selenium, , FALSE);
    }
    
   public function saveDetails($name, $relationship, $homeTelephone, $workTelephone=NULL, $mobile=NULL){
   	return $this->editPane->saveDetails($name, $relationship, $homeTelephone, $workTelephone=NULL, $mobile=NULL);
   }
}
?>
