<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AttachmentEditPane
 *
 * @author irshad
 */


class AttachmentEditPane extends EditPane{

    private $btnSave;
    private $btnCancel;
    private $btnSaveComment;

    public function  __construct(PHPUnit_Extensions_SeleniumTestCase $selenium) {
        $this->btnSave = "btnSaveAttachment";
        $this->btnCancel = "cancelButton";
        $this->btnSaveComment = "btnCommentOnly";
        parent::__construct($selenium, $this->btnSave, $this->btnCancel);
    }

    public function clickSaveComment(){
        $this->selenium->selectFrame("relative=top");
        $this->selenium->click($this->btnSaveComment);
        $this->selenium->waitForPageToLoad(Config::$timeoutValue);
    }
}
?>
