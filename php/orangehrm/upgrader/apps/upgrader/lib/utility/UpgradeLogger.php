<?php

class UpgradeLogger {
    
    public static function writeErrorMessage($logMessage) {
        $logMessage = gmdate("Y-M-d H:i:s", time())." : ".$logMessage;
        $file = sfConfig::get('sf_root_dir')."/log/error.log";
        $result = file_put_contents($file, $logMessage, FILE_APPEND | LOCK_EX);
    
        return $result;
    }
    
    public static function clearErrorLog() {
        $file = sfConfig::get('sf_root_dir')."/log/error.log";
        $result = file_put_contents($file, "", LOCK_EX);
    }
    
    public static function getErrorLog() {
        $file = sfConfig::get('sf_root_dir')."/log/error.log";
        $result = file_get_contents($file);
        return $result;
    }
    
    public static function writeNoteMessage($upgradeDetails, $upgradeNotes) {
        $message = "Upgrade Details ========================\n\n";
        
        $startVersion = $upgradeDetails['start_version'];
        $endVersion = $upgradeDetails['end_version'];
        $startIncement = $upgradeDetails['start_increment'];
        $endIncement = $upgradeDetails['end_increment'];
        $message .= "Upgraded from $startVersion to $endVersion (SchemaIncrementers from $startIncement to $endIncement).\n\n";
        
        $message .= "Upgrade Notes ==========================\n\n";
        
        foreach ($upgradeNotes as $note) {
            $note = "(*) $note \n";
            $message .= $note;
        }
        $file = sfConfig::get('sf_root_dir')."/log/notes.log";
        $result = file_put_contents($file, $message, LOCK_EX);
    
        return $result;
    }
}