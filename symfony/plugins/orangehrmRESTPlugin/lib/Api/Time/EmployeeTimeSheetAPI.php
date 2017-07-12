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
use Orangehrm\Rest\Http\Response;

class EmployeeTimeSheetAPI extends EndPoint
{

    const PARAMETER_EMPLOYEE_ID = "employeeId";
    const PARAMETER_TIMESHEET_ID = "timesheetId";
    const PARAMETER_INITIAL_ROWS = "initialRaws";
    const PARAMETER_START_DATE = "startDate";
    const PARAMETER_ID = "id";

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
    public function getTimesheetService() {

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
     * get employee timesheets
     *
     * @return Response
     * @throws InvalidParamException
     */
    public function getEmployeeTimesheets()
    {
        $responseArray = null;
        $empId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);

        $timesheets = $this->getTimesheetService()->getTimesheetByEmployeeId($empId);

        foreach ($timesheets as $timesheet) {

            $responseArray[] = $timesheet->toArray();
        }
        return new Response($responseArray, array());
    }

    /**
     * Save employee Timesheet
     *
     * @return Response
     * @throws BadRequestException
     * @throws InvalidParamException
     */
    public function saveEmployeeTimeSheets($initialRaws)
    {
        $filters = $this->filterParameters();
        $this->getTimesheetService()->createTimesheet($filters[self::PARAMETER_EMPLOYEE_ID], $filters[self::PARAMETER_START_DATE]);
        $timesheet = $this->getTimesheetService()->getTimesheetByStartDateAndEmployeeId($filters[self::PARAMETER_START_DATE], $filters[self::PARAMETER_EMPLOYEE_ID]);
        $endDate = $timesheet->getEndDate();
        $startDate = $timesheet->getStartDate();
        $currentWeekDates = $this->getDatesOfTheTimesheetPeriod($startDate, $endDate);
        $initialRaws = json_decode($initialRaws, true);
        $result = $this->getTimesheetService()->saveTimesheetItems($initialRaws, $filters[self::PARAMETER_EMPLOYEE_ID], $timesheet->getTimesheetId(), $currentWeekDates, 0);
        return new Response(array('success' => 'Successfully Saved'));
    }


    public function getDatesOfTheTimesheetPeriod($startDate, $endDate) {

        if ($startDate < $endDate) {
            $dates_range[] = $startDate;

            $startDate = strtotime($startDate);
            $endDate = strtotime($endDate);


            while (date('Y-m-d', $startDate) != date('Y-m-d', $endDate)) {
                $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate) + 1, date("Y", $startDate));
                $dates_range[] = date('Y-m-d', $startDate);
            }
        }
        return $dates_range;
    }


    /**
     * Filter Post parameters to validate
     *
     * @return array
     *
     */
    protected function filterParameters()
    {
        $filters[] = array();

        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_EMPLOYEE_ID))) {
            $filters[self::PARAMETER_EMPLOYEE_ID] = $this->getRequestParams()->getPostParam(self::PARAMETER_EMPLOYEE_ID);
        }

        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_TIMESHEET_ID))) {
            $filters[self::PARAMETER_TIMESHEET_ID] = $this->getRequestParams()->getPostParam(self::PARAMETER_TIMESHEET_ID);
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_INITIAL_ROWS))) {
            $filters[self::PARAMETER_INITIAL_ROWS] = $this->getRequestParams()->getPostParam(self::PARAMETER_INITIAL_ROWS);
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_START_DATE))) {
            $filters[self::PARAMETER_START_DATE] = $this->getRequestParams()->getPostParam(self::PARAMETER_START_DATE);
        }

        return $filters;

    }


}


