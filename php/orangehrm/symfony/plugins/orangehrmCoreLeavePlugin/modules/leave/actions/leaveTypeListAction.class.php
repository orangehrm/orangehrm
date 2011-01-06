<?php

class leaveTypeListAction extends orangehrmAction {

    public function execute($request) {
        $message = $this->getUser()->getFlash('templateMessage');
        $this->messageType = (isset($message[0]))?strtolower($message[0]):"";
        $this->message = (isset($message[1]))?$message[1]:"";
		$this->leaveTypeList = $this->getLeaveTypeList();
    }

    protected function getLeaveTypeList() {

    	$leaveTypeService = new LeaveTypeService();
    	$leaveTypeDao = new LeaveTypeDao();
    	$leaveTypeService->setLeaveTypeDao($leaveTypeDao);

        return $leaveTypeService->getLeaveTypeList();

    }



}
