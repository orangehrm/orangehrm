<?php

class orangehrmPerformanceTrackerPluginAllTests {

    protected function setUp() {

    }

    public static function suite() {

        $suite = new PHPUnit_Framework_TestSuite('orangehrmPerformanceTrackerPluginAllTest');

        /* Dao Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/PerformanceTrackerDaoTest.php');
        
        /*Services Test Cases*/
        $suite->addTestFile(dirname(__FILE__) . '/model/service/PerformanceTrackerServiceTest.php');
        
        return $suite;

    }

    public static function main() {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

}

if (PHPUnit_MAIN_METHOD == 'orangehrmPerformanceTrackerPluginAllTests::main') {
    orangehrmPerformanceTrackerPluginAllTests::main();
}

?>
