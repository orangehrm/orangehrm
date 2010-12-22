<?php

class orangehrmCoreLeavePluginAllTests {

    protected function setUp() {

    }

    public static function suite() {

        $suite = new PHPUnit_Framework_TestSuite('orangehrmCoreLeavePluginAllTest');

        /* Dao Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/WorkWeekDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/HolidayDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/LeavePeriodDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/LeaveTypeDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/LeaveEntitlementDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/LeaveRequestDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/LeaveSummaryDaoTest.php');

        /* Service Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/service/WorkWeekServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/HolidayServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/LeavePeriodServiceGenerateEndDateTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/LeavePeriodServiceGenerateStartDateTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/LeavePeriodServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/LeaveTypeServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/LeaveEntitlementServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/LeaveRequestServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/LeaveSummaryServiceTest.php');

        /* Action Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/actions/ApplyLeaveActionTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/actions/AssignLeaveActionTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/actions/ViewLeaveSummaryActionTest.php');

        return $suite;

    }

    public static function main() {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

}

if (PHPUnit_MAIN_METHOD == 'orangehrmCoreLeavePluginAllTests::main') {
    orangehrmCoreLeavePluginAllTests::main();
}

?>
