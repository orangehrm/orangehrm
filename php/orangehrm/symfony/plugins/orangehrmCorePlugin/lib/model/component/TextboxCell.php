<?php

class TextboxCell extends Cell {

    public function __toString() {
        $html = ($this->getPropertyValue('readOnly', false)) ? $this->getValue() : tag('input', array(
                    'type' => 'text',
                    'name' => $this->getPropertyValue('name'),
                    'class' => $this->getPropertyValue('classPattern'),
                    'value' => $this->getValue(),
                ));

        return $html;
    }

}
