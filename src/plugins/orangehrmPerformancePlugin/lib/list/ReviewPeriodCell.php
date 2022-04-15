<?php

class ReviewPeriodCell extends Cell {

    public function __toString() {
        return set_datepicker_date_format($this->dataObject->getWorkPeriodStart()).' - '.set_datepicker_date_format($this->dataObject->getWorkPeriodEnd());
    }

}
