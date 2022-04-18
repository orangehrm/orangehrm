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

namespace OrangeHRM\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\LeavePeriodHistoryDecorator;

/**
 * @method LeavePeriodHistoryDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_leave_period_history")
 * @ORM\Entity
 */
class LeavePeriodHistory
{
    use DecoratorTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var int
     *
     * @ORM\Column(name="leave_period_start_month", type="integer")
     */
    private int $startMonth;

    /**
     * @var int
     *
     * @ORM\Column(name="leave_period_start_day", type="integer")
     */
    private int $startDay;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="created_at", type="date")
     */
    private DateTime $createdAt;

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
     * @return int
     */
    public function getStartMonth(): int
    {
        return $this->startMonth;
    }

    /**
     * @param int $startMonth
     */
    public function setStartMonth(int $startMonth): void
    {
        $this->startMonth = $startMonth;
    }

    /**
     * @return int
     */
    public function getStartDay(): int
    {
        return $this->startDay;
    }

    /**
     * @param int $startDay
     */
    public function setStartDay(int $startDay): void
    {
        $this->startDay = $startDay;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
