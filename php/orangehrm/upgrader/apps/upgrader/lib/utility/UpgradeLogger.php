<?php

class UpgradeLogger {
    
    public static function writeMessage($logMessage) {
        $logMessage = gmdate("Y-M-d H:i:s", time())." : ".$logMessage;
        $file = sfConfig::get('sf_root_dir')."/log/upgrader.log";
        $result = file_put_contents($file, $logMessage, FILE_APPEND | LOCK_EX);
    
        return $result;
    }
    
    public static function clearLog() {
        $file = sfConfig::get('sf_root_dir')."/log/upgrader.log";
        $result = file_put_contents($file, "", LOCK_EX);
    }
    
    public static function getLog() {
        $file = sfConfig::get('sf_root_dir')."/log/upgrader.log";
        $result = file_get_contents($file);
        return $result;
    }
    
}