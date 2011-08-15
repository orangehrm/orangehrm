<?php


class LeftMenu{

    function  __construct($selenium) {
    $this->selenium=$selenium;   
    }

     public  function clickOnLInk($linkName){
        $this->selenium->selectFrame("relative=up");
        $this->selenium->clickAndWait("//div[@id='pimleftmenu']//a/span[text()='". $linkName ."']");
	/*$serverAddress = $this->selenium->getAttribute("//div[@id='pimleftmenu']//span[text()='$linkName']/..@href");
        $FrameAddress=substr($serverAddress,32,100);
        return $this->selenium->waitForFrameToLoad($FrameAddress);
     */
    }
     
     
}

?>
