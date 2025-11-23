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

use OrangeHRM\Entity\Vacancy;
use OrangeHRM\Entity\VacancyAttachment;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Recruitment
 * @group Entity
 */
class VacancyAttachmentTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([VacancyAttachment::class]);
    }

    public function testVacancyAttachmentEntity(): void
    {
        $vacancyAttachment = new VacancyAttachment();
        $vacancyAttachment->setFileName('Attachment6.pdf');
        $vacancyAttachment->getDecorator()->setVacancyById(1);
        $vacancyAttachment->setFileType('application/pdf');
        $vacancyAttachment->setFileSize('14874');
        $vacancyAttachment->setFileContent('attachment_06_content');
        $vacancyAttachment->setAttachmentType(1);
        $vacancyAttachment->setComment('This is the attachment 06');
        $this->persist($vacancyAttachment);

        $vacancyAttachment = $this->getRepository(VacancyAttachment::class)->find(1);
        $this->assertInstanceOf(VacancyAttachment::class, $vacancyAttachment);
        $this->assertEquals('Attachment6.pdf', $vacancyAttachment->getFileName());
        $this->assertEquals('application/pdf', $vacancyAttachment->getFileType());
        $this->assertInstanceOf(Vacancy::class, $vacancyAttachment->getVacancy());
    }
}
