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
        
        /* Dao Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/dao/ConfigDaoTest.php');

        /* Service Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/service/ConfigServiceTest.php');

        /* Factory Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/factory/SimpleUserRoleFactoryTest.php');

        /* AccessFlowStateMachine Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/AccessFlowStateMachineDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/AccessFlowStateMachineServiceTest.php');

        /* ReportGenerator Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/ReportableDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/ReportableServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/ReportGeneratorServiceTest.php');

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
