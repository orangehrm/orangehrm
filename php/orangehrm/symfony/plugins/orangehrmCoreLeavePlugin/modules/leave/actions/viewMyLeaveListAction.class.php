<?php

/**

 */
class viewMyLeaveListAction extends viewLeaveListAction {    
    
    protected function getMode() {
        
        $empNumber = $this->getUser()->getAttribute('auth.empNumber');            
        if (empty($empNumber)) {
            $mode = parent::getMode();
        } else {
            $mode = LeaveListForm::MODE_MY_LEAVE_LIST;
        }        
        
        return $mode;
    }

}