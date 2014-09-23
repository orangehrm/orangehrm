<?php

class PluginExecutionManager {

    private static $instance;
    private $preExecuteMethodStack = array();
    private $postExecuteMethodStack = array();

    /**
     *
     * @return PluginExecutionManager
     */
    public static function instance() {
        if (!(self::$instance instanceof PluginExecutionManager)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPreExecuteMethodStack($module, $action) {
        if (array_key_exists($module, $this->preExecuteMethodStack)) {
            if (array_key_exists($action, $this->preExecuteMethodStack[$module])) {
                return $this->preExecuteMethodStack[$module][$action];
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public function getPostExecuteMethodStack($module, $action) {
        
        if (array_key_exists($module, $this->postExecuteMethodStack)) {
            if (array_key_exists($action, $this->postExecuteMethodStack[$module])) {
                
                return $this->postExecuteMethodStack[$module][$action];
            } else {
                return array();
            }
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
                $configuraitonPath = $pluginsPath . '/' . $pluginName . '/config/action_extensions.yml';

                if (is_file($configuraitonPath)) {
                    $configuraiton = sfYaml::load($configuraitonPath);

                    if (!is_array($configuraiton)) {
                        continue;
                    }

                    foreach ($configuraiton as $module => $extentionsForActions) {
                        if (!isset($this->preExecuteMethodStack[$module])) {
                            $this->preExecuteMethodStack[$module] = array();
                        }

                        if (!isset($this->postExecuteMethodStack[$module])) {
                            $this->postExecuteMethodStack[$module] = array();
                        }

                        foreach ($extentionsForActions as $action => $extentions) {
                            if (!isset($this->preExecuteMethodStack[$module][$action])) {
                                $this->preExecuteMethodStack[$module][$action] = array();
                            }
                            if (!isset($this->postExecuteMethodStack[$module][$action])) {
                                $this->postExecuteMethodStack[$module][$action] = array();
                            }
                            $this->preExecuteMethodStack[$module][$action] = array_merge($this->preExecuteMethodStack[$module][$action], $extentions['pre']);
                            $this->postExecuteMethodStack[$module][$action] = array_merge($this->postExecuteMethodStack[$module][$action], $extentions['post']);
                        }
                    }
                }
            }
        }
    }

}

