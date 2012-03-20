<?php

class executeConfChangeAction extends sfAction {
    
    private $selfConfigPath;
    private $remortConfigPath;
    public function preExecute() {
        $this->getUser()->setAttribute('currentScreen',3);
        $this->selfConfigPath = sfConfig::get('sf_root_dir')."/../symfony/apps/orangehrm/config/";
    }
    
    public function execute($request) {
        $this->form = new FolderInput();
        if ($request->isMethod('post')) {
            $this->form->bind($request->getParameter('folderPath'));
            if ($this->form->isValid())
            {
                $folderPath = $this->form->getValue('folder_path');
                $this->remortConfigPath = $folderPath."/symfony/apps/orangehrm/config/";
                $result = $this->copyConfigurationFiles();
                if ($result) {
                    $this->getUser()->setFlash('message', 'Successfully Copied Files', false);
                } else {
                    $this->getUser()->setFlash('message', 'Failed to Copy Files', false);
                }
            }
            
        }
    }
    
    private function copyConfigurationFiles() {
        $success = true;
        if (!copy($this->remortConfigPath."emailConfiguration.yml", $this->selfConfigPath."emailConfiguration.yml")) {
            $success = false;
        }
        
        if (!copy($this->remortConfigPath."parameters.yml", $this->selfConfigPath."parameters.yml")) {
            $success = false;
        }
        return $success;
    }
}