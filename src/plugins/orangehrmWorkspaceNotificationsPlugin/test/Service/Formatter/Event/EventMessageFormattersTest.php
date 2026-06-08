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

namespace OrangeHRM\Tests\WorkspaceNotifications\Service\Formatter\Event;

use DateTime;
use OrangeHRM\WorkspaceNotifications\Dto\WorkspaceNotificationRecipient;
use OrangeHRM\WorkspaceNotifications\Service\Formatter\Event\BirthdayMessageFormatter;
use OrangeHRM\WorkspaceNotifications\Service\Formatter\Event\GenericMessageFormatter;
use OrangeHRM\WorkspaceNotifications\Service\Formatter\Event\LeaveTodayMessageFormatter;
use OrangeHRM\WorkspaceNotifications\Service\Formatter\Syntax\SlackMrkdwnDialect;
use OrangeHRM\WorkspaceNotifications\Service\Formatter\Syntax\TeamsMrkdwnDialect;
use PHPUnit\Framework\TestCase;

/**
 * @group Slack
 * @group Service
 */
class EventMessageFormattersTest extends TestCase
{
    private DateTime $date;

    protected function setUp(): void
    {
        $this->date = new DateTime('2026-06-02');
    }

    public function testBirthdaySingularPhrasingUnderSlack(): void
    {
        $msg = (new BirthdayMessageFormatter())->format(
            new SlackMrkdwnDialect(),
            $this->date,
            [new WorkspaceNotificationRecipient('Alex Carter', 'Engineering')]
        );
        $this->assertStringContainsString('1 birthday today', $msg);
        $this->assertStringNotContainsString('1 birthdays', $msg);
    }

    public function testBirthdayPluralPhrasingUnderTeams(): void
    {
        $msg = (new BirthdayMessageFormatter())->format(
            new TeamsMrkdwnDialect(),
            $this->date,
            [
                new WorkspaceNotificationRecipient('Alex Carter', 'Engineering'),
                new WorkspaceNotificationRecipient('Priya Singh', 'People Operations'),
            ]
        );
        $this->assertStringContainsString('2 birthdays today', $msg);
    }

    public function testBirthdayDelegatesSyntaxToDialect(): void
    {
        $reg = new WorkspaceNotificationRecipient('Alex Carter', 'Engineering');
        $slackOutput = (new BirthdayMessageFormatter())->format(new SlackMrkdwnDialect(), $this->date, [$reg]);
        $teamsOutput = (new BirthdayMessageFormatter())->format(new TeamsMrkdwnDialect(), $this->date, [$reg]);

        $this->assertStringContainsString('*1 birthday today*', $slackOutput);
        $this->assertStringNotContainsString('**1 birthday today**', $slackOutput);
        $this->assertStringContainsString('**1 birthday today**', $teamsOutput);

        $this->assertStringContainsString('•', $slackOutput);
        $this->assertStringContainsString('- ', $teamsOutput);
        $this->assertStringContainsString('🎂', $slackOutput);
        $this->assertStringContainsString('🎂', $teamsOutput);
        $this->assertStringNotContainsString(':birthday:', $slackOutput);
    }

    public function testBirthdayOmitsDanglingDashWhenSubunitMissing(): void
    {
        $msg = (new BirthdayMessageFormatter())->format(
            new SlackMrkdwnDialect(),
            $this->date,
            [new WorkspaceNotificationRecipient('Maria Orphan')]
        );
        $this->assertStringNotContainsString('Maria Orphan* — ', $msg);
    }

    public function testBirthdayHeaderAppendsSubunitLabel(): void
    {
        $msg = (new BirthdayMessageFormatter())->format(
            new SlackMrkdwnDialect(),
            $this->date,
            [new WorkspaceNotificationRecipient('Alex Carter', 'Engineering')],
            'Engineering'
        );
        $this->assertStringContainsString('· *Engineering*', $msg);
    }

    public function testLeaveTodaySingularAndPluralPhrasing(): void
    {
        $oneEmployee = (new LeaveTodayMessageFormatter())->format(
            new SlackMrkdwnDialect(),
            $this->date,
            [new WorkspaceNotificationRecipient('Jordan Lee', 'Engineering', 'Annual leave')]
        );
        $this->assertStringContainsString('1 employee on leave today', $oneEmployee);

        $twoEmployees = (new LeaveTodayMessageFormatter())->format(
            new SlackMrkdwnDialect(),
            $this->date,
            [
                new WorkspaceNotificationRecipient('Jordan Lee', 'Engineering', 'Annual leave'),
                new WorkspaceNotificationRecipient('Sam Patel', 'People Operations', 'Casual leave'),
            ]
        );
        $this->assertStringContainsString('2 employees on leave today', $twoEmployees);
    }

    public function testLeaveTodayCarriesLeaveTypeAsMetadata(): void
    {
        $msg = (new LeaveTodayMessageFormatter())->format(
            new SlackMrkdwnDialect(),
            $this->date,
            [new WorkspaceNotificationRecipient('Jordan Lee', 'Engineering', 'Annual leave')]
        );
        $this->assertStringContainsString('Jordan Lee* — Annual leave', $msg);
        $this->assertStringContainsString('_(Engineering)_', $msg);
    }

    public function testLeaveTodayOmitsParensWhenSubunitMissing(): void
    {
        $msg = (new LeaveTodayMessageFormatter())->format(
            new SlackMrkdwnDialect(),
            $this->date,
            [new WorkspaceNotificationRecipient('Maria Santos', null, 'Medical leave')]
        );
        $this->assertStringContainsString('Maria Santos* — Medical leave', $msg);
        $this->assertStringNotContainsString('_()_', $msg);
    }

    public function testGenericCarriesEventTypeLabel(): void
    {
        $generic = new GenericMessageFormatter();
        $generic->setEventType('NEW_HIRE');
        $msg = $generic->format(
            new SlackMrkdwnDialect(),
            $this->date,
            [new WorkspaceNotificationRecipient('Dakota Lin')]
        );
        $this->assertStringContainsString('*NEW_HIRE*', $msg);
        $this->assertStringContainsString('Dakota Lin', $msg);
    }

    public function testGenericTestMessageHasNoEventSpecificPreview(): void
    {
        $msg = (new GenericMessageFormatter())->formatTest(new SlackMrkdwnDialect());
        $this->assertStringContainsString('Test notification — OrangeHRM', $msg);
        $this->assertStringContainsString('your webhook destination is connected', $msg);
        $this->assertStringNotContainsString('Birthday notification', $msg);
        $this->assertStringNotContainsString('Employees on leave today', $msg);
    }
}
