<?php



class AddPane {

    /**
     *
     * @var PHPUnit_Extensions_SeleniumTestCase $selenium
     */
    public $saveButton = '';
    public $cancelButton = '';
    public $selenium = '';

    function __construct($selenium, $saveBUtton, $cancelButton) {
        $this->selenium = $selenium;
        $this->saveButton = $saveBUtton;
        $this->cancelButton = $cancelButton;
    }

    function clickSave() {
        $this->selenium->click($this->saveButton);
        $this->selenium->waitForPageToLoad(Config::$timeoutValue);
        return $this;
    }

    function clickCancel() {

        $this->selenium->click($this->cancelButton);
        $this->selenium->waitForPageToLoad(Config::$timeoutValue);
        return $this;
    }

}

?>
