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

namespace OrangeHRM\Tests\Recruitment\Entity;

use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Candidate;
use OrangeHRM\Entity\CandidateAttachment;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Recruitment
 * @group Entity
 */
class CandidateAttachmentTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([CandidateAttachment::class]);
        $fixtures = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmRecruitmentPlugin/test/fixtures/CandidateAttachmentEntityTest.yaml';
        TestDataService::populate($fixtures);
    }

    public function testCandidateAttachmentEntity(): void
    {
        $candidateAttachment = new CandidateAttachment();
        $candidateAttachment->setFileName('Attachment1.pdf');
        $candidateAttachment->getDecorator()->setCandidateById(1);
        $candidateAttachment->setFileType('application/pdf');
        $candidateAttachment->setFileSize('14874');
        $candidateAttachment->setAttachmentType(1);
        $candidateAttachment->setFileContent('attachment_01_content');
        $this->persist($candidateAttachment);

        $candidateAttachment = $this->getRepository(CandidateAttachment::class)->find(1);

        $this->assertInstanceOf(CandidateAttachment::class, $candidateAttachment);
        $this->assertInstanceOf(Candidate::class, $candidateAttachment->getCandidate());
        $this->assertEquals('Attachment1.pdf', $candidateAttachment->getFileName());
        $this->assertEquals('application/pdf', $candidateAttachment->getFileType());
    }
}
