<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Leave\Api\Model;

use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\LeaveComment;

/**
 * @OA\Schema(
 *     schema="Leave-OverlapLeaveModel",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="date", type="string", format="date"),
 *     @OA\Property(property="lengthHours", type="integer"),
 *     @OA\Property(property="lengthDays", type="integer"),
 *     @OA\Property(property="status", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string"),
 *     ),
 *     @OA\Property(property="leaveRequest", type="object",
 *         @OA\Property(property="id", type="integer"),
 *     ),
 *     @OA\Property(property="leaveType", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="deleted", type="boolean"),
 *     ),
 *     @OA\Property(property="startTime", type="string", format="time"),
 *     @OA\Property(property="endTime", type="string", format="time"),
 *     @OA\Property(property="durationType", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="type", type="string"),
 *     ),
 *     @OA\Property(property="lastComment", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="comment", type="string"),
 *         @OA\Property(property="date", type="string", format="date"),
 *         @OA\Property(property="time", type="string", format="time"),
 *     ),
 * )
 */
class OverlapLeaveModel extends LeaveModel
{
    private Leave $leave;

    /**
     * @param Leave $leave
     */
    public function __construct(Leave $leave)
    {
        $this->leave = $leave;
        parent::__construct($leave);
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $normalizedLeave = parent::toArray();
        if ($this->leave->getDecorator()->getLastComment() instanceof LeaveComment) {
            return $normalizedLeave;
        }

        $this->setEntity($this->leave->getLeaveRequest());
        $this->setFilters(
            [
                ['getDecorator', 'getLastComment', 'getId'],
                ['getDecorator', 'getLastComment', 'getComment'],
                ['getDecorator', 'getLastComment', 'getDecorator', 'getCreatedAtDate'],
                ['getDecorator', 'getLastComment', 'getDecorator', 'getCreatedAtTime'],
            ]
        );
        $this->setAttributeNames(
            [
                ['lastComment', 'id'],
                ['lastComment', 'comment'],
                ['lastComment', 'date'],
                ['lastComment', 'time'],
            ]
        );
        return array_merge($normalizedLeave, parent::toArray());
    }
}
