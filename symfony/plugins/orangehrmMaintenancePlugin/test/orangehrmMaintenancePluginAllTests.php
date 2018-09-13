<?php

class orangehrmMaintenancePluginAllTests {


    public static function suite() {


        $suite = new PHPUnit_Framework_TestSuite('orangehrmMaintenancePluginAllTest');

        $suite->addTestFile(dirname(__FILE__) . '/model/dao/PurgeDaoTest.php');

        return $suite;

    }

}
