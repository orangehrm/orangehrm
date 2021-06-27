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

namespace OrangeHRM\Core\test\Service;

use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Core\Authorization\Service\HomePageService;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\KernelTestCase;

/**
 * @group Core
 * @group Service
 */
class HomePageServiceTest extends KernelTestCase
{
    public function testGetHomePagePath(): void
    {
        $homepage = 'dashboard/index';
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->onlyMethods(['getHomePage'])
            ->getMock();
        $userRoleManager->expects($this->once())
            ->method('getHomePage')
            ->willReturn($homepage);

        $this->createKernelWithMockServices([Services::USER_ROLE_MANAGER => $userRoleManager]);
        $service = new HomePageService();

        $this->assertEquals($homepage, $service->getHomePagePath());
    }

    public function testGetModuleDefaultPage(): void
    {
        $defaultPage = 'admin/viewSystemUsers';
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->onlyMethods(['getModuleDefaultPage'])
            ->getMock();
        $userRoleManager->expects($this->once())
            ->method('getModuleDefaultPage')
            ->willReturn($defaultPage);

        $this->createKernelWithMockServices([Services::USER_ROLE_MANAGER => $userRoleManager]);
        $service = new HomePageService();

        $this->assertEquals($defaultPage, $service->getModuleDefaultPage('admin'));
    }

    public function testGetTimeModuleDefaultPath(): void
    {
        $defaultPage = 'time/viewEmployeeTimesheet';
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->onlyMethods(['getModuleDefaultPage'])
            ->getMock();
        $userRoleManager->expects($this->once())
            ->method('getModuleDefaultPage')
            ->willReturn($defaultPage);

        $this->createKernelWithMockServices([Services::USER_ROLE_MANAGER => $userRoleManager]);
        $service = new HomePageService();

        $this->assertEquals($defaultPage, $service->getTimeModuleDefaultPath());
    }

    public function testGetLeaveModuleDefaultPath(): void
    {
        $defaultPage = 'leave/viewMyLeaveList';
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->onlyMethods(['getModuleDefaultPage'])
            ->getMock();
        $userRoleManager->expects($this->once())
            ->method('getModuleDefaultPage')
            ->willReturn($defaultPage);

        $this->createKernelWithMockServices([Services::USER_ROLE_MANAGER => $userRoleManager]);
        $service = new HomePageService();

        $this->assertEquals($defaultPage, $service->getLeaveModuleDefaultPath());
    }

    public function testGetAdminModuleDefaultPath(): void
    {
        $defaultPage = 'admin/viewSystemUsers';
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->onlyMethods(['getModuleDefaultPage'])
            ->getMock();
        $userRoleManager->expects($this->once())
            ->method('getModuleDefaultPage')
            ->willReturn($defaultPage);

        $this->createKernelWithMockServices([Services::USER_ROLE_MANAGER => $userRoleManager]);
        $service = new HomePageService();

        $this->assertEquals($defaultPage, $service->getAdminModuleDefaultPath());
    }

    public function testGetPimModuleDefaultPath(): void
    {
        $defaultPage = 'pim/viewEmployeeList';
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->onlyMethods(['getModuleDefaultPage'])
            ->getMock();
        $userRoleManager->expects($this->once())
            ->method('getModuleDefaultPage')
            ->willReturn($defaultPage);

        $this->createKernelWithMockServices([Services::USER_ROLE_MANAGER => $userRoleManager]);
        $service = new HomePageService();

        $this->assertEquals($defaultPage, $service->getPimModuleDefaultPath());
    }

    public function testGetRecruitmentModuleDefaultPath(): void
    {
        $defaultPage = 'recruitment/viewCandidates';
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->onlyMethods(['getModuleDefaultPage'])
            ->getMock();
        $userRoleManager->expects($this->once())
            ->method('getModuleDefaultPage')
            ->willReturn($defaultPage);

        $this->createKernelWithMockServices([Services::USER_ROLE_MANAGER => $userRoleManager]);
        $service = new HomePageService();

        $this->assertEquals($defaultPage, $service->getRecruitmentModuleDefaultPath());
    }

    public function testGetPerformanceModuleDefaultPath(): void
    {
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->onlyMethods(['getModuleDefaultPage'])
            ->getMock();
        $userRoleManager->expects($this->once())
            ->method('getModuleDefaultPage')
            ->willReturn(null);

        $this->createKernelWithMockServices([Services::USER_ROLE_MANAGER => $userRoleManager]);
        $service = new HomePageService();

        $this->assertNull($service->getPerformanceModuleDefaultPath());
    }
}
