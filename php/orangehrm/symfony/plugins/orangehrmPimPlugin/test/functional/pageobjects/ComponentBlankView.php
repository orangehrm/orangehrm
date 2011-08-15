<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ComponentBlankView
 *
 * @author irshad
 */


abstract class ComponentBlankView extends ComponentView{

    protected $btnAdd;

    public function  __construct(PHPUnit_Extensions_SeleniumTestCase $selenium, $btnAdd) {
        parent::__construct($selenium, "blank");
        $this->btnAdd = $btnAdd;

    }

    protected function clickAddButton(){
        $this->selenium->click($this->btnAdd);
    }


}
?>
