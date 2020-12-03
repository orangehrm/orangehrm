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

class GraphAPI extends EndPoint
{
    /**
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     */

    const PARAMETER_FROM_DATE = 'fromDate';
    const PARAMETER_TO_DATE = 'toDate';
    const PARAMETER_EMPLOYEE_NUMBER = 'empNumber';
    const WEEK_DAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    protected $employeeService;
    protected $attendanceService;

    public function getGraphRecords()
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

        $workHoursResult = $this->getWorkHours(
            $params[self::PARAMETER_FROM_DATE],
            $params[self::PARAMETER_TO_DATE],
            $loggedInEmpNumber
        );
        $leaveHoursResult = $this->getLeaveHours(
            $params[self::PARAMETER_FROM_DATE],
            $params[self::PARAMETER_TO_DATE],
            $loggedInEmpNumber
        );
        $workSummary = array();
        foreach (self::WEEK_DAYS as $day) {
            if (array_key_exists($day, $workHoursResult)) {
                $workSummary[$day]['workHours'] = $workHoursResult[$day];
            }
            if (array_key_exists($day, $leaveHoursResult)) {
                $workSummary[$day]['leave'] = $leaveHoursResult[$day];
            }
        }
        return new Response(
            $workSummary
        );
    }

    public function getLeaveHours($fromDate, $toDate, $employeeId)
    {
        $date1 = new \DateTime($fromDate);
        $date2 = new \DateTime($toDate);
        $diff = $date1->diff($date2)->days;
        if ($diff != 6) {
            return "exception";
        }
        $result = [];
        $leaveRecords = $this->getAttendanceService()->getLeaveRecordsBetweenTwoDays($fromDate, $toDate, $employeeId);


        foreach ($leaveRecords as $leaveRecord) {
            $day = (new \DateTime($leaveRecord->getDate()))->format('l');
            $duration = $leaveRecord->getLength_hours();
            $leaveType = $this->getAttendanceService()->getLeaveType($leaveRecord->getLeave_type_id())->getName();

            if (array_key_exists($day, $result)) {
                echo $leaveType . "uhuh";
                if (array_key_exists($leaveType, $result[$day])) {
                    $result[$day][$leaveType] = $result[$day][$leaveType] + $duration;
                } else {
                    $result[$day][$leaveType] = $duration;
                }
            } else {
                $result[$day][$leaveType] = $duration;
            }
        }

        foreach ($result as $day => $dayArray) {
            foreach ($dayArray as $type => $value) {
                $result[$day][$type] = round($value, 2);
            }
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

    public function getWorkHours($fromDate, $toDate, $employeeId)
    {
        // need to check from date is starting date
        $date1 = new \DateTime($fromDate);
        $date2 = new \DateTime($toDate);
        $diff = $date1->diff($date2)->days;
        if ($diff != 6) {
            throw new InvalidParamException(
                'Duration should be one week   e.g :- fromDate=2020-11-24 & toDate=2020-11-30'
            );
        }
        $result = [];
        $attendanceRecords = $this->getAttendanceService()->getAttendanceRecordsBetweenTwoDays(
            $fromDate,
            $toDate,
            $employeeId
        );

        foreach ($attendanceRecords as $attendanceRecord) {
            $date1 = $attendanceRecord->getPunchInUserTime();
            $date2 = $attendanceRecord->getPunchOutUserTime();
            $day = (new \DateTime($date1))->format('l');

            $duration = abs(strtotime($date2) - strtotime($date1)) / (60 * 60);

            if (array_key_exists($day, $result)) {
                $result[$day] = $result[$day] + $duration;
            } else {
                $result[$day] = $duration;
            }
        }

        foreach ($result as $key => $value) {
            $result[$key] = number_format($value, 2);
        }
        return $result;
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
     * @return mixed|null
     * @throws sfException
     */
    public function GetLoggedInEmployeeNumber()
    {
        return \sfContext::getInstance()->getUser()->getAttribute("auth.empNumber");
    }

    /**
     * @param $empNumber
     * @return \Employee
     */
    public function checkValidEmployee($empNumber)
    {
        try {
            return $this->getEmployeeService()->getEmployee($empNumber);
        } catch (\Exception $e) {
            new BadRequestException($e->getMessage());
        }
    }

    /**
     * @return \EmployeeService
     */
    public function getEmployeeService()
    {
        if (!$this->employeeService) {
            $this->employeeService = new \EmployeeService();
        }
        return $this->employeeService;
    }

    /**
     * @param $employeeService
     * @return $this
     */
    public function setEmployeeService($employeeService)
    {
        $this->employeeService = $employeeService;
        return $this;
    }

    /**
     * @return \AttendanceService
     */
    public function getAttendanceService()
    {
        if (is_null($this->attendanceService)) {
            $this->attendanceService = new \AttendanceService();
        }
        return $this->attendanceService;
    }

}
