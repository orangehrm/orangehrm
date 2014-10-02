<?php

class orangehrmBeaconPluginAllTests {


    protected function setUp() {

    }

    public static function suite() {


        $suite = new PHPUnit_Framework_TestSuite('orangehrmBeaconPluginAllTest');

        //$suite->addTestFile(dirname(__FILE__) . '';
        
        /* Service Test Cases */
        
        /* Dao Test Cases */

        /* Other Test Cases */

        return $suite;

    }


    public static function main() {

        PHPUnit_TextUI_TestRunner::run(self::suite());

    }


}

if (PHPUnit_MAIN_METHOD == 'orangehrmBeaconPluginAllTests::main') {

    orangehrmBeaconPluginPluginAllTests::main();

}
