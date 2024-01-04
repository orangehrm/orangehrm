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

namespace OrangeHRM\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\LeaveRequestDecorator;

/**
 * @method LeaveRequestDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_leave_request")
 * @ORM\Entity
 */
class LeaveRequest
{
    use DecoratorTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var LeaveType
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\LeaveType")
     * @ORM\JoinColumn(name="leave_type_id", referencedColumnName="id")
     */
    private LeaveType $leaveType;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date_applied", type="date")
     */
    private DateTime $dateApplied;

    /**
     * @var Employee
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee")
     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     */
    private Employee $employee;

    /**
     * @var Leave[]
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Leave", mappedBy="leaveRequest")
     */
    private iterable $leaves;

    public function __construct()
    {
        $this->leaves = new ArrayCollection();
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
    public function getDateApplied(): DateTime
    {
        return $this->dateApplied;
    }

    /**
     * @param DateTime $dateApplied
     */
    public function setDateApplied(DateTime $dateApplied): void
    {
        $this->dateApplied = $dateApplied;
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
     * @return Leave[]
     */
    public function getLeaves(): iterable
    {
        return $this->leaves;
    }

    /**
     * @param Leave[] $leaves
     */
    public function setLeaves(array $leaves): void
    {
        $this->leaves = $leaves;
    }
}
