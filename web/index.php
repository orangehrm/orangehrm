<?php

use OrangeHRM\Framework\Framework;
use OrangeHRM\Framework\Http\Request;
use Symfony\Component\ErrorHandler\Debug;

require realpath(__DIR__ . '/../symfony/vendor/autoload.php');

$env = $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = 'prod';
$debug = (bool)($_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? $_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] = ('prod' !== $env));

if ($debug) {
    umask(0000);
    Debug::enable();
}

$kernel = new Framework($env, $debug);
$request = Request::createFromGlobals();
$response = $kernel->handleRequest($request);
$response->send();
$kernel->terminate($request, $response);
