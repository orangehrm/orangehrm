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
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Service\NormalizerService;
use OrangeHRM\Entity\Decorator\LeaveDecorator;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeTerminationRecord;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\LeaveRequest;
use OrangeHRM\Entity\LeaveType;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Api\Model\LeaveDetailedModel;
use OrangeHRM\Leave\Dto\LeavePeriod;
use OrangeHRM\Leave\Dto\LeaveRequest\DetailedLeave;
use OrangeHRM\Leave\Dto\LeaveRequest\LeaveBalanceWithLeavePeriod;
use OrangeHRM\Leave\Dto\LeaveRequest\LeaveRequestDatesDetail;
use OrangeHRM\Leave\Dto\LeaveRequest\LeaveStatusWithLengthDays;
use OrangeHRM\Leave\Entitlement\LeaveBalance;
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
                    "used" => 0.0,
                    "scheduled" => 0.0,
                    "pending" => 0.0,
                    "taken" => 0.0,
                    "balance" => 0.0,
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
                0 => [
                    'name' => "Approve",
                    'action' => "APPROVE"
                ],
                1 => [
                    'name' => "Reject",
                    'action' => "REJECT"
                ],
                2 => [
                    'name' => "Cancel",
                    'action' => "CANCEL"
                ]
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

        $leave = $this->getMockBuilder(Leave::class)
            ->onlyMethods(['getDecorator'])
            ->getMock();
        $leave->setId(1);
        $leave->setStatus(1);
        $leave->setDate(new DateTime('2021-08-01'));
        $leave->setEmployee($employee);
        $leaveType = new LeaveType();
        $leaveType->setId(1);
        $leaveType->setName("Medical");
        $leave->setLeaveType($leaveType);

        $leave->setStatus(1);
        $leave->setLengthHours(8);
        $leave->setLengthDays(1);

        $leaveDecorator = $this->getMockBuilder(LeaveDecorator::class)
            ->setConstructorArgs([$leave])
            ->onlyMethods(['getLeaveStatus', 'getLeaveStatusName'])
            ->getMock();

        $leave->expects($this->any())
            ->method('getDecorator')
            ->willReturn($leaveDecorator);


        $detailedLeave = $this->getMockBuilder(DetailedLeave::class)
            ->setConstructorArgs([$leave])
            ->onlyMethods(['getDatesDetail', 'getLeaveStatus', 'getLeaveBalance', 'getAllowedActions'])
            ->getMock();
        $detailedLeave->setLeaves([$leave1, $leave2, $leave3]);

        $datesDetail = new LeaveRequestDatesDetail($leave->getDate());
        $duration = 'full_day';
        $datesDetail->setDurationTypeId(0);
        $datesDetail->setDurationType($duration);

        $detailedLeave->expects($this->once())
            ->method('getDatesDetail')
            ->willReturn($datesDetail);

        $leaveStatus = new LeaveStatusWithLengthDays(
            1,
            'Pending Approval',
            1
        );
        $detailedLeave->expects($this->any())
            ->method('getLeaveStatus')
            ->willReturn($leaveStatus);

        $this->createKernelWithMockServices(
            [
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
                Services::LEAVE_REQUEST_SERVICE => new LeaveRequestService(),
                Services::LEAVE_ENTITLEMENT_SERVICE => new LeaveEntitlementService(),
                Services::LEAVE_CONFIG_SERVICE => new LeaveConfigurationService(),
                Services::LEAVE_PERIOD_SERVICE => new LeavePeriodService(),
                Services::NORMALIZER_SERVICE => new NormalizerService(),
            ]
        );
        $leavePeriod = new LeavePeriod(new DateTime('2021-01-01'), new DateTime('2021-12-31'));
        $leaveBalance = new LeaveBalance();
        $leaveBalance->setAsAtDate(new DateTime('2021-08-01'));
        $leaveBalance->setEndDate(new DateTime('2021-12-31'));
        $leaveBalanceWithLeavePeriod = new LeaveBalanceWithLeavePeriod($leaveBalance, $leavePeriod);

        $detailedLeave->expects($this->once())
            ->method('getLeaveBalance')
            ->willReturn($leaveBalanceWithLeavePeriod);

        $detailedLeave->expects($this->once())
            ->method('getAllowedActions')
            ->willReturn(['APPROVE', 'REJECT', 'CANCEL']);

        $leaveDetailedModel = new LeaveDetailedModel($detailedLeave);

        $this->assertEquals($resultArray, $leaveDetailedModel->toArray());
    }
}
