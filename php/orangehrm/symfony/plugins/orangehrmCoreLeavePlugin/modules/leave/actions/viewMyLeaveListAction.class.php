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

}