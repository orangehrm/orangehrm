<?php

class LeaveSummaryLeaveTypeCell extends Cell {

    public function __toString() {
        if ($this->isHiddenOnCallback()) {
            return '&nbsp;';
        }

        $leaveType = $this->getValue();
        if(!$this->getValue('leaveTypeStatus')) {
            $leaveType .= " (".__('Deleted').")";
        }
        $default = $this->getPropertyValue('default');

        return $leaveType . $this->getHiddenFieldHTML();
    }

}
