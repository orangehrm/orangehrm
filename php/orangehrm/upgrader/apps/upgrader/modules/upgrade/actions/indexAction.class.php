<?php

class indexAction extends sfAction {
    
    public function execute($request) {
        if ($request->isMethod('post')) {
            $submitBy = $request->getParameter('submitBy');
            switch ($submitBy) {
                case 'databaseInfo':
                    $this->redirect('upgrade/executeSystemCheck');
                    break;
                case 'systemCheck':
                    $this->redirect('upgrade/calculateIncrementNumbers');
                    break;
                case 'versionSelection':
                    $this->redirect('upgrade/calculateIncrementNumbers');
                    break; 
                case 'calculateIncrementNumbers':
                    $this->redirect('upgrade/displayVersionInfo');
                    break;                
                case 'displayVersionInfo':
                    $this->redirect('upgrade/executeDbChange');
                    break;
                case 'dbChange':
                    $this->redirect('upgrade/executeConfChange');
                    break;
                case 'configureFile':
                    $this->redirect('upgrade/executeComplete');
                    break;
                    
            }
        } else {
            $this->redirect('upgrade/getDatabaseInfo');
        }
    }
}