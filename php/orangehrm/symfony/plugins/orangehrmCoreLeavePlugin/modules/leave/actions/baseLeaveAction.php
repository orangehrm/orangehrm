<?php

abstract class baseLeaveAction extends sfAction {

    public $form;
    
    protected $workWeekService;
    
    /**
     *
     * @return WorkWeekService
     */
    protected function getWorkWeekService() {
        if (!($this->workWeekService instanceof WorkWeekService)) {
            $this->workWeekService = new WorkWeekService();
        }
        return $this->workWeekService;
    }
    
    /**
     *
     * @param WorkWeekService $service 
     */
    protected function setWorkWeekService(WorkWeekService $service) {
        $this->workWeekService = $service;
    }

}

