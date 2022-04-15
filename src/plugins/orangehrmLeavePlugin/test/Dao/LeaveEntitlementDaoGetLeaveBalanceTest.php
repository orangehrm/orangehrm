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
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Dao\LeaveEntitlementDao;
use OrangeHRM\Leave\Entitlement\FIFOEntitlementConsumptionStrategy;
use OrangeHRM\Leave\Entitlement\LeaveBalance;
use OrangeHRM\Leave\Service\LeaveConfigurationService;
use OrangeHRM\Leave\Service\LeaveEntitlementService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Leave
 * @group Dao
 */
class LeaveEntitlementDaoGetLeaveBalanceTest extends KernelTestCase
{
    /**
     * @var LeaveEntitlementDao
     */
    private LeaveEntitlementDao $dao;
    private string $fixture;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->dao = new LeaveEntitlementDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) .
            '/orangehrmLeavePlugin/test/fixtures/LeaveEntitlementGetLeaveBalanceDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetLeaveBalance(): void
    {
        $unlinkedDateLimits = [new DateTime('2001-01-01'), new DateTime('2020-01-01')];
        $mockStrategy = $this->getMockBuilder(FIFOEntitlementConsumptionStrategy::class)
            ->onlyMethods(['getLeaveWithoutEntitlementDateLimitsForLeaveBalance'])
            ->getMock();
        $mockStrategy->expects($this->exactly(31))
            ->method('getLeaveWithoutEntitlementDateLimitsForLeaveBalance')
            ->will($this->returnValue($unlinkedDateLimits));

        $leaveEntitlementService = $this->getMockBuilder(LeaveEntitlementService::class)
            ->onlyMethods(['getLeaveEntitlementStrategy'])
            ->getMock();
        $leaveEntitlementService->expects($this->exactly(31))
            ->method('getLeaveEntitlementStrategy')
            ->willReturn($mockStrategy);

        $this->createKernelWithMockServices(
            [
                Services::LEAVE_CONFIG_SERVICE => new LeaveConfigurationService(),
                Services::LEAVE_ENTITLEMENT_SERVICE => $leaveEntitlementService,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );

        // As at before entitlement start:
        $balance = $this->dao->getLeaveBalance(2, 6, new DateTime('2013-08-01'));
        $expected = new LeaveBalance(4, 1, 0, 0.5);
        $expected->setAsAtDate(new DateTime('2013-08-01'));
        $this->assertEquals($expected, $balance);

        // On Start Date
        $balance = $this->dao->getLeaveBalance(2, 6, new DateTime('2013-08-05'));
        $expected = new LeaveBalance(4, 1, 0, 0.5);
        $expected->setAsAtDate(new DateTime('2013-08-05'));
        $this->assertEquals($expected, $balance);

        // Between start end
        $balance = $this->dao->getLeaveBalance(2, 6, new DateTime('2013-08-10'));
        $expected = new LeaveBalance(4, 1, 0, 0.5);
        $expected->setAsAtDate(new DateTime('2013-08-10'));
        $this->assertEquals($expected, $balance);

        // On End date
        $balance = $this->dao->getLeaveBalance(2, 6, new DateTime('2013-09-01'));
        $expected = new LeaveBalance(4, 1, 0, 0.5);
        $expected->setAsAtDate(new DateTime('2013-09-01'));
        $this->assertEquals($expected, $balance);

        // After End
        $balance = $this->dao->getLeaveBalance(2, 6, new DateTime('2013-09-02'));
        $expected = new LeaveBalance(0, 0, 0, 0);
        $expected->setAsAtDate(new DateTime('2013-09-02'));
        $this->assertEquals($expected, $balance);

        // Using Date - Before
        $balance = $this->dao->getLeaveBalance(2, 6, new DateTime('2013-08-01'), new DateTime('2013-08-01'));
        $expected = new LeaveBalance(0, 0, 0, 0);
        $expected->setAsAtDate(new DateTime('2013-08-01'));
        $expected->setEndDate(new DateTime('2013-08-01'));
        $this->assertEquals($expected, $balance);

        // On Start Date
        $balance = $this->dao->getLeaveBalance(2, 6, new DateTime('2013-08-01'), new DateTime('2013-08-05'));
        $expected = new LeaveBalance(4, 1, 0, 0.5);
        $expected->setAsAtDate(new DateTime('2013-08-01'));
        $expected->setEndDate(new DateTime('2013-08-05'));
        $this->assertEquals($expected, $balance);

        // Between start end
        $balance = $this->dao->getLeaveBalance(2, 6, new DateTime('2013-08-01'), new DateTime('2013-08-10'));
        $expected = new LeaveBalance(4, 1, 0, 0.5);
        $expected->setAsAtDate(new DateTime('2013-08-01'));
        $expected->setEndDate(new DateTime('2013-08-10'));
        $this->assertEquals($expected, $balance);

        // On End date
        $balance = $this->dao->getLeaveBalance(2, 6, new DateTime('2013-08-01'), new DateTime('2013-09-01'));
        $expected = new LeaveBalance(4, 1, 0, 0.5);
        $expected->setAsAtDate(new DateTime('2013-08-01'));
        $expected->setEndDate(new DateTime('2013-09-01'));
        $this->assertEquals($expected, $balance);

        // After End
        $balance = $this->dao->getLeaveBalance(2, 6, new DateTime('2013-08-01'), new DateTime('2013-09-02'));
        $expected = new LeaveBalance(0, 0, 0, 0);
        $expected->setAsAtDate(new DateTime('2013-08-01'));
        $expected->setEndDate(new DateTime('2013-09-02'));
        $this->assertEquals($expected, $balance);

        // Two entitlements - before both
        $balance = $this->dao->getLeaveBalance(1, 2, new DateTime('2012-03-01'));
        $expected = new LeaveBalance(3, 0, 0, 0);
        $expected->setAsAtDate(new DateTime('2012-03-01'));
        $this->assertEquals($expected, $balance);

        // First day of one entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, new DateTime('2012-04-04'));
        $expected = new LeaveBalance(3, 0, 0, 0);
        $expected->setAsAtDate(new DateTime('2012-04-04'));
        $this->assertEquals($expected, $balance);

        // After first day of first entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, new DateTime('2012-05-01'));
        $expected = new LeaveBalance(3, 0, 0, 0);
        $expected->setAsAtDate(new DateTime('2012-05-01'));
        $this->assertEquals($expected, $balance);

        // First day of second entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, new DateTime('2012-05-05'));
        $expected = new LeaveBalance(3, 0, 0, 0);
        $expected->setAsAtDate(new DateTime('2012-05-05'));
        $this->assertEquals($expected, $balance);

        // After First day of second entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, new DateTime('2012-05-09'));
        $expected = new LeaveBalance(3, 0, 0, 0);
        $expected->setAsAtDate(new DateTime('2012-05-09'));
        $this->assertEquals($expected, $balance);

        // Last day of first entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, new DateTime('2012-06-01'));
        $expected = new LeaveBalance(3, 0, 0, 0);
        $expected->setAsAtDate(new DateTime('2012-06-01'));
        $this->assertEquals($expected, $balance);

        // After first entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, new DateTime('2012-06-02'));
        $expected = new LeaveBalance(2, 0, 0, 0);
        $expected->setAsAtDate(new DateTime('2012-06-02'));
        $this->assertEquals($expected, $balance);

        // On last day of second entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, new DateTime('2012-08-01'));
        $expected = new LeaveBalance(2, 0, 0, 0);
        $expected->setAsAtDate(new DateTime('2012-08-01'));
        $this->assertEquals($expected, $balance);

        // After second entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, new DateTime('2012-08-02'));
        $expected = new LeaveBalance(0, 0, 0, 0);
        $expected->setAsAtDate(new DateTime('2012-08-02'));
        $this->assertEquals($expected, $balance);

        // With date - before first entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, new DateTime('2012-03-01'), new DateTime('2012-03-01'));
        $expected = new LeaveBalance(0, 0, 0, 0);
        $expected->setAsAtDate(new DateTime('2012-03-01'));
        $expected->setEndDate(new DateTime('2012-03-01'));
        $this->assertEquals($expected, $balance);

        // on start date of first entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, new DateTime('2012-03-01'), new DateTime('2012-04-04'));
        $expected = new LeaveBalance(1, 0, 0, 0);
        $expected->setAsAtDate(new DateTime('2012-03-01'));
        $expected->setEndDate(new DateTime('2012-04-04'));
        $this->assertEquals($expected, $balance);

        // after first date of first entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, new DateTime('2012-03-01'), new DateTime('2012-05-01'));
        $expected = new LeaveBalance(1, 0, 0, 0);
        $expected->setAsAtDate(new DateTime('2012-03-01'));
        $expected->setEndDate(new DateTime('2012-05-01'));
        $this->assertEquals($expected, $balance);

        // on first date of second entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, new DateTime('2012-03-01'), new DateTime('2012-05-05'));
        $expected = new LeaveBalance(3, 0, 0, 0);
        $expected->setAsAtDate(new DateTime('2012-03-01'));
        $expected->setEndDate(new DateTime('2012-05-05'));
        $this->assertEquals($expected, $balance);

        // after first date of second entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, new DateTime('2012-03-01'), new DateTime('2012-05-09'));
        $expected = new LeaveBalance(3, 0, 0, 0);
        $expected->setAsAtDate(new DateTime('2012-03-01'));
        $expected->setEndDate(new DateTime('2012-05-09'));
        $this->assertEquals($expected, $balance);

        // on last date of first entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, new DateTime('2012-03-01'), new DateTime('2012-06-01'));
        $expected = new LeaveBalance(3, 0, 0, 0);
        $expected->setAsAtDate(new DateTime('2012-03-01'));
        $expected->setEndDate(new DateTime('2012-06-01'));
        $this->assertEquals($expected, $balance);

        // after last date of first entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, new DateTime('2012-03-01'), new DateTime('2012-06-02'));
        $expected = new LeaveBalance(2, 0, 0, 0);
        $expected->setAsAtDate(new DateTime('2012-03-01'));
        $expected->setEndDate(new DateTime('2012-06-02'));
        $this->assertEquals($expected, $balance);

        // last date of second entitlement
        $balance = $this->dao->getLeaveBalance(1, 2, new DateTime('2012-03-01'), new DateTime('2012-08-01'));
        $expected = new LeaveBalance(2, 0, 0, 0);
        $expected->setAsAtDate(new DateTime('2012-03-01'));
        $expected->setEndDate(new DateTime('2012-08-01'));
        $this->assertEquals($expected, $balance);

        // after both entitlements end dates
        $balance = $this->dao->getLeaveBalance(1, 2, new DateTime('2012-03-01'), new DateTime('2012-08-02'));
        $expected = new LeaveBalance(0, 0, 0, 0);
        $expected->setAsAtDate(new DateTime('2012-03-01'));
        $expected->setEndDate(new DateTime('2012-08-02'));
        $this->assertEquals($expected, $balance);

        // leave type with no leave entitlement
        $balance = $this->dao->getLeaveBalance(6, 7, new DateTime('2012-03-01'), new DateTime('2012-08-02'));
        $expected = new LeaveBalance(0, 0, 0, 0);
        $expected->setAsAtDate(new DateTime('2012-03-01'));
        $expected->setEndDate(new DateTime('2012-08-02'));
        $this->assertEquals($expected, $balance);

        // Checking values for scheduled and pending
        $balance = $this->dao->getLeaveBalance(1, 1, new DateTime('2012-03-01'), new DateTime('2012-07-02'));
        $expected = new LeaveBalance(3, 0, 0.75, 0.5);
        $expected->setAsAtDate(new DateTime('2012-03-01'));
        $expected->setEndDate(new DateTime('2012-07-02'));
        $this->assertEquals($expected, $balance);

        // No entitlements for employee
        $balance = $this->dao->getLeaveBalance(3, 1, new DateTime('2012-03-01'), new DateTime('2012-07-02'));
        $expected = new LeaveBalance(0, 0, 0, 0);
        $expected->setAsAtDate(new DateTime('2012-03-01'));
        $expected->setEndDate(new DateTime('2012-07-02'));
        $this->assertEquals($expected, $balance);
    }

    public function testGetLeaveBalanceWithUnlinkedLeave(): void
    {
        $unlinkedDateLimits = [new DateTime('2013-01-01'), new DateTime('2013-12-31')];
        $mockStrategy = $this->getMockBuilder(FIFOEntitlementConsumptionStrategy::class)
            ->onlyMethods(['getLeaveWithoutEntitlementDateLimitsForLeaveBalance'])
            ->getMock();
        $mockStrategy->expects($this->once())
            ->method('getLeaveWithoutEntitlementDateLimitsForLeaveBalance')
            ->with(new DateTime('2013-01-01'), new DateTime('2013-12-31'))
            ->will($this->returnValue($unlinkedDateLimits));

        $leaveEntitlementService = $this->getMockBuilder(LeaveEntitlementService::class)
            ->onlyMethods(['getLeaveEntitlementStrategy'])
            ->getMock();
        $leaveEntitlementService->expects($this->once())
            ->method('getLeaveEntitlementStrategy')
            ->willReturn($mockStrategy);

        $this->createKernelWithMockServices(
            [
                Services::LEAVE_CONFIG_SERVICE => new LeaveConfigurationService(),
                Services::LEAVE_ENTITLEMENT_SERVICE => $leaveEntitlementService,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );

        $balance = $this->dao->getLeaveBalance(7, 1, new DateTime('2013-01-01'), new DateTime('2013-12-31'));

        // $entitled = 0, $used = 0, $scheduled = 0, $pending = 0, $notLinked = 0, $taken = 0 ,$adjustment =0
        $expected = new LeaveBalance(5, 3, 3, 0, 0);
        $expected->setAsAtDate(new DateTime('2013-01-01'));
        $expected->setEndDate(new DateTime('2013-12-31'));
        $this->assertEquals($expected, $balance);
    }

    public function testGetLeaveBalanceExcludingUnlinkedLeaveWhenAfterPeriod(): void
    {
        $unlinkedDateLimits = [new DateTime('2013-01-01'), new DateTime('2014-12-31')];
        $mockStrategy = $this->getMockBuilder(FIFOEntitlementConsumptionStrategy::class)
            ->onlyMethods(['getLeaveWithoutEntitlementDateLimitsForLeaveBalance'])
            ->getMock();
        $mockStrategy->expects($this->once())
            ->method('getLeaveWithoutEntitlementDateLimitsForLeaveBalance')
            ->with(new DateTime('2013-01-01'), new DateTime('2013-12-31'))
            ->will($this->returnValue($unlinkedDateLimits));

        $leaveEntitlementService = $this->getMockBuilder(LeaveEntitlementService::class)
            ->onlyMethods(['getLeaveEntitlementStrategy'])
            ->getMock();
        $leaveEntitlementService->expects($this->once())
            ->method('getLeaveEntitlementStrategy')
            ->willReturn($mockStrategy);

        $this->createKernelWithMockServices(
            [
                Services::LEAVE_CONFIG_SERVICE => new LeaveConfigurationService(),
                Services::LEAVE_ENTITLEMENT_SERVICE => $leaveEntitlementService,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );

        $balance = $this->dao->getLeaveBalance(7, 1, new DateTime('2013-01-01'), new DateTime('2013-12-31'));

        // $entitled = 0, $used = 0, $scheduled = 0, $pending = 0, $notLinked = 0, $taken = 0 ,$adjustment =0
        $expected = new LeaveBalance(5, 5, 5, 0, 0);
        $expected->setAsAtDate(new DateTime('2013-01-01'));
        $expected->setEndDate(new DateTime('2013-12-31'));
        $this->assertEquals($expected, $balance);
    }

    public function testGetLeaveBalanceExcludingUnlinkedBeforePeriod(): void
    {
        $unlinkedDateLimits = [new DateTime('2012-01-01'), new DateTime('2013-12-31')];
        $mockStrategy = $this->getMockBuilder(FIFOEntitlementConsumptionStrategy::class)
            ->onlyMethods(['getLeaveWithoutEntitlementDateLimitsForLeaveBalance'])
            ->getMock();
        $mockStrategy->expects($this->once())
            ->method('getLeaveWithoutEntitlementDateLimitsForLeaveBalance')
            ->with(new DateTime('2013-01-01'), new DateTime('2013-12-31'))
            ->will($this->returnValue($unlinkedDateLimits));

        $leaveEntitlementService = $this->getMockBuilder(LeaveEntitlementService::class)
            ->onlyMethods(['getLeaveEntitlementStrategy'])
            ->getMock();
        $leaveEntitlementService->expects($this->once())
            ->method('getLeaveEntitlementStrategy')
            ->willReturn($mockStrategy);

        $this->createKernelWithMockServices(
            [
                Services::LEAVE_CONFIG_SERVICE => new LeaveConfigurationService(),
                Services::LEAVE_ENTITLEMENT_SERVICE => $leaveEntitlementService,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );
        $balance = $this->dao->getLeaveBalance(7, 1, new DateTime('2013-01-01'), new DateTime('2013-12-31'));

        // $entitled = 0, $used = 0, $scheduled = 0, $pending = 0, $notLinked = 0, $taken = 0 ,$adjustment =0
        $expected = new LeaveBalance(5, 4, 4, 0, 0);
        $expected->setAsAtDate(new DateTime('2013-01-01'));
        $expected->setEndDate(new DateTime('2013-12-31'));
        $this->assertEquals($expected, $balance);
    }

    public function testGetLeaveBalanceExcludingUnlinkedLeave(): void
    {
        $mockStrategy = $this->getMockBuilder(FIFOEntitlementConsumptionStrategy::class)
            ->onlyMethods(['getLeaveWithoutEntitlementDateLimitsForLeaveBalance'])
            ->getMock();
        $mockStrategy->expects($this->once())
            ->method('getLeaveWithoutEntitlementDateLimitsForLeaveBalance')
            ->with(new DateTime('2013-01-01'), new DateTime('2013-12-31'))
            ->will($this->returnValue(null));

        $leaveEntitlementService = $this->getMockBuilder(LeaveEntitlementService::class)
            ->onlyMethods(['getLeaveEntitlementStrategy'])
            ->getMock();
        $leaveEntitlementService->expects($this->once())
            ->method('getLeaveEntitlementStrategy')
            ->willReturn($mockStrategy);

        $this->createKernelWithMockServices(
            [
                Services::LEAVE_CONFIG_SERVICE => new LeaveConfigurationService(),
                Services::LEAVE_ENTITLEMENT_SERVICE => $leaveEntitlementService,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );
        $balance = $this->dao->getLeaveBalance(7, 1, new DateTime('2013-01-01'), new DateTime('2013-12-31'));

        // $entitled = 0, $used = 0, $scheduled = 0, $pending = 0, $notLinked = 0, $taken = 0 ,$adjustment =0
        $expected = new LeaveBalance(5, 2, 2, 0, 0);
        $expected->setAsAtDate(new DateTime('2013-01-01'));
        $expected->setEndDate(new DateTime('2013-12-31'));
        $this->assertEquals($expected, $balance);
    }
}
