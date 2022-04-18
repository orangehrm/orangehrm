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
use InvalidArgumentException;
use LogicException;

class LeaveParameterObject
{
    public const PARTIAL_OPTION_NONE = 'none';
    public const PARTIAL_OPTION_ALL = 'all';
    public const PARTIAL_OPTION_START = 'start';
    public const PARTIAL_OPTION_END = 'end';
    public const PARTIAL_OPTION_START_END = 'start_end';

    /**
     * @var int
     */
    protected int $employeeNumber;

    /**
     * @var int
     */
    protected int $leaveTypeId;

    /**
     * @var DateTime
     */
    protected DateTime $fromDate;

    /**
     * @var DateTime
     */
    protected DateTime $toDate;

    /**
     * @var string|null
     */
    protected ?string $comment = null;

    /**
     * @var LeaveDuration|null
     */
    protected ?LeaveDuration $singleDayDuration = null;

    /**
     * @var LeaveDuration|null
     */
    protected ?LeaveDuration $startMultiDayDuration = null;

    /**
     * @var LeaveDuration|null
     */
    protected ?LeaveDuration $endMultiDayDuration = null;

    /**
     * @var string
     */
    protected string $multiDayPartialOption = self::PARTIAL_OPTION_NONE;


    public function __construct(int $empNumber, int $leaveTypeId, DateTime $fromDate, DateTime $toDate)
    {
        $this->setEmployeeNumber($empNumber);
        $this->setLeaveType($leaveTypeId);
        $this->setFromDate($fromDate);
        $this->setToDate($toDate);
    }

    /**
     * @return bool
     */
    public function isMultiDayLeave(): bool
    {
        return $this->getFromDate() != $this->getToDate();
    }

    /**
     * @return int
     */
    public function getEmployeeNumber(): int
    {
        return $this->employeeNumber;
    }

    /**
     * @param int $employeeNumber
     */
    public function setEmployeeNumber(int $employeeNumber): void
    {
        $this->employeeNumber = $employeeNumber;
    }

    /**
     * @return int
     */
    public function getLeaveType(): int
    {
        return $this->leaveTypeId;
    }

    /**
     * @param int $leaveType
     */
    public function setLeaveType(int $leaveType): void
    {
        $this->leaveTypeId = $leaveType;
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
        if ($this->getFromDate() > $toDate) {
            throw new InvalidArgumentException("To date should be greater than from date");
        }
        $this->toDate = $toDate;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     */
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return LeaveDuration|null
     */
    public function getSingleDayDuration(): ?LeaveDuration
    {
        if ($this->isMultiDayLeave()) {
            throw new LogicException("Shouldn't call for multi day leave");
        }
        return $this->singleDayDuration;
    }

    /**
     * @param LeaveDuration|null $singleDayDuration
     */
    public function setSingleDayDuration(?LeaveDuration $singleDayDuration): void
    {
        $this->singleDayDuration = $singleDayDuration;
    }

    /**
     * @return LeaveDuration|null
     */
    public function getStartMultiDayDuration(): ?LeaveDuration
    {
        $this->checkMultiDayPartialOption(
            [
                LeaveParameterObject::PARTIAL_OPTION_ALL,
                LeaveParameterObject::PARTIAL_OPTION_START,
                LeaveParameterObject::PARTIAL_OPTION_START_END,
            ]
        );
        return $this->startMultiDayDuration;
    }

    /**
     * @param LeaveDuration|null $startMultiDayDuration
     */
    public function setStartMultiDayDuration(?LeaveDuration $startMultiDayDuration): void
    {
        $this->checkMultiDayPartialOption(
            [
                LeaveParameterObject::PARTIAL_OPTION_ALL,
                LeaveParameterObject::PARTIAL_OPTION_START,
                LeaveParameterObject::PARTIAL_OPTION_START_END,
            ]
        );
        $this->checkDurationTypeForMultiDayDuration($startMultiDayDuration);
        $this->startMultiDayDuration = $startMultiDayDuration;
    }

    /**
     * @return LeaveDuration|null
     */
    public function getEndMultiDayDuration(): ?LeaveDuration
    {
        $this->checkMultiDayPartialOption(
            [LeaveParameterObject::PARTIAL_OPTION_END, LeaveParameterObject::PARTIAL_OPTION_START_END]
        );
        return $this->endMultiDayDuration;
    }

    /**
     * @param LeaveDuration|null $endMultiDayDuration
     */
    public function setEndMultiDayDuration(?LeaveDuration $endMultiDayDuration): void
    {
        $this->checkMultiDayPartialOption(
            [LeaveParameterObject::PARTIAL_OPTION_END, LeaveParameterObject::PARTIAL_OPTION_START_END]
        );
        $this->checkDurationTypeForMultiDayDuration($endMultiDayDuration);
        $this->endMultiDayDuration = $endMultiDayDuration;
    }

    /**
     * @return string
     */
    public function getMultiDayPartialOption(): string
    {
        if (!$this->isMultiDayLeave()) {
            throw new LogicException("Shouldn't call for single day leave");
        }
        return $this->multiDayPartialOption;
    }

    /**
     * @param string $multiDayPartialOption
     */
    public function setMultiDayPartialOption(string $multiDayPartialOption): void
    {
        if (!in_array(
            $multiDayPartialOption,
            [
                LeaveParameterObject::PARTIAL_OPTION_NONE,
                LeaveParameterObject::PARTIAL_OPTION_ALL,
                LeaveParameterObject::PARTIAL_OPTION_START,
                LeaveParameterObject::PARTIAL_OPTION_END,
                LeaveParameterObject::PARTIAL_OPTION_START_END,
            ]
        )) {
            throw new InvalidArgumentException("Invalid partial day option");
        }
        $this->multiDayPartialOption = $multiDayPartialOption;
    }

    /**
     * @param LeaveDuration|null $multiDayDuration
     */
    private function checkDurationTypeForMultiDayDuration(?LeaveDuration $multiDayDuration): void
    {
        if ($multiDayDuration instanceof LeaveDuration && $multiDayDuration->getType() === LeaveDuration::FULL_DAY) {
            throw new InvalidArgumentException("`" . LeaveDuration::FULL_DAY . "` not allowed with multi day leave");
        }
    }

    /**
     * @param string[] $allowedPartialOptions
     */
    private function checkMultiDayPartialOption(array $allowedPartialOptions): void
    {
        if (!$this->isMultiDayLeave() || !in_array($this->getMultiDayPartialOption(), $allowedPartialOptions)) {
            throw new LogicException("Shouldn't call with `" . $this->getMultiDayPartialOption() . "` partial option");
        }
    }
}
