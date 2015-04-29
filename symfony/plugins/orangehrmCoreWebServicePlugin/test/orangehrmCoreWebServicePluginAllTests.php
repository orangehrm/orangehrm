<?php

class orangehrmCoreWebServicePluginAllTests {

    protected function setUp() {

    }

    public static function suite() {

        $suite = new PHPUnit_Framework_TestSuite('orangehrmCoreWebServicePluginAllTest');

        /* Utility Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/lib/utility/WSHelperTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/lib/utility/WSManagerTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/lib/utility/WSWrapperFactoryTest.php');
        
        /* Service Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/lib/service/WSUtilityServiceTest.php');
        
        /* DAO Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/lib/dao/WSUtilityDaoTest.php');

        return $suite;

    }

    public static function main() {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

}

if (PHPUnit_MAIN_METHOD == 'orangehrmCoreWebServicePluginAllTests::main') {
    orangehrmCoreWebServicePluginAllTests::main();
}

?>
