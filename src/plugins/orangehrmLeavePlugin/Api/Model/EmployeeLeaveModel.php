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

namespace OrangeHRM\Leave\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\Leave;

class EmployeeLeaveModel implements Normalizable
{
    use ModelTrait;

    /**
     * @OA\Schema(
     *     schema="Leave-EmployeeLeaveModel",
     *     type="object",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="date", type="string", format="date"),
     *     @OA\Property(property="lengthHours", type="string"),
     *     @OA\Property(property="lengthDays", type="string"),
     *     @OA\Property(
     *         property="status",
     *         type="object",
     *         @OA\Property(property="id", type="integer"),
     *         @OA\Property(property="name", type="string"),
     *     ),
     *     @OA\Property(
     *         property="leaveRequest",
     *         type="object",
     *         @OA\Property(property="id", type="integer"),
     *     ),
     *     @OA\Property(
     *         property="leaveType",
     *         type="object",
     *         @OA\Property(property="id", type="integer"),
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="deleted", type="boolean")
     *     ),
     *     @OA\Property(property="startTime", type="string"),
     *     @OA\Property(property="endTime", type="string"),
     *     @OA\Property(
     *         property="durationType",
     *         type="object",
     *         @OA\Property(property="id", type="integer"),
     *         @OA\Property(property="type", type="string"),
     *     ),
     * )
     */
    public function __construct(Leave $leave)
    {
        $this->setEntity($leave);
        $this->setFilters(
            [
                'id',
                ['getDecorator', 'getLeaveDate'],
                'lengthHours',
                'lengthDays',
                'status',
                ['getDecorator', 'getLeaveStatus'],
                ['getLeaveRequest', 'getId'],
                ['getLeaveType', 'getId'],
                ['getLeaveType', 'getName'],
                ['getLeaveType', 'isDeleted'],
                ['getDecorator', 'getStartTime'],
                ['getDecorator', 'getEndTime'],
                'durationType',
                ['getDecorator', 'getLeaveDuration'],
            ]
        );
        $this->setAttributeNames(
            [
                'id',
                'date',
                'lengthHours',
                'lengthDays',
                ['status', 'id'],
                ['status', 'name'],
                ['leaveRequest', 'id'],
                ['leaveType', 'id'],
                ['leaveType', 'name'],
                ['leaveType', 'deleted'],
                'startTime',
                'endTime',
                ['durationType', 'id'],
                ['durationType', 'type'],
            ]
        );
    }
}
