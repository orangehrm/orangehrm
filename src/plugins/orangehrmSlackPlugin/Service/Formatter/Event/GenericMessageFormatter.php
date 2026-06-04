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
 * Default fallback used when the orchestrator hands us an event type that
 * doesn't have its own formatter yet. Keeps delivery working (the admin
 * sees *something*) while a real per-event class is added.
 *
 * The event-type name is passed in via {@see setEventType()} so the same
 * instance can serve any unknown event without proliferating one-off
 * subclasses.
 */
class GenericMessageFormatter implements EventMessageFormatterInterface
{
    private string $eventType = 'EVENT';

    public function setEventType(string $eventType): void
    {
        $this->eventType = $eventType;
    }

    /**
     * @param SlackEmployeeRecipient[] $recipients
     */
    public function format(
        SyntaxDialectInterface $dialect,
        DateTime $date,
        array $recipients,
        ?string $subunitLabel = null
    ): string {
        $header = $dialect->bold($this->eventType) . ' — ' . $date->format('Y-m-d');
        if ($subunitLabel !== null) {
            $header .= ' ' . $dialect->italic('(' . $subunitLabel . ')');
        }
        $lines = array_map(
            fn (SlackEmployeeRecipient $r) => $dialect->bullet() . ' ' . $r->getFullName(),
            $recipients
        );
        return $header . "\n" . implode("\n", $lines);
    }

    public function formatTest(SyntaxDialectInterface $dialect): string
    {
        return $dialect->emoji('test_tube') . ' '
            . $dialect->bold('Test notification — OrangeHRM') . "\n"
            . 'This confirms your webhook is configured correctly. No action is required.'
            . "\n\nIf you received this message, your webhook destination is connected. "
            . $dialect->emoji('check');
    }
}
