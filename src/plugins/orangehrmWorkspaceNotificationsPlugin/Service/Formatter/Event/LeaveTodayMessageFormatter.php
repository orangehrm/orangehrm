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

namespace OrangeHRM\WorkspaceNotifications\Service\Formatter\Event;

use DateTime;
use OrangeHRM\WorkspaceNotifications\Dto\WorkspaceNotificationRecipient;
use OrangeHRM\WorkspaceNotifications\Service\Formatter\Syntax\SyntaxDialectInterface;

class LeaveTodayMessageFormatter implements EventMessageFormatterInterface
{
    use TemplateRenderTrait;

    /**
     * @param WorkspaceNotificationRecipient[] $recipients
     */
    public function format(
        SyntaxDialectInterface $dialect,
        DateTime $date,
        array $recipients,
        ?string $subunitLabel = null
    ): string {
        $count = count($recipients);
        return $this->renderTemplate('leaveToday.twig', [
            'dialect' => $dialect,
            'headerText' => $count . ' ' . ($count === 1 ? 'employee' : 'employees') . ' on leave today',
            'dateLabel' => $date->format('F j, Y'),
            'subunitLabel' => $subunitLabel,
            'rows' => array_map(fn (WorkspaceNotificationRecipient $r) => [
                'name' => $r->getFullName(),
                'leaveType' => $r->getMetadata() ?? 'leave',
                'subunit' => $r->getSubunit(),
            ], $recipients),
        ]);
    }

    public function formatTest(SyntaxDialectInterface $dialect): string
    {
        return $this->renderTemplate('leaveToday.test.twig', [
            'dialect' => $dialect,
            'dateLabel' => (new DateTime())->format('F j, Y'),
        ]);
    }
}
