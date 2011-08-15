<?php


class SalaryListView extends ListView
{
    function  __construct($selenium) {
        parent::__construct($selenium, $addButton, $deleteButton);
        $this->selenium=$selenium;
        $this->addButton="//form[@id='frmEmp']//div[@id='parentPanePayments']/div[3]/div/input[1]";
        $this->deleteButton="//form[@id='frmEmp']//div[@id='parentPanePayments']/div[3]/div/input[2]";
        $this->xpathOfList="//form[@id='frmEmp']/div[@id='payments']//div[@id='parentPanePayments']";
        }
}
?>
