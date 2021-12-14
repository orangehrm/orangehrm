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

use OrangeHRM\Authentication\Auth\User;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Authorization\Dto\ResourcePermission;
use OrangeHRM\Core\Authorization\Helper\UserRoleManagerHelper;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Core\Service\NormalizerService;
use OrangeHRM\Entity\CustomField;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Api\EmployeeCustomFieldAPI;
use OrangeHRM\Pim\Dao\CustomFieldDao;
use OrangeHRM\Pim\Service\CustomFieldService;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;

/**
 * @group Pim
 * @group APIv2
 */
class EmployeeCustomFieldAPITest extends EndpointTestCase
{
    public function testGetCustomFieldService(): void
    {
        $api = new EmployeeCustomFieldAPI($this->getRequest());
        $this->assertTrue($api->getCustomFieldService() instanceof CustomFieldService);
    }

    public function testGetOne(): void
    {
        $empNumber = 1;
        $customFieldDao = $this->getMockBuilder(CustomFieldDao::class)
            ->onlyMethods(['searchCustomField'])
            ->getMock();

        $customField = new CustomField();
        $customField->setFieldNum(1);
        $customField->setName('Field 1');
        $customField->setType(1);
        $customField->setScreen(CustomField::SCREEN_PERSONAL_DETAILS);

        $customFieldDao->expects($this->once())
            ->method('searchCustomField')
            ->willReturn([$customField]);

        $customFieldService = $this->getMockBuilder(CustomFieldService::class)
            ->onlyMethods(['getCustomFieldDao'])
            ->getMock();

        $customFieldService->expects($this->once())
            ->method('getCustomFieldDao')
            ->willReturn($customFieldDao);

        /** @var MockObject&EmployeeCustomFieldAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeCustomFieldAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    EmployeeCustomFieldAPI::PARAMETER_SCREEN => 'personal',
                ]
            ]
        )->onlyMethods(['getCustomFieldService'])
            ->getMock();
        $api->expects($this->exactly(3))
            ->method('getCustomFieldService')
            ->will($this->returnValue($customFieldService));

        $employee = new Employee();
        $employee->setEmpNumber(1);
        $employee->setCustom1('Test');
        $employeeService = $this->getMockBuilder(EmployeeService::class)
            ->onlyMethods(['getEmployeeByEmpNumber'])
            ->getMock();
        $employeeService->expects($this->once())
            ->method('getEmployeeByEmpNumber')
            ->willReturn($employee);
        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $employeeService,
                Services::NORMALIZER_SERVICE => new NormalizerService()
            ]
        );

        $result = $api->getOne();
        $this->assertEquals(
            [
                "custom1" => "Test",
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                "empNumber" => $empNumber,
                "fields" => [
                    [
                        "id" => 1,
                        "fieldName" => "Field 1",
                        "fieldType" => 1,
                        "extraData" => null,
                        "screen" => "personal",
                    ]
                ]
            ],
            $result->getMeta()->all()
        );
    }

    public function testGetValidationRuleForGetOne(): void
    {
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAccessibleEntityIds'])
            ->getMock();
        $userRoleManager->expects($this->exactly(2))
            ->method('getAccessibleEntityIds')
            ->willReturn([1, 2]);

        $authUser = $this->getMockBuilder(User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->exactly(2))
            ->method('getEmpNumber')
            ->willReturn(2);
        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::AUTH_USER => $authUser
            ]
        );
        $api = new EmployeeCustomFieldAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetOne();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    EmployeeCustomFieldAPI::PARAMETER_SCREEN => 'personal'
                ],
                $rules
            )
        );

        $this->expectInvalidParamException();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    EmployeeCustomFieldAPI::PARAMETER_SCREEN => 'invalid screen'
                ],
                $rules
            )
        );
    }

    public function testUpdate(): void
    {
        $empNumber = 1;
        $customFieldDao = $this->getMockBuilder(CustomFieldDao::class)
            ->onlyMethods(['searchCustomField'])
            ->getMock();

        $customField = new CustomField();
        $customField->setFieldNum(1);
        $customField->setName('Field 1');
        $customField->setType(1);
        $customField->setScreen(CustomField::SCREEN_PERSONAL_DETAILS);

        $customFieldDao->expects($this->once())
            ->method('searchCustomField')
            ->willReturn([$customField]);

        $customFieldService = $this->getMockBuilder(CustomFieldService::class)
            ->onlyMethods(['getCustomFieldDao'])
            ->getMock();

        $customFieldService->expects($this->once())
            ->method('getCustomFieldDao')
            ->willReturn($customFieldDao);

        /** @var MockObject&EmployeeCustomFieldAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeCustomFieldAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    "custom1" => 'Test Updated'
                ]
            ]
        )->onlyMethods(['getCustomFieldService'])
            ->getMock();
        $api->expects($this->exactly(5))
            ->method('getCustomFieldService')
            ->will($this->returnValue($customFieldService));

        $employee = new Employee();
        $employee->setEmpNumber(1);
        $employee->setCustom1('Test');
        $employeeService = $this->getMockBuilder(EmployeeService::class)
            ->onlyMethods(['getEmployeeByEmpNumber', 'saveEmployee'])
            ->getMock();
        $employeeService->expects($this->once())
            ->method('getEmployeeByEmpNumber')
            ->willReturn($employee);
        $employeeService->expects($this->once())
            ->method('saveEmployee')
            ->willReturnCallback(
                function (Employee $employee) {
                    return $employee;
                }
            );

        $userRoleManagerHelper = $this->getMockBuilder(UserRoleManagerHelper::class)
            ->onlyMethods(['getDataGroupPermissionsForEmployee'])
            ->getMock();
        $userRoleManagerHelper->expects($this->once())
            ->method('getDataGroupPermissionsForEmployee')
            ->willReturn(new ResourcePermission(true, false, true, false));

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $employeeService,
                Services::NORMALIZER_SERVICE => new NormalizerService(),
                Services::USER_ROLE_MANAGER_HELPER => $userRoleManagerHelper,
            ]
        );

        $result = $api->update();
        $this->assertEquals(
            [
                "custom1" => "Test Updated",
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                "empNumber" => $empNumber,
                "fields" => [
                    [
                        "id" => 1,
                        "fieldName" => "Field 1",
                        "fieldType" => 1,
                        "extraData" => null,
                        "screen" => "personal",
                    ]
                ]
            ],
            $result->getMeta()->all()
        );
    }

    public function testUpdateWithoutPermission(): void
    {
        $empNumber = 1;
        $customFieldDao = $this->getMockBuilder(CustomFieldDao::class)
            ->onlyMethods(['searchCustomField'])
            ->getMock();

        $customField = new CustomField();
        $customField->setFieldNum(1);
        $customField->setName('Field 1');
        $customField->setType(1);
        $customField->setScreen(CustomField::SCREEN_PERSONAL_DETAILS);

        $customFieldDao->expects($this->once())
            ->method('searchCustomField')
            ->willReturn([$customField]);

        $customFieldService = $this->getMockBuilder(CustomFieldService::class)
            ->onlyMethods(['getCustomFieldDao'])
            ->getMock();

        $customFieldService->expects($this->once())
            ->method('getCustomFieldDao')
            ->willReturn($customFieldDao);

        /** @var MockObject&EmployeeCustomFieldAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeCustomFieldAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    "custom1" => 'Test Updated'
                ]
            ]
        )->onlyMethods(['getCustomFieldService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getCustomFieldService')
            ->will($this->returnValue($customFieldService));

        $employee = new Employee();
        $employee->setEmpNumber(1);
        $employee->setCustom1('Test');
        $employeeService = $this->getMockBuilder(EmployeeService::class)
            ->onlyMethods(['getEmployeeByEmpNumber', 'saveEmployee'])
            ->getMock();
        $employeeService->expects($this->once())
            ->method('getEmployeeByEmpNumber')
            ->willReturn($employee);
        $employeeService->expects($this->never())
            ->method('saveEmployee');

        $userRoleManagerHelper = $this->getMockBuilder(UserRoleManagerHelper::class)
            ->onlyMethods(['getDataGroupPermissionsForEmployee'])
            ->getMock();
        $userRoleManagerHelper->expects($this->once())
            ->method('getDataGroupPermissionsForEmployee')
            ->willReturn(new ResourcePermission(true, false, false, false));

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $employeeService,
                Services::NORMALIZER_SERVICE => new NormalizerService(),
                Services::USER_ROLE_MANAGER_HELPER => $userRoleManagerHelper,
            ]
        );

        $this->expectForbiddenException();
        $api->update();
    }

    public function testGetValidationRuleForUpdate(): void
    {
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAccessibleEntityIds'])
            ->getMock();
        $userRoleManager->expects($this->exactly(5))
            ->method('getAccessibleEntityIds')
            ->willReturn([1, 2]);

        $authUser = $this->getMockBuilder(User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->exactly(5))
            ->method('getEmpNumber')
            ->willReturn(2);
        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::AUTH_USER => $authUser
            ]
        );
        $api = new EmployeeCustomFieldAPI($this->getRequest());
        $rules = $api->getValidationRuleForUpdate();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    "custom1" => 'Test 1'
                ],
                $rules
            )
        );
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    "custom10" => 'Test 10'
                ],
                $rules
            )
        );
        // Check for empty string
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    'custom1' => ''
                ],
                $rules
            )
        );
        // Check for null
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    'custom10' => null
                ],
                $rules
            )
        );
        $this->assertInvalidParamException(
            fn () => $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    'custom11' => 'Test 11'
                ],
                $rules
            ),
            ['custom11']
        );
    }

    public function testDelete(): void
    {
        $api = new EmployeeCustomFieldAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new EmployeeCustomFieldAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }
}
