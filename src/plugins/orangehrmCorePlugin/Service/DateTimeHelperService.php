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

namespace OrangeHRM\Core\Service;

use DateInterval;
use DateTime;
use DateTimeZone;
use InvalidArgumentException;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Helper\LocalizedDateFormatter;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;

class DateTimeHelperService
{
    use ConfigServiceTrait;

    public const TIMEZONE_UTC = '+0000';

    public const TUESDAY_LAST_WEEK = 'tuesday last week';
    public const WEDNESDAY_LAST_WEEK = 'wednesday last week';
    public const THURSDAY_LAST_WEEK = 'thursday last week';
    public const FRIDAY_LAST_WEEK = 'friday last week';
    public const SATURDAY_LAST_WEEK = 'saturday last week';
    public const SUNDAY_LAST_WEEK = 'sunday last week';

    public const LAST_WEEK_MAP = [
        2 => self::TUESDAY_LAST_WEEK,
        3 => self::WEDNESDAY_LAST_WEEK,
        4 => self::THURSDAY_LAST_WEEK,
        5 => self::FRIDAY_LAST_WEEK,
        6 => self::SATURDAY_LAST_WEEK,
        7 => self::SUNDAY_LAST_WEEK
    ];

    private ?LocalizedDateFormatter $dateFormatter = null;

    /**
     * @return LocalizedDateFormatter
     */
    public function getDateFormatter(): LocalizedDateFormatter
    {
        if (!$this->dateFormatter instanceof LocalizedDateFormatter) {
            $this->dateFormatter = new LocalizedDateFormatter();
        }
        return $this->dateFormatter;
    }


    /**
     * Format given \DateTime object to Y-m-d string.
     * Return null if null given
     *
     * @param DateTime|null $dateTime
     * @return string|null
     */
    public function formatDateTimeToYmd(?DateTime $dateTime): ?string
    {
        return $dateTime instanceof DateTime ? $dateTime->format('Y-m-d') : null;
    }

    /**
     * Format given \DateTime object to H:i string.
     * Return null if null given
     *
     * @param DateTime|null $dateTime
     * @param bool $withSeconds
     * @return string|null
     */
    public function formatDateTimeToTimeString(?DateTime $dateTime, bool $withSeconds = false): ?string
    {
        $format = 'H:i' . ($withSeconds ? ':s' : '');
        return $dateTime instanceof DateTime ? $dateTime->format($format) : null;
    }

    /**
     * Format given seconds to H:i or H:i:s string.
     *
     * @param int $seconds
     * @param bool $withSeconds
     * @return string
     */
    public function convertSecondsToTimeString(int $seconds, bool $withSeconds = false): string
    {
        $format = '%02d:%02d';
        $args = [floor($seconds / 3600), ($seconds / 60) % 60];
        if ($withSeconds) {
            $format = '%02d:%02d:%02d';
            $args[] = $seconds % 60;
        }
        return sprintf($format, ...$args);
    }

    /**
     * Check only date equals of given \DateTime objects, by converting into Y-m-d
     *
     * @param DateTime|null $dateTime1
     * @param DateTime|null $dateTime2
     * @param bool $acceptNull
     * @return bool
     */
    public function isDatesEqual(?DateTime $dateTime1, ?DateTime $dateTime2, bool $acceptNull = false): bool
    {
        $bothDatesNull = is_null($dateTime1) && is_null($dateTime2);
        if ($bothDatesNull && $acceptNull) {
            return true;
        } elseif ($bothDatesNull) {
            return false;
        }

        return $this->formatDateTimeToYmd($dateTime1) === $this->formatDateTimeToYmd($dateTime2);
    }

    /**
     * @see https://www.php.net/manual/en/datetime.diff.php
     * @see https://www.php.net/manual/en/dateinterval.format.php
     *
     * @param DateTime|null $baseDateTime
     * @param DateTime|null $targetDateTime
     * @return float
     */
    public function dateDiffInHours(?DateTime $baseDateTime, ?DateTime $targetDateTime): float
    {
        $dateInterval = $baseDateTime->diff($targetDateTime);
        return $dateInterval->days * 24 + $dateInterval->h + $dateInterval->i / 60;
    }

    /**
     * @param DateTime|null $fromDateTime
     * @param DateTime|null $toDateTime
     * @param string $duration https://www.php.net/manual/en/dateinterval.construct.php#refsect1-dateinterval.construct-parameters
     * @return DateTime[]
     */
    public function dateRange(?DateTime $fromDateTime, ?DateTime $toDateTime, string $duration = 'P1D'): array
    {
        if ($fromDateTime > $toDateTime) {
            throw new InvalidArgumentException('From date should be before that to date');
        }
        $currentDateTime = clone $fromDateTime;
        do {
            $dates[] = clone $currentDateTime;
            $currentDateTime = $currentDateTime->add(new DateInterval($duration));
        } while ($currentDateTime <= $toDateTime);
        return $dates;
    }

    /**
     * @param DateTimeZone|null $timezone
     * @return DateTime
     */
    public function getNow(DateTimeZone $timezone = null): DateTime
    {
        return new DateTime('now', $timezone);
    }

    /**
     * @return DateTime
     */
    public function getNowInUTC(): DateTime
    {
        return $this->getNow()->setTimezone(new DateTimeZone(self::TIMEZONE_UTC));
    }

    /**
     * @param float $timezoneOffset
     * @return DateTimeZone eg:- 5.5 -> +0530, -5.5 -> -0530
     */
    public function getTimezoneByTimezoneOffset(float $timezoneOffset): DateTimeZone
    {
        $absoluteOffset = abs($timezoneOffset);
        $hours = floor($absoluteOffset);
        $minutes = ($absoluteOffset * 60) % 60 == 0 ? '00' : ($absoluteOffset * 60) % 60;
        $hours = $hours < 10 ? '0' . $hours : $hours;
        return new DateTimeZone(($timezoneOffset > 0 ? '+' : '-') . $hours . $minutes);
    }

    /**
     * @param DateTime|null $dateTime
     * @return string|null
     */
    public function formatDate(?DateTime $dateTime): ?string
    {
        if (is_null($dateTime)) {
            return null;
        }
        if (!Config::get(Config::DATE_FORMATTING_ENABLED)) {
            return $this->formatDateTimeToYmd($dateTime);
        }
        $dateFormat = $this->getConfigService()->getAdminLocalizationDefaultDateFormat();
        return $this->getDateFormatter()->formatDate($dateTime, $dateFormat);
    }

    /**
     * @param DateTime $dateTime
     * @param int $weekStartDateIndex
     * @return array
     */
    public function getWeekBoundaryForGivenDate(DateTime $dateTime, int $weekStartDateIndex): array
    {
        /**
         * By Default week starts on Monday
         */
        $weekNumber = $dateTime->format('W');
        $year = $dateTime->format('o');

        /**
         * Sunday => 0 and Saturday => 6
         */
        $currentDayIndex = $dateTime->format('w');

        $currentDayIndex = $currentDayIndex == 0 ? 7 : $currentDayIndex;

        $weekStartDate = (clone $dateTime)->setISODate($year, $weekNumber, $weekStartDateIndex);

        if ($currentDayIndex - $weekStartDateIndex < 0) {
            $weekStartDate->modify(self::LAST_WEEK_MAP[$weekStartDateIndex]);
        }
        $weekEndDate = (clone $weekStartDate)->add(new DateInterval('P6D'));

        return [
            $weekStartDate->format('Y-m-d'),
            $weekEndDate->format('Y-m-d')
        ];
    }
}
