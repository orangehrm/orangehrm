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
class orangehrmRESTPluginAllTests
{

    protected function setUp() {

    }

    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite('orangehrmRESTPluginAllTest');

        /* HTTP test Cases */

        $suite->addTestFile(dirname(__FILE__) . '/http/HttpResponseTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/http/RequestParamsTest.php');

        $suite->addTestFile(dirname(__FILE__) . '/api/ValidatorTest.php');
        /* API test cases */

        $suite->addTestFile(dirname(__FILE__) . '/api/pim/ApiEmployeeDetailAPITest.php');
        $suite->addTestFile(dirname(__FILE__) . '/api/pim/ApiEmployeeJobDetailAPITest.php');
        $suite->addTestFile(dirname(__FILE__) . '/api/pim/ApiEmployeeContactDetailAPITest.php');
        $suite->addTestFile(dirname(__FILE__) . '/api/pim/ApiEmployeeSearchAPITest.php');
       // $suite->addTestFile(dirname(__FILE__) . '/api/pim/ApiSupervisorAPITest.php');
        $suite->addTestFile(dirname(__FILE__) . '/api/pim/ApiEmployeeDependentAPITest.php');
        $suite->addTestFile(dirname(__FILE__) . '/api/pim/ApiEmployeeTerminateAPITest.php');

        $suite->addTestFile(dirname(__FILE__) . '/api/leave/ApiLeaveEntitlementAPITest.php');
        $suite->addTestFile(dirname(__FILE__) . '/api/leave/ApiLeaveTypeAPITest.php');
        $suite->addTestFile(dirname(__FILE__) . '/api/leave/ApiLeavePeriodAPITest.php');

        $suite->addTestFile(dirname(__FILE__) . '/api/pim/ApiCustomFieldAPITest1.php');
         $suite->addTestFile(dirname(__FILE__) . '/api/pim/ApiEmployeeCustomFieldAPITest.php');
        $suite->addTestFile(dirname(__FILE__) . '/api/pim/ApiEmployeeWorkExperienceAPITest.php');
//        $suite->addTestFile(dirname(__FILE__) . '/api/pim/ApiCustomFieldAPITest.php');
        $suite->addTestFile(dirname(__FILE__) . '/api/pim/ApiEmployeeEducationAPITest.php');
       // $suite->addTestFile(dirname(__FILE__) . '/api/leave/ApiLeaveRequestAPITest.php');
        $suite->addTestFile(dirname(__FILE__) . '/api/admin/ApiUsersAPITest.php');
        //$suite->addTestFile(dirname(__FILE__) . '/api/admin/ApiUserLoginAPITest.php');

        /* Entity test cases - PIM*/

        $suite->addTestFile(dirname(__FILE__) . '/api/pim/entity/ApiEmployeeContactDetailTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/api/pim/entity/ApiEmployeeDependentTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/api/pim/entity/ApiEmployeeJobDetailTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/api/pim/entity/ApiEmployeeTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/api/pim/entity/ApiCustomFieldTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/api/pim/entity/ApiEmployeeCustomFieldTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/api/pim/entity/ApiSupervisorTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/api/leave/entity/ApiLeaveEntitlementTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/api/leave/entity/ApiLeaveRequestTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/api/leave/entity/ApiLeaveTypeTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/api/leave/entity/ApiLeaveRequestCommentTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/api/leave/entity/ApiLeaveTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/api/admin/entity/ApiUserTest.php');


        return $suite;
    }

    public static function main() {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }
}


