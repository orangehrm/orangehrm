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

namespace OrangeHRM\Tests\Buzz\Api;

use OrangeHRM\Buzz\Api\BuzzCommentAPI;
use OrangeHRM\Config\Config;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Buzz
 * @group APIv2
 */
class BuzzCommentAPITest extends EndpointIntegrationTestCase
{
    public static function setUpBeforeClass(): void
    {
        TestDataService::populate(Config::get(Config::TEST_DIR) . '/phpunit/fixtures/DataGroupPermission.yaml', true);
    }

    /**
     * @dataProvider dataProviderForTestCreate
     */
    public function testCreate(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('BuzzCommentAPI.yaml', null, true);
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(BuzzCommentAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'create', $testCaseParams);
    }

    public function dataProviderForTestCreate(): array
    {
        return $this->getTestCases('BuzzCommentAPITestCases.yaml', 'Create');
    }

    /**
     * @dataProvider dataProviderForTestGetOne
     */
    public function testGetOne(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('BuzzCommentAPI.yaml', null, true);
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(BuzzCommentAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getOne', $testCaseParams);
    }

    public function dataProviderForTestGetOne()
    {
        return $this->getTestCases('BuzzCommentAPITestCases.yaml', 'GetOne');
    }

    /**
     * @dataProvider dataProviderForTestGetAll
     */
    public function testGetAll(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('BuzzCommentAPI.yaml', null, true);
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(BuzzCommentAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getAll', $testCaseParams);
    }

    public function dataProviderForTestGetAll(): array
    {
        return $this->getTestCases('BuzzCommentAPITestCases.yaml', 'GetAll');
    }

    /**
     * @dataProvider dataProviderForTestDelete
     */
    public function testDelete(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('BuzzCommentAPI.yaml', null, true);
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(BuzzCommentAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'delete', $testCaseParams);
    }

    public function dataProviderForTestDelete(): array
    {
        return $this->getTestCases('BuzzCommentAPITestCases.yaml', 'Delete');
    }

    /**
     * @dataProvider dataProviderForTestUpdate
     */
    public function testUpdate(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('BuzzCommentAPI.yaml', null, true);
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(BuzzCommentAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'update', $testCaseParams);
    }

    public function dataProviderForTestUpdate(): array
    {
        return $this->getTestCases('BuzzCommentAPITestCases.yaml', 'Update');
    }
}
