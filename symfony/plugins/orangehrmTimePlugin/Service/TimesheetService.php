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

    private ?TimesheetDao $timesheetDao = null;

    /**
     * @var TimesheetPeriodService|null
     */
    private ?TimesheetPeriodService $timesheetPeriodService = null;

    /**
     * @var AccessFlowStateMachineService|null
     */
    private ?AccessFlowStateMachineService $accessFlowStateMachineService = null;

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
