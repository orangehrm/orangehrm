<?php

class orangehrmRecruitmentPluginAllTests {
    public static function suite() {

        $suite = new PHPUnit\Framework\TestSuite('orangehrmRecruitmentPluginAllTest');

        /* Dao Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/CandidateDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/VacancyDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/RecruitmentAttachmentDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/JobInterviewDaoTest.php');

        /* Service Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/service/CandidateServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/VacancyServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/RecruitmentAttachmentServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/JobInterviewServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/CandidateHistoryServiceTest.php');



        return $suite;
    }
}

