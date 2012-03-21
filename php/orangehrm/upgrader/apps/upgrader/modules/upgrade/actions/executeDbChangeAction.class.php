<?php

class executeDbChangeAction extends sfAction {
    
    public function preExecute() {
        $this->getUser()->setAttribute('currentScreen', 2);
        $this->getUser()->setAttribute('startIncrementer', 29);
        $this->getUser()->setAttribute('endIncrementer', 29);
    }
    public function execute($request) {
        $startIncrementer = $this->getUser()->getAttribute('startIncrementer');
        $endIncrementer = $this->getUser()->getAttribute('endIncrementer');
        $this->form = new DatabaseChange();
        
        $schemaIncremantArray;
        for ($i = $startIncrementer; $i <= $endIncrementer; $i++) {
            $schemaIncremantArray[] = 'increment_'.$i;
        }
        $this->schemaIncremantArray = $schemaIncremantArray;
        if ($request->isMethod('post')) {
            $this->form->bind($request->getParameter('databaseChange'));
            if ($this->form->isValid()) {
                $this->getRequest()->setParameter('submitBy', 'dbChange');
                $this->forward('upgrade','index');
            }
        }
    }
}