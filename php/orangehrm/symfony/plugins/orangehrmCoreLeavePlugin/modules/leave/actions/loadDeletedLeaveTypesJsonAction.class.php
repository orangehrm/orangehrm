<?php

class loadDeletedLeaveTypesJsonAction extends baseLeaveAction {
    public function execute($request) {
        $deletedLeaveTypesList = $this->getLeaveTypeService()->getDeletedLeaveTypeNamesArray();
        $this->getResponse()->setContent(json_encode($deletedLeaveTypesList));
        return sfView::NONE;
    }
}
