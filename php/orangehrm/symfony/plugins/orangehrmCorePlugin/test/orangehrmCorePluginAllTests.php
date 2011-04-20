<?php

class orangehrmCorePluginAllTests {

    protected function setUp() {

    }

    public static function suite() {

        $suite = new PHPUnit_Framework_TestSuite('orangehrmCorePluginAllTest');

        /* Component Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/components/ohrmListComponentTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/components/ListHeaderTest.php');

        return $suite;

    }

    public static function main() {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

}

if (PHPUnit_MAIN_METHOD == 'orangehrmCorePluginAllTests::main') {
    orangehrmCorePluginAllTests::main();
}

?>
