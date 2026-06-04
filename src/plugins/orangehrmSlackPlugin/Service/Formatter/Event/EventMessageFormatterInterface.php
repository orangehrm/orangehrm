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

namespace OrangeHRM\Slack\Service\Formatter\Event;

use DateTime;
use OrangeHRM\Slack\Dto\SlackEmployeeRecipient;
use OrangeHRM\Slack\Service\Formatter\Syntax\SyntaxDialectInterface;

/**
 * One implementation per supported event type. Each formatter:
 *   - knows the *structure* of its event (header phrase, intro line, the
 *     way recipient rows are shaped),
 *   - asks the {@see SyntaxDialectInterface} for *syntax* (bold, bullet,
 *     emoji), so the same class emits Slack-mrkdwn or Teams MD depending on
 *     which dialect is passed in.
 *
 * Adding a new event type (e.g. `NEW_HIRE`) is one new class implementing
 * this interface, registered in each platform formatter's event map. No
 * existing class changes.
 */
interface EventMessageFormatterInterface
{
    /**
     * Render the user-facing notification body for a real run.
     *
     * @param SlackEmployeeRecipient[] $recipients
     * @param string|null $subunitLabel Comma-joined subunit names, or null for whole-org
     */
    public function format(
        SyntaxDialectInterface $dialect,
        DateTime $date,
        array $recipients,
        ?string $subunitLabel = null
    ): string;

    /**
     * Render the "Send test" preview. No recipients are passed — the formatter
     * picks representative sample content so the admin sees what real messages
     * will look like.
     */
    public function formatTest(SyntaxDialectInterface $dialect): string;
}
