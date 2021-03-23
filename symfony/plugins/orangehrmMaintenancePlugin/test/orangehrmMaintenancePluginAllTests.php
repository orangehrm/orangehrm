<?php

/**
 * Class orangehrmMaintenancePluginAllTests
 */
class orangehrmMaintenancePluginAllTests {

    /**
     * @return PHPUnit\Framework\TestSuite
     */
    public static function suite() {

        $suite = new PHPUnit\Framework\TestSuite('orangehrmMaintenancePluginAllTest');

        $suite->addTestFile(dirname(__FILE__) . '/model/dao/MaintenanceDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/MaintenanceServiceTest.php');
        return $suite;
    }

}
