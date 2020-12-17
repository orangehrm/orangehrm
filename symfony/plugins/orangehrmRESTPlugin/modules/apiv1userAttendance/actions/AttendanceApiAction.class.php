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
use Orangehrm\Rest\Api\User\Attendance\AttendanceAPI;
use Orangehrm\Rest\Api\Exception\NotImplementedException;
use Orangehrm\Rest\Api\Exception\BadRequestException;

class AttendanceApiAction extends BaseUserApiAction
{

    private $attendanceAPI = null;

    /**
     * @param Request $request
     * @return AttendanceAPI
     */
    public function getAttendanceApi(Request $request)
    {
        if (is_null($this->attendanceAPI)) {
            $this->attendanceAPI = new AttendanceAPI($request);
        }
        return $this->attendanceAPI;
    }

    /**
     * @param AttendanceAPI $attendanceAPI
     */
    public function setAttendanceApi(AttendanceAPI $attendanceAPI)
    {
        $this->attendanceAPI = $attendanceAPI;
    }

    /**
     * @param Request $request
     */
    protected function init(Request $request)
    {
        $this->attendanceAPI = new AttendanceAPI($request);
        $this->attendanceAPI->setRequest($request);
        $this->getValidationRule = $this->attendanceAPI->getValidationRules();
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
        return $this->attendanceAPI->getAttendanceRecords();
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
