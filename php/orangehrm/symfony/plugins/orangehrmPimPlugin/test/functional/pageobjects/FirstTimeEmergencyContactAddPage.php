<?php




class FirstTimeEmergencyContactAddPage extends PIMPage {

    
    public function __construct($selenium) {
        parent::__construct($selenium);
        $this->editPane = new EmergencyContactEditPane($selenium);
    }

    /**
     *
     * @param <type> $name
     * @param <type> $relationship
     * @param <type> $homeTelephone
     * @param <type> $workTelephone
     * @param <type> $mobile
     * @return EmergencyContactsListView
     */
     public function saveDetails($name, $relationship, $homeTelephone, $workTelephone=NULL, $mobile=NULL){
        return $this->editPane->saveDetails($name, $relationship, $homeTelephone, $workTelephone, $mobile);
    }

}

?>
