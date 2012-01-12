<?php

class deleteLeaveTypeAction extends orangehrmAction {

    protected $leaveTypeService;

    /**
     * Executes deleteLeaveType action
     *
     * @param sfRequest $request A request object
     */
    public function execute($request) {

        //authentication
        if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin']!='Yes') {
            $this->forward('leave', 'viewMyLeaveList');
        }
        
        if ($request->isMethod('post')) {

            if (count($request->getParameter('chkSelectRow')) == 0) {
                $this->getUser()->setFlash('templateMessage', array('NOTICE', __('Please Select at Least One Leave Type to Delete')));
            } else {

                $leaveTypeService = $this->getLeaveTypeService();
                $leaveTypeService->deleteLeaveType($request->getParameter('chkSelectRow'));
                $this->getUser()->setFlash('templateMessage', array('SUCCESS', __('Leave Type Successfully Deleted')));
            }
            
            $this->redirect('leave/leaveTypeList');
        }
    }

    protected function getLeaveTypeService() {

        if(is_null($this->leaveTypeService)) {
            $this->leaveTypeService	= new LeaveTypeService();
        }

        return $this->leaveTypeService;
    }


}
