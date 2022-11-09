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

namespace OrangeHRM\Tests\Leave\Service;

use DateTime;
use Error;
use Generator;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Core\Helper\ClassHelper;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Service\NumberHelperService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\LeaveEntitlement;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\UserRole;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Dao\LeaveEntitlementDao;
use OrangeHRM\Leave\Dto\CurrentAndChangeEntitlement;
use OrangeHRM\Leave\Dto\LeavePeriod;
use OrangeHRM\Leave\Entitlement\EntitlementConsumptionStrategy;
use OrangeHRM\Leave\Entitlement\FIFOEntitlementConsumptionStrategy;
use OrangeHRM\Leave\Entitlement\LeaveBalance;
use OrangeHRM\Leave\Service\LeaveConfigurationService;
use OrangeHRM\Leave\Service\LeaveEntitlementService;
use OrangeHRM\Leave\Service\LeavePeriodService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;
use TypeError;

/**
 * @group Leave
 * @group Service
 */
class LeaveEntitlementServiceTest extends KernelTestCase
{
    private LeaveEntitlementService $service;

    protected function setUp(): void
    {
        $this->service = new LeaveEntitlementService();
    }

    public function testGetLeaveEntitlementStrategy(): void
    {
        $leaveConfigService = $this->getMockBuilder(LeaveConfigurationService::class)
            ->onlyMethods(['getLeaveEntitlementConsumptionStrategy'])
            ->getMock();
        $leaveConfigService->expects($this->once())
            ->method('getLeaveEntitlementConsumptionStrategy')
            ->willReturn(TestEntitlementConsumptionStrategy::class);
        $this->createKernelWithMockServices(
            [
                Services::LEAVE_CONFIG_SERVICE => $leaveConfigService,
                Services::CLASS_HELPER => new ClassHelper(),
            ]
        );
        $this->assertTrue($this->service->getLeaveEntitlementStrategy() instanceof EntitlementConsumptionStrategy);
    }

    public function testGetLeaveEntitlementStrategyNonExistClass(): void
    {
        $leaveConfigService = $this->getMockBuilder(LeaveConfigurationService::class)
            ->onlyMethods(['getLeaveEntitlementConsumptionStrategy'])
            ->getMock();
        $leaveConfigService->expects($this->once())
            ->method('getLeaveEntitlementConsumptionStrategy')
            ->willReturn('NonExistLeaveEntitlementConsumptionStrategy');
        $this->createKernelWithMockServices(
            [
                Services::LEAVE_CONFIG_SERVICE => $leaveConfigService,
                Services::CLASS_HELPER => new ClassHelper(),
            ]
        );

        $this->expectError();
        $this->service->getLeaveEntitlementStrategy();
    }

    public function testGetLeaveEntitlementStrategyInvalidClass(): void
    {
        $leaveConfigService = $this->getMockBuilder(LeaveConfigurationService::class)
            ->onlyMethods(['getLeaveEntitlementConsumptionStrategy'])
            ->getMock();
        $leaveConfigService->expects($this->once())
            ->method('getLeaveEntitlementConsumptionStrategy')
            ->willReturn(InvalidLeaveEntitlementConsumptionStrategy::class);
        $this->createKernelWithMockServices(
            [
                Services::LEAVE_CONFIG_SERVICE => $leaveConfigService,
                Services::CLASS_HELPER => new ClassHelper(),
            ]
        );

        try {
            $this->service->getLeaveEntitlementStrategy();
            $this->fail();
        } catch (Error $e) {
            $this->assertTrue($e instanceof TypeError);
        }
    }

    public function testGetLeaveEntitlementDao(): void
    {
        $this->assertTrue($this->service->getLeaveEntitlementDao() instanceof LeaveEntitlementDao);
    }

    /**
     * @dataProvider getDeletableIdsFromEntitlementIdsDataProvider
     */
    public function testGetDeletableIdsFromEntitlementIds(array $ids, array $entitlementList, array $expected): void
    {
        $dao = $this->getMockBuilder(LeaveEntitlementDao::class)
            ->onlyMethods(['getLeaveEntitlementsByIds'])
            ->getMock();
        $dao->expects($this->once())
            ->method('getLeaveEntitlementsByIds')
            ->with($ids)
            ->willReturn($entitlementList);
        $service = $this->getMockBuilder(LeaveEntitlementService::class)
            ->onlyMethods(['getLeaveEntitlementDao'])
            ->getMock();
        $service->expects($this->once())
            ->method('getLeaveEntitlementDao')
            ->willReturn($dao);
        $this->assertEquals($expected, $service->getDeletableIdsFromEntitlementIds($ids));
    }

    /**
     * @return Generator
     */
    public function getDeletableIdsFromEntitlementIdsDataProvider(): Generator
    {
        $this->createKernelWithMockServices([Services::NUMBER_HELPER_SERVICE => new NumberHelperService()]);
        $entitlement1 = new LeaveEntitlement();
        $entitlement1->setId(1);
        $entitlement1->setDaysUsed(0);

        $entitlement2 = new LeaveEntitlement();
        $entitlement2->setId(2);
        $entitlement2->setDaysUsed(0);

        $entitlement3 = new LeaveEntitlement();
        $entitlement3->setId(3);
        $entitlement3->setDaysUsed(1);

        yield [
            [1, 2, 3],
            [$entitlement1, $entitlement2, $entitlement3],
            [1, 2]
        ];
        yield [
            [2, 1],
            [$entitlement2, $entitlement1],
            [2, 1]
        ];
        yield [
            [3, 2, 1],
            [$entitlement3, $entitlement2, $entitlement1],
            [2, 1]
        ];
        yield [
            [100, 2, 1],
            [$entitlement2, $entitlement1],
            [2, 1]
        ];
        yield [[100], [], []];
        yield [
            [100, 2, 1],
            [$entitlement2, $entitlement1],
            [2, 1]
        ];
    }

    public function testGetLeaveBalanceWithDates(): void
    {
        $empNumber = 1;
        $leaveTypeId = 2;
        $asAtDate = new DateTime('2021-08-19');
        $endDate = new DateTime('2021-12-31');
        $leaveConfigurationService = $this->getMockBuilder(LeaveConfigurationService::class)
            ->onlyMethods(['includePendingLeaveInBalance'])
            ->getMock();
        $leaveConfigurationService->expects($this->once())
            ->method('includePendingLeaveInBalance')
            ->willReturn(true);

        $this->createKernelWithMockServices([Services::LEAVE_CONFIG_SERVICE => $leaveConfigurationService]);

        $leaveBalance = new LeaveBalance();
        $dao = $this->getMockBuilder(LeaveEntitlementDao::class)
            ->onlyMethods(['getLeaveBalance'])
            ->getMock();
        $dao->expects($this->once())
            ->method('getLeaveBalance')
            ->with($empNumber, $leaveTypeId, $asAtDate, $endDate)
            ->willReturn($leaveBalance);
        $service = $this->getMockBuilder(LeaveEntitlementService::class)
            ->onlyMethods(['getLeaveEntitlementDao'])
            ->getMock();
        $service->expects($this->once())
            ->method('getLeaveEntitlementDao')
            ->willReturn($dao);
        $this->assertEquals($leaveBalance, $service->getLeaveBalance($empNumber, $leaveTypeId, $asAtDate, $endDate));
    }

    public function testGetLeaveBalanceWithOnlyEndDate()
    {
        $empNumber = 1;
        $leaveTypeId = 2;
        $asAtDate = new DateTime('2021-08-19');
        $endDate = new DateTime('2021-12-31');

        $leaveConfigurationService = $this->getMockBuilder(LeaveConfigurationService::class)
            ->onlyMethods(['includePendingLeaveInBalance'])
            ->getMock();
        $leaveConfigurationService->expects($this->once())
            ->method('includePendingLeaveInBalance')
            ->willReturn(true);

        $dateTimeHelperService = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelperService->expects($this->once())
            ->method('getNow')
            ->willReturn(new DateTime('2021-08-19'));
        $this->createKernelWithMockServices(
            [
                Services::DATETIME_HELPER_SERVICE => $dateTimeHelperService,
                Services::LEAVE_CONFIG_SERVICE => $leaveConfigurationService
            ]
        );

        $leaveBalance = new LeaveBalance();
        $dao = $this->getMockBuilder(LeaveEntitlementDao::class)
            ->onlyMethods(['getLeaveBalance'])
            ->getMock();
        $dao->expects($this->once())
            ->method('getLeaveBalance')
            ->with($empNumber, $leaveTypeId, $asAtDate, $endDate)
            ->willReturn($leaveBalance);
        $service = $this->getMockBuilder(LeaveEntitlementService::class)
            ->onlyMethods(['getLeaveEntitlementDao'])
            ->getMock();
        $service->expects($this->once())
            ->method('getLeaveEntitlementDao')
            ->willReturn($dao);
        $this->assertEquals($leaveBalance, $service->getLeaveBalance($empNumber, $leaveTypeId, null, $endDate));
    }

    public function testGetLeaveBalanceWithOnlyAsAtDate(): void
    {
        $empNumber = 1;
        $leaveTypeId = 2;
        $asAtDate = new DateTime('2021-08-19');
        $endDate = new DateTime('2021-12-31');

        $leaveConfigurationService = $this->getMockBuilder(LeaveConfigurationService::class)
            ->onlyMethods(['getLeavePeriodStatus', 'includePendingLeaveInBalance'])
            ->getMock();
        $leaveConfigurationService->expects($this->once())
            ->method('getLeavePeriodStatus')
            ->willReturn(LeavePeriodService::LEAVE_PERIOD_STATUS_FORCED);
        $leaveConfigurationService->expects($this->once())
            ->method('includePendingLeaveInBalance')
            ->willReturn(true);

        $this->createKernelWithMockServices([Services::LEAVE_CONFIG_SERVICE => $leaveConfigurationService]);

        $leaveBalance = new LeaveBalance();
        $dao = $this->getMockBuilder(LeaveEntitlementDao::class)
            ->onlyMethods(['getLeaveBalance'])
            ->getMock();
        $dao->expects($this->once())
            ->method('getLeaveBalance')
            ->with($empNumber, $leaveTypeId, $asAtDate, $endDate)
            ->willReturn($leaveBalance);

        $strategy = $this->getMockBuilder(FIFOEntitlementConsumptionStrategy::class)
            ->onlyMethods(['getLeavePeriod'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('getLeavePeriod')
            ->willReturn(new LeavePeriod(new DateTime('2021-01-01'), $endDate));

        $service = $this->getMockBuilder(LeaveEntitlementService::class)
            ->onlyMethods(['getLeaveEntitlementDao', 'getLeaveEntitlementStrategy'])
            ->getMock();
        $service->expects($this->once())
            ->method('getLeaveEntitlementDao')
            ->willReturn($dao);
        $service->expects($this->once())
            ->method('getLeaveEntitlementStrategy')
            ->willReturn($strategy);
        $this->assertEquals($leaveBalance, $service->getLeaveBalance($empNumber, $leaveTypeId, $asAtDate, null));
    }

    public function testGetLeaveBalanceWithOnlyAsAtDateLeavePeriodNotForced(): void
    {
        $empNumber = 1;
        $leaveTypeId = 2;
        $asAtDate = new DateTime('2021-08-19');
        $endDate = null;

        $leaveConfigurationService = $this->getMockBuilder(LeaveConfigurationService::class)
            ->onlyMethods(['getLeavePeriodStatus', 'includePendingLeaveInBalance'])
            ->getMock();
        $leaveConfigurationService->expects($this->once())
            ->method('getLeavePeriodStatus')
            ->willReturn(LeavePeriodService::LEAVE_PERIOD_STATUS_NOT_FORCED);
        $leaveConfigurationService->expects($this->once())
            ->method('includePendingLeaveInBalance')
            ->willReturn(true);

        $this->createKernelWithMockServices(
            [Services::LEAVE_CONFIG_SERVICE => $leaveConfigurationService]
        );

        $leaveBalance = new LeaveBalance();
        $dao = $this->getMockBuilder(LeaveEntitlementDao::class)
            ->onlyMethods(['getLeaveBalance'])
            ->getMock();
        $dao->expects($this->once())
            ->method('getLeaveBalance')
            ->with($empNumber, $leaveTypeId, $asAtDate, $endDate)
            ->willReturn($leaveBalance);

        $service = $this->getMockBuilder(LeaveEntitlementService::class)
            ->onlyMethods(['getLeaveEntitlementDao', 'getLeaveEntitlementStrategy'])
            ->getMock();
        $service->expects($this->once())
            ->method('getLeaveEntitlementDao')
            ->willReturn($dao);
        $service->expects($this->never())
            ->method('getLeaveEntitlementStrategy');
        $this->assertEquals($leaveBalance, $service->getLeaveBalance($empNumber, $leaveTypeId, $asAtDate, null));
    }

    public function testAddEntitlementForEmployeeAsNew(): void
    {
        $this->loadFixtures();
        $user = $this->getEntityReference(User::class, 1);

        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUser'])
            ->getMock();
        $userRoleManager->expects($this->once())
            ->method('getUser')
            ->willReturn($user);
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->once())
            ->method('getNow')
            ->willReturn(new DateTime('2021-10-04'));
        $this->createKernelWithMockServices(
            [
                Services::LEAVE_CONFIG_SERVICE => new LeaveConfigurationService(),
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::DATETIME_HELPER_SERVICE => $dateTimeHelper,
            ]
        );

        $leaveEntitlement = $this->service->addEntitlementForEmployee(
            2,
            2,
            new DateTime('2021-01-01'),
            new DateTime('2021-12-31'),
            5.5
        );
        $this->assertEquals(5.5, $leaveEntitlement->getNoOfDays());
        $this->assertEquals(0, $leaveEntitlement->getDaysUsed());
        $this->assertEquals('2021-10-04', $leaveEntitlement->getCreditedDate()->format('Y-m-d'));
    }

    public function testAddEntitlementForEmployeeAsAdding(): void
    {
        $this->loadFixtures();
        $user = $this->getEntityReference(User::class, 1);

        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUser'])
            ->getMock();
        $userRoleManager->expects($this->once())
            ->method('getUser')
            ->willReturn($user);
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->once())
            ->method('getNow')
            ->willReturn(new DateTime('2021-10-04'));
        $this->createKernelWithMockServices(
            [
                Services::LEAVE_CONFIG_SERVICE => new LeaveConfigurationService(),
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::DATETIME_HELPER_SERVICE => $dateTimeHelper,
            ]
        );

        $leaveEntitlement = $this->service->addEntitlementForEmployee(
            1,
            1,
            new DateTime('2021-01-01'),
            new DateTime('2021-12-31'),
            2.5
        );
        $this->assertEquals(5.5, $leaveEntitlement->getNoOfDays());
        $this->assertEquals(0, $leaveEntitlement->getDaysUsed());
        $this->assertEquals('2021-10-04', $leaveEntitlement->getCreditedDate()->format('Y-m-d'));
    }

    public function testAddEntitlementForEmployeeAsAddingWhenDeletedAlsoThere(): void
    {
        $this->loadFixtures();
        $user = $this->getEntityReference(User::class, 1);

        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUser'])
            ->getMock();
        $userRoleManager->expects($this->once())
            ->method('getUser')
            ->willReturn($user);
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->once())
            ->method('getNow')
            ->willReturn(new DateTime('2021-10-04'));
        $this->createKernelWithMockServices(
            [
                Services::LEAVE_CONFIG_SERVICE => new LeaveConfigurationService(),
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::DATETIME_HELPER_SERVICE => $dateTimeHelper,
            ]
        );

        $leaveEntitlement = $this->service->addEntitlementForEmployee(
            2,
            1,
            new DateTime('2020-01-01'),
            new DateTime('2020-12-31'),
            2.5
        );
        $this->assertEquals(7.5, $leaveEntitlement->getNoOfDays());
        $this->assertEquals(2, $leaveEntitlement->getDaysUsed());
        $this->assertEquals('2021-10-04', $leaveEntitlement->getCreditedDate()->format('Y-m-d'));
    }

    public function testBulkAssignLeaveEntitlements(): void
    {
        $this->loadFixtures();
        $user = $this->getEntityReference(User::class, 1);

        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUser'])
            ->getMock();
        $userRoleManager->expects($this->once())
            ->method('getUser')
            ->willReturn($user);
        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );

        $dao = $this->getMockBuilder(LeaveEntitlementDao::class)
            ->onlyMethods(['bulkAssignLeaveEntitlements'])
            ->getMock();
        $dao->expects($this->once())
            ->method('bulkAssignLeaveEntitlements')
            ->willReturnCallback(function (array $empNumbers, LeaveEntitlement $leaveEntitlement) {
                $entitlement = [];
                foreach ($empNumbers as $empNumber) {
                    $employee = new Employee();
                    $employee->setEmpNumber($empNumber);
                    $newLeaveEntitlement = clone $leaveEntitlement;
                    $newLeaveEntitlement->setEmployee($employee);
                    $entitlement[] = $newLeaveEntitlement;
                }
                return [$entitlement, count($entitlement)];
            });

        $this->service = $this->getMockBuilder(LeaveEntitlementService::class)
            ->onlyMethods(['getLeaveEntitlementDao'])
            ->getMock();
        $this->service->expects($this->once())
            ->method('getLeaveEntitlementDao')
            ->willReturn($dao);

        list($leaveEntitlements, $count) = $this->service->bulkAssignLeaveEntitlements(
            [1, 2],
            1,
            new DateTime('2020-01-01'),
            new DateTime('2020-12-31'),
            2.5
        );
        $this->assertEquals(2, $count);
        /** @var LeaveEntitlement $leaveEntitlement1 */
        $leaveEntitlement1 = $leaveEntitlements[0];
        $this->assertEquals(2.5, $leaveEntitlement1->getNoOfDays());
        $this->assertEquals(1, $leaveEntitlement1->getLeaveType()->getId());
        $this->assertEquals(1, $leaveEntitlement1->getEmployee()->getEmpNumber());
        $this->assertEquals('2020-01-01', $leaveEntitlement1->getDecorator()->getFromDate());
        $this->assertEquals('2020-12-31', $leaveEntitlement1->getDecorator()->getToDate());

        $leaveEntitlement2 = $leaveEntitlements[1];
        $this->assertEquals(2.5, $leaveEntitlement2->getNoOfDays());
        $this->assertEquals(1, $leaveEntitlement2->getLeaveType()->getId());
        $this->assertEquals(2, $leaveEntitlement2->getEmployee()->getEmpNumber());
        $this->assertEquals('2020-01-01', $leaveEntitlement2->getDecorator()->getFromDate());
        $this->assertEquals('2020-12-31', $leaveEntitlement2->getDecorator()->getToDate());
    }

    protected function loadFixtures(): void
    {
        TestDataService::truncateSpecificTables([UserRole::class, User::class]);
        $fixture = Config::get(Config::PLUGINS_DIR) .
            '/orangehrmLeavePlugin/test/fixtures/LeaveEntitlementService.yml';
        TestDataService::populate($fixture);
    }
}

class TestEntitlementConsumptionStrategy implements EntitlementConsumptionStrategy
{
    public function handleLeaveCancel($leave): CurrentAndChangeEntitlement
    {
        return new CurrentAndChangeEntitlement();
    }

    public function handleEntitlementStatusChange()
    {
    }

    /**
     * @inheritDoc
     */
    public function getLeaveWithoutEntitlementDateLimitsForLeaveBalance(
        DateTime $balanceStartDate,
        ?DateTime $balanceEndDate = null,
        ?int $empNumber = null,
        ?int $leaveTypeId = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getLeavePeriod(DateTime $date, ?int $empNumber = null, ?int $leaveTypeId = null): ?LeavePeriod
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function handleLeaveCreate(
        int $empNumber,
        int $leaveTypeId,
        array $leaveDates,
        bool $allowNoEntitlements = false
    ): ?CurrentAndChangeEntitlement {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function handleLeavePeriodChange(
        LeavePeriod $leavePeriodForToday,
        int $oldStartMonth,
        int $oldStartDay,
        int $newStartMonth,
        int $newStartDay
    ): void {
    }
}

class InvalidLeaveEntitlementConsumptionStrategy
{
}
