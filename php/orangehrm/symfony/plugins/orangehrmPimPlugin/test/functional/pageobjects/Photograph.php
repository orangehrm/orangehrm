<?php





class Photograph extends PIMPage
{
    function  __construct($selenium) {
         parent::__construct($selenium);
         $this->selenium=$selenium;
    }

    function clickOnEditButton()
    {
         $this->selenium->click("btnSave");
    }

 /**
  * 
  * @param String $filePath
  * @return Photograph
  */
    public function addPhotograph($filePath)
    {
        $this->selenium->click("//img[@id='empPic']");
        $this->selenium->waitForPageToLoad(Config::$timeoutValue);
        $this->selenium->click("btnSave");
        $this->selenium->type("photofile",$filePath );
        $this->selenium->click("btnSave ");
        $this->selenium->waitForPageToLoad(Config::$timeoutValue);
        return ($this);
    }

    /**
     *
     * @return Photograph
     */
    public function deletePhotograph()
    {
        $this->selenium->click("//img[@id='empPic']");
        $this->selenium->waitForPageToLoad(Config::$timeoutValue);
        $this->selenium->click("btnDelete");
        $this->selenium->click("btnYes");
        $this->selenium->waitForPageToLoad(Config::$timeoutValue);
        return ($this);
    }

    /**
     *
     * @return String
     */
    public function getSuccessmessage(){
    	$successmessage = $this->selenium->getText("//div[@id='messagebar']/span");
    	return $successmessage;
        }
    
}


?>
