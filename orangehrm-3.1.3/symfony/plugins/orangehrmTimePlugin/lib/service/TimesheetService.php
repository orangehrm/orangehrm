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
class TimesheetService {

    // Timesheet Data Access Object
    private $timesheetDao;
    private $employeeDao;
    private $timesheetPeriodService;
    
    // Cache timesheet time format for better performance.
    private static $timesheetTimeFormat = null;

    /**
     * Get the Timesheet Data Access Object
     * @return TimesheetDao
     */
    public function getTimesheetDao() {

        if (is_null($this->timesheetDao)) {
            $this->timesheetDao = new TimesheetDao();
        }
        return $this->timesheetDao;
    }

    /**
     * Set TimesheetData Access Object
     * @param TimesheetDao $TimesheetDao
     * @return void
     */
    public function setTimesheetDao(TimesheetDao $timesheetDao) {

        $this->timesheetDao = $timesheetDao;
    }

    /**
     * Set EmployeeData Access Object
     * @param EmployeeDao $employeeDao
     * @return void
     */
    public function setEmployeeDao(EmployeeDao $employeeDao) {

        $this->employeeDao = $employeeDao;
    }

    /**
     * Get the Employee Data Access Object
     * @return EmployeeDao
     */
    public function getEmployeeDao() {

        if (is_null($this->employeeDao)) {
            $this->employeeDao = new EmployeeDao();
        }
        return $this->employeeDao;
    }

    public function getTimesheetPeriodService() {

        if (is_null($this->timesheetPeriodService)) {
            $this->timesheetPeriodService = new TimesheetPeriodService();
        }

        return $this->timesheetPeriodService;
    }

    public function setTimesheetPeriodDao(TimesheetPeriodService $timesheetPeriodService) {

        $this->timesheetPeriodService = $timesheetPeriodService;
    }

    /**
     * Add, Update Timesheet
     * @param Timesheet $timesheet
     * @return boolean
     */
    public function saveTimesheet(Timesheet $timesheet) {

        return $this->getTimesheetDao()->saveTimesheet($timesheet);
    }

    /**
     * Get Timesheet by given timesheetId
     * @param int $timesheetId
     * @return Timesheet $timesheet
     */
    public function getTimesheetById($timesheetId) {

        $timesheet = $this->getTimesheetDao()->getTimesheetById($timesheetId);

        if (!$timesheet instanceof Timesheet) {
            $timesheet = new Timesheet();
        }

        return $timesheet;
    }

    /**
     * Get Timesheet by given Start Date
     * @param int $startDate
     * @return Timesheet $timesheet
     */
    public function getTimesheetByStartDate($startDate) {

        $timesheet = $this->getTimesheetDao()->getTimesheetByStartDate($startDate);

        return $timesheet;
    }

    /**
     * Get TimesheetItem by given Id
     * @param int $timesheetItemId
     * @return TimesheetItem $timesheetItem
     */
    public function getTimesheetItemById($timesheetItemId) {

        $timesheetItem = $this->getTimesheetDao()->getTimesheetItemById($timesheetItemId);

        return $timesheetItem;
    }

    /**
     * Get Timesheet by given Start Date and Employee Id
     * @param $startDate , int $employeeId
     * @return Timesheet $timesheet
     */
    public function getTimesheetByStartDateAndEmployeeId($startDate, $employeeId) {

        $timesheet = $this->getTimesheetDao()->getTimesheetByStartDateAndEmployeeId($startDate, $employeeId);

        return $timesheet;
    }

    public function getTimesheetByEmployeeId($employeeId) {

        return $this->getTimesheetDao()->getTimesheetByEmployeeId($employeeId);
    }

    /**
     * Get Timesheet by given Employee Id and state list
     * @param $employeeId, $stateList
     * @return Timesheet $timesheet
     */
    public function getTimesheetByEmployeeIdAndState($employeeId, $stateList) {

        return $this->getTimesheetDao()->getTimesheetByEmployeeIdAndState($employeeId, $stateList);
    }

    /**
     * Return an Array of Timesheets for given Employee Ids and States
     * 
     * <pre>
     * Ex: $employeeIdList = array('1', '2')
     *     $stateList = array('SUBMITTED', 'ACCEPTED');
     * 
     * For above $employeeIdList and $stateList parameters there will be an array like below as the response.
     *
     * array(
     *          0 => array('timesheetId' => 2, 'timesheetStartday' => '2011-04-22', 'timesheetEndDate' => '2011-04-19', 'employeeId' => 2, 'employeeFirstName' => 'Kayla', 'employeeLastName' => 'Abay'),
     *          1 => array('timesheetId' => 8, 'timesheetStartday' => '2011-04-22', 'timesheetEndDate' => '2011-04-28', 'employeeId' => 1, 'employeeFirstName' => 'John', 'employeeLastName' => 'Dunion')
     * )
     * </pre>
     * 
     * @version 2.7.1
     * @param Array $employeeIdList Array of Employee Ids
     * @param Array $stateList Array of States
     * @param $limit Number of Timesheets return
     * @return Array of Timesheets
     */
    public function getTimesheetListByEmployeeIdAndState($employeeIdList, $stateList, $limit) {
        return $this->getTimesheetDao()->getTimesheetListByEmployeeIdAndState($employeeIdList, $stateList, $limit);
    }
    
    public function getStartAndEndDatesList($employeeId) {

        $resultArray = $this->getTimesheetDao()->getStartAndEndDatesList($employeeId);

        return $resultArray;
    }

    /**
     * Add or Save TimesheetActionLog
     * @param Timesheet $timesheet
     * @return boolean
     */
    public function saveTimesheetActionLog(TimesheetActionLog $timesheetActionLog) {

        return $this->getTimesheetDao()->saveTimesheetActionLog($timesheetActionLog);
    }

    /**
     * Get TimesheetActionLog
     * @param TimesheetId $timesheetId
     * @return
     */
    public function getTimesheetActionLogByTimesheetId($timesheetId) {

        return $this->getTimesheetDao()->getTimesheetActionLogByTimesheetId($timesheetId);
    }

    public function saveTimesheetItems($inputTimesheetItems, $employeeId, $timesheetId, $keysArray, $initialRows) {

        foreach ($inputTimesheetItems as $inputTimesheetItem) {
            $activityId = $inputTimesheetItem['projectActivityName'];
            if ($activityId != null) {
                $activity = $this->getTimesheetDao()->getProjectActivityByActivityId($activityId);
                $projectId = $activity->getProjectId();

                $tempArray = array_slice($inputTimesheetItem, 3);
                for ($i = 0; $i < sizeof($keysArray); $i++) {

                    $date = $keysArray[$i];
                    $timesheetItemId = $inputTimesheetItem['TimesheetItemId' . $i];
                    $timesheetItemDuration = $inputTimesheetItem[$i];
                    if ($timesheetItemId != null) {

                        $existingTimesheetItem = $this->getTimesheetDao()->getTimesheetItemById($timesheetItemId);
                        $existingTimesheetItem->setProjectId($projectId);
                        $existingTimesheetItem->setActivityId($activityId);

                        if ($timesheetItemDuration == null) {
                            $timesheetItemDuration = 0;
                        }

                        $existingTimesheetItem->setDuration($this->convertDurationToSeconds($timesheetItemDuration));

                        $existingTimesheetItem->save();
                    } else if ($timesheetItemDuration != null) {

                        $existingTimesheetItem = $this->getTimesheetDao()->getTimesheetItemByDateProjectId($timesheetId, $employeeId, $projectId, $activityId, $date);

                        if ($existingTimesheetItem[0]->getProjectId() != null) {
                            $existingTimesheetItem[0]->setProjectId($projectId);
                            $existingTimesheetItem[0]->setActivityId($activityId);
                            $existingTimesheetItem[0]->setDuration($this->convertDurationToSeconds($timesheetItemDuration));

                            $existingTimesheetItem[0]->save();
                        } else {
                            $newTimesheetItem = new TimesheetItem();

                            $newTimesheetItem->setProjectId($projectId);
                            $newTimesheetItem->setActivityId($activityId);
                            $newTimesheetItem->setDate($date);
                            $newTimesheetItem->setDuration($this->convertDurationToSeconds($timesheetItemDuration));
                            $newTimesheetItem->setTimesheetId($timesheetId);
                            $newTimesheetItem->setEmployeeId($employeeId);

                            $this->getTimesheetDao()->saveTimesheetItem($newTimesheetItem);
                        }
                    }
                }
            }
        }
    }

    public function deleteTimesheetItems($employeeId, $timesheetId, $projectId, $activityId) {


        return $this->getTimesheetDao()->deleteTimesheetItems($employeeId, $timesheetId, $projectId, $activityId);
    }

    /**
     * get pending approvel timesheets
     * @param
     * @return supervispr approved timesheets array
     */
    public function getPendingApprovelTimesheetsForAdmin() {

        return $this->getTimesheetDao()->getPendingApprovelTimesheetsForAdmin();
    }

    public function getTimesheetTimeFormat() {
        if (is_null(self::$timesheetTimeFormat)) {
            self::$timesheetTimeFormat = $this->getTimesheetDao()->getTimesheetTimeFormat();
        }
        return self::$timesheetTimeFormat;
    }

    public function convertDurationToHours($durationInSecs) {

        $timesheetTimeFormat = $this->getTimesheetTimeFormat();

        if ($timesheetTimeFormat == '1') {

            $padHours = false;
            $hms = "";
            $hours = intval(intval($durationInSecs) / 3600);
            $hms .= ( $padHours) ? str_pad($hours, 2, "0", STR_PAD_LEFT) . ':' : $hours . ':';
            $minutes = intval(($durationInSecs / 60) % 60);
            $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT);
            return $hms;
        } elseif ($timesheetTimeFormat == '2') {

            $durationInHours = number_format($durationInSecs / (60 * 60), 2, '.', '');
            return $durationInHours;
        }
    }

    public function convertDurationToSeconds($duration) {

        $find = ':';
        $pos = strpos($duration, $find);

        if ($pos !== false) {

            $str_time = $duration;
            sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
            $durationInSeconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 3600 + $minutes * 60;
            return $durationInSeconds;
        } else {
            $durationInSeconds = $duration * 60 * 60;
            return $durationInSeconds;
        }
    }

    public function getActivityByActivityId($activityId) {

        $activity = $this->getTimesheetDao()->getActivityByActivityId($activityId);

        return $activity;
    }

    function addConvertTime($initialTime, $timeToAdd) {

        $old = explode(":", $initialTime);
        $play = explode(":", $timeToAdd);


        $hours = $old[0] + $play[0];

        $minutes = $old[1] + $play[1];

        if ($minutes > 59) {
            $minutes = $minutes - 60;
            $hours++;
        }
        if ($minutes < 10) {
            $minutes = "0" . $minutes;
        }
        if ($minutes == 0) {
            $minutes = "00";
        }
        $sum = $hours . ":" . $minutes;
        return $sum;
    }

    function dateDiff($start, $end) {

        $start_ts = strtotime($start);
        $end_ts = strtotime($end);
        $diff = $end_ts - $start_ts;
        return round($diff / 86400) + 1;
    }

    public function getProjectList() {

        return $this->getTimesheetDao()->getProjectList();
    }

    public function getProjectListForValidation() {

        return $this->getTimesheetDao()->getProjectListForValidation();
    }
    
    /**
     * Return an Array of Project Names
     * 
     * <pre>
     * This will return an array like below as the response.
     *
     * array(
     *          0 => array('projectId' => 1, 'projectName' => 'UB', 'customerName' => 'University of Belize')
     *          1 => array('projectId' => 2, 'projectName' => 'KM2', 'customerName' => 'KM2 Solutions')
     * )
     * </pre>
     * 
     * @version 2.7.1
     * @param Boolean $excludeDeletedProjects Exclude deleted projects or not
     * @param String $orderField Sort order field
     * @param String $orderBy Sort order
     * @return Array of Project Names
     */
    public function getProjectNameList($excludeDeletedProjects = true, $orderField = 'project_id', $orderBy = 'ASC') {
        return $this->getTimesheetDao()->getProjectNameList($excludeDeletedProjects, $orderField, $orderBy);
    }

    /**
     * Return an Array of Project Activities by Project Id
     * 
     * <pre>
     * Ex: $projectId = 1
     *     $excludeDeletedActivities = true;
     * 
     * For above $projectId and $excludeDeletedActivities parameters there will be an array like below as the response.
     *
     * array(
     *          0 => array('activityId' => 1, 'projectId' => 1, 'is_deleted' => 0, 'name' => 'Development')
     * )
     * </pre>
     * 
     * @version 2.7.1
     * @param Integer $projectId Project Id
     * @param Boolean $excludeDeletedActivities Exclude Deleted Project Activities or not
     * @return Array of Project Activities
     */
    public function getProjectActivityListByPorjectId($projectId, $excludeDeletedActivities = true) {
        return $this->getTimesheetDao()->getProjectActivityListByPorjectId($projectId, $excludeDeletedActivities);
    }
    
    public function getLatestTimesheetEndDate($employeeId) {

        return $this->getTimesheetDao()->getLatestTimesheetEndDate($employeeId);
    }

    public function checkForOverlappingTimesheets($startDate, $endDate, $employeeId) {

        return $this->getTimesheetDao()->checkForOverlappingTimesheets($startDate, $endDate, $employeeId);
    }

    public function checkForMatchingTimesheetForCurrentDate($employeeId, $currentDate) {

        return $this->getTimesheetDao()->checkForMatchingTimesheetForCurrentDate($employeeId, $currentDate);
    }

    public function createPreviousTimesheets($currentTimesheetStartDate, $employeeId) {

        // this method is for creating past timesheets.This would get conflicted if the user changes the timesheet period and does not loging to the system for couple of weeks



        $previousTimesheetEndDate = mktime(0, 0, 0, date("m", strtotime($currentTimesheetStartDate)), date("d", strtotime($currentTimesheetStartDate)) - 1, date("Y", strtotime($currentTimesheetStartDate)));
        $datesInTheCurrentTimesheetPeriod = $this->getTimesheetPeriodService()->getDefinedTimesheetPeriod(date("Y-m-d", $previousTimesheetEndDate));

        $timesheetStartingDate = $datesInTheCurrentTimesheetPeriod[0];
        $endDate = end($datesInTheCurrentTimesheetPeriod);




        if ($this->checkForOverlappingTimesheets($timesheetStartingDate, $endDate, $employeeId) == 1) {


            $accessFlowStateMachineService = new AccessFlowStateMachineService();
            $tempNextState = $accessFlowStateMachineService->getNextState(WorkflowStateMachine::FLOW_TIME_TIMESHEET, Timesheet::STATE_INITIAL, "SYSTEM", WorkflowStateMachine::TIMESHEET_ACTION_CREATE);
            $timesheet = new Timesheet();
            $timesheet->setState($tempNextState);
            $timesheet->setStartDate($timesheetStartingDate);
            $timesheet->setEndDate($endDate);
            $timesheet->setEmployeeId($employeeId);
            $timesheet = $this->saveTimesheet($timesheet);
            //create Timesheet

            $this->createPreviousTimesheets($timesheetStartingDate, $employeeId);
        }
    }

    public function createTimesheet($employeeId, $currentDate) {

        $datesInTheCurrenTimesheetPeriod = $this->getTimesheetPeriodService()->getDefinedTimesheetPeriod($currentDate);
        $timesheetStartingDate = $datesInTheCurrenTimesheetPeriod[0];
        $endDate = end($datesInTheCurrenTimesheetPeriod);
        $timesheet = $this->getTimesheetByStartDateAndEmployeeId($timesheetStartingDate, $employeeId);
        if ($timesheet == null) {


            if ($this->checkForOverlappingTimesheets($timesheetStartingDate, $endDate, $employeeId) == 0) {
                if ($this->checkForMatchingTimesheetForCurrentDate($employeeId, date('Y-m-d')) == null) {
                    //state 1 is given when timehseet is overlapping + mathcing timesheet is not found
                    $statusValuesArray['state'] = 1;
                } else {
                    $currentDatesTimesheet = $this->checkForMatchingTimesheetForCurrentDate($employeeId, date('Y-m-d'));
                    $timesheetStartingDate = $currentDatesTimesheet->getStartDate();
                    //state 2 is given when the matching timesheet is found
                    $statusValuesArray['state'] = 2;
                    $statusValuesArray['message'] = $timesheetStartingDate;
                }
            } else {


                $accessFlowStateMachineService = new AccessFlowStateMachineService();
                $tempNextState = $accessFlowStateMachineService->getNextState(WorkflowStateMachine::FLOW_TIME_TIMESHEET, Timesheet::STATE_INITIAL, "SYSTEM", WorkflowStateMachine::TIMESHEET_ACTION_CREATE);
                $timesheet = new Timesheet();
                $timesheet->setState($tempNextState);
                $timesheet->setStartDate($timesheetStartingDate);
                $timesheet->setEndDate($endDate);
                $timesheet->setEmployeeId($employeeId);
                $timesheet = $this->saveTimesheet($timesheet);
                //state 3 is given when the new timesheet is created
                $statusValuesArray['state'] = 3;
                $statusValuesArray['message'] = $timesheetStartingDate;
            }
        } else {

            //state 4 is given for when there is no timesheets to access
            $statusValuesArray['state'] = 4;
            $statusValuesArray['message'] = $timesheetStartingDate;
        }
        return $statusValuesArray;
    }

    public function createTimesheets($startDate, $employeeId) {

        $datesInTheCurrenTimesheetPeriod = $this->getTimesheetPeriodService()->getDefinedTimesheetPeriod($startDate);
        $timesheetStartingDate = $datesInTheCurrenTimesheetPeriod[0];
        $endDate = end($datesInTheCurrenTimesheetPeriod);
        $timesheet = $this->getTimesheetByStartDateAndEmployeeId($timesheetStartingDate, $employeeId);
        if ($timesheet == null) {
            if ($this->checkForOverlappingTimesheets($timesheetStartingDate, $endDate, $employeeId) == 0) {

                $statusValuesArray['state'] = 1;
            } else {


                $accessFlowStateMachineService = new AccessFlowStateMachineService();
                $tempNextState = $accessFlowStateMachineService->getNextState(WorkflowStateMachine::FLOW_TIME_TIMESHEET, Timesheet::STATE_INITIAL, "SYSTEM", WorkflowStateMachine::TIMESHEET_ACTION_CREATE);
                $timesheet = new Timesheet();
                $timesheet->setState($tempNextState);
                $timesheet->setStartDate($timesheetStartingDate);
                $timesheet->setEndDate($endDate);
                $timesheet->setEmployeeId($employeeId);
                $timesheet = $this->saveTimesheet($timesheet);
                $statusValuesArray['state'] = 2;
                $statusValuesArray['startDate'] = $timesheetStartingDate;
            }
        } else {
            $statusValuesArray['state'] = 3;
        }
        return $statusValuesArray;
    }

    public function validateStartDate($startDate) {

        $datesInTheCurrenTimesheetPeriod = $this->getTimesheetPeriodService()->getDefinedTimesheetPeriod($startDate);
        $timesheetStartingDate = $datesInTheCurrenTimesheetPeriod[0];


        if ($timesheetStartingDate == $startDate) {

            return true;
        } else {

            return false;
        }
    }

    public function returnEndDate($startDate) {

        $datesInTheCurrenTimesheetPeriod = $this->getTimesheetPeriodService()->getDefinedTimesheetPeriod($startDate);
        $timesheetStartingDate = $datesInTheCurrenTimesheetPeriod[0];
        $endDate = end($datesInTheCurrenTimesheetPeriod);
        
        return $endDate;
    }
    
    /**
     *
     * @param array/Integer $employeeIds
     * @param date $dateFrom
     * @param date $dateTo
     * @param int $subDivision
     * @param String $employeementStatus 
     * @return array
     */
    public function searchTimesheetItems($employeeIds = null, $employeementStatus = null, $subDivision = null,$supervisorId = null, $dateFrom = null , $dateTo = null ){
        
        if(!is_array($employeeIds) && $employeeIds != null ){
            $employeeIds = array($employeeIds);
        }
        
        $employeeService = new EmployeeService();
        $subordinates = $employeeService->getSubordinateListForEmployee($supervisorId);
        
        $supervisorIds = array();
        foreach($subordinates as $subordinate){           
            $supervisorIds [] = $subordinate->getSubordinateId();
        }
        
        return $this->getTimesheetDao()->searchTimesheetItems($employeeIds, $employeementStatus, $supervisorIds,  $subDivision, $dateFrom, $dateTo );
    }

}
