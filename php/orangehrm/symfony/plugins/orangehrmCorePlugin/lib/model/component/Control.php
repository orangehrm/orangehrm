<?php

abstract class Control implements PopulatableFromArray {

    protected $properties;
    protected $identifier;

    public function populateFromArray(array $properties) {
        PropertyPopulator::populateFromArray($this, $properties);
    }

    public function getProperties() {
        return $this->properties;
    }

    public function setProperties($properties) {
        $this->properties = $properties;
    }

    public function getPropertyValue($name, $default = null) {
        return isset($this->properties[$name]) ? __($this->properties[$name]) : $default;
    }

    public function getIdentifier() {
        return $this->identifier;
    }

    public function setIdentifier($identifier) {
        $this->identifier = $identifier;
    }
}

