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
use OrangeHRM\Entity\SlackRegistration;
use OrangeHRM\Slack\Service\Formatter\Event\BirthdayMessageFormatter;
use OrangeHRM\Slack\Service\Formatter\Event\EventMessageFormatterInterface;
use OrangeHRM\Slack\Service\Formatter\Event\GenericMessageFormatter;
use OrangeHRM\Slack\Service\Formatter\Event\LeaveTodayMessageFormatter;
use OrangeHRM\Slack\Service\Formatter\Syntax\SlackMrkdwnDialect;
use OrangeHRM\Slack\Service\Formatter\Syntax\SyntaxDialectInterface;

/**
 * Slack-mrkdwn rendering. Thin dispatcher: holds the
 * {@see SlackMrkdwnDialect} (the *syntax* — `*bold*`, Unicode emoji,
 * `•` bullets) and a map of per-event formatters (the *structure* — header
 * phrase, intro line, recipient row shape). Adding a new event type is one
 * new class in the `Event/` folder + one line in this map; adding a new
 * platform with a different markup is one new dialect + one new platform
 * formatter (or this same class with a different dialect injected).
 *
 * Reused as-is for Google Chat — both platforms accept the same `*bold*` /
 * `_italic_` / `•` bullet markup. Emojis are emitted as Unicode glyphs
 * (rather than Slack `:shortcodes:`) because Google Chat does not expand
 * shortcodes — Unicode is the lowest common denominator that renders
 * identically everywhere. Teams uses a different markup and has its own
 * {@see TeamsMessageFormatter}.
 */
class SlackMessageFormatter implements MessageFormatterInterface
{
    private SyntaxDialectInterface $dialect;

    /** @var array<string, EventMessageFormatterInterface> */
    private array $eventFormatters;

    public function __construct(?SyntaxDialectInterface $dialect = null)
    {
        // Defaulting the dialect rather than requiring it lets the provider
        // wire up with `new SlackMessageFormatter()` for the common case and
        // pass a custom dialect only when needed (e.g. a future Slack variant).
        $this->dialect = $dialect ?? new SlackMrkdwnDialect();
        $this->eventFormatters = [
            SlackRegistration::EVENT_TYPE_BIRTHDAY => new BirthdayMessageFormatter(),
            SlackRegistration::EVENT_TYPE_LEAVE_TODAY => new LeaveTodayMessageFormatter(),
        ];
    }

    /**
     * @param \OrangeHRM\Slack\Dto\SlackEmployeeRecipient[] $recipients
     */
    public function format(string $eventType, DateTime $date, array $recipients, ?string $subunitLabel = null): string
    {
        return $this->resolve($eventType)->format($this->dialect, $date, $recipients, $subunitLabel);
    }

    public function formatTestMessage(string $eventType): string
    {
        return $this->resolve($eventType)->formatTest($this->dialect);
    }

    /**
     * Resolves an event type to its formatter. Unknown event types use the
     * {@see GenericMessageFormatter} fallback with the type name set so the
     * generated message at least carries the right label rather than silently
     * dropping to a placeholder.
     */
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
