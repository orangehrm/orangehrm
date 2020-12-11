<?php

use function OpenApi\scan;
use OpenApi\Annotations as OA;

require(realpath(__DIR__ . "/../../symfony/lib/vendor/autoload.php"));
$pathToRestPlugin = realpath(__DIR__ . '/../../symfony/plugins/orangehrmRESTPlugin');
$pathToOpenApiDir = realpath($pathToRestPlugin . '/doc/openApi');
$pathToScanDir = realpath($pathToRestPlugin . '/modules');
$pathToOrangehrmV1Json = realpath($pathToOpenApiDir . '/orangehrm-v1.json');
$pathToOrangehrmV1MinJson = realpath($pathToOpenApiDir . '/orangehrm-v1-minified.json');

$openapi = scan($pathToScanDir);
file_put_contents($pathToOrangehrmV1Json, $openapi->toJson() . "\n");

array_push(OA\OpenApi::$_blacklist, 'servers', 'tags');
array_push(OA\Operation::$_blacklist, 'summary', 'operationId');
array_push(OA\Schema::$_blacklist, 'example');

$minifiedJson = json_decode($openapi->toJson(), true);
// Since OA\Components::$_blacklist `securitySchemes` not working
unset($minifiedJson['components']['securitySchemes']);

file_put_contents(
    $pathToOrangehrmV1MinJson,
    json_encode($minifiedJson, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n"
);

header('Content-Type: application/json');
echo file_get_contents($pathToOrangehrmV1Json);
