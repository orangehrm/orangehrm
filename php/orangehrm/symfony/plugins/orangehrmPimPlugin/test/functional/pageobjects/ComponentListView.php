<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ComponentListView
 *
 * @author irshad
 */
abstract  class ComponentListView extends ComponentView{
        /**
     *
     * @var  BasicList list
     */
        public $list;
        private $btnAdd;
        private $btnDelete;



    public function  __construct($selenium,  $addButton,$deleteButton,  BasicList $list) {

        $this->selenium=$selenium;
        $this->list=$list;
        $this->btnAdd=$addButton;
        $this->btnDelete=$deleteButton;
        parent::__construct($selenium, "list");
    }

    public  function clickOnAddButton()
    {
       $this->selenium->click($this->btnAdd);

    }

    public function clickOnDeleteButton()
    {
        $this->selenium->click($this->btnDelete);
        $this->selenium->waitForPageToLoad(Config::$timeoutValue);
    }
}
?>
