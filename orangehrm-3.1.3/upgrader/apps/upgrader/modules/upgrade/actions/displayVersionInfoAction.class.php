<?php

class displayVersionInfoAction extends sfAction {

    public function preExecute() {
        $this->getUser()->setAttribute('currentScreen', 'verInfo');
    }
    
    public function execute ($request) {
        $upgradeUtility = new UpgradeUtility();
        $startIncNumber = $this->getUser()->getAttribute('upgrade.startIncNumber');
        $endIncNumber   = $this->getUser()->getAttribute('upgrade.endIncNumber');
        $startVersion   = $this->getUser()->getAttribute('upgrade.currentVersion');
        $endVersion     = $upgradeUtility->getNewVersion();
        $this->newVersion   = $upgradeUtility->getNewVersion();    
        
        $this->notes        = $upgradeUtility->getNotes($startIncNumber, $endIncNumber);
        $upgradeDetails = array('start_version' => $startVersion, 'end_version' => $endVersion, 'start_increment' => $startIncNumber, 'end_increment' => $endIncNumber);
        UpgradeLogger::writeNoteMessage($upgradeDetails, $this->notes);
        if ($request->isMethod('post')) {

            $this->getRequest()->setParameter('submitBy', 'displayVersionInfo');
            $this->forward('upgrade','index');

        }
        
    }
    
}