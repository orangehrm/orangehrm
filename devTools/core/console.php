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

$pathToAutoload = realpath(__DIR__ . '/../../symfony/vendor/autoload.php');
$pathToDevAutoload = realpath(__DIR__ . '/vendor/autoload.php');

$errorMessage = "
Cannot find all composer dependencies.
Run below command and try again;\n
$ cd %s
$ composer install -d symfony
$ composer install -d devTools/core\n
";

if (!($pathToAutoload && $pathToDevAutoload)) {
    die(sprintf($errorMessage, realpath(__DIR__ . '/../../')));
}

require_once $pathToAutoload;
require_once $pathToDevAutoload;

use OrangeHRM\DevTools\Command\AddDataGroupCommand;
use OrangeHRM\DevTools\Command\AddRolePermissionCommand;
use OrangeHRM\Framework\ServiceContainer;
use OrangeHRM\Framework\Services;
use OrangeHRM\ORM\Doctrine;
use Symfony\Component\Console\Application;

$application = new Application();

$application->add(new AddDataGroupCommand());
$application->add(new AddRolePermissionCommand());

ServiceContainer::getContainer()->register(Services::DOCTRINE)
    ->setFactory([Doctrine::class, 'getEntityManager']);
$application->run();
