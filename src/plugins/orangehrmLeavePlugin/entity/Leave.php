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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Core\Traits\Service\NumberHelperTrait;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\LeaveDecorator;

/**
 * @method LeaveDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_leave")
 * @ORM\Entity
 */
class Leave
{
    use DecoratorTrait;
    use NumberHelperTrait;

    public const LEAVE_STATUS_LEAVE_REJECTED = -1;
    public const LEAVE_STATUS_LEAVE_CANCELLED = 0;
    public const LEAVE_STATUS_LEAVE_PENDING_APPROVAL = 1;
    public const LEAVE_STATUS_LEAVE_APPROVED = 2;
    public const LEAVE_STATUS_LEAVE_TAKEN = 3;
    public const LEAVE_STATUS_LEAVE_WEEKEND = 4;
    public const LEAVE_STATUS_LEAVE_HOLIDAY = 5;

    public const DURATION_TYPE_FULL_DAY = 0;
    public const DURATION_TYPE_HALF_DAY_AM = 1;
    public const DURATION_TYPE_HALF_DAY_PM = 2;
    public const DURATION_TYPE_SPECIFY_TIME = 3;

    /**
     * @var int|null
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = null;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private DateTime $date;

    /**
     * @var float
     *
     * @ORM\Column(name="length_hours", type="decimal", precision=6, scale=2, options={"unsigned":true})
     */
    private float $lengthHours;

    /**
     * @var float
     *
     * @ORM\Column(name="length_days", type="decimal", precision=6, scale=4, options={"unsigned":true})
     */
    private float $lengthDays;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint", length=6)
     */
    private int $status;

    /**
     * @var LeaveRequest
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\LeaveRequest", inversedBy="leaves")
     * @ORM\JoinColumn(name="leave_request_id", referencedColumnName="id")
     */
    private LeaveRequest $leaveRequest;

    /**
     * @var LeaveType
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\LeaveType")
     * @ORM\JoinColumn(name="leave_type_id", referencedColumnName="id")
     */
    private LeaveType $leaveType;

    /**
     * @var Employee
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee")
     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     */
    private Employee $employee;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="start_time", type="time", nullable=true)
     */
    private DateTime $startTime;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="end_time", type="time", nullable=true)
     */
    private DateTime $endTime;

    /**
     * @var int
     *
     * @ORM\Column(name="duration_type", type="integer", options={"default":0})
     */
    private int $durationType = 0;

    /**
     * @var Collection|LeaveLeaveEntitlement[]
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\LeaveLeaveEntitlement", mappedBy="leave")
     */
    private iterable $leaveLeaveEntitlements;

    public function __construct()
    {
        $this->leaveLeaveEntitlements = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
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
        $this->lengthHours = $this->getNumberHelper()->numberFormat($lengthHours, 2);
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
        $this->lengthDays = $this->getNumberHelper()->numberFormat($lengthDays, 4);
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
     * @return LeaveRequest
     */
    public function getLeaveRequest(): LeaveRequest
    {
        return $this->leaveRequest;
    }

    /**
     * @param LeaveRequest $leaveRequest
     */
    public function setLeaveRequest(LeaveRequest $leaveRequest): void
    {
        $this->leaveRequest = $leaveRequest;
    }

    /**
     * @return LeaveType
     */
    public function getLeaveType(): LeaveType
    {
        return $this->leaveType;
    }

    /**
     * @param LeaveType $leaveType
     */
    public function setLeaveType(LeaveType $leaveType): void
    {
        $this->leaveType = $leaveType;
    }

    /**
     * @return Employee
     */
    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    /**
     * @param Employee $employee
     */
    public function setEmployee(Employee $employee): void
    {
        $this->employee = $employee;
    }

    /**
     * @return DateTime
     */
    public function getStartTime(): DateTime
    {
        return $this->startTime;
    }

    /**
     * @param DateTime $startTime
     */
    public function setStartTime(DateTime $startTime): void
    {
        $this->startTime = $startTime;
    }

    /**
     * @return DateTime
     */
    public function getEndTime(): DateTime
    {
        return $this->endTime;
    }

    /**
     * @param DateTime $endTime
     */
    public function setEndTime(DateTime $endTime): void
    {
        $this->endTime = $endTime;
    }

    /**
     * @return int
     */
    public function getDurationType(): int
    {
        return $this->durationType;
    }

    /**
     * @param int $durationType
     */
    public function setDurationType(int $durationType): void
    {
        $this->durationType = $durationType;
    }
}
