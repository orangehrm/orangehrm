<?php

use OrangeHRM\Framework\Framework;
use OrangeHRM\Framework\RouteManager;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

require realpath(__DIR__ . '/../vendor/autoload.php');

$env = $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? 'prod';
$debug = (bool)($_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? ('prod' !== $env));

if ($debug) {
    umask(0000);
    Debug::enable();
}

$request = Request::createFromGlobals();

$requestStack = new RequestStack();
$context = new RequestContext();
$context->fromRequest($request);
$matcher = new UrlMatcher(RouteManager::getRoutes(), $context);

$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new RouterListener($matcher, $requestStack));
$controllerResolver = new ControllerResolver();
$argumentResolver = new ArgumentResolver();

$kernel = new Framework($dispatcher, $controllerResolver, $requestStack, $argumentResolver);
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
