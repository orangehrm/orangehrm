<?php

class orangehrmCoreWebServicePluginAllTests {
    public static function suite() {

        $suite = new PHPUnit\Framework\TestSuite('orangehrmCoreWebServicePluginAllTest');

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
}


