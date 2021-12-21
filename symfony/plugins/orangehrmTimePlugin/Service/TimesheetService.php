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

namespace OrangeHRM\Time\Service;

use DateTime;
use LogicException;
use OrangeHRM\Core\Service\AccessFlowStateMachineService;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Timesheet;
use OrangeHRM\Entity\TimesheetItem;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Time\Dao\TimesheetDao;
use OrangeHRM\Time\Dto\DetailedTimesheet;
use OrangeHRM\Time\Dto\TimesheetColumn;
use OrangeHRM\Time\Dto\TimesheetRow;

class TimesheetService
{
    use DateTimeHelperTrait;
    use UserRoleManagerTrait;

    public const TIMESHEET_ACTION_MAP = [
        '0' => 'VIEW',
        '1' => 'SUBMIT',
        '2' => 'APPROVE',
        '3' => 'REJECT',
        '4' => 'RESET',
        '5' => 'MODIFY',
        '6' => 'CREATE',
    ];

    private static $timesheetTimeFormat = null;
    private ?TimesheetDao $timesheetDao = null;
    private $employeeDao;

    // Cache timesheet time format for better performance.
    /**
     * @var TimesheetPeriodService|null
     */
    private ?TimesheetPeriodService $timesheetPeriodService = null;

    /**
     * @var AccessFlowStateMachineService|null
     */
    private ?AccessFlowStateMachineService $accessFlowStateMachineService = null;

    /**
     * Get the Employee Data Access Object
     * @return EmployeeDao
     */
    public function getEmployeeDao()
    {
        // TODO
        if (is_null($this->employeeDao)) {
            $this->employeeDao = new EmployeeDao();
        }
        return $this->employeeDao;
    }

    /**
     * Set EmployeeData Access Object
     * @param EmployeeDao $employeeDao
     * @return void
     */
    public function setEmployeeDao(EmployeeDao $employeeDao)
    {
        // TODO
        $this->employeeDao = $employeeDao;
    }

    public function setTimesheetPeriodDao(TimesheetPeriodService $timesheetPeriodService)
    {
        // TODO
        $this->timesheetPeriodService = $timesheetPeriodService;
    }

    /**
     * @return AccessFlowStateMachineService
     */
    protected function getAccessFlowStateMachineService(): AccessFlowStateMachineService
    {
        if (is_null($this->accessFlowStateMachineService)) {
            $this->accessFlowStateMachineService = new AccessFlowStateMachineService();
        }
        return $this->accessFlowStateMachineService;
    }

    /**
     * Get Timesheet by given timesheetId
     * @param int $timesheetId
     * @return Timesheet $timesheet
     */
    public function getTimesheetById($timesheetId)
    {
        // TODO
        $timesheet = $this->getTimesheetDao()->getTimesheetById($timesheetId);

        if (!$timesheet instanceof Timesheet) {
            $timesheet = new Timesheet();
        }

        return $timesheet;
    }

    /**
     * @return TimesheetDao
     */
    public function getTimesheetDao(): TimesheetDao
    {
        if (is_null($this->timesheetDao)) {
            $this->timesheetDao = new TimesheetDao();
        }
        return $this->timesheetDao;
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
     * @param Array $employeeIdList Array of Employee Ids
     * @param Array $stateList Array of States
     * @param $limit Number of Timesheets return
     * @return Array of Timesheets
     * @version 2.7.1
     */
    public function getTimesheetListByEmployeeIdAndState($employeeIdList, $stateList, $limit)
    {
        // TODO
        return $this->getTimesheetDao()->getTimesheetListByEmployeeIdAndState($employeeIdList, $stateList, $limit);
    }

    public function saveTimesheetItems(
        $inputTimesheetItems,
        $employeeId,
        $timesheetId,
        $keysArray,
        $initialRows,
        $isFromService = true
    ) {
        // TODO
        foreach ($inputTimesheetItems as $inputTimesheetItem) {
            if ($isFromService) {
                $activityId = $inputTimesheetItem['projectActivityName'];
            } else {
                $activityId = $inputTimesheetItem['projectActivityId'];
            }

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
                    } elseif ($timesheetItemDuration != null) {
                        $existingTimesheetItem = $this->getTimesheetDao()->getTimesheetItemByDateProjectId(
                            $timesheetId,
                            $employeeId,
                            $projectId,
                            $activityId,
                            $date
                        );

                        if ($existingTimesheetItem[0]->getProjectId() != null) {
                            $existingTimesheetItem[0]->setProjectId($projectId);
                            $existingTimesheetItem[0]->setActivityId($activityId);
                            $existingTimesheetItem[0]->setDuration(
                                $this->convertDurationToSeconds($timesheetItemDuration)
                            );

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

    public function convertDurationToSeconds($duration)
    {
        // TODO
        $find = ':';
        $pos = strpos($duration, $find);

        if ($pos !== false) {
            $str_time = $duration;
            sscanf($str_time, '%d:%d:%d', $hours, $minutes, $seconds);
            $durationInSeconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 3600 + $minutes * 60;
            return $durationInSeconds;
        } else {
            $durationInSeconds = $duration * 60 * 60;
            return $durationInSeconds;
        }
    }

    /**
     * @param EmployeeID $employeeId
     * @param TimeSheetId $timesheetId
     * @return bool
     */
    public function deleteTimesheetItemsByTimesheetId($employeeId, $timesheetId)
    {
        // TODO
        $timesheetItemDeleted = $this->getTimesheetDao()->deleteTimesheetItemsByTimesheetId($employeeId, $timesheetId);

        return $timesheetItemDeleted > 0 ? true : false;
    }

    public function convertDurationToHours($durationInSecs)
    {
        // TODO
        $timesheetTimeFormat = $this->getTimesheetTimeFormat();

        if ($timesheetTimeFormat == '1') {
            $padHours = false;
            $hms = '';
            $hours = intval(intval($durationInSecs) / 3600);
            $hms .= ($padHours) ? str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' : $hours . ':';
            $minutes = intval(($durationInSecs / 60) % 60);
            $hms .= str_pad($minutes, 2, '0', STR_PAD_LEFT);
            return $hms;
        } elseif ($timesheetTimeFormat == '2') {
            $durationInHours = number_format($durationInSecs / (60 * 60), 2, '.', '');
            return $durationInHours;
        }
    }

    public function getTimesheetTimeFormat()
    {
        // TODO
        if (is_null(self::$timesheetTimeFormat)) {
            self::$timesheetTimeFormat = $this->getTimesheetDao()->getTimesheetTimeFormat();
        }
        return self::$timesheetTimeFormat;
    }

    public function getActivityByActivityId($activityId)
    {
        // TODO
        $activity = $this->getTimesheetDao()->getActivityByActivityId($activityId);

        return $activity;
    }

    public function addConvertTime($initialTime, $timeToAdd)
    {
        // TODO
        $old = explode(':', $initialTime);
        $play = explode(':', $timeToAdd);


        $hours = $old[0] + $play[0];

        $minutes = $old[1] + $play[1];

        if ($minutes > 59) {
            $minutes = $minutes - 60;
            $hours++;
        }
        if ($minutes < 10) {
            $minutes = '0' . $minutes;
        }
        if ($minutes == 0) {
            $minutes = '00';
        }
        $sum = $hours . ':' . $minutes;
        return $sum;
    }

    public function dateDiff($start, $end)
    {
        // TODO
        $start_ts = strtotime($start);
        $end_ts = strtotime($end);
        $diff = $end_ts - $start_ts;
        return round($diff / 86400) + 1;
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
     * @param Boolean $excludeDeletedProjects Exclude deleted projects or not
     * @param String $orderField Sort order field
     * @param String $orderBy Sort order
     * @return Array of Project Names
     * @version 2.7.1
     */
    public function getProjectNameList($excludeDeletedProjects = true, $orderField = 'project_id', $orderBy = 'ASC')
    {
        // TODO
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
     * @param Integer $projectId Project Id
     * @param Boolean $excludeDeletedActivities Exclude Deleted Project Activities or not
     * @return Array of Project Activities
     * @version 2.7.1
     */
    public function getProjectActivityListByPorjectId($projectId, $excludeDeletedActivities = true)
    {
        // TODO
        return $this->getTimesheetDao()->getProjectActivityListByPorjectId($projectId, $excludeDeletedActivities);
    }

    public function createPreviousTimesheets($currentTimesheetStartDate, $employeeId)
    {
        // TODO
        // this method is for creating past timesheets.This would get conflicted if the user changes the timesheet period and does not loging to the system for couple of weeks

        $previousTimesheetEndDate = mktime(
            0,
            0,
            0,
            date('m', strtotime($currentTimesheetStartDate)),
            date('d', strtotime($currentTimesheetStartDate)) - 1,
            date('Y', strtotime($currentTimesheetStartDate))
        );
        $datesInTheCurrentTimesheetPeriod = $this->getTimesheetPeriodService()->getDefinedTimesheetPeriod(
            date('Y-m-d', $previousTimesheetEndDate)
        );

        $timesheetStartingDate = $datesInTheCurrentTimesheetPeriod[0];
        $endDate = end($datesInTheCurrentTimesheetPeriod);


        if ($this->checkForOverlappingTimesheets($timesheetStartingDate, $endDate, $employeeId) == 1) {
            $accessFlowStateMachineService = new AccessFlowStateMachineService();
            $tempNextState = $accessFlowStateMachineService->getNextState(
                WorkflowStateMachine::FLOW_TIME_TIMESHEET,
                Timesheet::STATE_INITIAL,
                'SYSTEM',
                WorkflowStateMachine::TIMESHEET_ACTION_CREATE
            );
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

    /**
     * @return TimesheetPeriodService
     */
    public function getTimesheetPeriodService(): TimesheetPeriodService
    {
        if (is_null($this->timesheetPeriodService)) {
            $this->timesheetPeriodService = new TimesheetPeriodService();
        }

        return $this->timesheetPeriodService;
    }

    public function createTimesheet($employeeId, $currentDate)
    {
        // TODO
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
                $tempNextState = $accessFlowStateMachineService->getNextState(
                    WorkflowStateMachine::FLOW_TIME_TIMESHEET,
                    Timesheet::STATE_INITIAL,
                    'SYSTEM',
                    WorkflowStateMachine::TIMESHEET_ACTION_CREATE
                );
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

    public function createTimesheets($startDate, $employeeId)
    {
        // TODO
        $datesInTheCurrenTimesheetPeriod = $this->getTimesheetPeriodService()->getDefinedTimesheetPeriod($startDate);
        $timesheetStartingDate = $datesInTheCurrenTimesheetPeriod[0];
        $endDate = end($datesInTheCurrenTimesheetPeriod);
        $timesheet = $this->getTimesheetByStartDateAndEmployeeId($timesheetStartingDate, $employeeId);
        if ($timesheet == null) {
            if ($this->checkForOverlappingTimesheets($timesheetStartingDate, $endDate, $employeeId) == 0) {
                $statusValuesArray['state'] = 1;
            } else {
                $accessFlowStateMachineService = new AccessFlowStateMachineService();
                $tempNextState = $accessFlowStateMachineService->getNextState(
                    WorkflowStateMachine::FLOW_TIME_TIMESHEET,
                    Timesheet::STATE_INITIAL,
                    'SYSTEM',
                    WorkflowStateMachine::TIMESHEET_ACTION_CREATE
                );
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

    public function validateStartDate($startDate)
    {
        // TODO
        $datesInTheCurrenTimesheetPeriod = $this->getTimesheetPeriodService()->getDefinedTimesheetPeriod($startDate);
        $timesheetStartingDate = $datesInTheCurrenTimesheetPeriod[0];

        if ($timesheetStartingDate == $startDate) {
            return true;
        } else {
            return false;
        }
    }

    public function returnEndDate($startDate)
    {
        // TODO
        $datesInTheCurrenTimesheetPeriod = $this->getTimesheetPeriodService()->getDefinedTimesheetPeriod($startDate);
        $timesheetStartingDate = $datesInTheCurrenTimesheetPeriod[0];
        $endDate = end($datesInTheCurrenTimesheetPeriod);

        return $endDate;
    }

    /**
     * @param int $timesheetId
     * @return DetailedTimesheet
     */
    public function getDetailedTimesheet(int $timesheetId): DetailedTimesheet
    {
        $timesheet = $this->getTimesheetDao()->getTimesheetById($timesheetId);
        list($timesheetRows, $timesheetColumns) = $this->getTimesheetData($timesheet);
        return new DetailedTimesheet($timesheet, array_values($timesheetRows), array_values($timesheetColumns));
    }

    /**
     * @param Timesheet $timesheet
     * @return array[]
     */
    protected function getTimesheetData(Timesheet $timesheet): array
    {
        $timesheetDates = $this->getDateTimeHelper()->dateRange($timesheet->getStartDate(), $timesheet->getEndDate());
        $timesheetItems = $this->getTimesheetDao()->getTimesheetItemsByTimesheetId($timesheet->getId());

        $timesheetRows = [];
        $timesheetColumns = [];
        foreach ($timesheetDates as $timesheetDate) {
            $date = $this->getDateTimeHelper()->formatDateTimeToYmd($timesheetDate);
            if (!isset($timesheetColumns[$date])) {
                $timesheetColumns[$date] = new TimesheetColumn($timesheetDate);
            }
        }
        foreach ($timesheetItems as $timesheetItem) {
            $projectId = $timesheetItem->getProject()->getId();
            $projectActivityId = $timesheetItem->getProjectActivity()->getId();
            $timesheetRowKey = "${projectId}_${projectActivityId}";
            if (!isset($timesheetRows[$timesheetRowKey])) {
                $timesheetRows[$timesheetRowKey] = new TimesheetRow(
                    $timesheetItem->getProject(),
                    $timesheetItem->getProjectActivity(),
                    $timesheetDates
                );
            }
            $timesheetRows[$timesheetRowKey]->incrementTotal($timesheetItem->getDuration());
            $timesheetRows[$timesheetRowKey]->assignTimesheetItem($timesheetItem);

            $date = $this->getDateTimeHelper()->formatDateTimeToYmd($timesheetItem->getDate());
            if ($timesheetColumns[$date] instanceof TimesheetColumn) {
                $timesheetColumns[$date]->incrementTotal($timesheetItem->getDuration());
            }
        }
        return [$timesheetRows, $timesheetColumns];
    }

    /**
     * @param Timesheet $timesheet
     * @param DateTime $date
     * @return Timesheet
     */
    public function createTimesheetByDate(Timesheet $timesheet, DateTime $date): Timesheet
    {
        $nextState = $this->getAccessFlowStateMachineService()->getNextState(
            WorkflowStateMachine::FLOW_TIME_TIMESHEET,
            Timesheet::STATE_INITIAL,
            'SYSTEM',
            WorkflowStateMachine::TIMESHEET_ACTION_CREATE
        );
        list($startDate, $endDate) = $this->extractStartDateAndEndDateFromDate($date);
        $timesheet->setState($nextState);
        $timesheet->setStartDate(new DateTime($startDate));
        $timesheet->setEndDate(new DateTime($endDate));
        return $this->getTimesheetDao()->saveTimesheet($timesheet);
    }

    /**
     * @param DateTime $date
     * @return array  e.g array(if monday as first day in config => '2021-12-13', '2021-12-19')
     */
    public function extractStartDateAndEndDateFromDate(DateTime $date): array
    {
        $currentWeekFirstDate = date('Y-m-d', strtotime('monday this week', strtotime($date->format('Y-m-d'))));
        $configDate = $this->getTimesheetPeriodService()->getTimesheetStartDate() - 1;
        $startDate = date('Y-m-d', strtotime($currentWeekFirstDate . ' + ' . $configDate . ' days'));
        $endDate = date('Y-m-d', strtotime($startDate . ' + 6 days'));
        return [$startDate, $endDate];
    }

    /**
     * @param int $employeeNumber
     * @param DateTime $date
     * @return bool
     */
    public function hasTimesheetForDate(int $employeeNumber, DateTime $date): bool
    {
        list($startDate) = $this->extractStartDateAndEndDateFromDate($date);
        return $this->getTimesheetDao()->hasTimesheetForStartDate($employeeNumber, new DateTime($startDate));
    }

    /**
     * @param Timesheet $timesheet
     * @param array $rows
     * @return array<string, TimesheetItem>
     */
    protected function createTimesheetItemsFromRows(Timesheet $timesheet, array $rows): array
    {
        $timesheetItems = [];
        foreach ($rows as $row) {
            if (!(isset($row['projectId']) &&
                isset($row['activityId']) &&
                isset($row['dates']))) {
                throw new LogicException('`projectId` & `activityId` & `dates` required attributes');
            }

            foreach ($row['dates'] as $date => $dateValue) {
                if (!isset($dateValue['duration'])) {
                    throw new LogicException('`duration` required attribute');
                }
                $date = new DateTime($date);
                $itemKey = $this->generateTimesheetItemKey(
                    $timesheet->getId(),
                    $row['projectId'],
                    $row['activityId'],
                    $date
                );
                $timesheetItem = new TimesheetItem();
                $timesheetItem->setTimesheet($timesheet);
                $timesheetItem->setEmployee($timesheet->getEmployee());
                $timesheetItem->getDecorator()->setProjectById($row['projectId']);
                $timesheetItem->getDecorator()->setProjectActivityById($row['activityId']);
                $timesheetItem->setDate($date);
                $timesheetItem->setDuration(strtotime($dateValue['duration']) - strtotime('TODAY'));
                $timesheetItems[$itemKey] = $timesheetItem;
            }
        }

        return $timesheetItems;
    }

    /**
     * @param int $timesheetId
     * @param int $projectId
     * @param int $activityId
     * @param DateTime $date
     * @return string
     */
    public function generateTimesheetItemKey(int $timesheetId, int $projectId, int $activityId, DateTime $date): string
    {
        return $timesheetId . '_' .
            $projectId . '_' .
            $activityId . '_' .
            $date->format('Y_m_d');
    }

    /**
     * @param Timesheet $timesheet
     * @param array $rows
     */
    public function saveAndUpdateTimesheetItemsFromRows(Timesheet $timesheet, array $rows): void
    {
        $timesheetItems = $this->createTimesheetItemsFromRows($timesheet, $rows);
        $this->getTimesheetDao()->saveAndUpdateTimesheetItems($timesheetItems);
    }

    /**
     * @param int $loggedInEmpNumber
     * @param Timesheet $timesheet
     * @return WorkflowStateMachine[]
     */
    public function getAllowedWorkflowsForTimesheet(
        int $loggedInEmpNumber,
        Timesheet $timesheet
    ): array {
        $includeRoles = [];
        if ($loggedInEmpNumber == $timesheet->getEmployee()->getEmpNumber()
            && $this->getUserRoleManager()->essRightsToOwnWorkflow()) {
            $includeRoles = ['ESS'];
        }

        return $this->getUserRoleManager()->getAllowedActions(
            WorkflowStateMachine::FLOW_TIME_TIMESHEET,
            $timesheet->getState(),
            [],
            $includeRoles,
            [Employee::class => $timesheet->getEmployee()->getEmpNumber()]
        );
    }
}
