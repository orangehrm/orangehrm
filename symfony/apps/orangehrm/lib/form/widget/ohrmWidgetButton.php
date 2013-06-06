<?php

class ohrmWidgetButton extends sfWidget {

    protected $name;
    protected $label;
    protected $attributes;

    public function __construct($name, $label, $attributes = null) {
        if (empty($attributes)) {
            $attributes = array();
        }

        $this->name = $name;
        $this->label = $label;
        $this->attributes = empty($attributes) ? array() : $attributes;
    }

    /**
     * @return string An HTML tag string
     */
    public function render($name, $value = null, $attributes = array(), $errors = array()) {
        if (empty($name)) {
            $name = $this->name;
        }

        if (empty($value)) {
            $value = $this->label;
        }

        if (empty($attributes)) {
            $attributes = $this->attributes;
        }

        if (is_array($attributes)) {
            if (!array_key_exists('id', $attributes)) {
                $attributes['id'] = $name;
            }
            if (!array_key_exists('class', $attributes)) {
                $attributes['class'] = 'plainbtn';
            }
        } else {
            $attributes['id'] = $name;
            $attributes['class'] = 'plainbtn';
        }

        return $this->renderTag('input', array_merge(array('type' => 'button', 'name' => $name, 'value' => __($value)), $attributes));
    }

    public function getDefault() {
        if (!array_key_exists('value', $this->attributes)) {
            $this->attributes['value'] = $this->label;
        }

        return $this->attributes;
    }

    public function getName() {
        return $this->name;
    }

}
