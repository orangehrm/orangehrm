<?php

class addDatabaseInfoAction extends sfAction {
    
    public function preExecute() {
        $this->getUser()->setAttribute('currentScreen', 0);
    }
    
    public function execute($request) {
        $this->form = new DatabaseInfo();
        
        if ($request->isMethod('post')) {
            $this->form->bind($request->getParameter('databaseInfo'));
            if ($this->form->isValid()) {
                $this->getRequest()->setParameter('submitBy', 'databaseInfo');
                $this->forward('upgrade','index');
            } 
        }
    }
}