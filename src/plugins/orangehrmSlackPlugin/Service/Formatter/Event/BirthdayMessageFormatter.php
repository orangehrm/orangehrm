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

class BirthdayMessageFormatter implements EventMessageFormatterInterface
{
    /**
     * Renders the birthday notification body.
     *
     * Single-recipient case (the common one):
     *   :birthday: Happy Birthday! Let's celebrate *Adam Samwell* from *Engineering* today!
     *
     * Multi-recipient case: a one-line header followed by one bullet per person,
     * keeping the same "Name from Sub-unit" phrasing for consistency:
     *   :birthday: Happy Birthday! Let's celebrate today's birthdays:
     *   • *Alex Carter* from *Engineering*
     *   • *Priya Singh* from *People Operations*
     *
     * `$date` and `$subunitLabel` are accepted to satisfy the interface but
     * intentionally unused — the spec wording omits the date, and each
     * recipient already carries their own subunit (the per-registration filter
     * is just a query constraint, not a display element).
     *
     * @param SlackEmployeeRecipient[] $recipients
     */
    public function format(
        SyntaxDialectInterface $dialect,
        DateTime $date,
        array $recipients,
        ?string $subunitLabel = null
    ): string {
        if (count($recipients) === 1) {
            return $dialect->emoji('birthday') . ' '
                . $this->celebrationLine($dialect, $recipients[0]) . '!';
        }

        $header = $dialect->emoji('birthday')
            . " Happy Birthday! Let's celebrate today's birthdays:";

        $lines = array_map(
            function (SlackEmployeeRecipient $r) use ($dialect) {
                $row = $dialect->bullet() . ' ' . $dialect->bold($r->getFullName());
                if ($r->getSubunit()) {
                    $row .= ' from ' . $dialect->bold($r->getSubunit());
                }
                return $row;
            },
            $recipients
        );

        return $header . "\n\n" . implode("\n", $lines);
    }

    /**
     * Builds the user-facing celebration line for one recipient. Separated so
     * the single-recipient `format()` path and any future preview/test path
     * stay byte-for-byte aligned.
     */
    private function celebrationLine(SyntaxDialectInterface $dialect, SlackEmployeeRecipient $r): string
    {
        $line = "Happy Birthday! Let's celebrate " . $dialect->bold($r->getFullName());
        if ($r->getSubunit()) {
            $line .= ' from ' . $dialect->bold($r->getSubunit());
        }
        $line .= ' today';
        return $line;
    }

    public function formatTest(SyntaxDialectInterface $dialect): string
    {
        $header = $dialect->emoji('test_tube') . ' '
            . $dialect->bold('Test notification — OrangeHRM') . "\n"
            . 'This confirms your webhook is configured correctly. No action is required.';

        return $header
            . "\n\n" . $dialect->bold('Preview — Birthday notification:') . "\n"
            . '> ' . $dialect->emoji('birthday') . ' '
            . "Happy Birthday! Let's celebrate "
            . $dialect->bold('Alex Carter') . ' from ' . $dialect->bold('Engineering')
            . " today!\n\n"
            . 'When real birthdays match the schedule, you\'ll receive a message in this format. '
            . $dialect->emoji('check');
    }
}
