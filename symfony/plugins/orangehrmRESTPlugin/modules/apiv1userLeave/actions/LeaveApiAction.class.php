<?php
/*
 *
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
 *
 */

use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Api\User\Leave\LeaveAPI;
use Orangehrm\Rest\Api\Exception\NotImplementedException;
use Orangehrm\Rest\Api\Exception\BadRequestException;

class LeaveApiAction extends BaseUserApiAction
{
    private $leaveAPI = null;

    /**
     * @param Request $request
     * @return LeaveAPI
     */
    public function getLeaveAPI(Request $request)
    {
        if (is_null($this->leaveAPI)) {
            $this->leaveAPI = new LeaveAPI($request);
        }
        return $this->leaveAPI;
    }

    /**
     * @param LeaveAPI $LeaveAPI
     */
    public function setLeaveAPI(LeaveAPI $LeaveAPI)
    {
        $this->leaveAPI = $LeaveAPI;
    }

    /**
     * @param Request $request
     */
    protected function init(Request $request)
    {
        $this->leaveAPI = new LeaveAPI($request);
        $this->leaveAPI->setRequest($request);
        $this->getValidationRule = $this->leaveAPI->getValidationRules();
    }

    /**
     * @param Request $request
     * @return \Orangehrm\Rest\Http\Response
     * @throws AuthenticationServiceException
     * @throws BadRequestException
     * @throws ServiceException
     */
    protected function handleGetRequest(Request $request)
    {
        $this->setUserToContext();
        return $this->getLeaveAPI($request)->getLeaveRecords();
    }
    
    /**
     * @param Request $request
     * @throws NotImplementedException
     */
    protected function handlePostRequest(Request $request)
    {
        throw new NotImplementedException();
    }
}
