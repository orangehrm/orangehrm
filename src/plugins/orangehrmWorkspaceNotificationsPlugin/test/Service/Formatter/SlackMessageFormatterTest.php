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

namespace OrangeHRM\Tests\WorkspaceNotifications\Service\Formatter;

use DateTime;
use OrangeHRM\Entity\WorkspaceNotificationRegistration;
use OrangeHRM\WorkspaceNotifications\Dto\WorkspaceNotificationRecipient;
use OrangeHRM\WorkspaceNotifications\Service\Formatter\SlackMessageFormatter;
use PHPUnit\Framework\TestCase;

/**
 * @group Slack
 * @group Service
 */
class SlackMessageFormatterTest extends TestCase
{
    private SlackMessageFormatter $formatter;
    private DateTime $date;

    protected function setUp(): void
    {
        $this->formatter = new SlackMessageFormatter();
        $this->date = new DateTime('2026-06-02');
    }

    public function testBirthdayWithSingleRecipientPluralises(): void
    {
        $message = $this->formatter->format(
            WorkspaceNotificationRegistration::EVENT_TYPE_BIRTHDAY,
            $this->date,
            [new WorkspaceNotificationRecipient('Alex Carter', 'Engineering')]
        );

        $this->assertStringContainsString('1 birthday today', $message);
        $this->assertStringNotContainsString('1 birthdays today', $message);
        $this->assertStringContainsString('June 2, 2026', $message);
        $this->assertStringContainsString('*Alex Carter*', $message);
        $this->assertStringContainsString('Engineering', $message);
        $this->assertStringContainsString('🎂', $message);
        $this->assertStringContainsString('🎉', $message);
        $this->assertStringNotContainsString(':birthday:', $message);
        $this->assertStringNotContainsString(':tada:', $message);
    }

    public function testBirthdayWithMultipleRecipientsPluralises(): void
    {
        $message = $this->formatter->format(
            WorkspaceNotificationRegistration::EVENT_TYPE_BIRTHDAY,
            $this->date,
            [
                new WorkspaceNotificationRecipient('Alex Carter', 'Engineering'),
                new WorkspaceNotificationRecipient('Priya Singh', 'People Operations'),
                new WorkspaceNotificationRecipient('Maria Santos', null),
            ]
        );

        $this->assertStringContainsString('3 birthdays today', $message);
        $this->assertStringContainsString('Maria Santos', $message);
        $this->assertStringNotContainsString('*Maria Santos* — ', $message);
    }

    public function testBirthdayHeaderAppendsSubunitLabelWhenFiltered(): void
    {
        $message = $this->formatter->format(
            WorkspaceNotificationRegistration::EVENT_TYPE_BIRTHDAY,
            $this->date,
            [new WorkspaceNotificationRecipient('Alex Carter', 'Engineering')],
            'Engineering'
        );

        $this->assertStringContainsString('1 birthday today', $message);
        $this->assertStringContainsString('· *Engineering*', $message);
    }

    public function testLeaveTodaySingularPlural(): void
    {
        $oneRecipient = $this->formatter->format(
            WorkspaceNotificationRegistration::EVENT_TYPE_LEAVE_TODAY,
            $this->date,
            [new WorkspaceNotificationRecipient('Jordan Lee', 'Engineering', 'Annual leave')]
        );
        $this->assertStringContainsString('1 employee on leave today', $oneRecipient);

        $manyRecipients = $this->formatter->format(
            WorkspaceNotificationRegistration::EVENT_TYPE_LEAVE_TODAY,
            $this->date,
            [
                new WorkspaceNotificationRecipient('Jordan Lee', 'Engineering', 'Annual leave'),
                new WorkspaceNotificationRecipient('Sam Patel', 'People Operations', 'Casual leave'),
            ]
        );
        $this->assertStringContainsString('2 employees on leave today', $manyRecipients);
    }

    public function testLeaveTodayShowsLeaveTypeAndSubunit(): void
    {
        $message = $this->formatter->format(
            WorkspaceNotificationRegistration::EVENT_TYPE_LEAVE_TODAY,
            $this->date,
            [new WorkspaceNotificationRecipient('Jordan Lee', 'Engineering', 'Annual leave')]
        );

        $this->assertStringContainsString('*Jordan Lee*', $message);
        $this->assertStringContainsString('— Annual leave', $message);
        $this->assertStringContainsString('_(Engineering)_', $message);
        $this->assertStringContainsString('🌴', $message);
        $this->assertStringNotContainsString(':palm_tree:', $message);
    }

    public function testLeaveTodayOmitsParensWhenNoSubunit(): void
    {
        $message = $this->formatter->format(
            WorkspaceNotificationRegistration::EVENT_TYPE_LEAVE_TODAY,
            $this->date,
            [new WorkspaceNotificationRecipient('Maria Santos', null, 'Medical leave')]
        );

        $this->assertStringContainsString('*Maria Santos* — Medical leave', $message);
        $this->assertStringNotContainsString('_()_', $message);
    }

    public function testFormatTestMessageBirthdayContainsTestIndicators(): void
    {
        $message = $this->formatter->formatTestMessage(WorkspaceNotificationRegistration::EVENT_TYPE_BIRTHDAY);

        $this->assertStringContainsString('🧪', $message);
        $this->assertStringContainsString('Test notification — OrangeHRM', $message);
        $this->assertStringContainsString('No action is required', $message);
        $this->assertStringContainsString('Preview — Birthday notification', $message);
        $this->assertStringContainsString('Alex Carter', $message);
    }

    public function testFormatTestMessageLeaveTodayContainsPreview(): void
    {
        $message = $this->formatter->formatTestMessage(WorkspaceNotificationRegistration::EVENT_TYPE_LEAVE_TODAY);

        $this->assertStringContainsString('🧪', $message);
        $this->assertStringContainsString('Preview — Employees on leave today', $message);
        $this->assertStringContainsString('Annual leave', $message);
        $this->assertStringContainsString('Casual leave', $message);
    }

    public function testFormatTestMessageUnknownEventTypeFallsBackGracefully(): void
    {
        $message = $this->formatter->formatTestMessage('NOT_A_REAL_EVENT_TYPE');

        $this->assertStringContainsString('🧪', $message);
        $this->assertStringContainsString('Test notification — OrangeHRM', $message);
        $this->assertStringContainsString('your webhook destination is connected', $message);
        $this->assertStringNotContainsString('Birthday notification', $message);
        $this->assertStringNotContainsString('Employees on leave today', $message);
    }

    public function testUnknownEventTypeUsesGenericFormatter(): void
    {
        $message = $this->formatter->format(
            'NEW_HIRE',
            $this->date,
            [new WorkspaceNotificationRecipient('Dakota Lin')]
        );

        $this->assertStringContainsString('*NEW_HIRE*', $message);
        $this->assertStringContainsString('Dakota Lin', $message);
    }
}
