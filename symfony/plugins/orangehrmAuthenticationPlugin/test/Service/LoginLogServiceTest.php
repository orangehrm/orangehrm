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

namespace OrangeHRM\Tests\Authentication\Service;

use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Authentication\Dao\LoginLogDao;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Authentication\Service\LoginService;
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

        $userService = $this->getMockBuilder(UserService::class)
            ->onlyMethods(['getCredentials'])
            ->getMock();

        $userService->expects($this->once())
            ->method('getCredentials')
            ->willReturn($user);

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
        $result = $loginService->addLogin($credentials);
        $this->assertTrue($result instanceof LoginLog);
        $this->assertEquals(1, $result->getUserId());
        $this->assertEquals('username', $result->getUserName());
        $this->assertEquals('Admin', $result->getUserRoleName());
        $this->assertEquals(0, $result->getUserRolePredefined());
    }
}
