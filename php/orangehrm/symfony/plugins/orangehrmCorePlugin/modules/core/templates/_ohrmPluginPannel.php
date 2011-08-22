<?php
if (isset($subComponents[$location])) {
    foreach ($subComponents[$location] as $subComponent) {
        include_component($subComponent['module'], $subComponent['component']);
    }
}

