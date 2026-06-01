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
use OrangeHRM\Slack\Dto\SlackEmployeeRecipient;

class SlackMessageFormatter
{
    /**
     * @param SlackEmployeeRecipient[] $recipients
     */
    public function format(string $eventType, DateTime $date, array $recipients, ?string $subunitLabel = null): string
    {
        switch ($eventType) {
            case SlackRegistration::EVENT_TYPE_BIRTHDAY:
                return $this->birthdayMessage($date, $recipients, $subunitLabel);
            case SlackRegistration::EVENT_TYPE_LEAVE_TODAY:
                return $this->leaveTodayMessage($date, $recipients, $subunitLabel);
            default:
                return $this->genericMessage($eventType, $date, $recipients, $subunitLabel);
        }
    }

    public function formatTestMessage(string $eventType): string
    {
        $time = (new DateTime())->format('Y-m-d H:i:s');
        switch ($eventType) {
            case SlackRegistration::EVENT_TYPE_BIRTHDAY:
                return ":birthday: *OrangeHRM test* — birthday notification preview\n"
                    . "_Sample employees today:_ Alex Carter, Priya Singh\n"
                    . "_Sent at:_ {$time}";
            case SlackRegistration::EVENT_TYPE_LEAVE_TODAY:
                return ":palm_tree: *OrangeHRM test* — on-leave notification preview\n"
                    . "_Sample employees on leave today:_ Jordan Lee (Annual), Sam Patel (Casual)\n"
                    . "_Sent at:_ {$time}";
            default:
                return "*OrangeHRM test message* — {$time}";
        }
    }

    /**
     * @param SlackEmployeeRecipient[] $recipients
     */
    private function birthdayMessage(DateTime $date, array $recipients, ?string $subunitLabel): string
    {
        $header = ":birthday: *Birthdays today* — " . $date->format('F j, Y');
        if ($subunitLabel !== null) {
            $header .= " _(" . $subunitLabel . ")_";
        }
        $lines = array_map(
            fn(SlackEmployeeRecipient $r) => "• " . $r->getFullName()
                . ($r->getSubunit() ? " _(" . $r->getSubunit() . ")_" : ''),
            $recipients
        );
        return $header . "\n" . implode("\n", $lines);
    }

    /**
     * @param SlackEmployeeRecipient[] $recipients
     */
    private function leaveTodayMessage(DateTime $date, array $recipients, ?string $subunitLabel): string
    {
        $header = ":palm_tree: *On leave today* — " . $date->format('F j, Y');
        if ($subunitLabel !== null) {
            $header .= " _(" . $subunitLabel . ")_";
        }
        $lines = array_map(
            fn(SlackEmployeeRecipient $r) => "• " . $r->getFullName()
                . ($r->getMetadata() ? " _(" . $r->getMetadata() . ")_" : ''),
            $recipients
        );
        return $header . "\n" . implode("\n", $lines);
    }

    /**
     * @param SlackEmployeeRecipient[] $recipients
     */
    private function genericMessage(string $eventType, DateTime $date, array $recipients, ?string $subunitLabel): string
    {
        $header = "*{$eventType}* — " . $date->format('Y-m-d');
        if ($subunitLabel !== null) {
            $header .= " _(" . $subunitLabel . ")_";
        }
        $lines = array_map(fn(SlackEmployeeRecipient $r) => "• " . $r->getFullName(), $recipients);
        return $header . "\n" . implode("\n", $lines);
    }
}
