<?php

class PluginConfigurationManager {

    private static $instance;
    private $externalConfigurations = array();

    /**
     *
     * @return PluginConfigurationManager
     */
    public static function instance() {
        if (!(self::$instance instanceof PluginConfigurationManager)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getExternalConfigurations($index = null) {
        if (empty($index)) {
            return $this->externalConfigurations;
        } elseif (array_key_exists($index, $this->externalConfigurations)) {
            return $this->externalConfigurations[$index];
        } else {
            return array();
        }
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
                $configuraitonPath = $pluginsPath . '/' . $pluginName . '/config/external_configurations.yml';
                
                if (is_file($configuraitonPath)) {
                    $configuraiton = sfYaml::load($configuraitonPath);
                    
                    if (!is_array($configuraiton)) {
                        continue;
                    }
                    
                    foreach ($configuraiton as $component => $configuraitonForComponent) {
                        if (!isset($this->externalConfigurations[$component])) {
                            $this->externalConfigurations[$component] = array();
                        }
                        
                        foreach ($configuraitonForComponent as $property => $value) {
                            if (!isset($this->externalConfigurations[$component][$property])) {
                                $this->externalConfigurations[$component][$property] = array();
                            }
                            
                            if (is_array($value)) {
                                foreach ($value as $k => $v) {
                                    $this->externalConfigurations[$component][$property]["{$pluginName}_{$k}"] = $v;
                                }
                            } else {
                                $this->externalConfigurations[$component][$property][] = $value;
                            }
                        }
                    }
                }
            }
        }
    }

}

