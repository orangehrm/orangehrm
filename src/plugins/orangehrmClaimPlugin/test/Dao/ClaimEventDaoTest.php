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
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\ClaimEvent;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

class ClaimEventDaoTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmClaimPlugin/test/fixtures/ClaimEvent.yaml';
        TestDataService::populate($fixture);
    }
    public function testSaveEvent(): void
    {
        $claimEvent=new ClaimEvent();
        $claimEvent->setName("testname2");
        $claimEvent->setStatus(true);
        $dao=new ClaimEventDao();
        $result=$dao->saveEvent($claimEvent);
        $this->assertEquals("testname2", $result->getName());
        $this->assertEquals(true, $result->getStatus());
    }
}
