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

use Doctrine\DBAL\Exception\ConnectionException;
use OrangeHRM\Framework\ServiceContainer;
use OrangeHRM\Framework\Services;
use OrangeHRM\ORM\Doctrine;
use OrangeHRM\ORM\Exception\ConfigNotFoundException;
use OrangeHRM\Tests\Util\CoreFixtureService;

define('ENVIRONMENT', 'test');
date_default_timezone_set('UTC');

require realpath(__DIR__ . '/../../../vendor/autoload.php');

$errorMessage = "
Can't connect to test database.
Run below command and try again;
$ php ./devTools/core/console.php i:create-test-db -p root

Error:
%s\n
";

ServiceContainer::getContainer()
    ->register(Services::DOCTRINE)
    ->setFactory([Doctrine::class, 'getEntityManager']);

try {
    ServiceContainer::getContainer()->get(Services::DOCTRINE)->getConnection()->connect();
} catch (ConnectionException $e) {
    echo sprintf(
        $errorMessage,
        $e->getMessage()
    );
    die;
} catch (ConfigNotFoundException $e) {
    die($e->getMessage() . "\n\n");
}

$coreFixtureService = new CoreFixtureService();
if (!$coreFixtureService->isReady()) {
    $errorMessage = "
Core fixtures not found.
Run below command and try again;
$ php ./devTools/core/console.php i:create-test-db -p root
\n
";
    echo $errorMessage;
    die;
}
