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

use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Config\Config;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Installer\Framework\HttpKernel;
use OrangeHRM\Installer\Util\AppSetupUtility;
use OrangeHRM\Installer\Util\StateContainer;
use Symfony\Component\Yaml\Yaml;

$pathToAutoload = realpath(__DIR__ . '/../src/vendor/autoload.php');

$errorMessage = "
Cannot find composer dependencies.
Run below command and try again;\n
$ cd %s
$ composer install -d src
";

if ($pathToAutoload === false) {
    die(sprintf($errorMessage, realpath(__DIR__ . '/../')));
}

require_once $pathToAutoload;

if (Config::isInstalled()) {
    die("This system already installed.\n");
}

$kernel = new HttpKernel('prod', false);
$request = new Request();
$kernel->handleRequest($request);

$cliConfig = Yaml::parseFile(realpath(__DIR__ . '/cli_install_config.yaml'));

if ($cliConfig['license']['agree'] != 'y') {
    $licenseFilePath = realpath(__DIR__ . "/../LICENSE");
    echo "For continue installation need to accept OrangeHRM license agreement. It is available in '$licenseFilePath'.";
    die;
}
echo "Agreed to license from config file\n";

$dbType = $cliConfig['database']['isExistingDatabase'] == 'n' ? AppSetupUtility::INSTALLATION_DB_TYPE_NEW : AppSetupUtility::INSTALLATION_DB_TYPE_EXISTING;
$dbHost = $cliConfig['database']['hostName'];
$dbPort = $cliConfig['database']['hostPort'];
$dbUser = $cliConfig['database']['privilegedDatabaseUser'];
$dbPassword = $cliConfig['database']['privilegedDatabasePassword'];
$dbName = $cliConfig['database']['databaseName'];
$useSameDbUserForOrangeHRM = $cliConfig['database']['useSameDbUserForOrangeHRM'] == 'y';

$organizationName = $cliConfig['organization']['name'];
$countryCode = $cliConfig['organization']['country'];

$adminUsername = $cliConfig['admin']['adminUserName'];
$adminPassword = $cliConfig['admin']['adminPassword'];

$firstName = $cliConfig['admin']['adminEmployeeFirstName'];
$lastName = $cliConfig['admin']['adminEmployeeLastName'];
$email = $cliConfig['admin']['workEmail'];
$contact = $cliConfig['admin']['contactNumber'];


if ($dbType === AppSetupUtility::INSTALLATION_DB_TYPE_NEW) {
    $ohrmDbUser = $dbUser;
    $ohrmDbPassword = $dbPassword;
    if (!$useSameDbUserForOrangeHRM) {
        $ohrmDbUser = $cliConfig['database']['orangehrmDatabaseUser'];
        $ohrmDbPassword = $cliConfig['database']['orangehrmDatabasePassword'];
    }

    StateContainer::getInstance()->storeDbInfo(
        $dbHost,
        $dbPort,
        new UserCredential($dbUser, $dbPassword),
        $dbName,
        new UserCredential($ohrmDbUser, $ohrmDbPassword)
    );
    StateContainer::getInstance()->setDbType(AppSetupUtility::INSTALLATION_DB_TYPE_NEW);
} else {
    // `existing` database
    StateContainer::getInstance()->storeDbInfo(
        $dbHost,
        $dbPort,
        new UserCredential($dbUser, $dbPassword),
        $dbName
    );
    StateContainer::getInstance()->setDbType(AppSetupUtility::INSTALLATION_DB_TYPE_EXISTING);
}

// Instance data
StateContainer::getInstance()->storeInstanceData($organizationName, $countryCode, 'en_US', 'UTC');

// Admin user
StateContainer::getInstance()->storeAdminUserData(
    $firstName,
    $lastName,
    $email,
    new UserCredential($adminUsername, $adminPassword),
    $contact
);

$appSetupUtility = new AppSetupUtility();
echo "Database creation\n";
$appSetupUtility->createDatabase();
echo "Applying database changes\n";
$appSetupUtility->runMigrations('3.3.3', Config::PRODUCT_VERSION);
echo "Instance creation & Admin user creation\n";
$appSetupUtility->insertSystemConfiguration();
echo "Create OrangeHRM database user\n";
$appSetupUtility->createDBUser();
echo "Creating configuration files\n";
$appSetupUtility->writeConfFile();

echo "Done\n";
