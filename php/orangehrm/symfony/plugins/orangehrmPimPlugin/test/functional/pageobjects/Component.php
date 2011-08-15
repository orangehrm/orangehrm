<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Component
 *
 * @author irshad
 */


class Component {

    /**
     * @var FunctionalTestcase $selenium
     */
    protected $name;

    protected $selenium;


    public function __construct(FunctionalTestCase $selenium, $name) {
        $this->name = $name;
        $this->selenium = $selenium;
    }

    public function getName() {
        return $this->name;
    }


}

?>
