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

namespace OrangeHRM\Tests\Slack\Service\Formatter\Syntax;

use OrangeHRM\Slack\Service\Formatter\Syntax\SlackMrkdwnDialect;
use OrangeHRM\Slack\Service\Formatter\Syntax\SyntaxDialectInterface;
use OrangeHRM\Slack\Service\Formatter\Syntax\TeamsMrkdwnDialect;
use PHPUnit\Framework\TestCase;

/**
 * Each dialect's primitives — bold, italic, bullet, emoji — must agree with
 * the platform's native markup. If a dialect ever drifts (e.g. SlackMrkdwn
 * starts emitting `**bold**`), every event formatter using `$d->bold(...)`
 * produces wrong output silently. The asserts here are the regression net.
 *
 * @group Slack
 * @group Service
 */
class SyntaxDialectsTest extends TestCase
{
    /* ─────────────────────── Slack-mrkdwn ────────────────────────────────────── */

    public function testSlackBoldUsesSingleAsterisks(): void
    {
        $this->assertSame('*hello*', (new SlackMrkdwnDialect())->bold('hello'));
    }

    public function testSlackItalicUsesUnderscores(): void
    {
        $this->assertSame('_hello_', (new SlackMrkdwnDialect())->italic('hello'));
    }

    public function testSlackBulletIsBlackCircle(): void
    {
        $this->assertSame('•', (new SlackMrkdwnDialect())->bullet());
    }

    public function testSlackEmojiUsesShortcodes(): void
    {
        $d = new SlackMrkdwnDialect();
        $this->assertSame(':tada:', $d->emoji('party'));
        $this->assertSame(':birthday:', $d->emoji('birthday'));
        $this->assertSame(':palm_tree:', $d->emoji('palm'));
        $this->assertSame(':white_check_mark:', $d->emoji('check'));
        $this->assertSame(':test_tube:', $d->emoji('test_tube'));
    }

    public function testSlackUnknownEmojiFallsBackToLiteralShortcode(): void
    {
        // Future-proofing: an event formatter that uses a not-yet-mapped name
        // should still produce *something* Slack can render — `:newname:` is
        // a real Slack shortcode lookup, so the worst case is "unknown emoji".
        $this->assertSame(':newname:', (new SlackMrkdwnDialect())->emoji('newname'));
    }

    /* ─────────────────────── Teams MessageCard MD ────────────────────────────── */

    public function testTeamsBoldUsesDoubleAsterisks(): void
    {
        $this->assertSame('**hello**', (new TeamsMrkdwnDialect())->bold('hello'));
    }

    public function testTeamsItalicUsesUnderscores(): void
    {
        $this->assertSame('_hello_', (new TeamsMrkdwnDialect())->italic('hello'));
    }

    public function testTeamsBulletIsHyphen(): void
    {
        // `•` would render as a literal bullet character in Teams, not as a
        // proper list. Hyphen makes Teams render an actual bulleted list.
        $this->assertSame('-', (new TeamsMrkdwnDialect())->bullet());
    }

    public function testTeamsEmojiUsesUnicodeGlyphs(): void
    {
        $d = new TeamsMrkdwnDialect();
        $this->assertSame('🎉', $d->emoji('party'));
        $this->assertSame('🎂', $d->emoji('birthday'));
        $this->assertSame('🌴', $d->emoji('palm'));
        $this->assertSame('✅', $d->emoji('check'));
        $this->assertSame('🧪', $d->emoji('test_tube'));
    }

    public function testTeamsUnknownEmojiFallsBackToEmptyString(): void
    {
        // Teams treats `:shortcode:` as literal text, so the fallback there
        // must NOT be a `:foo:` form (it would print verbatim and look like
        // a bug). Empty string is the least-surprising fallback.
        $this->assertSame('', (new TeamsMrkdwnDialect())->emoji('newname'));
    }

    /* ─────────────────────── Cross-dialect invariants ────────────────────────── */

    public function testBothDialectsImplementTheInterface(): void
    {
        // If either dialect ever stopped implementing the contract, event
        // formatters depending on the interface methods would fail late at
        // dispatch time. Pin the contract here so the failure is loud.
        $this->assertInstanceOf(SyntaxDialectInterface::class, new SlackMrkdwnDialect());
        $this->assertInstanceOf(SyntaxDialectInterface::class, new TeamsMrkdwnDialect());
    }

    public function testBothDialectsCoverTheSameEmojiNameSet(): void
    {
        // The whole point of the dialect abstraction is that an event class
        // can ask for `'party'` without caring which platform renders it. If
        // one dialect ever stopped mapping a name the other still mapped,
        // event output would silently get wrong on that platform.
        $names = ['party', 'birthday', 'palm', 'check', 'test_tube'];
        $slack = new SlackMrkdwnDialect();
        $teams = new TeamsMrkdwnDialect();
        foreach ($names as $name) {
            $this->assertNotSame(
                '',
                $slack->emoji($name),
                "Slack dialect dropped its mapping for '{$name}'"
            );
            $this->assertNotSame(
                '',
                $teams->emoji($name),
                "Teams dialect dropped its mapping for '{$name}'"
            );
        }
    }
}
