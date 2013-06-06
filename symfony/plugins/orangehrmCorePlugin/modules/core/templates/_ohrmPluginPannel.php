<?php
if (isset($subComponents[$location])) {
    foreach ($subComponents[$location] as $subComponent) {
        if (isset($subComponent['component'])) {
            include_component($subComponent['module'], $subComponent['component']);
        }
        if (isset($subComponent['partial'])) {
            include_partial("{$subComponent['module']}/{$subComponent['partial']}");
        }
    }
}

