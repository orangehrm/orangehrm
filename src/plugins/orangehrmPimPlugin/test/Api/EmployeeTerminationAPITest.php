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

use DateTime;
use OrangeHRM\Authentication\Auth\User;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeTerminationRecord;
use OrangeHRM\Entity\TerminationReason;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Api\EmployeeTerminationAPI;
use OrangeHRM\Pim\Dao\EmployeeTerminationDao;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Pim\Service\EmployeeTerminationService;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group APIv2
 */
class EmployeeTerminationAPITest extends EndpointTestCase
{
    protected function loadFixtures(): void
    {
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmPimPlugin/test/fixtures/EmployeeTerminationDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testDelete(): void
    {
        $this->loadFixtures();

        $empNumber = 1;
        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $terminationReason = new TerminationReason();
        $terminationReason->setId(1);
        $terminationReason->setName('Test Reason');
        $terminationRecord = new EmployeeTerminationRecord();
        $terminationRecord->setTerminationReason($terminationReason);
        $terminationRecord->setId(1);
        $terminationRecord->setEmployee($employee);
        $terminationRecord->setDate(new DateTime('2021-06-17'));
        $terminationRecord->setNote('Test Note');
        $employee->setEmployeeTerminationRecord($terminationRecord);

        $employeeService = $this->getMockBuilder(EmployeeService::class)
            ->onlyMethods(['saveEmployee', 'getEmployeeByEmpNumber'])
            ->getMock();
        $employeeService->expects($this->once())
            ->method('saveEmployee')
            ->will(
                $this->returnCallback(
                    function ($employee) {
                        return $employee;
                    }
                )
            );

        $employee2 = new Employee();
        $employee2->setEmpNumber(2);

        $map = [
            [1, $employee],
            [2, $employee2],
            [3, null],
        ];
        $employeeService->expects($this->exactly(3))
            ->method('getEmployeeByEmpNumber')
            ->will($this->returnValueMap($map));

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $employeeService,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );

        /** @var MockObject&EmployeeTerminationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeTerminationAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ]
            ]
        )->onlyMethods([])->getMock();

        $result = $api->delete();
        $this->assertEquals(
            [
                'id' => 1,
                'note' => 'Test Note',
                'date' => '2021-06-17',
                'terminationReason' => [
                    'id' => 1,
                    'name' => 'Test Reason',
                ]
            ],
            $result->normalize()
        );
        $this->assertEquals(
            ['empNumber' => $empNumber],
            $result->getMeta()->all()
        );

        /** @var MockObject&EmployeeTerminationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeTerminationAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => 2,
                ]
            ]
        )->onlyMethods([])->getMock();
        try {
            $api->delete();
        } catch (RecordNotFoundException $e) {
            $this->assertEquals('Record Not Found', $e->getMessage());
        }

        /** @var MockObject&EmployeeTerminationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeTerminationAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => 3,
                ]
            ]
        )->onlyMethods([])->getMock();
        try {
            $api->delete();
        } catch (RecordNotFoundException $e) {
            $this->assertEquals('Record Not Found', $e->getMessage());
        }
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
        $api = new EmployeeTerminationAPI($this->getRequest());
        $rules = $api->getValidationRuleForDelete();
        $this->assertTrue(
            $this->validate(
                [CommonParams::PARAMETER_EMP_NUMBER => 1],
                $rules
            )
        );
    }

    public function testGetAll(): void
    {
        $api = new EmployeeTerminationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getAll();
    }

    public function testGetValidationRuleForGetAll(): void
    {
        $api = new EmployeeTerminationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForGetAll();
    }

    public function testGetOne(): void
    {
        $empNumber = 1;
        $employeeTerminationRecordId = 1;
        $employeeTerminationDao = $this->getMockBuilder(EmployeeTerminationDao::class)
            ->onlyMethods(['getEmployeeTermination'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $terminationReason = new TerminationReason();
        $terminationReason->setId(2);
        $terminationReason->setName('Test Reason');
        $employeeTerminationRecord = new EmployeeTerminationRecord();
        $employeeTerminationRecord->setId($employeeTerminationRecordId);
        $employeeTerminationRecord->setEmployee($employee);
        $employeeTerminationRecord->setTerminationReason($terminationReason);
        $employeeTerminationRecord->setDate(new DateTime('2020-05-23'));
        $employeeTerminationRecord->setNote('Note');

        $map = [
            [$employeeTerminationRecordId, $employeeTerminationRecord],
            [2, null],
        ];
        $employeeTerminationDao->expects($this->exactly(2))
            ->method('getEmployeeTermination')
            ->will($this->returnValueMap($map));

        $employeeTerminationService = $this->getMockBuilder(EmployeeTerminationService::class)
            ->onlyMethods(['getEmployeeTerminationDao'])
            ->getMock();
        $employeeTerminationService->expects($this->exactly(2))
            ->method('getEmployeeTerminationDao')
            ->willReturn($employeeTerminationDao);

        $employeeService = $this->getMockBuilder(EmployeeService::class)
            ->onlyMethods(['getEmployeeTerminationService'])
            ->getMock();
        $employeeService->expects($this->exactly(2))
            ->method('getEmployeeTerminationService')
            ->willReturn($employeeTerminationService);

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $employeeService,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );

        /** @var MockObject&EmployeeTerminationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeTerminationAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_ID => $employeeTerminationRecordId,
                ]
            ]
        )->onlyMethods([])->getMock();

        $result = $api->getOne();
        $this->assertEquals(
            [
                'id' => 1,
                'note' => 'Note',
                'date' => '2020-05-23',
                'terminationReason' => [
                    'id' => 2,
                    'name' => 'Test Reason',
                ]
            ],
            $result->normalize()
        );
        $this->assertEquals(
            ['empNumber' => $empNumber],
            $result->getMeta()->all()
        );

        /** @var MockObject&EmployeeTerminationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeTerminationAPI::class,
            [
                CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                CommonParams::PARAMETER_ID => 2,
            ]
        )->onlyMethods([])->getMock();

        $this->expectRecordNotFoundException();
        $api->getOne();
    }

    public function testGetValidationRuleForGetOne(): void
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
        $api = new EmployeeTerminationAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetOne();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    CommonParams::PARAMETER_ID => 1
                ],
                $rules
            )
        );
    }

    public function testCreateWithRecordNotFound(): void
    {
        $employeeService = $this->getMockBuilder(EmployeeService::class)
            ->onlyMethods(['getEmployeeByEmpNumber'])
            ->getMock();
        $employeeService->expects($this->once())
            ->method('getEmployeeByEmpNumber')
            ->willReturn(null);

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $employeeService,
            ]
        );

        /** @var MockObject&EmployeeTerminationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeTerminationAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    CommonParams::PARAMETER_ID => 1,
                ]
            ]
        )->onlyMethods([])->getMock();

        $this->expectRecordNotFoundException();
        $api->create();
    }

    public function testCreate(): void
    {
        $this->loadFixtures();

        $empNumber = 1;
        $employee = new Employee();
        $employee->setEmpNumber($empNumber);

        $employeeService = $this->getMockBuilder(EmployeeService::class)
            ->onlyMethods(['saveEmployee', 'getEmployeeByEmpNumber'])
            ->getMock();
        $employeeService->expects($this->once())
            ->method('saveEmployee')
            ->will(
                $this->returnCallback(
                    function ($employee) {
                        $employee->getEmployeeTerminationRecord()->setId(1);
                        return $employee;
                    }
                )
            );
        $employeeService->expects($this->once())
            ->method('getEmployeeByEmpNumber')
            ->willReturn($employee);

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $employeeService,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );

        /** @var MockObject&EmployeeTerminationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeTerminationAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    EmployeeTerminationAPI::PARAMETER_TERMINATION_REASON_ID => 2,
                    EmployeeTerminationAPI::PARAMETER_DATE => '2020-05-24',
                    EmployeeTerminationAPI::PARAMETER_NOTE => 'Comment',
                ]
            ]
        )->onlyMethods([])->getMock();

        $result = $api->create();
        $this->assertEquals(
            [
                'id' => 1,
                'note' => 'Comment',
                'date' => '2020-05-24',
                'terminationReason' => [
                    'id' => 2,
                    'name' => 'Dismissed',
                ]
            ],
            $result->normalize()
        );
        $this->assertEquals(
            ['empNumber' => $empNumber],
            $result->getMeta()->all()
        );
    }

    public function testGetValidationRuleForCreate(): void
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
        $api = new EmployeeTerminationAPI($this->getRequest());
        $rules = $api->getValidationRuleForCreate();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    EmployeeTerminationAPI::PARAMETER_TERMINATION_REASON_ID => 1,
                    EmployeeTerminationAPI::PARAMETER_DATE => '2020-02-02',
                    EmployeeTerminationAPI::PARAMETER_NOTE => 'Note',
                ],
                $rules
            )
        );
    }

    public function testUpdate(): void
    {
        $this->loadFixtures();

        $empNumber = 1;
        $employeeTerminationRecordId = 1;
        $employeeTerminationDao = $this->getMockBuilder(EmployeeTerminationDao::class)
            ->onlyMethods(['getEmployeeTermination', 'saveEmployeeTermination'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $terminationReason = new TerminationReason();
        $terminationReason->setId(1);
        $terminationReason->setName('Test Reason');
        $employeeTerminationRecord = new EmployeeTerminationRecord();
        $employeeTerminationRecord->setId($employeeTerminationRecordId);
        $employeeTerminationRecord->setEmployee($employee);
        $employeeTerminationRecord->setTerminationReason($terminationReason);
        $employeeTerminationRecord->setDate(new DateTime('2020-05-23'));
        $employeeTerminationRecord->setNote('Note');

        $map = [
            [$employeeTerminationRecordId, $employeeTerminationRecord],
            [2, null],
        ];
        $employeeTerminationDao->expects($this->exactly(2))
            ->method('getEmployeeTermination')
            ->will($this->returnValueMap($map));
        $employeeTerminationDao->expects($this->once())
            ->method('saveEmployeeTermination')
            ->will(
                $this->returnCallback(
                    function ($terminationRecord) {
                        return $terminationRecord;
                    }
                )
            );

        $employeeTerminationService = $this->getMockBuilder(EmployeeTerminationService::class)
            ->onlyMethods(['getEmployeeTerminationDao'])
            ->getMock();
        $employeeTerminationService->expects($this->exactly(3))
            ->method('getEmployeeTerminationDao')
            ->willReturn($employeeTerminationDao);

        $employeeService = $this->getMockBuilder(EmployeeService::class)
            ->onlyMethods(['getEmployeeTerminationService'])
            ->getMock();
        $employeeService->expects($this->exactly(3))
            ->method('getEmployeeTerminationService')
            ->willReturn($employeeTerminationService);

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $employeeService,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );

        /** @var MockObject&EmployeeTerminationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeTerminationAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_ID => $employeeTerminationRecordId,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    EmployeeTerminationAPI::PARAMETER_TERMINATION_REASON_ID => 2,
                    EmployeeTerminationAPI::PARAMETER_DATE => '2020-05-24',
                    EmployeeTerminationAPI::PARAMETER_NOTE => 'Comment',
                ]
            ]
        )->onlyMethods([])->getMock();

        $result = $api->update();
        $this->assertEquals(
            [
                'id' => 1,
                'note' => 'Comment',
                'date' => '2020-05-24',
                'terminationReason' => [
                    'id' => 2,
                    'name' => 'Dismissed',
                ]
            ],
            $result->normalize()
        );
        $this->assertEquals(
            ['empNumber' => $empNumber],
            $result->getMeta()->all()
        );

        /** @var MockObject&EmployeeTerminationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeTerminationAPI::class,
            [
                CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                CommonParams::PARAMETER_ID => 2,
            ]
        )->onlyMethods([])->getMock();

        $this->expectRecordNotFoundException();
        $api->update();
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
        $api = new EmployeeTerminationAPI($this->getRequest());
        $rules = $api->getValidationRuleForUpdate();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    CommonParams::PARAMETER_ID => 1,
                    EmployeeTerminationAPI::PARAMETER_TERMINATION_REASON_ID => 1,
                    EmployeeTerminationAPI::PARAMETER_DATE => '2020-02-02',
                    EmployeeTerminationAPI::PARAMETER_NOTE => 'Note',
                ],
                $rules
            )
        );
    }
}
