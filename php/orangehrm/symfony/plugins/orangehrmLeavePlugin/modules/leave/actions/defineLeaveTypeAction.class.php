<?php

class defineLeaveTypeAction extends orangehrmAction {

    protected $leaveTypeService;

    public function execute($request) {

        /* For highlighting corresponding menu item */
        $request->setParameter('initialActionName', 'leaveTypeList');

        $this->form = $this->getForm();

        if ($request->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {
                $leaveType = $this->form->getLeaveTypeObject();
                $this->saveLeaveType($leaveType);

                
                $eventType = ( $this->form->getValue('hdnLeaveTypeId') > 0) ? LeaveEvents::LEAVE_TYPE_UPDATE : LeaveEvents::LEAVE_TYPE_ADD;
                $this->dispatcher->notify(new sfEvent($this, $eventType,
                                array('leaveType' => $leaveType)));

                $this->redirect("leave/leaveTypeList");
            }
        } else {

            $this->undeleteForm = $this->getUndeleteForm();
            $leaveTypeId = $request->getParameter('id'); // This comes as a GET request from Leave Type List page

            if (!empty($leaveTypeId)) {
                $this->form->setDefaultValues($leaveTypeId);
                $this->form->setUpdateMode();
            }
        }
    }

    protected function saveLeaveType(LeaveType $leaveType) {
        $this->getLeaveTypeService()->saveLeaveType($leaveType);
        $message = __(TopLevelMessages::SAVE_SUCCESS);
        $this->getUser()->setFlash('success', $message);
    }

    protected function getForm() {
        $form = new LeaveTypeForm(array(), array(), true);
        $form->setLeaveTypeService($this->getLeaveTypeService());
        return $form;
    }

    protected function getUndeleteForm() {
        return new UndeleteLeaveTypeForm(array(), array(), true);
    }

    protected function getLeaveTypeService() {

        if (is_null($this->leaveTypeService)) {
            $this->leaveTypeService = new LeaveTypeService();
        }

        return $this->leaveTypeService;
    }

}
