<?php

class showLogAction extends sfAction {
    
    public function execute($request) {
        $log = UpgradeLogger::getErrorLog();
        $array = array('log' => $log);
        echo json_encode($array);
    }
}