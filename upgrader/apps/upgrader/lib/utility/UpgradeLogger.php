<?php

class UpgradeLogger {
    
    public static function writeLogMessage($logMessage) {
        $logMessage = gmdate("Y-M-d H:i:s", time())." : ".$logMessage. "\n";
        $file = sfConfig::get('sf_root_dir')."/log/debug.log";
        $result = file_put_contents($file, $logMessage, FILE_APPEND | LOCK_EX);
    
        return $result;
    }
    
    public static function writeErrorMessage($logMessage, $includeStackTrace = false) {
        $logMessage = gmdate("Y-M-d H:i:s", time())." : ".$logMessage . "\n";
        
        // Using debug_backtrace resulted in very high memory usage. 
        // Therfore, using an exception to get the stack trace.
        if ($includeStackTrace) {
            try {
                throw new Exception();
            } catch (Exception $e) {
                $stackTrace = $e->getTraceAsString();
                $logMessage .= "\nStackTrace:$stackTrace\n";
            }             
        }
 
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