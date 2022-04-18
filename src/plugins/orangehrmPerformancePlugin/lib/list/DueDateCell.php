<?php

class DueDateCell extends Cell {

    public function __toString() {
        return set_datepicker_date_format($this->dataObject->getDueDate());
    }

}
