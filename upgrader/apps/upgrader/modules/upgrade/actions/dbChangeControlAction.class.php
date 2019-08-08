<?php

class dbChangeControlAction extends sfAction {
    
    public function execute($request) {
        $dbInfo = $this->getUser()->getAttribute('dbInfo');
        $taskNo = $request->getParameter('task');
        
        UpgradeLogger::writeLogMessage('Task:' . $taskNo);
        
        $currentTask = "SchemaIncrementTask$taskNo";
        
        UpgradeLogger::writeLogMessage("Running task class: $currentTask");        
        $task = new $currentTask($dbInfo);
        $installedAddons = $this->getInstalledPlugins();
        $pluginsDir = ROOT_PATH . '/symfony/plugins';
        $classes = get_declared_classes();
        foreach (array_column($installedAddons, 'plugin_name') as $installedPlugin) {
            if (is_file("$pluginsDir/$installedPlugin/upgrader/SchemaIncrementTask/$taskNo.php")) {
                require "$pluginsDir/$installedPlugin/upgrader/SchemaIncrementTask/$taskNo.php";
            }
        }
        $tasks = [$task];
        foreach (array_diff(get_declared_classes(), $classes) as $taskClass) {
            $tasks[] = new $taskClass($dbInfo);
        }

        try {
            foreach ($tasks as $task) {
                $task->execute();
            }
        } catch (Exception $e) {
            UpgradeLogger::writeErrorMessage("Error when running task: " . $e->getMessage() . 
                    ', stacktrace = ' . $e->getTraceAsString());
        }
        $progress = array_sum(array_map(function($task) {
            return $task->getProgress();
        }, $tasks)) / count($tasks);
        $arr = array('progress' => $progress);

        echo json_encode($arr);
    }

    protected function getInstalledPlugins() {
        $dbInfo = $this->getUser()->getAttribute('dbInfo');
        $upgradeUtility = new UpgradeUtility();
        $upgradeUtility->getDbConnection(
            $dbInfo['host'],
            $dbInfo['username'],
            $dbInfo['password'],
            $dbInfo['database'],
            $dbInfo['port']
        );
        return $upgradeUtility->getInstalledAddons();
    }
}
