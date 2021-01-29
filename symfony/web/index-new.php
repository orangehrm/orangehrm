<?php

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

require realpath(__DIR__ . '/../vendor/autoload.php');
require_once realpath(__DIR__ . '/../lib/Framework.php');

$routes = new RouteCollection();
$routes->add(
    'api_public_api_definition',
    new Route('/api/v1/api-definition', ['_controller' => 'Orangehrm\Rest\Modules\apiv1public\actions\ApiDefinitionApiAction::execute'])
);

$request = Request::createFromGlobals();

$requestStack = new RequestStack();
$context = new RequestContext();
$context->fromRequest($request);
$matcher = new UrlMatcher($routes, $context);

$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new RouterListener($matcher, $requestStack));
$controllerResolver = new ControllerResolver();
$argumentResolver = new ArgumentResolver();

$kernel = new Framework($dispatcher, $controllerResolver, $requestStack, $argumentResolver);
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
