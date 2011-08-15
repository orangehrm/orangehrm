<?php



class SalaryEditPane extends EditPane {

    public $payGrade;
    public $currency;
    public $basicValue;
    public $payPeriod;

    function __construct($selenium) {
        parent::__construct($selenium, $saveButton, $cancelButton);
        $this->selenium = $selenium;
        $this->payGrade = "cmbSalaryGrade";
        $this->currency = "cmbCurrCode";
        $this->basicValue = "txtBasSal";
        $this->payPeriod = "cmbPayPeriod";
        $this->saveButton = "//input[@id='btnAddPayment']";
        $this->cancelButoon = "//input[@value='Reset' and @type='button' and @onclick='resetForm()']";
        $this->editSaveButton = "//input[@id='btnEditPayment']";
    }

    public function editSalaryDetails($paygrade, $currency, $basicSalary, $payPeriod) {
        if (!is_null($paygrade))
            $this->selenium->select($this->payGrade, "label=$paygrade");
        sleep(3);
        $this->selenium->select($this->currency, "label=$currency");
        sleep(3);
        $this->selenium->type($this->basicValue, $basicSalary);
        $this->selenium->select($this->payPeriod, "label=$payPeriod");
        if (!$this->selenium->click($this->saveButton)
            );
        $this->selenium->click($this->editSaveButton);
    }

}

?>
