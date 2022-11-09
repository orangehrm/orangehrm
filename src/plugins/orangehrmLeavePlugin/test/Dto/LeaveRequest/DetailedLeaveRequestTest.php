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

namespace OrangeHRM\Tests\Leave\Dto\LeaveRequest;

use DateTime;
use Error;
use Exception;
use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Authorization\Manager\UserRoleManagerFactory;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\LeaveRequest;
use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Dto\LeaveRequest\DetailedLeaveRequest;
use OrangeHRM\Leave\Service\LeaveConfigurationService;
use OrangeHRM\Leave\Service\LeaveEntitlementService;
use OrangeHRM\Leave\Service\LeavePeriodService;
use OrangeHRM\Leave\Service\LeaveRequestService;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\Mock\MockAuthUser;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Leave
 * @group Dto
 */
class DetailedLeaveRequestTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmLeavePlugin/test/fixtures/DetailedLeaveRequest.yaml';
        TestDataService::populate($fixture);
    }

    public function testEmptyLeaveRequest(): void
    {
        $leaveRequest = new LeaveRequest();
        $detailedLeaveRequest = new DetailedLeaveRequest($leaveRequest);
        try {
            $detailedLeaveRequest->getLeaves();
        } catch (Error $e) {
            $this->assertEquals(
                'Cannot access uninitialized non-nullable property OrangeHRM\Leave\Dto\LeaveRequest\DetailedLeaveRequest::$leaveDates by reference',
                $e->getMessage()
            );
        }

        try {
            $detailedLeaveRequest->setLeaves([]);
        } catch (Exception $e) {
            $this->assertEquals('Not excepting empty iterable', $e->getMessage());
        }
    }

    public function testSingleDayLeaveRequest(): void
    {
        $leaveRequest = $this->getEntityReference(LeaveRequest::class, 1);
        $detailedLeaveRequest = new DetailedLeaveRequest($leaveRequest);
        $detailedLeaveRequest->fetchLeaves();
        $this->assertEquals(1, $detailedLeaveRequest->getCount());
        $this->assertEquals(1.0, $detailedLeaveRequest->getNoOfDays());
        $datesDetail = $detailedLeaveRequest->getDatesDetail();
        $this->assertEquals('2021-09-01', $datesDetail->getFromDate()->format('Y-m-d'));
        $this->assertNull($datesDetail->getToDate());
        $this->assertEquals(0, $datesDetail->getDurationTypeId());
        $this->assertEquals('full_day', $datesDetail->getDurationType());
    }

    public function testMultiDayLeaveRequestFirstDayHoliday(): void
    {
        $this->setServices();
        $leaveRequest = $this->getEntityReference(LeaveRequest::class, 19);
        $detailedLeaveRequest = new DetailedLeaveRequest($leaveRequest);
        $detailedLeaveRequest->fetchLeaves();
        $this->assertEquals(['CANCEL'], $detailedLeaveRequest->getAllowedActions());
    }

    public function testMultiStatusLeaveRequest(): void
    {
        $this->setServices();
        $leaveRequest = $this->getEntityReference(LeaveRequest::class, 4);
        $detailedLeaveRequest = new DetailedLeaveRequest($leaveRequest);
        $detailedLeaveRequest->fetchLeaves();
        $this->assertEquals([], $detailedLeaveRequest->getAllowedActions());
    }

    private function setServices(): void
    {
        $authUser = $this->getMockBuilder(MockAuthUser::class)
            ->onlyMethods(['getUserId', 'getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->method('getUserId')
            ->willReturn(1);
        $authUser->method('getEmpNumber')
            ->willReturn(
                $this->getEntityReference(
                    User::class,
                    1
                )->getEmployee()->getEmpNumber()
            );
        $this->createKernelWithMockServices([
            Services::LEAVE_REQUEST_SERVICE => new LeaveRequestService(),
            Services::CONFIG_SERVICE => new ConfigService(),
            Services::USER_SERVICE => new UserService(),
            Services::EMPLOYEE_SERVICE => new EmployeeService(),
            Services::AUTH_USER => $authUser,
        ]);
        $this->getContainer()->register(Services::USER_ROLE_MANAGER)->setFactory(
            [UserRoleManagerFactory::class, 'getNewUserRoleManager']
        );
    }

    public function testGetLeavePeriodsWithAssignedLeaveForPastLeavePeriod(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->atLeastOnce())
            ->method('getNow')
            ->willReturnCallback(fn () => new DateTime('2021-11-16'));

        $this->createKernelWithMockServices([
            Services::LEAVE_REQUEST_SERVICE => new LeaveRequestService(),
            Services::LEAVE_ENTITLEMENT_SERVICE => new LeaveEntitlementService(),
            Services::LEAVE_CONFIG_SERVICE => new LeaveConfigurationService(),
            Services::LEAVE_PERIOD_SERVICE => new LeavePeriodService(),
            Services::DATETIME_HELPER_SERVICE => $dateTimeHelper,
        ]);
        $leaveRequest = $this->getEntityReference(LeaveRequest::class, 20);
        $detailedLeaveRequest = new DetailedLeaveRequest($leaveRequest);
        $detailedLeaveRequest->fetchLeaves();
        $this->assertEquals([], $detailedLeaveRequest->getLeavePeriods());
    }
}
