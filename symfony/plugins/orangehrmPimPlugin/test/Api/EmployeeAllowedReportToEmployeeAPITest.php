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

namespace OrangeHRM\Tests\Pim\Api;

;


use OrangeHRM\Authentication\Auth\User;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Api\EmployeeAllowedReportToEmployeeAPI;
use OrangeHRM\Pim\Service\EmployeeReportingMethodService;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;

/**
 * @group Pim
 * @group APIv2
 */
class EmployeeAllowedReportToEmployeeAPITest extends EndpointTestCase
{
    public function testDelete(): void
    {
        $api = new EmployeeAllowedReportToEmployeeAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new EmployeeAllowedReportToEmployeeAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }

    public function testCreate(): void
    {
        $api = new EmployeeAllowedReportToEmployeeAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->create();
    }

    public function testGetValidationRuleForCreate(): void
    {
        $api = new EmployeeAllowedReportToEmployeeAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForCreate();
    }


    public function testGetEmployeeReportingMethodService(): void
    {
        $api = new EmployeeAllowedReportToEmployeeAPI($this->getRequest());
        $result = $api->getEmployeeReportingMethodService();
        $this->assertTrue($result instanceof EmployeeReportingMethodService);
    }

    public function testGetAll(): void
    {
        $empNumber = 1;

        $employee = new Employee();
        $employee->setEmpNumber(7);
        $employee->setFirstName('Andrea');
        $employee->setLastName('Smith');

        $employeeService = $this->getMockBuilder(EmployeeService::class)
            ->onlyMethods(
                [
                    'getSubordinateIdListBySupervisorId',
                    'getSupervisorIdListBySubordinateId',
                    'getEmployeeList',
                    'getEmployeeCount'
                ]
            )
            ->getMock();


        $employeeReportingMethodService = $this->getMockBuilder(EmployeeReportingMethodService::class)
            ->onlyMethods(
                [
                    'getAlreadyAssignedSupervisorsSubordinatesAndSelfIdCombinedList',
                    'getAccessibleAndAvailableSupervisorsIdCombinedList',
                ]
            )
            ->getMock();

        $employeeService->expects($this->once())
            ->method('getSubordinateIdListBySupervisorId')
            ->with($empNumber)
            ->will($this->returnValue([2, 3]));

        $employeeService->expects($this->once())
            ->method('getSupervisorIdListBySubordinateId')
            ->with($empNumber)
            ->will($this->returnValue([4, 5]));

        $employeeService->expects($this->once())
            ->method('getEmployeeList')
            ->will($this->returnValue([$employee]));
        $employeeService->expects($this->once())
            ->method('getEmployeeCount')
            ->will($this->returnValue(1));

        $employeeReportingMethodService->expects($this->once())
            ->method('getAlreadyAssignedSupervisorsSubordinatesAndSelfIdCombinedList')
            ->will($this->returnValue([2, 3, 4, 5]));

        $employeeReportingMethodService->expects($this->once())
            ->method('getAccessibleAndAvailableSupervisorsIdCombinedList')
            ->will($this->returnValue([7, 8]));


        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAccessibleEntityIds'])
            ->getMock();
        $userRoleManager->expects($this->exactly(1))
            ->method('getAccessibleEntityIds')
            ->willReturn([1, 2, 3, 4, 5, 6, 7, 8]);


        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::EMPLOYEE_SERVICE => $employeeService,
            ]
        );

        /** @var MockObject&EmployeeAllowedReportToEmployeeAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeAllowedReportToEmployeeAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ]
            ]
        )->onlyMethods(['getEmployeeReportingMethodService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getEmployeeReportingMethodService')
            ->will($this->returnValue($employeeReportingMethodService));

        $result = $api->getAll();
        $this->assertEquals(
            [
                [
                    'empNumber' => 7,
                    'lastName' => 'Smith',
                    'firstName' => 'Andrea',
                    'middleName' => '',
                    'employeeId' => null,
                    'terminationId' => null
                ]
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                "total" => 1
            ],
            $result->getMeta()->all()
        );
    }


    public function testGetValidationRuleForGetAll(): void
    {
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAccessibleEntityIds'])
            ->getMock();
        $userRoleManager->expects($this->exactly(3))
            ->method('getAccessibleEntityIds')
            ->willReturn([1, 2]);

        $authUser = $this->getMockBuilder(User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->exactly(3))
            ->method('getEmpNumber')
            ->willReturn(2);
        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::AUTH_USER => $authUser
            ]
        );
        $api = new EmployeeAllowedReportToEmployeeAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetAll();
        $this->assertTrue(
            $this->validate(
                [CommonParams::PARAMETER_EMP_NUMBER => 1],
                $rules
            )
        );

        $this->assertTrue(
            $this->validate(
                [CommonParams::PARAMETER_EMP_NUMBER => 1, EmployeeAllowedReportToEmployeeAPI::FILTER_NAME_OR_ID => 'Andrea'],
                $rules
            )
        );

        $this->expectInvalidParamException();
        $this->validate(
            [CommonParams::PARAMETER_EMP_NUMBER => 100],
            $rules
        );
    }
}
