<?php

class LabelCell extends Cell {

    public function __toString() {
        return $this->getValue() . $this->getHiddenFieldHTML();
    }

}
