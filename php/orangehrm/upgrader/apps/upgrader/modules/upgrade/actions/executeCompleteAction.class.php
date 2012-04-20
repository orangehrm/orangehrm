<?php

class executeCompleteAction extends sfAction {
    
    public function preExecute() {
        $this->getUser()->setAttribute('currentScreen', 'completion');
    }

    public function execute($request) {
        $currentUri = $this->getRequest()->getUri();
        $this->mainAppUrl = str_replace("/upgrader/web/index.php/upgrade/executeComplete", "", $currentUri);
    }
}