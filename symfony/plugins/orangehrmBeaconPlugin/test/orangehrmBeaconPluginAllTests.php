<?php

class orangehrmBeaconPluginAllTests {
    public static function suite() {


        $suite = new PHPUnit\Framework\TestSuite('orangehrmBeaconPluginAllTest');

        //$suite->addTestFile(dirname(__FILE__) . '';
        
        /* Service Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/service/BeaconNotificationServiceTest.php');
//        $suite->addTestFile(dirname(__FILE__) . '/model/service/BeaconDatapointServiceTest.php');
//        $suite->addTestFile(dirname(__FILE__) . '/model/service/BeaconConfigurationServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/BeaconCommunicationServiceTest.php');
        /* Dao Test Cases */
        
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/BeaconDatapointDaoTest.php');
         $suite->addTestFile(dirname(__FILE__) . '/model/dao/BeaconConfigurationDaoTest.php');
        
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/BeaconNotificationDaoTest.php');
        /* Processor Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/processor/CountDataPointProcessorTest.php');
        
        return $suite;

    }
}


