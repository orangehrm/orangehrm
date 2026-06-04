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

namespace OrangeHRM\Tests\Slack\Service\Formatter;

use DateTime;
use OrangeHRM\Entity\SlackRegistration;
use OrangeHRM\Slack\Dto\SlackEmployeeRecipient;
use OrangeHRM\Slack\Service\Formatter\TeamsMessageFormatter;
use PHPUnit\Framework\TestCase;

/**
 * Teams renders MessageCard / workflow bodies with a different markdown subset
 * than Slack. The asserted contract here is the user-visible delta:
 *
 *   - bold uses `**double-asterisks**` (Slack: `*single*`)
 *   - emoji are Unicode glyphs (Slack: `:shortcode:`)
 *   - bullets use `-` (Slack: `•`)
 *
 * If a future change "shares" the Slack formatter with Teams to save code,
 * these assertions all fail loudly — which is the desired behaviour because
 * the output would surface raw `*asterisks*` and `:emoji:` text in the
 * Teams channel.
 *
 * @group Slack
 * @group Service
 */
class TeamsMessageFormatterTest extends TestCase
{
    private TeamsMessageFormatter $formatter;
    private DateTime $date;

    protected function setUp(): void
    {
        $this->formatter = new TeamsMessageFormatter();
        $this->date = new DateTime('2026-06-02');
    }

    /* ───────────────────── Birthday — real notification ─────────────────────── */

    public function testBirthdayMessageUsesDoubleAsteriskBoldAndUnicodeEmoji(): void
    {
        $message = $this->formatter->format(
            SlackRegistration::EVENT_TYPE_BIRTHDAY,
            $this->date,
            [new SlackEmployeeRecipient('Alex Carter', 'Engineering')]
        );

        // Bold uses `**` (Teams) — NOT `*` (Slack)
        $this->assertStringContainsString('**1 birthday today**', $message);
        $this->assertStringNotContainsString('*1 birthday today*', preg_replace('/\*\*/', '', $message));

        // Emoji are Unicode glyphs, NOT `:shortcode:`
        $this->assertStringContainsString('🎂', $message);
        $this->assertStringContainsString('🎉', $message);
        $this->assertStringNotContainsString(':birthday:', $message);
        $this->assertStringNotContainsString(':tada:', $message);

        // Bullets are `-` (Teams), NOT `•` (Slack-mrkdwn)
        $this->assertStringContainsString('- **Alex Carter** — Engineering', $message);
        $this->assertStringNotContainsString('•', $message);
    }

    public function testBirthdayMessagePluralisesAtMultipleRecipients(): void
    {
        $message = $this->formatter->format(
            SlackRegistration::EVENT_TYPE_BIRTHDAY,
            $this->date,
            [
                new SlackEmployeeRecipient('Alex Carter', 'Engineering'),
                new SlackEmployeeRecipient('Priya Singh', 'People Operations'),
                new SlackEmployeeRecipient('Maria Santos', null),
            ]
        );

        $this->assertStringContainsString('**3 birthdays today**', $message);
        $this->assertStringContainsString('Maria Santos', $message);
        // No dangling "— " when an employee has no subunit
        $this->assertStringNotContainsString('**Maria Santos** — ', $message);
    }

    public function testBirthdayHeaderAppendsSubunitLabelWhenFiltered(): void
    {
        $message = $this->formatter->format(
            SlackRegistration::EVENT_TYPE_BIRTHDAY,
            $this->date,
            [new SlackEmployeeRecipient('Alex Carter', 'Engineering')],
            'Engineering'
        );

        $this->assertStringContainsString('· **Engineering**', $message);
    }

    /* ───────────────────── Leave Today — real notification ──────────────────── */

    public function testLeaveTodayUsesUnicodePalmAndTeamsMarkdown(): void
    {
        $message = $this->formatter->format(
            SlackRegistration::EVENT_TYPE_LEAVE_TODAY,
            $this->date,
            [new SlackEmployeeRecipient('Jordan Lee', 'Engineering', 'Annual leave')]
        );

        $this->assertStringContainsString('🌴', $message);
        $this->assertStringNotContainsString(':palm_tree:', $message);
        $this->assertStringContainsString('**1 employee on leave today**', $message);
        $this->assertStringContainsString('**Jordan Lee** — Annual leave', $message);
        $this->assertStringContainsString('_(Engineering)_', $message);
    }

    public function testLeaveTodayPluralisesAndOmitsParensWhenNoSubunit(): void
    {
        $message = $this->formatter->format(
            SlackRegistration::EVENT_TYPE_LEAVE_TODAY,
            $this->date,
            [
                new SlackEmployeeRecipient('Jordan Lee', 'Engineering', 'Annual leave'),
                new SlackEmployeeRecipient('Sam Patel', null, 'Casual leave'),
            ]
        );

        $this->assertStringContainsString('**2 employees on leave today**', $message);
        $this->assertStringContainsString('**Sam Patel** — Casual leave', $message);
        $this->assertStringNotContainsString('_()_', $message);
    }

    /* ─────────────────────────── Test message ────────────────────────────────── */

    public function testFormatTestMessageBirthdayContainsTeamsMarkdownIndicators(): void
    {
        $message = $this->formatter->formatTestMessage(SlackRegistration::EVENT_TYPE_BIRTHDAY);

        $this->assertStringContainsString('🧪', $message);
        $this->assertStringContainsString('**Test notification — OrangeHRM**', $message);
        $this->assertStringContainsString('No action is required', $message);
        $this->assertStringContainsString('**Preview — Birthday notification:**', $message);
        $this->assertStringContainsString('Alex Carter', $message);

        // Must NOT leak any Slack-mrkdwn shortcodes
        $this->assertStringNotContainsString(':test_tube:', $message);
        $this->assertStringNotContainsString(':white_check_mark:', $message);
    }

    public function testFormatTestMessageLeaveTodayContainsTeamsMarkdownPreview(): void
    {
        $message = $this->formatter->formatTestMessage(SlackRegistration::EVENT_TYPE_LEAVE_TODAY);

        $this->assertStringContainsString('🧪', $message);
        $this->assertStringContainsString('**Preview — Employees on leave today:**', $message);
        $this->assertStringContainsString('Annual leave', $message);
        $this->assertStringContainsString('Casual leave', $message);
    }

    public function testFormatTestMessageUnknownEventTypeFallsBackGracefully(): void
    {
        $message = $this->formatter->formatTestMessage('NOT_A_REAL_EVENT_TYPE');

        $this->assertStringContainsString('🧪', $message);
        $this->assertStringContainsString('**Test notification — OrangeHRM**', $message);
        $this->assertStringContainsString('your webhook destination is connected', $message);
        // Default branch must NOT include any of the per-event preview blocks
        $this->assertStringNotContainsString('Preview — Birthday notification', $message);
        $this->assertStringNotContainsString('Preview — Employees on leave today', $message);
    }

    /* ─────────────────────────── Generic fallback ────────────────────────────── */

    public function testUnknownEventTypeUsesGenericTeamsFormatter(): void
    {
        $message = $this->formatter->format(
            'NEW_HIRE',
            $this->date,
            [new SlackEmployeeRecipient('Dakota Lin')]
        );

        $this->assertStringContainsString('**NEW_HIRE**', $message);
        $this->assertStringContainsString('Dakota Lin', $message);
    }
}
