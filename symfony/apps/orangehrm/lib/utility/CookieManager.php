<?php

class CookieManager {

    /**
     *
     * @param string $name
     * @param string $value
     * @param int $expire
     * @param string $path 
     */
    public function setCookie($name, $value = null, $expire = null, $path = null) {
        if (version_compare(PHP_VERSION, '5.2.0') >= 0) { 
            // Specify httponly variable if using php >= 5.2.0
            setcookie($name, $value, $expire, $path, '', false, true);
        } else {
            setcookie($name, $value, $expire, $path);
        }
                
    }

    /**
     *
     * @param string $name
     * @param string $path 
     */
    public function destroyCookie($name, $path) {
        setcookie($name, null, time() - 3600, $path);
    }

    /**
     *
     * @param string $name
     * @param mixed $defaultValue
     * @return string 
     */
    public function readCookie($name, $defaultValue = null) {
        return (isset($_COOKIE[$name])) ? $_COOKIE[$name] : $defaultValue;
    }

    public function isCookieSet($name) {
        return isset($_COOKIE[$name]);
    }

}
