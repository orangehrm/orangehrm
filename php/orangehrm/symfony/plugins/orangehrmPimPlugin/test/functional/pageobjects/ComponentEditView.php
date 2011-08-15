<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ComponentEditView
 *
 * @author irshad
 */
abstract  class ComponentEditView extends ComponentView{
    protected $editPane = null;
    protected $list = null;

    function __construct($selenium, $name, TitledList $list, EditPane $editPane) {
        parent::__construct($selenium, $name);
        $this->list = $list;
        $this->editPane = $editPane;

    }
}
?>
