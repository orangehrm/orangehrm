<?php







class EmployeeInformation extends PIMPage {

    private $xpathToName = "//table[@id='emp_list']/tbody/*";
    public $list;
    public $btnAdd;
    public $btnDelete;


    function __construct($selenium) {
        parent::__construct($selenium);
        // $this->pimAddEmployee = new PIMAddEmployee($this->selenium);
        $this->list = new ListEmployeeInformation($this->selenium);
        $this->btnAdd = "addBtn";
        $this->btnDelete = "//form[@id='frmDelete']//*[@class='plainbtn']//.[@value='Delete']";
    }

    /**
     *
     * @return PIMAddEmployee
     */
    public function clickAdd() {
        $this->selenium->selectFrame("relative=top");
        $this->selenium->click("//input[@value='Add']");
        $this->selenium->waitForPageToLoad(Config::$timeoutValue);
        return new PIMAddEmployee($this->selenium);
    }

    /**
     *
     * @param <type> $empFirstName
     * @param <type> $empLastName
     * @param <type> $empMiddleName
     * @return <type>
     */
    public function isEmployeePresent($empFirstName, $empLastName, $empMiddleName=NULL) {
        $fullName = Helper::getFullName($empFirstName, $empLastName, $empMiddleName);
        return $this->selenium->isElementPresent($this->xpathToName . "//a[contains(text(),'" . $empFirstName . "')]/../..//a[contains(text(), '" . $empLastName . "')]");
    }

    /**
     * @param <type> $empFirstName
     * @param <type> $empLastName
     * @param <type> $empMiddleName
     * @return PersonalDetails
     */
    public function clickOnAnEmployee($empFirstName, $empLastName, $empMiddleName=NULL) {
        $this->selenium->selectFrame("relative=up");
        $fullName = Helper::getFullName($empFirstName, $empLastName, $empMiddleName);
        $this->selenium->click($this->xpathToName . "//a[contains(text(),'" . $empFirstName . "')]/../..//a[contains(text(), '" . $empLastName . "')]");
        $this->selenium->waitForPageToLoad(Config::$timeoutValue);
        return new PersonalDetails($this->selenium);
    }

    public function clickOnCancel() {
        $this->selenium->selectFrame("relative=up");
        $this->selenium->click("link=Add Employee");
        $this->selenium->waitForPageToLoad(Config::$timeoutValue);
        $this->selenium->click("btnCancel");
        $this->selenium->waitForPageToLoad(Config::$timeoutValue);
    }

    public function getHeading() {
        Helper::$selenium = $this->selenium;
        return Helper::getTitle();
    }

   

    public function searchEmployee($employeeName = NULL, $supervisorName = NULL, $employeeID = NULL, $employmentStatus =NULL, $subunit = NULL, $jobTitle =NULL) {

        if ($employeeName)
            $this->selenium->type("empsearch_employee_name", $employeeName);
        if ($supervisorName)
            $this->selenium->type("empsearch_supervisor_name", $supervisorName);
        if ($employeeID)
            $this->selenium->type("empsearch_id", $employeeID);
        if ($employmentStatus)
            $this->selenium->select("empsearch_employee_status", "label=$employmentStatus");
        if ($subunit)
            $this->selenium->select("empsearch_sub_unit", "label=$subunit");
        if ($jobTitle)
            $this->selenium->select("empsearch_job_title", "label=$jobTitle");
        $this->clickSearchButton();
    }

    public function sortByFieldName($fieldName) {

        $this->selenium->click("//table[@id='emp_list']//a[text()='" . $fieldName . "']");
        $this->selenium->waitForPageToLoad(Config::$timeoutValue);
    }

    protected function clickSearchButton() {
        $this->selenium->click("searchBtn");
        $this->selenium->waitForPageToLoad(Config::$timeoutValue);
    }

    public function clickReset() {
        $this->selenium->click("resetBtn");
        $this->selenium->waitForPageToLoad(Config::$timeoutValue);
    }

    public function verifySearchedEmployeePresentInColumn($nameOrId) {
        return $this->selenium->isElementPresent("//table[@id='emp_list']/tbody/tr//*[text()='" . $nameOrId . "']");
    }

}