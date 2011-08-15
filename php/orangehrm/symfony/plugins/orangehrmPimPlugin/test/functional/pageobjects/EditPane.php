<?php

class EditPane {

    /**
     *
     * @var PHPUnit_Extensions_SeleniumTestCase $selenium
     */
    protected $selenium;
    public $saveButton;
    public $cancelButoon;

    public function __construct($selenium, $saveButton, $cancelButton) {
        $this->selenium = $selenium;
        $this->saveButton = $saveButton;
        $this->cancelButton = $cancelButton;
    }

    public function save() {
        $this->selenium->selectFrame("relative=top");
        $this->selenium->click($this->saveButton);
        $this->selenium->waitForPageToLoad(Config::$timeoutValue);
        return($this);
    }

    public function cancel() {
        $this->selenium->selectFrame("relative=top");
        $this->selenium->click($this->cancelButton);
        $this->selenium->waitForPageToLoad(Config::$timeoutValue);
    }

    public function getBrowserInstance(){
        return $this->selenium;
    }

}

?>
