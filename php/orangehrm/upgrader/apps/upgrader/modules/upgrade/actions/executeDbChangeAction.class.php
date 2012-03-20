<?php

class executeDbChangeAction extends sfAction {
    
    public function preExecute() {
        $this->getUser()->setAttribute('currentScreen',2);
        $this->getUser()->setAttribute('startIncrementer',28);
        $this->getUser()->setAttribute('endIncrementer',29);
    }
    public function execute($request) {
        $startIncrementer = $this->getUser()->getAttribute('startIncrementer');
        $endIncrementer = $this->getUser()->getAttribute('endIncrementer');
        
        $schemaIncremantArray;
        for ($i = $startIncrementer; $i <= $endIncrementer; $i++) {
            $schemaIncremantArray[] = 'increment_'.$i;
        }
        $this->schemaIncremantArray = $schemaIncremantArray;
    }
}