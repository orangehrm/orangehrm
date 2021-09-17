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

namespace OrangeHRM\Tests\Leave\Api\Model;

use DateTime;
use OrangeHRM\Authentication\Auth\User;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Service\NormalizerService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeTerminationRecord;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\LeaveRequest;
use OrangeHRM\Entity\LeaveType;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Api\Model\LeaveDetailedModel;
use OrangeHRM\Leave\Dto\LeaveRequest\DetailedLeave;
use OrangeHRM\Leave\Service\LeaveConfigurationService;
use OrangeHRM\Leave\Service\LeaveEntitlementService;
use OrangeHRM\Leave\Service\LeavePeriodService;
use OrangeHRM\Leave\Service\LeaveRequestService;
use OrangeHRM\Tests\Util\KernelTestCase;

/**
 * @group Leave
 * @group Model
 */
class LeaveDetailedModelTest extends KernelTestCase
{
    public function testToArray()
    {
        $resultArray = [
            "id" => 1,
            "dates" => [
                "fromDate" => "2021-08-01",
                "toDate" => null,
                "durationType" => [
                    "id" => 0,
                    "type" => "full_day"
                ],
                "startTime" => null,
                "endTime" => null
            ],
            "lengthHours" => 8.0,
            "leaveBalance" => [
                "period" => [
                    "startDate" => "2021-01-01",
                    "endDate" => "2021-12-31"
                ],
                "balance" => [
                    "entitled" => 0.0,
                    "used" => 1.0,
                    "scheduled" => 1.0,
                    "pending" => 0.0,
                    "taken" => 0.0,
                    "balance" => -1.0,
                    "asAtDate" => "2021-08-01",
                    "endDate" => "2021-12-31"
                ]
            ],
            "leaveStatus" => [
                "id" => 1,
                "name" => "Pending Approval",
                "lengthDays" => 1
            ],
            "allowedActions" => [
            ],
            "leaveType" => [
                "id" => 1,
                "name" => "Medical",
                "deleted" => false
            ],
            "lastComment" => null
        ];
        $leaveRequest1 = new LeaveRequest();
        $leaveRequest1->setId(1);
        $leave1 = new Leave();
        $leave1->setId(1);
        $leave1->setLeaveRequest($leaveRequest1);
        $leave1->setDate(new DateTime('2021-08-01'));
        $leave2 = new Leave();
        $leave2->setLeaveRequest($leaveRequest1);
        $leave2->setDate(new DateTime('2021-08-03'));
        $leave3 = new Leave();
        $leave3->setLeaveRequest($leaveRequest1);
        $leave3->setDate(new DateTime('2021-08-02'));

        $employeeTerminationRecord = new EmployeeTerminationRecord();
        $employeeTerminationRecord->setId(1);
        $employee = new Employee();
        $employee->setEmpNumber(1);
        $employee->setFirstName('Kayla');
        $employee->setLastName('Abbey');
        $employee->setEmployeeTerminationRecord($employeeTerminationRecord);

        $leave1->setEmployee($employee);
        $leaveType = new LeaveType();
        $leaveType->setId(1);
        $leaveType->setName("Medical");
        $leave1->setLeaveType($leaveType);

        $leave1->setStatus(1);
        $leave1->setLengthHours(8);
        $leave1->setLengthDays(1);

        $detailedLeave = new DetailedLeave($leave1);
        $detailedLeave->setLeaves([$leave1, $leave2, $leave3]);

        $leaveDetailedModel = new LeaveDetailedModel($detailedLeave);
        $authUser = $this->getMockBuilder(User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->exactly(1))
            ->method('getEmpNumber')
            ->willReturn(1);

        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->createKernelWithMockServices(
            [
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
                Services::LEAVE_REQUEST_SERVICE => new LeaveRequestService(),
                Services::LEAVE_ENTITLEMENT_SERVICE => new LeaveEntitlementService(),
                Services::LEAVE_CONFIG_SERVICE => new LeaveConfigurationService(),
                Services::LEAVE_PERIOD_SERVICE => new LeavePeriodService(),
                Services::NORMALIZER_SERVICE => new NormalizerService(),
                Services::AUTH_USER => $authUser,
                Services::USER_ROLE_MANAGER => $userRoleManager,
            ]
        );
        $this->assertEquals($resultArray, $leaveDetailedModel->toArray());
    }
}
