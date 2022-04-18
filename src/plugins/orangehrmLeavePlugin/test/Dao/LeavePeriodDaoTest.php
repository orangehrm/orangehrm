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

namespace OrangeHRM\Tests\Leave\Dao;

use DateTime;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Exception\CoreServiceException;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\LeaveEntitlement;
use OrangeHRM\Entity\LeavePeriodHistory;
use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Dao\LeavePeriodDao;
use OrangeHRM\Leave\Dto\LeavePeriod;
use OrangeHRM\Leave\Entitlement\FIFOEntitlementConsumptionStrategy;
use OrangeHRM\Leave\Service\LeaveConfigurationService;
use OrangeHRM\Leave\Service\LeaveEntitlementService;
use OrangeHRM\Leave\Service\LeavePeriodService;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\Mock\MockUserRoleManager;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Leave
 * @group Dao
 */
class LeavePeriodDaoTest extends KernelTestCase
{
    /**
     * @var LeavePeriodDao
     */
    private LeavePeriodDao $leavePeriodDao;

    protected function setUp(): void
    {
        $this->leavePeriodDao = new LeavePeriodDao();
        TestDataService::populate(
            Config::get(Config::PLUGINS_DIR) . '/orangehrmLeavePlugin/test/fixtures/LeavePeriodDao.yml'
        );
    }

    public function testSaveLeavePeriodHistoryFirstTime(): void
    {
        TestDataService::truncateSpecificTables([LeavePeriodHistory::class]);
        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setStartMonth(1);
        $leavePeriodHistory->setStartDay(1);
        $leavePeriodHistory->setCreatedAt(new DateTime('2012-01-01'));

        $this->leavePeriodDao = $this->getMockBuilder(LeavePeriodDao::class)
            ->onlyMethods(['getCurrentLeavePeriodStartDateAndMonth'])
            ->getMock();
        $this->leavePeriodDao->expects($this->once())
            ->method('getCurrentLeavePeriodStartDateAndMonth')
            ->willReturn(null);

        $leaveEntitlementService = $this->getMockBuilder(LeaveEntitlementService::class)
            ->onlyMethods(['getLeaveEntitlementStrategy'])
            ->getMock();
        $leaveEntitlementService->expects($this->never())
            ->method('getLeaveEntitlementStrategy');

        $leaveConfigService = $this->getMockBuilder(LeaveConfigurationService::class)
            ->onlyMethods(['isLeavePeriodDefined', 'setLeavePeriodDefined'])
            ->getMock();
        $leaveConfigService->expects($this->once())
            ->method('isLeavePeriodDefined')
            ->willReturn(false);
        $leaveConfigService->expects($this->once())
            ->method('setLeavePeriodDefined');

        $leavePeriodService = $this->getMockBuilder(LeavePeriodService::class)
            ->onlyMethods(['getCurrentLeavePeriodByDate'])
            ->getMock();
        $leavePeriodService->expects($this->never())
            ->method('getCurrentLeavePeriodByDate');

        $this->createKernelWithMockServices(
            [
                Services::LEAVE_CONFIG_SERVICE => $leaveConfigService,
                Services::LEAVE_ENTITLEMENT_SERVICE => $leaveEntitlementService,
                Services::LEAVE_PERIOD_SERVICE => $leavePeriodService,
            ]
        );
        $result = $this->leavePeriodDao->saveLeavePeriodHistory($leavePeriodHistory);
        $this->assertEquals(1, $result->getStartMonth());
        $this->assertEquals(1, $result->getStartDay());
        $this->assertEquals('2012-01-01', $result->getCreatedAt()->format('Y-m-d'));
    }

    public function testSaveLeavePeriodHistory(): void
    {
        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setStartMonth(1);
        $leavePeriodHistory->setStartDay(1);
        $leavePeriodHistory->setCreatedAt(new DateTime('2021-01-01'));

        $currentLeavePeriod = new LeavePeriod(new DateTime('2020-01-01'), new DateTime('2020-12-31'));
        $mockStrategy = $this->getMockBuilder(FIFOEntitlementConsumptionStrategy::class)
            ->onlyMethods(['handleLeavePeriodChange'])
            ->getMock();
        $mockStrategy->expects($this->once())
            ->method('handleLeavePeriodChange')
            ->with($currentLeavePeriod, 1, 3, 1, 1);

        $leaveEntitlementService = $this->getMockBuilder(LeaveEntitlementService::class)
            ->onlyMethods(['getLeaveEntitlementStrategy'])
            ->getMock();
        $leaveEntitlementService->expects($this->once())
            ->method('getLeaveEntitlementStrategy')
            ->willReturn($mockStrategy);

        $leaveConfigService = $this->getMockBuilder(LeaveConfigurationService::class)
            ->onlyMethods(['isLeavePeriodDefined', 'setLeavePeriodDefined'])
            ->getMock();
        $leaveConfigService->expects($this->once())
            ->method('isLeavePeriodDefined')
            ->willReturn(true);
        $leaveConfigService->expects($this->never())
            ->method('setLeavePeriodDefined');

        $leavePeriodService = $this->getMockBuilder(LeavePeriodService::class)
            ->onlyMethods(['getCurrentLeavePeriodByDate'])
            ->getMock();
        $leavePeriodService->expects($this->once())
            ->method('getCurrentLeavePeriodByDate')
            ->willReturn($currentLeavePeriod);

        $this->createKernelWithMockServices(
            [
                Services::LEAVE_CONFIG_SERVICE => $leaveConfigService,
                Services::LEAVE_ENTITLEMENT_SERVICE => $leaveEntitlementService,
                Services::LEAVE_PERIOD_SERVICE => $leavePeriodService,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );
        $result = $this->leavePeriodDao->saveLeavePeriodHistory($leavePeriodHistory);
        $this->assertEquals(1, $result->getStartMonth());
        $this->assertEquals(1, $result->getStartDay());
        $this->assertEquals('2021-01-01', $result->getCreatedAt()->format('Y-m-d'));
    }

    public function testSaveLeavePeriodHistoryWithTransactionException(): void
    {
        $total = $this->getEntityManager()->getRepository(LeavePeriodHistory::class)->count([]);

        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setStartMonth(1);
        $leavePeriodHistory->setStartDay(1);
        $leavePeriodHistory->setCreatedAt(new DateTime('2021-01-01'));

        $mockStrategy = $this->getMockBuilder(FIFOEntitlementConsumptionStrategy::class)
            ->onlyMethods(['handleLeavePeriodChange'])
            ->getMock();
        $mockStrategy->expects($this->never())
            ->method('handleLeavePeriodChange');

        $leaveEntitlementService = $this->getMockBuilder(LeaveEntitlementService::class)
            ->onlyMethods(['getLeaveEntitlementStrategy'])
            ->getMock();
        $leaveEntitlementService->expects($this->never())
            ->method('getLeaveEntitlementStrategy');

        $leaveConfigService = $this->getMockBuilder(LeaveConfigurationService::class)
            ->onlyMethods(['isLeavePeriodDefined', 'setLeavePeriodDefined'])
            ->getMock();
        $leaveConfigService->expects($this->once())
            ->method('isLeavePeriodDefined')
            ->willReturnCallback(function () {
                throw new CoreServiceException();
            });
        $leaveConfigService->expects($this->never())
            ->method('setLeavePeriodDefined');

        $leavePeriodService = $this->getMockBuilder(LeavePeriodService::class)
            ->onlyMethods(['getCurrentLeavePeriodByDate'])
            ->getMock();
        $leavePeriodService->expects($this->never())
            ->method('getCurrentLeavePeriodByDate');

        $this->createKernelWithMockServices(
            [
                Services::LEAVE_CONFIG_SERVICE => $leaveConfigService,
                Services::LEAVE_ENTITLEMENT_SERVICE => $leaveEntitlementService,
                Services::LEAVE_PERIOD_SERVICE => $leavePeriodService,
            ]
        );
        $this->expectException(TransactionException::class);
        $this->leavePeriodDao->saveLeavePeriodHistory($leavePeriodHistory);
        $this->assertEquals($total, $this->getEntityManager()->getRepository(LeavePeriodHistory::class)->count([]));
    }

    public function testGetCurrentLeavePeriodStartDateAndMonth(): void
    {
        $result = $this->leavePeriodDao->getCurrentLeavePeriodStartDateAndMonth();
        $this->assertEquals(1, $result->getStartMonth());
        $this->assertEquals(3, $result->getStartDay());
        $this->assertEquals('2012-01-02', $result->getCreatedAt()->format('Y-m-d'));
    }

    public function testGetLeavePeriodHistoryList(): void
    {
        $result = $this->leavePeriodDao->getLeavePeriodHistoryList();
        $this->assertEquals(1, $result[0]->getStartMonth());
        $this->assertEquals(4, $result[0]->getStartDay());
        $this->assertEquals('2012-01-01', $result[0]->getCreatedAt()->format('Y-m-d'));

        $this->assertEquals(1, $result[1]->getStartMonth());
        $this->assertEquals(1, $result[1]->getStartDay());
        $this->assertEquals('2012-01-02', $result[1]->getCreatedAt()->format('Y-m-d'));

        $this->assertEquals(1, $result[2]->getStartMonth());
        $this->assertEquals(2, $result[2]->getStartDay());
        $this->assertEquals('2012-01-02', $result[2]->getCreatedAt()->format('Y-m-d'));

        $this->assertCount(4, $result);
    }

    public function testEntitlementChangeWhenLeavePeriodChanged(): void
    {
        TestDataService::truncateSpecificTables([LeaveEntitlement::class]);
        $userRoleManager = $this->getMockBuilder(MockUserRoleManager::class)
            ->onlyMethods(['getUser'])
            ->getMock();
        $userRoleManager->method('getUser')
            ->willReturn($this->getEntityReference(User::class, 1));
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->method('getNow')
            ->willReturnCallback(fn () => new DateTime('2020-10-04'));
        $this->createKernelWithMockServices([
            Services::DATETIME_HELPER_SERVICE => $dateTimeHelper,
            Services::USER_ROLE_MANAGER => $userRoleManager,
            Services::LEAVE_CONFIG_SERVICE => new LeaveConfigurationService(),
            Services::LEAVE_PERIOD_SERVICE => new LeavePeriodService(),
            Services::LEAVE_ENTITLEMENT_SERVICE => new LeaveEntitlementService(),
        ]);

        $leaveEntitlementService = new LeaveEntitlementService();
        $leaveEntitlementService->addEntitlementForEmployee(
            1,
            1,
            new DateTime('2011-01-04'),
            new DateTime('2012-01-02'),
            10.5
        );
        $leaveEntitlementService->addEntitlementForEmployee(
            1,
            2,
            new DateTime('2011-01-04'),
            new DateTime('2012-01-02'),
            20
        );
        $leaveEntitlementService->addEntitlementForEmployee(
            2,
            3,
            new DateTime('2012-01-03'),
            new DateTime('2013-01-02'),
            50
        );
        $leaveEntitlementService->addEntitlementForEmployee(
            3,
            4,
            new DateTime('2020-01-03'),
            new DateTime('2021-01-02'),
            7
        );
        $leaveEntitlementService->addEntitlementForEmployee(
            3,
            4,
            new DateTime('2021-01-03'),
            new DateTime('2022-01-02'),
            7
        );

        /**
         * Verify entitlement dates before change leave period
         */
        $leaveEntitlement = $this->getEntityManager()
            ->getRepository(LeaveEntitlement::class)
            ->findOneBy(['employee' => 1, 'leaveType' => 1]);
        $this->assertEquals('2011-01-04', $leaveEntitlement->getFromDate()->format('Y-m-d'));
        $this->assertEquals('2012-01-02', $leaveEntitlement->getToDate()->format('Y-m-d'));

        $leaveEntitlement = $this->getEntityManager()
            ->getRepository(LeaveEntitlement::class)
            ->findOneBy(['employee' => 1, 'leaveType' => 2]);
        $this->assertEquals('2011-01-04', $leaveEntitlement->getFromDate()->format('Y-m-d'));
        $this->assertEquals('2012-01-02', $leaveEntitlement->getToDate()->format('Y-m-d'));

        $leaveEntitlement = $this->getEntityManager()
            ->getRepository(LeaveEntitlement::class)
            ->findOneBy(['employee' => 2, 'leaveType' => 3]);
        $this->assertEquals('2012-01-03', $leaveEntitlement->getFromDate()->format('Y-m-d'));
        $this->assertEquals('2013-01-02', $leaveEntitlement->getToDate()->format('Y-m-d'));

        $leaveEntitlements = $this->getEntityManager()
            ->getRepository(LeaveEntitlement::class)
            ->findBy(['employee' => 3, 'leaveType' => 4]);
        $this->assertEquals('2020-01-03', $leaveEntitlements[0]->getFromDate()->format('Y-m-d'));
        $this->assertEquals('2021-01-02', $leaveEntitlements[0]->getToDate()->format('Y-m-d'));
        $this->assertEquals('2021-01-03', $leaveEntitlements[1]->getFromDate()->format('Y-m-d'));
        $this->assertEquals('2022-01-02', $leaveEntitlements[1]->getToDate()->format('Y-m-d'));

        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setStartMonth(3);
        $leavePeriodHistory->setStartDay(1);
        $leavePeriodHistory->setCreatedAt(new DateTime('2020-10-04'));
        $this->leavePeriodDao->saveLeavePeriodHistory($leavePeriodHistory);

        $this->getEntityManager()->clear();
        /**
         * Verify entitlement dates after change leave period
         */
        // Check for old leave periods
        $leaveEntitlement = $this->getEntityManager()
            ->getRepository(LeaveEntitlement::class)
            ->findOneBy(['employee' => 1, 'leaveType' => 1]);
        $this->assertEquals('2011-01-04', $leaveEntitlement->getFromDate()->format('Y-m-d'));
        $this->assertEquals('2012-01-02', $leaveEntitlement->getToDate()->format('Y-m-d'));

        $leaveEntitlement = $this->getEntityManager()
            ->getRepository(LeaveEntitlement::class)
            ->findOneBy(['employee' => 1, 'leaveType' => 2]);
        $this->assertEquals('2011-01-04', $leaveEntitlement->getFromDate()->format('Y-m-d'));
        $this->assertEquals('2012-01-02', $leaveEntitlement->getToDate()->format('Y-m-d'));

        $leaveEntitlement = $this->getEntityManager()
            ->getRepository(LeaveEntitlement::class)
            ->findOneBy(['employee' => 2, 'leaveType' => 3]);
        $this->assertEquals('2012-01-03', $leaveEntitlement->getFromDate()->format('Y-m-d'));
        $this->assertEquals('2013-01-02', $leaveEntitlement->getToDate()->format('Y-m-d'));

        $leaveEntitlements = $this->getEntityManager()
            ->getRepository(LeaveEntitlement::class)
            ->findBy(['employee' => 3, 'leaveType' => 4]);
        // Check for current leave periods
        $this->assertEquals('2020-01-03', $leaveEntitlements[0]->getFromDate()->format('Y-m-d'));
        $this->assertEquals('2021-02-28', $leaveEntitlements[0]->getToDate()->format('Y-m-d'));
        // Check for future leave periods
        $this->assertEquals('2021-03-01', $leaveEntitlements[1]->getFromDate()->format('Y-m-d'));
        $this->assertEquals('2022-02-28', $leaveEntitlements[1]->getToDate()->format('Y-m-d'));
    }
}
