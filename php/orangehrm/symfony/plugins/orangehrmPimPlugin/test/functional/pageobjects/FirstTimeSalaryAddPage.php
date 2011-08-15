<?php




class FirstTimeSalaryAddPage extends PIMPage
{
    function  __construct($selenium) {
        parent::__construct($selenium);
        $this->salaryEditPane=new SalaryEditPane($selenium);
        $this->selenium=$selenium;
    }
}
?>
