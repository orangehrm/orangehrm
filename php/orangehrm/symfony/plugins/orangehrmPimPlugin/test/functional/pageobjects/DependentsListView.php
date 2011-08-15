<?php





class DependentsListView extends ListView
{
        
    public function  __construct($selenium) {
        $this->selenium = $selenium;
        $list1 = new BasicList($this->selenium, "//form[@id='frmEmpDelDependents']", TRUE);
        parent::__construct($selenium, "btnAddDependent", "delDependentBtn", $list1);
       
    }
/**
 *
 * @return DependentsAddView 
 */
   public function getBrowserInstance(){
        return $this->selenium;
    }
    
    public function  clickOnAddButton() {
        parent::clickOnAddButton();
        return new DependentsAddView($this->selenium);
    }
/**
 *
 * @return DependentsListView
 */
    public function  delete() {
        parent::clickOnDeleteButton();
        return $this;
    }

}
?>





