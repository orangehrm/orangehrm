<?php

class executeCompleteAction extends sfAction {
    
    public function preExecute() {
        $this->getUser()->setAttribute('currentScreen', 'completion');
    }

    public function execute($request) {
        $upgradeUtility     = new UpgradeUtility();
        $this->newVersion   = $upgradeUtility->getNewVersion();
        $currentUri         = $this->getRequest()->getUri();
        if ($this->getUser()->getAttribute('hasPlugins')) {
            $commands = [
                'php symfony cc',
                'php symfony doctrine:build-model'
            ];
            if ($this->getUser()->getAttribute('includesThemePlugin')) {
                $dbInfo = $this->getUser()->getAttribute('dbInfo');
                $upgradeUtility->getDbConnection(
                    $dbInfo['host'],
                    $dbInfo['username'],
                    $dbInfo['password'],
                    $dbInfo['database'],
                    $dbInfo['port']
                );
                $query = 'select `value` from hs_hr_config where `key` = "themeName"';
                $result = $upgradeUtility->fetchArray($upgradeUtility->executeSql($query));
                if ($result['value'] != 'default') {
                    $commands[] = 'php symfony orangehrm:publish-themes';
                }
            }
            $commands[] = 'php symfony orangehrm:publish-assets';
            chdir(ROOT_PATH . DIRECTORY_SEPARATOR . 'symfony');
            foreach ($commands as $command) {
                exec($command, $output, $status);
                if ($status) {
                    UpgradeLogger::writeErrorMessage("An error occurred while executing '$command'. 
                    command output: " . implode("\n", $output));
                }
            }
        }
        $this->mainAppUrl   = str_replace("/upgrader/web/index.php/upgrade/executeComplete", "", $currentUri);
        $_SESSION['Installation'] = "You have successfully upgraded OrangeHRM";
    }
}