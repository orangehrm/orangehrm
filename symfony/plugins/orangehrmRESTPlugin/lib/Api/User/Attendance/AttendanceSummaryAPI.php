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
use \PluginAttendanceRecord;
use \DateTime;
use \sfException;
use \AttendanceService;
use \EmployeeService;
use \Exception;
use \LeaveRequestService;
use \sfContext;
use \PluginLeave;
use \BasicUserRoleManager;
use TimesheetPeriodService;
use \UserRoleManagerFactory;
use \ServiceException;

class AttendanceSummaryAPI extends EndPoint
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
    const PARAMETER_REJECTED = "rejected";
    const PARAMETER_CANCELLED = "cancelled";
    const PARAMETER_PENDING_APPROVAL = "pendingApproval";
    const PARAMETER_SCHEDULED = "scheduled";
    const PARAMETER_TAKEN = 'taken';
    protected $employeeService;
    protected $attendanceService;
    protected $dayMapper;
    /**
     * @var null|TimesheetPeriodService
     */
    protected $timesheetPeriodService = null;

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
     * @throws InvalidParamException
     */
    public function getAttendanceSummary()
    {
        $params = $this->getParameters();
        $loggedInEmpNumber = $this->getLoggedInEmployeeNumber();
        $empNumber = $params[self::PARAMETER_EMPLOYEE_NUMBER];
        $date1 = new DateTime($params[self::PARAMETER_FROM_DATE]);
        $date2 = new DateTime($params[self::PARAMETER_TO_DATE]);
        if(empty($params[self::PARAMETER_FROM_DATE])){
            throw new InvalidParamException(
                'From Date is Required'
            );
        }
        if(empty($params[self::PARAMETER_TO_DATE])){
            throw new InvalidParamException(
                'To Date is Required'
            );
        }
        if($date1>$date2){
            throw new InvalidParamException(
                'Invalid date Period'
            );
        }
        $diff = $date1->diff($date2)->days;
        if ($diff != 6) {
            throw new InvalidParamException(
                'Duration should be one week   e.g :- fromDate=2020-11-24 00:00:00 & toDate=2020-11-30 00:00:00'
            );
        }
        if (empty($empNumber)) {
            $empNumber = $loggedInEmpNumber;
        }
        if (!in_array($empNumber, $this->getAccessibleEmpNumbers()) && $loggedInEmpNumber!=$empNumber) {
            throw new BadRequestException('Access Denied');
        }
        $statuses = $this->getStatusesArray($params);
        $dayMapper = [];
        foreach (self::WEEK_DAYS as $weekDay) {
            $dayMapper[$weekDay] = strtolower($weekDay);
        }
        $this->dayMapper = $dayMapper;

        $workHoursResult = $this->getWorkHours(
            $params[self::PARAMETER_FROM_DATE],
            $params[self::PARAMETER_TO_DATE],
            $empNumber
        );
        $leaveHoursResult = $this->getLeaveHours(
            $params[self::PARAMETER_FROM_DATE],
            $params[self::PARAMETER_TO_DATE],
            $empNumber,
            $statuses
        );
        $workSummary = array();
        foreach (self::WEEK_DAYS as $day) {
            $mappedDay = $this->dayMapper[$day];
            if (array_key_exists($day, $workHoursResult)) {
                $workSummary[$mappedDay]['workHours'] = $workHoursResult[$day];
            } else {
                $workSummary[$mappedDay] = ['workHours' => 0];
            }
            if (array_key_exists($day, $leaveHoursResult)) {
                $workSummary[$mappedDay]['leave'] = $leaveHoursResult[$day];
            } else {
                $workSummary[$mappedDay]['leave'] = [];
            }
        }
        $totalWorkHours = 0;
        $totalLeaveHours = 0;
        $totalLeaveTypeHours = [];
        foreach ($workSummary as $day => $dayResult) {
            $totalWorkHours = $totalWorkHours + $dayResult['workHours'];
            foreach ($dayResult['leave'] as $singleLeaveType) {
                $type = $singleLeaveType['type'];
                $hours = $singleLeaveType['hours'];
                $typeId = $singleLeaveType['typeId'];
                $totalLeaveHours = $totalLeaveHours + $hours;

                $found = false;

                for ($i = 0; $i < count($totalLeaveTypeHours); $i++) {
                    if ($totalLeaveTypeHours[$i]['type'] == $type) {
                        $totalLeaveTypeHours[$i]['hours'] = number_format(
                            $totalLeaveTypeHours[$i]['hours'] + $hours,
                            2
                        );
                        $found = true;
                    }
                }
                if (!$found) {
                    array_push($totalLeaveTypeHours, ['typeId' => $typeId, 'type' => $type, 'hours' => $hours]);
                }
            }
        }
        $totalWorkHours = number_format($totalWorkHours, 2);
        $totalLeaveHours = number_format($totalLeaveHours, 2);
        return new Response(
            array(
                'totalWorkHours' => $totalWorkHours,
                'totalLeaveHours' => $totalLeaveHours,
                'totalLeaveTypeHours' => $totalLeaveTypeHours,
                'workSummary' => $workSummary
            )
        );
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        $params = array();
        $fromDate = $this->getRequestParams()->getQueryParam(self::PARAMETER_FROM_DATE);
        $toDate = $this->getRequestParams()->getQueryParam(self::PARAMETER_TO_DATE);
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
        $params[self::PARAMETER_EMPLOYEE_NUMBER] = $this->getRequestParams()->getQueryParam(
            self::PARAMETER_EMPLOYEE_NUMBER
        );
        $params[self::PARAMETER_TAKEN] = ($this->getRequestParams()->getUrlParam(self::PARAMETER_TAKEN));
        $params[self::PARAMETER_REJECTED] = ($this->getRequestParams()->getUrlParam(self::PARAMETER_REJECTED));
        $params[self::PARAMETER_CANCELLED] = ($this->getRequestParams()->getUrlParam(self::PARAMETER_CANCELLED));
        $params[self::PARAMETER_SCHEDULED] = ($this->getRequestParams()->getUrlParam(self::PARAMETER_SCHEDULED));
        $params[self::PARAMETER_PENDING_APPROVAL] = ($this->getRequestParams()->getUrlParam(
            self::PARAMETER_PENDING_APPROVAL
        ));

        return $params;
    }

    /**
     * @param $fromDate
     * @param $toDate
     * @param $employeeId
     * @return array|string
     * @throws Exception
     */
    public function getLeaveHours($fromDate, $toDate, $employeeId, $statuses)
    {
        $leaveSummary = [];
        $leaveRecords = $this->getLeaveRequestService()->getLeaveRecordsBetweenTwoDays(
            $fromDate,
            $toDate,
            $employeeId,
            $statuses
        );
        foreach ($leaveRecords as $leaveRecord) {
            $day = (new DateTime($leaveRecord->getDate()))->format('l');
            $duration = $leaveRecord->getLength_hours();
            $leaveType = $leaveRecord->toArray()['LeaveType']['name'];
            $leaveTypeId = $leaveRecord->toArray()['LeaveType']['id'];
            if (array_key_exists($day, $leaveSummary)) {
                $found = false;
                for ($i = 0; $i < count($leaveSummary[$day]); $i++) {
                    if ($leaveSummary[$day][$i]['type'] == $leaveType) {
                        $leaveSummary[$day][$i]['hours'] = $leaveSummary[$day][$i]['hours'] + $duration;
                        $found = true;
                    }
                }
                if (!$found) {
                    array_push(
                        $leaveSummary[$day],
                        ['typeId' => $leaveTypeId, 'type' => $leaveType, 'hours' => $duration]
                    );
                }
            } else {
                $leaveSummary[$day] = [['typeId' => $leaveTypeId, 'type' => $leaveType, 'hours' => $duration]];
            }
            foreach ($leaveSummary as $day => $leaves) {
                foreach ($leaves as $leave) {
                    $leave['hours'] = number_format($leave['hours'], 2);
                }
            }
        }
        return $leaveSummary;
    }

    /**
     * @param $fromDate
     * @param $toDate
     * @param $employeeId
     * @return array
     * @throws InvalidParamException
     */
    public function getWorkHours($fromDate, $toDate, $employeeId)
    {
        $result = [];
        $attendanceRecords = $this->getAttendanceService()->getAttendanceRecordsBetweenTwoDays(
            $fromDate,
            $toDate,
            $employeeId,
            PluginAttendanceRecord::STATE_PUNCHED_OUT
        );

        foreach ($attendanceRecords as $attendanceRecord) {
            $punchInDateTime1 = $attendanceRecord->getPunchInUtcTime();
            $punchInDateTime2 = $attendanceRecord->getPunchOutUtcTime();
            $day = (new DateTime($punchInDateTime1))->format('l');

            $duration = abs(strtotime($punchInDateTime2) - strtotime($punchInDateTime1)) / (60 * 60);

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
            self::PARAMETER_FROM_DATE => ['Date' => ['Y-m-d H:i:s']],
            self::PARAMETER_TO_DATE => ['Date' => ['Y-m-d H:i:s']],
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
     * @param AttendanceService $attendanceService
     */
    public function setAttendanceService(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
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
     *
     * @param LeaveRequestService $leaveRequestService
     */
    public function setLeaveRequestService(LeaveRequestService $leaveRequestService)
    {
        $this->leaveRequestService = $leaveRequestService;
    }

    /**
     *
     * @return LeaveRequestService
     */
    public function getLeaveRequestService(): LeaveRequestService
    {
        if (is_null($this->leaveRequestService)) {
            $this->leaveRequestService = new LeaveRequestService();
        }

        return $this->leaveRequestService;
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
     * @param EmployeeService $employeeService
     */
    public function setEmployeeService(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    /**
     * Get statuses
     *
     * @param $filter
     * @return array|null
     */
    protected function getStatusesArray($filter)
    {
        $statusIdArray = null;
        if (!empty($filter[self::PARAMETER_TAKEN]) && $filter[self::PARAMETER_TAKEN] == 'true') {
            $statusIdArray[] = PluginLeave::LEAVE_STATUS_LEAVE_TAKEN;
        }
        if (!empty($filter[self::PARAMETER_CANCELLED]) && $filter[self::PARAMETER_CANCELLED] == 'true') {
            $statusIdArray[] = PluginLeave::LEAVE_STATUS_LEAVE_CANCELLED;
        }
        if (!empty($filter[self::PARAMETER_PENDING_APPROVAL]) && $filter[self::PARAMETER_PENDING_APPROVAL] == 'true') {
            $statusIdArray[] = PluginLeave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL;
        }
        if (!empty($filter[self::PARAMETER_REJECTED]) && $filter[self::PARAMETER_REJECTED] == 'true') {
            $statusIdArray[] = PluginLeave::LEAVE_STATUS_LEAVE_REJECTED;
        }
        if (!empty($filter[self::PARAMETER_SCHEDULED]) && $filter[self::PARAMETER_SCHEDULED] == 'true') {
            $statusIdArray[] = PluginLeave::LEAVE_STATUS_LEAVE_APPROVED;
        }

        return $statusIdArray;
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
