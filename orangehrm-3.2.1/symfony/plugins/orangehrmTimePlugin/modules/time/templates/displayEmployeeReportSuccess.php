<?php

if ($employeeReportsPermissions->canRead()) {
    include_component('core', 'ohrmList', $parmetersForListComponent);
}
?>

