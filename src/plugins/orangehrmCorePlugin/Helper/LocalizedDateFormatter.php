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

namespace OrangeHRM\Core\Helper;

use DateTime;
use OrangeHRM\Core\Traits\Service\TextHelperTrait;
use OrangeHRM\I18N\Traits\Service\I18NHelperTrait;

class LocalizedDateFormatter
{
    use TextHelperTrait;
    use I18NHelperTrait;

    public const FORMAT_CHAR_SHORT_DAY = 'D'; // Mon through Sun
    public const FORMAT_CHAR_LONG_DAY = 'l'; // Sunday through Saturday
    public const FORMAT_CHAR_SHORT_MONTH = 'M'; // Jan through Dec
    public const FORMAT_CHAR_LONG_MONTH = 'F'; // January through December

    public const SHORT_DAYS = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    public const LONG_DAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    public const SHORT_MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    public const LONG_MONTHS = [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December',
    ];

    private array $localizableFormatCache = [];

    /**
     * @param DateTime $dateTime
     * @param string $dateFormat
     * @return string
     */
    public function formatDate(DateTime $dateTime, string $dateFormat): string
    {
        $formattedDate = $dateTime->format($dateFormat);

        list($search, $replace) = $this->getSearchAndReplaceParams($dateFormat);
        if (empty($search) && empty($replace)) {
            return $formattedDate;
        }

        return str_replace($search, $replace, $formattedDate);
    }

    /**
     * @param string $dateFormat
     * @return array[]
     */
    private function getSearchAndReplaceParams(string $dateFormat): array
    {
        if (isset($this->localizableFormatCache[$dateFormat])) {
            return $this->localizableFormatCache[$dateFormat];
        }

        $search = [];
        $replace = [];
        $this->getSearchReplaceTupleForShortDays($dateFormat, $search, $replace);
        $this->getSearchReplaceTupleForLongDays($dateFormat, $search, $replace);
        $this->getSearchReplaceTupleForShortMonth($dateFormat, $search, $replace);
        $this->getSearchReplaceTupleForLongMonth($dateFormat, $search, $replace);
        $this->localizableFormatCache[$dateFormat] = [$search, $replace];

        return $this->localizableFormatCache[$dateFormat];
    }

    /**
     * @param string $dateFormat
     * @param array $search
     * @param array $replace
     */
    private function getSearchReplaceTupleForShortDays(string $dateFormat, array &$search, array &$replace): void
    {
        if (!$this->getTextHelper()->strContains($dateFormat, self::FORMAT_CHAR_SHORT_DAY)) {
            return;
        }
        array_push($search, ...self::SHORT_DAYS);
        array_push(
            $replace,
            ...array_map(
                fn (string $day) => $this->getI18NHelper()->transBySource($day),
                self::SHORT_DAYS
            )
        );
    }

    /**
     * @param string $dateFormat
     * @param array $search
     * @param array $replace
     */
    private function getSearchReplaceTupleForLongDays(string $dateFormat, array &$search, array &$replace)
    {
        if (!$this->getTextHelper()->strContains($dateFormat, self::FORMAT_CHAR_LONG_DAY)) {
            return;
        }
        array_push($search, ...self::LONG_DAYS);
        array_push(
            $replace,
            ...array_map(
                fn (string $day) => $this->getI18NHelper()->transBySource($day),
                self::LONG_DAYS
            )
        );
    }

    /**
     * @param string $dateFormat
     * @param array $search
     * @param array $replace
     */
    private function getSearchReplaceTupleForShortMonth(string $dateFormat, array &$search, array &$replace)
    {
        if (!$this->getTextHelper()->strContains($dateFormat, self::FORMAT_CHAR_SHORT_MONTH)) {
            return;
        }
        array_push($search, ...self::SHORT_MONTHS);
        array_push(
            $replace,
            ...array_map(
                fn (string $day) => $this->getI18NHelper()->transBySource($day),
                self::SHORT_MONTHS
            )
        );
    }

    /**
     * @param string $dateFormat
     * @param array $search
     * @param array $replace
     */
    private function getSearchReplaceTupleForLongMonth(string $dateFormat, array &$search, array &$replace)
    {
        if (!$this->getTextHelper()->strContains($dateFormat, self::FORMAT_CHAR_LONG_MONTH)) {
            return;
        }
        array_push($search, ...self::LONG_MONTHS);
        array_push(
            $replace,
            ...array_map(
                fn (string $day) => $this->getI18NHelper()->transBySource($day),
                self::LONG_MONTHS
            )
        );
    }
}
