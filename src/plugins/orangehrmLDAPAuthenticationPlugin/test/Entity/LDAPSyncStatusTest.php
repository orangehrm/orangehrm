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

namespace OrangeHRM\Tests\LDAP\Entity;

use DateTime;
use OrangeHRM\Entity\LDAPSyncStatus;
use OrangeHRM\Entity\User;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group LDAP
 * @group Entity
 */
class LDAPSyncStatusTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([LDAPSyncStatus::class]);
    }

    public function testLDAPSyncEntity(): void
    {
        //LDAP is not configured
        $ldapSyncStatus = new LDAPSyncStatus();
        $this->assertInstanceOf(LDAPSyncStatus::class, $ldapSyncStatus);
        $this->assertEquals(null, $ldapSyncStatus->getSyncedBy());
        $this->assertEquals(LDAPSyncStatus::SYNC_STATUS_NOT_AVAILABLE, $ldapSyncStatus->getSyncStatus());
        $this->assertEquals(null, $ldapSyncStatus->getSyncStartedAt());
        $this->assertEquals(null, $ldapSyncStatus->getSyncFinishedAt());

        //Sync Successful
        $ldapSyncStatus = new LDAPSyncStatus();
        $ldapSyncStatus->setSyncStartedAt(new DateTime('2022-10-12 01:31'));
        $ldapSyncStatus->setSyncFinishedAt(new DateTime('2022-10-12 01:32'));
        $ldapSyncStatus->getDecorator()->setSyncedUserByUserId(1);
        $ldapSyncStatus->setSyncStatus(LDAPSyncStatus::SYNC_STATUS_SUCCEEDED);

        $this->persist($ldapSyncStatus);

        $ldapSyncStatus = $this->getRepository(LDAPSyncStatus::class)->find(1);
        $this->assertInstanceOf(LDAPSyncStatus::class, $ldapSyncStatus);
        $this->assertInstanceOf(User::class, $ldapSyncStatus->getSyncedBy());
        $this->assertEquals(LDAPSyncStatus::SYNC_STATUS_SUCCEEDED, $ldapSyncStatus->getSyncStatus());
        $this->assertEquals(new DateTime('2022-10-12 01:31'), $ldapSyncStatus->getSyncStartedAt());
        $this->assertEquals(new DateTime('2022-10-12 01:32'), $ldapSyncStatus->getSyncFinishedAt());


        //Sync Failed
        $ldapSyncStatus = new LDAPSyncStatus();
        $ldapSyncStatus->setSyncStartedAt(new DateTime('2022-10-12 02:31'));
        $ldapSyncStatus->getDecorator()->setSyncedUserByUserId(1);
        $ldapSyncStatus->setSyncStatus(LDAPSyncStatus::SYNC_STATUS_FAILED);

        $this->persist($ldapSyncStatus);

        $ldapSyncStatus = $this->getRepository(LDAPSyncStatus::class)->find(2);
        $this->assertInstanceOf(LDAPSyncStatus::class, $ldapSyncStatus);
        $this->assertInstanceOf(User::class, $ldapSyncStatus->getSyncedBy());
        $this->assertEquals(LDAPSyncStatus::SYNC_STATUS_FAILED, $ldapSyncStatus->getSyncStatus());
        $this->assertEquals(new DateTime('2022-10-12 02:31'), $ldapSyncStatus->getSyncStartedAt());
        $this->assertEquals(null, $ldapSyncStatus->getSyncFinishedAt());
    }
}
