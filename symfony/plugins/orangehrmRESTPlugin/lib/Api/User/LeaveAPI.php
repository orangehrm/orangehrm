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

use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Http\Response;
use \PluginAttendanceRecord;
use \AttendanceRecord;
use Orangehrm\Rest\Api\Attendance\PunchTimeAPI;
use Orangehrm\Rest\Api\User\Model\AttendanceLeaveTypeModel;
use Orangehrm\Rest\Api\User\Model\AttendanceLeaveModel;
use \LeaveRequestService;
use \WorkWeekService;
use \Leave;
use \HolidayService;

class LeaveAPI extends PunchTimeAPI
{
    /**
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     */

    const PARAMETER_FROM_DATE = 'fromDate';
    const PARAMETER_TO_DATE = 'toDate';
    const PARAMETER_EMPLOYEE_NUMBER = 'empNumber';

    protected $leaveRequestService;
    protected $workWeekService;
    protected $holidayService;

    public function getLeaveRecords()
    {
        $params = $this->getParameters();
        $loggedInEmpNumber = $this->GetLoggedInEmployeeNumber();
        $empNumber = $params[self::PARAMETER_EMPLOYEE_NUMBER];
        if (empty($empNumber)) {
            $empNumber = $loggedInEmpNumber;
        }
        if (!empty($empNumber) && !$this->checkValidEmployee($empNumber)) {
            throw new BadRequestException('Employee Id ' . $empNumber . ' Not Found');
        }

        $response = $this->getAttendanceFinalDetails($params, $empNumber);
        return new Response(
            $response
        );
    }

    public function getAttendanceFinalDetails($params, int $empNumber)
    {
        $leaveHoursResultArray = $this->getLeaveHours(
            $params[self::PARAMETER_FROM_DATE],
            $params[self::PARAMETER_TO_DATE],
            $empNumber
        );
        $result =[];
        foreach ($leaveHoursResultArray as $leaveResult) {
            $status_id = $leaveResult->getStatus();
            $leaveResultArray = (new AttendanceLeaveModel($leaveResult))->toArray();
            $formattedLeaveTypeArray = (new AttendanceLeaveTypeModel($leaveResultArray['leaveType']))->toArray();
            $leaveResultArray['leaveType'] = $formattedLeaveTypeArray;

            $leaveStatusName = Leave::getTextForLeaveStatus($status_id);
            $leaveResultArray['status'] = $leaveStatusName;
            array_push($result,$leaveResultArray);
        }
        return $result;
    }

    public function getParameters()
    {
        $params = array();
        $params[self::PARAMETER_FROM_DATE] = $this->getRequestParams()->getQueryParam(
            self::PARAMETER_FROM_DATE
        );
        $params[self::PARAMETER_TO_DATE] = $this->getRequestParams()->getQueryParam(self::PARAMETER_TO_DATE);
        $params[self::PARAMETER_EMPLOYEE_NUMBER] = $this->getRequestParams()->getQueryParam(
            self::PARAMETER_EMPLOYEE_NUMBER
        );
        return $params;
    }

    public function getLeaveHours($fromDate, $toDate, $employeeId)
    {
        return $this->getLeaveRequestService()->getLeaveRecordsBetweenTwoDays($fromDate, $toDate, $employeeId);
    }

    /**
     * @return array
     */
    public function getValidationRules(): array
    {
        return [
            self::PARAMETER_FROM_DATE => ['Date' => ['Y-m-d']],
            self::PARAMETER_TO_DATE => ['Date' => ['Y-m-d']],
            self::PARAMETER_EMPLOYEE_NUMBER => ['Numeric' => true],
        ];
    }

    /**
     * @return LeaveRequestService
     */
    public function getLeaveRequestService()
    {
        if (is_null($this->leaveRequestService)) {
            $this->leaveRequestService = new LeaveRequestService();
        }
        return $this->leaveRequestService;
    }

    /**
     * @param LeaveRequestService $leaveRequestService
     */
    public function setLeaveRequestService(LeaveRequestService $leaveRequestService)
    {
        $this->leaveRequestService = $leaveRequestService;
    }

    /**
     * @return WorkWeekService
     */
    public function getWorkWeekService()
    {
        if (is_null($this->workWeekService)) {
            $this->workWeekService = new WorkWeekService();
        }
        return $this->workWeekService;
    }

    /**
     * @param WorkWeekService $workWeekService
     */
    public function setWorkWeekService(WorkWeekService $workWeekService)
    {
        $this->workWeekService = $workWeekService;
    }

    /**
     * @return HolidayService
     */
    public function getHolidayService()
    {
        if (is_null($this->holidayService)) {
            $this->holidayService = new HolidayService();
        }
        return $this->holidayService;
    }

    /**
     * @param HolidayService $holidayService
     */
    public function setHolidayService(WorkWeekService $holidayService)
    {
        $this->holidayService = $holidayService;
    }
}
