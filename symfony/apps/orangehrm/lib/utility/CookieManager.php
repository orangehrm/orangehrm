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
        setcookie($name, $value, $expire, $path);
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
