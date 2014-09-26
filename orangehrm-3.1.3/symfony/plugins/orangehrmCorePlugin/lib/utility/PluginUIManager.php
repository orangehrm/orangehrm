<?php

class PluginUIManager {

    private static $instance;
    private $uiSubComponents = array();

    /**
     *
     * @return PluginUIManager
     */
    public static function instance() {
        if (!(self::$instance instanceof PluginUIManager)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getUISubComponents($module, $action) {

        if (array_key_exists($module, $this->uiSubComponents)) {
            if (array_key_exists($action, $this->uiSubComponents[$module])) {
                return $this->uiSubComponents[$module][$action];
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
                $configuraitonPath = $pluginsPath . '/' . $pluginName . '/config/ui_extensions.yml';

                if (is_file($configuraitonPath)) {
                    $configuraiton = sfYaml::load($configuraitonPath);

                    if (!is_array($configuraiton)) {
                        continue;
                    }

                    foreach ($configuraiton as $module => $uiExtentionsForActions) {

                        if (!isset($this->uiSubComponents[$module])) {
                            $this->uiSubComponents[$module] = array();
                        }

                        foreach ($uiExtentionsForActions as $action => $extensions) {
                            if (!isset($this->uiSubComponents[$module][$action])) {
                                $this->uiSubComponents[$module][$action] = array();
                            }

                            foreach ($extensions as $location => $subComponents) {
                                if (isset($this->uiSubComponents[$module][$action][$location])) {
                                    $this->uiSubComponents[$module][$action][$location] = array_merge($this->uiSubComponents[$module][$action][$location], $subComponents);
                                } else {
                                    $this->uiSubComponents[$module][$action][$location] = $subComponents;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

}

