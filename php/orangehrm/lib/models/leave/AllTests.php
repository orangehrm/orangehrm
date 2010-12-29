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
    define('PHPUnit_MAIN_METHOD', 'models_leave_AllTests::main');
}
set_include_path(get_include_path() . PATH_SEPARATOR . "../../../build");

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'HolidaysTest.php';
require_once 'LeaveQuotaTest.php';
require_once 'LeaveRequestsTest.php';
require_once 'LeaveSummaryTest.php';
require_once 'LeaveTest.php';
require_once 'LeaveTypeTest.php';
require_once 'WeekendsTest.php';

class models_leave_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('OrangeHRM models_leave');
	$suite->addTestSuite('HolidaysTest');
	$suite->addTestSuite('LeaveQuotaTest');
	$suite->addTestSuite('LeaveRequestsTest');
	$suite->addTestSuite('LeaveSummaryTest');
	$suite->addTestSuite('LeaveTest');
	$suite->addTestSuite('LeaveTypeTest');
	$suite->addTestSuite('WeekendsTest');

    return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'models_leave_AllTests::main') {
    models_leave_AllTests::main();
}
?>
