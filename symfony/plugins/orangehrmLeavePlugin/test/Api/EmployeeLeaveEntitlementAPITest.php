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

namespace OrangeHRM\Tests\Leave\Api;

use DateTime;
use OrangeHRM\Authentication\Auth\User;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\LeaveEntitlement;
use OrangeHRM\Entity\LeaveEntitlementType;
use OrangeHRM\Entity\LeaveType;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Api\EmployeeLeaveEntitlementAPI;
use OrangeHRM\Leave\Api\LeaveCommonParams;
use OrangeHRM\Leave\Dao\LeaveEntitlementDao;
use OrangeHRM\Leave\Dao\LeaveTypeDao;
use OrangeHRM\Leave\Dto\LeaveEntitlementSearchFilterParams;
use OrangeHRM\Leave\Dto\LeavePeriod;
use OrangeHRM\Leave\Service\LeaveEntitlementService;
use OrangeHRM\Leave\Service\LeavePeriodService;
use OrangeHRM\Leave\Service\LeaveTypeService;
use OrangeHRM\Pim\Dto\EmployeeSearchFilterParams;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;

/**
 * @group Leave
 * @group APIv2
 */
class EmployeeLeaveEntitlementAPITest extends EndpointTestCase
{
    public function testDelete(): void
    {
        $api = new EmployeeLeaveEntitlementAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new EmployeeLeaveEntitlementAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }

    public function testCreate(): void
    {
        $api = new EmployeeLeaveEntitlementAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->create();
    }

    public function testGetValidationRuleForCreate(): void
    {
        $api = new EmployeeLeaveEntitlementAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForCreate();
    }

    public function testUpdate(): void
    {
        $api = new EmployeeLeaveEntitlementAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->update();
    }

    public function testGetValidationRuleForUpdate(): void
    {
        $api = new EmployeeLeaveEntitlementAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForUpdate();
    }

    public function testGetOne(): void
    {
        $empNumber = 1;
        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $employee->setFirstName('Kayla');
        $employee->setLastName('Abbey');

        $employeeService = $this->getMockBuilder(EmployeeService::class)
            ->onlyMethods(['getEmployeeByEmpNumber'])
            ->getMock();
        $employeeService->expects($this->once())
            ->method('getEmployeeByEmpNumber')
            ->with($empNumber)
            ->willReturn($employee);

        $leaveEntitlementType = new LeaveEntitlementType();
        $leaveEntitlementType->setId(1);
        $leaveEntitlement = new LeaveEntitlement();
        $leaveEntitlement->setNoOfDays(2);
        $leaveEntitlement->setEmployee($employee);
        $leaveEntitlement->setEntitlementType($leaveEntitlementType);

        $leaveEntitlementDao = $this->getMockBuilder(LeaveEntitlementDao::class)
            ->onlyMethods(['getMatchingEntitlements'])
            ->getMock();
        $leaveEntitlementDao->expects($this->once())
            ->method('getMatchingEntitlements')
            ->with(1, new DateTime('2021-01-01'), new DateTime('2021-12-31'), 1)
            ->willReturn([$leaveEntitlement]);

        $leaveEntitlementService = $this->getMockBuilder(LeaveEntitlementService::class)
            ->onlyMethods(['getLeaveEntitlementDao'])
            ->getMock();
        $leaveEntitlementService->expects($this->once())
            ->method('getLeaveEntitlementDao')
            ->willReturn($leaveEntitlementDao);

        $leavePeriodService = $this->getMockBuilder(LeavePeriodService::class)
            ->onlyMethods(['getCurrentLeavePeriod'])
            ->getMock();
        $leavePeriodService->expects($this->once())
            ->method('getCurrentLeavePeriod')
            ->willReturn(new LeavePeriod(new DateTime('2020-01-01'), new DateTime('2020-12-31')));

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $employeeService,
                Services::LEAVE_ENTITLEMENT_SERVICE => $leaveEntitlementService,
                Services::LEAVE_PERIOD_SERVICE => $leavePeriodService,
            ]
        );

        /** @var MockObject&EmployeeLeaveEntitlementAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeLeaveEntitlementAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ],
                RequestParams::PARAM_TYPE_QUERY => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 1,
                    LeaveCommonParams::PARAMETER_FROM_DATE => '2021-01-01',
                    LeaveCommonParams::PARAMETER_TO_DATE => '2021-12-31',
                    EmployeeLeaveEntitlementAPI::PARAMETER_ENTITLEMENT => 1.5
                ]
            ]
        )->onlyMethods([])->getMock();

        $result = $api->getOne();
        $this->assertEquals(
            [
                'empNumber' => 1,
                'lastName' => 'Abbey',
                'firstName' => 'Kayla',
                'middleName' => '',
                'employeeId' => null,
                'terminationId' => null,
                'entitlement' => [
                    'current' => 2,
                    'updateAs' => 3.5
                ]
            ],
            $result->normalize()
        );
    }

    public function testGetOneWithEmptyEntitlements(): void
    {
        $empNumber = 1;
        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $employee->setFirstName('Kayla');
        $employee->setLastName('Abbey');

        $employeeService = $this->getMockBuilder(EmployeeService::class)
            ->onlyMethods(['getEmployeeByEmpNumber'])
            ->getMock();
        $employeeService->expects($this->once())
            ->method('getEmployeeByEmpNumber')
            ->with($empNumber)
            ->willReturn($employee);

        $leaveEntitlementDao = $this->getMockBuilder(LeaveEntitlementDao::class)
            ->onlyMethods(['getMatchingEntitlements'])
            ->getMock();
        $leaveEntitlementDao->expects($this->once())
            ->method('getMatchingEntitlements')
            ->with(1, new DateTime('2020-01-01'), new DateTime('2020-12-31'), 1)
            ->willReturn([]);

        $leaveEntitlementService = $this->getMockBuilder(LeaveEntitlementService::class)
            ->onlyMethods(['getLeaveEntitlementDao'])
            ->getMock();
        $leaveEntitlementService->expects($this->once())
            ->method('getLeaveEntitlementDao')
            ->willReturn($leaveEntitlementDao);

        $leavePeriodService = $this->getMockBuilder(LeavePeriodService::class)
            ->onlyMethods(['getCurrentLeavePeriod'])
            ->getMock();
        $leavePeriodService->expects($this->once())
            ->method('getCurrentLeavePeriod')
            ->willReturn(new LeavePeriod(new DateTime('2020-01-01'), new DateTime('2020-12-31')));

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $employeeService,
                Services::LEAVE_ENTITLEMENT_SERVICE => $leaveEntitlementService,
                Services::LEAVE_PERIOD_SERVICE => $leavePeriodService,
            ]
        );

        /** @var MockObject&EmployeeLeaveEntitlementAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeLeaveEntitlementAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ],
                RequestParams::PARAM_TYPE_QUERY => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 1,
                    EmployeeLeaveEntitlementAPI::PARAMETER_ENTITLEMENT => 1.5
                ]
            ]
        )->onlyMethods([])->getMock();

        $result = $api->getOne();
        $this->assertEquals(
            [
                'empNumber' => 1,
                'lastName' => 'Abbey',
                'firstName' => 'Kayla',
                'middleName' => '',
                'employeeId' => null,
                'terminationId' => null,
                'entitlement' => [
                    'current' => 0,
                    'updateAs' => 1.5
                ]
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForGetOne(): void
    {
        $calledGetLeaveTypeById = 7;
        $calledEmployeeNumberValidation = 7;
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAccessibleEntityIds'])
            ->getMock();
        $userRoleManager->expects($this->exactly($calledEmployeeNumberValidation))
            ->method('getAccessibleEntityIds')
            ->willReturn([1, 2]);

        $authUser = $this->getMockBuilder(User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->exactly($calledEmployeeNumberValidation))
            ->method('getEmpNumber')
            ->willReturn(1);

        $leaveTypeDao = $this->getMockBuilder(LeaveTypeDao::class)
            ->onlyMethods(['getLeaveTypeById'])
            ->getMock();
        $leaveTypeDao->expects($this->exactly($calledGetLeaveTypeById))
            ->method('getLeaveTypeById')
            ->willReturnMap(
                [
                    [50, new LeaveType()],
                    [100, null],
                ]
            );

        $leaveTypeService = $this->getMockBuilder(LeaveTypeService::class)
            ->onlyMethods(['getLeaveTypeDao'])
            ->getMock();
        $leaveTypeService->expects($this->exactly($calledGetLeaveTypeById))
            ->method('getLeaveTypeDao')
            ->willReturn($leaveTypeDao);

        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::AUTH_USER => $authUser,
                Services::LEAVE_TYPE_SERVICE => $leaveTypeService,
            ]
        );
        $api = new EmployeeLeaveEntitlementAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetOne();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 2,
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 50,
                ],
                $rules
            )
        );

        $this->assertInvalidParamException(
        // invalid leave type id
            fn () => $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 2,
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 100
                ],
                $rules
            ),
            [LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID]
        );

        $this->assertInvalidParamException(
        // inaccessible employee number
            fn () => $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 3,
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 50
                ],
                $rules
            ),
            [CommonParams::PARAMETER_EMP_NUMBER]
        );

        $queryParams = [
            CommonParams::PARAMETER_EMP_NUMBER => 2,
            LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 50,
            LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-25',
            LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-24',
        ];
        $api = new EmployeeLeaveEntitlementAPI($this->getRequest($queryParams));
        $rules = $api->getValidationRuleForGetOne();
        $this->assertInvalidParamException(
        // from date < to date
            fn () => $this->validate($queryParams, $rules),
            [LeaveCommonParams::PARAMETER_FROM_DATE]
        );

        $queryParams = [
            CommonParams::PARAMETER_EMP_NUMBER => 2,
            LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 50,
            LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-24',
            LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-24',
        ];
        $api = new EmployeeLeaveEntitlementAPI($this->getRequest($queryParams));
        $rules = $api->getValidationRuleForGetOne();
        $this->assertInvalidParamException(
        // from date != to date
            fn () => $this->validate($queryParams, $rules),
            [LeaveCommonParams::PARAMETER_FROM_DATE]
        );

        $queryParams = [
            CommonParams::PARAMETER_EMP_NUMBER => 2,
            LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 50,
            LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-23'
        ];
        $api = new EmployeeLeaveEntitlementAPI($this->getRequest($queryParams));
        $rules = $api->getValidationRuleForGetOne();
        $this->assertInvalidParamException(
        // if defined only from date, to date also need to define
            fn () => $this->validate($queryParams, $rules),
            [LeaveCommonParams::PARAMETER_FROM_DATE, LeaveCommonParams::PARAMETER_TO_DATE]
        );

        $queryParams = [
            CommonParams::PARAMETER_EMP_NUMBER => 2,
            LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 50,
            LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-24',
        ];
        $api = new EmployeeLeaveEntitlementAPI($this->getRequest($queryParams));
        $rules = $api->getValidationRuleForGetOne();
        $this->assertInvalidParamException(
        // if defined only to date, from date also need to define
            fn () => $this->validate($queryParams, $rules),
            [LeaveCommonParams::PARAMETER_FROM_DATE]
        );
    }

    public function testGetAll(): void
    {
        $empNumber = 1;
        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $employee->setFirstName('Kayla');
        $employee->setLastName('Abbey');

        $employee2 = new Employee();
        $employee2->setEmpNumber(2);
        $employee2->setFirstName('Ashley');
        $employee2->setLastName('Abel');

        $employeeSearchFilterParams = new EmployeeSearchFilterParams();
        $employeeSearchFilterParams->setSubunitId(1);
        $employeeSearchFilterParams->setLocationId(2);
        $employeeService = $this->getMockBuilder(EmployeeService::class)
            ->onlyMethods(['getEmployeeList', 'getEmployeeCount'])
            ->getMock();
        $employeeService->expects($this->once())
            ->method('getEmployeeList')
            ->with($employeeSearchFilterParams)
            ->willReturn([$employee, $employee2]);
        $employeeService->expects($this->once())
            ->method('getEmployeeCount')
            ->with($employeeSearchFilterParams)
            ->willReturn(2);

        $leaveEntitlementType = new LeaveEntitlementType();
        $leaveEntitlementType->setId(1);
        $leaveEntitlement = new LeaveEntitlement();
        $leaveEntitlement->setNoOfDays(2);
        $leaveEntitlement->setEmployee($employee);
        $leaveEntitlement->setEntitlementType($leaveEntitlementType);

        $entitlementSearchFilterParams = new LeaveEntitlementSearchFilterParams();
        $entitlementSearchFilterParams->setEmpNumbers([1, 2]);
        $entitlementSearchFilterParams->setLeaveTypeId(1);
        $entitlementSearchFilterParams->setFromDate(new DateTime('2021-01-01'));
        $entitlementSearchFilterParams->setToDate(new DateTime('2021-12-31'));
        $entitlementSearchFilterParams->setLimit(0);
        $leaveEntitlementDao = $this->getMockBuilder(LeaveEntitlementDao::class)
            ->onlyMethods(['getLeaveEntitlements'])
            ->getMock();
        $leaveEntitlementDao->expects($this->once())
            ->method('getLeaveEntitlements')
            ->with($entitlementSearchFilterParams)
            ->willReturn([$leaveEntitlement]);

        $leaveEntitlementService = $this->getMockBuilder(LeaveEntitlementService::class)
            ->onlyMethods(['getLeaveEntitlementDao'])
            ->getMock();
        $leaveEntitlementService->expects($this->once())
            ->method('getLeaveEntitlementDao')
            ->willReturn($leaveEntitlementDao);

        $leavePeriodService = $this->getMockBuilder(LeavePeriodService::class)
            ->onlyMethods(['getCurrentLeavePeriod'])
            ->getMock();
        $leavePeriodService->expects($this->once())
            ->method('getCurrentLeavePeriod')
            ->willReturn(new LeavePeriod(new DateTime('2020-01-01'), new DateTime('2020-12-31')));

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $employeeService,
                Services::LEAVE_ENTITLEMENT_SERVICE => $leaveEntitlementService,
                Services::LEAVE_PERIOD_SERVICE => $leavePeriodService,
            ]
        );

        /** @var MockObject&EmployeeLeaveEntitlementAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeLeaveEntitlementAPI::class,
            [
                RequestParams::PARAM_TYPE_QUERY => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 1,
                    LeaveCommonParams::PARAMETER_FROM_DATE => '2021-01-01',
                    LeaveCommonParams::PARAMETER_TO_DATE => '2021-12-31',
                    EmployeeLeaveEntitlementAPI::PARAMETER_ENTITLEMENT => 1.5,
                    EmployeeLeaveEntitlementAPI::PARAMETER_LOCATION_ID => 2,
                    EmployeeLeaveEntitlementAPI::PARAMETER_SUBUNIT_ID => 1,
                ]
            ]
        )->onlyMethods([])->getMock();

        $result = $api->getAll();
        $this->assertEquals(
            [
                [
                    'empNumber' => 1,
                    'lastName' => 'Abbey',
                    'firstName' => 'Kayla',
                    'middleName' => '',
                    'employeeId' => null,
                    'terminationId' => null,
                    'entitlement' => [
                        'current' => 2,
                        'updateAs' => 3.5
                    ]
                ],
                [
                    'empNumber' => 2,
                    'lastName' => 'Abel',
                    'firstName' => 'Ashley',
                    'middleName' => '',
                    'employeeId' => null,
                    'terminationId' => null,
                    'entitlement' => [
                        'current' => 0,
                        'updateAs' => 1.5
                    ]
                ]
            ],
            $result->normalize()
        );
        $this->assertEquals(['total' => 2], $result->getMeta()->all());
    }

    public function testGetValidationRuleForGetAll(): void
    {
        $calledGetLeaveTypeById = 9;
        $leaveTypeDao = $this->getMockBuilder(LeaveTypeDao::class)
            ->onlyMethods(['getLeaveTypeById'])
            ->getMock();
        $leaveTypeDao->expects($this->exactly($calledGetLeaveTypeById))
            ->method('getLeaveTypeById')
            ->willReturnMap(
                [
                    [50, new LeaveType()],
                    [100, null],
                ]
            );

        $leaveTypeService = $this->getMockBuilder(LeaveTypeService::class)
            ->onlyMethods(['getLeaveTypeDao'])
            ->getMock();
        $leaveTypeService->expects($this->exactly($calledGetLeaveTypeById))
            ->method('getLeaveTypeDao')
            ->willReturn($leaveTypeDao);

        $this->createKernelWithMockServices([Services::LEAVE_TYPE_SERVICE => $leaveTypeService]);
        $api = new EmployeeLeaveEntitlementAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetAll();
        $this->assertTrue(
            $this->validate(
                [LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 50],
                $rules
            )
        );

        $this->assertInvalidParamException(
        // invalid leave type id
            fn () => $this->validate(
                [LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 100],
                $rules
            ),
            [LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID]
        );

        $this->assertInvalidParamException(
        // unexpected param
            fn () => $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 2,
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 50
                ],
                $rules
            ),
            [CommonParams::PARAMETER_EMP_NUMBER]
        );

        $this->assertTrue(
            $this->validate(
                [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 50,
                    EmployeeLeaveEntitlementAPI::PARAMETER_SUBUNIT_ID => 1,
                    EmployeeLeaveEntitlementAPI::PARAMETER_LOCATION_ID => 2,
                    EmployeeLeaveEntitlementAPI::PARAMETER_ENTITLEMENT => 1.5,
                ],
                $rules
            )
        );

        $this->assertInvalidParamException(
        // invalid param
            fn () => $this->validate(
                [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 50,
                    EmployeeLeaveEntitlementAPI::PARAMETER_SUBUNIT_ID => 'invalid',
                    EmployeeLeaveEntitlementAPI::PARAMETER_LOCATION_ID => 'invalid',
                    EmployeeLeaveEntitlementAPI::PARAMETER_ENTITLEMENT => 'invalid',
                ],
                $rules
            ),
            [
                EmployeeLeaveEntitlementAPI::PARAMETER_SUBUNIT_ID,
                EmployeeLeaveEntitlementAPI::PARAMETER_LOCATION_ID,
                EmployeeLeaveEntitlementAPI::PARAMETER_ENTITLEMENT
            ]
        );

        $queryParams = [
            LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 50,
            LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-25',
            LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-24',
        ];
        $api = new EmployeeLeaveEntitlementAPI($this->getRequest($queryParams));
        $rules = $api->getValidationRuleForGetAll();
        $this->assertInvalidParamException(
        // from date < to date
            fn () => $this->validate($queryParams, $rules),
            [LeaveCommonParams::PARAMETER_FROM_DATE]
        );

        $queryParams = [
            LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 50,
            LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-24',
            LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-24',
        ];
        $api = new EmployeeLeaveEntitlementAPI($this->getRequest($queryParams));
        $rules = $api->getValidationRuleForGetAll();
        $this->assertInvalidParamException(
        // from date != to date
            fn () => $this->validate($queryParams, $rules),
            [LeaveCommonParams::PARAMETER_FROM_DATE]
        );

        $queryParams = [
            LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 50,
            LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-23'
        ];
        $api = new EmployeeLeaveEntitlementAPI($this->getRequest($queryParams));
        $rules = $api->getValidationRuleForGetAll();
        $this->assertInvalidParamException(
        // if defined only from date, to date also need to define
            fn () => $this->validate($queryParams, $rules),
            [LeaveCommonParams::PARAMETER_FROM_DATE, LeaveCommonParams::PARAMETER_TO_DATE]
        );

        $queryParams = [
            LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 50,
            LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-24',
        ];
        $api = new EmployeeLeaveEntitlementAPI($this->getRequest($queryParams));
        $rules = $api->getValidationRuleForGetAll();
        $this->assertInvalidParamException(
        // if defined only to date, from date also need to define
            fn () => $this->validate($queryParams, $rules),
            [LeaveCommonParams::PARAMETER_FROM_DATE]
        );
    }
}
