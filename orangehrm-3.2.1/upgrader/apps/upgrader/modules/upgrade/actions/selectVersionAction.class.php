<?php

class selectVersionAction extends sfAction {
    
    public function preExecute() {
        $this->getUser()->setAttribute('currentScreen', 'verInfo');
    }    

    public function execute ($request) {
        
        $this->form = new VersionInfoForm();
        
    }
    
}