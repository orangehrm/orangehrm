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
use Doctrine\Common\Collections\ArrayCollection;
use OrangeHRM\Entity\SlackRegistration;
use OrangeHRM\Entity\Subunit;
use PHPUnit\Framework\TestCase;

/**
 * @group Slack
 * @group Entity
 */
class SlackRegistrationTest extends TestCase
{
    public function testDefaultsOnFreshEntity(): void
    {
        $registration = new SlackRegistration();

        $this->assertSame(SlackRegistration::PROVIDER_SLACK, $registration->getProvider());
        $this->assertSame('UTC', $registration->getTimezone());
        $this->assertSame('09:00', $registration->getDailySendTime());
        $this->assertTrue($registration->isActive());
        $this->assertNull($registration->getChannelLabel());
        $this->assertNull($registration->getCreatedAt());
        $this->assertNull($registration->getUpdatedAt());
        $this->assertInstanceOf(ArrayCollection::class, $registration->getSubunits());
        $this->assertCount(0, $registration->getSubunits());
    }

    public function testEventTypesConstantsCoverPhase1(): void
    {
        $this->assertSame('BIRTHDAY', SlackRegistration::EVENT_TYPE_BIRTHDAY);
        $this->assertSame('LEAVE_TODAY', SlackRegistration::EVENT_TYPE_LEAVE_TODAY);
        $this->assertSame(
            [SlackRegistration::EVENT_TYPE_BIRTHDAY, SlackRegistration::EVENT_TYPE_LEAVE_TODAY],
            SlackRegistration::EVENT_TYPES
        );
    }

    public function testGettersAndSettersRoundTrip(): void
    {
        $registration = new SlackRegistration();
        $registration->setId(42);
        $registration->setProvider('slack');
        $registration->setEventType(SlackRegistration::EVENT_TYPE_BIRTHDAY);
        $registration->setWebhookUrl('cipher://abc');
        $registration->setChannelLabel('#hr-team');
        $registration->setTimezone('Asia/Colombo');
        $registration->setDailySendTime('09:30');
        $registration->setActive(false);
        $now = new DateTime('2026-06-02 10:00:00');
        $registration->setCreatedAt($now);
        $registration->setUpdatedAt($now);

        $this->assertSame(42, $registration->getId());
        $this->assertSame('slack', $registration->getProvider());
        $this->assertSame(SlackRegistration::EVENT_TYPE_BIRTHDAY, $registration->getEventType());
        $this->assertSame('cipher://abc', $registration->getWebhookUrl());
        $this->assertSame('#hr-team', $registration->getChannelLabel());
        $this->assertSame('Asia/Colombo', $registration->getTimezone());
        $this->assertSame('09:30', $registration->getDailySendTime());
        $this->assertFalse($registration->isActive());
        $this->assertSame($now, $registration->getCreatedAt());
        $this->assertSame($now, $registration->getUpdatedAt());
    }

    public function testChannelLabelNullableRoundTrip(): void
    {
        $registration = new SlackRegistration();
        $registration->setChannelLabel(null);
        $this->assertNull($registration->getChannelLabel());

        $registration->setChannelLabel('#everyone');
        $this->assertSame('#everyone', $registration->getChannelLabel());
    }

    public function testAddSubunitAvoidsDuplicates(): void
    {
        $registration = new SlackRegistration();
        $a = $this->subunit(1, 'Engineering');
        $b = $this->subunit(2, 'People Ops');

        $registration->addSubunit($a);
        $registration->addSubunit($b);
        $this->assertCount(2, $registration->getSubunits());

        // Adding the same subunit again must NOT duplicate
        $registration->addSubunit($a);
        $this->assertCount(2, $registration->getSubunits());
    }

    public function testClearSubunitsEmptiesTheCollection(): void
    {
        $registration = new SlackRegistration();
        $registration->addSubunit($this->subunit(1, 'Engineering'));
        $registration->addSubunit($this->subunit(2, 'People Ops'));
        $this->assertCount(2, $registration->getSubunits());

        $registration->clearSubunits();
        $this->assertCount(0, $registration->getSubunits());
    }

    private function subunit(int $id, string $name): Subunit
    {
        $subunit = new Subunit();
        $subunit->setId($id);
        $subunit->setName($name);
        return $subunit;
    }
}
