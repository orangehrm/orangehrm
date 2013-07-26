<?php

if ($attendancePermissions->canRead()) {
    include_component('core', 'ohrmList', $parmetersForListComponent);
}
?>