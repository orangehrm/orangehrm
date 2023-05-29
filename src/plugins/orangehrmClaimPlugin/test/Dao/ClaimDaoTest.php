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
use OrangeHRM\Entity\ClaimEvent;
use OrangeHRM\Entity\ClaimRequest;
use OrangeHRM\Entity\CurrencyType;
use OrangeHRM\Entity\ExpenseType;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Claim
 * @group Dao
 */
class ClaimDaoTest extends KernelTestCase
{
    private ClaimDao $claimDao;

    protected function setUp(): void
    {
        $this->claimDao = new ClaimDao();
        $claimEventFixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmClaimPlugin/test/fixtures/ClaimEvent.yaml';
        TestDataService::populate($claimEventFixture);
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
        $claimEventSearchFilterParams->setId(null);
        $result = $this->claimDao->getClaimEventList($claimEventSearchFilterParams);
        $this->assertEquals("Auto insurance claim", $result[0]->getName());
    }

    public function testGetClaimEventById(): void
    {
        $result = $this->claimDao->getClaimEventById(4);
        $this->assertEquals("Auto insurance claim", $result->getName());
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
        $claimEventSearchFilterParams->setId(null);
        $result = $this->claimDao->getClaimEventCount($claimEventSearchFilterParams);
        $this->assertEquals(4, $result);

        $claimEventSearchFilterParams->setName("Auto insurance claim");
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

    public function testSaveClaimRequest(): void
    {
        $claimEvent = new ClaimEvent();
        $claimEvent->setName("sample claim event");
        $claimEvent->setStatus(true);
        $claimEvent->getDecorator()->setUserByUserId(1);
        $this->claimDao->saveEvent($claimEvent);

        $currencyType = new CurrencyType();
        $currencyType->setCode(151);
        $currencyType->setId('USD');
        $currencyType->setName('United States Dollar');

        $claimRequest = new ClaimRequest();
        $claimRequest->setClaimEvent($claimEvent);
        $claimRequest->setReferenceId('202301180000005');
        $claimRequest->setCurrencyType($currencyType);
        $claimRequest->setDescription('sample description for claim request');
        $result = $this->claimDao->saveClaimRequest($claimRequest);
        $this->assertEquals('sample claim event', $result->getClaimEvent()->getName());
    }

    public function testGetNextId(): void
    {
        $result = $this->claimDao->getNextId();
        $this->assertIsInt($result);
    }

    public function testIsClaimEventUsed(): void
    {
        $eventId = 1;
        $result = $this->claimDao->isClaimEventUsed($eventId);
        $this->assertEquals(false, $result);
    }
}
