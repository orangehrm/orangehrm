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

namespace OrangeHRM\Slack\Service\Formatter;

use OrangeHRM\Slack\Service\Formatter\Syntax\TeamsMrkdwnDialect;

/**
 * Microsoft Teams MessageCard / workflow body formatter.
 *
 * Identical structure to {@see SlackMessageFormatter} — the difference is the
 * markup dialect injected into the per-event formatters. Teams renders:
 *   - `**bold**` (NOT Slack's `*single*`)
 *   - Unicode emoji glyphs (Teams does NOT expand `:shortcodes:`)
 *   - `-` for bullets (Teams MessageCard does not render `•` as a list)
 *
 * Adding a new event type means dropping a class into
 * `Service/Formatter/Event/` — Teams picks it up automatically through the
 * shared event-formatter registration in the parent class.
 */
class TeamsMessageFormatter extends SlackMessageFormatter
{
    public function __construct()
    {
        // The only difference vs the Slack-mrkdwn flavour is the dialect we
        // hand to every event formatter. Everything else — the map of per-
        // event classes, the dispatch logic, the unknown-event fallback —
        // is inherited unchanged from the parent.
        parent::__construct(new TeamsMrkdwnDialect());
    }
}
