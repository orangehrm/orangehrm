<?php

class CookieManager {

    public function setCookie($name, $value = null, $expire = null, $path = null) {
        setcookie($name, $value, $expire, $path);
    }
    
    public function destroyCookie($name, $path) {
        setcookie($name, null, time() - 3600, $path);
    }

}
