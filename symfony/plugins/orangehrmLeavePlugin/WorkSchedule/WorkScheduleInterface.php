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

namespace OrangeHRM\Leave\WorkSchedule;

use DateTime;
use OrangeHRM\Admin\Dto\WorkShiftStartAndEndTime;

interface WorkScheduleInterface
{
    /**
     * @param int|null $empNumber
     */
    public function setEmpNumber(?int $empNumber): void;

    /**
     * @return float e.g. 8, 8.25
     */
    public function getWorkShiftLength(): float;

    /**
     * @return WorkShiftStartAndEndTime
     */
    public function getWorkShiftStartEndTime(): WorkShiftStartAndEndTime;

    /**
     * @param DateTime $day
     * @param bool $fullDay
     * @return bool
     */
    public function isNonWorkingDay(DateTime $day, bool $fullDay): bool;

    /**
     * @param DateTime $day
     * @return bool
     */
    public function isHalfDay(DateTime $day): bool;

    /**
     * @param DateTime $day
     * @return bool
     */
    public function isHoliday(DateTime $day): bool;

    /**
     * @param DateTime $day
     * @return bool
     */
    public function isHalfDayHoliday(DateTime $day): bool;
}
