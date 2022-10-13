<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Installer\Framework\HttpKernel;
use Symfony\Component\ErrorHandler\Debug;

function trimVersion($currentVersion, $points)
{
    $pattern = '/^(\d+)' . str_repeat('\.(\d+)', $points) . '/';
    preg_match($pattern, $currentVersion, $matches);
    return $matches[0];
}

function isInSupportedPHPRange()
{
    $systemRequirements = require realpath(__DIR__ . '/config/system_requirements.php');
    $max = $systemRequirements['phpversion']['max'];
    $min = $systemRequirements['phpversion']['min'];
    $currentVersion = phpversion();

    $message = "PHP version should be higher than `$min` and lower than `$max`, detected version is `$currentVersion`.";

    if (!(version_compare(trimVersion($currentVersion, substr_count($min, '.')), $min, '>=') &&
        version_compare(trimVersion($currentVersion, substr_count($max, '.')), $max, '<='))) {
        die($message);
    }

    if (in_array($currentVersion, $systemRequirements['phpversion']['excludeRange'])) {
        die($message);
    }
}

isInSupportedPHPRange();

require realpath(__DIR__ . '/../src/vendor/autoload.php');

$env = 'prod';
$debug = 'prod' !== $env;

if ($debug) {
    umask(0000);
    Debug::enable();
}

$kernel = new HttpKernel($env, $debug);
$request = Request::createFromGlobals();
$response = $kernel->handleRequest($request);
$response->send();
$kernel->terminate($request, $response);
