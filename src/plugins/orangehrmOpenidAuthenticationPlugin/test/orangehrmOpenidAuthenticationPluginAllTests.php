<?php

class orangehrmOpenidAuthenticationPluginAllTests {
    public static function suite() {

        $suite = new PHPUnit\Framework\TestSuite('orangehrmOpenidAuthenticationPluginAllTest');

        /* Dao Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/OpenIdAuthenticationDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/OpenIdProviderDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/AuthenticationProviderDaoTest.php');

        /* Service Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/service/OpenIdProviderServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/AuthenticationProviderServiceTest.php');
        
        /* Mail related tests */


        /* Other test cases */
        //$suite->addTestFile(dirname(__FILE__) . '/entitlement/FIFOEntitlementConsumptionStrategyTest.php');



        return $suite;
    }
}

