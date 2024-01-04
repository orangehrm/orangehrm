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

namespace OrangeHRM\Leave\Dto\LeaveRequest;

use DateTime;

class LeaveRequestDatesDetail
{
    private DateTime $fromDate;

    private ?DateTime $toDate;

    private ?int $durationTypeId = null;

    private ?string $durationType = null;

    private ?DateTime $startTime = null;

    private ?DateTime $endTime = null;

    /**
     * @param DateTime $fromDate
     * @param DateTime|null $toDate
     */
    public function __construct(DateTime $fromDate, ?DateTime $toDate = null)
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
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
     * @return DateTime|null
     */
    public function getToDate(): ?DateTime
    {
        return $this->toDate;
    }

    /**
     * @param DateTime|null $toDate
     */
    public function setToDate(?DateTime $toDate): void
    {
        $this->toDate = $toDate;
    }

    /**
     * @return int|null
     */
    public function getDurationTypeId(): ?int
    {
        return $this->durationTypeId;
    }

    /**
     * @param int|null $durationTypeId
     */
    public function setDurationTypeId(?int $durationTypeId): void
    {
        $this->durationTypeId = $durationTypeId;
    }

    /**
     * @return string|null
     */
    public function getDurationType(): ?string
    {
        return $this->durationType;
    }

    /**
     * @param string|null $durationType
     */
    public function setDurationType(?string $durationType): void
    {
        $this->durationType = $durationType;
    }

    /**
     * @return DateTime|null
     */
    public function getStartTime(): ?DateTime
    {
        return $this->startTime;
    }

    /**
     * @param DateTime|null $startTime
     */
    public function setStartTime(?DateTime $startTime): void
    {
        $this->startTime = $startTime;
    }

    /**
     * @return DateTime|null
     */
    public function getEndTime(): ?DateTime
    {
        return $this->endTime;
    }

    /**
     * @param DateTime|null $endTime
     */
    public function setEndTime(?DateTime $endTime): void
    {
        $this->endTime = $endTime;
    }
}
