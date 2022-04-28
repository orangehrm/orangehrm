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

include_once('./../lib/confs/log_settings.php');

use OrangeHRM\Config\Config;
use OrangeHRM\Framework\Framework;
use OrangeHRM\Framework\Http\RedirectResponse;
use OrangeHRM\Framework\Http\Request;
use Symfony\Component\ErrorHandler\Debug;

require realpath(__DIR__ . '/../src/vendor/autoload.php');

$env = $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = 'prod';
$debug = (bool)($_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? $_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] = ('prod' !== $env));

if ($debug) {
    umask(0000);
    Debug::enable();
}

$kernel = new Framework($env, $debug);
$request = Request::createFromGlobals();

if (Config::isInstalled()) {
    $response = $kernel->handleRequest($request);
} else {
    $response = new RedirectResponse(str_replace('/web/index.php', '', $request->getBaseUrl()));
}

$response->send();
$kernel->terminate($request, $response);
