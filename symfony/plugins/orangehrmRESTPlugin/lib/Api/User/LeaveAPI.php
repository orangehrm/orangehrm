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

use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Leave\LeaveRequestAPI;
use Orangehrm\Rest\Http\Response;
use Orangehrm\Rest\Api\User\Model\AttendanceLeaveTypeModel;
use Orangehrm\Rest\Api\User\Model\AttendanceLeaveModel;
use \Leave;
use \sfException;
use \sfContext;

class LeaveAPI extends LeaveRequestAPI
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
        $statuses=$this->getStatusesArray($params);
        $leaveHoursResultArray = $this->getLeaveHours(
            $params[self::PARAMETER_FROM_DATE],
            $params[self::PARAMETER_TO_DATE],
            $empNumber,
            $statuses
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
        $params[self::PARAMETER_TAKEN] = ($this->getRequestParams()->getUrlParam(self::PARAMETER_TAKEN));
        $params[self::PARAMETER_REJECTED] = ($this->getRequestParams()->getUrlParam(self::PARAMETER_REJECTED));
        $params[self::PARAMETER_CANCELLED] = ($this->getRequestParams()->getUrlParam(self::PARAMETER_CANCELLED));
        $params[self::PARAMETER_SCHEDULED] = ($this->getRequestParams()->getUrlParam(self::PARAMETER_SCHEDULED));
        $params[self::PARAMETER_PENDING_APPROVAL] = ($this->getRequestParams()->getUrlParam(self::PARAMETER_PENDING_APPROVAL));

        return $params;
    }

    public function getLeaveHours($fromDate, $toDate, $employeeId,$statuses)
    {
        return $this->getLeaveRequestService()->getLeaveRecordsBetweenTwoDays($fromDate, $toDate, $employeeId,$statuses);
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
     * @param $empNumber
     * @return \Employee
     */
    public function checkValidEmployee($empNumber){
        try {
            return $this->getEmployeeService()->getEmployee($empNumber);
        }catch (\Exception $e){
            new BadRequestException($e->getMessage());
        }

    }

    /**
     * @return mixed|null
     * @throws sfException
     */
    public function GetLoggedInEmployeeNumber()
    {
        return sfContext::getInstance()->getUser()->getAttribute("auth.empNumber");
    }

}
