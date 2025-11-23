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

namespace OrangeHRM\Tests\Claim\Entity;

use DateTime;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\ClaimAttachment;
use OrangeHRM\Entity\ClaimEvent;
use OrangeHRM\Entity\ClaimRequest;
use OrangeHRM\Entity\CurrencyType;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Claim
 * @group Entity
 */
class ClaimRequestTest extends EntityTestCase
{
    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmClaimPlugin/test/fixtures/ClaimRequest.yaml';
        TestDataService::populate($fixture);
        TestDataService::truncateSpecificTables([ClaimAttachment::class]);
    }

    public function testEntity(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->method('getNow')
            ->willReturn(new DateTime('2023-06-08 09:20'), new DateTime('2023-06-10 13:20'));
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);

        $claimRequest = new ClaimRequest();
        $claimRequest->setId(1);
        $claimRequest->setEmployee($this->getReference(Employee::class, 1));
        $claimRequest->setUser($this->getReference(User::class, 2));
        $claimRequest->setClaimEvent($this->getReference(ClaimEvent::class, 1));
        $claimRequest->setDescription('sample description for claim request');
        $claimRequest->setCurrencyType($this->getReference(CurrencyType::class, 'USD'));
        $claimRequest->setIsDeleted(false);
        $claimRequest->setStatus('INITIATED');
        $claimRequest->setCreatedDate($dateTimeHelper->getNow());
        $claimRequest->setSubmittedDate($dateTimeHelper->getNow());

        $this->assertEquals(1, $claimRequest->getId());
        $this->assertEquals('Kayla', $claimRequest->getEmployee()->getFirstName());
        $this->assertEquals(2, $claimRequest->getUser()->getId());
        $this->assertEquals('Office Rent', $claimRequest->getClaimEvent()->getName());
        $this->assertEquals('USD', $claimRequest->getCurrencyType()->getId());
        $this->assertEquals('INITIATED', $claimRequest->getStatus());
        $this->assertFalse($claimRequest->isDeleted());
        $this->assertEquals('2023-06-08', $claimRequest->getCreatedDate()->format('Y-m-d'));
        $this->assertEquals('2023-06-10', $claimRequest->getSubmittedDate()->format('Y-m-d'));
    }
}
