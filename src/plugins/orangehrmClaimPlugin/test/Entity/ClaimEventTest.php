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

use OrangeHRM\Config\Config;
use OrangeHRM\Entity\ClaimAttachment;
use OrangeHRM\Entity\ClaimEvent;
use OrangeHRM\Entity\User;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Claim
 * @group Entity
 */
class ClaimEventTest extends EntityTestCase
{
    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmClaimPlugin/test/fixtures/ClaimEvent.yaml';
        TestDataService::populate($fixture);
        TestDataService::truncateSpecificTables([ClaimAttachment::class]);
    }

    public function testEntity(): void
    {
        $claimEvent = new ClaimEvent();
        $claimEvent->setId(6);
        $claimEvent->setName('medical expense claim');
        $claimEvent->setDescription('sample medical claim expense request');
        $claimEvent->setUser($this->getReference(User::class, 1));
        $claimEvent->setStatus(true);
        $claimEvent->isDeleted();
        $this->persist($claimEvent);

        $this->assertEquals(6, $claimEvent->getId());
        $this->assertEquals('medical expense claim', $claimEvent->getName());
        $this->assertEquals('sample medical claim expense request', $claimEvent->getDescription());
        $this->assertTrue($claimEvent->getStatus());
        $this->assertFalse($claimEvent->isDeleted());
        $this->assertEquals(1, $claimEvent->getUser()->getId());
    }
}
