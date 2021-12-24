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
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Authentication\Exception\AuthenticationException;
use OrangeHRM\Authentication\Service\AuthenticationService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeTerminationRecord;
use OrangeHRM\Entity\TerminationReason;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\UserRole;
use OrangeHRM\Framework\Http\Session\Session;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\KernelTestCase;

/**
 * @group Pim
 * @group Service
 */
class AuthenticationServiceTest extends KernelTestCase
{
    public function testsSetCredentialsWithInvalidCredentials(): void
    {
        $userService = $this->getMockBuilder(UserService::class)
            ->onlyMethods(['getCredentials'])
            ->getMock();
        $userService->expects($this->once())
            ->method('getCredentials')
            ->willReturn(null);

        $authenticationService = $this->getMockBuilder(AuthenticationService::class)
            ->onlyMethods(['getUserService'])
            ->getMock();

        $authenticationService->expects($this->once())
            ->method('getUserService')
            ->willReturn($userService);

        $credentials = new UserCredential('username', 'password');
        $result = $authenticationService->setCredentials($credentials, []);
        $this->assertFalse($result);
    }

    public function testsSetCredentialsWithNotAssignedEss(): void
    {
        $userRole = new UserRole();
        $userRole->setId(2);
        $userRole->setName('ESS');

        $user = new User();
        $user->setId(1);
        $user->setUserRole($userRole);
        $user->setUserName('username');

        $userService = $this->getMockBuilder(UserService::class)
            ->onlyMethods(['getCredentials'])
            ->getMock();
        $userService->expects($this->once())
            ->method('getCredentials')
            ->willReturn($user);

        $authenticationService = $this->getMockBuilder(AuthenticationService::class)
            ->onlyMethods(['getUserService'])
            ->getMock();

        $authenticationService->expects($this->once())
            ->method('getUserService')
            ->willReturn($userService);

        $credentials = new UserCredential('username', 'password');
        try {
            $authenticationService->setCredentials($credentials, []);
        } catch (AuthenticationException $e) {
            $this->assertEquals(
                ['error' => AuthenticationException::EMPLOYEE_NOT_ASSIGNED, 'message' => 'Employee not assigned'],
                $e->normalize()
            );
        }
    }

    public function testsSetCredentialsWithDisabledUser(): void
    {
        $userRole = new UserRole();
        $userRole->setId(1);
        $userRole->setName('Admin');

        $user = new User();
        $user->setId(1);
        $user->setUserRole($userRole);
        $user->setUserName('username');
        $user->setStatus(false);

        $userService = $this->getMockBuilder(UserService::class)
            ->onlyMethods(['getCredentials'])
            ->getMock();
        $userService->expects($this->once())
            ->method('getCredentials')
            ->willReturn($user);

        $authenticationService = $this->getMockBuilder(AuthenticationService::class)
            ->onlyMethods(['getUserService'])
            ->getMock();

        $authenticationService->expects($this->once())
            ->method('getUserService')
            ->willReturn($userService);

        $credentials = new UserCredential('username', 'password');
        try {
            $authenticationService->setCredentials($credentials, []);
        } catch (AuthenticationException $e) {
            $this->assertEquals(
                ['error' => AuthenticationException::USER_DISABLED, 'message' => 'Account disabled'],
                $e->normalize()
            );
        }
    }

    public function testsSetCredentialsWithTerminatedEmployee(): void
    {
        $userRole = new UserRole();
        $userRole->setId(1);
        $userRole->setName('Admin');

        $terminationReason = new TerminationReason();
        $terminationReason->setId(1);
        $terminationReason->setName('Other');

        $termination = new EmployeeTerminationRecord();
        $termination->setId(1);
        $termination->setTerminationReason($terminationReason);

        $employee = new Employee();
        $employee->setEmpNumber(1);
        $employee->setEmployeeTerminationRecord($termination);

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

        $authenticationService = $this->getMockBuilder(AuthenticationService::class)
            ->onlyMethods(['getUserService'])
            ->getMock();

        $authenticationService->expects($this->once())
            ->method('getUserService')
            ->willReturn($userService);

        $credentials = new UserCredential('username', 'password');
        try {
            $authenticationService->setCredentials($credentials, []);
        } catch (AuthenticationException $e) {
            $this->assertEquals(
                ['error' => AuthenticationException::EMPLOYEE_TERMINATED, 'message' => 'Employee is terminated'],
                $e->normalize()
            );
        }
    }

    public function testsSetCredentialsWithoutEmployee(): void
    {
        $userRole = new UserRole();
        $userRole->setId(1);
        $userRole->setName('Admin');

        $user = new User();
        $user->setId(1);
        $user->setUserRole($userRole);
        $user->setUserName('username');

        $userService = $this->getMockBuilder(UserService::class)
            ->onlyMethods(['getCredentials'])
            ->getMock();
        $userService->expects($this->once())
            ->method('getCredentials')
            ->willReturn($user);

        $authenticationService = $this->getMockBuilder(AuthenticationService::class)
            ->onlyMethods(['getUserService', 'getAuthUser'])
            ->getMock();

        $session = $this->getMockBuilder(Session::class)
            ->onlyMethods(['set'])
            ->getMock();
        $session->expects($this->exactly(3))
            ->method('set');

        $this->createKernelWithMockServices([Services::SESSION => $session]);

        $authUser = \OrangeHRM\Authentication\Auth\User::getInstance();
        $authenticationService->expects($this->once())
            ->method('getUserService')
            ->willReturn($userService);
        $authenticationService->expects($this->exactly(3))
            ->method('getAuthUser')
            ->willReturn($authUser);

        $credentials = new UserCredential('username', 'password');
        $result = $authenticationService->setCredentials($credentials, []);
        $this->assertTrue($result);
    }

    public function testsSetCredentials(): void
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

        $authenticationService = $this->getMockBuilder(AuthenticationService::class)
            ->onlyMethods(['getUserService', 'getAuthUser'])
            ->getMock();

        $session = $this->getMockBuilder(Session::class)
            ->onlyMethods(['set'])
            ->getMock();
        $session->expects($this->exactly(4))
            ->method('set');

        $this->createKernelWithMockServices([Services::SESSION => $session]);

        $authUser = \OrangeHRM\Authentication\Auth\User::getInstance();
        $authenticationService->expects($this->once())
            ->method('getUserService')
            ->willReturn($userService);
        $authenticationService->expects($this->exactly(4))
            ->method('getAuthUser')
            ->willReturn($authUser);

        $credentials = new UserCredential('username', 'password');
        $result = $authenticationService->setCredentials($credentials, []);
        $this->assertTrue($result);
    }
}
