<?php

/**
 * Class orangehrmMarketPlacePluginAllTests
 */
class orangehrmMarketPlacePluginAllTests {

    /**
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite() {

        $suite = new PHPUnit_Framework_TestSuite('orangehrmMaintenancePluginAllTest');

        $suite->addTestFile(dirname(__FILE__) . '/model/service/APIManagerServiceTest.php');

        return $suite;
    }

}
