<?php

class leaveTypeListAction extends orangehrmAction {

    public function execute($request) {

        $message = $this->getUser()->getFlash('templateMessage');
        $this->messageType = (isset($message[0]))?strtolower($message[0]):"";
        $this->message = (isset($message[1]))?$message[1]:"";
        
        $this->_setListComponent($this->getLeaveTypeList());
    }

    protected function getLeaveTypeList() {

    	$leaveTypeService = new LeaveTypeService();
    	$leaveTypeDao = new LeaveTypeDao();
    	$leaveTypeService->setLeaveTypeDao($leaveTypeDao);

        return $leaveTypeService->getLeaveTypeList();

    }

    protected function _setListComponent($leaveTypeList) {

        $configurationFactory = $this->getListConfigurationFactory();
        
        ohrmListComponent::setActivePlugin('orangehrmCoreLeavePlugin');
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

}
