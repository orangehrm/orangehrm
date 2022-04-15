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

$methodUrl = '/addEmployee';
$employee = array('personalDetails' => array('firstName' => 'Adam', 'lastName' => 'Smith'),
        'contactDetails' => array('street1' => 'Street 1', 'street2' => 'Street 2'), 
        'jobDetails' => array('location_name' => 'AU-A', 'effective_date' => '2010-01-12', 'job_title' => 'Software Engineer', 'contract_start_date' => NULL),
        'customFields' => array(array('fieldName' => 'Mage Nama', 'value' => 'Test Nama')),
        'reportTo' => array(array('supervisorEmpNumber' => 180, 'reportingMethod' => 'Direct', 'relationship' => 1))
    );
$parameters = array('employee' => $employee);

$results = $client->callMethod($methodUrl, 'POST', $parameters);
var_dump($results);