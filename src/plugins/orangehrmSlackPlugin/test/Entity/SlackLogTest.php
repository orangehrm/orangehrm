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

namespace OrangeHRM\Tests\Slack\Entity;

use DateTime;
use OrangeHRM\Entity\SlackLog;
use OrangeHRM\Entity\SlackRegistration;
use PHPUnit\Framework\TestCase;

/**
 * @group Slack
 * @group Entity
 */
class SlackLogTest extends TestCase
{
    public function testStatusConstants(): void
    {
        $this->assertSame('SUCCESS', SlackLog::STATUS_SUCCESS);
        $this->assertSame('FAILED', SlackLog::STATUS_FAILED);
        $this->assertSame('SKIPPED', SlackLog::STATUS_SKIPPED);
    }

    public function testDefaultRecipientCountIsZero(): void
    {
        $log = new SlackLog();
        $this->assertSame(0, $log->getRecipientCount());
    }

    public function testGettersAndSettersRoundTrip(): void
    {
        $registration = new SlackRegistration();
        $registration->setId(7);

        $log = new SlackLog();
        $log->setId(101);
        $log->setRegistration($registration);
        $log->setEventType(SlackRegistration::EVENT_TYPE_BIRTHDAY);
        $log->setStatus(SlackLog::STATUS_SUCCESS);
        $log->setRecipientCount(3);
        $log->setErrorMessage(null);
        $date = new DateTime('2026-06-02');
        $log->setEventDate($date);
        $createdAt = new DateTime('2026-06-02 09:00:14');
        $log->setCreatedAt($createdAt);

        $this->assertSame(101, $log->getId());
        $this->assertSame($registration, $log->getRegistration());
        $this->assertSame(SlackRegistration::EVENT_TYPE_BIRTHDAY, $log->getEventType());
        $this->assertSame(SlackLog::STATUS_SUCCESS, $log->getStatus());
        $this->assertSame(3, $log->getRecipientCount());
        $this->assertNull($log->getErrorMessage());
        $this->assertSame($date, $log->getEventDate());
        $this->assertSame($createdAt, $log->getCreatedAt());
    }

    public function testFailedLogCarriesErrorMessage(): void
    {
        $log = new SlackLog();
        $log->setStatus(SlackLog::STATUS_FAILED);
        $log->setErrorMessage('Slack rejected: invalid_token');

        $this->assertSame(SlackLog::STATUS_FAILED, $log->getStatus());
        $this->assertSame('Slack rejected: invalid_token', $log->getErrorMessage());
    }

    public function testRegistrationCanBeNull(): void
    {
        // Defensive: nullable FK on registration_id means orphan logs are tolerated
        $log = new SlackLog();
        $log->setRegistration(null);
        $this->assertNull($log->getRegistration());
    }
}
