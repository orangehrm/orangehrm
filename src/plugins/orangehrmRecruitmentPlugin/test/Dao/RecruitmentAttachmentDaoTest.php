<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Tests\Recruitment\Dao;

use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\VacancyAttachment;
use OrangeHRM\Recruitment\Dao\RecruitmentAttachmentDao;
use OrangeHRM\Recruitment\Dto\RecruitmentAttachment;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Recruitment
 * @group Dao
 */
class RecruitmentAttachmentDaoTest extends KernelTestCase
{
    private RecruitmentAttachmentDao $recruitmentAttachmentDao;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->recruitmentAttachmentDao = new RecruitmentAttachmentDao();
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmRecruitmentPlugin/test/fixtures/RecruitmentAttachmentDao.yaml';
        TestDataService::populate($this->fixture);
    }

    public function testSaveVacancyAttachment(): void
    {
        $vacancyAttachment = new VacancyAttachment();
        $vacancyAttachment->getDecorator()->setVacancyById(1);
        $vacancyAttachment->setFileName('Attachment5');
        $vacancyAttachment->setFileType('text/plain');
        $vacancyAttachment->setFileSize(1200);
        $vacancyAttachment->setFileContent('attachment_05_content');
        $vacancyAttachment->setAttachmentType(1);
        $vacancyAttachment->setComment('This is the attachment 05');
        $result = $this->recruitmentAttachmentDao->saveVacancyAttachment($vacancyAttachment);
        $this->assertInstanceOf(VacancyAttachment::class, $result);
        $this->assertEquals('Attachment5', $result->getFileName());
        $this->assertEquals('attachment_05_content', $result->getFileContent());
    }

    public function testGetVacancyAttachmentContentById(): void
    {
        $attachment = $this->recruitmentAttachmentDao->getVacancyAttachmentById(1);
        $this->assertInstanceOf(VacancyAttachment::class, $attachment);
        $this->assertEquals('Attachment1.pdf', $attachment->getFileName());
        $this->assertEquals('application/pdf', $attachment->getFileType());
    }

    public function testGetVacancyAttachmentsByVacancyId(): void
    {
        $attachments = $this->recruitmentAttachmentDao->getVacancyAttachmentsByVacancyId(1);
        $this->assertCount(2, $attachments);
        $this->assertInstanceOf(RecruitmentAttachment::class, $attachments[0]);
        $this->assertEquals('1', $attachments[0]->getId());
        $this->assertEquals('Attachment1.pdf', $attachments[0]->getFileName());
        $this->assertEquals('application/pdf', $attachments[0]->getFileType());
        $this->assertEquals(1, $attachments[0]->getFkIdentity());
    }

    public function testDeleteVacancyAttachments(): void
    {
        $result = $this->recruitmentAttachmentDao->deleteVacancyAttachments([1, 2]);
        $this->assertTrue($result);
        $attachment = $this->recruitmentAttachmentDao->getVacancyAttachmentById(1);
        $this->assertNull($attachment);
    }
}
