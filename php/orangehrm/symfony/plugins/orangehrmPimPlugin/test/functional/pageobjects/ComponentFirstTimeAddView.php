<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ComponentFirstTimeAddView
 *
 * @author irshad
 */
abstract class ComponentFirstTimeAddView extends ComponentView {

    /**
     *
     * @var EditPane $editPane
     */
    protected $editPane;

    public function __construct(PHPUnit_Extensions_SeleniumTestCase $selenium, EditPane $editPane) {
        parent::__construct($selenium, "firsttimeadd");
        $this->editPane = $editPane;
    }



}

?>
