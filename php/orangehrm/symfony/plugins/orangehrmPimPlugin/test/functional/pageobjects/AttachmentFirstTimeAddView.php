<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AttachmentFirstTimeAddView
 *
 * @author irshad
 */




class AttachmentFirstTimeAddView extends ComponentFirstTimeAddView {

    private $file;
    private $txtComment;

    public function __construct(PHPUnit_Extensions_SeleniumTestCase $selenium) {
        $this->file = "ufile";
        $this->txtComment = "txtAttDesc";
        parent::__construct($selenium, new AttachmentEditPane($selenium));
    }

    public function saveAttachment($filePath, $comments=NULL) {
        $this->selenium->selectFrame("relative=top");
        $this->selenium->type($this->file, $filePath);
        if ($comments)
            $this->selenium->type($this->txtComment, $comments);
        $this->editPane->save();
        return new AttachmentListView($this->selenium);
    }

    /**
     *@return Boolean
     */
    public function isViewLoaded(){
        if ($this->selenium->isVisible($this->btnSave)
                && $this->getTitle() == "Add Attachment" 
                && !$this->selenium->isVisible("btnCommentOnly"))
                return TRUE;
        else
            return FALSE;
    }

    public function  getTitle() {
        return $this->selenium->getText("attachmentSubHeading");
    }

    

}

?>
