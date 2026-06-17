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

namespace OrangeHRM\WorkspaceNotifications\Service\Formatter\Syntax;

class SlackMrkdwnDialect implements SyntaxDialectInterface
{
    public function bold(string $text): string
    {
        return '*' . $this->escape($text) . '*';
    }

    public function italic(string $text): string
    {
        return '_' . $this->escape($text) . '_';
    }

    public function bullet(): string
    {
        return '•';
    }

    /**
     * Slack/Google Chat mrkdwn link and mention syntax is built from `<`, `>` and `&`.
     * Encoding only those three is Slack's documented escaping; quotes are left intact
     * (ENT_NOQUOTES) so names like "Let's" are not mangled into "Let&#039;s".
     */
    public function escape(string $text): string
    {
        return htmlspecialchars($text, ENT_NOQUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
