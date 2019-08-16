<?php

class orangehrmUpgradePluginTask extends sfBaseTask
{
    protected function configure()
    {
        $this->namespace = 'upgrader';
        $this->name = 'upgrade-plugin';
        $this->briefDescription = 'upgrades a given plugin to latest version.';
        $this->addOptions([
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'upgrader'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),]);
        $this->addArgument('plugin', sfCommandArgument::REQUIRED, 'plugin name');
        $this->addArgument('previousVersion', sfCommandArgument::REQUIRED, 'previous version of the plugin');
    }

    /**
     * @inheritDoc
     */
    protected function execute($arguments = array(), $options = array())
    {
        $pluginName = $arguments['plugin'];
        $previousVersion = $arguments['previousVersion'];
        $pluginsDir = sfConfig::get('sf_root_dir') . '/../symfony/plugins';
        $data = require "$pluginsDir/$pluginName/upgrader/index.php";
        if (!array_key_exists($previousVersion, $data)) {
            $this->log('nothing to upgrade');
            return;
        }
        $startNo = $data[$previousVersion];
        $files = scandir("$pluginsDir/$pluginName/upgrader/SchemaIncrementTask");
        $classes = get_declared_classes();
        foreach ($files as $fileName) {
            if (preg_match('/(\d+)\.php/', $fileName, $matches) && $matches[1] >= $startNo) {
                require "$pluginsDir/$pluginName/upgrader/SchemaIncrementTask/$fileName";
            }
        }
        $taskClasses = array_diff(array_diff(get_declared_classes(), $classes), ['SchemaIncrementTask']);
        $databasesYml = sfConfig::get('sf_root_dir') . '/../symfony/config/databases.yml';
        $ymlContent = sfYaml::load($databasesYml);
        $params = $ymlContent['all']['doctrine']['param'];
        $port = 3306;
        $host = null;
        $dbname = null;
        foreach (explode(';', str_replace('mysql:', '', $params['dsn'])) as $part) {
            list($key, $value) = explode('=', $part);
            $$key = $value;
        }
        $dbInfo = [
           'host' => $host,
           'port' => $port,
           'username' => $params['username'],
           'password' => $params['password'],
           'database' => $dbname,
        ];

        try {
            foreach ($taskClasses as $taskClass) {
                $task = new $taskClass($dbInfo);
                $task->execute();
                if ($task->getProgress() !== 100) {
                    throw new sfCommandException("Error while upgrading.");
                }
            }
        } catch (Exception $e) {
            throw new sfCommandException("Error while upgrading.", $e->getCode(), $e);
        }
        $upgradeUtility = new UpgradeUtility();
        $upgradeUtility->getDbConnection($host, $params['username'], $params['password'], $dbname, $port);
        $upgradeUtility->dropUpgradeStatusTable();
    }
}