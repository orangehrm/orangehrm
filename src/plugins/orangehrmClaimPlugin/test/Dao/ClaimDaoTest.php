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
use OrangeHRM\Claim\Dto\ClaimEventSearchFilterParams;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\ClaimEvent;
use OrangeHRM\Entity\ExpenseType;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

class ClaimDaoTest extends KernelTestCase
{
    use EntityManagerHelperTrait;
    private ClaimDao $claimDao;

    protected function setUp(): void
    {
        $this->claimDao = new ClaimDao();
        $fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmClaimPlugin/test/fixtures/ClaimEvent.yml';
        TestDataService::populate($fixture);
    }

    public function testSaveEvent(): void
    {
        $claimEvent = new ClaimEvent();
        $claimEvent->setName("testname2");
        $claimEvent->setStatus(true);
        $claimEvent->getDecorator()->setUserByUserId(1);
        $result = $this->claimDao->saveEvent($claimEvent);
        $this->assertEquals("testname2", $result->getName());
        $this->assertEquals(true, $result->getStatus());
    }

    public function testGetClaimEventList(): void
    {
        $claimEventSearchFilterParams = new ClaimEventSearchFilterParams();
        $claimEventSearchFilterParams->setName(null);
        $claimEventSearchFilterParams->setStatus(null);
        $result = $this->claimDao->getClaimEventList($claimEventSearchFilterParams);
        $this->assertEquals("event1", $result[0]->getName());
    }

    public function testGetClaimEventById(): void
    {
        $result = $this->claimDao->getClaimEventById(1);
        $this->assertEquals("event1", $result->getName());
    }

    public function testDeleteClaimEvents(): void
    {
        $result = $this->claimDao->deleteClaimEvents([1,2]);
        $this->assertEquals(2, $result);
    }

    public function testCountClaimEvents(): void
    {
        $claimEventSearchFilterParams = new ClaimEventSearchFilterParams();
        $claimEventSearchFilterParams->setName(null);
        $claimEventSearchFilterParams->setStatus(null);
        $result = $this->claimDao->getClaimEventCount($claimEventSearchFilterParams);
        $this->assertEquals(4, $result);

        $claimEventSearchFilterParams->setName("event1");
        $result = $this->claimDao->getClaimEventCount($claimEventSearchFilterParams);
        $this->assertEquals(1, $result);
    }

    public function testSaveExpenseType(): void
    {
        $expenseType = new ExpenseType();
        $expenseType->setName('sample expenses');
        $expenseType->setDescription('description for expenses');
        $expenseType->getDecorator()->setUserByUserId(1);
        $result = $this->claimDao->saveExpenseType($expenseType);
        $this->assertEquals('sample expenses', $result->getName());
        $this->assertEquals('1', $result->getUser()->getId());
    }
}
