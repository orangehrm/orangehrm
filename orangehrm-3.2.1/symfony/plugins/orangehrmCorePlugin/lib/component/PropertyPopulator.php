<?php

class PropertyPopulator {

    public static function populateFromArray($object, $properties) {
        foreach ($properties as $property => $value) {
            $setter = (preg_match('/^is/', $property)) ? $property : 'set' . ucfirst($property);
            $object->$setter($value);
        }
    }

}

