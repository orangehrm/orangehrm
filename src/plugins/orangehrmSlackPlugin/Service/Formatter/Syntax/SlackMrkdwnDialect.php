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
 * Slack-mrkdwn dialect. Reused as-is for Google Chat — Google Chat accepts the
 * same `*bold*` / `_italic_` / `:emoji:` syntax. See
 * {@see \OrangeHRM\Slack\Service\Formatter\SlackMessageFormatter} for the
 * full rationale on why we emit `:shortcode:` form rather than Unicode glyphs.
 */
class SlackMrkdwnDialect implements SyntaxDialectInterface
{
    /**
     * Logical-name → Slack `:shortcode:` map. Adding an emoji here is the
     * only place a new event formatter has to coordinate with the dialect —
     * the event class refers to the name (`'party'`), never the shortcode.
     */
    private const EMOJI_SHORTCODES = [
        'party' => ':tada:',
        'birthday' => ':birthday:',
        'palm' => ':palm_tree:',
        'check' => ':white_check_mark:',
        'test_tube' => ':test_tube:',
        // 📢 — used by the "Today's Absences" header. Slack renders the same
        // glyph for `:loudspeaker:` (📢) and `:mega:` (📣); the spec sample
        // uses the steady-tone 📢 so we map there.
        'megaphone' => ':loudspeaker:',
    ];

    public function bold(string $text): string
    {
        return '*' . $text . '*';
    }

    public function italic(string $text): string
    {
        return '_' . $text . '_';
    }

    public function bullet(): string
    {
        return '•';
    }

    public function emoji(string $name): string
    {
        // Unknown emoji name → fall back to a literal `:name:`, which Slack
        // either renders (if it's a real shortcode we forgot to register) or
        // shows verbatim. Either way, no crash, no missing-symbol.
        return self::EMOJI_SHORTCODES[$name] ?? (':' . $name . ':');
    }
}
