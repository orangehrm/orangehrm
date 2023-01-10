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

use OrangeHRM\Claim\Dao\ClaimEventDao;
use OrangeHRM\Claim\Dto\ClaimEventSearchFilterParams;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\ClaimEvent;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

class ClaimEventDaoTest extends KernelTestCase
{
    private ClaimEventDao $claimEventDao;

    protected function setUp(): void
    {
        $this->claimEventDao = new ClaimEventDao();
        $fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmClaimPlugin/test/fixtures/ClaimEvent.yml';
        TestDataService::populate($fixture);
    }

    public function testSaveEvent(): void
    {
        $claimEvent = new ClaimEvent();
        $claimEvent->setName("testname2");
        $claimEvent->setStatus(true);
        $result = $this->claimEventDao->saveEvent($claimEvent);
        $this->assertEquals("testname2", $result->getName());
        $this->assertEquals(true, $result->getStatus());
    }

    public function testGetClaimEventList(): void
    {
        $claimEventSearchFilterParams = new ClaimEventSearchFilterParams();
        $claimEventSearchFilterParams->setName(null);
        $claimEventSearchFilterParams->setStatus(null);
        $result = $this->claimEventDao->getClaimEventList($claimEventSearchFilterParams);
        $this->assertEquals("event1", $result[0]->getName());
    }

    public function testGetClaimEventById(): void
    {
        $result = $this->claimEventDao->getClaimEventById(1);
        $this->assertEquals("event1", $result->getName());
    }

    public function testDeleteClaimEvents(): void
    {
        $result = $this->claimEventDao->deleteClaimEvents([1,2]);
        $this->assertEquals(2, $result);
    }

    public function testCountClaimEvents(): void
    {
        $claimEventSearchFilterParams = new ClaimEventSearchFilterParams();
        $claimEventSearchFilterParams->setName(null);
        $claimEventSearchFilterParams->setStatus(null);
        $result = $this->claimEventDao->getClaimEventCount($claimEventSearchFilterParams);
        $this->assertEquals(4, $result);

        $claimEventSearchFilterParams->setName("event1");
        $result = $this->claimEventDao->getClaimEventCount($claimEventSearchFilterParams);
        $this->assertEquals(1, $result);
    }
}
