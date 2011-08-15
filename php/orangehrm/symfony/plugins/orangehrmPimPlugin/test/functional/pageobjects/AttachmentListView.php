<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AttachmentListView
 *
 * @author irshad
 */


class AttachmentListView extends ComponentListView{

    public function  __construct(PHPUnit_Extensions_SeleniumTestCase $selenium) {

        $list = new BasicList($selenium, "//form[@id='frmEmpDelAttachments']", True);
        parent::__construct($selenium, "btnAddAttachment", "btnDeleteAttachment", $list);
    }

    public function  getTitle() {
        ;
    }

    public function  isViewLoaded() {

        ;
    }
}
?>
