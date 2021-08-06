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

/**
 * @ORM\Table(name="ohrm_leave_request_comment")
 * @ORM\Entity
 */
class LeaveRequestComment
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var LeaveRequest
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\LeaveRequest")
     * @ORM\JoinColumn(name="leave_request_id", referencedColumnName="id")
     */
    private LeaveRequest $leaveRequest;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private DateTime $created;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\User")
     * @ORM\JoinColumn(name="created_by_id", referencedColumnName="id", nullable=true)
     */
    private ?User $createdBy;

    /**
     * @var Employee|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee")
     * @ORM\JoinColumn(name="created_by_emp_number", referencedColumnName="emp_number", nullable=true)
     */
    private ?Employee $createdByEmployee;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comments", type="string", length=255, nullable=true)
     */
    private ?string $comment;

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
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }

    /**
     * @param DateTime $created
     */
    public function setCreated(DateTime $created): void
    {
        $this->created = $created;
    }

    /**
     * @return User|null
     */
    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    /**
     * @param User|null $createdBy
     */
    public function setCreatedBy(?User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return Employee|null
     */
    public function getCreatedByEmployee(): ?Employee
    {
        return $this->createdByEmployee;
    }

    /**
     * @param Employee|null $createdByEmployee
     */
    public function setCreatedByEmployee(?Employee $createdByEmployee): void
    {
        $this->createdByEmployee = $createdByEmployee;
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
}
