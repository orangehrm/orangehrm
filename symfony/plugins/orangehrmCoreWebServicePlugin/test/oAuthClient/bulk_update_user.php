<?php

ini_set('display_erros', 1);
error_reporting(E_ALL);
require_once __DIR__ . "/lib/ohrmWebServiceClient.php";
session_start();

$baseURL = 'http://localhost/ent412Trunk/symfony/web/';

// 
// Create client. 
// 
$client = new ohrmWebServiceClient($baseURL);

//
// You should first add an oAuth client with the given credentials using
// the url: /admin/registerOAuthClient
// 
$client->setCredentials("demoapp", "demopass");

// Use token if available
if (isset($_SESSION['access_token'])) {
	$client->setToken($_SESSION['access_token']);
}

$methodUrl = '/bulkUpdateUsers';
$users = array(array('userName' => 'ashley14', 'passWord' => 'ssss', 'type' => 2, 'userId' => 47),
        array('userName' => 'ashley1478', 'passWord' => 'ssss', 'type' => 2, 'userId' => 50));
$parameters = array('users' => $users);

$results = $client->callMethod($methodUrl, 'POST', $parameters);
var_dump($results);
