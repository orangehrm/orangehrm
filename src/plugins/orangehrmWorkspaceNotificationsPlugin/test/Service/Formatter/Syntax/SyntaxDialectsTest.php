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

use OrangeHRM\WorkspaceNotifications\Service\Formatter\Syntax\GoogleChatMrkdwnDialect;
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

    public function testBothDialectsImplementTheInterface(): void
    {
        $this->assertInstanceOf(SyntaxDialectInterface::class, new SlackMrkdwnDialect());
        $this->assertInstanceOf(SyntaxDialectInterface::class, new TeamsMrkdwnDialect());
    }

    public function testSlackEscapeNeutralizesAngleBracketsAndAmpersand(): void
    {
        $this->assertSame('&lt;a&gt; &amp; &lt;b&gt;', (new SlackMrkdwnDialect())->escape('<a> & <b>'));
    }

    public function testSlackEscapePreservesApostrophes(): void
    {
        // ENT_NOQUOTES: apostrophes must survive so "Let's" does not become "Let&#039;s"
        $this->assertSame("Let's", (new SlackMrkdwnDialect())->escape("Let's"));
    }

    public function testSlackBoldEscapesItsArgument(): void
    {
        $this->assertSame('*&lt;b&gt;*', (new SlackMrkdwnDialect())->bold('<b>'));
    }

    public function testSlackItalicEscapesItsArgument(): void
    {
        $this->assertSame('_&lt;i&gt;_', (new SlackMrkdwnDialect())->italic('<i>'));
    }

    public function testGoogleChatDialectInheritsSlackEscape(): void
    {
        $this->assertSame('&lt;x&gt;', (new GoogleChatMrkdwnDialect())->escape('<x>'));
    }

    public function testTeamsEscapeNeutralizesBracketsAndParens(): void
    {
        $this->assertSame('\[link\]\(url\)', (new TeamsMrkdwnDialect())->escape('[link](url)'));
    }

    public function testTeamsEscapePreservesOrdinaryNames(): void
    {
        $this->assertSame('Alice Smith', (new TeamsMrkdwnDialect())->escape('Alice Smith'));
    }

    public function testTeamsBoldEscapesItsArgument(): void
    {
        $this->assertSame('**\[link\]\(url\)**', (new TeamsMrkdwnDialect())->bold('[link](url)'));
    }

    public function testTeamsItalicEscapesItsArgument(): void
    {
        $this->assertSame('_\[link\]\(url\)_', (new TeamsMrkdwnDialect())->italic('[link](url)'));
    }
}
