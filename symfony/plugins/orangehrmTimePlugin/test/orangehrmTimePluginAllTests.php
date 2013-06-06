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
class orangehrmTimePluginAllTests {

    protected function setUp() {

    }

    public static function suite() {

        $suite = new PHPUnit_Framework_TestSuite('orangehrmTimePluginAllTest');

        /* Dao Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/TimesheetDaoTest.php');

        /* Service Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/service/TimesheetServiceTest.php');

         /* TimesheetPeriodService Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/service/TimesheetPeriodServiceTest.php');

	/* TimesheetPeriodDao Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/TimesheetPeriodDaoTest.php');
        
        /* MonthlyTimesheetPeriodTest*/
         $suite->addTestFile(dirname(__FILE__) . '/timesheetPeriod/MonthlyTimesheetPeriodTest.php');
         
         /* WeeklyTimesheetPeriodTest*/
         $suite->addTestFile(dirname(__FILE__) . '/timesheetPeriod/WeeklyTimesheetPeriodTest.php');


        return $suite;
    }

    public static function main() {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

}

if (PHPUnit_MAIN_METHOD == 'orangehrmTimePluginAllTests::main') {
    orangehrmTimePluginAllTests::main();
}
?>
