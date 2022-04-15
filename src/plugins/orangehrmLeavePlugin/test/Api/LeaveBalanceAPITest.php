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
use Generator;
use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Authentication\Auth\User;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Core\Helper\ClassHelper;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Service\NormalizerService;
use OrangeHRM\Entity\LeaveType;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Api\LeaveBalanceAPI;
use OrangeHRM\Leave\Api\LeaveCommonParams;
use OrangeHRM\Leave\Dao\LeaveTypeDao;
use OrangeHRM\Leave\Dto\LeaveDuration;
use OrangeHRM\Leave\Dto\LeaveParameterObject;
use OrangeHRM\Leave\Service\HolidayService;
use OrangeHRM\Leave\Service\LeaveApplicationService;
use OrangeHRM\Leave\Service\LeaveConfigurationService;
use OrangeHRM\Leave\Service\LeaveEntitlementService;
use OrangeHRM\Leave\Service\LeavePeriodService;
use OrangeHRM\Leave\Service\LeaveRequestService;
use OrangeHRM\Leave\Service\LeaveTypeService;
use OrangeHRM\Leave\Service\WorkScheduleService;
use OrangeHRM\Leave\Service\WorkWeekService;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Leave
 * @group APIv2
 */
class LeaveBalanceAPITest extends EndpointTestCase
{
    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmLeavePlugin/test/fixtures/LeaveBalanceAPI.yml';
        TestDataService::populate($fixture);
    }

    public function testDelete(): void
    {
        $api = new LeaveBalanceAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new LeaveBalanceAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }

    public function testUpdate(): void
    {
        $api = new LeaveBalanceAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->update();
    }

    public function testGetValidationRuleForUpdate(): void
    {
        $api = new LeaveBalanceAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForUpdate();
    }

    public function testGetLeaveApplicationService(): void
    {
        $api = $this->getMockBuilder(LeaveBalanceAPI::class)
            ->onlyMethods([])
            ->setConstructorArgs([$this->getRequest()])
            ->getMock();
        $leaveApplicationService = $this->invokeProtectedMethodOnMock(
            LeaveBalanceAPI::class,
            $api,
            'getLeaveApplicationService'
        );
        $this->assertTrue($leaveApplicationService instanceof LeaveApplicationService);
    }

    /**
     * @dataProvider getOneOnlyWithLeaveTypeIdDataProvider
     */
    public function testGetOneOnlyWithLeaveTypeId(
        array $expected,
        array $expectedMeta,
        array $requestParams,
        int $empNumber,
        DateTime $now
    ): void {
        $authUser = $this->getMockBuilder(User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->once())
            ->method('getEmpNumber')
            ->willReturn($empNumber);

        $dateTimeHelperService = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelperService->expects($this->exactly(2))
            ->method('getNow')
            ->willReturnCallback(fn () => clone $now);

        $this->createKernelWithMockServices(
            [
                Services::AUTH_USER => $authUser,
                Services::LEAVE_ENTITLEMENT_SERVICE => new LeaveEntitlementService(),
                Services::LEAVE_CONFIG_SERVICE => new LeaveConfigurationService(),
                Services::LEAVE_PERIOD_SERVICE => new LeavePeriodService(),
                Services::DATETIME_HELPER_SERVICE => $dateTimeHelperService,
                Services::NORMALIZER_SERVICE => new NormalizerService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::LEAVE_REQUEST_SERVICE => new LeaveRequestService(),
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::LEAVE_TYPE_SERVICE => new LeaveTypeService(),
            ]
        );
        /** @var MockObject&LeaveBalanceAPI $api */
        $api = $this->getApiEndpointMockBuilder(LeaveBalanceAPI::class, $requestParams)
            ->onlyMethods([])
            ->getMock();

        $result = $api->getOne();
        $this->assertEquals($expected, $result->normalize());
        $this->assertEquals($expectedMeta, $result->getMeta()->all());
    }

    public function getOneOnlyWithLeaveTypeIdDataProvider(): Generator
    {
        yield [
            [
                'balance' => [
                    'entitled' => 3.0,
                    'used' => 0.0,
                    'scheduled' => 0.0,
                    'pending' => 0.0,
                    'taken' => 0.0,
                    'balance' => 3.0,
                    'asAtDate' => '2021-08-18',
                    'endDate' => '2021-12-31',
                ]
            ],
            [
                'employee' => [
                    'empNumber' => 1,
                    'firstName' => 'Kayla',
                    'lastName' => 'Abbey',
                    'middleName' => 'T',
                    'employeeId' => '0001',
                    'terminationId' => null
                ],
                'leaveType' => [
                    'id' => 1,
                    'name' => 'Casual',
                    'deleted' => false,
                    'situational' => false,
                ]
            ],
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 1
                ]
            ],
            1,
            new DateTime('2021-08-18'),
        ];
        yield [
            [
                'balance' => [
                    'entitled' => 10.0,
                    'used' => 3.0,
                    'scheduled' => 0.0,
                    'pending' => 2.0,
                    'taken' => 1.0,
                    'balance' => 7.0,
                    'asAtDate' => '2021-08-18',
                    'endDate' => '2021-12-31'
                ]
            ],
            [
                'employee' => [
                    'empNumber' => 1,
                    'firstName' => 'Kayla',
                    'lastName' => 'Abbey',
                    'middleName' => 'T',
                    'employeeId' => '0001',
                    'terminationId' => null
                ],
                'leaveType' => [
                    'id' => 2,
                    'name' => 'Medical',
                    'deleted' => false,
                    'situational' => false,
                ]
            ],
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 2
                ]
            ],
            1,
            new DateTime('2021-08-18'),
        ];

        // Employee 1 does not have entitlement for leave type id 3
        yield [
            [
                'balance' => [
                    'entitled' => 0.0,
                    'used' => 0.0,
                    'scheduled' => 0.0,
                    'pending' => 0.0,
                    'taken' => 0.0,
                    'balance' => 0.0,
                    'asAtDate' => '2021-08-18',
                    'endDate' => '2021-12-31'
                ]
            ],
            [
                'employee' => [
                    'empNumber' => 1,
                    'firstName' => 'Kayla',
                    'lastName' => 'Abbey',
                    'middleName' => 'T',
                    'employeeId' => '0001',
                    'terminationId' => null
                ],
                'leaveType' => [
                    'id' => 3,
                    'name' => 'Company',
                    'deleted' => true,
                    'situational' => false,
                ]
            ],
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 3
                ]
            ],
            1,
            new DateTime('2021-08-18'),
        ];

        // Employee 2 does not have entitlement for leave type id 1
        yield [
            [
                'balance' => [
                    'entitled' => 0.0,
                    'used' => 0.0,
                    'scheduled' => 0.0,
                    'pending' => 0.0,
                    'taken' => 0.0,
                    'balance' => 0.0,
                    'asAtDate' => '2021-08-18',
                    'endDate' => '2021-12-31'
                ]
            ],
            [
                'employee' => [
                    'empNumber' => 2,
                    'firstName' => 'Ashley',
                    'lastName' => 'Abel',
                    'middleName' => 'ST',
                    'employeeId' => '0002',
                    'terminationId' => null
                ],
                'leaveType' => [
                    'id' => 1,
                    'name' => 'Casual',
                    'deleted' => false,
                    'situational' => false,
                ]
            ],
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 1
                ]
            ],
            2,
            new DateTime('2021-08-18'),
        ];

        // Employee 100 does not exist
        yield [
            [
                'balance' => [
                    'entitled' => 0.0,
                    'used' => 0.0,
                    'scheduled' => 0.0,
                    'pending' => 0.0,
                    'taken' => 0.0,
                    'balance' => 0.0,
                    'asAtDate' => '2021-08-18',
                    'endDate' => '2021-12-31'
                ]
            ],
            [
                'employee' => null,
                'leaveType' => [
                    'id' => 1,
                    'name' => 'Casual',
                    'deleted' => false,
                    'situational' => false,
                ]
            ],
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 1
                ]
            ],
            100,
            new DateTime('2021-08-18'),
        ];
    }

    /**
     * @dataProvider getOneSingleDayDataProvider
     */
    public function testGetOneSingleDay(
        array $expected,
        array $expectedMeta,
        array $requestParams,
        int $empNumber
    ): void {
        $this->createKernelWithMockServices(
            [
                Services::USER_SERVICE => new UserService(),
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::CLASS_HELPER => new ClassHelper(),
            ]
        );
        $userRoleManager = new BasicUserRoleManager();
        $userRoleManager->setUser($this->getEntityReference(\OrangeHRM\Entity\User::class, 1));
        $authUser = $this->getMockBuilder(User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->once())
            ->method('getEmpNumber')
            ->willReturn($empNumber);

        $dateTimeHelperService = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelperService->expects($this->once())
            ->method('getNow')
            ->willReturn(new DateTime('2021-09-10'));

        $this->createKernelWithMockServices(
            [
                Services::AUTH_USER => $authUser,
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::LEAVE_ENTITLEMENT_SERVICE => new LeaveEntitlementService(),
                Services::LEAVE_CONFIG_SERVICE => new LeaveConfigurationService(),
                Services::LEAVE_PERIOD_SERVICE => new LeavePeriodService(),
                Services::WORK_SCHEDULE_SERVICE => new WorkScheduleService(),
                Services::WORK_WEEK_SERVICE => new WorkWeekService(),
                Services::HOLIDAY_SERVICE => new HolidayService(),
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::CONFIG_SERVICE => new ConfigService(),
                Services::DATETIME_HELPER_SERVICE => $dateTimeHelperService,
                Services::NORMALIZER_SERVICE => new NormalizerService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::LEAVE_REQUEST_SERVICE => new LeaveRequestService(),
                Services::LEAVE_TYPE_SERVICE => new LeaveTypeService(),
            ]
        );
        /** @var MockObject&LeaveBalanceAPI $api */
        $api = $this->getApiEndpointMockBuilder(LeaveBalanceAPI::class, $requestParams)
            ->onlyMethods([])
            ->getMock();

        $result = $api->getOne();
        $this->assertEquals($expected, $result->normalize());
        $this->assertEquals($expectedMeta, $result->getMeta()->all());
    }

    public function getOneSingleDayDataProvider(): Generator
    {
        $expectedPeriod = [
            'startDate' => '2021-01-01',
            'endDate' => '2021-12-31',
        ];

        /**
         * Single day - Full Day
         */
        yield [
            [
                'negative' => false,
                'breakdown' => [
                    [
                        'period' => $expectedPeriod,
                        'balance' => [
                            'entitled' => 3.0,
                            'used' => 0.0,
                            'scheduled' => 0.0,
                            'pending' => 0.0,
                            'taken' => 0.0,
                            'balance' => 3.0,
                            'asAtDate' => '2021-08-17',
                            'endDate' => '2021-12-31',
                        ],
                        'leaves' => [
                            [
                                'balance' => 2.0,
                                'date' => '2021-08-17',
                                'length' => 1,
                                'status' => null,
                            ]
                        ]
                    ]
                ],
            ],
            [
                'employee' => [
                    'empNumber' => 1,
                    'firstName' => 'Kayla',
                    'lastName' => 'Abbey',
                    'middleName' => 'T',
                    'employeeId' => '0001',
                    'terminationId' => null
                ],
                'leaveType' => [
                    'id' => 1,
                    'name' => 'Casual',
                    'deleted' => false,
                    'situational' => false,
                ]
            ],
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 1,
                ],
                RequestParams::PARAM_TYPE_QUERY => [
                    LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-17',
                    LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-17',
                ]
            ],
            1,
        ];
        yield [
            [
                'negative' => false,
                'breakdown' => [
                    [
                        'period' => $expectedPeriod,
                        'balance' => [
                            'entitled' => 10.0,
                            'used' => 3.0,
                            'scheduled' => 0.0,
                            'pending' => 2.0,
                            'taken' => 1.0,
                            'balance' => 7.0,
                            'asAtDate' => '2021-08-17',
                            'endDate' => '2021-12-31'
                        ],
                        'leaves' => [
                            [
                                'balance' => 6.0,
                                'date' => '2021-08-17',
                                'length' => 1,
                                'status' => null,
                            ]
                        ]
                    ]
                ],
            ],
            [
                'employee' => [
                    'empNumber' => 1,
                    'firstName' => 'Kayla',
                    'lastName' => 'Abbey',
                    'middleName' => 'T',
                    'employeeId' => '0001',
                    'terminationId' => null
                ],
                'leaveType' => [
                    'id' => 2,
                    'name' => 'Medical',
                    'deleted' => false,
                    'situational' => false,
                ]
            ],
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 2,
                ],
                RequestParams::PARAM_TYPE_QUERY => [
                    LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-17',
                    LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-17',
                ]
            ],
            1,
        ];

        $zeroBalanceResult = [
            'negative' => true,
            'breakdown' => [
                [
                    'period' => $expectedPeriod,
                    'balance' => [
                        'entitled' => 0.0,
                        'used' => 0.0,
                        'scheduled' => 0.0,
                        'pending' => 0.0,
                        'taken' => 0.0,
                        'balance' => 0.0,
                        'asAtDate' => '2021-08-17',
                        'endDate' => '2021-12-31'
                    ],
                    'leaves' => [
                        [
                            'balance' => -1.0,
                            'date' => '2021-08-17',
                            'length' => 1,
                            'status' => null,
                        ]
                    ]
                ]
            ],
        ];

        // Employee 1 does not have entitlement for leave type id 3
        yield [
            $zeroBalanceResult,
            [
                'employee' => [
                    'empNumber' => 1,
                    'firstName' => 'Kayla',
                    'lastName' => 'Abbey',
                    'middleName' => 'T',
                    'employeeId' => '0001',
                    'terminationId' => null
                ],
                'leaveType' => [
                    'id' => 3,
                    'name' => 'Company',
                    'deleted' => true,
                    'situational' => false,
                ]
            ],
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 3
                ],
                RequestParams::PARAM_TYPE_QUERY => [
                    LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-17',
                    LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-17',
                ],
            ],
            1,
        ];

        // Employee 2 does not have entitlement for leave type id 1
        yield [
            $zeroBalanceResult,
            [
                'employee' => [
                    'empNumber' => 2,
                    'firstName' => 'Ashley',
                    'lastName' => 'Abel',
                    'middleName' => 'ST',
                    'employeeId' => '0002',
                    'terminationId' => null
                ],
                'leaveType' => [
                    'id' => 1,
                    'name' => 'Casual',
                    'deleted' => false,
                    'situational' => false,
                ]
            ],
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 1
                ],
                RequestParams::PARAM_TYPE_QUERY => [
                    LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-17',
                    LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-17',
                ],
            ],
            2,
        ];

        // Employee 100 does not exist
        yield [
            $zeroBalanceResult,
            [
                'employee' => null,
                'leaveType' => [
                    'id' => 1,
                    'name' => 'Casual',
                    'deleted' => false,
                    'situational' => false,
                ]
            ],
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 1
                ],
                RequestParams::PARAM_TYPE_QUERY => [
                    LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-17',
                    LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-17',
                ],
            ],
            100
        ];

        /**
         * Single day - Half day morning
         */
        yield [
            [
                'negative' => false,
                'breakdown' => [
                    [
                        'period' => $expectedPeriod,
                        'balance' => [
                            'entitled' => 3.0,
                            'used' => 0.0,
                            'scheduled' => 0.0,
                            'pending' => 0.0,
                            'taken' => 0.0,
                            'balance' => 3.0,
                            'asAtDate' => '2021-08-17',
                            'endDate' => '2021-12-31',
                        ],
                        'leaves' => [
                            [
                                'balance' => 2.5,
                                'date' => '2021-08-17',
                                'length' => 0.5,
                                'status' => null,
                            ]
                        ]
                    ]
                ],
            ],
            [
                'employee' => [
                    'empNumber' => 1,
                    'firstName' => 'Kayla',
                    'lastName' => 'Abbey',
                    'middleName' => 'T',
                    'employeeId' => '0001',
                    'terminationId' => null
                ],
                'leaveType' => [
                    'id' => 1,
                    'name' => 'Casual',
                    'deleted' => false,
                    'situational' => false,
                ]
            ],
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 1,
                ],
                RequestParams::PARAM_TYPE_QUERY => [
                    LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-17',
                    LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-17',
                    LeaveCommonParams::PARAMETER_DURATION => [
                        LeaveCommonParams::PARAMETER_DURATION_TYPE => LeaveDuration::HALF_DAY_MORNING
                    ]
                ]
            ],
            1,
        ];

        /**
         * Single day - Half day afternoon
         */
        yield [
            [
                'negative' => false,
                'breakdown' => [
                    [
                        'period' => $expectedPeriod,
                        'balance' => [
                            'entitled' => 3.0,
                            'used' => 0.0,
                            'scheduled' => 0.0,
                            'pending' => 0.0,
                            'taken' => 0.0,
                            'balance' => 3.0,
                            'asAtDate' => '2021-08-17',
                            'endDate' => '2021-12-31',
                        ],
                        'leaves' => [
                            [
                                'balance' => 2.5,
                                'date' => '2021-08-17',
                                'length' => 0.5,
                                'status' => null,
                            ]
                        ]
                    ]
                ],
            ],
            [
                'employee' => [
                    'empNumber' => 1,
                    'firstName' => 'Kayla',
                    'lastName' => 'Abbey',
                    'middleName' => 'T',
                    'employeeId' => '0001',
                    'terminationId' => null
                ],
                'leaveType' => [
                    'id' => 1,
                    'name' => 'Casual',
                    'deleted' => false,
                    'situational' => false,
                ]
            ],
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 1,
                ],
                RequestParams::PARAM_TYPE_QUERY => [
                    LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-17',
                    LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-17',
                    LeaveCommonParams::PARAMETER_DURATION => [
                        LeaveCommonParams::PARAMETER_DURATION_TYPE => LeaveDuration::HALF_DAY_AFTERNOON
                    ]
                ]
            ],
            1,
        ];

        /**
         * Single day - Specify time
         */
        yield [
            [
                'negative' => false,
                'breakdown' => [
                    [
                        'period' => $expectedPeriod,
                        'balance' => [
                            'entitled' => 3.0,
                            'used' => 0.0,
                            'scheduled' => 0.0,
                            'pending' => 0.0,
                            'taken' => 0.0,
                            'balance' => 3.0,
                            'asAtDate' => '2021-08-17',
                            'endDate' => '2021-12-31',
                        ],
                        'leaves' => [
                            [
                                'balance' => 2.75,
                                'date' => '2021-08-17',
                                'length' => 0.25,
                                'status' => null,
                            ]
                        ]
                    ]
                ],
            ],
            [
                'employee' => [
                    'empNumber' => 1,
                    'firstName' => 'Kayla',
                    'lastName' => 'Abbey',
                    'middleName' => 'T',
                    'employeeId' => '0001',
                    'terminationId' => null
                ],
                'leaveType' => [
                    'id' => 1,
                    'name' => 'Casual',
                    'deleted' => false,
                    'situational' => false,
                ]
            ],
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 1,
                ],
                RequestParams::PARAM_TYPE_QUERY => [
                    LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-17',
                    LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-17',
                    LeaveCommonParams::PARAMETER_DURATION => [
                        LeaveCommonParams::PARAMETER_DURATION_TYPE => LeaveDuration::SPECIFY_TIME,
                        LeaveCommonParams::PARAMETER_DURATION_FROM_TIME => '09:00',
                        LeaveCommonParams::PARAMETER_DURATION_TO_TIME => '11:00',
                    ]
                ]
            ],
            1,
        ];

        yield [
            [
                'negative' => false,
                'breakdown' => [
                    [
                        'period' => $expectedPeriod,
                        'balance' => [
                            'entitled' => 3.0,
                            'used' => 0.0,
                            'scheduled' => 0.0,
                            'pending' => 0.0,
                            'taken' => 0.0,
                            'balance' => 3.0,
                            'asAtDate' => '2021-08-17',
                            'endDate' => '2021-12-31',
                        ],
                        'leaves' => [
                            [
                                'balance' => 2.875,
                                'date' => '2021-08-17',
                                'length' => 0.125,
                                'status' => null,
                            ]
                        ]
                    ]
                ],
            ],
            [
                'employee' => [
                    'empNumber' => 1,
                    'firstName' => 'Kayla',
                    'lastName' => 'Abbey',
                    'middleName' => 'T',
                    'employeeId' => '0001',
                    'terminationId' => null
                ],
                'leaveType' => [
                    'id' => 1,
                    'name' => 'Casual',
                    'deleted' => false,
                    'situational' => false,
                ]
            ],
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 1,
                ],
                RequestParams::PARAM_TYPE_QUERY => [
                    LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-17',
                    LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-17',
                    LeaveCommonParams::PARAMETER_DURATION => [
                        LeaveCommonParams::PARAMETER_DURATION_TYPE => LeaveDuration::SPECIFY_TIME,
                        LeaveCommonParams::PARAMETER_DURATION_FROM_TIME => '12:00',
                        LeaveCommonParams::PARAMETER_DURATION_TO_TIME => '13:00',
                    ]
                ]
            ],
            1,
        ];

        /**
         * Single day - Holiday - Full Day
         */
        yield [
            [
                'negative' => false,
                'breakdown' => [
                    [
                        'period' => $expectedPeriod,
                        'balance' => [
                            'entitled' => 3.0,
                            'used' => 0.0,
                            'scheduled' => 0.0,
                            'pending' => 0.0,
                            'taken' => 0.0,
                            'balance' => 3.0,
                            'asAtDate' => '2021-08-25',
                            'endDate' => '2021-12-31',
                        ],
                        'leaves' => [
                            [
                                'balance' => 3.0,
                                'date' => '2021-08-25',
                                'length' => 0,
                                'status' => [
                                    'key' => 5,
                                    'name' => 'Holiday'
                                ],
                            ]
                        ]
                    ]
                ],
            ],
            [
                'employee' => [
                    'empNumber' => 1,
                    'firstName' => 'Kayla',
                    'lastName' => 'Abbey',
                    'middleName' => 'T',
                    'employeeId' => '0001',
                    'terminationId' => null
                ],
                'leaveType' => [
                    'id' => 1,
                    'name' => 'Casual',
                    'deleted' => false,
                    'situational' => false,
                ]
            ],
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 1,
                ],
                RequestParams::PARAM_TYPE_QUERY => [
                    LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-25',
                    LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-25',
                    LeaveCommonParams::PARAMETER_DURATION => [
                        LeaveCommonParams::PARAMETER_DURATION_TYPE => LeaveDuration::FULL_DAY,
                    ]
                ]
            ],
            1,
        ];

        /**
         * Single day - Holiday - Half Day
         */
        yield [
            [
                'negative' => false,
                'breakdown' => [
                    [
                        'period' => $expectedPeriod,
                        'balance' => [
                            'entitled' => 3.0,
                            'used' => 0.0,
                            'scheduled' => 0.0,
                            'pending' => 0.0,
                            'taken' => 0.0,
                            'balance' => 3.0,
                            'asAtDate' => '2021-08-04',
                            'endDate' => '2021-12-31',
                        ],
                        'leaves' => [
                            [
                                'balance' => 2.5,
                                'date' => '2021-08-04',
                                'length' => 0.5,
                                'status' => null
                            ]
                        ]
                    ]
                ],
            ],
            [
                'employee' => [
                    'empNumber' => 1,
                    'firstName' => 'Kayla',
                    'lastName' => 'Abbey',
                    'middleName' => 'T',
                    'employeeId' => '0001',
                    'terminationId' => null
                ],
                'leaveType' => [
                    'id' => 1,
                    'name' => 'Casual',
                    'deleted' => false,
                    'situational' => false,
                ]
            ],
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 1,
                ],
                RequestParams::PARAM_TYPE_QUERY => [
                    LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-04',
                    LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-04',
                    LeaveCommonParams::PARAMETER_DURATION => [
                        LeaveCommonParams::PARAMETER_DURATION_TYPE => LeaveDuration::FULL_DAY,
                    ]
                ]
            ],
            1,
        ];

        /**
         * Single day - Non-working day - Full Day
         */
        yield [
            [
                'negative' => false,
                'breakdown' => [
                    [
                        'period' => $expectedPeriod,
                        'balance' => [
                            'entitled' => 3.0,
                            'used' => 0.0,
                            'scheduled' => 0.0,
                            'pending' => 0.0,
                            'taken' => 0.0,
                            'balance' => 3.0,
                            'asAtDate' => '2021-08-22',
                            'endDate' => '2021-12-31',
                        ],
                        'leaves' => [
                            [
                                'balance' => 3.0,
                                'date' => '2021-08-22',
                                'length' => 0,
                                'status' => [
                                    'key' => 4,
                                    'name' => 'Weekend'
                                ],
                            ]
                        ]
                    ]
                ],
            ],
            [
                'employee' => [
                    'empNumber' => 1,
                    'firstName' => 'Kayla',
                    'lastName' => 'Abbey',
                    'middleName' => 'T',
                    'employeeId' => '0001',
                    'terminationId' => null
                ],
                'leaveType' => [
                    'id' => 1,
                    'name' => 'Casual',
                    'deleted' => false,
                    'situational' => false,
                ]
            ],
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 1,
                ],
                RequestParams::PARAM_TYPE_QUERY => [
                    LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-22',
                    LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-22',
                    LeaveCommonParams::PARAMETER_DURATION => [
                        LeaveCommonParams::PARAMETER_DURATION_TYPE => LeaveDuration::FULL_DAY,
                    ]
                ]
            ],
            1,
        ];

        /**
         * Single day - Non-working day - Half Day
         */
        yield [
            [
                'negative' => false,
                'breakdown' => [
                    [
                        'period' => $expectedPeriod,
                        'balance' => [
                            'entitled' => 3.0,
                            'used' => 0.0,
                            'scheduled' => 0.0,
                            'pending' => 0.0,
                            'taken' => 0.0,
                            'balance' => 3.0,
                            'asAtDate' => '2021-08-21',
                            'endDate' => '2021-12-31',
                        ],
                        'leaves' => [
                            [
                                'balance' => 2.5,
                                'date' => '2021-08-21',
                                'length' => 0.5,
                                'status' => null,
                            ]
                        ]
                    ]
                ],
            ],
            [
                'employee' => [
                    'empNumber' => 1,
                    'firstName' => 'Kayla',
                    'lastName' => 'Abbey',
                    'middleName' => 'T',
                    'employeeId' => '0001',
                    'terminationId' => null
                ],
                'leaveType' => [
                    'id' => 1,
                    'name' => 'Casual',
                    'deleted' => false,
                    'situational' => false,
                ]
            ],
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 1,
                ],
                RequestParams::PARAM_TYPE_QUERY => [
                    LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-21',
                    LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-21',
                    LeaveCommonParams::PARAMETER_DURATION => [
                        LeaveCommonParams::PARAMETER_DURATION_TYPE => LeaveDuration::FULL_DAY,
                    ]
                ]
            ],
            1,
        ];
    }

    /**
     * @dataProvider getOneMultiDayDataProvider
     */
    public function testGetOneMultiDay(
        array $expected,
        array $expectedMeta,
        array $requestParams,
        int $empNumber
    ): void {
        $this->createKernelWithMockServices(
            [
                Services::USER_SERVICE => new UserService(),
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::CLASS_HELPER => new ClassHelper(),
            ]
        );
        $userRoleManager = new BasicUserRoleManager();
        $userRoleManager->setUser($this->getEntityReference(\OrangeHRM\Entity\User::class, 1));
        $authUser = $this->getMockBuilder(User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->once())
            ->method('getEmpNumber')
            ->willReturn($empNumber);

        $dateTimeHelperService = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelperService->expects($this->once())
            ->method('getNow')
            ->willReturn(new DateTime('2021-09-10'));

        $this->createKernelWithMockServices(
            [
                Services::AUTH_USER => $authUser,
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::LEAVE_ENTITLEMENT_SERVICE => new LeaveEntitlementService(),
                Services::LEAVE_CONFIG_SERVICE => new LeaveConfigurationService(),
                Services::LEAVE_PERIOD_SERVICE => new LeavePeriodService(),
                Services::WORK_SCHEDULE_SERVICE => new WorkScheduleService(),
                Services::WORK_WEEK_SERVICE => new WorkWeekService(),
                Services::HOLIDAY_SERVICE => new HolidayService(),
                Services::EMPLOYEE_SERVICE => new EmployeeService(),
                Services::CONFIG_SERVICE => new ConfigService(),
                Services::DATETIME_HELPER_SERVICE => $dateTimeHelperService,
                Services::NORMALIZER_SERVICE => new NormalizerService(),
                Services::CLASS_HELPER => new ClassHelper(),
                Services::LEAVE_REQUEST_SERVICE => new LeaveRequestService(),
                Services::LEAVE_TYPE_SERVICE => new LeaveTypeService(),
            ]
        );
        /** @var MockObject&LeaveBalanceAPI $api */
        $api = $this->getApiEndpointMockBuilder(LeaveBalanceAPI::class, $requestParams)
            ->onlyMethods([])
            ->getMock();

        $result = $api->getOne();
        $this->assertEquals($expected, $result->normalize());
        $this->assertEquals($expectedMeta, $result->getMeta()->all());
    }

    public function getOneMultiDayDataProvider(): Generator
    {
        $expectedPeriod = [
            'startDate' => '2021-01-01',
            'endDate' => '2021-12-31',
        ];

        /**
         * Multi day - Partial Days - None
         */
        yield [
            [
                'negative' => true,
                'breakdown' => [
                    [
                        'period' => $expectedPeriod,
                        'balance' => [
                            'entitled' => 3.0,
                            'used' => 0.0,
                            'scheduled' => 0.0,
                            'pending' => 0.0,
                            'taken' => 0.0,
                            'balance' => 3.0,
                            'asAtDate' => '2021-08-20',
                            'endDate' => '2021-08-24',
                        ],
                        'leaves' => [
                            [
                                'balance' => 2.0,
                                'date' => '2021-08-20',
                                'length' => 1,
                                'status' => null,
                            ],
                            [
                                'balance' => 1.5,
                                'date' => '2021-08-21',
                                'length' => 0.5,
                                'status' => null,
                            ],
                            [
                                'balance' => 1.5,
                                'date' => '2021-08-22',
                                'length' => 0,
                                'status' => [
                                    'key' => 4,
                                    'name' => 'Weekend'
                                ],
                            ],
                            [
                                'balance' => 0.5,
                                'date' => '2021-08-23',
                                'length' => 1,
                                'status' => null,
                            ],
                            [
                                'balance' => -0.5,
                                'date' => '2021-08-24',
                                'length' => 1,
                                'status' => null,
                            ]
                        ]
                    ]
                ],
            ],
            [
                'employee' => [
                    'empNumber' => 1,
                    'firstName' => 'Kayla',
                    'lastName' => 'Abbey',
                    'middleName' => 'T',
                    'employeeId' => '0001',
                    'terminationId' => null
                ],
                'leaveType' => [
                    'id' => 1,
                    'name' => 'Casual',
                    'deleted' => false,
                    'situational' => false,
                ]
            ],
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 1,
                ],
                RequestParams::PARAM_TYPE_QUERY => [
                    LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-20',
                    LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-24',
                    LeaveCommonParams::PARAMETER_PARTIAL_OPTION => LeaveParameterObject::PARTIAL_OPTION_NONE,
                ]
            ],
            1,
        ];

        /**
         * Multi day - Partial Days - All
         */
        yield [
            [
                'negative' => false,
                'breakdown' => [
                    [
                        'period' => $expectedPeriod,
                        'balance' => [
                            'entitled' => 3.0,
                            'used' => 0.0,
                            'scheduled' => 0.0,
                            'pending' => 0.0,
                            'taken' => 0.0,
                            'balance' => 3.0,
                            'asAtDate' => '2021-08-20',
                            'endDate' => '2021-08-24',
                        ],
                        'leaves' => [
                            [
                                'balance' => 2.5,
                                'date' => '2021-08-20',
                                'length' => 0.5,
                                'status' => null,
                            ],
                            [
                                'balance' => 2.0,
                                'date' => '2021-08-21',
                                'length' => 0.5,
                                'status' => null,
                            ],
                            [
                                'balance' => 2.0,
                                'date' => '2021-08-22',
                                'length' => 0,
                                'status' => [
                                    'key' => 4,
                                    'name' => 'Weekend'
                                ],
                            ],
                            [
                                'balance' => 1.5,
                                'date' => '2021-08-23',
                                'length' => 0.5,
                                'status' => null,
                            ],
                            [
                                'balance' => 1.0,
                                'date' => '2021-08-24',
                                'length' => 0.5,
                                'status' => null,
                            ]
                        ]
                    ]
                ],
            ],
            [
                'employee' => [
                    'empNumber' => 1,
                    'firstName' => 'Kayla',
                    'lastName' => 'Abbey',
                    'middleName' => 'T',
                    'employeeId' => '0001',
                    'terminationId' => null
                ],
                'leaveType' => [
                    'id' => 1,
                    'name' => 'Casual',
                    'deleted' => false,
                    'situational' => false,
                ]
            ],
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 1,
                ],
                RequestParams::PARAM_TYPE_QUERY => [
                    LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-20',
                    LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-24',
                    LeaveCommonParams::PARAMETER_PARTIAL_OPTION => LeaveParameterObject::PARTIAL_OPTION_ALL,
                    LeaveCommonParams::PARAMETER_DURATION => [
                        LeaveCommonParams::PARAMETER_DURATION_TYPE => LeaveDuration::HALF_DAY_AFTERNOON
                    ],
                ]
            ],
            1,
        ];

        /**
         * Multi day - Partial Days - Start
         */
        yield [
            [
                'negative' => false,
                'breakdown' => [
                    [
                        'period' => $expectedPeriod,
                        'balance' => [
                            'entitled' => 3.0,
                            'used' => 0.0,
                            'scheduled' => 0.0,
                            'pending' => 0.0,
                            'taken' => 0.0,
                            'balance' => 3.0,
                            'asAtDate' => '2021-08-20',
                            'endDate' => '2021-08-24',
                        ],
                        'leaves' => [
                            [
                                'balance' => 2.5,
                                'date' => '2021-08-20',
                                'length' => 0.5,
                                'status' => null,
                            ],
                            [
                                'balance' => 2.0,
                                'date' => '2021-08-21',
                                'length' => 0.5,
                                'status' => null,
                            ],
                            [
                                'balance' => 2.0,
                                'date' => '2021-08-22',
                                'length' => 0,
                                'status' => [
                                    'key' => 4,
                                    'name' => 'Weekend'
                                ],
                            ],
                            [
                                'balance' => 1.0,
                                'date' => '2021-08-23',
                                'length' => 1,
                                'status' => null,
                            ],
                            [
                                'balance' => 0.0,
                                'date' => '2021-08-24',
                                'length' => 1,
                                'status' => null,
                            ]
                        ]
                    ]
                ],
            ],
            [
                'employee' => [
                    'empNumber' => 1,
                    'firstName' => 'Kayla',
                    'lastName' => 'Abbey',
                    'middleName' => 'T',
                    'employeeId' => '0001',
                    'terminationId' => null
                ],
                'leaveType' => [
                    'id' => 1,
                    'name' => 'Casual',
                    'deleted' => false,
                    'situational' => false,
                ]
            ],
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 1,
                ],
                RequestParams::PARAM_TYPE_QUERY => [
                    LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-20',
                    LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-24',
                    LeaveCommonParams::PARAMETER_PARTIAL_OPTION => LeaveParameterObject::PARTIAL_OPTION_START,
                    LeaveCommonParams::PARAMETER_DURATION => [
                        LeaveCommonParams::PARAMETER_DURATION_TYPE => LeaveDuration::HALF_DAY_MORNING
                    ],
                ]
            ],
            1,
        ];

        /**
         * Multi day - Partial Days - End
         */
        yield [
            [
                'negative' => false,
                'breakdown' => [
                    [
                        'period' => $expectedPeriod,
                        'balance' => [
                            'entitled' => 3.0,
                            'used' => 0.0,
                            'scheduled' => 0.0,
                            'pending' => 0.0,
                            'taken' => 0.0,
                            'balance' => 3.0,
                            'asAtDate' => '2021-08-20',
                            'endDate' => '2021-08-24',
                        ],
                        'leaves' => [
                            [
                                'balance' => 2.0,
                                'date' => '2021-08-20',
                                'length' => 1,
                                'status' => null,
                            ],
                            [
                                'balance' => 1.5,
                                'date' => '2021-08-21',
                                'length' => 0.5,
                                'status' => null,
                            ],
                            [
                                'balance' => 1.5,
                                'date' => '2021-08-22',
                                'length' => 0,
                                'status' => [
                                    'key' => 4,
                                    'name' => 'Weekend'
                                ],
                            ],
                            [
                                'balance' => 0.5,
                                'date' => '2021-08-23',
                                'length' => 1,
                                'status' => null,
                            ],
                            [
                                'balance' => 0.0,
                                'date' => '2021-08-24',
                                'length' => 0.5,
                                'status' => null,
                            ]
                        ]
                    ]
                ],
            ],
            [
                'employee' => [
                    'empNumber' => 1,
                    'firstName' => 'Kayla',
                    'lastName' => 'Abbey',
                    'middleName' => 'T',
                    'employeeId' => '0001',
                    'terminationId' => null
                ],
                'leaveType' => [
                    'id' => 1,
                    'name' => 'Casual',
                    'deleted' => false,
                    'situational' => false,
                ]
            ],
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 1,
                ],
                RequestParams::PARAM_TYPE_QUERY => [
                    LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-20',
                    LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-24',
                    LeaveCommonParams::PARAMETER_PARTIAL_OPTION => LeaveParameterObject::PARTIAL_OPTION_END,
                    LeaveCommonParams::PARAMETER_DURATION => [
                        LeaveCommonParams::PARAMETER_DURATION_TYPE => LeaveDuration::HALF_DAY_MORNING
                    ],
                ]
            ],
            1,
        ];

        /**
         * Multi day - Partial Days - Start End
         */
        yield [
            [
                'negative' => false,
                'breakdown' => [
                    [
                        'period' => $expectedPeriod,
                        'balance' => [
                            'entitled' => 3.0,
                            'used' => 0.0,
                            'scheduled' => 0.0,
                            'pending' => 0.0,
                            'taken' => 0.0,
                            'balance' => 3.0,
                            'asAtDate' => '2021-08-20',
                            'endDate' => '2021-08-24',
                        ],
                        'leaves' => [
                            [
                                'balance' => 2.5,
                                'date' => '2021-08-20',
                                'length' => 0.5,
                                'status' => null,
                            ],
                            [
                                'balance' => 2,
                                'date' => '2021-08-21',
                                'length' => 0.5,
                                'status' => null,
                            ],
                            [
                                'balance' => 2,
                                'date' => '2021-08-22',
                                'length' => 0,
                                'status' => [
                                    'key' => 4,
                                    'name' => 'Weekend'
                                ],
                            ],
                            [
                                'balance' => 1,
                                'date' => '2021-08-23',
                                'length' => 1,
                                'status' => null,
                            ],
                            [
                                'balance' => 0.5,
                                'date' => '2021-08-24',
                                'length' => 0.5,
                                'status' => null,
                            ]
                        ]
                    ]
                ],
            ],
            [
                'employee' => [
                    'empNumber' => 1,
                    'firstName' => 'Kayla',
                    'lastName' => 'Abbey',
                    'middleName' => 'T',
                    'employeeId' => '0001',
                    'terminationId' => null
                ],
                'leaveType' => [
                    'id' => 1,
                    'name' => 'Casual',
                    'deleted' => false,
                    'situational' => false,
                ]
            ],
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 1,
                ],
                RequestParams::PARAM_TYPE_QUERY => [
                    LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-20',
                    LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-24',
                    LeaveCommonParams::PARAMETER_PARTIAL_OPTION => LeaveParameterObject::PARTIAL_OPTION_START_END,
                    LeaveCommonParams::PARAMETER_DURATION => [
                        LeaveCommonParams::PARAMETER_DURATION_TYPE => LeaveDuration::HALF_DAY_AFTERNOON
                    ],
                    LeaveCommonParams::PARAMETER_END_DURATION => [
                        LeaveCommonParams::PARAMETER_DURATION_TYPE => LeaveDuration::HALF_DAY_MORNING
                    ],
                ]
            ],
            1,
        ];

        /**
         * Multi day - Holiday - Full day
         */
        yield [
            [
                'negative' => false,
                'breakdown' => [
                    [
                        'period' => $expectedPeriod,
                        'balance' => [
                            'entitled' => 3.0,
                            'used' => 0.0,
                            'scheduled' => 0.0,
                            'pending' => 0.0,
                            'taken' => 0.0,
                            'balance' => 3.0,
                            'asAtDate' => '2021-08-24',
                            'endDate' => '2021-08-25',
                        ],
                        'leaves' => [
                            [
                                'balance' => 2.0,
                                'date' => '2021-08-24',
                                'length' => 1,
                                'status' => null,
                            ],
                            [
                                'balance' => 2.0,
                                'date' => '2021-08-25',
                                'length' => 0,
                                'status' => [
                                    'key' => 5,
                                    'name' => 'Holiday'
                                ],
                            ],
                        ]
                    ]
                ],
            ],
            [
                'employee' => [
                    'empNumber' => 1,
                    'firstName' => 'Kayla',
                    'lastName' => 'Abbey',
                    'middleName' => 'T',
                    'employeeId' => '0001',
                    'terminationId' => null
                ],
                'leaveType' => [
                    'id' => 1,
                    'name' => 'Casual',
                    'deleted' => false,
                    'situational' => false,
                ]
            ],
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 1,
                ],
                RequestParams::PARAM_TYPE_QUERY => [
                    LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-24',
                    LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-25',
                    LeaveCommonParams::PARAMETER_PARTIAL_OPTION => LeaveParameterObject::PARTIAL_OPTION_NONE,
                ]
            ],
            1,
        ];

        /**
         * Multi day - Holiday - Half day
         */
        yield [
            [
                'negative' => false,
                'breakdown' => [
                    [
                        'period' => $expectedPeriod,
                        'balance' => [
                            'entitled' => 3.0,
                            'used' => 0.0,
                            'scheduled' => 0.0,
                            'pending' => 0.0,
                            'taken' => 0.0,
                            'balance' => 3.0,
                            'asAtDate' => '2021-08-04',
                            'endDate' => '2021-08-05',
                        ],
                        'leaves' => [
                            [
                                'balance' => 2.5,
                                'date' => '2021-08-04',
                                'length' => 0.5,
                                'status' => null,
                            ],
                            [
                                'balance' => 1.5,
                                'date' => '2021-08-05',
                                'length' => 1,
                                'status' => null,
                            ],
                        ]
                    ]
                ],
            ],
            [
                'employee' => [
                    'empNumber' => 1,
                    'firstName' => 'Kayla',
                    'lastName' => 'Abbey',
                    'middleName' => 'T',
                    'employeeId' => '0001',
                    'terminationId' => null
                ],
                'leaveType' => [
                    'id' => 1,
                    'name' => 'Casual',
                    'deleted' => false,
                    'situational' => false,
                ]
            ],
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 1,
                ],
                RequestParams::PARAM_TYPE_QUERY => [
                    LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-04',
                    LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-05',
                    LeaveCommonParams::PARAMETER_PARTIAL_OPTION => LeaveParameterObject::PARTIAL_OPTION_NONE,
                ]
            ],
            1,
        ];
        yield [
            [
                'negative' => false,
                'breakdown' => [
                    [
                        'period' => $expectedPeriod,
                        'balance' => [
                            'entitled' => 3.0,
                            'used' => 0.0,
                            'scheduled' => 0.0,
                            'pending' => 0.0,
                            'taken' => 0.0,
                            'balance' => 3.0,
                            'asAtDate' => '2021-08-04',
                            'endDate' => '2021-08-05',
                        ],
                        'leaves' => [
                            [
                                'balance' => 2.5,
                                'date' => '2021-08-04',
                                'length' => 0.5,
                                'status' => null,
                            ],
                            [
                                'balance' => 1.5,
                                'date' => '2021-08-05',
                                'length' => 1,
                                'status' => null,
                            ],
                        ]
                    ]
                ],
            ],
            [
                'employee' => [
                    'empNumber' => 1,
                    'firstName' => 'Kayla',
                    'lastName' => 'Abbey',
                    'middleName' => 'T',
                    'employeeId' => '0001',
                    'terminationId' => null
                ],
                'leaveType' => [
                    'id' => 1,
                    'name' => 'Casual',
                    'deleted' => false,
                    'situational' => false,
                ]
            ],
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 1,
                ],
                RequestParams::PARAM_TYPE_QUERY => [
                    LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-04',
                    LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-05',
                    LeaveCommonParams::PARAMETER_PARTIAL_OPTION => LeaveParameterObject::PARTIAL_OPTION_START,
                    LeaveCommonParams::PARAMETER_DURATION => [
                        LeaveCommonParams::PARAMETER_DURATION_TYPE => LeaveDuration::HALF_DAY_AFTERNOON
                    ],
                ]
            ],
            1,
        ];

        /**
         * Multi day - Multiple leave periods
         */
        yield [
            [
                'negative' => true,
                'breakdown' => [
                    [
                        'period' => $expectedPeriod,
                        'balance' => [
                            'entitled' => 3.0,
                            'used' => 0.0,
                            'scheduled' => 0.0,
                            'pending' => 0.0,
                            'taken' => 0.0,
                            'balance' => 3.0,
                            'asAtDate' => '2021-12-30',
                            'endDate' => '2021-12-31',
                        ],
                        'leaves' => [
                            [
                                'balance' => 2.0,
                                'date' => '2021-12-30',
                                'length' => 1,
                                'status' => null,
                            ],
                            [
                                'balance' => 1.0,
                                'date' => '2021-12-31',
                                'length' => 1,
                                'status' => null,
                            ],
                        ]
                    ],
                    [
                        'period' => [
                            'startDate' => '2022-01-01',
                            'endDate' => '2022-12-31',
                        ],
                        'balance' => [
                            'entitled' => 0.0,
                            'used' => 0.0,
                            'scheduled' => 0.0,
                            'pending' => 0.0,
                            'taken' => 0.0,
                            'balance' => 0.0,
                            'asAtDate' => '2022-01-01',
                            'endDate' => '2022-01-02',
                        ],
                        'leaves' => [
                            [
                                'balance' => -0.5,
                                'date' => '2022-01-01',
                                'length' => 0.5,
                                'status' => null,
                            ],
                            [
                                'balance' => -0.5,
                                'date' => '2022-01-02',
                                'length' => 0,
                                'status' => [
                                    'key' => 4,
                                    'name' => 'Weekend'
                                ],
                            ],
                        ]
                    ]
                ],
            ],
            [
                'employee' => [
                    'empNumber' => 1,
                    'firstName' => 'Kayla',
                    'lastName' => 'Abbey',
                    'middleName' => 'T',
                    'employeeId' => '0001',
                    'terminationId' => null
                ],
                'leaveType' => [
                    'id' => 1,
                    'name' => 'Casual',
                    'deleted' => false,
                    'situational' => false,
                ]
            ],
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 1,
                ],
                RequestParams::PARAM_TYPE_QUERY => [
                    LeaveCommonParams::PARAMETER_FROM_DATE => '2021-12-30',
                    LeaveCommonParams::PARAMETER_TO_DATE => '2022-01-02',
                    LeaveCommonParams::PARAMETER_PARTIAL_OPTION => LeaveParameterObject::PARTIAL_OPTION_NONE,
                ]
            ],
            1,
        ];
    }

    public function testGetValidationRuleForGetOne(): void
    {
        $calledGetLeaveTypeById = 7;
        $calledEmployeeNumberValidation = 2;
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
        $api = new LeaveBalanceAPI($this->getRequest());
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
                [LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 100],
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
            LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 50,
            LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-23'
        ];
        $api = new LeaveBalanceAPI($this->getRequest($queryParams));
        $rules = $api->getValidationRuleForGetOne();
        $this->assertInvalidParamException(
        // if defined only from date, to date also need to define
            fn () => $this->validate($queryParams, $rules),
            [
                LeaveCommonParams::PARAMETER_TO_DATE,
                LeaveCommonParams::PARAMETER_FROM_DATE,
                LeaveCommonParams::PARAMETER_DURATION
            ]
        );

        $queryParams = [
            LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 50,
            LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-24',
        ];
        $api = new LeaveBalanceAPI($this->getRequest($queryParams));
        $rules = $api->getValidationRuleForGetOne();
        $this->assertInvalidParamException(
        // if defined only from date, to date also need to define
            fn () => $this->validate($queryParams, $rules),
            [LeaveCommonParams::PARAMETER_FROM_DATE]
        );

        $queryParams = [
            LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 50,
            LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-25',
            LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-24',
        ];
        $api = new LeaveBalanceAPI($this->getRequest($queryParams));
        $rules = $api->getValidationRuleForGetOne();
        $this->assertInvalidParamException(
        // from date < to date
            fn () => $this->validate($queryParams, $rules),
            [LeaveCommonParams::PARAMETER_FROM_DATE, LeaveCommonParams::PARAMETER_DURATION]
        );

        $queryParams = [
            LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 50,
            LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-24',
            LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-24',
        ];
        $api = new LeaveBalanceAPI($this->getRequest($queryParams));
        $rules = $api->getValidationRuleForGetOne();
        $this->assertTrue($this->validate($queryParams, $rules));
    }
}
