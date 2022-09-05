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

use DateInterval;
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

    /**
     * @var int
     */
    private int $totalTimeForWeek = 0;

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
        return $this->getDataForCurrentWeek($empNumber);
    }

    /**
     * @param int $empNumber
     * @return array
     */
    public function getTimeAtWorkMetaData(int $empNumber): array
    {
        $totalTimeForCurrentDay = $this->getTotalTimeForDayInMinutes($empNumber, $this->getDateTimeHelper()->getNow());
        list($weekStartDate, $weekEndDate) = $this->extractStartDateAndEndDateFromDate(
            $this->getDateTimeHelper()->getNow()
        );
        $currentDate = $this->getDateTimeHelper()->getNow();
        $weekStartDate = new DateTime($weekStartDate);
        $weekEndDate = new DateTime($weekEndDate);

        return [
            'lastAction' => $this->getLastActionDetails($empNumber),
            'currentDay' => [
                'currentDate' => [
                    'date' => $this->getDateTimeHelper()->formatDate($currentDate),
                    'label' => $currentDate->format('M') . ' ' . $currentDate->format('d')
                ],
                'totalTime' => [
                    'hours' => floor($totalTimeForCurrentDay / 60),
                    'minutes' => $totalTimeForCurrentDay % 60
                ]
            ],
            'currentWeek' => [
                'startDate' => [
                    'date' => $this->getDateTimeHelper()->formatDate($weekStartDate),
                    'label' => $weekStartDate->format('M') . ' ' . $weekStartDate->format('d')
                ],
                'endDate' => [
                    'date' => $this->getDateTimeHelper()->formatDate($weekEndDate),
                    'label' => $weekEndDate->format('M') . ' ' . $weekEndDate->format('d')
                ],
                'totalTime' => [
                    'hours' => floor($this->totalTimeForWeek / 60),
                    'minutes' => $this->totalTimeForWeek % 60
                ]
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
     * @param DateTime $dateTime
     * @return int
     */
    public function getTotalTimeForDayInMinutes(int $empNumber, DateTime $dateTime): int
    {
        //TODO:: Handle when punch in date and punch out date are different
        $attendanceRecords = $this->getEmployeeTimeAtWorkDao()
            ->getAttendanceRecordsByEmployeeAndDate($empNumber, $dateTime);
        $totalTime = 0;
        foreach ($attendanceRecords as $attendanceRecord) {
            if ($attendanceRecord->getState() === self::STATE_PUNCHED_OUT) {
                $punchedInUTC = $attendanceRecord->getPunchInUtcTime();
                $punchOutUTC = $attendanceRecord->getPunchOutUtcTime();
                $totalTime = $totalTime + (
                        $punchOutUTC->diff($punchedInUTC)->h * 60 +
                        $punchOutUTC->diff($punchedInUTC)->i
                    );
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

    /**
     * @param int $empNumber
     * @return array
     */
    private function getDataForCurrentWeek(int $empNumber): array
    {
        list($startDate) = $this->extractStartDateAndEndDateFromDate($this->getDateTimeHelper()->getNow());
        $counter = 0;
        $date = new DateTime($startDate);
        $weeklyData = [];
        while ($counter < 7) {
            $totalTimeForDay = $this->getTotalTimeForDayInMinutes($empNumber, $date);
            $weeklyData[] = [
                'workDay' => [
                    'id' => $date->format('w'),
                    'day' => $date->format('D'),
                    'date' => $this->getDateTimeHelper()->formatDate($date),
                ],
                'totalTime' => [
                    'hours' => floor($totalTimeForDay / 60),
                    'minutes' => $totalTimeForDay % 60
                ],
            ];
            $date = clone $date;
            $date = $date->add(new DateInterval('P1D'));
            $this->totalTimeForWeek = $this->totalTimeForWeek + $totalTimeForDay;
            $counter++;
        }
        return $weeklyData;
    }

}
