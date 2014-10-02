<?php
if (count($data) > 0) {
    include_component('dashboard', 'ohrmGraphVisualizer', array('chart' => $chart));
}else {
    echo DashboardService::NO_DATA_MESSAGE;
}
?>