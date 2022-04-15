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
class RecruitmentAttachmentDaoTest extends PHPUnit_Framework_TestCase {

    private $recruitmentAttachmentDao;
    protected $fixture;

    /**
     * Set up method
     */
    protected function setUp() {

        $this->recruitmentAttachmentDao = new RecruitmentAttachmentDao();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmRecruitmentPlugin/test/fixtures/CandidateDao.yml';
        TestDataService::populate($this->fixture);
    }

    /**
     * 
     */
    public function testSaveVacancyAttachment() {

        $file = tmpfile();
        fwrite($file, "writing to tempfile");
        fseek($file, 0);
        $resume = new JobVacancyAttachment();
        $resume->id = 5;
        $resume->vacancyId = 1;
        $resume->fileName = "abc.txt";
        $resume->fileType = ".txt";
        $resume->fileSize = '512';
        $this->recruitmentAttachmentDao->saveVacancyAttachment($resume);

        $resume = TestDataService::fetchObject('JobVacancyAttachment', 5);
        $this->assertNotNull($resume->getId());
        $this->assertEquals($resume->getFileName(), "abc.txt");
        $this->assertEquals($resume->getFileType(), ".txt");
        $this->assertEquals($resume->getFileSize(), '512');
    }

    /**
     *
     */
    public function testSaveVacancyAttachmentForNullId() {

        TestDataService::truncateSpecificTables(array('JobVacancyAttachment'));

        $file = tmpfile();
        fwrite($file, "writing to tempfile");
        fseek($file, 0);
        $resume = new JobVacancyAttachment();
        $resume->setId(null);
        $resume->setVacancyId(1);
        $resume->setFileType('.txt');
        $resume->setFileName('xyz.txt');
        $resume->setFileSize('512');
        $return = $this->recruitmentAttachmentDao->saveVacancyAttachment($resume);
        $this->assertTrue($return);
    }

    /**
     *
     */
    public function testSaveCandidateAttachment() {

        $file = tmpfile();
        fwrite($file, "writing to tempfile");
        fseek($file, 0);
        $resume = new JobCandidateAttachment();
        $resume->id = 5;
        $resume->candidateId = 1;
        $resume->fileName = "abc.txt";
        $resume->fileType = ".txt";
        $resume->fileSize = '512';
        $this->recruitmentAttachmentDao->saveCandidateAttachment($resume);

        $resume = TestDataService::fetchObject('JobCandidateAttachment', 5);
        $this->assertNotNull($resume->getId());
        $this->assertEquals($resume->getFileName(), "abc.txt");
        $this->assertEquals($resume->getFileType(), ".txt");
        $this->assertEquals($resume->getFileSize(), '512');
    }

    /**
     * 
     */
    public function testSaveCandidateAttachmentForNullId() {
        TestDataService::truncateSpecificTables(array('JobCandidateAttachment'));

        $file = tmpfile();
        fwrite($file, "writing to tempfile");
        fseek($file, 0);
        $resume = new JobCandidateAttachment();
        $resume->setId(null);
        $resume->setCandidateId(1);
        $resume->setFileName('xyz.txt');
        $resume->setFileType('.txt');
        $resume->setFileSize('512');
        $return = $this->recruitmentAttachmentDao->saveCandidateAttachment($resume);
        $this->assertTrue($return);
    }

    /**
     * Testing getVacancyList
     */
    public function testGetVacancyAttachments() {

        $vacancyId = 1;
        $vacancyList = $this->recruitmentAttachmentDao->getVacancyAttachments($vacancyId);
        $this->assertTrue($vacancyList[0] instanceof JobVacancyAttachment);
        $this->assertEquals(sizeof($vacancyList), 2);
    }

    public function testGetInterviewAttachments() {

        $interviewId = 1;
        $attachments = $this->recruitmentAttachmentDao->getInterviewAttachments($interviewId);
        $this->assertTrue($attachments[0] instanceof JobInterviewAttachment);
        $this->assertEquals(sizeof($attachments), 2);
    }

    public function testGetVacancyAttachment() {

        $attachId = 1;
        $attachment = $this->recruitmentAttachmentDao->getVacancyAttachment($attachId);
        $this->assertTrue($attachment instanceof JobVacancyAttachment);
        $this->assertEquals($attachment->fileName, 'xyz.txt');
        $this->assertEquals($attachment->fileSize, 512);
    }

    public function testGetInterviewAttachment() {

        $attachId = 1;
        $attachment = $this->recruitmentAttachmentDao->getInterviewAttachment($attachId);
        $this->assertTrue($attachment instanceof JobInterviewAttachment);
        $this->assertEquals($attachment->fileName, 'resume.pdf');
        $this->assertEquals($attachment->fileSize, 512);
    }

    public function testGetCandidateAttachment() {

        $attachId = 1;
        $attachment = $this->recruitmentAttachmentDao->getCandidateAttachment($attachId);
        $this->assertTrue($attachment instanceof JobCandidateAttachment);
        $this->assertEquals($attachment->fileName, 'xyz.txt');
        $this->assertEquals($attachment->fileSize, 512);
    }

}

