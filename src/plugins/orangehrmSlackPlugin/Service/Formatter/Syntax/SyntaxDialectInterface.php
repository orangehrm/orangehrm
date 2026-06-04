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
 * Markup primitives every platform supports under different syntax — bold,
 * italic, bullets, emoji. Event formatters build their messages out of these
 * calls rather than baking in `*asterisks*` / `**asterisks**` directly, so the
 * same event class produces the right output for Slack-mrkdwn AND Teams
 * MessageCard markdown.
 *
 * Adding a new platform with its own markup dialect is one new implementation
 * of this interface, paired with a one-line `new PlatformMessageFormatter(
 * new MyDialect(), …)` instantiation. No event class changes.
 */
interface SyntaxDialectInterface
{
    public function bold(string $text): string;
    public function italic(string $text): string;

    /**
     * Bullet glyph for list rows — caller is responsible for the trailing
     * space (`$d->bullet() . ' Alex Carter'`). Slack/GChat use `•`; Teams
     * MessageCard markdown requires `-` for the bullet to render.
     */
    public function bullet(): string;

    /**
     * Lookup-by-name emoji. Implementations map a stable logical name
     * (`'party'`, `'birthday'`, `'palm'`, `'check'`, `'test_tube'`) to the
     * platform-native form — Slack `:shortcodes:`, Teams Unicode glyphs.
     *
     * Unknown names fall back to a safe placeholder rather than crashing,
     * so a future event using an emoji we haven't mapped yet won't break
     * delivery.
     */
    public function emoji(string $name): string;
}
