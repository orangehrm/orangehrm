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

namespace OrangeHRM\Leave\Service\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Entity\Leave;

class LeaveListModal implements Normalizable
{
    use ModelTrait;
    use ConfigServiceTrait;
    use AuthUserTrait;

    public function __construct(Leave $leave)
    {
        $this->setEntity($leave);
        $filter = [
            'id',
            ['getDecorator', 'getLeaveDate'],
            'lengthHours',
            ['getEmployee','getEmpNumber'],
            ['getEmployee','getLastName'],
            ['getEmployee','getFirstName'],
            ['getEmployee','getMiddleName'],
            ['getEmployee','getEmployeeId'],
            ['getEmployee', 'getEmployeeTerminationRecord', 'getId'],
            ['getDecorator', 'getLeaveDuration'],
            ['getDecorator', 'getEndTime'],
            ['getDecorator', 'getStartTime'],
        ];

        $attributeNames = [
            'id',
            'date',
            'lengthHours',
            ['employee','empNumber'],
            ['employee','lastName'],
            ['employee','firstName'],
            ['employee','middleName'],
            ['employee','employeeId'],
            ['employee','terminationId'],
            'duration',
            'endTime',
            'startTime',
        ];

        //TODO:: will change this to evaluate using data-group permission
        if ($this->getAuthUser()->getUserRoleName() === 'Admin') {
            $filter[] = ['getLeaveType', 'getId'];
            $filter[] = ['getLeaveType', 'getName'];

            $attributeNames[] = ['leaveType', 'id'];
            $attributeNames[] = ['leaveType', 'name'];
        }

        $this->setFilters($filter);
        $this->setAttributeNames($attributeNames);
    }
}
