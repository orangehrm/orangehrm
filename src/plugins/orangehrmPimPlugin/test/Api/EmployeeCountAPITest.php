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

use OrangeHRM\Admin\Service\CompanyStructureService;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Api\EmployeeAPI;
use OrangeHRM\Pim\Api\EmployeeCountAPI;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;

/**
 * @group Pim
 * @group APIv2
 */
class EmployeeCountAPITest extends EndpointTestCase
{
    public function testDelete(): void
    {
        $api = new EmployeeCountAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new EmployeeCountAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }

    public function testCreate(): void
    {
        $api = new EmployeeCountAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->create();
    }

    public function testGetValidationRuleForCreate(): void
    {
        $api = new EmployeeCountAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForCreate();
    }

    public function testGetValidationRuleForGetAll(): void
    {
        $api = new EmployeeCountAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetAll();
        // without filters
        $this->assertTrue($this->validate([], $rules));

        $this->assertTrue($this->validate([EmployeeAPI::FILTER_EMPLOYEE_ID => '0001'], $rules));
        $this->assertTrue($this->validate([EmployeeAPI::FILTER_EMPLOYEE_ID => str_repeat('00001', 10)], $rules));
        $this->assertInvalidParamException(
        // maximum 50 allowed in the rule
            fn () => $this->validate([EmployeeAPI::FILTER_EMPLOYEE_ID => str_repeat('00001', 10) . '1'], $rules),
            [EmployeeAPI::FILTER_EMPLOYEE_ID]
        );

        $this->assertTrue($this->validate([EmployeeAPI::FILTER_INCLUDE_EMPLOYEES => 1], $rules));
        $this->assertTrue($this->validate([EmployeeAPI::FILTER_INCLUDE_EMPLOYEES => 2], $rules));
        $this->assertTrue($this->validate([EmployeeAPI::FILTER_INCLUDE_EMPLOYEES => 3], $rules));
        $this->assertTrue($this->validate([EmployeeAPI::FILTER_INCLUDE_EMPLOYEES => 'onlyCurrent'], $rules));
        $this->assertTrue($this->validate([EmployeeAPI::FILTER_INCLUDE_EMPLOYEES => 'onlyPast'], $rules));
        $this->assertTrue($this->validate([EmployeeAPI::FILTER_INCLUDE_EMPLOYEES => 'currentAndPast'], $rules));
        $this->assertInvalidParamException(
            fn () => $this->validate([EmployeeAPI::FILTER_INCLUDE_EMPLOYEES => 'invalid'], $rules),
            [EmployeeAPI::FILTER_INCLUDE_EMPLOYEES]
        );

        $this->assertTrue($this->validate([EmployeeAPI::FILTER_NAME => 'Kayla'], $rules));
        $this->assertTrue($this->validate([EmployeeAPI::FILTER_NAME => str_repeat('Kayla', 20)], $rules));
        $this->assertInvalidParamException(
        // maximum 100 allowed in the rule
            fn () => $this->validate([EmployeeAPI::FILTER_NAME => str_repeat('ස්ටීව්', 17)], $rules),
            [EmployeeAPI::FILTER_NAME]
        );

        $this->assertTrue($this->validate([EmployeeAPI::FILTER_NAME_OR_ID => '0002'], $rules));
        $this->assertTrue($this->validate([EmployeeAPI::FILTER_NAME_OR_ID => str_repeat('Kayla', 20)], $rules));
        $this->assertInvalidParamException(
        // maximum 100 allowed in the rule
            fn () => $this->validate([EmployeeAPI::FILTER_NAME_OR_ID => str_repeat('Kyla ', 20) . 'a'], $rules),
            [EmployeeAPI::FILTER_NAME_OR_ID]
        );

        $this->assertTrue($this->validate([EmployeeAPI::FILTER_EMP_STATUS_ID => 1], $rules));
        $this->assertInvalidParamException(
            fn () => $this->validate([EmployeeAPI::FILTER_EMP_STATUS_ID => 0], $rules),
            [EmployeeAPI::FILTER_EMP_STATUS_ID]
        );
        $this->assertInvalidParamException(
            fn () => $this->validate([EmployeeAPI::FILTER_EMP_STATUS_ID => 'invalid'], $rules),
            [EmployeeAPI::FILTER_EMP_STATUS_ID]
        );

        $this->assertTrue($this->validate([EmployeeAPI::FILTER_JOB_TITLE_ID => 1], $rules));
        $this->assertInvalidParamException(
            fn () => $this->validate([EmployeeAPI::FILTER_JOB_TITLE_ID => 0], $rules),
            [EmployeeAPI::FILTER_JOB_TITLE_ID]
        );

        $this->assertTrue($this->validate([EmployeeAPI::FILTER_SUBUNIT_ID => 1], $rules));
        $this->assertInvalidParamException(
            fn () => $this->validate([EmployeeAPI::FILTER_SUBUNIT_ID => 0], $rules),
            [EmployeeAPI::FILTER_SUBUNIT_ID]
        );

        $this->assertTrue($this->validate([EmployeeCountAPI::FILTER_LOCATION_ID => 1], $rules));
        $this->assertInvalidParamException(
            fn () => $this->validate([EmployeeCountAPI::FILTER_LOCATION_ID => 0], $rules),
            [EmployeeCountAPI::FILTER_LOCATION_ID]
        );
    }

    /**
     * @dataProvider getAllDataProvider
     */
    public function testGetAll(int $count, array $requestParams, array $expected, array $expectedMeta): void
    {
        $service = $this->getMockBuilder(EmployeeService::class)
            ->onlyMethods(['getEmployeeCount'])
            ->getMock();
        $service->expects($this->once())
            ->method('getEmployeeCount')
            ->willReturn($count);

        /** @var MockObject&EmployeeCountAPI $api */
        $api = $this->getApiEndpointMockBuilder(EmployeeCountAPI::class, $requestParams)
            ->onlyMethods(['getEmployeeService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getEmployeeService')
            ->will($this->returnValue($service));

        $result = $api->getAll();
        $this->assertEquals($expected, $result->normalize());
        $this->assertEquals($expectedMeta, $result->getMeta()->all());
    }

    public function getAllDataProvider()
    {
        yield [
            5,
            [],
            ['count' => 5],
            [
                EmployeeCountAPI::META_PARAMETER_NAME => null,
                EmployeeCountAPI::META_PARAMETER_NAME_OR_ID => null,
                EmployeeCountAPI::META_PARAMETER_EMPLOYEE_ID => null,
                EmployeeCountAPI::META_PARAMETER_INCLUDE_EMPLOYEES => null,
                EmployeeCountAPI::META_PARAMETER_EMP_STATUS_ID => null,
                EmployeeCountAPI::META_PARAMETER_JOB_TITLE_ID => null,
                EmployeeCountAPI::META_PARAMETER_SUBUNIT_IDS => null,
                EmployeeCountAPI::META_PARAMETER_LOCATION_ID => null,
            ]
        ];
        $locationId = 1;
        yield [
            3,
            [
                RequestParams::PARAM_TYPE_QUERY => [
                    EmployeeCountAPI::FILTER_LOCATION_ID => $locationId,
                ]
            ],
            ['count' => 3],
            [
                EmployeeCountAPI::META_PARAMETER_NAME => null,
                EmployeeCountAPI::META_PARAMETER_NAME_OR_ID => null,
                EmployeeCountAPI::META_PARAMETER_EMPLOYEE_ID => null,
                EmployeeCountAPI::META_PARAMETER_INCLUDE_EMPLOYEES => null,
                EmployeeCountAPI::META_PARAMETER_EMP_STATUS_ID => null,
                EmployeeCountAPI::META_PARAMETER_JOB_TITLE_ID => null,
                EmployeeCountAPI::META_PARAMETER_SUBUNIT_IDS => null,
                EmployeeCountAPI::META_PARAMETER_LOCATION_ID => $locationId,
            ]
        ];

        $jobTitleId = 1;
        yield [
            3,
            [
                RequestParams::PARAM_TYPE_QUERY => [
                    EmployeeAPI::FILTER_JOB_TITLE_ID => $jobTitleId,
                ]
            ],
            ['count' => 3],
            [
                EmployeeCountAPI::META_PARAMETER_NAME => null,
                EmployeeCountAPI::META_PARAMETER_NAME_OR_ID => null,
                EmployeeCountAPI::META_PARAMETER_EMPLOYEE_ID => null,
                EmployeeCountAPI::META_PARAMETER_INCLUDE_EMPLOYEES => null,
                EmployeeCountAPI::META_PARAMETER_EMP_STATUS_ID => null,
                EmployeeCountAPI::META_PARAMETER_JOB_TITLE_ID => $jobTitleId,
                EmployeeCountAPI::META_PARAMETER_SUBUNIT_IDS => null,
                EmployeeCountAPI::META_PARAMETER_LOCATION_ID => null,
            ]
        ];
    }

    public function testGetAllWithSubunitId(): void
    {
        $companyStructureService = $this->getMockBuilder(CompanyStructureService::class)
            ->onlyMethods(['getSubunitChainById'])
            ->getMock();
        $companyStructureService->expects($this->once())
            ->method('getSubunitChainById')
            ->willReturn([1, 2, 3]);

        $this->createKernelWithMockServices([Services::COMPANY_STRUCTURE_SERVICE => $companyStructureService]);

        $service = $this->getMockBuilder(EmployeeService::class)
            ->onlyMethods(['getEmployeeCount'])
            ->getMock();
        $service->expects($this->once())
            ->method('getEmployeeCount')
            ->willReturn(3);

        /** @var MockObject&EmployeeCountAPI $api */
        $api = $this->getApiEndpointMockBuilder(EmployeeCountAPI::class, [
            RequestParams::PARAM_TYPE_QUERY => [
                EmployeeAPI::FILTER_SUBUNIT_ID => 1,
            ]
        ])->onlyMethods(['getEmployeeService'])->getMock();
        $api->expects($this->once())
            ->method('getEmployeeService')
            ->will($this->returnValue($service));

        $result = $api->getAll();
        $this->assertEquals(['count' => 3], $result->normalize());
        $this->assertEquals(
            [
                EmployeeCountAPI::META_PARAMETER_NAME => null,
                EmployeeCountAPI::META_PARAMETER_NAME_OR_ID => null,
                EmployeeCountAPI::META_PARAMETER_EMPLOYEE_ID => null,
                EmployeeCountAPI::META_PARAMETER_INCLUDE_EMPLOYEES => null,
                EmployeeCountAPI::META_PARAMETER_EMP_STATUS_ID => null,
                EmployeeCountAPI::META_PARAMETER_JOB_TITLE_ID => null,
                EmployeeCountAPI::META_PARAMETER_SUBUNIT_IDS => [1, 2, 3],
                EmployeeCountAPI::META_PARAMETER_LOCATION_ID => null,
            ],
            $result->getMeta()->all()
        );
    }
}
