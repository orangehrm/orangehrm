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

            if (count($request->getParameter('chkLeaveType')) == 0) {
                $this->getUser()->setFlash('templateMessage', array('NOTICE', 'Please Select at Least One Leave Type to Delete'));
                $this->redirect('leave/leaveTypeList');
            }

            $leaveTypeService = $this->getLeaveTypeService();
            $leaveTypeService->setLeaveTypeDao(new LeaveTypeDao());

            $leaveTypeService->deleteLeaveType($request->getParameter('chkLeaveType'));

            $this->getUser()->setFlash('templateMessage', array('SUCCESS', 'Leave Type Successfully Deleted'));
            $this->redirect('leave/leaveTypeList');
        }
    }

    protected function saveLeaveType($form) {

        $leaveType = new LeaveType();
        $leaveType->setLeaveTypeName($form->getValue('txtLeaveTypeName'));
        $leaveType->setAvailableFlag(1); // TODO: Replace 1 with a constant
        $this->getLeaveTypeService()->saveLeaveType($leaveType);
        $this->form->setMessage('success', 'Leave Type Successfully Saved');
        
    }

    protected function updateLeaveType() {}

    protected function undeleteLeaveType() {}   


    protected function getLeaveTypeForm() {

        return new LeaveTypeForm();

    }

    protected function getLeaveTypeService() {

        if(is_null($this->leaveTypeService)) {
            $this->leaveTypeService	= new LeaveTypeService();
        }

        return $this->leaveTypeService;

    }


}
