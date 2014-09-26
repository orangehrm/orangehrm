<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdvanceLeaveDBCreator
 *
 * @author poojitha
 */
class PluginInstaller {

    protected $installData;
    protected $task;
    
    
    public function __construct($task) {
        $this->task = $task;
    }

    /**
     *
     * @param type $pluginName
     * @return type null
     */
    protected function readInstallerData($pluginName) {
        
        $pluginDir = sfConfig::get('sf_plugins_dir') . DIRECTORY_SEPARATOR . $pluginName;
        if (!is_dir($pluginDir)) {
            throw new sfCommandException("Plugin $pluginName is not available under the plugins directory");
        }
        
        $installerYmlFile = $pluginDir . DIRECTORY_SEPARATOR . "install" . DIRECTORY_SEPARATOR . "installer.yml";
        if (!is_file($installerYmlFile)) {
            throw new sfCommandException("Plugin $installerYmlFile is not available");            
        }
        
        if (!is_readable($installerYmlFile)) {
            throw new sfCommandException("Plugin $installerYmlFile is not readable");            
        }
        try {
            $this->log('Reading ' . $installerYmlFile);
            $this->installData = sfYaml::load($installerYmlFile);
        } catch (Exception $e) {
            throw new sfCommandException('Error loading plugin installer.yml file. ' . $e->getMessage());
        }

    }
    /**
     * Build SQL Tables
     * 
     * @return type null
     */
    protected function buildDataTables() {
        try {

            $sqlPaths = $this->installData['dbscript_path'];
            
            if (!is_array($sqlPaths)) {
                $sqlPaths = array($sqlPaths);
            }
            
            $sqlString = '';
            
            foreach ($sqlPaths as $sqlPath) {
                $sqlString = $sqlString . file_get_contents(sfConfig::get('sf_plugins_dir') . DIRECTORY_SEPARATOR . $this->installData['plugin_name'] . DIRECTORY_SEPARATOR . $sqlPath);
            }
            
            if (!empty($sqlString)) {
                $q = Doctrine_Manager::getInstance()->getCurrentConnection();
                $patterns = array();
                $patterns[0] = '/DELIMITER \\$\\$/';
                $patterns[1] = '/DELIMITER ;/';
                $patterns[2] = '/\\$\\$/';

                $new_sql_string = preg_replace($patterns, '', $sqlString);
                $result = $q->exec($new_sql_string);


                foreach ($sqlPaths as $value) {
                    $this->log('Execute ' . $value . " file");
                }
            }
        } catch (Exception $e) {
            throw new sfCommandException($e->getMessage());
        }
    }
    
    /**
     * do symfoony tasks
     * @param $skipCommonTasks Skip common tasks like symfony cc, orangehrm:publish-assets
     * orangehrm:build-model. 
     * 
     */
    protected function doSymfonyTaks($skipCommonTasks = false) {

        $commands_list = $this->installData['symfony_commands'];

        foreach ($commands_list as $key => $val) {
            exec($val);
            $this->log('Execute ' . $val);
        }
    }
    
    public function installPlugin($pluginName, $skipCommonTasks = false) {
        $this->readInstallerData($pluginName);
        $this->buildDataTables();
        $this->doSymfonyTaks($skipCommonTasks);      
        $version = $this->getPluginVersion($pluginName);
        $this->savePluginVersion($pluginName, $version);
    }
    
    protected function log($message, $size = null, $style = 'INFO') {
        $this->task->logSection('orangehrm', $message, $size, $style);
    }
    
    protected function getPluginVersion($pluginName) {
        $version = null;
        
        $pluginDir = sfConfig::get('sf_plugins_dir') . DIRECTORY_SEPARATOR . $pluginName;
        if (!is_dir($pluginDir)) {
            throw new sfCommandException("Plugin $pluginName is not available under the plugins directory");
        }
        
        $appYmlFile = $pluginDir . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "app.yml";
        
        if (is_file($appYmlFile) && is_readable($appYmlFile)) {

            try {
                $this->log('Reading ' . $appYmlFile);
                $appYml = sfYaml::load($appYmlFile);
                if (isset($appYml['all'][$pluginName]['version'])) {
                    $version = $appYml['all'][$pluginName]['version'];
                }
            } catch (Exception $e) {
                throw new sfCommandException('Error loading plugin installer.yml file. ' . $e->getMessage());
                $message = "Error loading plugin $appYmlFile file. " . $e->getMessage();
                $this->log($message, null, 'ERROR');
            }
        } else {
            $this->log($appYmlFile . ' not found. Plugin version number cannot be determined', null, 'ERROR');
        }
        return $version;
    }
    
    protected function savePluginVersion($pluginName, $version) {
        
        $sql = "INSERT INTO ohrm_plugin(name, version) VALUES(:name, :version) "
               . " ON DUPLICATE KEY UPDATE version = :version";
        $pdo = Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh();
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute(array(':name' => $pluginName, ':version' => $version));
        if (!$result) {
            $this->log("Failed to update plugin version for $pluginName plugin", null, 'ERROR');
        }
    }
    
}