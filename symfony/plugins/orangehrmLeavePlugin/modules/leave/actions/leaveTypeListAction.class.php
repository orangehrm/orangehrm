<?php

class leaveTypeListAction extends baseLeaveAction {

    public function execute($request) {

        $message = $this->getUser()->getFlash('templateMessage');
        $this->messageType = (isset($message[0])) ? strtolower($message[0]) : "";
        $this->message = (isset($message[1])) ? $message[1] : "";

        $this->leaveTypePermissions = $this->getDataGroupPermissions('leave_types');
        if ($this->leaveTypePermissions->canRead()) {
            $this->_setListComponent($this->getLeaveTypeList(), $this->leaveTypePermissions);
        }
    }

    protected function getLeaveTypeList() {

        $leaveTypeService = new LeaveTypeService();
        $leaveTypeDao = new LeaveTypeDao();
        $leaveTypeService->setLeaveTypeDao($leaveTypeDao);

        return $leaveTypeService->getLeaveTypeList();
    }

    protected function _setListComponent($leaveTypeList, $permissions) {
        $runtimeDefinitions = array();
        $buttons = array();

        if ($permissions->canCreate()) {
            $buttons['Add'] = array('label' => 'Add');
        }

        if (!$permissions->canDelete()) {
            $runtimeDefinitions['hasSelectableRows'] = false;
        } else if ($permissions->canDelete()) {
            $buttons['Delete'] = array('label' => 'Delete',
                'type' => 'submit',
                'data-toggle' => 'modal',
                'data-target' => '#deleteConfModal',
                'class' => 'delete');
        }
        $runtimeDefinitions['buttons'] = $buttons;
        
        $readOnlyLeaveTypeIds = $this->getUnselectableLeaveTypeIds();
        if (count($readOnlyLeaveTypeIds) > 0) {
            $runtimeDefinitions['unselectableRowIds'] = $readOnlyLeaveTypeIds;
        }
        
        $configurationFactory = $this->getListConfigurationFactory();
        
        $configurationFactory->setRuntimeDefinitions($runtimeDefinitions);
        ohrmListComponent::setActivePlugin('orangehrmLeavePlugin');
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setListData($leaveTypeList);
        ohrmListComponent::setPageNumber(0);
        $numRecords = count($leaveTypeList);
        ohrmListComponent::setItemsPerPage($numRecords);
        ohrmListComponent::setNumberOfRecords($numRecords);
    }

    protected function getListConfigurationFactory() {
        return new LeaveTypeListConfigurationFactory();
    }
    
    protected function getUnselectableLeaveTypeIds() {
        return array();
    }

}
