<?php

class defineLeaveTypeAction extends baseLeaveAction {

    protected $leaveTypeService;

    public function execute($request) {

        $this->leaveTypePermissions = $this->getDataGroupPermissions('leave_types');
        
        $leaveTypeId = $request->getParameter('id'); // This comes as a GET request from Leave Type List page
        
        $valuesForForm = array('leaveTypePermissions' => $this->leaveTypePermissions, 'leaveTypeId' => $leaveTypeId);

        /* For highlighting corresponding menu item */
        $request->setParameter('initialActionName', 'leaveTypeList');

        $this->form = $this->getForm($valuesForForm);

        if ($request->isMethod('post')) {
            if ($this->leaveTypePermissions->canCreate() || $this->leaveTypePermissions->canUpdate()) {
                $this->form->bind($request->getParameter($this->form->getName()));

                if ($this->form->isValid()) {
                    $leaveType = $this->form->getLeaveTypeObject();
                    $this->saveLeaveType($leaveType);


                    $eventType = ( $this->form->getValue('hdnLeaveTypeId') > 0) ? LeaveEvents::LEAVE_TYPE_UPDATE : LeaveEvents::LEAVE_TYPE_ADD;
                    $this->dispatcher->notify(new sfEvent($this, $eventType,
                                    array('leaveType' => $leaveType)));

                    $this->redirect("leave/leaveTypeList");
                }
            }
        } else {            
                    
            if (!$this->leaveTypePermissions->canRead() || 
                    (empty($leaveTypeId) && !$this->leaveTypePermissions->canCreate())) {
               $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));   
            }
            
            if ($this->leaveTypePermissions->canCreate()) {
                $this->undeleteForm = $this->getUndeleteForm();
            }

            $this->leaveTypeId = $leaveTypeId;

            if (!empty($leaveTypeId)) {
                $this->form->setDefaultValues($leaveTypeId);
                $this->form->setUpdateMode();
            }
        }
        
        $title = __('View Leave Type');
        if ($this->form->isUpdateMode()) {
            if ($this->leaveTypePermissions->canUpdate()) {
                $title = __('Edit Leave Type');
            }
        } else {
            $title = __('Add Leave Type');
        }
        
        $this->title = $title;
    }

    protected function saveLeaveType(LeaveType $leaveType) {
        $this->getLeaveTypeService()->saveLeaveType($leaveType);
        $message = __(TopLevelMessages::SAVE_SUCCESS);
        $this->getUser()->setFlash('success', $message);
    }

    protected function getForm($valuesForForm) {
        $form = new LeaveTypeForm(array(), $valuesForForm, true);
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
