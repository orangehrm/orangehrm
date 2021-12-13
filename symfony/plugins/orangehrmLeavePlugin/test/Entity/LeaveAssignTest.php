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

namespace OrangeHRM\Tests\Leave\Entity;

use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\LeaveComment;
use OrangeHRM\Entity\LeaveLeaveEntitlement;
use OrangeHRM\Entity\LeaveRequest;
use OrangeHRM\Entity\LeaveRequestComment;
use OrangeHRM\Entity\LeaveType;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Leave
 * @group Entity
 */
class LeaveAssignTest extends EntityTestCase
{
    use DateTimeHelperTrait;

    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([Employee::class]);
        TestDataService::truncateSpecificTables([Leave::class]);
        TestDataService::truncateSpecificTables([LeaveType::class]);
        TestDataService::truncateSpecificTables([LeaveComment::class]);
        TestDataService::truncateSpecificTables([LeaveLeaveEntitlement::class]);
        TestDataService::truncateSpecificTables([LeaveRequest::class]);
        TestDataService::truncateSpecificTables([LeaveRequestComment::class]);
    }

    public function testAssignLeave(): void
    {
        $employee = new Employee();
        $leaveType = new LeaveType();
        $leave = new Leave();
        $leaveRequest = new LeaveRequest();

        $employee->setFirstName("Test");
        $employee->setEmployeeId(100);
        $employee->setLastName("OHRM");
        $this->persist($employee);

        $leaveType->setName("Medical");
        $this->persist($leaveType);

        $leaveRequest->setLeaveType($leaveType);
        $leaveRequest->setDateApplied(date_create('2021-10-01'));
        $leaveRequest->setEmployee($employee);
        $this->persist($leaveRequest);

        $leave->setDate(date_create('2021-10-01'));
        $leave->setLengthHours(8.00);
        $leave->setLengthDays(1.0000);
        $leave->setStatus(2);
        $leave->setLeaveRequest($leaveRequest);
        $leave->setLeaveType($leaveType);
        $leave->setEmployee($employee);
        $this->persist($leave);

        /** @var Leave $leave */
        $leave = $this->getRepository(Leave::class)->find(1);
        $this->assertEquals(date_create('2021-10-01'), $leave->getDate());
        $this->assertEquals(8.0, $leave->getLengthHours());
        $this->assertEquals(1.0, $leave->getLengthDays());
        $this->assertEquals(2, $leave->getStatus());
        $this->assertEquals(1, $leave->getLeaveRequest()->getId());
        $this->assertEquals(1, $leave->getLeaveType()->getId());
        $this->assertEquals(1, $leave->getEmployee()->getEmpNumber());
    }
}
