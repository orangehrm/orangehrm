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

namespace OrangeHRM\Slack\Service\Formatter\Syntax;

/**
 * Microsoft Teams (MessageCard / Power Automate workflow body) markdown dialect:
 *
 *   - `**bold**` (NOT `*single*` — Teams would print the asterisks verbatim)
 *   - `-` for bullets (Teams MessageCard does not render `•` as a list)
 *   - Unicode glyphs for emoji — Teams does not expand `:shortcodes:`
 */
class TeamsMrkdwnDialect implements SyntaxDialectInterface
{
    /**
     * Logical-name → Unicode glyph map. Names match {@see SlackMrkdwnDialect}
     * exactly so an event formatter never has to know which platform it's
     * rendering for — it just asks the dialect for `'birthday'`.
     */
    private const EMOJI_UNICODE = [
        'party' => '🎉',
        'birthday' => '🎂',
        'palm' => '🌴',
        'check' => '✅',
        'test_tube' => '🧪',
        'megaphone' => '📢',
    ];

    public function bold(string $text): string
    {
        return '**' . $text . '**';
    }

    public function italic(string $text): string
    {
        return '_' . $text . '_';
    }

    public function bullet(): string
    {
        return '-';
    }

    public function emoji(string $name): string
    {
        // Unknown name → drop to empty string rather than a literal `:name:`,
        // which Teams would print as plain text and look like a bug to the
        // reader. Empty is the least surprising fallback in Teams.
        return self::EMOJI_UNICODE[$name] ?? '';
    }
}
