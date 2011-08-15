<?php



class ListEmployeeInformation extends BasicList {

    function __construct($selenium) {

       
        $this->xpathOfList = "//form[@id='frmDelete']";
        parent::__construct($selenium, $this->xpathOfList, True);
    }

    public function IsItemWithoutLinksPresentInColumn($header, $itemName) {

        $columnNumber = $this->getColumnNumber($header);
        if ($columnNumber != FALSE) {

            try{
                $ok=$this->selenium->isElementPresent($this->xpathOfList . "//*/tr//td[$columnNumber][text(),'" . $itemName . "']");
                return $ok;
                }
                catch(Exception $e){ return FALSE;}
        }
        else
            return FALSE;
    }

    public function verifySortingOrder($array, $header) {
        for ($i = 1; $i <= 4; $i++) {
            if ($this->selenium->isElementPresent($this->xpathOfList . "//*/tbody/tr[$i]//td[3]/a[text()='" . $array[$i - 1] . "']")) {
                          return TRUE;
            }
            return FALSE;
        }
    }

    public function clickDelete() {

        $this->selenium->click("//input[@value='Delete']");
        $this->selenium->click("dialogDeleteBtn");
        $this->selenium->waitForPageToLoad(Config::$timeoutValue);
        $heading = $this->selenium->getText("//div[@id='messagebar']");
        $this->selenium->assertEquals("Selected Employee(s) Were Deleted Successfully", $heading);
    }

}

?>
