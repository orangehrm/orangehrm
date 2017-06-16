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
class RecruitmentAttachmentServiceTest extends PHPUnit_Framework_TestCase {

	private $recruitmentAttachmentService;
	protected $fixture;

	/**
	 * Set up method
	 */
	protected function setUp() {

		$this->recruitmentAttachmentService = new RecruitmentAttachmentService();
		$this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmRecruitmentPlugin/test/fixtures/CandidateDao.yml';
		TestDataService::populate($this->fixture);
	}

	/**
	 *
	 */
	public function testSaveVacancyAttachment() {

		$resume = new JobVacancyAttachment();

		$recruitmentAttachmentDao = $this->getMockBuilder('RecruitmentAttachmentDao')->getMock();
		$recruitmentAttachmentDao->expects($this->once())
			->method('saveVacancyAttachment')
			->with($resume)
			->will($this->returnValue(true));

		$this->recruitmentAttachmentService->setRecruitmentAttachmentDao($recruitmentAttachmentDao);

		$return = $this->recruitmentAttachmentService->saveVacancyAttachment($resume);
		$this->assertTrue($return);
	}

	/**
	 * 
	 */
	public function testSaveCandidateAttachment() {

		$resume = new JobCandidateAttachment();

		$recruitmentAttachmentDao = $this->getMockBuilder('RecruitmentAttachmentDao')->getMock();
		$recruitmentAttachmentDao->expects($this->once())
			->method('saveCandidateAttachment')
			->with($resume)
			->will($this->returnValue(true));

		$this->recruitmentAttachmentService->setRecruitmentAttachmentDao($recruitmentAttachmentDao);

		$return = $this->recruitmentAttachmentService->saveCandidateAttachment($resume);
		$this->assertTrue($return);
	}

	/**
	 * Testing getVacancyAttachments
	 */
	public function testGetVacancyAttachment() {

		$vacancyId = 1;
		$vacancyList = TestDataService::loadObjectList('JobVacancyAttachment', $this->fixture, 'JobVacancyAttachment');
		$testVacancyList = array($vacancyList[0], $vacancyList[1]);

		$recruitmentAttachmentDao = $this->getMockBuilder('RecruitmentAttachmentDao')->getMock();

		$recruitmentAttachmentDao->expects($this->once())
			->method('getVacancyAttachment')
			->will($this->returnValue($testVacancyList));

		$this->recruitmentAttachmentService->setRecruitmentAttachmentDao($recruitmentAttachmentDao);

		$readVacancyList = $this->recruitmentAttachmentService->getVacancyAttachment($vacancyId);

		$this->assertEquals($readVacancyList, $testVacancyList);
	}

	/**
	 * Testing getVacancyAttachments
	 */
	public function testGetCandidateAttachment() {

		$candidateId = 1;
		$candidateList = TestDataService::loadObjectList('JobCandidateAttachment', $this->fixture, 'JobCandidateAttachment');
		$testCandidateList = array($candidateList[0], $candidateList[1]);

		$recruitmentAttachmentDao = $this->getMockBuilder('RecruitmentAttachmentDao')->getMock();

		$recruitmentAttachmentDao->expects($this->once())
			->method('getCandidateAttachment')
			->will($this->returnValue($testCandidateList));

		$this->recruitmentAttachmentService->setRecruitmentAttachmentDao($recruitmentAttachmentDao);

		$readCandidateList = $this->recruitmentAttachmentService->getCandidateAttachment($candidateId);

		$this->assertEquals($readCandidateList, $testCandidateList);
	}

	public function testGetRecruitmentAttachmentDao() {

		$dao = $this->recruitmentAttachmentService->getRecruitmentAttachmentDao();
		$this->assertTrue($dao instanceof RecruitmentAttachmentDao);
	}

	public function testGetAttachmentForCandidate() {

		$id = 1;
		$screen = "CANDIDATE";
		$candidateList = TestDataService::loadObjectList('JobCandidateAttachment', $this->fixture, 'JobCandidateAttachment');
		$recruitmentAttachmentDao = $this->getMockBuilder('RecruitmentAttachmentDao')->getMock();

		$recruitmentAttachmentDao->expects($this->once())
			->method('getCandidateAttachment')
			->will($this->returnValue($candidateList));

		$this->recruitmentAttachmentService->setRecruitmentAttachmentDao($recruitmentAttachmentDao);

		$attachment = $this->recruitmentAttachmentService->getAttachment($id, $screen);
		$this->assertEquals($attachment, $candidateList);
	}

	public function testGetAttachmentForVacancy() {

		$id = 1;
		$screen = "VACANCY";
		$candidateList = TestDataService::loadObjectList('JobVacancyAttachment', $this->fixture, 'JobVacancyAttachment');
		$recruitmentAttachmentDao = $this->getMockBuilder('RecruitmentAttachmentDao')->getMock();

		$recruitmentAttachmentDao->expects($this->once())
			->method('getVacancyAttachment')
			->will($this->returnValue($candidateList));

		$this->recruitmentAttachmentService->setRecruitmentAttachmentDao($recruitmentAttachmentDao);
		$attachment = $this->recruitmentAttachmentService->getAttachment($id, $screen);
		$this->assertEquals($attachment, $candidateList);
	}

	public function testGetAttachmentForInterview() {

		$id = 1;
		$screen = "INTERVIEW";
		$candidateList = TestDataService::loadObjectList('JobInterviewAttachment', $this->fixture, 'JobInterviewAttachment');
		$recruitmentAttachmentDao = $this->getMockBuilder('RecruitmentAttachmentDao')->getMock();

		$recruitmentAttachmentDao->expects($this->once())
			->method('getInterviewAttachment')
			->will($this->returnValue($candidateList));

		$this->recruitmentAttachmentService->setRecruitmentAttachmentDao($recruitmentAttachmentDao);
		$attachment = $this->recruitmentAttachmentService->getAttachment($id, $screen);
		$this->assertEquals($attachment, $candidateList);
	}

	public function testGetAttachmentForInvalidScreen() {

		$id = 1;
		$screen = "INVALID";
		$attachment = $this->recruitmentAttachmentService->getAttachment($id, $screen);
		$this->assertFalse($attachment);
	}

	public function testGetAttachmentsForVacancy() {

		$id = 1;
		$screen = "VACANCY";
		$candidateList = TestDataService::loadObjectList('JobVacancyAttachment', $this->fixture, 'JobVacancyAttachment');
		$recruitmentAttachmentDao = $this->getMockBuilder('RecruitmentAttachmentDao')->getMock();

		$recruitmentAttachmentDao->expects($this->once())
			->method('getVacancyAttachments')
			->will($this->returnValue($candidateList));

		$this->recruitmentAttachmentService->setRecruitmentAttachmentDao($recruitmentAttachmentDao);
		$attachment = $this->recruitmentAttachmentService->getAttachments($id, $screen);
		$this->assertEquals($attachment, $candidateList);
	}

	public function testGetAttachmentsForInterview() {

		$id = 1;
		$screen = "INTERVIEW";
		$candidateList = TestDataService::loadObjectList('JobInterviewAttachment', $this->fixture, 'JobInterviewAttachment');
		$recruitmentAttachmentDao = $this->getMockBuilder('RecruitmentAttachmentDao')->getMock();

		$recruitmentAttachmentDao->expects($this->once())
			->method('getInterviewAttachments')
			->will($this->returnValue($candidateList));

		$this->recruitmentAttachmentService->setRecruitmentAttachmentDao($recruitmentAttachmentDao);
		$attachment = $this->recruitmentAttachmentService->getAttachments($id, $screen);
		$this->assertEquals($attachment, $candidateList);
	}

	public function testGetAttachmentsForInvalidScreen() {

		$id = 1;
		$screen = "INVALID";
		$attachment = $this->recruitmentAttachmentService->getAttachments($id, $screen);
		$this->assertFalse($attachment);
	}

	public function testGetNewAttachmentForVacancy() {

		$id = 1;
		$screen = "VACANCY";
		$attach = $this->recruitmentAttachmentService->getNewAttachment($screen, $id);
		$this->assertTrue($attach instanceof JobVacancyAttachment);
		$this->assertEquals($attach->getVacancyId(), 1);
	}

	public function testGetNewAttachmentForInterview() {

		$id = 1;
		$screen = "INTERVIEW";
		$attach = $this->recruitmentAttachmentService->getNewAttachment($screen, $id);
		$this->assertTrue($attach instanceof JobInterviewAttachment);
		$this->assertEquals($attach->getInterviewId(), 1);
	}

}
