<?php



class AddView extends PIMPage
{
    public $editPane=null;
    public $list = null;    

    function  __construct($selenium,  TitledList $list,  EditPane $editPane)
    {
        parent::__construct($selenium);
        $this->list=$list;
        $this->editPane = $editPane;

     }
}

?>
