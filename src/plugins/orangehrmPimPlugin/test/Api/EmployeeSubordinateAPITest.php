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

namespace OrangeHRM\Tests\Pim\Api;

use OrangeHRM\Authentication\Auth\User;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\ReportingMethod;
use OrangeHRM\Entity\ReportTo;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Api\EmployeeSubordinateAPI;
use OrangeHRM\Pim\Dao\EmployeeReportingMethodDao;
use OrangeHRM\Pim\Service\EmployeeReportingMethodService;

use OrangeHRM\Pim\Service\ReportingMethodConfigurationService;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;

/**
 * @group Pim
 * @group APIv2
 */
class EmployeeSubordinateAPITest extends EndpointTestCase
{
    public function testGetEmployeeReportingMethodService(): void
    {
        $api = new EmployeeSubordinateAPI($this->getRequest());
        $this->assertTrue($api->getEmployeeReportingMethodService() instanceof EmployeeReportingMethodService);
    }

    public function testGetReportingMethodConfigurationService(): void
    {
        $api = new EmployeeSubordinateAPI($this->getRequest());
        $this->assertTrue($api->getReportingMethodConfigurationService() instanceof ReportingMethodConfigurationService);
    }

    public function testGetOne(): void
    {
        $reportingMethod = new ReportingMethod();
        $reportingMethod->setId(1);
        $reportingMethod->setName('Direct');

        $supervisor = new Employee();
        $supervisor->setEmpNumber(1);
        $supervisor->setFirstName('Andrea');
        $supervisor->setLastName('Smith');

        $subordinate = new Employee();
        $subordinate->setEmpNumber(2);
        $subordinate->setFirstName('Peter');
        $subordinate->setLastName('Samuel');

        $reportTo = new ReportTo();
        $reportTo->setReportingMethod($reportingMethod);
        $reportTo->setSupervisor($supervisor);
        $reportTo->setSubordinate($subordinate);


        $employeeReportingMethodDao = $this->getMockBuilder(EmployeeReportingMethodDao::class)
            ->onlyMethods(['getEmployeeReportToByEmpNumbers'])
            ->getMock();

        $employeeReportingMethodDao->expects($this->once())
            ->method('getEmployeeReportToByEmpNumbers')
            ->with(2, 1)
            ->will($this->returnValue($reportTo));

        $employeeReportingMethodService = $this->getMockBuilder(EmployeeReportingMethodService::class)
            ->onlyMethods(
                [
                    'getEmployeeReportingMethodDao',
                ]
            )
            ->getMock();

        $employeeReportingMethodService->expects($this->exactly(1))
            ->method('getEmployeeReportingMethodDao')
            ->willReturn($employeeReportingMethodDao);


        /** @var MockObject&EmployeeSubordinateAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeSubordinateAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    CommonParams::PARAMETER_ID => 2,
                ],
            ]
        )->onlyMethods(['getEmployeeReportingMethodService'])
            ->getMock();
        $api->expects($this->exactly(1))
            ->method('getEmployeeReportingMethodService')
            ->will($this->returnValue($employeeReportingMethodService));

        $result = $api->getOne();
        $this->assertEquals(
            [
                'subordinate' => ['empNumber' => 2, 'firstName' => 'Peter', 'lastName' => 'Samuel', 'middleName' => '', 'terminationId' => null],
                'reportingMethod' => ['id' => 1, 'name' => 'Direct']
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                "empNumber" => 1,
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
        $userRoleManager->expects($this->exactly(1))
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
        $api = new EmployeeSubordinateAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetOne();
        $this->assertTrue(
            $this->validate(
                [CommonParams::PARAMETER_EMP_NUMBER => 1, CommonParams::PARAMETER_ID => 2],
                $rules
            )
        );
    }

    public function testGetValidationRuleForGetAll(): void
    {
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAccessibleEntityIds'])
            ->getMock();
        $userRoleManager->expects($this->exactly(1))
            ->method('getAccessibleEntityIds')
            ->willReturn([1, 2]);

        $authUser = $this->getMockBuilder(User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->exactly(1))
            ->method('getEmpNumber')
            ->willReturn(2);
        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::AUTH_USER => $authUser
            ]
        );
        $api = new EmployeeSubordinateAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetAll();
        $this->assertTrue(
            $this->validate(
                [CommonParams::PARAMETER_EMP_NUMBER => 1],
                $rules
            )
        );
    }

    public function testGetAll(): void
    {
        $reportingMethod = new ReportingMethod();
        $reportingMethod->setId(1);
        $reportingMethod->setName('Direct');

        $supervisor1 = new Employee();
        $supervisor1->setEmpNumber(1);
        $supervisor1->setFirstName('Andrea');
        $supervisor1->setLastName('Smith');

        $subordinate1 = new Employee();
        $subordinate1->setEmpNumber(2);
        $subordinate1->setFirstName('Peter');
        $subordinate1->setLastName('Samuel');

        $subordinate2 = new Employee();
        $subordinate2->setEmpNumber(3);
        $subordinate2->setFirstName('Andrew');
        $subordinate2->setLastName('Daniel');

        $reportTo1 = new ReportTo();
        $reportTo1->setReportingMethod($reportingMethod);
        $reportTo1->setSupervisor($supervisor1);
        $reportTo1->setSubordinate($subordinate1);

        $reportTo2 = new ReportTo();
        $reportTo2->setReportingMethod($reportingMethod);
        $reportTo2->setSupervisor($supervisor1);
        $reportTo2->setSubordinate($subordinate2);

        $employeeReportingMethodService = $this->getMockBuilder(EmployeeReportingMethodService::class)
            ->onlyMethods(
                [
                    'getSubordinateListForEmployee',
                    'getSubordinateListCountForEmployee',
                ]
            )
            ->getMock();

        $employeeReportingMethodService->expects($this->exactly(1))
            ->method('getSubordinateListForEmployee')
            ->willReturn([$reportTo1, $reportTo2]);


        $employeeReportingMethodService->expects($this->exactly(1))
            ->method('getSubordinateListCountForEmployee')
            ->willReturn(2);


        /** @var MockObject&EmployeeSubordinateAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeSubordinateAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                ],
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
                    'subordinate' => [
                        'empNumber' => 2,
                        'firstName' => 'Peter',
                        'lastName' => 'Samuel',
                        'middleName' => '',
                        "terminationId" => null
                    ],
                    'reportingMethod' => ['id' => 1, 'name' => 'Direct']
                ],
                [
                    'subordinate' => [
                        'empNumber' => 3,
                        'firstName' => 'Andrew',
                        'lastName' => 'Daniel',
                        'middleName' => '',
                        "terminationId" => null
                    ],
                    'reportingMethod' => ['id' => 1, 'name' => 'Direct']
                ]
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                "empNumber" => 1,
                "total" => 2
            ],
            $result->getMeta()->all()
        );
    }

    public function testCreate(): void
    {
        $reportingMethod = new ReportingMethod();
        $reportingMethod->setId(1);
        $reportingMethod->setName('Direct');

        $supervisor = new Employee();
        $supervisor->setEmpNumber(1);
        $supervisor->setFirstName('Andrea');
        $supervisor->setLastName('Smith');

        $subordinate = new Employee();
        $subordinate->setEmpNumber(2);
        $subordinate->setFirstName('Peter');
        $subordinate->setLastName('Samuel');

        $reportTo = new ReportTo();
        $reportTo->setReportingMethod($reportingMethod);
        $reportTo->setSupervisor($supervisor);
        $reportTo->setSubordinate($subordinate);

        $employeeReportingMethodDao = $this->getMockBuilder(EmployeeReportingMethodDao::class)
            ->onlyMethods(['saveEmployeeReportTo'])
            ->getMock();

        $employeeReportingMethodDao->expects($this->once())
            ->method('saveEmployeeReportTo')
            ->will($this->returnValue($reportTo));

        $employeeReportingMethodService = $this->getMockBuilder(EmployeeReportingMethodService::class)
            ->onlyMethods(
                [
                    'getEmployeeReportingMethodDao',
                ]
            )
            ->getMock();

        $employeeReportingMethodService->expects($this->exactly(1))
            ->method('getEmployeeReportingMethodDao')
            ->willReturn($employeeReportingMethodDao);


        /** @var MockObject&EmployeeSubordinateAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeSubordinateAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    EmployeeSubordinateAPI::PARAMETER_REPORTING_METHOD => 1,
                    CommonParams::PARAMETER_EMP_NUMBER => 2,
                ],
            ]
        )->onlyMethods(['getEmployeeReportingMethodService'])
            ->getMock();
        $api->expects($this->exactly(1))
            ->method('getEmployeeReportingMethodService')
            ->will($this->returnValue($employeeReportingMethodService));

        $result = $api->create();
        $this->assertEquals(
            [
                "subordinate" => [
                    "empNumber" => 2,
                    "firstName" => "Peter",
                    "lastName" => "Samuel",
                    "middleName" => "",
                    "terminationId" => null
                ],
                "reportingMethod" => [
                    "id" => 1,
                    "name" => "Direct",
                ]
            ],
            $result->normalize()
        );
    }


    public function testGetValidationRuleForCreate(): void
    {
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAccessibleEntityIds'])
            ->getMock();
        $userRoleManager->expects($this->exactly(1))
            ->method('getAccessibleEntityIds')
            ->willReturn([1, 2]);

        $authUser = $this->getMockBuilder(User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->once())
            ->method('getEmpNumber')
            ->willReturn(1);
        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::AUTH_USER => $authUser
            ]
        );
        $api = new EmployeeSubordinateAPI($this->getRequest());
        $rules = $api->getValidationRuleForCreate();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    CommonParams::PARAMETER_EMP_NUMBER => 2,
                    EmployeeSubordinateAPI::PARAMETER_REPORTING_METHOD => 1,
                ],
                $rules
            )
        );
    }

    public function testUpdate(): void
    {
        $reportingMethod1 = new ReportingMethod();
        $reportingMethod1->setId(1);
        $reportingMethod1->setName('Direct');

        $supervisor = new Employee();
        $supervisor->setEmpNumber(1);
        $supervisor->setFirstName('Andrea');
        $supervisor->setLastName('Smith');

        $subordinate = new Employee();
        $subordinate->setEmpNumber(2);
        $subordinate->setFirstName('Peter');
        $subordinate->setLastName('Samuel');

        $reportTo = new ReportTo();
        $reportTo->setReportingMethod($reportingMethod1);
        $reportTo->setSupervisor($supervisor);
        $reportTo->setSubordinate($subordinate);

        $employeeReportingMethodDao = $this->getMockBuilder(EmployeeReportingMethodDao::class)
            ->onlyMethods(['saveEmployeeReportTo', 'getEmployeeReportToByEmpNumbers'])
            ->getMock();

        $employeeReportingMethodDao->expects($this->once())
            ->method('saveEmployeeReportTo')
            ->will($this->returnValue($reportTo));

        $employeeReportingMethodDao->expects($this->once())
            ->method('getEmployeeReportToByEmpNumbers')
            ->with(2, 1)
            ->will($this->returnValue($reportTo));

        $employeeReportingMethodService = $this->getMockBuilder(EmployeeReportingMethodService::class)
            ->onlyMethods(
                [
                    'getEmployeeReportingMethodDao',
                ]
            )
            ->getMock();

        $employeeReportingMethodService->expects($this->exactly(2))
            ->method('getEmployeeReportingMethodDao')
            ->willReturn($employeeReportingMethodDao);

        $employeeReportingMethodConfigurationService = $this->getMockBuilder(ReportingMethodConfigurationService::class)
            ->onlyMethods(
                [
                    'getReportingMethodById',
                ]
            )
            ->getMock();

        $employeeReportingMethodConfigurationService->expects($this->exactly(1))
            ->method('getReportingMethodById')
            ->willReturn($reportingMethod1);



        /** @var MockObject&EmployeeSubordinateAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeSubordinateAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    CommonParams::PARAMETER_ID => 2,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    EmployeeSubordinateAPI::PARAMETER_REPORTING_METHOD => 1,
                ],
            ]
        )->onlyMethods(['getEmployeeReportingMethodService', 'getReportingMethodConfigurationService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getEmployeeReportingMethodService')
            ->will($this->returnValue($employeeReportingMethodService));

        $api->expects($this->exactly(1))
            ->method('getReportingMethodConfigurationService')
            ->will($this->returnValue($employeeReportingMethodConfigurationService));

        $result = $api->update();
        $this->assertEquals(
            [
                "subordinate" => [
                    "empNumber" => 2,
                    "firstName" => "Peter",
                    "lastName" => "Samuel",
                    "middleName" => "",
                    "terminationId" => null
                ],
                "reportingMethod" => [
                    "id" => 1,
                    "name" => "Direct",
                ]
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForUpdate(): void
    {
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAccessibleEntityIds'])
            ->getMock();
        $userRoleManager->expects($this->exactly(0))
            ->method('getAccessibleEntityIds')
            ->willReturn([1, 2]);

        $authUser = $this->getMockBuilder(User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->once())
            ->method('getEmpNumber')
            ->willReturn(1);
        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::AUTH_USER => $authUser
            ]
        );

        $api = new EmployeeSubordinateAPI($this->getRequest());
        $rules = $api->getValidationRuleForUpdate();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    CommonParams::PARAMETER_ID => 2,
                    EmployeeSubordinateAPI::PARAMETER_REPORTING_METHOD => 1,
                ],
                $rules
            )
        );
    }



    public function testDelete()
    {
        $empNumber = 1;
        $employeeReportingMethodDao = $this->getMockBuilder(EmployeeReportingMethodDao::class)
            ->onlyMethods(['deleteEmployeeSubordinates'])
            ->getMock();

        $employee1 = new Employee();
        $employee1->setEmpNumber($empNumber);

        $employee2 = new Employee();
        $employee2->setEmpNumber($empNumber);


        $reportingMethod = new ReportingMethod();
        $reportingMethod->setId(1);
        $reportingMethod->setName('Direct');

        $reportTo = new ReportTo();
        $reportTo->setSubordinate($employee1);
        $reportTo->setSupervisor($employee2);
        $reportTo->setReportingMethod($reportingMethod);

        $employeeReportingMethodDao->expects($this->exactly(0))
            ->method('deleteEmployeeSubordinates')
            ->with(1, [1])
            ->willReturn(1);

        $employeeReportingMethodService = $this->getMockBuilder(EmployeeReportingMethodService::class)
            ->onlyMethods(['getEmployeeReportingMethodDao'])
            ->getMock();

        $employeeReportingMethodService->expects($this->exactly(1))
            ->method('getEmployeeReportingMethodDao')
            ->willReturn($employeeReportingMethodDao);

        /** @var MockObject&EmployeeSubordinateAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeSubordinateAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    CommonParams::PARAMETER_IDS => [1],
                ]
            ]
        )->onlyMethods(['getEmployeeReportingMethodService'])
            ->getMock();
        $api->expects($this->exactly(1))
            ->method('getEmployeeReportingMethodService')
            ->will($this->returnValue($employeeReportingMethodService));

        $this->expectRecordNotFoundException();
        $result = $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAccessibleEntityIds'])
            ->getMock();
        $userRoleManager->expects($this->exactly(0))
            ->method('getAccessibleEntityIds')
            ->willReturn([1, 2]);

        $authUser = $this->getMockBuilder(User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->once())
            ->method('getEmpNumber')
            ->willReturn(1);
        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::AUTH_USER => $authUser
            ]
        );
        $api = new EmployeeSubordinateAPI($this->getRequest());
        $rules = $api->getValidationRuleForDelete();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    CommonParams::PARAMETER_IDS => [2, 3],
                ],
                $rules
            )
        );
    }
}
