<?php

class dbChangeControlAction extends sfAction {
    
    private $task;
    public function preExecute() {
        error_reporting(0);
        $this->task['increment_28'] = new SchemaIncrementTask28();
        $this->task['increment_29'] = new SchemaIncrementTask29();
        $this->task['increment_30'] = new SchemaIncrementTask30();
    }
    
    public function execute($request) {
        $currentTask = $request->getParameter('task');
        try {
            $this->task[$currentTask]->execute();
        } catch (Exception $e) {
            
        }
        $progeress = $this->task[$currentTask]->getProgress();
        $arr = array('progress' => $progeress);

        echo json_encode($arr);
    }
}
