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

namespace OrangeHRM\Leave\Dto;

use DateTime;

class LeaveWithDaysLeft
{
    private int $id;

    private DateTime $date;

    private float $lengthHours;

    private float $lengthDays;

    private int $status;

    private int $leaveTypeId;

    private int $empNumber;

    private float $daysLeft;

    /**
     * @param int $id
     * @param DateTime $date
     * @param float $lengthHours
     * @param float $lengthDays
     * @param int $status
     * @param int $leaveTypeId
     * @param int $empNumber
     * @param float $daysLeft
     */
    public function __construct(
        int $id,
        DateTime $date,
        float $lengthHours,
        float $lengthDays,
        int $status,
        int $leaveTypeId,
        int $empNumber,
        float $daysLeft = 0
    ) {
        $this->id = $id;
        $this->date = $date;
        $this->lengthHours = $lengthHours;
        $this->lengthDays = $lengthDays;
        $this->status = $status;
        $this->leaveTypeId = $leaveTypeId;
        $this->empNumber = $empNumber;
        $this->daysLeft = $daysLeft;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     */
    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return float
     */
    public function getLengthHours(): float
    {
        return $this->lengthHours;
    }

    /**
     * @param float $lengthHours
     */
    public function setLengthHours(float $lengthHours): void
    {
        $this->lengthHours = $lengthHours;
    }

    /**
     * @return float
     */
    public function getLengthDays(): float
    {
        return $this->lengthDays;
    }

    /**
     * @param float $lengthDays
     */
    public function setLengthDays(float $lengthDays): void
    {
        $this->lengthDays = $lengthDays;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getLeaveTypeId(): int
    {
        return $this->leaveTypeId;
    }

    /**
     * @param int $leaveTypeId
     */
    public function setLeaveTypeId(int $leaveTypeId): void
    {
        $this->leaveTypeId = $leaveTypeId;
    }

    /**
     * @return int
     */
    public function getEmpNumber(): int
    {
        return $this->empNumber;
    }

    /**
     * @param int $empNumber
     */
    public function setEmpNumber(int $empNumber): void
    {
        $this->empNumber = $empNumber;
    }

    /**
     * @return float|int
     */
    public function getDaysLeft()
    {
        return $this->daysLeft;
    }

    /**
     * @param float|int $daysLeft
     */
    public function setDaysLeft($daysLeft): void
    {
        $this->daysLeft = $daysLeft;
    }
}
