<?php

class OrangeActionHelper {

    private static $actionDescriptors;

    public static function getActionDescriptor($moduleName, $actionName, $upperCase = true) {
        self::init();

        if (isset(self::$actionDescriptors[$moduleName][$actionName])) {
            $actionDescriptor = self::$actionDescriptors[$moduleName][$actionName];
        } else {
            $actionDescriptor = ucfirst(preg_replace('/[A-Z]/', ' $0', $actionName));
        }

        if ($upperCase) {
            $actionDescriptor = strtoupper($actionDescriptor);
        }

        return $actionDescriptor;
    }

    protected static function init() {
        if (is_null(self::$actionDescriptors)) {
            self::$actionDescriptors = sfYaml::load(sfConfig::get('sf_apps_dir') . '/orangehrm/config/action_descriptions.yml');
        }
    }

}

