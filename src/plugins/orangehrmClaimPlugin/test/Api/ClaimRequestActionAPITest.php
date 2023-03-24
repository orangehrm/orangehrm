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

namespace OrangeHRM\Tests\Claim\Api;

use OrangeHRM\Claim\Api\ClaimRequestActionAPI;
use OrangeHRM\Config\Config;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Claim
 * @group APIv2
 */
class ClaimRequestActionAPITest extends EndpointIntegrationTestCase
{
    public static function setUpBeforeClass(): void
    {
        TestDataService::populate(
            Config::get(Config::TEST_DIR) . '/phpunit/fixtures/WorkflowStateMachine.yaml',
            true
        );
    }

    /**
     * @dataProvider dataProviderForTestUpdate
     */
    public function testUpdate(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('ClaimRequestActionAPITest.yaml', null, true);
        $this->createKernelWithMockServices([
            Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)
        ]);
        $this->registerMockDateTimeHelper($testCaseParams);
        $this->registerServices($testCaseParams);
        $api = $this->getApiEndpointMock(ClaimRequestActionAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'update', $testCaseParams);
    }

    public function dataProviderForTestUpdate(): array
    {
        return $this->getTestCases('ClaimRequestActionAPITestCases.yaml', 'Update');
    }

    public function testDelete(): void
    {
        $api = new ClaimRequestActionAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new ClaimRequestActionAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }

    public function testGetOne(): void
    {
        $api = new ClaimRequestActionAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getOne();
    }

    public function testGetValidationRuleForGetOne(): void
    {
        $api = new ClaimRequestActionAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForGetOne();
    }
}
