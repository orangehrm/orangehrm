<?php

abstract class ComponentProperty implements PopulatableFromArray {
    public function populateFromArray(array $properties) {
        PropertyPopulator::populateFromArray($this, $properties);
    }
}

