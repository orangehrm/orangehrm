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

namespace Orangehrm\Rest\Api\User\Attendance;

use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Http\Response;
use Orangehrm\Rest\Api\User\Model\UserAttendanceModel;
use \sfException;
use \AttendanceService;
use \EmployeeService;
use \Employee;
use \Exception;
use \sfContext;
use \BasicUserRoleManager;
use \UserRoleManagerFactory;
use \ServiceException;

class AttendanceAPI extends EndPoint
{
    /**
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     */

    protected $employeeService;
    protected $attendanceService;
    const PARAMETER_FROM_DATE = 'fromDate';
    const PARAMETER_TO_DATE = 'toDate';
    const PARAMETER_EMPLOYEE_NUMBER = 'empNumber';

    public function getAttendanceRecords()
    {
        $params = $this->getParameters();
        $loggedInEmpNumber = $this->getLoggedInEmployeeNumber();
        $empNumber = $params[self::PARAMETER_EMPLOYEE_NUMBER];
        if (empty($empNumber)) {
            $empNumber = $loggedInEmpNumber;
        }
        if (!empty($empNumber) && !in_array($empNumber, $this->getAccessibleEmpNumbers())) {
            throw new BadRequestException('Access Denied');
        }
        $response = $this->getAttendanceFinalDetails($params, $empNumber);
        return new Response(
            $response
        );
    }

    public function getAttendanceFinalDetails($params, int $empNumber)
    {
        $workHoursResult = $this->getWorkHours(
            $params[self::PARAMETER_FROM_DATE],
            $params[self::PARAMETER_TO_DATE],
            $empNumber
        );
        return $workHoursResult;
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
        $leaveRecordsArray = $this->getAttendanceService()->getAttendanceRecordsBetweenTwoDays(
            $fromDate,
            $toDate,
            $employeeId,
            'ALL'
        );
        if (count($leaveRecordsArray) == 0) {
            throw new RecordNotFoundException('No Records Found');
        }
        $result = [];
        foreach ($leaveRecordsArray as $leaveRecord) {
            array_push($result, (new UserAttendanceModel($leaveRecord))->toArray());
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
    public function getLoggedInEmployeeNumber()
    {
        return sfContext::getInstance()->getUser()->getAttribute("auth.empNumber");
    }

    /**
     * @return AttendanceService
     */
    public function getAttendanceService()
    {
        if (is_null($this->attendanceService)) {
            $this->attendanceService = new AttendanceService();
        }
        return $this->attendanceService;
    }

    /**
     * @param AttendanceService $attendanceService
     */
    public function setAttendanceService(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * @return EmployeeService
     */
    public function getEmployeeService()
    {
        if (!$this->employeeService) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    /**
     * @param EmployeeService $employeeService
     */
    public function setEmployeeService(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    /**
     * @return array
     * @throws ServiceException
     */
    protected function getAccessibleEmpNumbers(): array
    {
        $properties = ["empNumber"];
        $requiredPermissions = [BasicUserRoleManager::PERMISSION_TYPE_ACTION => ['attendance_records']];
        $employeeList = UserRoleManagerFactory::getUserRoleManager()->getAccessibleEntityProperties(
            'Employee',
            $properties,
            null,
            null,
            [],
            [],
            $requiredPermissions
        );
        return array_keys($employeeList);
    }
}
