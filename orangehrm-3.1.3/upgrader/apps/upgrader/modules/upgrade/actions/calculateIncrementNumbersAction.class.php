<?php

class calculateIncrementNumbersAction extends sfAction {
    
    public function execute ($request) {
        
        $upgradeUtility = new UpgradeUtility();
        $upgradeUtility->setDbConnection($this->getUser()->getAttribute('dbConnection'));
        
        $tableStatus    = $upgradeUtility->isUpgradeInfoTableAvailable();
        $versionInfo    = $request->getParameter('versionInfo');
        $submitBy       = $versionInfo['submitBy'];
        
        if (!$tableStatus && $submitBy != 'selectVersion') {
            $this->redirect('upgrade/selectVersion');
        }
        
        $this->form = new VersionInfoForm();

        if ($request->isMethod('post')) {
            
            $this->form->bind($request->getParameter($this->form->getName()));
            
            if ($this->form->isValid()) {
                
                $selectedVersion = $this->form->getValue('version');
                
                if ($selectedVersion < 0) {
                    throw new Exception("Invalid version");
                }
                
                $this->getUser()->setAttribute('upgrade.startIncNumber', $upgradeUtility->getStartIncrementNumber($selectedVersion));
                $this->getUser()->setAttribute('upgrade.endIncNumber', $upgradeUtility->getEndIncrementNumber()); 
                $this->getUser()->setAttribute('upgrade.currentVersion', $selectedVersion);
                
                $this->getRequest()->setParameter('submitBy', 'calculateIncrementNumbers');
                $this->forward('upgrade','index');
                
            } 
        }
    }
}