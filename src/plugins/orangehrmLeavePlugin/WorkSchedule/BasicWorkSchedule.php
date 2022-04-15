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
use OrangeHRM\Admin\Service\WorkShiftService;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Entity\EmployeeWorkShift;
use OrangeHRM\Leave\Traits\Service\HolidayServiceTrait;
use OrangeHRM\Leave\Traits\Service\WorkWeekServiceTrait;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class BasicWorkSchedule implements WorkScheduleInterface
{
    use EmployeeServiceTrait;
    use WorkWeekServiceTrait;
    use HolidayServiceTrait;
    use ConfigServiceTrait;

    /**
     * @var int|null
     */
    protected ?int $empNumber = null;

    /**
     * @var WorkShiftService|null
     */
    protected ?WorkShiftService $workShiftService = null;

    /**
     *
     * @return WorkShiftService
     */
    public function getWorkShiftService(): WorkShiftService
    {
        if (!($this->workShiftService instanceof WorkShiftService)) {
            $this->workShiftService = new WorkShiftService();
        }
        return $this->workShiftService;
    }

    /**
     * @return int|null
     */
    public function getEmpNumber(): ?int
    {
        return $this->empNumber;
    }

    /**
     * @inheritDoc
     */
    public function setEmpNumber(?int $empNumber): void
    {
        $this->empNumber = $empNumber;
    }

    /**
     * @inheritDoc
     */
    public function getWorkShiftLength(): float
    {
        $workshift = $this->getEmployeeService()->getEmployeeDao()->getEmployeeWorkShift($this->getEmpNumber());
        if ($workshift != null) {
            $workShiftLength = $workshift->getWorkShift()->getHoursPerDay();
        } else {
            $workShiftLength = $this->getConfigService()->getDefaultWorkShiftLength();
        }

        return $workShiftLength;
    }

    /**
     * @inheritDoc
     */
    public function getWorkShiftStartEndTime(): WorkShiftStartAndEndTime
    {
        $workshift = $this->getEmployeeService()->getEmployeeDao()->getEmployeeWorkShift($this->empNumber);
        if ($workshift instanceof EmployeeWorkShift) {
            $workShift = $workshift->getWorkShift();
            return new WorkShiftStartAndEndTime(
                $workShift->getStartTime(),
                $workShift->getEndTime()
            );
        }

        return $this->getWorkShiftService()->getWorkShiftDefaultStartAndEndTime();
    }

    /**
     * @inheritDoc
     */
    public function isNonWorkingDay(DateTime $day, bool $fullDay): bool
    {
        return $this->getWorkWeekService()->getWorkWeekDao()->isNonWorkingDay($day, $fullDay);
    }

    /**
     * @inheritDoc
     */
    public function isHalfDay(DateTime $day): bool
    {
        return $this->getHolidayService()->isHalfDay($day);
    }

    /**
     * @inheritDoc
     */
    public function isHoliday(DateTime $day): bool
    {
        return $this->getHolidayService()->isHoliday($day);
    }

    /**
     * @inheritDoc
     */
    public function isHalfDayHoliday(DateTime $day): bool
    {
        return $this->getHolidayService()->isHalfDayHoliday($day);
    }
}
