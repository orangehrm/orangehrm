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

use OrangeHRM\Framework\ServiceContainer;
use OrangeHRM\Framework\Services;
use OrangeHRM\ORM\Doctrine;

require_once realpath(__DIR__ . '/../../src/vendor/autoload.php');

ServiceContainer::getContainer()->register(Services::DOCTRINE)
    ->setFactory([Doctrine::class, 'getEntityManager']);

$migration = new \OrangeHRM\Installer\Migration\V4_3_4\Migration();
$migration->up();

$migration = new \OrangeHRM\Installer\Migration\V4_3_5\Migration();
$migration->up();

$migration = new \OrangeHRM\Installer\Migration\V4_4_0\Migration();
$migration->up();

$migration = new \OrangeHRM\Installer\Migration\V4_5_0\Migration();
$migration->up();

$migration = new \OrangeHRM\Installer\Migration\V4_6_0\Migration();
$migration->up();

$migration = new \OrangeHRM\Installer\Migration\V4_6_0_1\Migration();
$migration->up();

$migration = new \OrangeHRM\Installer\Migration\V4_7_0\Migration();
$migration->up();

$migration = new \OrangeHRM\Installer\Migration\V4_8_0\Migration();
$migration->up();

$migration = new \OrangeHRM\Installer\Migration\V4_9_0\Migration();
$migration->up();

$migration = new \OrangeHRM\Installer\Migration\V4_10_0\Migration();
$migration->up();

$migration = new \OrangeHRM\Installer\Migration\V4_10_1\Migration();
$migration->up();

$migration = new \OrangeHRM\Installer\Migration\V5_0_0_beta\Migration();
$migration->up();

$migration = new \OrangeHRM\Installer\Migration\V5_0_0\Migration();
$migration->up();
