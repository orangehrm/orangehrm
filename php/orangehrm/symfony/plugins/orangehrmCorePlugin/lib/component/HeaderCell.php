<?php

class HeaderCell extends Cell {

    public function __toString() {
        return content_tag('span', $this->getPropertyValue('label', 'Heading'), array(
            'class' => 'headerCell',
        ));
    }

}

