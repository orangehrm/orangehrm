<?php

class Button extends Control {

    public function __toString() {
        
        $label = $this->getPropertyValue('label', $this->identifier);
        $id = $this->getId();
        $class = '';
        
        $attributes = array(
            'type' => $this->getPropertyValue('type', 'button'),
            'class' => $class,
            'id' => $id,
            'name' => $this->getPropertyValue('name', $id),
            'value' => __($label)
            );        
        
        $dataToggle = $this->getPropertyValue('data-toggle');
        
        if (!empty($dataToggle)) {
            $attributes['data-toggle'] = $dataToggle;
        }
        
        $dataTarget = $this->getPropertyValue('data-target');
        
        if (!empty($dataTarget)) {
            $attributes['data-target'] = $dataTarget;
        }
        
        $class = $this->getPropertyValue('class');
        
        if (!empty($class)) {
            $attributes['class'] = $class;
        }        
 
        return tag('input', $attributes);        
        
    }

    public function getId() {
        return $this->getPropertyValue('id', 'btn' . ucfirst($this->identifier));
    }

}
