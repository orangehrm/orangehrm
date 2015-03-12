<?php

class WSManager extends baseWSUtility {

    protected $serviceWrapperFactory;
    protected $pluginsDirectoryPath;
    protected static $configurations = array();

    /**
     *
     * @return WSWrapperFactory
     */
    public function getWSWrapperFactory() {
        if (!($this->serviceWrapperFactory instanceof WSWrapperFactory)) {
            $this->serviceWrapperFactory = new WSWrapperFactory();
        }
        return $this->serviceWrapperFactory;
    }

    /**
     *
     * @param WSWrapperFactory $serviceWraperFactory 
     */
    public function setWSWrapperFactory(WSWrapperFactory $serviceWraperFactory) {
        $this->serviceWrapperFactory = $serviceWraperFactory;
    }

    /**
     *
     * @param string methodName 
     * @return bool
     */
    public function isMethodAvailable($methodName) {

        return array_key_exists($methodName, $this->getConfiguration('methods'));
    }

    /**
     *
     * @param WSRequestParameters $paramObj 
     * @return bool
     */
    public function isAuthenticated(WSRequestParameters $paramObj) {
        $enabledModules = sfConfig::get('sf_enabled_modules'); 
        return in_array('oauth', $enabledModules);
    }

    /**
     *
     * @param WSRequestParameters $paramObj 
     * @return bool
     */
    public function isAuthorized(WSRequestParameters $paramObj) {
        $enabledModules = sfConfig::get('sf_enabled_modules'); 
        return in_array('oauth', $enabledModules);
    }

    /**
     *
     * @param WSRequestParameters $paramObj 
     * @return mixed
     */
    public function callMethod(WSRequestParameters $paramObj) {
        $methodName = $paramObj->getMethod();

        $methodConfigurations = $this->getConfiguration('methods', $methodName);

        $serviceWrapperClassName = $methodConfigurations['wrapper'];

        $serviceWrapperInstance = new $serviceWrapperClassName();
        return call_user_func_array(array($serviceWrapperInstance, $methodName), $paramObj->getParameters());
    }

    /**
     *
     * @return string
     */
    public function getPluginsDirectoryPath() {
        if (empty($this->pluginsDirectoryPath)) {
            $this->setPluginsDirectoryPath(sfConfig::get('sf_plugins_dir'));
        }
        return $this->pluginsDirectoryPath;
    }

    /**
     *
     * @param string $pluginsDirectoryPath 
     */
    public function setPluginsDirectoryPath($pluginsDirectoryPath) {
        $this->pluginsDirectoryPath = $pluginsDirectoryPath;
    }

    /**
     * @return array
     */
    public function getConfigurations() {
        if (!empty(self::$configurations) && is_array(self::$configurations)) {
            return self::$configurations;
        }

        $pluginsPath = $this->getPluginsDirectoryPath();

        $directoryIterator = new DirectoryIterator($pluginsPath);

        self::$configurations = array();

        foreach ($directoryIterator as $fileInfo) {
            if ($fileInfo->isDir()) {
                $pluginName = $fileInfo->getFilename();
                $configuraitonPath = $pluginsPath . '/' . $pluginName . '/config/ws_config.yml';
                if (is_file($configuraitonPath)) {
                    $configuraiton = sfYaml::load($configuraitonPath);

                    if (!is_array($configuraiton)) {
                        continue;
                    }

                    foreach ($configuraiton as $configName => $configValue) {
                        if (array_key_exists($configName, self::$configurations)) {
                            self::$configurations[$configName] = self::$configurations[$configName] + $configValue;
                        } else {
                            self::$configurations[$configName] = $configValue;
                        }
                    }
                }
            }
        }

        return self::$configurations;
    }

    /**
     *
     * @param string $configurationName
     * @param string $configurationKey
     * @return mixed
     * @throws WebServiceException 
     */
    protected final function getConfiguration($configurationName, $configurationKey = null) {
        $configuration = $this->getConfigurations();

        if (array_key_exists($configurationName, $configuration)) {
            if (!is_null($configurationKey) && array_key_exists($configurationKey, $configuration[$configurationName])) {
                return $configuration[$configurationName][$configurationKey];
            } else {
                return $configuration[$configurationName];
            }
        } else {
            throw new WebServiceException('Invalid configurations');
        }
    }

}
