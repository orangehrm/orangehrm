<?php

class loadActiveLeaveTypesJsonAction extends baseLeaveAction {

    public function execute($request) {
        $activeLeaveTypesList = $this->getLeaveTypeNames();
        $this->getResponse()->setContent(json_encode($activeLeaveTypesList));
        return sfView::NONE;
    }
    
    protected function getLeaveTypeNames() {
        return $this->getLeaveTypeService()->getActiveLeaveTypeNamesArray();
    }

}
