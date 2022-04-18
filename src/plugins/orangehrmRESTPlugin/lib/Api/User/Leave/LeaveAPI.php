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

namespace Orangehrm\Rest\Api\User\Leave;

use LeavePeriodService;
use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Http\Response;
use Orangehrm\Rest\Api\User\Model\AttendanceLeaveTypeModel;
use Orangehrm\Rest\Api\User\Model\AttendanceLeaveModel;
use \Leave;
use \sfException;
use \sfContext;
use \LeaveRequestService;
use \EmployeeService;
use \PluginLeave;
use \BasicUserRoleManager;
use \UserRoleManagerFactory;
use \ServiceException;

class LeaveAPI extends EndPoint
{
    const PARAMETER_FROM_DATE = 'fromDate';
    const PARAMETER_TO_DATE = 'toDate';
    const PARAMETER_EMPLOYEE_NUMBER = 'empNumber';
    const PARAMETER_REJECTED = "rejected";
    const PARAMETER_CANCELLED = "cancelled";
    const PARAMETER_PENDING_APPROVAL = "pendingApproval";
    const PARAMETER_SCHEDULED = "scheduled";
    const PARAMETER_TAKEN = 'taken';

    protected $leaveRequestService;
    protected $workWeekService;
    protected $holidayService;
    /**
     * @var null|LeavePeriodService
     */
    protected $leavePeriodService = null;

    /**
     * @return LeavePeriodService
     */
    public function getLeavePeriodService(): LeavePeriodService
    {
        if (is_null($this->leavePeriodService)) {
            $this->leavePeriodService = new LeavePeriodService();
        }
        return $this->leavePeriodService;
    }

    /**
     * @param LeavePeriodService $leavePeriodService
     */
    public function setLeavePeriodService(LeavePeriodService $leavePeriodService)
    {
        $this->leavePeriodService = $leavePeriodService;
    }

    public function getLeaveRecords()
    {
        $params = $this->getParameters();
        $loggedInEmpNumber = $this->getLoggedInEmployeeNumber();
        $empNumber = $params[self::PARAMETER_EMPLOYEE_NUMBER];
        if($params[self::PARAMETER_FROM_DATE]>$params[self::PARAMETER_TO_DATE]){
            throw new InvalidParamException(
                'Invalid date Period'
            );
        }
        if (empty($empNumber)) {
            $empNumber = $loggedInEmpNumber;
        }
        if (!in_array($empNumber, $this->getAccessibleEmpNumbers()) && $loggedInEmpNumber!=$empNumber) {
            throw new BadRequestException('Access Denied');
        }
        $response = $this->getAttendanceFinalDetails($params, $empNumber);
        return new Response(
            $response
        );
    }

    public function getAttendanceFinalDetails($params, int $empNumber)
    {
        $statuses = $this->getStatusesArray($params);
        $leaveHoursResultArray = $this->getLeaveRequestService()->getLeaveRecordsBetweenTwoDays(
            $params[self::PARAMETER_FROM_DATE],
            $params[self::PARAMETER_TO_DATE],
            $empNumber,
            $statuses
        );
        if (count($leaveHoursResultArray) == 0) {
            throw new RecordNotFoundException('No Records Found');
        }
        $result = [];
        foreach ($leaveHoursResultArray as $leaveResult) {
            $status_id = $leaveResult->getStatus();
            $leaveResultArray = (new AttendanceLeaveModel($leaveResult))->toArray();
            $formattedLeaveTypeArray = (new AttendanceLeaveTypeModel($leaveResultArray['leaveType']))->toArray();
            $leaveResultArray['leaveType'] = $formattedLeaveTypeArray;

            $leaveStatusName = Leave::getTextForLeaveStatus($status_id);
            $leaveResultArray['status'] = $leaveStatusName;
            array_push($result, $leaveResultArray);
        }
        return $result;
    }

    public function getParameters()
    {
        $params = array();
        $fromDate = $this->getRequestParams()->getQueryParam(self::PARAMETER_FROM_DATE);
        $toDate = $this->getRequestParams()->getQueryParam(self::PARAMETER_TO_DATE);
        if (empty($fromDate) && empty($toDate)) {
            $currentLeavePeriod = $this->getLeavePeriodService()->getCurrentLeavePeriodByDate(date('Y-m-d'));
            $fromDate = $currentLeavePeriod[0];
            $toDate = $currentLeavePeriod[1];
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

    public function getLeaveHours($fromDate, $toDate, $employeeId, $statuses)
    {
        return $this->getLeaveRequestService()->getLeaveRecordsBetweenTwoDays(
            $fromDate,
            $toDate,
            $employeeId,
            $statuses
        );
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
     * Sets EmployeeService
     * @param EmployeeService $service
     */
    public function setEmployeeService(EmployeeService $service)
    {
        $this->employeeService = $service;
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
     *
     * @param LeaveRequestService $leaveRequestService
     */
    public function setLeaveRequestService(LeaveRequestService $leaveRequestService)
    {
        $this->leaveRequestService = $leaveRequestService;
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
