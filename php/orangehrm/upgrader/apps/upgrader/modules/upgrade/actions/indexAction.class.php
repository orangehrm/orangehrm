<?php

class indexAction extends sfAction {
    
    public function execute($request) {
        if ($request->isMethod('post')) {
            $submitBy = $request->getParameter('submitBy');
            switch ($submitBy) {
                case 'databaseInfo':
                    $this->redirect('upgrade/addVersionInfo');
                    break;
                case 'versionInfo':
                    $this->redirect('upgrade/executeDbChange');
                    break;
                case 'dbChange':
                    $this->redirect('upgrade/executeConfChange');
                    break;
            }
        } else {
            $this->redirect('upgrade/addDatabaseInfo');
        }
    }
}