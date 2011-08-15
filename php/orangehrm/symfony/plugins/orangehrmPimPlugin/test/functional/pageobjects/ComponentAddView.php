<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ComponentAddView
 *
 * @author irshad
 */
abstract class ComponentAddView extends ComponentView{

    public $editPane=null;
    public $list = null;

    function  __construct($selenium, $name, TitledList $list,  EditPane $editPane)
    {
        parent::__construct($selenium, $name);
        $this->list=$list;
        $this->editPane = $editPane;

     }
}
?>
