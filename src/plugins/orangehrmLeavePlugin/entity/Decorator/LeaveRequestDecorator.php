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

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\LeaveRequest;
use OrangeHRM\Entity\LeaveRequestComment;
use OrangeHRM\Entity\LeaveType;
use OrangeHRM\ORM\ListSorter;

class LeaveRequestDecorator
{
    use EntityManagerHelperTrait;
    use DateTimeHelperTrait;

    /**
     * @var LeaveRequest
     */
    private LeaveRequest $leaveRequest;

    /**
     * @param LeaveRequest $leaveRequest
     */
    public function __construct(LeaveRequest $leaveRequest)
    {
        $this->leaveRequest = $leaveRequest;
    }

    /**
     * @return LeaveRequest
     */
    protected function getLeaveRequest(): LeaveRequest
    {
        return $this->leaveRequest;
    }

    /**
     * @param int $empNumber
     */
    public function setEmployeeByEmpNumber(int $empNumber): void
    {
        /** @var Employee|null $employee */
        $employee = $this->getReference(Employee::class, $empNumber);
        $this->getLeaveRequest()->setEmployee($employee);
    }

    /**
     * @param int $id
     */
    public function setLeaveTypeById(int $id): void
    {
        /** @var LeaveType|null $leaveType */
        $leaveType = $this->getReference(LeaveType::class, $id);
        $this->getLeaveRequest()->setLeaveType($leaveType);
    }

    /**
     * @return string
     */
    public function getDateApplied(): string
    {
        return $this->getDateTimeHelper()->formatDate($this->getLeaveRequest()->getDateApplied());
    }

    /**
     * @return LeaveRequestComment|null
     */
    public function getLastComment(): ?LeaveRequestComment
    {
        return $this->getRepository(LeaveRequestComment::class)
            ->findOneBy(['leaveRequest' => $this->getLeaveRequest()->getId()], ['createdAt' => ListSorter::DESCENDING]);
    }
}
