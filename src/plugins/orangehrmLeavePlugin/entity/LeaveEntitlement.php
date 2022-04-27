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
use OrangeHRM\Entity\Decorator\LeaveEntitlementDecorator;

/**
 * @method LeaveEntitlementDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_leave_entitlement")
 * @ORM\Entity
 */
class LeaveEntitlement
{
    use DecoratorTrait;
    use NumberHelperTrait;

    public const ENTITLEMENT_TYPE_ADD = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=10, options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var Employee
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee")
     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     */
    private Employee $employee;

    /**
     * @var float
     *
     * @ORM\Column(name="no_of_days", type="decimal", precision=19, scale=15)
     */
    private float $noOfDays;

    /**
     * @var float
     *
     * @ORM\Column(name="days_used", type="decimal", precision=8, scale=4, options={"default":0.0000})
     */
    private float $daysUsed = 0.0000;

    /**
     * @var LeaveType
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\LeaveType", inversedBy="leaveEntitlement")
     * @ORM\JoinColumn(name="leave_type_id", referencedColumnName="id")
     */
    private LeaveType $leaveType;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="from_date", type="datetime")
     */
    private DateTime $fromDate;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="to_date", type="datetime")
     */
    private DateTime $toDate;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="credited_date", type="datetime")
     */
    private ?DateTime $creditedDate;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="string", length=255)
     */
    private string $note;

    /**
     * @var LeaveEntitlementType
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\LeaveEntitlementType")
     * @ORM\JoinColumn(name="entitlement_type", referencedColumnName="id")
     */
    private LeaveEntitlementType $entitlementType;

    /**
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean", options={"default":0})
     */
    private bool $deleted = false;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\User")
     * @ORM\JoinColumn(name="created_by_id", referencedColumnName="id")
     */
    private User $createdBy;

    /**
     * @var Collection|LeaveLeaveEntitlement[]
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\LeaveLeaveEntitlement", mappedBy="entitlement")
     */
    private iterable $leaveLeaveEntitlements;

    public function __construct()
    {
        $this->leaveLeaveEntitlements = new ArrayCollection();
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
        $this->noOfDays = $this->getNumberHelper()->numberFormat($noOfDays, 4);
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
        $this->daysUsed = $this->getNumberHelper()->numberFormat($daysUsed, 4);
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
     * @return DateTime|null
     */
    public function getCreditedDate(): ?DateTime
    {
        return $this->creditedDate;
    }

    /**
     * @param DateTime|null $creditedDate
     */
    public function setCreditedDate(?DateTime $creditedDate): void
    {
        $this->creditedDate = $creditedDate;
    }

    /**
     * @return string
     */
    public function getNote(): string
    {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote(string $note): void
    {
        $this->note = $note;
    }

    /**
     * @return LeaveEntitlementType
     */
    public function getEntitlementType(): LeaveEntitlementType
    {
        return $this->entitlementType;
    }

    /**
     * @param LeaveEntitlementType $entitlementType
     */
    public function setEntitlementType(LeaveEntitlementType $entitlementType): void
    {
        $this->entitlementType = $entitlementType;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    /**
     * @return User
     */
    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     */
    public function setCreatedBy(User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }
}
