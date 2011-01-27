<?php

class LeaveTypeForm extends orangehrmForm {

    const SAVING_MODE_NEW = "new";
    const SAVING_MODE_UPDATE = "update";
    const SAVING_MODE_UNDELETE = "undelete";

    public $activeTypesArray;
    public $deletedTypesArray;

    public function configure() {

        $this->loadInitialWidgets();
        $this->widgetSchema->setNameFormat('leaveType[%s]');

        /* These functions should be called when this class is initiated */
        $this->_createActiveLeaveTypesJsArray();
        $this->_createDeletedLeaveTypesJsArray();
    }

    protected function loadInitialWidgets() {

        $this->setWidget('txtLeaveTypeName', new sfWidgetFormInput(array(), array('size' => 30)));
        $this->setValidator('txtLeaveTypeName', new sfValidatorString(array(
                            'min_length' => 2,
                            'max_length' => 30
                            ),
                            array(
                            'min_length' => 'Leave type name should be at least 2 characters',
                            'max_length' => 'Leave type name should be less than 30 characters'
                            )));


        $this->setWidget('hdnSavingMode', new sfWidgetFormInputHidden(array('default' => self::SAVING_MODE_NEW)));
        $this->setValidator('hdnSavingMode', new sfValidatorPass());

        $this->setWidget('hdnOriginalLeaveTypeName', new sfWidgetFormInputHidden());
        $this->setValidator('hdnOriginalLeaveTypeName', new sfValidatorString(array('required' => false)));

        $this->setWidget('hdnLeaveTypeId', new sfWidgetFormInputHidden());
        $this->setValidator('hdnLeaveTypeId', new sfValidatorString(array('required' => false)));

        $this->setWidget('hdnUndeleteId', new sfWidgetFormInputHidden());
        $this->setValidator('hdnUndeleteId', new sfValidatorString(array('required' => false)));

    }

    public function setDefaultValues($leaveTypeId) {

        $leaveTypeService = new LeaveTypeService();
        $leaveTypeService->setLeaveTypeDao(new LeaveTypeDao());

        $leaveTypeObject = $leaveTypeService->readLeaveType($leaveTypeId);

        if ($leaveTypeObject instanceof LeaveType) {

            $this->setDefault('hdnLeaveTypeId', $leaveTypeObject->getLeaveTypeId());
            $this->setDefault('txtLeaveTypeName', $leaveTypeObject->getLeaveTypeName());
            $this->setDefault('hdnOriginalLeaveTypeName', $leaveTypeObject->getLeaveTypeName());
        }
    }

    public function setNewMode() {
        $this->setDefault('hdnSavingMode', self::SAVING_MODE_NEW);
    }
    public function setUpdateMode() {
        $this->setDefault('hdnSavingMode', self::SAVING_MODE_UPDATE);
    }
    public function setUndeleteMode() {
        $this->setDefault('hdnSavingMode', self::SAVING_MODE_UNDELETE);
    }

    private function _createDeletedLeaveTypesJsArray() {

        $leaveTypeService = new LeaveTypeService();
        $leaveTypeService->setLeaveTypeDao(new LeaveTypeDao());

        $deletedLeaveTypes = $leaveTypeService->getDeletedLeaveTypeList();

        $deletedTypesArray = array();
        $count = count($deletedLeaveTypes);

        for ($i = 0; $i < $count; $i++) {
            $deletedTypesArray[$i]['id'] = $deletedLeaveTypes[$i]->getLeaveTypeId();
            $deletedTypesArray[$i]['name'] = $deletedLeaveTypes[$i]->getLeaveTypeName();
        }

        $this->deletedTypesArray = $deletedTypesArray;
    }

    private function _createActiveLeaveTypesJsArray() {

        $leaveTypeService = new LeaveTypeService();
        $leaveTypeService->setLeaveTypeDao(new LeaveTypeDao());

        $ativeLeaveTypes = $leaveTypeService->getLeaveTypeList();

        $activeTypesArray = array();
        $count = count($ativeLeaveTypes);

        for ($i = 0; $i < $count; $i++) {
            $activeTypesArray[$i] = $ativeLeaveTypes[$i]->getLeaveTypeName();
        }

        $this->activeTypesArray = $activeTypesArray;
    }

}



?>
