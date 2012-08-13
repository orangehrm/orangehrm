<?php

class LeaveSummaryLeaveTypeCell extends Cell {

    public function __toString() {
        if ($this->isHiddenOnCallback()) {
            return '&nbsp;';
        }

        $html = $this->getValue();
        if(!$this->getValue('leaveTypeStatus')) {
            $html .= " (".__('Deleted').")";
        }

        $isEmployeeAccessible = isset($this->dataObject['is_accessible']) ? 
                                    $this->dataObject['is_accessible'] : false;
        
        if ($isEmployeeAccessible) {
            $html .= $this->getHiddenFieldHTML();
        }
        return $html;
    }

}
