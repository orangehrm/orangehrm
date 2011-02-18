<?php

class orangehrmPimPluginAllTests {

    protected function setUp() {

    }

    public static function suite() {

        $suite = new PHPUnit_Framework_TestSuite('orangehrmPimPluginAllTest');

        /* Dao Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/EmployeeDaoTest.php');

        /* Service Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/service/EmployeeServiceTest.php');

        return $suite;

    }

    public static function main() {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

}

if (PHPUnit_MAIN_METHOD == 'orangehrmPimPluginAllTests::main') {
    orangehrmPimPluginAllTests::main();
}

?>
