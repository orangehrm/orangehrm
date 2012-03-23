<?php

class getDatabaseInfoAction extends sfAction {
    
    public function preExecute() {
        $this->getUser()->setAttribute('currentScreen', 0);
    }
    
    public function execute($request) {
        $this->form = new DatabaseInfo();
        
        if ($request->isMethod('post')) {
            $this->form->bind($request->getParameter('databaseInfo'));
            if ($this->form->isValid()) {
                $dbInfo;
                $dbInfo['host'] = $this->form->getValue('host');
                $dbInfo['port'] = $this->form->getValue('port');
                $dbInfo['user'] = $this->form->getValue('user');
                $dbInfo['password'] = $this->form->getValue('password');
                $dbInfo['database'] = $this->form->getValue('database_name');
                
                $this->getUser()->setAttribute('dbInfo', $dbInfo);
                
                $this->getRequest()->setParameter('submitBy', 'databaseInfo');
                $this->forward('upgrade','index');
            } 
        }
    }
}