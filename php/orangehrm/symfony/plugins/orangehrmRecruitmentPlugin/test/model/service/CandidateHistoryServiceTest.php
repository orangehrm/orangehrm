<?php


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

/**
 * @group Recruitment
 */
class CandidateHistoryServiceTest extends PHPUnit_Framework_TestCase {

    private $candidateHistoryService;
    protected $fixture;

    /**
     * Set up method
     */
    protected function setUp() {

        $this->candidateHistoryService = new CandidateHistoryService();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmRecruitmentPlugin/test/fixtures/CandidateDao.yml';
        TestDataService::populate($this->fixture);
    }
    
    /**
     * Testing getCandidateHistoryList for return type
     */
    public function testGetCandidateHistoryListForCorrectReturnObject() {
        
        $allCadidateHistoryList = TestDataService::loadObjectList('CandidateHistory', $this->fixture, 'CandidateHistory');
        
        $result = $this->candidateHistoryService->getCandidateHistoryList($allCadidateHistoryList);
        
        $this->assertTrue($result[0] instanceof CandidateHistoryDto);
        
    }
    
    /**
     * Testing getCandidateHistoryList for correct number of results
     */
    public function testGetCandidateHistoryListForCorrectNumberOfResults() {
        
        $allCadidateHistoryList = TestDataService::loadObjectList('CandidateHistory', $this->fixture, 'CandidateHistory');
        
        $result = $this->candidateHistoryService->getCandidateHistoryList($allCadidateHistoryList);
        
        $this->assertEquals(15, count($result));
        
    }
    
}