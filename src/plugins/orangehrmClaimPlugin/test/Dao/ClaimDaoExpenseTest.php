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

namespace OrangeHRM\Tests\Claim\Dao;

use OrangeHRM\Claim\Dao\ClaimDao;
use OrangeHRM\Claim\Dto\ClaimExpenseSearchFilterParams;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\ClaimExpense;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Claim
 * @group Dao
 */
class ClaimDaoExpenseTest extends KernelTestCase
{
    use DateTimeHelperTrait;

    private ClaimDao $claimDao;

    protected function setUp(): void
    {
        $this->claimDao = new ClaimDao();
        $expenseFixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmClaimPlugin/test/fixtures/ClaimExpense.yaml';
        TestDataService::populate($expenseFixture);
    }

    public function testSaveClaimExpense(): void
    {
        $claimExpense = new ClaimExpense();
        $expenseType = $this->claimDao->getExpenseTypeById(1);
        $claimExpense->setExpenseType($expenseType);
        $claimExpense->setAmount(100);
        $claimRequest = $this->claimDao->getClaimRequestById(1);
        $claimExpense->setClaimRequest($claimRequest);
        $result = $this->claimDao->saveClaimExpense($claimExpense);
        $this->assertEquals(6, $result->getId());
    }

    public function testGetClaimExpenseById(): void
    {
        $result = $this->claimDao->getClaimExpenseById(1);
        $this->assertEquals(1, $result->getId());
    }

    public function testGetClaimExpenseList(): void
    {
        $claimRequestId = 1;
        $claimExpenseSearchFilterParams = new ClaimExpenseSearchFilterParams();
        $claimExpenseSearchFilterParams->setRequestId($claimRequestId);
        $result = $this->claimDao->getClaimExpenseList($claimExpenseSearchFilterParams);
        $this->assertCount(2, $result);
    }

    public function testDeleteClaimExpense(): void
    {
        $claimRequestId = 1;
        $result = $this->claimDao->deleteClaimExpense($claimRequestId, [1, 3]);
        $this->assertEquals(1, $result);
    }
}
