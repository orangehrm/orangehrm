<?php

class defineLeaveTypeAction extends orangehrmAction {

    protected $leaveTypeService;

    public function execute($request) {
        //authentication
        if(!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin']!='Yes') {
            $this->forward('leave', 'viewMyLeaveList');
        }
        
        $this->form = $this->getLeaveTypeForm();

        if ($request->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {

                $savingMode = $this->form->getValue('hdnSavingMode');

                if ($savingMode == 'new') {
                    $this->saveLeaveType($this->form);
                } elseif ($savingMode == 'update') {
                    $this->updateLeaveType($this->form);
                } elseif ($savingMode == 'undelete') {
                    $this->undeleteLeaveType($this->form);
                }
                $this->redirect("leave/leaveTypeList");
            }
        }
        else {
            $editId = $request->getParameter('hdnEditId'); // This comes as a GET request from Leave Type List page

            if ($editId) {
                $this->form->setDefaultValues($editId);
            }

            if ($editId || $request->getParameter('hdnEditMode') == 'yes') {
                $this->form->setUpdateMode();
            }
        }
    }

    protected function saveLeaveType($form) {

        $leaveType = new LeaveType();
        $leaveType->setLeaveTypeName($form->getValue('txtLeaveTypeName'));
        $leaveType->setAvailableFlag(1); // TODO: Replace 1 with a constant
        $this->getLeaveTypeService()->saveLeaveType($leaveType);
        $this->getUser()->setFlash('templateMessage', array('success', __('Leave Type Successfully Saved')));
        
    }

    protected function updateLeaveType($form) {

        $leaveTypeService = $this->getLeaveTypeService();

        $leaveType = $leaveTypeService->readLeaveType($form->getValue('hdnLeaveTypeId'));
        $leaveType->setLeaveTypeName($form->getValue('txtLeaveTypeName'));
        $leaveTypeService->saveLeaveType($leaveType);

        $message = __('Leave Type "%1%" Successfully Updated', array('%1%' => $leaveType->getLeaveTypeName()));
        $this->getUser()->setFlash('templateMessage', array('success', $message));
    }

    protected function undeleteLeaveType($form) {
        $leaveTypeService = $this->getLeaveTypeService();

        $undeleteId = $form->getValue('hdnUndeleteId');
        if ( !empty($undeleteId) ) {
            $leaveTypeService->undeleteLeaveType($undeleteId);
        }
        $leaveTypeName = $form->getValue('txtLeaveTypeName');
        
        $message = __('Leave Type "%1%" Successfully Undeleted', array('%1%' => $leaveTypeName));
        $this->getUser()->setFlash('templateMessage', array('success', $message));
        $this->redirect('leave/leaveTypeList');
    }


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
