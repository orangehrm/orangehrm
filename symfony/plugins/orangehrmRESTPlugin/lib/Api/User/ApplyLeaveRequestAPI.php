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

namespace Orangehrm\Rest\Api\User;

use LeaveAllocationServiceException;
use LeaveParameterObject;
use LeaveRequest;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Leave\SaveLeaveRequestAPI;
use Orangehrm\Rest\Api\User\Service\APILeaveApplicationService;
use Orangehrm\Rest\Http\Response;

class ApplyLeaveRequestAPI extends SaveLeaveRequestAPI
{
    /**
     * @var null|APILeaveApplicationService
     */
    protected $apiLeaveApplicationService = null;

    /**
     * @return APILeaveApplicationService|null
     */
    public function getApiLeaveApplicationService(): APILeaveApplicationService
    {
        if (is_null($this->apiLeaveApplicationService)) {
            $this->apiLeaveApplicationService = new APILeaveApplicationService();
        }
        return $this->apiLeaveApplicationService;
    }

    /**
     * @param APILeaveApplicationService|null $apiLeaveApplicationService
     */
    public function setApiLeaveApplicationService(APILeaveApplicationService $apiLeaveApplicationService)
    {
        $this->apiLeaveApplicationService = $apiLeaveApplicationService;
    }

    public function saveLeaveRequest()
    {
        $filters = $this->filterParameters();
        if (!$this->isValidToDate($filters['txtToDate'])) {
            throw new BadRequestException(
                sprintf('Cannot Apply Leave Beyond %s.', $this->getMaxAllowedToDate()->format('Y-m-d'))
            );
        }
        $leaveParameters = new LeaveParameterObject($filters);
        if ($this->validateLeaveType($filters['txtLeaveType'])) {
            try {
                $success = $this->getApiLeaveApplicationService()->applyLeave($leaveParameters);
                if ($success instanceof LeaveRequest) {
                    return new Response(array('success' => 'Successfully Saved'));
                }
            } catch (LeaveAllocationServiceException $e) {
                throw new BadRequestException($e->getMessage());
            }
        }

        throw new BadRequestException("Saving Failed");
    }
}
