<?php

class selectVersionAction extends sfAction {

    public function execute ($request) {
        
        $this->form = new VersionInfoForm();
        
//        if ($request->isMethod('post')) {
//            $this->form->bind($request->getParameter('versionInfo'));
//            if ($this->form->isValid()) {
//                $this->getUser()->setAttribute('versioninfo.currentVersion', $this->form->getValue('version'));
//                $this->getRequest()->setParameter('submitBy', 'versionInfo');
//                $this->forward('upgrade','index');
//            } 
//        }
    }
}