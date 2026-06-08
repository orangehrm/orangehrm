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

namespace OrangeHRM\Tests\WorkspaceNotifications\Entity;

use DateTime;
use OrangeHRM\Entity\WorkspaceNotificationLog;
use OrangeHRM\Entity\WorkspaceNotificationRegistration;
use PHPUnit\Framework\TestCase;

/**
 * @group Slack
 * @group Entity
 */
class SlackLogTest extends TestCase
{
    public function testStatusConstants(): void
    {
        $this->assertSame('SUCCESS', WorkspaceNotificationLog::STATUS_SUCCESS);
        $this->assertSame('FAILED', WorkspaceNotificationLog::STATUS_FAILED);
        $this->assertSame('SKIPPED', WorkspaceNotificationLog::STATUS_SKIPPED);
    }

    public function testDefaultRecipientCountIsZero(): void
    {
        $log = new WorkspaceNotificationLog();
        $this->assertSame(0, $log->getRecipientCount());
    }

    public function testGettersAndSettersRoundTrip(): void
    {
        $registration = new WorkspaceNotificationRegistration();
        $registration->setId(7);

        $log = new WorkspaceNotificationLog();
        $log->setId(101);
        $log->setRegistration($registration);
        $log->setEventType(WorkspaceNotificationRegistration::EVENT_TYPE_BIRTHDAY);
        $log->setStatus(WorkspaceNotificationLog::STATUS_SUCCESS);
        $log->setRecipientCount(3);
        $log->setErrorMessage(null);
        $date = new DateTime('2026-06-02');
        $log->setEventDate($date);
        $createdAt = new DateTime('2026-06-02 09:00:14');
        $log->setCreatedAt($createdAt);

        $this->assertSame(101, $log->getId());
        $this->assertSame($registration, $log->getRegistration());
        $this->assertSame(WorkspaceNotificationRegistration::EVENT_TYPE_BIRTHDAY, $log->getEventType());
        $this->assertSame(WorkspaceNotificationLog::STATUS_SUCCESS, $log->getStatus());
        $this->assertSame(3, $log->getRecipientCount());
        $this->assertNull($log->getErrorMessage());
        $this->assertSame($date, $log->getEventDate());
        $this->assertSame($createdAt, $log->getCreatedAt());
    }

    public function testFailedLogCarriesErrorMessage(): void
    {
        $log = new WorkspaceNotificationLog();
        $log->setStatus(WorkspaceNotificationLog::STATUS_FAILED);
        $log->setErrorMessage('Slack rejected: invalid_token');

        $this->assertSame(WorkspaceNotificationLog::STATUS_FAILED, $log->getStatus());
        $this->assertSame('Slack rejected: invalid_token', $log->getErrorMessage());
    }

    public function testRegistrationCanBeNull(): void
    {
        $log = new WorkspaceNotificationLog();
        $log->setRegistration(null);
        $this->assertNull($log->getRegistration());
    }
}
