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
 *
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'models_eimadmin_AllTests::main');
}
set_include_path(get_include_path() . PATH_SEPARATOR . "../../../build");
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'CustomerTest.php';
require_once 'EmailNotificationConfigurationTest.php';
require_once 'ProjectsTest.php';
require_once 'ProjectActivityTest.php';
require_once 'ProjectAdminGatewayTest.php';
require_once 'ProjectAdminTest.php';
require_once 'CustomExportTest.php';
require_once 'CustomImportTest.php';
require_once 'encryption/KeyHandlerOldTest.php';
require_once 'JobSpecTest.php';
require_once 'CompPropertyTest.php';
require_once 'JobTitleTest.php';

class models_eimadmin_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
    $suite = new PHPUnit_Framework_TestSuite('OrangeHRM models_eimadmin');
	$suite->addTestSuite('CustomerTest');
	$suite->addTestSuite('EmailNotificationConfigurationTest');
	$suite->addTestSuite('ProjectTest');
	$suite->addTestSuite('ProjectActivityTest');
	$suite->addTestSuite('ProjectAdminTest');
	$suite->addTestSuite('ProjectAdminGatewayTest');
	$suite->addTestSuite('CustomExportTest');
	$suite->addTestSuite('CustomImportTest');
	$suite->addTestSuite('JobSpecTest');
	$suite->addTestSuite('KeyHandlerOldTest');
    $suite->addTestSuite('CompPropertyTest');
	$suite->addTestSuite('JobTitleTest');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'models_eimadmin_AllTests::main') {
    models_eimadmin_AllTests::main();
}
?>
