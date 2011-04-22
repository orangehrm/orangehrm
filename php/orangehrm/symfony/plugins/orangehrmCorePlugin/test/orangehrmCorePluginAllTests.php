<?php

class orangehrmCorePluginAllTests {

    protected function setUp() {

    }

    public static function suite() {

        $suite = new PHPUnit_Framework_TestSuite('orangehrmCorePluginAllTest');

        /* Component Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/components/ListHeaderTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/components/PropertyPopulatorTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/components/LinkCellTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/components/ButtonTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/components/LabelCellTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/components/SortableHeaderCellTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/components/ListHeaderTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/components/CheckboxTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/components/HeaderCellTest.php');


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
