<?php

class loadActiveLeaveTypesJsonAction extends baseLeaveAction {

    public function execute($request) {
        $activeLeaveTypesList = $this->getLeaveTypeService()->getActiveLeaveTypeNamesArray();
        $this->getResponse()->setContent(json_encode($activeLeaveTypesList));
        return sfView::NONE;
    }

}
