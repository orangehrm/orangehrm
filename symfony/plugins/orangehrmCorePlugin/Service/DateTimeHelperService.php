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

namespace OrangeHRM\Core\Service;

use DateTime;

class DateTimeHelperService
{
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
     * @return string|null
     */
    public function formatDateTimeToTimeString(?DateTime $dateTime): ?string
    {
        return $dateTime instanceof DateTime ? $dateTime->format('H:i') : null;
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
}
