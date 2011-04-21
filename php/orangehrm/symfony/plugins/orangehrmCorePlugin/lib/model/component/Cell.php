<?php

abstract class Cell implements PopulatableFromArray {

    protected $properties;
    protected $dataObject;

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
        return isset($this->properties[$name]) ? $this->properties[$name] : $default;
    }

    public function getDataObject() {
        return $this->dataObject;
    }

    public function setDataobject($dataObject) {
        $this->dataObject = $dataObject;
    }

}

