<?php

class LabelCell extends Cell {

    public function __toString() {
        if ($this->isHiddenOnCallback()) {
            return '&nbsp;';
        }
        return $this->getValue() . $this->getHiddenFieldHTML();
    }

}
