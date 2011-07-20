<?php

class TextboxCell extends Cell {

    public function __toString() {
        $html = ($this->getPropertyValue('readOnly', false)) ? $this->getValue() : tag('input', array(
                    'type' => 'text',
                    'name' => $this->getPropertyValue('name'),
                    'value' => $this->getValue(),
                ));

        return $html;
    }

}
