<?php

class orangehrmPerformanceTrackerPluginAllTests {
    public static function suite() {

        $suite = new PHPUnit\Framework\TestSuite('orangehrmPerformanceTrackerPluginAllTest');

        /* Dao Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/PerformanceTrackerDaoTest.php');
        
        /*Services Test Cases*/
        $suite->addTestFile(dirname(__FILE__) . '/model/service/PerformanceTrackerServiceTest.php');
        
        return $suite;

    }
}


