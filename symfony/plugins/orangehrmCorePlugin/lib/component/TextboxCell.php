<?php

class TextboxCell extends Cell {

    public function __toString() {
        $readOnly = $this->getPropertyValue('readOnly', false);
        
        if (($readOnly instanceof sfOutputEscaperArrayDecorator) || is_array($readOnly)) {
            list($method, $params) = $readOnly;
            $readOnly = call_user_func_array(array($this->dataObject, $method), $params->getRawValue());
        }
        
        $html = ($readOnly) ? $this->getValue() : tag('input', array(
                    'type' => 'text',
                    'name' => $this->getPropertyValue('name'),
                    'class' => $this->getPropertyValue('classPattern'),
                    'value' => $this->getValue(),
                ));

        return $html;
    }

}
