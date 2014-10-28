<?php

class executeSystemCheckAction extends sfAction {

    public function preExecute() {
        $this->getUser()->setAttribute('currentScreen', 'sysCheck');
    }
    
    public function execute($request) {
        $this->root_path = $this->applicationRootPath = sfConfig::get('sf_root_dir')."/..";
        $this->systemCheckUtility = new SystemCheckUtility();
        $this->form = new SystemCheck();
        
        if ($request->isMethod('post')) {
            $this->form->bind($request->getParameter('systemCheck'));
            if ($this->form->isValid()) {
                $this->getRequest()->setParameter('submitBy', 'systemCheck');
                $this->forward('upgrade','index');
            }
        }
    }
}