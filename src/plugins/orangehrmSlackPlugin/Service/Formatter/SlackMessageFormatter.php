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
        $sentAt = (new DateTime())->format('M j, Y \a\t g:i A');

        $header = ":test_tube: *Test notification — OrangeHRM*\n"
            . "This confirms your Slack webhook is configured correctly. No action is required.";

        $footer = "\n\n_Sent {$sentAt} · OrangeHRM Slack notifications_";

        switch ($eventType) {
            case SlackRegistration::EVENT_TYPE_BIRTHDAY:
                $preview =
                    "\n\n*Preview — Birthday notification:*\n"
                    . "> :birthday: *2 birthdays today* — example message\n"
                    . "> Wish them a happy birthday! :tada:\n"
                    . ">\n"
                    . "> • *Alex Carter* — Engineering\n"
                    . "> • *Priya Singh* — People Operations\n\n"
                    . "When real birthdays match the schedule, you'll receive a message in this format. :white_check_mark:";
                return $header . $preview . $footer;

            case SlackRegistration::EVENT_TYPE_LEAVE_TODAY:
                $preview =
                    "\n\n*Preview — Employees on leave today:*\n"
                    . "> :palm_tree: *2 employees on leave today* — example message\n"
                    . "> Plan async work around their absence.\n"
                    . ">\n"
                    . "> • *Jordan Lee* — Annual leave _(Engineering)_\n"
                    . "> • *Sam Patel* — Casual leave _(People Operations)_\n\n"
                    . "When employees are on approved leave, you'll receive a message in this format. :white_check_mark:";
                return $header . $preview . $footer;

            default:
                return $header
                    . "\n\nIf you received this message, your Slack channel is connected. :white_check_mark:"
                    . $footer;
        }
    }

    /**
     * @param SlackEmployeeRecipient[] $recipients
     */
    private function birthdayMessage(DateTime $date, array $recipients, ?string $subunitLabel): string
    {
        $count = count($recipients);
        $countWord = $count === 1 ? '1 birthday' : "{$count} birthdays";

        $header = ":birthday: *{$countWord} today* — " . $date->format('F j, Y');
        if ($subunitLabel !== null) {
            $header .= " · *{$subunitLabel}*";
        }

        $intro = "\nWish them a happy birthday! :tada:";

        $lines = array_map(
            fn(SlackEmployeeRecipient $r) => "• *" . $r->getFullName() . "*"
                . ($r->getSubunit() ? " — " . $r->getSubunit() : ''),
            $recipients
        );

        return $header . $intro . "\n\n" . implode("\n", $lines);
    }

    /**
     * @param SlackEmployeeRecipient[] $recipients
     */
    private function leaveTodayMessage(DateTime $date, array $recipients, ?string $subunitLabel): string
    {
        $count = count($recipients);
        $countWord = $count === 1 ? '1 employee' : "{$count} employees";

        $header = ":palm_tree: *{$countWord} on leave today* — " . $date->format('F j, Y');
        if ($subunitLabel !== null) {
            $header .= " · *{$subunitLabel}*";
        }

        $intro = "\nPlan async work around their absence.";

        $lines = array_map(
            function (SlackEmployeeRecipient $r) {
                $row = "• *" . $r->getFullName() . "*";
                if ($r->getMetadata()) {
                    $row .= " — " . $r->getMetadata();
                }
                if ($r->getSubunit()) {
                    $row .= " _(" . $r->getSubunit() . ")_";
                }
                return $row;
            },
            $recipients
        );

        return $header . $intro . "\n\n" . implode("\n", $lines);
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
