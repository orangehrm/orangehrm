<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

use Composer\Autoload\ClassLoader;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\CacheService;
use OrangeHRM\Framework\Filesystem\Filesystem;
use OrangeHRM\Framework\ServiceContainer;
use OrangeHRM\Framework\Services;
use OrangeHRM\FunctionalTesting\Service\DatabaseBackupService;
use OrangeHRM\ORM\Doctrine;

require realpath(__DIR__ . '/../../../vendor/autoload.php');

if (Config::PRODUCT_MODE === Config::MODE_PROD) {
    echo "Not allowed to execute in prod mode`\n";
    exit;
}
$filesystem = new Symfony\Component\Filesystem\Filesystem();
$filesystem->symlink(
    realpath(__DIR__ . '/plugins/orangehrmFunctionalTestingPlugin'),
    Config::get(Config::PLUGINS_DIR) . '/orangehrmFunctionalTestingPlugin',
    true
);
echo "\nSuccessfully copied `src/test/functional/tools/plugins/orangehrmFunctionalTestingPlugin` " .
    "to src/plugins/orangehrmFunctionalTestingPlugin\n";

$loader = new ClassLoader();
$loader->addPsr4('OrangeHRM\\FunctionalTesting\\', [realpath(__DIR__ . '/plugins/orangehrmFunctionalTestingPlugin')]);
$loader->register();
ServiceContainer::getContainer()->register(Services::CACHE)->setFactory([CacheService::class, 'getCache']);
ServiceContainer::getContainer()->register(Services::DOCTRINE)->setFactory([Doctrine::class, 'getEntityManager']);
$databaseBackupService = new DatabaseBackupService();
$databaseBackupService->createInitialSavepoint();
echo "\nCreated initial savepoint for the database\n";

$filesystem = new Filesystem();
echo "\nAttempt to acquire the cache directory permission\n";
try {
    $filesystem->chmod(Config::get(Config::CACHE_DIR), 0777, 0000, true);
    echo "Successfully acquired cache directory permission\n";
} catch (Throwable $e) {
    echo "Failed to acquire cache directory permission\n";
}
