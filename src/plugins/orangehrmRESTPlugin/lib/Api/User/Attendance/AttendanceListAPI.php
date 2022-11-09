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

use AttendanceRecord;
use BasicUserRoleManager;
use DaoException;
use Employee;
use EmployeeSearchParameterHolder;
use EmployeeService;
use ListSorter;
use OrangeHRM\Attendance\Service\AttendanceService;
use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\User\Attendance\Model\EmployeeModel;
use Orangehrm\Rest\Http\Response;
use ResourcePermission;
use ServiceException;
use sfContext;
use sfException;
use TimesheetPeriodService;
use UserRoleManagerFactory;

class AttendanceListAPI extends EndPoint
{
    const PARAMETER_FROM_DATE = 'fromDate';
    const PARAMETER_TO_DATE = 'toDate';
    const PARAMETER_EMP_NUMBER = 'empNumber';
    const PARAMETER_PAST_EMPLOYEE = 'pastEmployee';
    const PARAMETER_ALL = 'all';
    const PARAMETER_INCLUDE_SELF = 'includeSelf';
    const DURATION = 'duration';

    const WITH_TERMINATED_ID = 2;
    /**
     * @var null|AttendanceService
     */
    protected $attendanceService = null;

    /**
     * @var null|EmployeeService
     */
    protected $employeeService = null;

    /**
     * @var null|TimesheetPeriodService
     */
    protected $timesheetPeriodService = null;

    /**
     * @return AttendanceService
     */
    public function getAttendanceService(): AttendanceService
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
    public function getEmployeeService(): EmployeeService
    {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    /**
     * @param EmployeeService $service
     */
    public function setEmployeeService(EmployeeService $service)
    {
        $this->employeeService = $service;
    }

    public function getTimesheetPeriodService(): TimesheetPeriodService
    {
        if (is_null($this->timesheetPeriodService)) {
            $this->timesheetPeriodService = new TimesheetPeriodService();
        }
        return $this->timesheetPeriodService;
    }

    /**
     * @param TimesheetPeriodService $service
     */
    public function setTimesheetPeriodService(TimesheetPeriodService $service)
    {
        $this->timesheetPeriodService = $service;
    }

    /**
     * @return Response
     * @throws BadRequestException
     * @throws DaoException
     */
    public function getAttendanceList(): Response
    {
        $params = $this->getParameters();
        $loggedInEmpNumber = $this->getUserAttribute("auth.empNumber");
        if (!empty($params[self::PARAMETER_EMP_NUMBER])) {
            $empNumbers = $params[self::PARAMETER_EMP_NUMBER];
        } else {
            $empNumbers = $this->getAccessibleEmployeeIds($params[self::PARAMETER_PAST_EMPLOYEE]);
            if (!$params[self::PARAMETER_INCLUDE_SELF] && ($key = array_search(
                    $loggedInEmpNumber,
                    $empNumbers
                )) !== false) {
                unset($empNumbers[$key]);
            }
        }

        $employees = [];
        if ($params[self::PARAMETER_ALL] && !empty($empNumbers)) {
            $filters['employee_id_list'] = is_array($empNumbers) ? $empNumbers : [$empNumbers];
            if ($params[self::PARAMETER_PAST_EMPLOYEE]){
                $filters['termination'] = self::WITH_TERMINATED_ID;
            }
            $parameterHolder = new EmployeeSearchParameterHolder();
            $parameterHolder->setOrderField('firstMiddleName');
            $parameterHolder->setOrderBy(ListSorter::ASCENDING);
            $parameterHolder->setFilters($filters);
            $employeeList = $this->getEmployeeService()->searchEmployees($parameterHolder);
            foreach ($employeeList as $employee) {
                if ($employee instanceof Employee) {
                    $employeeModel = new EmployeeModel($employee);
                    $employees[$employee->getEmpNumber()] = array_merge(
                        $employeeModel->toArray(),
                        [self::DURATION => 0]
                    );
                }
            }
        }

        $records = [];
        if (!empty($empNumbers)) {
            $records = $this->getAttendanceService()->getAttendanceRecordsByEmpNumbers(
                $empNumbers,
                $params[self::PARAMETER_FROM_DATE],
                $params[self::PARAMETER_TO_DATE]
            );
        }

        foreach ($records as $record) {
            if ($record instanceof AttendanceRecord) {
                $empId = $record->getEmployeeId();
                if (empty($employees[$empId])) {
                    $employeeModel = new EmployeeModel($record->getEmployee());
                    $employees[$empId] = array_merge(
                        $employeeModel->toArray(),
                        [self::DURATION => 0]
                    );
                }

                if ($record->getPunchOutUtcTime() != null) {
                    // assume saving records, punch in datetime early than punch out datetime
                    $timeDiff = strtotime($record->getPunchOutUtcTime()) - strtotime($record->getPunchInUtcTime());
                    $employees[$empId][self::DURATION] += $timeDiff;
                }
            }
        }
        foreach ($employees as $empId => $employee) {
            $hours = (gmdate("d", $employee[self::DURATION]) - 1) * 24 + gmdate("H", $employee[self::DURATION]);
            $mins = (gmdate("i", $employee[self::DURATION]));
            $employees[$empId][self::DURATION] = $hours . ':' . $mins;
        }

        if (count($employees) == 0) {
            throw new RecordNotFoundException('No Records Found');
        }
        return new Response(array_values($employees));
    }

    /**
     * @param string $name
     * @return string
     * @throws sfException
     */
    protected function getUserAttribute(string $name): string
    {
        return sfContext::getInstance()->getUser()->getAttribute($name);
    }

    /**
     * @param bool $withTerminated
     * @return array
     * @throws ServiceException
     */
    protected function getAccessibleEmployeeIds(bool $withTerminated = false): array
    {
        $properties = ["termination_id"];
        $requiredPermissions = [
            BasicUserRoleManager::PERMISSION_TYPE_DATA_GROUP => [
                'attendance_records' => new ResourcePermission(
                    true, false, false, false
                )
            ]
        ];

        $employeeList = UserRoleManagerFactory::getUserRoleManager()->getAccessibleEntityProperties(
            'Employee',
            $properties,
            null,
            null,
            [],
            [],
            $requiredPermissions
        );
        $empNumbers = [];
        if ($withTerminated) {
            $empNumbers = array_keys($employeeList);
        } else {
            foreach ($employeeList as $empNumber => $employee) {
                if (is_null($employee['termination_id'])) {
                    $empNumbers[] = $empNumber;
                }
            }
        }

        $loggedInEmpNumber = $this->getUserAttribute("auth.empNumber");
        if (!in_array($loggedInEmpNumber, $empNumbers)) {
            array_push($empNumbers, $loggedInEmpNumber);
        }
        return $empNumbers;
    }

    /**
     * @return array
     * @throws BadRequestException
     * @throws DaoException
     */
    public function getParameters(): array
    {
        $params = [];
        $empNumber = $this->getRequestParams()->getQueryParam(self::PARAMETER_EMP_NUMBER);
        $empNumbers = $this->getAccessibleEmployeeIds(true);
        if (!empty($empNumber) && !in_array($empNumber, $empNumbers)) {
            throw new BadRequestException('Employee Not Found');
        }
        $fromDate = $this->getRequestParams()->getQueryParam(self::PARAMETER_FROM_DATE);
        $toDate = $this->getRequestParams()->getQueryParam(self::PARAMETER_TO_DATE);
        $pastEmployee = $this->getRequestParams()->getQueryParam(self::PARAMETER_PAST_EMPLOYEE);
        $all = $this->getRequestParams()->getQueryParam(self::PARAMETER_ALL, false);
        $includeSelf = $this->getRequestParams()->getQueryParam(self::PARAMETER_INCLUDE_SELF, false);

        if (empty($fromDate) && empty($toDate)) {
            $period = $this->getTimesheetPeriodService()->getDefinedTimesheetPeriod(date("Y-m-d"));
            $fromDate = $period[0];
            $toDate = $period[6];
        }
        if (strtotime($fromDate) > strtotime($toDate)) {
            throw new InvalidParamException('To Date Should Be After From Date');
        }

        $params[self::PARAMETER_FROM_DATE] = $fromDate;
        $params[self::PARAMETER_TO_DATE] = $toDate;
        $params[self::PARAMETER_EMP_NUMBER] = $empNumber;
        $params[self::PARAMETER_PAST_EMPLOYEE] = filter_var($pastEmployee, FILTER_VALIDATE_BOOLEAN);
        $params[self::PARAMETER_ALL] = filter_var($all, FILTER_VALIDATE_BOOLEAN);
        $params[self::PARAMETER_INCLUDE_SELF] = filter_var($includeSelf, FILTER_VALIDATE_BOOLEAN);
        return $params;
    }

    /**
     * @return array
     */
    public function getValidationRules(): array
    {
        return [
            self::PARAMETER_FROM_DATE => ['Date' => ['Y-m-d H:i:s']],
            self::PARAMETER_TO_DATE => ['Date' => ['Y-m-d H:i:s']],
            self::PARAMETER_EMP_NUMBER => ['Numeric' => true],
        ];
    }
}
