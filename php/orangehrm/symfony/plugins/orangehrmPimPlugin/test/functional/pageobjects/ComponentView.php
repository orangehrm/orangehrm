<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
abstract class ComponentView{

    /**
     *
     * @var PHPUnit_Extensions_SeleniumTestCase $selenium
     */
    protected $name;
    protected $selenium;

    public function  __construct( PHPUnit_Extensions_SeleniumTestCase $selenium, $name) {
        $this->name = $name;
        $this->selenium = $selenium;
    }

    public function getName(){
        return $this->name;
    }

    public function getBrowserInstance(){
        return $this->selenium;
    }


    /**
     * @return Boolean
     */
    abstract public  function isViewLoaded();

    abstract public function getTitle();



}
?>
