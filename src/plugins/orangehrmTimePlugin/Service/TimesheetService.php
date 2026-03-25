<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
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
use OrangeHRM\Leave\Dao\LeaveRequestDao;
use OrangeHRM\Leave\Dao\HolidayDao;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;


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

    private ?TimesheetDao $timesheetDao = null;

    /**
     * @var TimesheetPeriodService|null
     */
    private ?TimesheetPeriodService $timesheetPeriodService = null;

    /**
     * @var AccessFlowStateMachineService|null
     */
    private ?AccessFlowStateMachineService $accessFlowStateMachineService = null;

    private ?LeaveRequestDao $leaveRequestDao = null;
    private ?HolidayDao $holidayDao = null;

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
     * @return TimesheetPeriodService
     */
    public function getTimesheetPeriodService(): TimesheetPeriodService
    {
        if (is_null($this->timesheetPeriodService)) {
            $this->timesheetPeriodService = new TimesheetPeriodService();
        }

        return $this->timesheetPeriodService;
    }

    protected function getLeaveRequestDao(): LeaveRequestDao
    {
        if (is_null($this->leaveRequestDao)) {
            $this->leaveRequestDao = new LeaveRequestDao();
        }
        return $this->leaveRequestDao;
    }

    protected function getHolidayDao(): HolidayDao
    {
        if (is_null($this->holidayDao)) {
            $this->holidayDao = new HolidayDao();
        }
        return $this->holidayDao;
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

            $date = $this->getDateTimeHelper()
                ->formatDateTimeToYmd($timesheetDate);

            if (!isset($timesheetColumns[$date])) {

                $column = new TimesheetColumn($timesheetDate);

                $empNumber = $timesheet->getEmployee()->getEmpNumber();

                // 🔹 Approved Leave
                if ($this->hasApprovedLeave($empNumber, $timesheetDate)) {
                    $column->setBlocked(true, 'leave');
                }

        // 🔹 Public Holiday
                if ($this->isPublicHoliday($timesheetDate)) {
                    $column->setBlocked(true, 'holiday');
                }
                $timesheetColumns[$date] = $column;
            }
        }
        foreach ($timesheetItems as $timesheetItem) {
            $projectId = $timesheetItem->getProject()->getId();
            $projectActivityId = $timesheetItem->getProjectActivity()->getId();
            $timesheetRowKey = "{$projectId}_{$projectActivityId}";
            if (!isset($timesheetRows[$timesheetRowKey])) {
                $timesheetRows[$timesheetRowKey] = new TimesheetRow(
                    $timesheetItem->getProject(),
                    $timesheetItem->getProjectActivity(),
                    $timesheetDates
                );
            }

            if (!is_null($timesheetItem->getDuration())) {
                $timesheetRows[$timesheetRowKey]->incrementTotal($timesheetItem->getDuration());

                $date = $this->getDateTimeHelper()->formatDateTimeToYmd($timesheetItem->getDate());
                if ($timesheetColumns[$date] instanceof TimesheetColumn) {
                    $timesheetColumns[$date]->incrementTotal($timesheetItem->getDuration());
                }
            }
            $timesheetRows[$timesheetRowKey]->assignTimesheetItem($timesheetItem);
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
        $weekStartDateIndex = $this->getTimesheetPeriodService()->getTimesheetStartDate();
        return $this->getDateTimeHelper()->getWeekBoundaryForGivenDate($date, $weekStartDateIndex);
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
     * ===========================
     *  LEAVE + HOLIDAY BLOCK LOGIC
     * ===========================
     */

    protected function hasApprovedLeave(
        int $empNumber,
        DateTime $date
    ): bool {
        $normalizedDate = new DateTime($date->format('Y-m-d'));

        $leaveList = $this->getLeaveRequestDao()
            ->getLeavesByEmpNumberAndDates(
                $empNumber,
                [$normalizedDate]
            );

        foreach ($leaveList as $leave) {

            $status = (int) $leave->getStatus();
             
            // 2 = Scheduled (Approved)
        // 3 = Taken
            if ($status === 2 || $status === 3) {
                return true;
            }
        }
        return false;
    }

    protected function isPublicHoliday(DateTime $date): bool
    {
        $holiday = $this->getHolidayDao()->getHolidayByDate($date);
        return !is_null($holiday);
    }

    /**
     * @param Timesheet $timesheet
     * @param array $rows
     */
    public function saveAndUpdateTimesheetItemsFromRows(
    Timesheet $timesheet,
    array $rows  
    ): void {

        $timesheetItems = $this->createTimesheetItemsFromRows($timesheet, $rows);
        $empNumber = $timesheet->getEmployee()->getEmpNumber();
        foreach ($timesheetItems as $item) {
            $date = $item->getDate();
            // 🔹 Block Approved Leave
            if ($this->hasApprovedLeave($empNumber, $date)) {
                throw new BadRequestException( 'Timesheet blocked. Approved Leave on ' 
            . $date->format('Y-m-d')
                );
            }

            // 🔹 Block Public Holiday

            if ($this->isPublicHoliday($date)) {
                throw new BadRequestException(
                  'Timesheet blocked. Public Holiday on ' .
            $date->format('Y-m-d')
                );    
            }
        }

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
