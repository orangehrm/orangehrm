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

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\Leave;

class LeaveModel implements Normalizable
{
    use ModelTrait;

    /**
     * @param Leave $leave
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
                ['getDecorator', 'getLastComment', 'getId'],
                ['getDecorator', 'getLastComment', 'getComment'],
                ['getDecorator', 'getLastComment', 'getDecorator', 'getCreatedAtDate'],
                ['getDecorator', 'getLastComment', 'getDecorator', 'getCreatedAtTime'],
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
                ['lastComment', 'id'],
                ['lastComment', 'comment'],
                ['lastComment', 'date'],
                ['lastComment', 'time'],
            ]
        );
    }
}
