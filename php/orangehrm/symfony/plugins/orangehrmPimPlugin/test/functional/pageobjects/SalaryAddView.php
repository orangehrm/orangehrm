<?php




class SalaryAddView extends AddView {

    function  __construct($selenium) {
        parent::__construct($selenium, "//form[@id='frmEmp']/div[@id='payments']//div[@id='parentPanePayments']", NULL);
        $this->selenium=$selenium;
        $this->salaryEditPane=new SalaryEditPane($selenium);
    }

}
?>

