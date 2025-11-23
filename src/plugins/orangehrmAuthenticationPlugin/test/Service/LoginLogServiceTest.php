<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Tests\Authentication\Service;

use DateTime;
use OrangeHRM\Admin\Dao\UserDao;
use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Authentication\Dao\LoginLogDao;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Authentication\Service\LoginService;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\LoginLog;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\UserRole;
use OrangeHRM\Framework\Http\Session\Session;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\KernelTestCase;

/**
 * @group Authentication
 * @group Service
 */
class LoginLogServiceTest extends KernelTestCase
{
    /**
     * @var LoginService
     */
    private LoginService $loginService;

    protected function setUp(): void
    {
        $this->loginService = new LoginService();
    }

    public function testGetLoginLogDao(): void
    {
        $this->assertTrue($this->loginService->getLoginLogDao() instanceof LoginLogDao);
    }

    public function testSaveLoginLog(): void
    {
        $userRole = new UserRole();
        $userRole->setId(1);
        $userRole->setName('Admin');

        $employee = new Employee();
        $employee->setEmpNumber(1);

        $user = new User();
        $user->setId(1);
        $user->setUserRole($userRole);
        $user->setUserName('username');
        $user->setEmployee($employee);

        $userDao = $this->getMockBuilder(UserDao::class)
            ->onlyMethods(['getUserByUserName'])
            ->getMock();
        $userDao->expects($this->once())
            ->method('getUserByUserName')
            ->willReturn($user);

        $userService = $this->getMockBuilder(UserService::class)
            ->onlyMethods(['geUserDao'])
            ->getMock();

        $userService->expects($this->once())
            ->method('geUserDao')
            ->willReturn($userDao);

        $loginService = $this->getMockBuilder(LoginService::class)
            ->onlyMethods(['getUserService'])
            ->getMock();

        $session = $this->getMockBuilder(Session::class)
            ->onlyMethods(['set'])
            ->getMock();
        $session->expects($this->exactly(0))
            ->method('set');

        $this->createKernelWithMockServices([Services::SESSION => $session]);

        $loginService->expects($this->once())
            ->method('getUserService')
            ->willReturn($userService);

        $credentials = new UserCredential('username', 'password');

        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->atLeastOnce())
            ->method('getNow')
            ->willReturn(new DateTime('2022-07-04 10:56:56'));
        $this->getContainer()->set(Services::DATETIME_HELPER_SERVICE, $dateTimeHelper);

        $result = $loginService->addLogin($credentials);
        $this->assertTrue($result instanceof LoginLog);
        $this->assertEquals(1, $result->getUserId());
        $this->assertEquals('username', $result->getUserName());
        $this->assertEquals('Admin', $result->getUserRoleName());
        $this->assertEquals(0, $result->getUserRolePredefined());
        $this->assertEquals('2022-07-04 10:56', $result->getLoginTime()->format('Y-m-d H:i'));
    }

    public function testAddOIDCLogin(): void
    {
        $userRole = new UserRole();
        $userRole->setId(1);
        $userRole->setName('Admin');

        $employee = new Employee();
        $employee->setEmpNumber(1);

        $user = new User();
        $user->setId(1);
        $user->setUserRole($userRole);
        $user->setUserName('manul@orangehrm.us.com');
        $user->setEmployee($employee);

        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->atLeastOnce())
            ->method('getNow')
            ->willReturn(new DateTime('2024-01-17 10:56:56'));
        $this->getContainer()->set(Services::DATETIME_HELPER_SERVICE, $dateTimeHelper);

        $result = $this->loginService->addOIDCLogin($user);
        $this->assertEquals(1, $result->getUserId());
        $this->assertEquals('manul@orangehrm.us.com', $result->getUserName());
        $this->assertEquals('Admin', $result->getUserRoleName());
        $this->assertEquals(0, $result->getUserRolePredefined());
        $this->assertEquals('2024-01-17 10:56', $result->getLoginTime()->format('Y-m-d H:i'));
        $this->assertTrue($result instanceof LoginLog);
    }
}
