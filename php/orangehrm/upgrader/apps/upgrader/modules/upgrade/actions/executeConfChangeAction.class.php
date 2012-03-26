<?php

class executeConfChangeAction extends sfAction {
    
    private $selfConfigPath;
    private $remortConfigPath;
    public function preExecute() {
        $this->getUser()->setAttribute('currentScreen',3);
        $this->applicationRootPath = sfConfig::get('sf_root_dir')."/..";
    }
    
    public function execute($request) {
        $this->form = new ConfigureFile();
        if ($request->isMethod('post')) {
            $this->form->bind($request->getParameter('configureFile'));
            if ($this->form->isValid()) {
                $upgraderUtility = new UpgradeUtility();
                $dbInfo = $this->getUser()->getAttribute('dbInfo');
                $host = $dbInfo['host'];
                $username = $dbInfo['username'];
                $password = $dbInfo['password'];
                $port = $dbInfo['port'];
                $database = $dbInfo['database'];
                
                $upgraderUtility->setApplicationRootPath($this->applicationRootPath);
                $result[] = $upgraderUtility->writeConfFile($host, $port, $database, $username, $password);
                $result[] = $upgraderUtility->writeSymfonyDbConfigFile($host, $port, $database, $username, $password);
                $success = true;
                foreach ($result as $res) {
                    if (!$res) {
                        $success = false;
                        break;
                    }
                }
                if ($success) {
                    $this->getRequest()->setParameter('submitBy', 'confFile');
                    $this->forward('upgrade','index');
                }
            }
        }
    }
}