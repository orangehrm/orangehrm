<?php

class SortableHeaderCell extends HeaderCell {

    public function __toString() {
        return content_tag('a', $this->getPropertyValue('label', 'Heading'), array(
            'href' => $this->getPropertyValue('sortUrl', '#'),
            'class' => $this->getPropertyValue('currentSortOrder', 'null'),
            )
        );
    }

}

