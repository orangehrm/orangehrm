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
use Respect\Validation\Validator as v;

class EmployeeTimeSheetAPI extends EndPoint
{

    const PARAMETER_EMPLOYEE_ID = "employeeId";
    const PARAMETER_TIMESHEET_ID = "timesheetId";
    const PARAMETER_INITIAL_ROWS = "initialRows";
    const PARAMETER_START_DATE = "startDate";
    const PARAMETER_ID = "id";
    const PARAMETER_TIMESHEET_DATA = 'timesheetData';

    private $employeeService;
    private $projectService;

    /**
     *
     * @return \ProjectService
     */
    public function getProjectService()
    {
        if (is_null($this->projectService)) {
            $this->projectService = new \ProjectService();
        }
        return $this->projectService;
    }

    public function setProjectService(\ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

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
     * Get employee timeshees
     *
     * @return Response
     * @throws RecordNotFoundException
     */
    public function getEmployeeTimesheets()
    {
        $responseArray = null;
        $empId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $this->validateEmployee($empId);
        $startDate = $this->getRequestParams()->getUrlParam(self::PARAMETER_START_DATE);
        $timeSheets = null;

        if (empty($startDate)) {
            $timeSheets = $this->getTimesheetService()->getTimesheetByEmployeeId($empId);
        } else {
            $employeeTimeSheet = $this->getTimesheetService()->getTimesheetByStartDateAndEmployeeId($startDate, $empId);
            if($employeeTimeSheet != null) {
                $timeSheets [] = $employeeTimeSheet;
            }
        }

        if (count($timeSheets) > 0) {
            foreach ($timeSheets as $timeSheet) {

                $timeSheetEntity = new TimeSheet();
                $timeSheetEntity->buildTimeSheet($timeSheet);
                $timeSheetItems = null;
                $timeSheetItems = $this->getTimesheetService()->getTimesheetItem($timeSheet->getTimesheetId(), $empId);
                $timeSheetItemArray  = null;

                foreach ($timeSheetItems as $item) {

                    $timeSheetItemEntity = new TimeSheetItem();
                    $timeSheetItemEntity->buildTimeSheetItem($item);
                    $timeSheetItemArray[] = $timeSheetItemEntity->toArray();
                }
                $timeSheetEntity->setTimeSheetItems($timeSheetItemArray);
                $responseArray[] = $timeSheetEntity->toArray();
            }
            return new Response($responseArray, array());
        } else {
            throw new RecordNotFoundException("No TimeSheets Found");
        }

    }

    /**
     * Save employee Timesheet
     *
     * @return Response
     * @throws BadRequestException
     * @throws InvalidParamException
     */
    public function saveEmployeeTimeSheets()
    {
        $filters = $this->filterParameters();
        $this->validateEmployee($filters[self::PARAMETER_ID]);
        $startDate = $filters[self::PARAMETER_START_DATE];
        $datetime = new \DateTime('tomorrow');
        $startDateTime =  new \DateTime($startDate);
        if($datetime < $startDateTime ) {
            throw new InvalidParamException("Future Timesheets Not Allowed");
        }

        $statusArray = $this->getTimesheetService()->createTimesheet($filters[self::PARAMETER_ID],
            $startDate);

        switch ($statusArray['state']) {
            case $statusArray['state'] == 1:
                throw new InvalidParamException("Overlapping Timesheets Found");
                break;
            case $statusArray['state'] == 2:
                throw new InvalidParamException("No Matching Timesheet Can Be Found");
                break;
            case $statusArray['state'] == 3:
                return new Response(array('success' => 'Successfully Created'));
                break;
            case $statusArray['state'] == 4:
                throw new InvalidParamException("No Accessible Timesheets Or Timesheet Already Exists");
                break;
        }
    }

    /**
     * Update timesheets
     *
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     */
    public function updateEmployeeTimeSheet()
    {
        $filters = $this->filterParametersForUpdate();

        if(!$filters[self::PARAMETER_TIMESHEET_DATA]){
            throw new BadRequestException("Invalid JSON data provided");
        }
        $this->validateEmployee($filters[self::PARAMETER_ID]);
        $this->validateTimeSheet($filters);
        $timeSheet = $this->getTimesheetService()->getTimesheetByStartDateAndEmployeeId($filters[self::PARAMETER_TIMESHEET_DATA]['startDate'],
            $filters[self::PARAMETER_ID]);
        if ($timeSheet instanceof \Timesheet) {

            $initialRows = $filters[self::PARAMETER_TIMESHEET_DATA]['timeSheetItems'];
            $initialRows = $this->validateTimeSheetData($initialRows, $timeSheet->getTimesheetId());

            $state = $filters[self::PARAMETER_TIMESHEET_DATA]['state'];
            if ($timeSheet->getState() != $state) {
                $state = $this->getTimeSheetState($state);
                $timeSheet->setState($state);
                $comment = $filters[self::PARAMETER_TIMESHEET_DATA]['state'];
                $this->getTimesheetService()->saveTimesheet($timeSheet);
                $this->setTimesheetActionLog($state, $comment, $timeSheet->getTimesheetId(),
                    $timeSheet->getEmployeeId());
            }

            $endDate = $timeSheet->getEndDate();
            $startDate = $timeSheet->getStartDate();
            $currentWeekDates = $this->getDatesOfTheTimesheetPeriod($startDate, $endDate);

            try {
                    $this->getTimesheetService()->saveTimesheetItems($initialRows, $filters[self::PARAMETER_ID],
                    $timeSheet->getTimesheetId(), $currentWeekDates, 0,false);

            } catch (\Exception $e) {
                throw new InvalidParamException($e->getMessage());
            }
            return new Response(array('success' => 'Successfully Updated'));
        } else {
            throw new RecordNotFoundException("Timesheet Not Found");
        }

    }

    /**
     * Get dates of the TimeSheet period
     *
     * @param $startDate
     * @param $endDate
     * @return array
     */
    public function getDatesOfTheTimesheetPeriod($startDate, $endDate)
    {

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
        if (!empty($this->getRequestParams()->getUrlParam(self::PARAMETER_ID))) {
            $filters[self::PARAMETER_ID] = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        }

        return $filters;

    }

    /**
     * Filter parameters for update
     *
     * @return array
     */
    protected function filterParametersForUpdate()
    {
        $filters[] = array();

        if (!empty($this->getRequestParams()->getUrlParam(self::PARAMETER_ID))) {
            $filters[self::PARAMETER_ID] = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        }
        $filters[self::PARAMETER_TIMESHEET_DATA] = json_decode($this->getRequestParams()->getContent(), true);

        return $filters;
    }

    /**
     * Validate timesheet data
     *
     * @param $initialRows
     * @param $timeSheetId
     * @return mixed
     */
    function validateTimeSheetData($initialRows, $timeSheetId)
    {
        $count = 0;

        $time = null;
        $timeDays = null;
        foreach ($initialRows as $initialRow) {

            $projectId = $initialRow['projectId'];
            $activityId = $initialRow['projectActivityId'];
            $this->validateProject($projectId,$activityId);
            $initialRow['projectActivityName'] = 1; // inside timeSheet service activity name is taken as activity id
            $timeSheetrow['row'] = $initialRow;
            $this->validateTimeSheetRow($timeSheetrow);
            $count++;

            for ($i = 0; $i < 7; $i++) {

                $timeDays[$i][$count] = $this->getTimeSheetItemValue($count, $i, $initialRow, $timeSheetId,$projectId,$activityId);
            }

        }
        return $initialRows;
    }

    /**
     * Get timesheet item value
     *
     * @param $count
     * @param $day
     * @param $row
     * @param $timeSheetId
     * @return mixed
     * @throws InvalidParamException
     */
    function getTimeSheetItemValue($count, $day, $row, $timeSheetId,$projectId,$activityId)
    {
        if (!empty($row['TimesheetItemId' . $day])) {

            $this->checkTimeSheetItem($row['TimesheetItemId' . $day], $timeSheetId,$projectId,$activityId);

            if (!empty($row[$day])) {

                return $row[$day];

            } else {
                throw new InvalidParamException("TimesheetItemId Is Not Set For TimeSheet Row :" . $count . " Day :" . $day);
            }
        }
    }

    /**
     * Sum time for a day
     *
     * @param $times
     * @return string
     */
    function sumTime($times)
    {
        $total = 0;
        foreach ($times as $t) {
            $total += $this->toSeconds($t);
        }
        return $this->toTime($total);
    }

    /**
     * Converting to seconds
     *
     * @param $time
     * @return int
     */
    function toSeconds($time)
    {
        $parts = explode(':', $time);
        return 3600 * $parts[0] + 60 * $parts[1] + $parts[2];
    }


    /**
     * Check time for a day
     *
     * @param $seconds
     * @return string
     * @throws InvalidParamException
     */
    function toTime($seconds)
    {
        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;
        if ($hours >= 24) {
            throw new InvalidParamException('Total Should Be Less Than 24 Hours');
        }
        return $hours . ':' . $minutes . ':' . $seconds;

    }

    /**
     * Check timesheet items ( validating )
     *
     * @param $itemId
     * @param $timeSheetId
     * @param $projectId
     * @param $activityId
     * @return bool
     * @throws RecordNotFoundException
     */
    function checkTimeSheetItem($itemId, $timeSheetId,$projectId,$activityId)
    {
        $timeSheetItem = $this->getTimesheetService()->getTimesheetItemById($itemId);
        if ($timeSheetItem instanceof \TimesheetItem && $timeSheetItem->getTimesheetId() == $timeSheetId && $timeSheetItem->getProjectId() == $projectId && $timeSheetItem->getActivityId() == $activityId) {
          return true;
        } else {
            throw new RecordNotFoundException("Timesheet Item Not Found For :" . $itemId);
        }
    }

    /**
     * Set timesheet action logs
     *
     * @param $state
     * @param $comment
     * @param $timesheetId
     * @param $employeeId
     */
    protected function setTimesheetActionLog($state, $comment, $timesheetId, $employeeId)
    {
        $timesheetActionLog = new \TimesheetActionLog();
        $timesheetActionLog->setAction($state);
        $timesheetActionLog->setComment($comment);
        $timesheetActionLog->setTimesheetId($timesheetId);
        $timesheetActionLog->setDateTime(date("Y-m-d"));
        $timesheetActionLog->setPerformedBy($employeeId);

        $this->getTimesheetService()->saveTimesheetActionLog($timesheetActionLog);
    }

    /**
     * Validate timesheet state,
     * this method check the timesheet state if it's been requested to change
     *
     * @param $state
     * @return mixed
     * @throws InvalidParamException
     */
    protected function getTimeSheetState($state)
    {
        if ($state == "NOT SUBMITTED" || $state == "SUBMITTED" || $state == "APPROVED" || $state == "REJECTED") {
            return $state;
        } else {
            throw new InvalidParamException("Invalid Timesheet State");
        }
    }

    /**
     * Validate time sheet rows with respect validation
     *
     * @param $timeSheetrow
     */
    protected function validateTimeSheetRow($timeSheetRow)
    {
        v::key('row', v::key('projectId', v::intVal()))
            ->key('row', v::key('projectActivityId', v::intVal()))
            ->key('row', v::key('TimesheetItemId0', v::optional(v::intVal())))
            ->key('row', v::key('TimesheetItemId1', v::optional(v::intVal())))
            ->key('row', v::key('TimesheetItemId2', v::optional(v::intVal())))
            ->key('row', v::key('TimesheetItemId3', v::optional(v::intVal())))
            ->key('row', v::key('TimesheetItemId4', v::optional(v::intVal())))
            ->key('row', v::key('TimesheetItemId5', v::optional(v::intVal())))
            ->key('row', v::key('TimesheetItemId6', v::optional(v::intVal())))
            ->key('row', v::key('0', v::optional(v::regex('/^([01]?[0-9]|2[0-3]):[0-5][0-9]|24:00/'))))
            ->key('row', v::key('1', v::optional(v::regex('/^([01]?[0-9]|2[0-3]):[0-5][0-9]|24:00/'))))
            ->key('row', v::key('2', v::optional(v::regex('/^([01]?[0-9]|2[0-3]):[0-5][0-9]|24:00/'))))
            ->key('row', v::key('3', v::optional(v::regex('/^([01]?[0-9]|2[0-3]):[0-5][0-9]|24:00/'))))
            ->key('row', v::key('4', v::optional(v::regex('/^([01]?[0-9]|2[0-3]):[0-5][0-9]|24:00/'))))
            ->key('row', v::key('5', v::optional(v::regex('/^([01]?[0-9]|2[0-3]):[0-5][0-9]|24:00/'))))
            ->key('row', v::key('6', v::optional(v::regex('/^([01]?[0-9]|2[0-3]):[0-5][0-9]|24:00/'))))
            ->check($timeSheetRow);
    }

    /**
     * Validate time sheet  with respect validation
     *
     * @param $timeSheetrow
     */
    protected function validateTimeSheet($timeSheet)
    {
             v::key(self::PARAMETER_TIMESHEET_DATA, v::key('startDate', v::date('Y-m-d')))
            ->key(self::PARAMETER_TIMESHEET_DATA, v::key('comment', v::optional(v::length(1,250))))

            ->check($timeSheet);
    }

    public function getValidationRules()
    {
        return array(
            self::PARAMETER_START_DATE => array( 'Date' => array('Y-m-d'))

        );
    }

    public function postValidationRules()
    {
        return array(
            self::PARAMETER_START_DATE => array( 'Date' => array('Y-m-d'))

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

    /**
     * @param $projectId
     * @return bool
     * @throws BadRequestException
     */
    protected function validateProject($projectId,$activityId)
    {

        $project = $this->getProjectService()->getProjectById($projectId,$activityId);

        if ($project instanceof \Project && $project->is_deleted != 1) {
            $this->validateActivity($projectId,$activityId);
            return true;
        } else {
            throw new BadRequestException("Project Not Found");
        }
    }

    protected function validateActivity($projectId,$activityId){

        $activities = $this->getProjectService()->getActivityListByProjectId($projectId);

            if(!empty($activities)){


                foreach ($activities as $activity){

                    if($activity->getActivityId() == $activityId && $activity->getIsDeleted()  == 0 ){
                       return true;
                    }
                }

            }else {
                throw new BadRequestException("Activity Not Found");
            }
                throw new BadRequestException("Activity Not Found");
        }


}



