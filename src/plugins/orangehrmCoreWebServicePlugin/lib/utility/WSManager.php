<?php

class WSManager extends baseWSUtility {

    protected $serviceWrapperFactory;
    protected $pluginsDirectoryPath;
    protected static $configurations = array();

    /**
     *  Reset the configuration for test purposes. This is needed for testing
     */
    public static function resetConfiguration() {
        self::$configurations = array();
    }

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
    public function isMethodAvailable($methodName, $requestType = 'GET') {
        $methods = $this->getConfiguration('methods');

        if (isset($methods[$methodName])) {
            $type = $methods[$methodName]['type'];
            if (!is_array($type)) {
                $type = array($type);
            }
            if (in_array($requestType, $type)) {
                return true;
            }
        }

        return false;
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
        $methodParameters = $paramObj->getParameters();

        $methodConfiguration = $this->getConfiguration('methods', $methodName);

        $serviceWrapperClassName = $methodConfiguration['wrapper'];

        if ($paramObj->getWrapperObject()) {
            $serviceWrapperInstance = $paramObj->getWrapperObject();
        } else {
            $serviceWrapperInstance = new $serviceWrapperClassName();
        }

        // If web service config mentions parameters, only get them in that order
        // 
        if (isset($methodConfiguration['params'])) {
            $paramNames = $methodConfiguration['params'];
            $expectedParams = array();
            $missingParameters = array();

            foreach ($paramNames as $paramName) {
                if (isset($methodParameters[$paramName])) {
                    $expectedParams[$paramName] = $methodParameters[$paramName]; 
                } else {
                    $missingParameters[] = $paramName;
                }                
            }

            if (count($missingParameters) > 0) {
                throw new WebServiceException("Parameters missing: " . implode(',', $missingParameters), 400);
            }
        } else {
            $expectedParams = $methodParameters;
        }
        
        return call_user_func_array(array($serviceWrapperInstance, $methodName), $expectedParams);
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
            if (is_null($configurationKey)) {
                return $configuration[$configurationName];
            }
            if (array_key_exists($configurationKey, $configuration[$configurationName])) {
                return $configuration[$configurationName][$configurationKey];
            } else {
                throw new WebServiceException('Invalid configuration key');
            }
        } else {
            throw new WebServiceException('Invalid configurations');
        }
    }

}
