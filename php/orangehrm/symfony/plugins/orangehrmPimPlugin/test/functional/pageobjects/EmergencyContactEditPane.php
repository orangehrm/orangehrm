<?php


class EmergencyContactEditPane extends EditPane
{
    private $name = '';
    private $relationship = '';
    private $homeTelephone = '';
    private $workTelephone = '';
    private $mobile = '';

    
    public function __construct($selenium) {
        parent::__construct($selenium, "btnSaveEContact", "btnCancel");
        $this->name= 'emgcontacts_name';
        $this->relationship = 'emgcontacts_relationship';
        $this->homeTelephone = 'emgcontacts_homePhone';
        $this->workTelephone = 'emgcontacts_workPhone';
        $this->mobile = 'emgcontacts_mobilePhone';
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
         $this->selenium->selectFrame("relative=up");
         $this->selenium->type($this->name,$name);
         $this->selenium->type($this->relationship,$relationship);
         $this->selenium->type($this->homeTelephone,$homeTelephone);
         if (!is_null($mobile)) $this->selenium->type($this->mobile,$mobile);
         if (!is_null($workTelephone)) $this->selenium->type($this->workTelephone,$workTelephone);
         if($this->save())
         $this->selenium->waitForPageToLoad(config::$timeoutValue);
                 return new EmergencyContactsListView($this->selenium);
    }



}
?>
