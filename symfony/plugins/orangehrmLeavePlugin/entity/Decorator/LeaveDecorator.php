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

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\LeaveComment;
use OrangeHRM\Entity\LeaveType;
use OrangeHRM\Leave\Dto\LeaveDuration;
use OrangeHRM\Leave\Traits\Service\LeaveRequestServiceTrait;
use OrangeHRM\ORM\ListSorter;

class LeaveDecorator
{
    use EntityManagerHelperTrait;
    use DateTimeHelperTrait;
    use LeaveRequestServiceTrait;

    /**
     * @var Leave
     */
    private Leave $leave;

    /**
     * @param Leave $leave
     */
    public function __construct(Leave $leave)
    {
        $this->leave = $leave;
    }

    /**
     * @return Leave
     */
    protected function getLeave(): Leave
    {
        return $this->leave;
    }

    /**
     * @param int $empNumber
     */
    public function setEmployeeByEmpNumber(int $empNumber): void
    {
        /** @var Employee|null $employee */
        $employee = $this->getReference(Employee::class, $empNumber);
        $this->getLeave()->setEmployee($employee);
    }

    /**
     * @param int $id
     */
    public function setLeaveTypeById(int $id): void
    {
        /** @var LeaveType|null $leaveType */
        $leaveType = $this->getReference(LeaveType::class, $id);
        $this->getLeave()->setLeaveType($leaveType);
    }

    /**
     * @return string e.g. ['Pending Approval', 'Scheduled', 'Taken', 'Rejected', 'Cancelled']
     */
    public function getLeaveStatus(): string
    {
        return ucwords(strtolower($this->getLeaveStatusName()));
    }

    /**
     * @return string e.g. ['PENDING APPROVAL', 'SCHEDULED', 'TAKEN', 'REJECTED', 'CANCELLED']
     */
    public function getLeaveStatusName(): string
    {
        return $this->getLeaveRequestService()->getLeaveStatusNameByStatus($this->getLeave()->getStatus());
    }

    /**
     * @return string Y-m-d date
     */
    public function getLeaveDate(): string
    {
        return $this->getDateTimeHelper()->formatDateTimeToYmd($this->getLeave()->getDate());
    }

    /**
     * @return string|null
     */
    public function getLeaveDuration(): ?string
    {
        $type = $this->getLeave()->getDurationType();
        if (isset(LeaveDuration::DURATION_MAP[$type])) {
            return LeaveDuration::DURATION_MAP[$type];
        }
        return null;
    }

    /**
     * @return string|null H:i format
     */
    public function getStartTime(): ?string
    {
        if ($this->getLeave()->getDurationType() !== Leave::DURATION_TYPE_SPECIFY_TIME) {
            return null;
        }
        return $this->getDateTimeHelper()->formatDateTimeToTimeString($this->getLeave()->getStartTime());
    }

    /**
     * @return string|null H:i format
     */
    public function getEndTime(): ?string
    {
        if ($this->getLeave()->getDurationType() !== Leave::DURATION_TYPE_SPECIFY_TIME) {
            return null;
        }
        return $this->getDateTimeHelper()->formatDateTimeToTimeString($this->getLeave()->getEndTime());
    }

    /**
     * @return LeaveComment|null
     */
    public function getLastComment(): ?LeaveComment
    {
        return $this->getRepository(LeaveComment::class)
            ->findOneBy(['leave' => $this->getLeave()->getId()], ['createdAt' => ListSorter::DESCENDING]);
    }
}
