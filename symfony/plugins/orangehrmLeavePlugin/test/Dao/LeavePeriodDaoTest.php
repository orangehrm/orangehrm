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
use OrangeHRM\Entity\LeavePeriodHistory;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Dao\LeavePeriodDao;
use OrangeHRM\Leave\Dto\LeavePeriod;
use OrangeHRM\Leave\Entitlement\FIFOEntitlementConsumptionStrategy;
use OrangeHRM\Leave\Service\LeaveConfigurationService;
use OrangeHRM\Leave\Service\LeaveEntitlementService;
use OrangeHRM\Leave\Service\LeavePeriodService;
use OrangeHRM\Tests\Util\KernelTestCase;
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
            ]
        );
        $result = $this->leavePeriodDao->saveLeavePeriodHistory($leavePeriodHistory);
        $this->assertEquals(1, $result->getStartMonth());
        $this->assertEquals(1, $result->getStartDay());
        $this->assertEquals('2021-01-01', $result->getCreatedAt()->format('Y-m-d'));
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
}
