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
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\ClaimAttachment;
use OrangeHRM\Entity\ClaimExpense;
use OrangeHRM\Entity\ClaimRequest;
use OrangeHRM\Entity\ExpenseType;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Claim
 * @group Entity
 */
class ClaimExpenseTest extends EntityTestCase
{
    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmClaimPlugin/test/fixtures/ClaimExpense.yaml';
        TestDataService::populate($fixture);
        TestDataService::truncateSpecificTables([ClaimAttachment::class]);
    }

    public function testEntity(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->method('getNow')
            ->willReturn(new DateTime('2023-06-08 09:20'));
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);

        $claimExpense = new ClaimExpense();
        $claimExpense->setId(6);
        $claimExpense->setExpenseType($this->getReference(ExpenseType::class, 1));
        $claimExpense->setDate($dateTimeHelper->getNow());
        $claimExpense->setAmount(10.54);
        $claimExpense->setNote('this claim request is for test');
        $claimExpense->setClaimRequest($this->getReference(ClaimRequest::class, 1));
        $claimExpense->setIsDeleted(false);
        $this->persist($claimExpense);

        $this->assertEquals(6, $claimExpense->getId());
        $this->assertEquals('2023-06-08', $claimExpense->getDate()->format('Y-m-d'));
        $this->assertEquals(10.54, $claimExpense->getAmount());
        $this->assertEquals('this claim request is for test', $claimExpense->getNote());
        $this->assertFalse($claimExpense->isDeleted());

        $this->assertEquals(1, $claimExpense->getExpenseType()->getId());
        $this->assertEquals(1, $claimExpense->getClaimRequest()->getId());
    }
}
