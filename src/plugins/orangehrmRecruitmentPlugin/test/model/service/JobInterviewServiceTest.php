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
class JobInterviewServiceTest extends PHPUnit_Framework_TestCase {

    private $jobInterviewService;
    protected $fixture;

    /**
     * Set up method
     */
    protected function setUp() {

        $this->jobInterviewService = new JobInterviewService();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmRecruitmentPlugin/test/fixtures/CandidateDao.yml';
        TestDataService::populate($this->fixture);
    }
    
    public function testTemp() {
        $this->assertTrue(true);
    }
    
//    /*
//     * Test getInterviewListByCandidateIdAndInterviewDateAndTime for true
//     */
//    public function testGetInterviewListByCandidateIdAndInterviewDateAndTimeForTrue() {
//        
//        $interviewList = TestDataService::loadObjectList('JobInterview', $this->fixture, 'JobInterview');
//        $requiredObject = $interviewList[1]; 
//        
//        $parameters = array('candidateId' => 4, 'interviewDate' => '2011-08-18', 'fromTime' => '09:00:00', 'toTime' => '11:00:00');
//
//        $jobInterviewDao = $this->getMockBuilder('JobInterviewDao')
//			->setMethods( array('getInterviewListByCandidateIdAndInterviewDateAndTime'))
//			->getMock();
//
//            $jobInterviewDao->expects($this->once())
//                           ->method('getInterviewListByCandidateIdAndInterviewDateAndTime')
//                           ->with($parameters)
//                           ->will($this->returnValue($requiredObject));
//
//            $this->jobInterviewService->setJobInterviewDao($jobInterviewDao);
//
//            $result = $this->jobInterviewService->getInterviewListByCandidateIdAndInterviewDateAndTime($parameters[0], $parameters[1], '09:30:00');
//
//            $this->assertEquals(true, $result);
//        
//    }
//    
//    /*
//     * Test getInterviewListByCandidateIdAndInterviewDateAndTime for false
//     */
//    public function testGetInterviewListByCandidateIdAndInterviewDateAndTimeFaorFalse() {
//        
//        $requiredObject = array(); 
//        
//        $parameters = array('candidateId' => 4, 'interviewDate' => '2011-08-18', 'fromTime' => '09:00:00', 'toTime' => '11:00:00');
//
//        $jobInterviewDao = $this->getMockBuilder('JobInterviewDao')
//			->setMethods( array('getInterviewListByCandidateIdAndInterviewDateAndTime'))
//			->getMock();
//
//            $jobInterviewDao->expects($this->once())
//                           ->method('getInterviewListByCandidateIdAndInterviewDateAndTime')
//                           ->with($parameters)
//                           ->will($this->returnValue($requiredObject));
//
//            $this->jobInterviewService->setJobInterviewDao($jobInterviewDao);
//
//            $result = $this->jobInterviewService->getInterviewListByCandidateIdAndInterviewDateAndTime($parameters[0], $parameters[1], '09:30:00');
//
//            $this->assertEquals(false, $result);
//        
//    }
    
    public function testGetInterviewById() {

        $interviews = TestDataService::loadObjectList('JobInterview', $this->fixture, 'JobInterview');
        $expectedresult = $interviews[0];

        $jobInterviewDao = $this->getMockBuilder('JobInterviewDao')->getMock();
        $jobInterviewDao->expects($this->once())
                ->method('getInterviewById')
                ->with(1)
                ->will($this->returnValue($expectedresult));

        $this->jobInterviewService->setJobInterviewDao($jobInterviewDao);

        $return = $this->jobInterviewService->getInterviewById(1);
        $this->assertEquals($expectedresult, $return);
    }
    
    public function testGetInterviewersByInterviewId() {

        $interviewInterViewer = TestDataService::loadObjectList('JobInterviewInterviewer', $this->fixture, 'JobInterviewInterviewer');
        $expectedresult = $interviewInterViewer;
        $jobInterviewDao = $this->getMockBuilder('JobInterviewDao')->getMock();
        $jobInterviewDao->expects($this->once())
                ->method('getInterviewersByInterviewId')
                ->with(1)
                ->will($this->returnValue($expectedresult));

        $this->jobInterviewService->setJobInterviewDao($jobInterviewDao);

        $return = $this->jobInterviewService->getInterviewersByInterviewId(1);
        $this->assertEquals($expectedresult, $return);
    }

    public function testGetInterviewsByCandidateVacancyId() {

        $interviews = TestDataService::loadObjectList('JobInterview', $this->fixture, 'JobInterview');
        $expectedresult = $interviews[1];
        $jobInterviewDao = $this->getMockBuilder('JobInterviewDao')->getMock();
        $jobInterviewDao->expects($this->once())
                ->method('getInterviewsByCandidateVacancyId')
                ->with(10)
                ->will($this->returnValue($expectedresult));

        $this->jobInterviewService->setJobInterviewDao($jobInterviewDao);

        $return = $this->jobInterviewService->getInterviewsByCandidateVacancyId(10);
        $this->assertEquals($expectedresult, $return);
    }
    
    public function testSaveJobInterview() {

        $jobInterview = new JobInterview();

        $jobInterviewDao = $this->getMockBuilder('JobInterviewDao')->getMock();
        $jobInterviewDao->expects($this->once())
                ->method('saveJobInterview')
                ->with($jobInterview)
                ->will($this->returnValue(true));

        $this->jobInterviewService->setJobInterviewDao($jobInterviewDao);

        $return = $this->jobInterviewService->saveJobInterview($jobInterview);
        $this->assertTrue($return);
    }
    
    public function testUpdateJobInterview() {

        $jobInterview = new JobInterview();
        
        $jobInterviewDao = $this->getMockBuilder('JobInterviewDao')->getMock();
        $jobInterviewDao->expects($this->once())
                ->method('updateJobInterview')
                ->with($jobInterview)
                ->will($this->returnValue($jobInterview));

        $this->jobInterviewService->setJobInterviewDao($jobInterviewDao);

        $return = $this->jobInterviewService->updateJobInterview($jobInterview);
        $this->assertTrue($return instanceof JobInterview);
    }
    
    public function testGetInterviewScheduledHistoryByInterviewId() {

        $candidateHistory = TestDataService::loadObjectList('CandidateHistory', $this->fixture, 'CandidateHistory');
        $expectedresult = $candidateHistory[2];
        $jobInterviewDao = $this->getMockBuilder('JobInterviewDao')->getMock();
        $jobInterviewDao->expects($this->once())
                ->method('getInterviewScheduledHistoryByInterviewId')
                ->with(1)
                ->will($this->returnValue($expectedresult));

        $this->jobInterviewService->setJobInterviewDao($jobInterviewDao);

        $return = $this->jobInterviewService->getInterviewScheduledHistoryByInterviewId(1);
        $this->assertEquals($expectedresult, $return);
    }
    
    public function testGetJobInterviewDao(){
        $dao = $this->jobInterviewService->getJobInterviewDao();
        $this->assertTrue($dao instanceof JobInterviewDao);
    }
    
}