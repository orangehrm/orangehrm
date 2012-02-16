<?php

class LeaveListActionCell extends SelectSingleCell {

    public function __toString() {
        //$statusDiffer = ($this->dataObject->getStatusCounter()->count() > 1);
        $statusDiffer = ($this->dataObject->isStatusDiffer());
        if ($statusDiffer) {
            return content_tag('a', __('Go to Detailed View'), array(
                'href' => url_for('leave/viewLeaveRequest?id=' . $this->dataObject->getLeaveRequestId()),
            ));
        } else {
            return parent::__toString();
        }
    }

}
