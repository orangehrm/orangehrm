<?php

if ($projectReportPermissions->canRead()) {
    include_component('core', 'ohrmList', $parmetersForListComponent);
}
?>

