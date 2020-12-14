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
use Orangehrm\Rest\Api\Leave\LeaveRequestAPI;
use Orangehrm\Rest\Http\Response;
use \LeaveRequestService;
use \PluginAttendanceRecord;

class GraphAPI extends LeaveRequestAPI
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
    protected $dayMapper;

    /**
     * @return Response
     * @throws BadRequestException
     * @throws InvalidParamException
     */
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
        $date1 = new \DateTime($params[self::PARAMETER_FROM_DATE]);
        $date2 = new \DateTime($params[self::PARAMETER_TO_DATE]);
        $diff = $date1->diff($date2)->days;
        if ($diff != 6) {
            throw new InvalidParamException(
                'Duration should be one week   e.g :- fromDate=2020-11-24 & toDate=2020-11-30'
            );
        }
        $statuses=$this->getStatusesArray($params);
        $dayMapper=[];
        foreach(self::WEEK_DAYS as $weekDay){
            $dayMapper[$weekDay]=strtolower($weekDay);
        }
        $this->dayMapper=$dayMapper;

        $workHoursResult = $this->getWorkHours(
            $params[self::PARAMETER_FROM_DATE],
            $params[self::PARAMETER_TO_DATE],
            $loggedInEmpNumber
        );
        $leaveHoursResult = $this->getLeaveHours(
            $params[self::PARAMETER_FROM_DATE],
            $params[self::PARAMETER_TO_DATE],
            $loggedInEmpNumber,
            $statuses
        );
        $workSummary = array();
        foreach (self::WEEK_DAYS as $day) {
            $mappedDay=$this->dayMapper[$day];
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
                $type =$singleLeaveType['type'];
                $hours = $singleLeaveType['hours'];
                $typeId =$singleLeaveType['typeId'];
                $totalLeaveHours = $totalLeaveHours + $hours;

                $found = false;

                for($i=0;$i<count($totalLeaveTypeHours);$i++){
                    if($totalLeaveTypeHours[$i]['type']==$type){
                        $totalLeaveTypeHours[$i]['hours']= number_format($totalLeaveTypeHours[$i]['hours']+$hours,2);
                        $found=true;
                    }
                }
                if (!$found) {
                    array_push($totalLeaveTypeHours, ['typeId'=>$typeId,'type' => $type, 'hours' => $hours]);
                }
            }
        }
        $totalWorkHours= number_format($totalWorkHours,2);
        $totalLeaveHours= number_format($totalLeaveHours,2);
        return new Response(
            array(
                'totalWorkHours'=>$totalWorkHours,
                'totalLeaveHours'=>$totalLeaveHours,
                'totalLeaveTypeHours'=> $totalLeaveTypeHours,
                'workSummary'=>$workSummary
            )
        );
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

    /**
     * @param $fromDate
     * @param $toDate
     * @param $employeeId
     * @return array|string
     * @throws \Exception
     */
    public function getLeaveHours($fromDate, $toDate, $employeeId,$statuses)
    {
        $leaveSummary = [];
        $leaveRecords = $this->getLeaveRequestService()->getLeaveRecordsBetweenTwoDays($fromDate, $toDate, $employeeId,$statuses);

        foreach ($leaveRecords as $leaveRecord) {
            $day = (new \DateTime($leaveRecord->getDate()))->format('l');
            $duration = $leaveRecord->getLength_hours();
            $leaveType = $leaveRecord->toArray()['LeaveType']['name'];
            $leaveTypeId = $leaveRecord->toArray()['LeaveType']['id'];
            if (array_key_exists($day, $leaveSummary)) {
                $found = false;
                for ($i=0;$i<count($leaveSummary[$day]);$i++){
                    if($leaveSummary[$day][$i]['type']==$leaveType){
                        $leaveSummary[$day][$i]['hours']=$leaveSummary[$day][$i]['hours']+$duration;
                        $found=true;
                    }
                }
                if (!$found) {
                    array_push($leaveSummary[$day], ['typeId'=>$leaveTypeId,'type' => $leaveType, 'hours' => $duration]);
                }
            } else {
                $leaveSummary[$day] = [['typeId'=>$leaveTypeId,'type' => $leaveType, 'hours' => $duration]];
            }
            foreach ($leaveSummary as $day=> $leaves){
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
            throw new BadRequestException($e->getMessage());
        }
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
