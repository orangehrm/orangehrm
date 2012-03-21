<?php

class addVersionInfoAction extends sfAction {

    public function preExecute() {
        $this->getUser()->setAttribute('currentScreen', 1);
    }
    
    public function execute ($request) {
        $this->form = new VersionInfo();
        
        if ($request->isMethod('post')) {
            $this->form->bind($request->getParameter('versionInfo'));
            if ($this->form->isValid()) {
                $this->getRequest()->setParameter('submitBy', 'versionInfo');
                $this->forward('upgrade','index');
            } 
        }
    }
}