<?php

class executeDbChangeAction extends sfAction {
    
    public function preExecute() {
        $this->getUser()->setAttribute('currentScreen', 'dbChange');
    }
    public function execute($request) {
        $startIncrementer = $this->getUser()->getAttribute('upgrade.startIncNumber');
        $endIncrementer = $this->getUser()->getAttribute('upgrade.endIncNumber');

        $this->form = new DatabaseChange();
        
        $schemaIncremantArray = array();
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