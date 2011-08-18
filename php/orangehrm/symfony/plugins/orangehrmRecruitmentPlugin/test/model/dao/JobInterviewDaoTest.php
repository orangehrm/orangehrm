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

class JobInterviewDaoTest extends PHPUnit_Framework_TestCase {

    private $jobInterviewDao;
    protected $fixture;

    /**
     * Set up method
     */
    protected function setUp() {

        $this->jobInterviewDao = new JobInterviewDao();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmRecruitmentPlugin/test/fixtures/CandidateDao.yml';
        TestDataService::populate($this->fixture);
    }
    
    /**
     * Test getInterviewListByCandidateIdAndInterviewDate for check return objects
     */
    public function testGetInterviewListByCandidateIdAndInterviewDateAndTimeForCorrectObjects() {
        
        $interviewList = $this->jobInterviewDao->getInterviewListByCandidateIdAndInterviewDateAndTime(4, '2011-08-18', '9:00:00', '11:00:00');
        $this->assertTrue($interviewList[0] instanceof JobInterview);
        
    }
    
    /**
     * Test getInterviewListByCandidateIdAndInterviewDate for existing results
     */
    public function testGetInterviewListByCandidateIdAndInterviewDateAndTimeForExistingResults() {
        
        $interviewList = $this->jobInterviewDao->getInterviewListByCandidateIdAndInterviewDateAndTime(4, '2011-08-18', '9:00:00', '11:00:00');
        $this->assertEquals(1, count($interviewList));
        
    }
    
    /**
     * Test getInterviewListByCandidateIdAndInterviewDate for not existing results
     */
    public function testGetInterviewListByCandidateIdAndInterviewDateAndTimeForNotExistingResults() {
        
        $interviewList = $this->jobInterviewDao->getInterviewListByCandidateIdAndInterviewDateAndTime(5, '2011-08-18', '9:00:00', '11:00:00');
        $this->assertTrue(true, empty($interviewList));
        
    }
    
}

