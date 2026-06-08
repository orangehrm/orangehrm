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

namespace OrangeHRM\Tests\WorkspaceNotifications\Service\Formatter\Syntax;

use OrangeHRM\WorkspaceNotifications\Service\Formatter\Syntax\SlackMrkdwnDialect;
use OrangeHRM\WorkspaceNotifications\Service\Formatter\Syntax\SyntaxDialectInterface;
use OrangeHRM\WorkspaceNotifications\Service\Formatter\Syntax\TeamsMrkdwnDialect;
use PHPUnit\Framework\TestCase;

/**
 * @group Slack
 * @group Service
 */
class SyntaxDialectsTest extends TestCase
{
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

    public function testSlackEmojiUsesUnicodeGlyphs(): void
    {
        $d = new SlackMrkdwnDialect();
        $this->assertSame('🎉', $d->emoji('party'));
        $this->assertSame('🎂', $d->emoji('birthday'));
        $this->assertSame('🌴', $d->emoji('palm'));
        $this->assertSame('✅', $d->emoji('check'));
        $this->assertSame('🧪', $d->emoji('test_tube'));
    }

    public function testSlackUnknownEmojiFallsBackToEmptyString(): void
    {
        $this->assertSame('', (new SlackMrkdwnDialect())->emoji('newname'));
    }

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
        $this->assertSame('', (new TeamsMrkdwnDialect())->emoji('newname'));
    }

    public function testBothDialectsImplementTheInterface(): void
    {
        $this->assertInstanceOf(SyntaxDialectInterface::class, new SlackMrkdwnDialect());
        $this->assertInstanceOf(SyntaxDialectInterface::class, new TeamsMrkdwnDialect());
    }

    public function testBothDialectsCoverTheSameEmojiNameSet(): void
    {
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
