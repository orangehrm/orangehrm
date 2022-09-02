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

namespace OrangeHRM\Dashboard\Service;

use DateTime;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Dashboard\Dao\EmployeeTimeAtWorkDao;
use OrangeHRM\Time\Service\TimesheetPeriodService;

class EmployeeTimeAtWorkService
{
    use DateTimeHelperTrait;
    use AuthUserTrait;

    public const STATE_PUNCHED_IN = 'PUNCHED IN';
    public const STATE_PUNCHED_OUT = 'PUNCHED OUT';

    /**
     * @var EmployeeTimeAtWorkDao
     */
    private EmployeeTimeAtWorkDao $employeeTimeAtWorkDao;

    /**
     * @var TimesheetPeriodService
     */
    private TimesheetPeriodService $timesheetPeriodService;

    public function getEmployeeTimeAtWorkDao(): EmployeeTimeAtWorkDao
    {
        return $this->employeeTimeAtWorkDao ??= new EmployeeTimeAtWorkDao();
    }

    /**
     * @return TimesheetPeriodService
     */
    private function getTimesheetPeriodService(): TimesheetPeriodService
    {
        return $this->timesheetPeriodService ??= new TimesheetPeriodService();
    }

    /**
     * @param int $empNumber
     * @return array
     */
    public function getTimeAtWorkData(int $empNumber): array
    {
        list($weekStartDate, $weekEndDate) = $this->extractStartDateAndEndDateFromDate(
            $this->getDateTimeHelper()->getNow()
        );

        return [
            'lastAction' => $this->getLastActionDetails($empNumber),
            'currentDate' => [
                'totalTime' => $this->getCurrentDayTotalTime($empNumber),
            ],
            'currentWeek' => [
                'firstDate' => $this->getTimesheetPeriodService()->getTimesheetStartDate(),
                'startDate' => $weekStartDate,
                'endDate' => $weekEndDate,
            ]
        ];
    }

    /**
     * @param int $empNumber
     * @return array
     */
    private function getLastActionDetails(int $empNumber): array
    {
        $attendanceRecord = $this->getEmployeeTimeAtWorkDao()->getLatestAttendanceRecordByEmpNumber($empNumber);
        if ($attendanceRecord->getState() === self::STATE_PUNCHED_IN) {
            return [
                'state' => $attendanceRecord->getState(),
                'utcDate' => $attendanceRecord->getDecorator()->getPunchInUTCDate(),
                'utcTime' => $attendanceRecord->getDecorator()->getPunchInUTCTime(),
                'userDate' => $attendanceRecord->getDecorator()->getPunchInUserDate(),
                'userTime' => $attendanceRecord->getDecorator()->getPunchInUserTime(),
                'timezoneOffset' => $attendanceRecord->getPunchInTimeOffset()
            ];
        } else {
            return [
                'state' => $attendanceRecord->getState(),
                'utcDate' => $attendanceRecord->getDecorator()->getPunchOutUTCDate(),
                'utcTime' => $attendanceRecord->getDecorator()->getPunchOutUTCTime(),
                'userDate' => $attendanceRecord->getDecorator()->getPunchOutUserDate(),
                'userTime' => $attendanceRecord->getDecorator()->getPunchOutUserTime(),
                'timezoneOffset' => $attendanceRecord->getPunchOutTimeOffset()
            ];
        }
    }

    /**
     * @param int $empNumber
     * @return int
     */
    public function getCurrentDayTotalTime(int $empNumber): int
    {
        //TODO:: Handle when punch in date and punch out date are different
        $attendanceRecords = $this->getEmployeeTimeAtWorkDao()
            ->getAttendanceRecordsByEmployeeAndDate(
                $empNumber,
                $this->getDateTimeHelper()->getNow()
            );
        $totalTime = 0;
        foreach ($attendanceRecords as $attendanceRecord) {
            if ($attendanceRecord->getState() === self::STATE_PUNCHED_OUT) {
                $punchedInUTC = $attendanceRecord->getPunchInUtcTime();
                $punchOutUTC = $attendanceRecord->getPunchOutUtcTime();
                $totalTime = $totalTime + $punchOutUTC->diff($punchedInUTC)->i;
            }
        }
        return $totalTime;
    }

    /**
     * @param DateTime $date
     * @return array  e.g array(if monday as first day in config => '2021-12-13', '2021-12-19')
     */
    private function extractStartDateAndEndDateFromDate(DateTime $date): array
    {
        $currentWeekFirstDate = date('Y-m-d', strtotime('monday this week', strtotime($date->format('Y-m-d'))));
        $configDate = $this->getTimesheetPeriodService()->getTimesheetStartDate() - 1;
        $startDate = date('Y-m-d', strtotime($currentWeekFirstDate . ' + ' . $configDate . ' days'));
        $endDate = date('Y-m-d', strtotime($startDate . ' + 6 days'));
        return [$startDate, $endDate];
    }
}
