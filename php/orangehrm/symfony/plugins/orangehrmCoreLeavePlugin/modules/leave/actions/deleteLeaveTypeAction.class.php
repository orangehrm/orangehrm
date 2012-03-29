<?php

class deleteLeaveTypeAction extends orangehrmAction {

    protected $leaveTypeService;
    
    /**
     * Executes deleteLeaveType action
     *
     * @param sfRequest $request A request object
     */
    public function execute($request) {
        
        if ($request->isMethod('post')) {

            if (count($request->getParameter('chkSelectRow')) == 0) {
                $this->getUser()->setFlash('templateMessage', array('NOTICE', __(TopLevelMessages::SELECT_RECORDS)));
            } else {

                $leaveTypeService = $this->getLeaveTypeService();
                
                $leaveTypeIds = $request->getParameter('chkSelectRow');
                $leaveTypeService->deleteLeaveType($leaveTypeIds);
                $this->getUser()->setFlash('templateMessage', array('SUCCESS', __(TopLevelMessages::DELETE_SUCCESS)));
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
