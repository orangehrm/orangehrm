<?php

class displayVersionInfoAction extends sfAction {

    public function preExecute() {
        $this->getUser()->setAttribute('currentScreen', 1);
    }
    
    public function execute ($request) {
        
//        $startIncNumber = $this->getUser()->getAttribute('upgrade.startIncNumber');
//        $endIncNumber   = $this->getUser()->getAttribute('upgrade.endIncNumber');
        
        $upgradeUtility = new UpgradeUtility();
        $this->newVersion = $upgradeUtility->getNewVersion();        
        
        if ($request->isMethod('post')) {

            $this->getRequest()->setParameter('submitBy', 'displayVersionInfo');
            $this->forward('upgrade','index');

        }
    }
}