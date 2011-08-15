<?php

class Job extends PIMPage
{
    function  __construct($selenium) {
        parent::__construct($selenium);

        $this->selenium=$selenium;
        $this->jobTitle="cmbJobTitle";
        $this->empStatus="cmbType";
        $this->eeoCat="cmbEEOCat";
        $this->joinDate="txtJoinedDate";
        $this->popLocation="popLoc";
        $this->location="cmbNewLocationId";
        $this->assignBtn="assignLocationButton";
        $this->saveBtn="btnEditJob";
    }

    public function clickEdit()
    {

    $this->selenium->selectFrame("relative=up");
    $this->selenium->click("btnEditJob");
           return new Job($this->selenium);
    }

    public function editJobInfo($jobTitle,$empStatus,$eeoCat,$joinDate,$divisionName,$location)
    {

        $this->selenium->select($this->jobTitle,"label=$jobTitle");
        $this->selenium->select($this->empStatus,"label=$empStatus");
        $this->selenium->select($this->eeoCat, "label=$eeoCat");
        $this->selenium->type($this->joinDate,$joinDate);
        $this->selenium->select($this->location,"label=$location");
        $this->selenium->click($this->assignBtn);
        $this->selenium->click($this->saveBtn);
        
        $this->selenium->waitForPageToLoad(Config::$timeoutValue);
        return new Job($this->selenium);

    }
    public function verifyJobDetailsPresent($jobTitle,$empStatus,$eeoCat,$joinDate,$divisionName,$location)
    {
        if($jobTitle != $this->selenium->getValue($this->jobTitle))
                   {return false;}
         if($empStatus != $this->selenium->getValue($this->empStatus))
                   {return false;}
         if($eeoCat != $this->selenium->getValue($this->eeoCat))
                   {return false;}
         if($joinDate != $this->selenium->getValue($this->joinDate))
                   {return false;}
         if($location != $this->selenium->getValue($this->location))
                   {return false;}
        
    }
}
?>

   
  
    
  