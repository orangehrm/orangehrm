<?php

class dbChangeControlAction extends sfAction {
    
    public function execute($request) {
        $dbInfo = $this->getUser()->getAttribute('dbInfo');
        $currentTask = $request->getParameter('task');
        $currentTask = "SchemaIncrementTask$currentTask";
        $task = new $currentTask($dbInfo);
        try {
            $task->execute();
        } catch (Exception $e) {
            
        }
        $progeress = $task->getProgress();
        $arr = array('progress' => $progeress);

        echo json_encode($arr);
    }
}
