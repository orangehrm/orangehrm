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

namespace OrangeHRM\Tests\Dashboard\Api;

use OrangeHRM\Dashboard\Api\QuickLaunchAPI;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;

/**
 * @group Dashboard
 * @group APIv2
 */
class QuickLaunchAPITest extends EndpointIntegrationTestCase
{
    /**
     * @dataProvider dataProviderForTestGetAll1
     */
    public function testGetAll1(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('QuickLaunchAPITest1.yml');
        $this->createKernelWithMockServices([
            Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)
        ]);
        $this->registerServices($testCaseParams);
        $api = $this->getApiEndpointMock(QuickLaunchAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getAll', $testCaseParams);
    }

    public function dataProviderForTestGetAll1(): array
    {
        return $this->getTestCases('QuickLaunchAPITestCases.yaml', 'GetAll - Modules enabled Periods defined');
    }

    /**
     * @dataProvider dataProviderForTestGetAll2
     */
    public function testGetAll2(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('QuickLaunchAPITest2.yml');
        $this->createKernelWithMockServices([
            Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)
        ]);
        $this->registerServices($testCaseParams);
        $api = $this->getApiEndpointMock(QuickLaunchAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getAll', $testCaseParams);
    }

    public function dataProviderForTestGetAll2(): array
    {
        return $this->getTestCases('QuickLaunchAPITestCases.yaml', 'GetAll - Modules enabled Periods undefined');
    }

    /**
     * @dataProvider dataProviderForTestGetAll3
     */
    public function testGetAll3(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('QuickLaunchAPITest3.yml');
        $this->createKernelWithMockServices([
            Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)
        ]);
        $this->registerServices($testCaseParams);
        $api = $this->getApiEndpointMock(QuickLaunchAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getAll', $testCaseParams);
    }

    public function dataProviderForTestGetAll3(): array
    {
        return $this->getTestCases('QuickLaunchAPITestCases.yaml', 'GetAll - Leave module disabled');
    }

    public function testCreate(): void
    {
        $api = new QuickLaunchAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->create();
    }

    public function testGetValidationRuleForCreate(): void
    {
        $api = new QuickLaunchAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForCreate();
    }

    public function testDelete(): void
    {
        $api = new QuickLaunchAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new QuickLaunchAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }
}
