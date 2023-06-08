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

use OrangeHRM\Config\Config;
use OrangeHRM\Entity\ClaimAttachment;
use OrangeHRM\Entity\ExpenseType;
use OrangeHRM\Entity\User;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Claim
 * @group Entity
 */
class ExpenseTypeTest extends EntityTestCase
{
    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmClaimPlugin/test/fixtures/ExpenseType.yaml';
        TestDataService::populate($fixture);
        TestDataService::truncateSpecificTables([ClaimAttachment::class]);
    }

    public function testEntity(): void
    {
        $expenseType = new ExpenseType();
        $expenseType->setId(5);
        $expenseType->setUser($this->getReference(User::class, 1));
        $expenseType->setName('transport expenses');
        $expenseType->setDescription('sample description for transport expense type');
        $expenseType->setStatus(true);
        $expenseType->setIsDeleted(false);
        $this->persist($expenseType);

        $this->assertEquals(5, $expenseType->getId());
        $this->assertEquals('transport expenses', $expenseType->getName());
        $this->assertEquals('sample description for transport expense type', $expenseType->getDescription());
        $this->assertTrue($expenseType->getStatus());
        $this->assertFalse($expenseType->isDeleted());
        $this->assertEquals(1, $expenseType->getUser()->getId());
    }
}
