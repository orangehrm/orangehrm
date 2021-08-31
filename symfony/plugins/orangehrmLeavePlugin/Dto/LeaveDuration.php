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
use OrangeHRM\Entity\Leave;

class LeaveDuration
{
    public const FULL_DAY = 'full_day';
    public const HALF_DAY_MORNING = 'half_day_morning';
    public const HALF_DAY_AFTERNOON = 'half_day_afternoon';
    public const SPECIFY_TIME = 'specify_time';

    public const DURATION_MAP = [
        Leave::DURATION_TYPE_FULL_DAY => self::FULL_DAY,
        Leave::DURATION_TYPE_HALF_DAY_AM => self::HALF_DAY_MORNING,
        Leave::DURATION_TYPE_HALF_DAY_PM => self::HALF_DAY_AFTERNOON,
        Leave::DURATION_TYPE_SPECIFY_TIME => self::SPECIFY_TIME,
    ];

    /**
     * @var string
     */
    protected string $type;

    /**
     * @var DateTime|null
     */
    protected ?DateTime $fromTime;

    /**
     * @var DateTime|null
     */
    protected ?DateTime $toTime;

    /**
     * @param string $type
     * @param DateTime|null $fromTime
     * @param DateTime|null $toTime
     */
    public function __construct(string $type, DateTime $fromTime = null, DateTime $toTime = null)
    {
        $this->setType($type);
        $fromTime == null ?: $this->setFromTime($fromTime);
        $toTime == null ?: $this->setToTime($toTime);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        if (!in_array(
            $type,
            [
                LeaveDuration::FULL_DAY,
                LeaveDuration::HALF_DAY_MORNING,
                LeaveDuration::HALF_DAY_AFTERNOON,
                LeaveDuration::SPECIFY_TIME,
            ]
        )) {
            throw new InvalidArgumentException('Invalid duration type');
        }
        $this->type = $type;
    }

    /**
     * @return DateTime|null
     */
    public function getFromTime(): ?DateTime
    {
        $this->restrictCallConditionally();
        return $this->fromTime;
    }

    /**
     * @param DateTime|null $fromTime
     */
    public function setFromTime(?DateTime $fromTime): void
    {
        $this->restrictCallConditionally();
        $this->fromTime = $fromTime;
    }

    /**
     * @return DateTime|null
     */
    public function getToTime(): ?DateTime
    {
        $this->restrictCallConditionally();
        return $this->toTime;
    }

    /**
     * @param DateTime|null $toTime
     */
    public function setToTime(?DateTime $toTime): void
    {
        $this->restrictCallConditionally();
        if ($this->getFromTime() > $toTime) {
            throw new InvalidArgumentException('To time should be greater than from time');
        }
        $this->toTime = $toTime;
    }

    private function restrictCallConditionally(): void
    {
        if ($this->getType() !== LeaveDuration::SPECIFY_TIME) {
            throw new LogicException("Shouldn't call with `" . $this->getType() . '` duration type');
        }
    }

    /**
     * @return bool
     */
    public function isTypeFullDay(): bool
    {
        return $this->getType() === self::FULL_DAY;
    }

    /**
     * @return bool
     */
    public function isTypeHalfDay(): bool
    {
        return $this->getType() == LeaveDuration::HALF_DAY_MORNING
            || $this->getType() == LeaveDuration::HALF_DAY_AFTERNOON;
    }

    /**
     * @return bool
     */
    public function isTypeHalfDayMorning(): bool
    {
        return $this->getType() == LeaveDuration::HALF_DAY_MORNING;
    }

    /**
     * @return bool
     */
    public function isTypeHalfDayAfternoon(): bool
    {
        return $this->getType() == LeaveDuration::HALF_DAY_AFTERNOON;
    }

    /**
     * @return bool
     */
    public function isTypeSpecifyTime(): bool
    {
        return $this->getType() == LeaveDuration::SPECIFY_TIME;
    }
}
