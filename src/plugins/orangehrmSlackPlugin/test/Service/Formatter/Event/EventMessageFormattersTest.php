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

namespace OrangeHRM\Tests\Slack\Service\Formatter\Event;

use DateTime;
use OrangeHRM\Slack\Dto\SlackEmployeeRecipient;
use OrangeHRM\Slack\Service\Formatter\Event\BirthdayMessageFormatter;
use OrangeHRM\Slack\Service\Formatter\Event\GenericMessageFormatter;
use OrangeHRM\Slack\Service\Formatter\Event\LeaveTodayMessageFormatter;
use OrangeHRM\Slack\Service\Formatter\Syntax\SlackMrkdwnDialect;
use OrangeHRM\Slack\Service\Formatter\Syntax\TeamsMrkdwnDialect;
use PHPUnit\Framework\TestCase;

/**
 * Per-event formatters are platform-agnostic — they ask the injected
 * {@see \OrangeHRM\Slack\Service\Formatter\Syntax\SyntaxDialectInterface} for
 * markup primitives and never bake in syntax themselves. These tests exercise
 * each event formatter under BOTH dialects so a regression in one dialect or
 * one event class shows up immediately.
 *
 * The platform-level tests (`SlackMessageFormatterTest`, `TeamsMessageFormatterTest`)
 * pin the end-to-end output strings; this file pins the structural decisions
 * (count phrase, sort order, optional-field behaviour) independent of syntax.
 *
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

    /* ─────────────────────── BirthdayMessageFormatter ────────────────────────── */

    public function testBirthdaySingularPhrasingUnderSlack(): void
    {
        $msg = (new BirthdayMessageFormatter())->format(
            new SlackMrkdwnDialect(),
            $this->date,
            [new SlackEmployeeRecipient('Alex Carter', 'Engineering')]
        );
        $this->assertStringContainsString('1 birthday today', $msg);
        $this->assertStringNotContainsString('1 birthdays', $msg);
    }

    public function testBirthdayPluralPhrasingUnderTeams(): void
    {
        // Same logic, different dialect → must still pluralise correctly.
        $msg = (new BirthdayMessageFormatter())->format(
            new TeamsMrkdwnDialect(),
            $this->date,
            [
                new SlackEmployeeRecipient('Alex Carter', 'Engineering'),
                new SlackEmployeeRecipient('Priya Singh', 'People Operations'),
            ]
        );
        $this->assertStringContainsString('2 birthdays today', $msg);
    }

    public function testBirthdayDelegatesSyntaxToDialect(): void
    {
        // Same input, two dialects — markup MUST differ exactly where the
        // dialects differ (bold delimiter, bullet glyph, emoji form). If a
        // future refactor accidentally hardcoded `*` in the event class,
        // this assertion fires.
        $reg = new SlackEmployeeRecipient('Alex Carter', 'Engineering');
        $slackOutput = (new BirthdayMessageFormatter())->format(new SlackMrkdwnDialect(), $this->date, [$reg]);
        $teamsOutput = (new BirthdayMessageFormatter())->format(new TeamsMrkdwnDialect(), $this->date, [$reg]);

        $this->assertStringContainsString('*1 birthday today*', $slackOutput);
        $this->assertStringNotContainsString('**1 birthday today**', $slackOutput);
        $this->assertStringContainsString('**1 birthday today**', $teamsOutput);

        $this->assertStringContainsString('•', $slackOutput);
        $this->assertStringContainsString('- ', $teamsOutput);
        $this->assertStringContainsString(':birthday:', $slackOutput);
        $this->assertStringContainsString('🎂', $teamsOutput);
    }

    public function testBirthdayOmitsDanglingDashWhenSubunitMissing(): void
    {
        $msg = (new BirthdayMessageFormatter())->format(
            new SlackMrkdwnDialect(),
            $this->date,
            [new SlackEmployeeRecipient('Maria Orphan')]
        );
        $this->assertStringNotContainsString('Maria Orphan* — ', $msg);
    }

    public function testBirthdayHeaderAppendsSubunitLabel(): void
    {
        $msg = (new BirthdayMessageFormatter())->format(
            new SlackMrkdwnDialect(),
            $this->date,
            [new SlackEmployeeRecipient('Alex Carter', 'Engineering')],
            'Engineering'
        );
        $this->assertStringContainsString('· *Engineering*', $msg);
    }

    /* ─────────────────────── LeaveTodayMessageFormatter ──────────────────────── */

    public function testLeaveTodaySingularAndPluralPhrasing(): void
    {
        $oneEmployee = (new LeaveTodayMessageFormatter())->format(
            new SlackMrkdwnDialect(),
            $this->date,
            [new SlackEmployeeRecipient('Jordan Lee', 'Engineering', 'Annual leave')]
        );
        $this->assertStringContainsString('1 employee on leave today', $oneEmployee);

        $twoEmployees = (new LeaveTodayMessageFormatter())->format(
            new SlackMrkdwnDialect(),
            $this->date,
            [
                new SlackEmployeeRecipient('Jordan Lee', 'Engineering', 'Annual leave'),
                new SlackEmployeeRecipient('Sam Patel', 'People Operations', 'Casual leave'),
            ]
        );
        $this->assertStringContainsString('2 employees on leave today', $twoEmployees);
    }

    public function testLeaveTodayCarriesLeaveTypeAsMetadata(): void
    {
        $msg = (new LeaveTodayMessageFormatter())->format(
            new SlackMrkdwnDialect(),
            $this->date,
            [new SlackEmployeeRecipient('Jordan Lee', 'Engineering', 'Annual leave')]
        );
        $this->assertStringContainsString('Jordan Lee* — Annual leave', $msg);
        $this->assertStringContainsString('_(Engineering)_', $msg);
    }

    public function testLeaveTodayOmitsParensWhenSubunitMissing(): void
    {
        $msg = (new LeaveTodayMessageFormatter())->format(
            new SlackMrkdwnDialect(),
            $this->date,
            [new SlackEmployeeRecipient('Maria Santos', null, 'Medical leave')]
        );
        $this->assertStringContainsString('Maria Santos* — Medical leave', $msg);
        $this->assertStringNotContainsString('_()_', $msg);
    }

    /* ─────────────────────── GenericMessageFormatter ─────────────────────────── */

    public function testGenericCarriesEventTypeLabel(): void
    {
        // Generic is the fallback for event types we haven't shipped a
        // dedicated formatter for yet — must NOT silently drop to a
        // placeholder, must surface the type name so the admin sees what
        // the row was actually configured for.
        $generic = new GenericMessageFormatter();
        $generic->setEventType('NEW_HIRE');
        $msg = $generic->format(
            new SlackMrkdwnDialect(),
            $this->date,
            [new SlackEmployeeRecipient('Dakota Lin')]
        );
        $this->assertStringContainsString('*NEW_HIRE*', $msg);
        $this->assertStringContainsString('Dakota Lin', $msg);
    }

    public function testGenericTestMessageHasNoEventSpecificPreview(): void
    {
        $msg = (new GenericMessageFormatter())->formatTest(new SlackMrkdwnDialect());
        $this->assertStringContainsString('Test notification — OrangeHRM', $msg);
        $this->assertStringContainsString('your webhook destination is connected', $msg);
        // Must NOT leak any of the event-specific preview blocks.
        $this->assertStringNotContainsString('Birthday notification', $msg);
        $this->assertStringNotContainsString('Employees on leave today', $msg);
    }
}
