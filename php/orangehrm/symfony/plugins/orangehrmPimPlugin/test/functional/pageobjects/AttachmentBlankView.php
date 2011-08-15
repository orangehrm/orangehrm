<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AttachmentBlankView
 *
 * @author irshad
 */


class AttachmentBlankView extends ComponentBlankView{

    public function  __construct(PHPUnit_Extensions_SeleniumTestCase $selenium) {
        parent::__construct($selenium, "btnAddAttachment");
    }

    public function  clickAddButton() {
        parent::clickAddButton();
        return new AttachmentFirstTimeAddView($this->selenium);
    }

    public function  getTitle() {
        ;
    }

    public function  isViewLoaded() {
        ;
    }
}
?>
