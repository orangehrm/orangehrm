<?php

class Checkbox extends Control {

    public function __toString() {
        $label = $this->getPropertyValue('label', $this->identifier);
        $id = $this->getPropertyValue('id', 'chk' . ucfirst($label));

        return tag('input', array(
            'type' => 'checkbox',
            'id' => $id,
            'name' => $this->getPropertyValue('name', $id),
            'value' => $this->getPropertyValue('value', ''),
        ));
    }

}

