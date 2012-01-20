<?php

class PluginQueryExtensionManager {

    private static $instance;
    private $queryExtensions = array();

    /**
     *
     * @return PluginQueryExtensionManager
     */
    public static function instance() {
        if (!(self::$instance instanceof PluginQueryExtensionManager)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     *
     * @param string $serviceName
     * @param string $methodName
     * @return array 
     */
    public function getQueryExtensions($serviceName = null, $methodName = null) {
        if (empty($serviceName)) {
            return $this->queryExtensions;
        } else {
            if (array_key_exists($serviceName, $this->queryExtensions)) {
                if (empty($methodName)) {
                    return $this->queryExtensions[$serviceName];
                } else {
                    if (array_key_exists($methodName, $this->queryExtensions[$serviceName])) {
                        return $this->queryExtensions[$serviceName][$methodName];
                    } else {
                        return array();
                    }
                }
            } else {
                return array();
            }
        }
    }
    
    /**
     *
     * @param array $queryExtensions 
     */
    public function setQueryExtensions(array $queryExtensions) {
        $this->queryExtensions = $queryExtensions;
    }

    private function __construct() {
        $this->_init();
    }

    private function _init() {

        $pluginsPath = sfConfig::get('sf_plugins_dir');
        $directoryIterator = new DirectoryIterator($pluginsPath);
        foreach ($directoryIterator as $fileInfo) {
            if ($fileInfo->isDir()) {
                
                $pluginName = $fileInfo->getFilename();
                $configuraitonPath = $pluginsPath . '/' . $pluginName . '/config/query_extensions.yml';
                
                if (is_file($configuraitonPath)) {
                    $configuraiton = sfYaml::load($configuraitonPath);
                    
                    if (!is_array($configuraiton)) {
                        continue;
                    }
                    
                    foreach ($configuraiton as $component => $configuraitonForComponent) {
                        if (!isset($this->queryExtensions[$component])) {
                            $this->queryExtensions[$component] = array();
                        }
                        
                        foreach ($configuraitonForComponent as $property => $value) {
                            if (!isset($this->queryExtensions[$component][$property])) {
                                $this->queryExtensions[$component][$property] = array();
                            }
                            
                            if (is_array($value)) {
                                foreach ($value as $k => $v) {
                                    if (isset($this->queryExtensions[$component][$property][$k])) {
                                        $this->queryExtensions[$component][$property][$k] = array_merge($this->queryExtensions[$component][$property][$k], $v);
                                    } else {
                                        $this->queryExtensions[$component][$property][$k] = $v;
                                    }
                                }
                            } else {
                                $this->queryExtensions[$component][$property][] = $value;
                            }
                        }
                    }
                }
            }
        }
    }

}

