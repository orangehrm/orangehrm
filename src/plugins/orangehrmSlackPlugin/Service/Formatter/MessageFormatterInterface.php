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

use DateTime;
use OrangeHRM\Slack\Dto\SlackEmployeeRecipient;

/**
 * Per-platform rendering strategy. Each {@see \OrangeHRM\Slack\Service\Webhook\WebhookProviderInterface}
 * implementation returns the formatter that produces its native message syntax —
 * Slack-mrkdwn for Slack + Google Chat (they accept the same shape), MessageCard
 * markdown for Teams, etc.
 *
 * The orchestrator never picks a formatter directly: it asks the resolved
 * provider for its formatter and feeds the same logical inputs to it. Adding
 * a new platform = one new provider class + one new formatter class; no
 * orchestrator changes.
 */
interface MessageFormatterInterface
{
    /**
     * Produce the user-facing message body for a real notification.
     *
     * @param string $eventType One of SlackRegistration::EVENT_TYPES
     * @param DateTime $date The notification's local "today"
     * @param SlackEmployeeRecipient[] $recipients
     * @param string|null $subunitLabel Comma-joined subunit names, or null for whole-org
     */
    public function format(
        string $eventType,
        DateTime $date,
        array $recipients,
        ?string $subunitLabel = null
    ): string;

    /**
     * Produce the body of the "Send test" message. No recipients are passed —
     * the formatter is responsible for picking representative sample content.
     */
    public function formatTestMessage(string $eventType): string;
}
