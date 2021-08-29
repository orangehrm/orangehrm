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

namespace OrangeHRM\Tests\Leave\Api\Traits;

use Generator;
use LogicException;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Authorization\Helper\UserRoleManagerHelper;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\LeaveEntitlement;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Api\Traits\LeaveEntitlementPermissionTrait;
use OrangeHRM\Tests\Util\KernelTestCase;
use ReflectionMethod;

class LeaveEntitlementPermissionTraitTest extends KernelTestCase
{
    public function testCheckLeaveEntitlementAccessibleInMockClass(): void
    {
        $leaveEntitlement = new LeaveEntitlement();

        $leaveEntitlementPermissionTrait = $this->getObjectForTrait(LeaveEntitlementPermissionTrait::class);
        $getRequestParameterReflection = new ReflectionMethod(
            get_class($leaveEntitlementPermissionTrait),
            'checkLeaveEntitlementAccessible'
        );
        $getRequestParameterReflection->setAccessible(true);

        $this->expectException(LogicException::class);
        $getRequestParameterReflection->invokeArgs(
            $leaveEntitlementPermissionTrait,
            [$leaveEntitlement]
        );
    }

    /**
     * @dataProvider checkLeaveEntitlementAccessibleInEndpointDataProvider
     */
    public function testCheckLeaveEntitlementAccessibleInEndpoint(
        bool $isEntityAccessibleWillReturn,
        $isEntityAccessibleExpects,
        bool $isSelfByEmpNumberWillReturn,
        $isSelfByEmpNumberExpects,
        bool $expectsForbiddenException = false
    ): void {
        $empNumber = 1;
        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $leaveEntitlement = new LeaveEntitlement();
        $leaveEntitlement->setEmployee($employee);

        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isEntityAccessible'])
            ->getMock();
        $userRoleManager->expects($isEntityAccessibleExpects)
            ->method('isEntityAccessible')
            ->with(Employee::class, $empNumber)
            ->willReturn($isEntityAccessibleWillReturn);

        $userRoleManagerHelper = $this->getMockBuilder(UserRoleManagerHelper::class)
            ->onlyMethods(['isSelfByEmpNumber'])
            ->getMock();
        $userRoleManagerHelper->expects($isSelfByEmpNumberExpects)
            ->method('isSelfByEmpNumber')
            ->willReturn($isSelfByEmpNumberWillReturn);

        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::USER_ROLE_MANAGER_HELPER => $userRoleManagerHelper,
            ]
        );

        $leaveEntitlementPermissionTrait = $this->getMockBuilder(TestEndpoint::class)
            ->disableOriginalConstructor()
            ->getMock();

        if ($expectsForbiddenException) {
            $this->expectException(ForbiddenException::class);
        }
        $this->invokeProtectedMethodOnMock(
            TestEndpoint::class,
            $leaveEntitlementPermissionTrait,
            'checkLeaveEntitlementAccessible',
            [$leaveEntitlement]
        );
    }

    public function checkLeaveEntitlementAccessibleInEndpointDataProvider(): Generator
    {
        yield [true, $this->once(), false, $this->never()];
        yield [true, $this->once(), true, $this->never()];
        yield [false, $this->once(), true, $this->once()];
        yield [false, $this->once(), false, $this->once(), true];
    }
}

class TestEndpoint extends Endpoint
{
    use LeaveEntitlementPermissionTrait;
}
