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

namespace OrangeHRM\Tests\Claim\Entity;

use DateTime;
use OrangeHRM\Entity\ClaimAttachment;
use OrangeHRM\Entity\User;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Config\Config;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Claim
 * @group Entity
 */
class ClaimAttachmentTest extends EntityTestCase
{
    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmClaimPlugin/test/fixtures/ClaimAttachment.yaml';
        TestDataService::populate($fixture);
        TestDataService::truncateSpecificTables([ClaimAttachment::class]);
    }

    public function testEntity(): void
    {
        $claimAttachment = new ClaimAttachment();
        $claimAttachment->setRequestId(1);
        $claimAttachment->setAttachId(1);
        $claimAttachment->setSize(100);
        $claimAttachment->setDescription('bill for marketing expenses');
        $claimAttachment->setFilename('bill_no01');
        $claimAttachment->setAttachment('text');
        $claimAttachment->setFileType('text/plain');
        $claimAttachment->setUser($this->getReference(User::class, 1));
        $claimAttachment->setAttachedDate(new DateTime('2023-06-08'));
        $this->persist($claimAttachment);

        $this->assertEquals('1', $claimAttachment->getAttachId());
        $this->assertEquals('1', $claimAttachment->getRequestId());
        $this->assertEquals('100', $claimAttachment->getSize());
        $this->assertEquals('bill for marketing expenses', $claimAttachment->getDescription());
        $this->assertEquals('bill_no01', $claimAttachment->getFilename());
        $this->assertEquals('text', $claimAttachment->getAttachment());
        $this->assertEquals('text/plain', $claimAttachment->getFileType());
        $this->assertEquals('2023-06-08', $claimAttachment->getAttachedDate()->format('Y-m-d'));
    }
}
