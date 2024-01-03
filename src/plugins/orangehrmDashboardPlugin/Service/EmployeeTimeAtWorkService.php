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

namespace OrangeHRM\Dashboard\Service;

use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Dashboard\Dao\EmployeeTimeAtWorkDao;
use OrangeHRM\Dashboard\Dto\TimeAtWorkLastActionDetails;
use OrangeHRM\Entity\AttendanceRecord;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;
use OrangeHRM\Time\Service\TimesheetPeriodService;

class EmployeeTimeAtWorkService
{
    use DateTimeHelperTrait;
    use AuthUserTrait;
    use EmployeeServiceTrait;

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
     * @var int|null
     */
    private ?int $weekStartDateIndex = null;

    /**
     * @return EmployeeTimeAtWorkDao
     */
    private function getEmployeeTimeAtWorkDao(): EmployeeTimeAtWorkDao
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
     * @param DateTime $currentDateTime
     * @param DateTime $spotDateTime
     * @return array
     * @throws Exception
     */
    public function getTimeAtWorkResults(int $empNumber, DateTime $currentDateTime, DateTime $spotDateTime): array
    {
        list($weeklyData, $totalTimeForWeek) = $this->getDataForCurrentWeek($empNumber, $currentDateTime);
        $weeklyMetaData = $this->getTimeAtWorkMetaData($empNumber, $currentDateTime, $spotDateTime, $totalTimeForWeek);
        return [$weeklyData, $weeklyMetaData];
    }

    /**
     * @param int $empNumber
     * @param DateTime $currentDateTime
     * @param DateTime $spotDateTime
     * @param int $totalTimeForWeek
     * @return array
     * @throws Exception
     */
    private function getTimeAtWorkMetaData(
        int $empNumber,
        DateTime $currentDateTime,
        DateTime $spotDateTime,
        int $totalTimeForWeek
    ): array {
        $currentUTCDateTime = (clone $currentDateTime)->setTimezone(
            new DateTimeZone(DateTimeHelperService::TIMEZONE_UTC)
        );
        $spotUTCDateTime = (clone $spotDateTime)->setTimezone(
            new DateTimeZone(DateTimeHelperService::TIMEZONE_UTC)
        );
        $totalTimeForCurrentDay = $this->getTotalTimeForGivenDate($empNumber, $currentUTCDateTime, $spotUTCDateTime);
        list($weekStartDate, $weekEndDate) = $this->getDateTimeHelper()
            ->getWeekBoundaryForGivenDate($currentDateTime, $this->getWeekStartDateIndex());

        $weekStartDate = new DateTime($weekStartDate);
        $weekEndDate = new DateTime($weekEndDate);

        return [
            'lastAction' => $this->getLastActionDetails($empNumber),
            'currentDay' => [
                'currentDate' => $this->getDateDetails($currentDateTime),
                'totalTime' => $this->getTimeInHoursAndMinutes($totalTimeForCurrentDay)
            ],
            'currentWeek' => [
                'startDate' => $this->getDateDetails($weekStartDate),
                'endDate' => $this->getDateDetails($weekEndDate),
                'totalTime' => $this->getTimeInHoursAndMinutes($totalTimeForWeek)
            ],
            'currentUser' => $this->getCurrentUserData($empNumber)
        ];
    }

    /**
     * @param DateTime $dateTime
     * @return array eg:- returns ['date' => 2022-09-05, 'label' => 'Sep 05']
     */
    private function getDateDetails(DateTime $dateTime): array
    {
        return [
            'date' => $this->getDateTimeHelper()->formatDate($dateTime),
            'label' => $dateTime->format('M') . ' ' . $dateTime->format('d')
        ];
    }

    /**
     * @param int $timeInMinutes
     * @return array eg:- for 80 minutes, this returns [ 'hours' => 1, 'minutes => 10 ]
     */
    private function getTimeInHoursAndMinutes(int $timeInMinutes): array
    {
        return [
            'hours' => floor($timeInMinutes / 60),
            'minutes' => $timeInMinutes % 60
        ];
    }

    /**
     * @param int $empNumber
     * @return array
     */
    private function getLastActionDetails(int $empNumber): array
    {
        $attendanceRecord = $this->getEmployeeTimeAtWorkDao()->getLatestAttendanceRecordByEmpNumber($empNumber);
        if (!$attendanceRecord instanceof AttendanceRecord) {
            $actionDetails = new TimeAtWorkLastActionDetails();
        } elseif ($attendanceRecord->getState() === self::STATE_PUNCHED_IN) {
            $actionDetails = new TimeAtWorkLastActionDetails(
                $attendanceRecord->getState(),
                $attendanceRecord->getDecorator()->getPunchInUTCDate(),
                $attendanceRecord->getDecorator()->getPunchInUTCTime(),
                $attendanceRecord->getDecorator()->getPunchInUserDate(),
                $attendanceRecord->getDecorator()->getPunchInUserTime(),
                $attendanceRecord->getPunchInTimeOffset()
            );
        } else {
            $actionDetails = new TimeAtWorkLastActionDetails(
                $attendanceRecord->getState(),
                $attendanceRecord->getDecorator()->getPunchOutUTCDate(),
                $attendanceRecord->getDecorator()->getPunchOutUTCTime(),
                $attendanceRecord->getDecorator()->getPunchOutUserDate(),
                $attendanceRecord->getDecorator()->getPunchOutUserTime(),
                $attendanceRecord->getPunchOutTimeOffset()
            );
        }
        return [
            'state' => $actionDetails->getState(),
            'utcDate' => $actionDetails->getUtcDate(),
            'utcTime' => $actionDetails->getUtcTime(),
            'userDate' => $actionDetails->getUserDate(),
            'userTime' => $actionDetails->getUserTime(),
            'timezoneOffset' => $actionDetails->getTimezoneOffset()
        ];
    }

    /**
     * @param int $empNumber
     * @param DateTime $startUTCDateTime
     * @param DateTime|null $spotUTCDateTime
     * @return int
     * @throws Exception
     */
    private function getTotalTimeForGivenDate(
        int $empNumber,
        DateTime $startUTCDateTime,
        DateTime $spotUTCDateTime = null
    ): int {
        $totalTime = 0;
        $endUTCDateTime = (clone $startUTCDateTime)->add(new DateInterval('P1D'));
        /**
         * Attendance Records for given day
         */
        $attendanceRecords = $this->getEmployeeTimeAtWorkDao()
            ->getAttendanceRecordsByEmployeeAndDate(
                $empNumber,
                $startUTCDateTime,
                $endUTCDateTime
            );
        if ($attendanceRecords) {
            foreach ($attendanceRecords as $attendanceRecord) {
                $punchInUtcDateTime = $this->getDateTimeInUTC($attendanceRecord->getPunchInUtcTime());
                if ($attendanceRecord->getState() === self::STATE_PUNCHED_OUT) {
                    $punchOutUtcDateTime = $this->getDateTimeInUTC($attendanceRecord->getPunchOutUtcTime());
                    if ($punchInUtcDateTime < $startUTCDateTime && $punchOutUtcDateTime > $endUTCDateTime) {
                        //punched in before start date and punched out after end date
                        $totalTime += $this->getTimeDifference($endUTCDateTime, $startUTCDateTime);
                    } elseif ($punchInUtcDateTime < $startUTCDateTime && $endUTCDateTime > $punchOutUtcDateTime
                        && $punchOutUtcDateTime > $startUTCDateTime
                    ) {
                        //punched in before start date and punched out before end date
                        $totalTime += $this->getTimeDifference($startUTCDateTime, $punchOutUtcDateTime);
                    } elseif ($startUTCDateTime < $punchInUtcDateTime && $punchInUtcDateTime < $endUTCDateTime
                        && $punchOutUtcDateTime > $endUTCDateTime
                    ) {
                        //punched in after start date and punched out after end date
                        $totalTime += $this->getTimeDifference($punchInUtcDateTime, $endUTCDateTime);
                    } elseif ($punchInUtcDateTime >= $startUTCDateTime && $punchOutUtcDateTime <= $endUTCDateTime) {
                        //punched in and punched out in middle of start and end date
                        $totalTime += $this->getTimeDifference($punchOutUtcDateTime, $punchInUtcDateTime);
                    }
                }
            }
        }
        if (!is_null($spotUTCDateTime)) {
            $openAttendanceRecord = $this->getEmployeeTimeAtWorkDao()
                ->getOpenAttendanceRecordByEmpNumber($empNumber);
            if ($openAttendanceRecord instanceof AttendanceRecord) {
                $openRecordPunchInUtcTime = $this->getDateTimeInUTC($openAttendanceRecord->getPunchInUtcTime());
                if ($openRecordPunchInUtcTime < $startUTCDateTime) {
                    $totalTime += $this->getTimeDifference($spotUTCDateTime, $startUTCDateTime);
                } else {
                    $totalTime += $this->getTimeDifference($spotUTCDateTime, $openRecordPunchInUtcTime);
                }
            }
        }

        return $totalTime;
    }

    /**
     * @param int $empNumber
     * @param DateTime $currentDateTime
     * @return array
     * @throws Exception
     */
    private function getDataForCurrentWeek(
        int $empNumber,
        DateTime $currentDateTime
    ): array {
        list($startDate) = $this->getDateTimeHelper()
            ->getWeekBoundaryForGivenDate($currentDateTime, $this->getWeekStartDateIndex());
        $startDateTime = new DateTime($startDate . ' 00:00:00', $currentDateTime->getTimezone());
        $startUTCDateTime = (clone $startDateTime)->setTimezone(
            new DateTimeZone(DateTimeHelperService::TIMEZONE_UTC)
        );

        $counter = 0;
        $weeklyData = [];
        $totalTimeForWeek = 0;

        while ($counter < 7) {
            $totalTimeForDay = $this->getTotalTimeForGivenDate($empNumber, $startUTCDateTime);
            $weeklyData[] = [
                'workDay' => [
                    'id' => $startDateTime->format('w'),
                    'day' => $startDateTime->format('D'),
                    'date' => $this->getDateTimeHelper()->formatDateTimeToYmd($startDateTime),
                ],
                'totalTime' => $this->getTimeInHoursAndMinutes($totalTimeForDay),
            ];
            $startUTCDateTime = clone $startUTCDateTime;
            $startUTCDateTime = $startUTCDateTime->add(new DateInterval('P1D'));
            $startDateTime = clone $startDateTime;
            $startDateTime = $startDateTime->add(new DateInterval('P1D'));

            $totalTimeForWeek += $totalTimeForDay;
            $counter++;
        }
        return [$weeklyData, $totalTimeForWeek];
    }

    /**
     * @param DateTime $endDateTime
     * @param DateTime $startDateTime
     * @return int difference will be given in minutes
     */
    private function getTimeDifference(DateTime $endDateTime, DateTime $startDateTime): int
    {
        $dateInterval = $endDateTime->diff($startDateTime);
        return $dateInterval->days * 24 * 60 + $dateInterval->h * 60 + $dateInterval->i;
    }

    /**
     * @param DateTime $dateTime
     * @return DateTime
     * @throws Exception
     */
    private function getDateTimeInUTC(DateTime $dateTime): DateTime
    {
        return new DateTime($dateTime->format('Y-m-d H:i'), new DateTimeZone(DateTimeHelperService::TIMEZONE_UTC));
    }

    /**
     * @param int $empNumber
     * @return array
     */
    private function getCurrentUserData(int $empNumber): array
    {
        $user = $this->getEmployeeService()->getEmployeeByEmpNumber($empNumber);
        return [
            'empNumber' => $user->getEmpNumber(),
            'firstName' => $user->getFirstName(),
            'middleName' => $user->getMiddleName(),
            'lastName' => $user->getLastName(),
            'terminationId' => is_null($user->getEmployeeTerminationRecord()) ?
                null : $user->getEmployeeTerminationRecord()->getId()
        ];
    }

    /**
     * @return int
     */
    private function getWeekStartDateIndex(): int
    {
        if (is_null($this->weekStartDateIndex)) {
            $this->weekStartDateIndex = $this->getTimesheetPeriodService()->getTimesheetStartDate();
        }
        return $this->weekStartDateIndex;
    }
}
