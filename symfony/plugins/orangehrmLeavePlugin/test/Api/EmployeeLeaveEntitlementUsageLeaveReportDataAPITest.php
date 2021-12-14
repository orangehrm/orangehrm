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

use OrangeHRM\Config\Config;
use OrangeHRM\Core\Api\Rest\ReportAPI;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Api\LeaveReportDataAPI;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Leave
 * @group APIv2
 */
class EmployeeLeaveEntitlementUsageLeaveReportDataAPITest extends EndpointIntegrationTestCase
{
    public static function setUpBeforeClass(): void
    {
        TestDataService::populate(Config::get(Config::TEST_DIR) . '/phpunit/fixtures/DataGroupPermission.yaml', true);
    }

    /**
     * @dataProvider dataProviderForTestGetAll
     */
    public function testGetAll(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('EmployeeLeaveEntitlementUsageLeaveReportDataAPITest.yaml', null, true);
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);

        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(LeaveReportDataAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getAll', $testCaseParams);
    }

    public function dataProviderForTestGetAll(): array
    {
        return $this->getTestCases('EmployeeLeaveEntitlementUsageLeaveReportDataAPITestCases.yaml', 'GetAll');
    }

    public function testDelete(): void
    {
        $api = new LeaveReportDataAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new LeaveReportDataAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }

    public function testCreate(): void
    {
        $api = new LeaveReportDataAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->create();
    }

    public function testGetValidationRuleForCreate(): void
    {
        $api = new LeaveReportDataAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForCreate();
    }

    public function testGetValidationRuleForGetOne(): void
    {
        $api = new LeaveReportDataAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetAll();

        $this->assertTrue(
            $this->validate(
                [ReportAPI::PARAMETER_NAME => 'leave_entitlements_and_usage'],
                $rules
            )
        );

        $this->assertTrue(
            $this->validate(
                [
                    ReportAPI::PARAMETER_NAME => 'leave_entitlements_and_usage',
                    'param' => 'any'
                ],
                $rules
            )
        );
        $this->assertInvalidParamException(
        // accept any parameter but report name required
            fn () => $this->validate(
                ['param' => 'any'],
                $rules
            ),
            [ReportAPI::PARAMETER_NAME]
        );
    }
}
