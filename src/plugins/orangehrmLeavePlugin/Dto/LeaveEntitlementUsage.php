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

namespace OrangeHRM\Leave\Dto;

use DateTime;

class LeaveEntitlementUsage
{
    private int $id;

    private float $noOfDays;

    private float $daysUsed;

    private DateTime $fromDate;

    private DateTime $toDate;

    private float $lengthDays;

    /**
     * @param int $id
     * @param float $noOfDays
     * @param float $daysUsed
     * @param DateTime $fromDate
     * @param DateTime $toDate
     * @param float $lengthDays
     */
    public function __construct(
        int $id,
        float $noOfDays,
        float $daysUsed,
        DateTime $fromDate,
        DateTime $toDate,
        float $lengthDays = 0
    ) {
        $this->id = $id;
        $this->noOfDays = $noOfDays;
        $this->daysUsed = $daysUsed;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->lengthDays = $lengthDays;
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
     * @return float
     */
    public function getNoOfDays(): float
    {
        return $this->noOfDays;
    }

    /**
     * @param float $noOfDays
     */
    public function setNoOfDays(float $noOfDays): void
    {
        $this->noOfDays = $noOfDays;
    }

    /**
     * @return float
     */
    public function getDaysUsed(): float
    {
        return $this->daysUsed;
    }

    /**
     * @param float $daysUsed
     */
    public function setDaysUsed(float $daysUsed): void
    {
        $this->daysUsed = $daysUsed;
    }

    /**
     * @return DateTime
     */
    public function getFromDate(): DateTime
    {
        return $this->fromDate;
    }

    /**
     * @param DateTime $fromDate
     */
    public function setFromDate(DateTime $fromDate): void
    {
        $this->fromDate = $fromDate;
    }

    /**
     * @return DateTime
     */
    public function getToDate(): DateTime
    {
        return $this->toDate;
    }

    /**
     * @param DateTime $toDate
     */
    public function setToDate(DateTime $toDate): void
    {
        $this->toDate = $toDate;
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
}
