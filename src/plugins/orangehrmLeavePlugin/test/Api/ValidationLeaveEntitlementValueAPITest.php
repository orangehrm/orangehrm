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

use Generator;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Service\NumberHelperService;
use OrangeHRM\Entity\LeaveEntitlement;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Api\ValidationLeaveEntitlementValueAPI;
use OrangeHRM\Leave\Dao\LeaveEntitlementDao;
use OrangeHRM\Leave\Service\LeaveEntitlementService;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;

/**
 * @group Leave
 * @group APIv2
 */
class ValidationLeaveEntitlementValueAPITest extends EndpointTestCase
{
    public function testDelete(): void
    {
        $api = new ValidationLeaveEntitlementValueAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new ValidationLeaveEntitlementValueAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }

    public function testUpdate(): void
    {
        $api = new ValidationLeaveEntitlementValueAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->update();
    }

    public function testGetValidationRuleForUpdate(): void
    {
        $api = new ValidationLeaveEntitlementValueAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForUpdate();
    }

    public function testGetValidationRuleForGetOne(): void
    {
        $api = new ValidationLeaveEntitlementValueAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetOne();

        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_ID => 2,
                    ValidationLeaveEntitlementValueAPI::PARAMETER_ENTITLEMENT => 0
                ],
                $rules
            )
        );
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_ID => 2,
                    ValidationLeaveEntitlementValueAPI::PARAMETER_ENTITLEMENT => 1
                ],
                $rules
            )
        );
        $this->assertInvalidParamException(
            fn () => $this->validate(
                [
                    CommonParams::PARAMETER_ID => 2,
                    ValidationLeaveEntitlementValueAPI::PARAMETER_ENTITLEMENT => -1
                ],
                $rules
            ),
            [ValidationLeaveEntitlementValueAPI::PARAMETER_ENTITLEMENT]
        );
        $this->assertInvalidParamException(
            fn () => $this->validate(
                [CommonParams::PARAMETER_ID => 2],
                $rules
            ),
            [ValidationLeaveEntitlementValueAPI::PARAMETER_ENTITLEMENT]
        );
    }

    /**
     * @dataProvider getOneDataProvider
     */
    public function testGetOne(
        array $map,
        array $requestParams,
        array $expected,
        bool $expectRecordNotFoundException = false
    ): void {
        $leaveEntitlementDao = $this->getMockBuilder(LeaveEntitlementDao::class)
            ->onlyMethods(['getLeaveEntitlement'])
            ->getMock();
        $leaveEntitlementDao->expects($this->once())
            ->method('getLeaveEntitlement')
            ->willReturnMap($map);

        $leaveEntitlementService = $this->getMockBuilder(LeaveEntitlementService::class)
            ->onlyMethods(['getLeaveEntitlementDao'])
            ->getMock();
        $leaveEntitlementService->expects($this->once())
            ->method('getLeaveEntitlementDao')
            ->willReturn($leaveEntitlementDao);

        $this->createKernelWithMockServices([Services::LEAVE_ENTITLEMENT_SERVICE => $leaveEntitlementService]);

        /** @var MockObject&ValidationLeaveEntitlementValueAPI $api */
        $api = $this->getApiEndpointMockBuilder(ValidationLeaveEntitlementValueAPI::class, $requestParams)
            ->onlyMethods(['checkLeaveEntitlementAccessible'])
            ->getMock();
        $api->expects($expectRecordNotFoundException ? $this->never() : $this->once())
            ->method('checkLeaveEntitlementAccessible');

        if ($expectRecordNotFoundException) {
            $this->expectRecordNotFoundException();
        }
        $result = $api->getOne();
        $this->assertEquals($expected, $result->normalize());
    }

    public function getOneDataProvider(): Generator
    {
        $this->createKernelWithMockServices([Services::NUMBER_HELPER_SERVICE => new NumberHelperService()]);
        $leaveEntitlement = new LeaveEntitlement();
        $leaveEntitlement->setDaysUsed(2);
        $map = [
            [1, $leaveEntitlement],
            [2, null]
        ];

        yield [
            $map,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [CommonParams::PARAMETER_ID => 1],
                RequestParams::PARAM_TYPE_QUERY => [
                    ValidationLeaveEntitlementValueAPI::PARAMETER_ENTITLEMENT => 2,
                ]
            ],
            [
                'valid' => true,
                'daysUsed' => 2,
            ]
        ];
        yield [
            $map,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [CommonParams::PARAMETER_ID => 1],
                RequestParams::PARAM_TYPE_QUERY => [
                    ValidationLeaveEntitlementValueAPI::PARAMETER_ENTITLEMENT => 3,
                ]
            ],
            [
                'valid' => true,
                'daysUsed' => 2,
            ]
        ];
        yield [
            $map,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [CommonParams::PARAMETER_ID => 1],
                RequestParams::PARAM_TYPE_QUERY => [
                    ValidationLeaveEntitlementValueAPI::PARAMETER_ENTITLEMENT => 1,
                ]
            ],
            [
                'valid' => false,
                'daysUsed' => 2,
            ]
        ];
        if (PHP_INT_SIZE == 8) {
            yield [
                $map,
                [
                    RequestParams::PARAM_TYPE_ATTRIBUTE => [CommonParams::PARAMETER_ID => 1],
                    RequestParams::PARAM_TYPE_QUERY => [
                        ValidationLeaveEntitlementValueAPI::PARAMETER_ENTITLEMENT => 1.999999999999999,
                    ]
                ],
                [
                    'valid' => false,
                    'daysUsed' => 2,
                ]
            ];
            // consider as valid since 1.9999999999999999 => 2.0
            yield [
                $map,
                [
                    RequestParams::PARAM_TYPE_ATTRIBUTE => [CommonParams::PARAMETER_ID => 1],
                    RequestParams::PARAM_TYPE_QUERY => [
                        ValidationLeaveEntitlementValueAPI::PARAMETER_ENTITLEMENT => 1.9999999999999999,
                    ]
                ],
                [
                    'valid' => true,
                    'daysUsed' => 2,
                ]
            ];
        }
        yield [
            $map,
            [RequestParams::PARAM_TYPE_ATTRIBUTE => [CommonParams::PARAMETER_ID => 2]],
            [],
            true,
        ];
    }
}
