<?php

abstract class baseLeaveAction extends ohrmBaseAction {

    protected $leaveTypeService;

    /**
     *
     * @return LeaveTypeService
     */
    protected function getLeaveTypeService() {
        if (!($this->leaveTypeService instanceof LeaveTypeService)) {
            $this->leaveTypeService = new LeaveTypeService();
        }
        return $this->leaveTypeService;
    }

    /**
     *
     * @param LeaveTypeService $service 
     */
    protected function setLeaveTypeService(LeaveTypeService $service) {
        $this->leaveTypeService = $service;
    }
    
    /**
     * 
     * @param type $dataGroups
     * @return type
     */
    protected function getDataGroupPermissions($dataGroups, $self = false) {
        return $this->getContext()->getUserRoleManager()->getDataGroupPermissions($dataGroups, array(), array(), $self, array());
    }
}