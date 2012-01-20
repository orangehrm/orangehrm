<?php

class ParameterService {

    private static $paramArray;
    private static $paramFilePath;

    private static function _loadParamArray() {

        $path = sfConfig::get('sf_root_dir') . '/apps/orangehrm/config/parameters.yml';

        if (is_writable($path)) {
            self::$paramFilePath = $path;
        } else {
            throw new Exception("Parameter container is not writable");
        }

        self::$paramArray = sfYaml::load($path);

        if ( !is_array(self::$paramArray) ) {
            self::$paramArray = array();
        }

    }

    public static function getParameter($key, $default = null) {

        self::_loadParamArray();

        if (array_key_exists($key, self::$paramArray)) {
            return self::$paramArray[$key];
        } else {
            return $default;
        }

    }

    public static function setParameter($key, $value) {

        self::_loadParamArray();

        if (array_key_exists($key, self::$paramArray)) {
            self::$paramArray[$key] = $value;
            file_put_contents(self::$paramFilePath, sfYaml::dump(self::$paramArray));
        }

    }


}