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

namespace OrangeHRM\Leave\Service;

use DateTime;
use OrangeHRM\Core\Traits\CacheTrait;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Entity\Holiday;
use OrangeHRM\Entity\WorkWeek;
use OrangeHRM\Leave\Dao\HolidayDao;
use OrangeHRM\Leave\Dto\HolidaySearchFilterParams;

class HolidayService
{
    use CacheTrait;
    use ConfigServiceTrait;

    public const LEAVE_HOLIDAYS_CACHE_KEY_PREFIX = 'leave.holidays';
    public const PARAMETER_DATA = 'data';
    public const PARAMETER_TOTAL = 'total';

    /**
     * @var HolidayDao|null
     */
    private ?HolidayDao $holidayDao = null;

    /**
     * @return HolidayDao
     */
    public function getHolidayDao(): HolidayDao
    {
        if (is_null($this->holidayDao)) {
            $this->holidayDao = new HolidayDao();
        }
        return $this->holidayDao;
    }

    /**
     * @param Holiday $holiday
     * @return Holiday
     */
    public function saveHoliday(Holiday $holiday): Holiday
    {
        $this->getCache()->clear(self::LEAVE_HOLIDAYS_CACHE_KEY_PREFIX);
        return $this->getHolidayDao()->saveHoliday($holiday);
    }

    /**
     * @param DateTime $fromDate
     * @param DateTime $toDate
     * @return string
     */
    protected function getHolidaysCacheKey(DateTime $fromDate, DateTime $toDate): string
    {
        return self::LEAVE_HOLIDAYS_CACHE_KEY_PREFIX .
            '.' . $fromDate->format('Y_m_d') . '.' . $toDate->format('Y_m_d');
    }

    /**
     * Search Holidays within a given leave period
     * @param HolidaySearchFilterParams $holidaySearchFilterParams
     * @return Holiday[]
     */
    public function searchHolidays(HolidaySearchFilterParams $holidaySearchFilterParams): array
    {
        $offset = $holidaySearchFilterParams->getOffset();
        $limit = $holidaySearchFilterParams->getLimit();
        if ($limit == 0) {  // when check uniqueness
            return $this->searchHolidaysAlongWithCache(
                $holidaySearchFilterParams->getFromDate(),
                $holidaySearchFilterParams->getToDate()
            )[self::PARAMETER_DATA];
        }
        return array_slice($this->searchHolidaysAlongWithCache(
            $holidaySearchFilterParams->getFromDate(),
            $holidaySearchFilterParams->getToDate()
        )[self::PARAMETER_DATA], $offset, $limit);
    }

    /**
     * @param HolidaySearchFilterParams $holidaySearchFilterParams
     * @return int
     */
    public function searchHolidaysCount(HolidaySearchFilterParams $holidaySearchFilterParams): int
    {
        return $this->searchHolidaysAlongWithCache(
            $holidaySearchFilterParams->getFromDate(),
            $holidaySearchFilterParams->getToDate()
        )[self::PARAMETER_TOTAL];
    }

    /**
     * @param DateTime $fromDate
     * @param DateTime $toDate
     * @return array
     */
    protected function searchHolidaysAlongWithCache(DateTime $fromDate, DateTime $toDate): array
    {
        return $this->getCache()->get(
            $this->getHolidaysCacheKey($fromDate, $toDate),
            function () use ($fromDate, $toDate) {
                $holidays = $this->getCalculatedHolidays($fromDate, $toDate);
                return [self::PARAMETER_DATA => $holidays, self::PARAMETER_TOTAL => count($holidays)];
            }
        );
    }

    /**
     * @param DateTime $fromDate
     * @param DateTime $toDate
     * @return array
     */
    protected function getCalculatedHolidays(DateTime $fromDate, DateTime $toDate): array
    {
        $holidaySearchFilterParams = new HolidaySearchFilterParams();
        $holidaySearchFilterParams->setFromDate($fromDate);
        $holidaySearchFilterParams->setToDate($toDate);
        $holidayList = $this->getHolidayDao()->searchHolidays($holidaySearchFilterParams);

        $startYear = $fromDate->format('Y');
        $endYear = $toDate->format('Y');
        $results = [];

        foreach ($holidayList as $holiday) {
            if ($holiday->isRecurring()) {
                $holidayDate = $holiday->getDate();

                for ($year = $startYear; $year <= $endYear; $year++) {
                    $recurringDateStr = "{$year}-{$holidayDate->format('m')}-{$holidayDate->format('d')}";
                    $recurringDate = new DateTime($recurringDateStr);

                    if ($recurringDate >= $fromDate && $recurringDate <= $toDate) {
                        $recurringHoliday = clone $holiday;
                        $recurringHoliday->setDate($recurringDate);
                        $recurringHoliday->setId($holiday->getId());

                        $results[] = $recurringHoliday;
                    }
                }
            } else {
                $results[] = $holiday;
            }
        }

        usort(
            $results,
            function (Holiday $a, Holiday $b) {
                $timeStampA = strtotime($a->getDate()->format('Y-m-d'));
                $timeStampB = strtotime($b->getDate()->format('Y-m-d'));
                if ($timeStampA === $timeStampB) {
                    return $a->getId() - $b->getId();
                } else {
                    return $timeStampA - $timeStampB;
                }
            }
        );

        return array_values($results);
    }

    /**
     * @param DateTime $day
     * @return bool
     */
    public function isHoliday(DateTime $day): bool
    {
        $holiday = $this->getHolidayDao()->getHolidayByDate($day);
        if ($holiday != null && $holiday->getLength() == Holiday::HOLIDAY_FULL_DAY_LENGTH) {
            return true;
        }
        return false;
    }

    /**
     * @param DateTime $day
     * @return bool
     */
    public function isHalfDay(DateTime $day): bool
    {
        $holiday = $this->getHolidayDao()->getHolidayByDate($day);
        if ($holiday != null &&
            $holiday->getLength() >= WorkWeek::WORKWEEK_LENGTH_HALF_DAY &&
            $holiday->getLength() < $this->getConfigService()->getDefaultWorkShiftLength()) {
            return true;
        }
        return false;
    }

    /**
     * @param DateTime $day
     * @return bool
     */
    public function isHalfDayHoliday(DateTime $day): bool
    {
        $holiday = $this->getHolidayDao()->getHolidayByDate($day);
        if ($holiday != null && $holiday->getLength() == Holiday::HOLIDAY_HALF_DAY_LENGTH) {
            return true;
        }
        return false;
    }

    /**
     * @param array $toDeleteIds
     * @return int
     */
    public function deleteHolidays(array $toDeleteIds): int
    {
        $this->getCache()->clear(self::LEAVE_HOLIDAYS_CACHE_KEY_PREFIX);
        return $this->getHolidayDao()->deleteHolidays($toDeleteIds);
    }
}
