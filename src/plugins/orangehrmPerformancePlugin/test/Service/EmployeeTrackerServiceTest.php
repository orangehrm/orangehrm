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

namespace OrangeHRM\Tests\Performance\Service;

use OrangeHRM\Config\Config;
use OrangeHRM\Entity\PerformanceTracker;
use OrangeHRM\Framework\Services;
use OrangeHRM\Performance\Dao\EmployeeTrackerDao;
use OrangeHRM\Performance\Dto\EmployeeTrackerSearchFilterParams;
use OrangeHRM\Performance\Service\EmployeeTrackerService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\Mock\MockAuthUser;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Performance
 * @group Service
 */
class EmployeeTrackerServiceTest extends KernelTestCase
{
    private EmployeeTrackerService $employeeTrackerService;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->employeeTrackerService = new EmployeeTrackerService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPerformancePlugin/test/fixtures/EmployeeTrackerService.yaml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmployeeTrackerDao(): void
    {
        $result = $this->employeeTrackerService->getEmployeeTrackerDao();
        $this->assertInstanceOf(EmployeeTrackerDao::class, $result);
    }

    public function testGetEmployeeTrackerList1(): void
    {
        // For Admin User Role
        $authUser = $this->getMockBuilder(MockAuthUser::class)
            ->onlyMethods(['getUserRoleId'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->once())
            ->method('getUserRoleId')
            ->willReturn(1);
        $this->createKernelWithMockServices([Services::AUTH_USER => $authUser]);

        $trackers = TestDataService::loadObjectList(PerformanceTracker::class, $this->fixture, 'PerformanceTracker');
        $expectedTrackerList = [$trackers[2], $trackers[0], $trackers[1]];

        $employeeTrackerSearchFilterParams = new EmployeeTrackerSearchFilterParams();
        $employeeTrackerDao = $this->getMockBuilder(EmployeeTrackerDao::class)
            ->onlyMethods(['getEmployeeTrackerListForAdmin'])
            ->getMock();
        $employeeTrackerDao->expects($this->once())
            ->method('getEmployeeTrackerListForAdmin')
            ->with($employeeTrackerSearchFilterParams)
            ->willReturn($expectedTrackerList);

        $employeeTrackerService = $this->getMockBuilder(EmployeeTrackerService::class)
            ->onlyMethods(['getEmployeeTrackerDao'])
            ->getMock();
        $employeeTrackerService->expects($this->once())
            ->method('getEmployeeTrackerDao')
            ->willReturn($employeeTrackerDao);

        $result = $employeeTrackerService->getEmployeeTrackerList($employeeTrackerSearchFilterParams);
        $this->assertEquals($expectedTrackerList, $result);
    }

    public function testGetEmployeeTrackerList2(): void
    {
        // For ESS User Role
        $authUser = $this->getMockBuilder(MockAuthUser::class)
            ->onlyMethods(['getUserRoleId', 'getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->once())
            ->method('getUserRoleId')
            ->willReturn(2);
        $authUser->expects($this->once())
            ->method('getEmpNumber')
            ->willReturn(2);
        $this->createKernelWithMockServices([Services::AUTH_USER => $authUser]);

        $trackers = TestDataService::loadObjectList(PerformanceTracker::class, $this->fixture, 'PerformanceTracker');
        $expectedTrackerList = [$trackers[2], $trackers[1]];

        $employeeTrackerSearchFilterParams = new EmployeeTrackerSearchFilterParams();
        $employeeTrackerDao = $this->getMockBuilder(EmployeeTrackerDao::class)
            ->onlyMethods(['getEmployeeTrackerListForESS'])
            ->getMock();
        $employeeTrackerDao->expects($this->once())
            ->method('getEmployeeTrackerListForESS')
            ->with($employeeTrackerSearchFilterParams)
            ->willReturn($expectedTrackerList);

        $employeeTrackerService = $this->getMockBuilder(EmployeeTrackerService::class)
            ->onlyMethods(['getEmployeeTrackerDao'])
            ->getMock();
        $employeeTrackerService->expects($this->once())
            ->method('getEmployeeTrackerDao')
            ->willReturn($employeeTrackerDao);

        $result = $employeeTrackerService->getEmployeeTrackerList($employeeTrackerSearchFilterParams);
        $this->assertEquals($expectedTrackerList, $result);
    }

    public function testGetEmployeeTrackerCount1(): void
    {
        // For Admin User Role
        $authUser = $this->getMockBuilder(MockAuthUser::class)
            ->onlyMethods(['getUserRoleId'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->once())
            ->method('getUserRoleId')
            ->willReturn(1);
        $this->createKernelWithMockServices([Services::AUTH_USER => $authUser]);

        $employeeTrackerSearchFilterParams = new EmployeeTrackerSearchFilterParams();
        $employeeTrackerDao = $this->getMockBuilder(EmployeeTrackerDao::class)
            ->onlyMethods(['getEmployeeTrackerCountForAdmin'])
            ->getMock();
        $employeeTrackerDao->expects($this->once())
            ->method('getEmployeeTrackerCountForAdmin')
            ->with($employeeTrackerSearchFilterParams)
            ->willReturn(3);

        $employeeTrackerService = $this->getMockBuilder(EmployeeTrackerService::class)
            ->onlyMethods(['getEmployeeTrackerDao'])
            ->getMock();
        $employeeTrackerService->expects($this->once())
            ->method('getEmployeeTrackerDao')
            ->willReturn($employeeTrackerDao);

        $result = $employeeTrackerService->getEmployeeTrackerCount($employeeTrackerSearchFilterParams);
        $this->assertEquals(3, $result);
    }

    public function testGetEmployeeTrackerCount2(): void
    {
        // For ESS User Role
        $authUser = $this->getMockBuilder(MockAuthUser::class)
            ->onlyMethods(['getUserRoleId', 'getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->once())
            ->method('getUserRoleId')
            ->willReturn(2);
        $authUser->expects($this->once())
            ->method('getEmpNumber')
            ->willReturn(2);
        $this->createKernelWithMockServices([Services::AUTH_USER => $authUser]);

        $employeeTrackerSearchFilterParams = new EmployeeTrackerSearchFilterParams();
        $employeeTrackerDao = $this->getMockBuilder(EmployeeTrackerDao::class)
            ->onlyMethods(['getEmployeeTrackerCountForESS'])
            ->getMock();
        $employeeTrackerDao->expects($this->once())
            ->method('getEmployeeTrackerCountForESS')
            ->with($employeeTrackerSearchFilterParams)
            ->willReturn(2);

        $employeeTrackerService = $this->getMockBuilder(EmployeeTrackerService::class)
            ->onlyMethods(['getEmployeeTrackerDao'])
            ->getMock();
        $employeeTrackerService->expects($this->once())
            ->method('getEmployeeTrackerDao')
            ->willReturn($employeeTrackerDao);

        $result = $employeeTrackerService->getEmployeeTrackerCount($employeeTrackerSearchFilterParams);
        $this->assertEquals(2, $result);
    }
}
