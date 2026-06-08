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

namespace OrangeHRM\Tests\WorkspaceNotifications\Dto;

use OrangeHRM\WorkspaceNotifications\Dto\WorkspaceNotificationRecipient;
use PHPUnit\Framework\TestCase;

/**
 * @group Slack
 * @group Dto
 */
class WorkspaceNotificationRecipientTest extends TestCase
{
    public function testNameOnlyConstruction(): void
    {
        $r = new WorkspaceNotificationRecipient('Alex Carter');

        $this->assertSame('Alex Carter', $r->getFullName());
        $this->assertNull($r->getSubunit());
        $this->assertNull($r->getMetadata());
    }

    public function testNameWithSubunit(): void
    {
        $r = new WorkspaceNotificationRecipient('Priya Singh', 'People Operations');

        $this->assertSame('Priya Singh', $r->getFullName());
        $this->assertSame('People Operations', $r->getSubunit());
        $this->assertNull($r->getMetadata());
    }

    public function testNameWithSubunitAndMetadata(): void
    {
        $r = new WorkspaceNotificationRecipient('Jordan Lee', 'Engineering', 'Annual leave');

        $this->assertSame('Jordan Lee', $r->getFullName());
        $this->assertSame('Engineering', $r->getSubunit());
        $this->assertSame('Annual leave', $r->getMetadata());
    }

    public function testMetadataWithoutSubunit(): void
    {
        $r = new WorkspaceNotificationRecipient('Sam Patel', null, 'Casual leave');

        $this->assertSame('Sam Patel', $r->getFullName());
        $this->assertNull($r->getSubunit());
        $this->assertSame('Casual leave', $r->getMetadata());
    }
}
