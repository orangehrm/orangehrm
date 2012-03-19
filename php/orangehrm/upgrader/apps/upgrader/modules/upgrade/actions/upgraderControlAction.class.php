<?php

class upgraderControlAction extends sfAction {
    
    private $task;
    public function preExecute() {
        $this->task[0] = new SchemaIncrementTask18();
    }
    
    public function execute($request) {
        $this->task[0]->execute();
        $progeress = $this->task[0]->getProgress();
        $arr = array('progress' => $progeress);

        echo json_encode($arr);
    }
}
