<?php
/**
 * This example shows how to create a basic provider usin HTTP Authentication.
 * This is only an example. You shouldn't use it as-is in your code.
 */
require 'provider.php';

class BasicProvider extends LightOpenIDProvider
{
    public $select_id = true;
    public $login = '';
    public $password = '';
    
    function __construct()
    {
        parent::__construct();
        
        # If we use select_id, we must disable it for identity pages,
        # so that an RP can discover it and get proper data (i.e. without select_id)
        if(isset($_GET['id'])) {
            $this->select_id = false;
        }
    }
    
    function setup($identity, $realm, $assoc_handle, $attributes)
    {
        header('WWW-Authenticate: Basic realm="' . $this->data['openid_realm'] . '"');
        header('HTTP/1.0 401 Unauthorized');
    }
    
    function checkid($realm, &$attributes)
    {
        if(!isset($_SERVER['PHP_AUTH_USER'])) {
            return false;
        }
        
        if ($_SERVER['PHP_AUTH_USER'] == $this->login
            && $_SERVER['PHP_AUTH_PW'] == $this->password
        ) {
            # Returning identity
            # It can be any url that leads here, or to any other place that hosts
            # an XRDS document pointing here.
            return $this->serverLocation . '?id=' . $this->login;
        }
        
        return false;
    }
    
}
$op = new BasicProvider;
$op->login = 'test';
$op->password = 'test';
$op->server();
