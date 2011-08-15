<?php


/**
 *
 */
class ListView extends PIMPage
{
    /**
     *
     * @var  BasicList list
     */
        public $list;
        private $btnAdd;
        private $btnDelete;


  
    public function  __construct($selenium, $addButton,$deleteButton,  BasicList $list) {
        $this->selenium=$selenium;
        $this->list=$list;
        $this->btnAdd=$addButton;
        $this->btnDelete=$deleteButton;
        parent::__construct($selenium);
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
