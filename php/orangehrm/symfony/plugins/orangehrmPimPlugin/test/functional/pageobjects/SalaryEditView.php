<?php



class SalaryEditView extends EditView
{
    function  __construct($selenium)
    {
        parent::__construct($selenium, "//form[@id='frmEmp']/div[@id='payments']//div[@id='parentPanePayments']", NULL);
        $this->salaryEditPane=new SalaryEditPane($selenium);
    }
}
?>
