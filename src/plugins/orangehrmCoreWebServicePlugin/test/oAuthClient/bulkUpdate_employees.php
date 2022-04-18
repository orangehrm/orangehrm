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

$methodUrl = '/bulkUpdateEmployees';
$employees = array(array('empNumber' => 166, 'customFields' => array(array('fieldName' => 'Mage Nama', 'value' => 'ssss')),

    ), array('empNumber' => 167, 'customFields' => array(array('fieldName' => 'Mage Nama', 'value' => 'oooo')),

    ));
$parameters = array('employees' => $employees);

$results = $client->callMethod($methodUrl, 'POST', $parameters);
var_dump($results);