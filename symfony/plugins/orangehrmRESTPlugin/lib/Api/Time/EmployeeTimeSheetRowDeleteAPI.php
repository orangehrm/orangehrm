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

namespace Orangehrm\Rest\Api\Time;

use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Http\Response;
use Orangehrm\Rest\Api\Time\Entity\TimeSheetItem;
use Orangehrm\Rest\Api\Time\Entity\TimeSheet;

class EmployeeTimeSheetRowDeleteAPI extends EndPoint
{

    const PARAMETER_TIMESHEET_ID = "timesheetId";
    const PARAMETER_EMPLOYEE_ID = 'id';
    const PARAMETER_START_DATE  = 'startDate';

    private $employeeService;

    /**
     * @return \EmployeeService|null
     */
    protected function getEmployeeService()
    {

        if ($this->employeeService != null) {
            return $this->employeeService;
        } else {
            return new \EmployeeService();
        }
    }

    public function setEmployeeService($employeeService)
    {
        $this->employeeService = $employeeService;
    }

    /**
     * @return TimesheetService
     */
    public function getTimesheetService()
    {
        if (is_null($this->timesheetService)) {

            $this->timesheetService = new \TimesheetService();
        }

        return $this->timesheetService;
    }

    /**
     * @param mixed $employeeEventService
     */
    public function setEmployeeEventService($employeeEventService)
    {
        $this->employeeEventService = $employeeEventService;
    }

    /**
     * Delete timeSheet rows
     *
     * @return Response
     * @throws BadRequestException
     * @throws InvalidParamException
     */
    public function deleteTimeSheetRows()
    {
        $filters = $this->filterParameters();
        $timeSheetId = $filters[self::PARAMETER_TIMESHEET_ID];
        $employeeId = $filters[self::PARAMETER_EMPLOYEE_ID];
        $startDate  = $filters[self::PARAMETER_START_DATE];
        $this->validateEmployee($employeeId);

        if (!empty($startDate)) {
            $employeeTimeSheet = $this->getTimesheetService()->getTimesheetByStartDateAndEmployeeId($startDate, $employeeId);
            if ($employeeTimeSheet != null) {
                $timeSheetId = $employeeTimeSheet->getTimesheetId();
            }else {
                throw new InvalidParamException("No Timesheet Available For Given Date");
            }
        }

        $isDeleted = $this->getTimesheetService()->deleteTimesheetItemsByTimesheetId($employeeId, $timeSheetId);
        if ($isDeleted) {
            return new Response(array('success' => 'Successfully Deleted'));

        } else {
            throw new BadRequestException("Unable To Delete Timesheet Rows");
        }
    }

    /**
     * Filter parameters
     *
     * @return array
     * @throws InvalidParamException
     */
    protected function filterParameters()
    {
        $filters[] = array();

        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_TIMESHEET_ID))) {
            $filters[self::PARAMETER_TIMESHEET_ID] = $this->getRequestParams()->getPostParam(self::PARAMETER_TIMESHEET_ID);
        } else {
            throw new InvalidParamException("Timesheet Id Needed");
        }
        if (!empty($this->getRequestParams()->getUrlParam(self::PARAMETER_EMPLOYEE_ID))) {
            $filters[self::PARAMETER_EMPLOYEE_ID] = $this->getRequestParams()->getUrlParam(self::PARAMETER_EMPLOYEE_ID);
        }
        if (!empty($this->getRequestParams()->getUrlParam(self::PARAMETER_START_DATE))) {
            $filters[self::PARAMETER_START_DATE] = $this->getRequestParams()->getUrlParam(self::PARAMETER_START_DATE);
        }

        return $filters;

    }

    public function deleteValidationRules()
    {
        return array(
            self::PARAMETER_TIMESHEET_ID => array('IntVal' => true, 'NotEmpty' => true),
            self::PARAMETER_START_DATE =>  array( 'Date' => array('Y-m-d')),
        );
    }

    /**
     * Check the employee is valid
     *
     * @param $empId
     * @throws BadRequestException
     */
    protected function validateEmployee($empId)
    {
        $employee = $this->getEmployeeService()->getEmployee($empId);
        if (!$employee instanceof \Employee) {
            throw new BadRequestException("Employee Not Found");
        }
    }

}




