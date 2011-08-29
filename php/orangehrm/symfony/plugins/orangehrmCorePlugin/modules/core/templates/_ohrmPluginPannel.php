<?php
if (isset($subComponents[$location])) {
    foreach ($subComponents[$location] as $subComponent) {
        if (array_key_exists('component', $subComponent)) {
            include_component($subComponent['module'], $subComponent['component']);
        } elseif (array_key_exists('partial', $subComponent)) {
            include_partial("{$subComponent['module']}/{$subComponent['partial']}");
        }
    }
}

