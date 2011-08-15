<?php





class PIMAddEmployee {

    function __construct($selenium) {
        $this->selenium = $selenium;
        $this->lastName = "lastName";
        $this->firstName = "//input[@id='firstName']";
        $this->photofile = "photofile";
        $this->loginCheck = "chkLogin";
        $this->userName = "user_name";
        $this->usePassword = "user_password";
        $this->retypePassword = "re_password";
    }

    /**
     *
     * @param <type> $txtEmpLastName
     * @param <type> $txtEmpFirstName
     * @param <type> $imageUrl
     * @return PersonalDetails
     */
    public function addEmployee($txtEmpLastName, $txtEmpFirstName) {
        $this->selenium->type($this->lastName, $txtEmpLastName);
        $this->selenium->type($this->firstName, $txtEmpFirstName);
        $this->selenium->click('btnSave');
        $this->selenium->waitForPageToLoad(Config::$timeoutValue);
        return new PersonalDetails($this->selenium);
    }

    public function addEmployeeWithLoginCredentialAndPhotograph($txtEmpLastName, $txtEmpFirstName, $imageUrl, $userName=NULL, $password=NULL, $re_password=NULL) {
        $this->selenium->type($this->lastName, $txtEmpLastName);
        $this->selenium->type($this->firstName, $txtEmpFirstName);
        $this->selenium->type($this->photofile, $imageUrl);
        $this->selenium->click($this->loginCheck);
        $this->selenium->type($this->userName, $userName);
        $this->selenium->type($this->usePassword, $password);
        $this->selenium->type($this->retypePassword, $re_password);
        $this->selenium->click('btnSave');
        $this->selenium->waitForPageToLoad(Config::$timeoutValue);
    }

    public function getHeading() {
        Helper::$selenium = $this->selenium;
        return Helper::getTitle();
    }

}

?>
