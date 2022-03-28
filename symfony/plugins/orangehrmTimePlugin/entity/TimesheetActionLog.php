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
use OrangeHRM\Entity\Decorator\TimesheetActionLogDecorator;

/**
 * @method TimesheetActionLogDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_timesheet_action_log")
 * @ORM\Entity
 */
class TimesheetActionLog
{
    use DecoratorTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="timesheet_action_log_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", length=255)
     */
    private string $action;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     */
    private ?string $comment = null;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date_time", type="date")
     */
    private DateTime $date;

    /**
     * @var User|null
     *
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\User")
     * @ORM\JoinColumn(name="performed_by", referencedColumnName="id")
     */
    private ?User $performedUser;

    /**
     * @var Timesheet
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Timesheet")
     * @ORM\JoinColumn(name="timesheet_id", referencedColumnName="timesheet_id")
     */
    private Timesheet $timesheet;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param  int  $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param  string  $action
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param  string|null  $comment
     */
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param  DateTime  $date
     */
    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return User|null
     */
    public function getPerformedUser(): ?User
    {
        return $this->performedUser;
    }

    /**
     * @param  User|null  $performedUser
     */
    public function setPerformedUser(?User $performedUser): void
    {
        $this->performedUser = $performedUser;
    }

    /**
     * @return Timesheet
     */
    public function getTimesheet(): Timesheet
    {
        return $this->timesheet;
    }

    /**
     * @param  Timesheet  $timesheet
     */
    public function setTimesheet(Timesheet $timesheet): void
    {
        $this->timesheet = $timesheet;
    }
}
