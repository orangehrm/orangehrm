<?php

class HeaderCell extends Cell {

    public function __toString() {
        return $this->getPropertyValue('label', 'Heading');
    }

}

