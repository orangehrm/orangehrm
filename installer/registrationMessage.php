<?php

$cupath = realpath(dirname(__FILE__) . '/../');
define('ROOT_PATH', $cupath);
require(ROOT_PATH .'/lib/confs/Conf.php');

global $dbConnection;


$url = "https://opensource-updates.orangehrm.com/app.php/register";
$data = http_build_query(array(
    'serverAddr' => array_key_exists('SERVER_ADDR', $_SERVER) ? urlencode($_SERVER['SERVER_ADDR']) : urlencode($_SERVER['LOCAL_ADDR']),
    'host' => urlencode(php_uname("s") . " " . php_uname("r")),
    'httphost' => urlencode($_SERVER['HTTP_HOST']),
    'phpVersion' => urlencode(constant('PHP_VERSION')),
    'server' => urlencode($_SERVER['SERVER_SOFTWARE']),
    'ohrmVersion' => urlencode('Open Source 3.3'),
        ));


$contextOpts = array(
    'ssl' => array(
        'verify_peer' => false,
        'allow_self_signed' => true,
        'cafile' => '/etc/ssl/certs/cacert.pem',
        'capath' => '/etc/ssl/certs',
        'verify_depth' => 20,
        'CN_match' => '*.orangehrm.com',
        'disable_compression' => true,
        'SNI_enabled' => true,
        'ciphers' => 'ALL!EXPORT!EXPORT40!EXPORT56!aNULL!LOW!RC4'
    ),
    'http' => array(
        'method' => 'POST',
        'header' => 'Content-type: application/x-www-form-urlencoded',
        'content' => $data
    )
);
//var_dump($data);
$sslContext = stream_context_create($contextOpts);
$result = file_get_contents($url, null, $sslContext);
//var_dump($result);
$headers = $http_response_header;
//var_dump($headers);
//        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//        curl_close($ch);
if (strpos($headers[0], '200 OK') !== false && strpos($result, 'SUCCESSFUL') !== false) {

    $resultParams = json_decode($result, true);

    if (isset($resultParams['uuid'])) {
        $uuid = base64_encode(urldecode($resultParams['uuid'])); //save uuid
        //set beacon activation to true
    }
    saveBeaconData($uuid);
}

function saveBeaconData($uuid = null) {
    
    $conf = new Conf();
    global $dbConnection;
//db credentials
    $dbInfo['host'] = $conf->dbhost;
    $dbInfo['username'] = $conf->dbuser;
    $dbInfo['password'] = $conf->dbpass;
    $dbInfo['database'] = $conf->dbname;
    $dbInfo['port'] = $conf->dbport;
    $dbConnection = createDbConnection($dbInfo['host'], $dbInfo['username'], $dbInfo['password'], $dbInfo['database'], $dbInfo['port']);


    executeSql("UPDATE `hs_hr_config` SET `value` = '$uuid' WHERE `key` = 'beacon.uuid'");

    executeSql("UPDATE `hs_hr_config` SET `value` = 'on' WHERE `key` = 'beacon.activiation_status'");

    mysqli_close($dbConnection);
    return true;
}

function createDbConnection($host, $username, $password, $dbname, $port) {
    if (!$port) {
        $dbConnection = mysqli_connect($host, $username, $password, $dbname);
    } else {
        $dbConnection = mysqli_connect($host, $username, $password, $dbname, $port);
    }

    if (!$dbConnection) {
       return null;// die('Could not connect: ' . mysqli_connect_error());
    }
    $dbConnection->set_charset("utf8");
    //mysqli_autocommit($dbConnection, FALSE);
    return $dbConnection;
}

function executeSql($query) {
    global $dbConnection;
    //echo ".";
    $result = mysqli_query($dbConnection, $query);
    if (mysqli_error($dbConnection)) {
       // echo "\n" . $query . "::" . mysqli_error($dbConnection) . "\n";
    }
    return $result;
}
