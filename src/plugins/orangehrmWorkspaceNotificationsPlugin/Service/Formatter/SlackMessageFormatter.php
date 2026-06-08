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

namespace OrangeHRM\WorkspaceNotifications\Service\Formatter;

use DateTime;
use OrangeHRM\Entity\WorkspaceNotificationRegistration;
use OrangeHRM\WorkspaceNotifications\Service\Formatter\Event\BirthdayMessageFormatter;
use OrangeHRM\WorkspaceNotifications\Service\Formatter\Event\EventMessageFormatterInterface;
use OrangeHRM\WorkspaceNotifications\Service\Formatter\Event\GenericMessageFormatter;
use OrangeHRM\WorkspaceNotifications\Service\Formatter\Event\LeaveTodayMessageFormatter;
use OrangeHRM\WorkspaceNotifications\Service\Formatter\Syntax\SlackMrkdwnDialect;
use OrangeHRM\WorkspaceNotifications\Service\Formatter\Syntax\SyntaxDialectInterface;

class SlackMessageFormatter implements MessageFormatterInterface
{
    private SyntaxDialectInterface $dialect;

    /** @var array<string, EventMessageFormatterInterface> */
    private array $eventFormatters;

    public function __construct(?SyntaxDialectInterface $dialect = null)
    {
        $this->dialect = $dialect ?? new SlackMrkdwnDialect();
        $this->eventFormatters = [
            WorkspaceNotificationRegistration::EVENT_TYPE_BIRTHDAY => new BirthdayMessageFormatter(),
            WorkspaceNotificationRegistration::EVENT_TYPE_LEAVE_TODAY => new LeaveTodayMessageFormatter(),
        ];
    }

    /**
     * @param \OrangeHRM\WorkspaceNotifications\Dto\WorkspaceNotificationRecipient[] $recipients
     */
    public function format(string $eventType, DateTime $date, array $recipients, ?string $subunitLabel = null): string
    {
        return $this->resolve($eventType)->format($this->dialect, $date, $recipients, $subunitLabel);
    }

    public function formatTestMessage(string $eventType): string
    {
        return $this->resolve($eventType)->formatTest($this->dialect);
    }

    private function resolve(string $eventType): EventMessageFormatterInterface
    {
        if (isset($this->eventFormatters[$eventType])) {
            return $this->eventFormatters[$eventType];
        }
        $generic = new GenericMessageFormatter();
        $generic->setEventType($eventType);
        return $generic;
    }
}
