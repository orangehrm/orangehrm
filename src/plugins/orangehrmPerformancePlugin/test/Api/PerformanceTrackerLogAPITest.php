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

namespace OrangeHRM\Tests\Performance\Api;

use OrangeHRM\Framework\Services;
use OrangeHRM\Performance\Api\PerformanceTrackerLogAPI;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;

class PerformanceTrackerLogAPITest extends EndpointIntegrationTestCase
{
    /**
     * @dataProvider dataProviderForTestGetOne
     *
     */
    public function testGetOne(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('PerformanceTrackerLogAPITest.yaml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(PerformanceTrackerLogAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getOne', $testCaseParams);
    }

    /**
     * @return array
     */
    public function dataProviderForTestGetOne(): array
    {
        return $this->getTestCases('PerformanceTrackerLogAPITestCases.yaml', 'GetOne');
    }

    /**
     * @dataProvider dataProviderForTestUpdate
     */
    public function testUpdate(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('PerformanceTrackerLogAPITest.yaml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(PerformanceTrackerLogAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'update', $testCaseParams);
    }

    /**
     * @return array
     */
    public function dataProviderForTestUpdate(): array
    {
        return $this->getTestCases('PerformanceTrackerLogAPITestCases.yaml', 'Update');
    }

    /**
     * @dataProvider dataProviderForTestGetAll
     *
     */
    public function testGetAll(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('PerformanceTrackerLogAPITest.yaml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(PerformanceTrackerLogAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getAll', $testCaseParams);
    }

    /**
     * @return array
     */
    public function dataProviderForTestGetAll(): array
    {
        return $this->getTestCases('PerformanceTrackerLogAPITestCases.yaml', 'GetAll');
    }

    /**
     * @dataProvider dataProviderForTestSave
     */
    public function testSave(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('PerformanceTrackerLogAPITest.yaml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(PerformanceTrackerLogAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'create', $testCaseParams);
    }

    /**
     * @return array
     */
    public function dataProviderForTestSave(): array
    {
        return $this->getTestCases('PerformanceTrackerLogAPITestCases.yaml', 'Create');
    }

    /**
     * @dataProvider dataProviderForTestDelete
     */
    public function testDelete(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('PerformanceTrackerLogAPITest.yaml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(PerformanceTrackerLogAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'delete', $testCaseParams);
    }

    /**
     * @return array
     */
    public function dataProviderForTestDelete(): array
    {
        return $this->getTestCases('PerformanceTrackerLogAPITestCases.yaml', 'Delete');
    }
}
