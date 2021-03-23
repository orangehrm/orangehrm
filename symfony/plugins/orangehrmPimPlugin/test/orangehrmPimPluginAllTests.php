<?php

class orangehrmPimPluginAllTests {

    public static function suite() {

        $suite = new PHPUnit\Framework\TestSuite('orangehrmPimPluginAllTest');

        /* Dao Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/EmployeeDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/EmployeeDaoReportingChainTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/ReportingMethodConfigurationDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/TerminationReasonConfigurationDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/EmployeeListDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/CustomFieldConfigurationDaoTest.php');

        /* Service Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/service/EmployeeServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/PIMLeftMenuServiceTest.php');

        /* ParameterHolder Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/parameterholder/EmployeeSearchParameterHolderTest.php');

        return $suite;
    }
}

