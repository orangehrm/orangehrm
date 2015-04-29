<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class orangehrmCorporateDirectoryPluginAllTests {

    protected function setUp() {
        
    }

    public static function suite() {

        $suite = new PHPUnit_Framework_TestSuite('orangehrmCorporateDirectoryPlugin');

        /* Dao Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/EmployeeDirectoryDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/EmployeeDirectoryServiceTest.php');
        
        /* Helper Test */
        $suite->addTestFile(dirname(__FILE__) . '/model/wrapper/CorporateDirectoryWebServiceHelperTest.php');

        /* Wrapper Test */
        $suite->addTestFile(dirname(__FILE__) . '/model/wrapper/CorporateDirectoryWebServiceWrapperTest.php');
        return $suite;
    }

    public static function main() {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }
    
}

    if (PHPUnit_MAIN_METHOD == 'orangehrmCorporateDirectoryPluginAllTests::main') {
    orangehrmAdvancedReportsPluginAllTests::main();
}


?>