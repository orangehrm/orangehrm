<?php




class EditView extends PIMPage {

    protected $editPane = null;
    protected $list = null;

    function __construct($selenium, TitledList $list, EditPane $editPane) {
        $this->list = $list;
        $this->editPane = $editPane;

    }

}

?>
