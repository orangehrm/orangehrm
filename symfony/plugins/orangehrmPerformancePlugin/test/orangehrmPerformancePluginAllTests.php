<?php

class orangehrmPerformancePluginAllTests {
    public static function suite() {
        
        $suite = new PHPUnit\Framework\TestSuite('orangehrmPerformancePluginTest');        
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/KpiDaoTest.php');       
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/PerforamanceReviewDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/reviewerGroup/ReviewerGroupFactoryTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/status/ReviewStatusFactoryTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/status/ReviewStatusTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/reviewerGroup/ReviewerGroupTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/KpiServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/PerforamanceReviewServiceTest.php');
        
        return $suite;
    }
}
