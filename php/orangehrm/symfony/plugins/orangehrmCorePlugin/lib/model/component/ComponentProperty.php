<?php

abstract class ComponentProperty {
    public function populateFromArray(array $properties) {
        foreach ($properties as $property => $value) {
            $this->$property = $value;
        }
    }
}

