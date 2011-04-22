<?php

class LabelCell extends Cell {

    public function __toString() {
        $getter = $this->getPropertyValue('getter');
        return $this->dataObject->$getter();
    }

}

