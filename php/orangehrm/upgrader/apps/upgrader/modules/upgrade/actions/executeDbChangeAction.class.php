<?php

class executeDbChangeAction extends sfAction {
    
    public function preExecute() {
        $this->getUser()->setAttribute('currentScreen', 2);
        $this->getUser()->setAttribute('startIncrementer', 28);
        $this->getUser()->setAttribute('endIncrementer', 30);
        $this->getUser()->setAttribute('startVersion', '2.6.11.3');
        $this->getUser()->setAttribute('endVersion', '2.7');
    }
    public function execute($request) {
        $startIncrementer = $this->getUser()->getAttribute('startIncrementer');
        $endIncrementer = $this->getUser()->getAttribute('endIncrementer');
        UpgradeLogger::clearLog();
        $this->form = new DatabaseChange();
        
        $schemaIncremantArray;
        for ($i = $startIncrementer; $i <= $endIncrementer; $i++) {
            $schemaIncremantArray[] = $i;
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