<?php

class dbChangeControlAction extends sfAction {
    
    public function execute($request) {
        $dbInfo = $this->getUser()->getAttribute('dbInfo');
        $taskNo = $request->getParameter('task');
        
        UpgradeLogger::writeLogMessage('Task:' . $taskNo);
        
        $currentTask = "SchemaIncrementTask$taskNo";
        
        UpgradeLogger::writeLogMessage("Running task class: $currentTask");        
        $task = new $currentTask($dbInfo);
        try {
            $task->execute();
        } catch (Exception $e) {
            UpgradeLogger::writeErrorMessage("Error when running task: " . $e->getMessage() . 
                    ', stacktrace = ' . $e->getTraceAsString());
        }
        $progeress = $task->getProgress();
        $arr = array('progress' => $progeress);

        echo json_encode($arr);
    }
}
