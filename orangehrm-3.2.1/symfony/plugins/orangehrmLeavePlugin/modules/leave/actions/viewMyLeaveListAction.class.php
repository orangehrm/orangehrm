<?php

/**

 */
class viewMyLeaveListAction extends viewLeaveListAction {    
    
    protected function getMode() {
       
        $mode = LeaveListForm::MODE_MY_LEAVE_LIST;
        return $mode;
    }
    
    protected function isEssMode() {
       
        return true;
    }
    
    protected function getPermissions(){
        return $this->getDataGroupPermissions('leave_list', true);
    }

    protected function getCommentPermissions(){
        return $this->getDataGroupPermissions('leave_list_comments', true);
    }    
}