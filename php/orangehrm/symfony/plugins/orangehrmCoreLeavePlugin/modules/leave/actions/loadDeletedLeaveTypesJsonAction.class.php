<?php

class loadDeletedLeaveTypesJsonAction extends baseLeaveAction {
    public function execute($request) {
        $deletedLeaveTypesList = $this->getLeaveTypeNames();
        $this->getResponse()->setContent(json_encode($deletedLeaveTypesList));
        return sfView::NONE;
    }
    
    protected function getLeaveTypeNames() {
        return $this->getLeaveTypeService()->getDeletedLeaveTypeNamesArray();
    }    
}
