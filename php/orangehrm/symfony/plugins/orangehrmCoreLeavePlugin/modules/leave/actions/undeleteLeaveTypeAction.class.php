<?php

class undeleteLeaveTypeAction extends orangehrmAction {

    protected $leaveTypeService;

    public function execute($request) {
        
        //authentication
        if(!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin']!='Yes') {
            $this->forward('leave', 'viewMyLeaveList');
        }
        
        $this->form = $this->getForm();

        if ($request->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {
                $undeleteId = $this->form->getValue('undeleteId');
                $this->undeleteLeaveType($undeleteId);
            }
        }
        $this->redirect("leave/leaveTypeList");        
    }



    protected function undeleteLeaveType($leaveTypeId) {
        $leaveTypeService = $this->getLeaveTypeService();
        $leaveTypeService->undeleteLeaveType($leaveTypeId);

        $leaveType = $leaveTypeService->readLeaveType($leaveTypeId);
        $leaveTypeName = $leaveType->getLeaveTypeName();
        
        $message = __('Leave Type "%1%" Successfully Undeleted', array('%1%' => $leaveTypeName));
        $this->getUser()->setFlash('templateMessage', array('success', $message));
    }


    protected function getForm() {
        $form = new UndeleteLeaveTypeForm();
        return $form;
    }

    protected function getLeaveTypeService() {

        if (is_null($this->leaveTypeService)) {
            $this->leaveTypeService = new LeaveTypeService();
        }

        return $this->leaveTypeService;
    }


}
