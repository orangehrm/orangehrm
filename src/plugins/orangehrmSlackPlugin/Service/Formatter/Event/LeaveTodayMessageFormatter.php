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

class LeaveTodayMessageFormatter implements EventMessageFormatterInterface
{
    /**
     * Renders the "today's absences" notification.
     *
     * Template (per spec sample):
     *   📢 Today's Absences (Marketing):
     *   *Jane Smith* is on *Annual Leave*
     *   _(Period: 2026/05/15 - 2026/05/20)_
     *
     * Header parenthetical resolves to `$subunitLabel` (the registration's
     * sub-unit filter, if set). When the registration has no filter, we drop
     * the parenthetical entirely — saying "(All Sub-Units)" is noisy and
     * doesn't actually narrow anything down. Each recipient line uses the
     * leave_request's applied period (start..end across all its days); a
     * single-day leave collapses to one date so we don't render "X - X".
     *
     * @param SlackEmployeeRecipient[] $recipients
     */
    public function format(
        SyntaxDialectInterface $dialect,
        DateTime $date,
        array $recipients,
        ?string $subunitLabel = null
    ): string {
        $headerCore = "Today's Absences";
        if ($subunitLabel !== null && $subunitLabel !== '') {
            $headerCore .= " ({$subunitLabel})";
        }
        $header = $dialect->emoji('megaphone') . ' '
            . $dialect->bold($headerCore . ':');

        $blocks = array_map(
            function (SlackEmployeeRecipient $r) use ($dialect) {
                $line = $dialect->bold($r->getFullName());
                if ($r->getMetadata()) {
                    $line .= ' is on ' . $dialect->bold($r->getMetadata());
                } else {
                    $line .= ' is on leave';
                }
                $periodLine = $this->renderPeriod($dialect, $r);
                return $periodLine !== null ? $line . "\n" . $periodLine : $line;
            },
            $recipients
        );

        return $header . "\n\n" . implode("\n\n", $blocks);
    }

    /**
     * Returns the `(Period: YYYY/MM/DD - YYYY/MM/DD)` italic line, or null if
     * the recipient has no period (BIRTHDAY case, or LEAVE_TODAY with a
     * resolver that hasn't populated the dates).
     */
    private function renderPeriod(SyntaxDialectInterface $dialect, SlackEmployeeRecipient $r): ?string
    {
        $start = $r->getStartDate();
        $end = $r->getEndDate();
        if ($start === null) {
            return null;
        }
        $startFmt = $start->format('Y/m/d');
        $endFmt = $end !== null ? $end->format('Y/m/d') : $startFmt;
        $body = $startFmt === $endFmt
            ? "Period: {$startFmt}"
            : "Period: {$startFmt} - {$endFmt}";
        return $dialect->italic("({$body})");
    }

    public function formatTest(SyntaxDialectInterface $dialect): string
    {
        $header = $dialect->emoji('test_tube') . ' '
            . $dialect->bold('Test notification — OrangeHRM') . "\n"
            . 'This confirms your webhook is configured correctly. No action is required.';

        return $header
            . "\n\n" . $dialect->bold("Preview — Today's Absences:") . "\n"
            . '> ' . $dialect->emoji('megaphone') . ' '
            . $dialect->bold("Today's Absences (Marketing):") . "\n"
            . '> ' . $dialect->bold('Jane Smith') . ' is on ' . $dialect->bold('Annual Leave') . "\n"
            . '> ' . $dialect->italic('(Period: 2026/05/15 - 2026/05/20)') . "\n\n"
            . 'When employees are on approved leave, you\'ll receive a message in this format. '
            . $dialect->emoji('check');
    }
}
