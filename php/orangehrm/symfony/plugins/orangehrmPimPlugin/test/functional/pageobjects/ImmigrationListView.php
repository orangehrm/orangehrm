<?php


class ImmigrationListView extends ListView
{

    public $addButton='';
    function  __construct($selenium) {
    	$this->selenium=$selenium;
    	$Immilist = new BasicList($this->selenium, "//form[@id='frmImmigrationDelete']", TRUE);
        parent::__construct($selenium, "btnAdd", "btnDelete", $Immilist );
        
       // $this->xpathOfList="//form[@id='frmImmigrationDelete']";
        //$this->chkBox="immigration";
        }
  
    public function  clickOnAddButton() {
        parent::clickOnAddButton();
        return new ImmigrationAddView($this->selenium);
    }

 }

?>
