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

namespace Orangehrm\Rest\Api\User\Service;

use LeaveAllocationServiceException;
use LeaveApplicationService;
use LeaveParameterObject;
use LeaveRequest;

class APILeaveApplicationService extends LeaveApplicationService
{
    /**
     * @param LeaveParameterObject $leaveApplicationData
     * @return bool|LeaveRequest
     * @throws LeaveAllocationServiceException
     */
    public function applyLeave(LeaveParameterObject $leaveApplicationData)
    {
        $employeeId = $leaveApplicationData->getEmployeeNumber();
        if (empty($employeeId)) {
            throw new LeaveAllocationServiceException('Invalid Employee');
        }
        if ($this->hasOverlapLeave($leaveApplicationData)) {
            throw new LeaveAllocationServiceException('Overlapping Leave Request Found.');
        }
        if ($this->applyMoreThanAllowedForADay($leaveApplicationData)) {
            throw new LeaveAllocationServiceException('Work Shift Length Exceeded.');
        }

        return $this->saveLeaveRequest($leaveApplicationData);
    }
}
