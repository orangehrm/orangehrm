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

namespace OrangeHRM\Tests\Mobile\Api;

use OrangeHRM\Framework\Services;
use OrangeHRM\Mobile\Api\MenuItemAPI;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;

/**
 * @group Mobile
 * @group APIv2
 */
class MenuItemAPITest extends EndpointIntegrationTestCase
{
    /**
     * Check for Leave period and Timesheet period defined scenarios
     * @dataProvider dataProviderForTestGetOne
     */
    public function testGetOne(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('MenuItemAPI.yaml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);

        $api = $this->getApiEndpointMock(MenuItemAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getOne', $testCaseParams);
    }

    public function dataProviderForTestGetOne(): array
    {
        return $this->getTestCases('MenuItemAPITestCases.yaml', 'GetOne - Leave period and Timesheet period defined');
    }

    /**
     * Check for Leave period and Timesheet period not defined scenarios
     * @dataProvider dataProviderForTestGetOne2
     */
    public function testGetOne2(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('MenuItemAPI2.yaml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);

        $api = $this->getApiEndpointMock(MenuItemAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getOne', $testCaseParams);
    }

    public function dataProviderForTestGetOne2(): array
    {
        return $this->getTestCases('MenuItemAPITestCases.yaml', 'GetOne - Leave period and Timesheet period not defined');
    }

    /**
     * Check for Time Module Disabled scenarios
     * @dataProvider dataProviderForTestGetOne3
     */
    public function testGetOne3(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('MenuItemAPI3.yaml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);

        $api = $this->getApiEndpointMock(MenuItemAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getOne', $testCaseParams);
    }

    public function dataProviderForTestGetOne3(): array
    {
        return $this->getTestCases('MenuItemAPITestCases.yaml', 'GetOne - Time Module Disabled');
    }

    public function testUpdate(): void
    {
        $api = new MenuItemAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->update();
    }

    public function testGetValidationRuleForUpdate(): void
    {
        $api = new MenuItemAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForUpdate();
    }

    public function testDelete(): void
    {
        $api = new MenuItemAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new MenuItemAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }
}
