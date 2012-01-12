<?php

class LeaveTypeForm extends orangehrmForm {

    const SAVING_MODE_NEW = "new";
    const SAVING_MODE_UPDATE = "update";

    private $leaveTypeService;

    public function configure() {

        $this->loadInitialWidgets();
        $this->widgetSchema->setNameFormat('leaveType[%s]');
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
    }

    public function setDefaultValues($leaveTypeId) {

        $leaveTypeService = $this->getLeaveTypeService();

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

    public function getLeaveTypeObject() {
        
        $savingMode = $this->getValue('hdnSavingMode');
        
        if ($savingMode == self::SAVING_MODE_UPDATE) {
            $leaveType = $this->getLeaveTypeService()->readLeaveType($this->getValue('hdnLeaveTypeId'));
        } else {
            $leaveType = new LeaveType();
            $leaveType->setAvailableFlag(LeaveType::AVAILABLE);
        }        
        
        $leaveType->setLeaveTypeName($this->getValue('txtLeaveTypeName'));

        return $leaveType;        
    }
    
    public function getDeletedLeaveTypesAsJsonArray() {

        $leaveTypeService = $this->getLeaveTypeService();
        $deletedLeaveTypes = $leaveTypeService->getDeletedLeaveTypeList();

        $deletedTypesArray = array();

        foreach ($deletedLeaveTypes as $deletedLeaveType) {
            $deletedTypesArray[] = array('id' => $deletedLeaveType->getLeaveTypeId(),
                                         'name' => $deletedLeaveType->getLeaveTypeName());
        }

        return json_encode($deletedTypesArray);
    }

    public function getActiveLeaveTypesJsonArray() {

        $leaveTypeService = $this->getLeaveTypeService();
        $activeLeaveTypes = $leaveTypeService->getLeaveTypeList();

        $activeTypesArray = array();

        foreach ($activeLeaveTypes as $activeLeaveType) {
            $activeTypesArray[] = $activeLeaveType->getLeaveTypeName();
        }

        return json_encode($activeTypesArray);
    }

    public function getLeaveTypeService() {

        if(is_null($this->leaveTypeService)) {
            $this->leaveTypeService = new LeaveTypeService();
        }

        return $this->leaveTypeService;

    }    
    
    public function setLeaveTypeService($leaveTypeService) {
        $this->leaveTypeService = $leaveTypeService;
    }
}



?>
