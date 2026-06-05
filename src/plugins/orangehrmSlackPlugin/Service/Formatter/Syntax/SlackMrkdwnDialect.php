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
 * Slack-mrkdwn dialect. Reused as-is for Google Chat — both platforms accept
 * the same `*bold*` / `_italic_` / `•` bullet syntax. Emojis are Unicode
 * glyphs (matching {@see TeamsMrkdwnDialect}) because Google Chat does NOT
 * expand `:shortcodes:` — using Unicode keeps all three platforms rendering
 * the same character.
 */
class SlackMrkdwnDialect implements SyntaxDialectInterface
{
    /**
     * Logical-name → Unicode glyph map. Mirrors {@see TeamsMrkdwnDialect} —
     * the only thing that varies across dialects now is bold delimiter (`*`
     * vs `**`) and bullet glyph (`•` vs `-`). Adding an emoji here means
     * adding the same name to the Teams dialect; the cross-dialect test in
     * `SyntaxDialectsTest::testBothDialectsCoverTheSameEmojiNameSet` guards
     * that invariant.
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
        // Unknown name → empty string, matching the Teams fallback. Printing
        // a literal `:foo:` would leak shortcode syntax into Google Chat
        // where it renders verbatim and looks like a bug.
        return self::EMOJI_UNICODE[$name] ?? '';
    }
}
