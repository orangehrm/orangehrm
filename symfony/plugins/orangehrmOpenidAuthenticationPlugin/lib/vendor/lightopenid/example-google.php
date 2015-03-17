<?php
# Logging in with Google accounts requires setting special identity,
# so this example shows how to do it.
require 'openid.php';

try {
    # Change 'example.org' to your domain name.
    $domain = 'localhost';
    $openid = new LightOpenID($domain);
    
    if (!$openid->mode) {
        if (isset($_GET['login'])) {
            $openid->identity = 'https://www.google.com/accounts/o8/id';
            $openid->required = array('contact/email');
            header('Location: ' . $openid->authUrl());
        }
 
    } elseif($openid->mode == 'cancel') {
        echo 'User has canceled authentication!';
    } else {
        echo 'User ' . ($openid->validate() ? $openid->identity . ' has ' : 'has not ') . 'logged in.';
        print_r($openid->getAttributes());
    }
} catch(ErrorException $e) {
    echo $e->getMessage();
}
