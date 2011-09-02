<?php

require_once 'PHPUnit/Framework.php';
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */
require_once sfConfig::get('sf_test_dir') . '/util/TestDataService.php';

class CandidateServiceTest extends PHPUnit_Framework_TestCase {

    private $candidateService;
    protected $fixture;

    /**
     * Set up method
     */
    protected function setUp() {

        $this->candidateService = new CandidateService();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmRecruitmentPlugin/test/fixtures/CandidateDao.yml';
        TestDataService::populate($this->fixture);
    }

    /**
     * Testing getAllCandidatesList
     */
    public function testGetAllCandidatesList() {

        $allCandidatesList = TestDataService::loadObjectList('JobCandidate', $this->fixture, 'JobCandidate');
	$allowedCandidateList = array(1,2,3);
        $candidateDao = $this->getMock('CandidateDao');

        $candidateDao->expects($this->once())
                ->method('getCandidateList')
                ->will($this->returnValue($allCandidatesList));

        $this->candidateService->setCandidateDao($candidateDao);

        $readCandidatesList = $this->candidateService->getCandidateList($allowedCandidateList);
        $this->assertEquals($readCandidatesList, $allCandidatesList);
    }

    /**
     * Testing getAllCandidatesList
     */
    public function testSearchCandidates() {

        $searchParam = new CandidateSearchParameters();

        $searchParam->setJobTitleCode('JOB002');

        $candidatesVacancyList = TestDataService::loadObjectList('JobCandidateVacancy', $this->fixture, 'JobCandidateVacancy');
        $canVacList = array($candidatesVacancyList[2], $candidatesVacancyList[3], $candidatesVacancyList[4]);
        $candidateDao = $this->getMock('CandidateDao');

        $candidateDao->expects($this->once())
                ->method('searchCandidates')
                ->with('')
                ->will($this->returnValue($canVacList));

        $this->candidateService->setCandidateDao($candidateDao);

        $readCanVacList = $this->candidateService->searchCandidates($searchParam);
        $this->assertEquals($readCanVacList, $canVacList);
    }

    /**
     * Testing getCandidateRecordsCount
     */
    public function testGetCandidateRecordsCount() {

        $searchParam = new CandidateSearchParameters();

        $searchParam->setJobTitleCode('JOB002');

        $candidateDao = $this->getMock('CandidateDao');

        $candidateDao->expects($this->once())
                ->method('getCandidateRecordsCount')
                ->with($searchParam)
                ->will($this->returnValue(4));

        $this->candidateService->setCandidateDao($candidateDao);

        $result = $this->candidateService->getCandidateRecordsCount($searchParam);
        $this->assertEquals($result, 4);
    }

    /**
     *
     */
    public function testSaveCandidate() {

        $candidate = new JobCandidate();

        $candidateDao = $this->getMock('CandidateDao');
        $candidateDao->expects($this->once())
                ->method('saveCandidate')
                ->with($candidate)
                ->will($this->returnValue(true));

        $this->candidateService->setCandidateDao($candidateDao);

        $return = $this->candidateService->saveCandidate($candidate);
        $this->assertTrue($return);
    }

    /**
     * 
     */
    public function testSaveCandidateVacancy() {

        $candidateVacancy = new JobCandidateVacancy();

        $candidateDao = $this->getMock('CandidateDao');
        $candidateDao->expects($this->once())
                ->method('saveCandidateVacancy')
                ->with($candidateVacancy)
                ->will($this->returnValue(true));

        $this->candidateService->setCandidateDao($candidateDao);

        $return = $this->candidateService->saveCandidateVacancy($candidateVacancy);
        $this->assertTrue($return);
    }

    /**
     * 
     */
    public function testGetCandidateVacancyById() {
        $candidateVacancyList = TestDataService::loadObjectList('JobCandidateVacancy', $this->fixture, 'JobCandidateVacancy');
        $requiredObject = $candidateVacancyList[2];

        $candidateDao = $this->getMock('CandidateDao');
        $candidateDao->expects($this->once())
                ->method('getCandidateVacancyById')
                ->with(3)
                ->will($this->returnValue($requiredObject));

        $this->candidateService->setCandidateDao($candidateDao);

        $result = $this->candidateService->getCandidateVacancyById(3);
        $this->assertEquals($requiredObject, $result);
    }
    /**
     *
     */
    public function testGetCandidateHistoryById() {
        $candidateHistoryList = TestDataService::loadObjectList('CandidateHistory', $this->fixture, 'CandidateHistory');

        $candidateDao = $this->getMock('CandidateDao');
        $candidateDao->expects($this->once())
                ->method('getCandidateHistoryById')
                ->with(1)
                ->will($this->returnValue($candidateHistoryList[0]));

        $this->candidateService->setCandidateDao($candidateDao);

        $result = $this->candidateService->getCandidateHistoryById(1);
        $this->assertEquals($candidateHistoryList[0], $result);
    }
    
    public function testProcessCandidatesVacancyArray() {
        
        $candidateVacancyId = array('1_2', '4_5');
        $result = $this->candidateService->processCandidatesVacancyArray($candidateVacancyId);
        $expextedResult = array(1, 4);
        $this->assertEquals($expextedResult, $result);
    }
    
    public function testDeleteCandidateVacanciesTestDeleteCandidate(){
        
        $candidateId = 1;
        $toBeDeleteCandidateIds = array(1);
        $toBeDeletedRecords = array(1 =>array(1, 3));
        
        $candidateDao = $this->getMock('CandidateDao', array('getAllVacancyIdsForCandidate', 'deleteCandidates', 'deleteCandidateVacancies'));
        
        $candidateDao->expects($this->any())
                     ->method('getAllVacancyIdsForCandidate')
                     ->with($candidateId)
                     ->will($this->returnValue(array(1, 3)));
        
        $candidateDao->expects($this->once())
                     ->method('deleteCandidates')
                     ->with($toBeDeleteCandidateIds)
                     ->will($this->returnValue(true));        
       
        $this->candidateService->setCandidateDao($candidateDao);
        $result = $this->candidateService->deleteCandidateVacancies($toBeDeletedRecords);
        $this->assertEquals(true ,$result);
        
    }
    
    public function testDeleteCandidateVacanciesTestDeleteCandidateVacancy(){
        
        $candidateId = 2;
        $toBeDeletedRecords = array(2 => array(1, 3));
        $toBeDeleteCandidateVacancies = array(array(2, 1), array(2, 3));
        
        $candidateDao = $this->getMock('CandidateDao', array('getAllVacancyIdsForCandidate', 'deleteCandidates', 'deleteCandidateVacancies'));
        
        $candidateDao->expects($this->any())
                     ->method('getAllVacancyIdsForCandidate')
                     ->with($candidateId)
                     ->will($this->returnValue(array(1, 2, 3)));
        
        $candidateDao->expects($this->once())
                     ->method('deleteCandidateVacancies')
                     ->with($toBeDeleteCandidateVacancies)
                     ->will($this->returnValue(true));
       
        $this->candidateService->setCandidateDao($candidateDao);
        $result = $this->candidateService->deleteCandidateVacancies($toBeDeletedRecords);
        $this->assertEquals(true ,$result);
        
    }
    


    
}

