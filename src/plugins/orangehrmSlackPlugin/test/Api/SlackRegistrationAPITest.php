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

namespace OrangeHRM\Tests\Slack\Api;

use OrangeHRM\Framework\Services;
use OrangeHRM\Slack\Api\SlackRegistrationAPI;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;

/**
 * Integration coverage of the SlackRegistration CRUD API at the HTTP boundary.
 *
 * The happy-path response shape (model serialisation, masked URL, embedded
 * subunit collection) is already exhaustively pinned at the model / service /
 * DAO layers — SlackRegistrationServiceTest, SlackRegistrationDaoTest, and the
 * model class itself. What's left for this layer is the **request contract**:
 *
 *   - which params are required on create vs update (partial PUT semantics)
 *   - format constraints (eventType allow-list, dailySendTime HH:mm regex,
 *     subunitIds shape, active boolean, provider allow-list)
 *   - path attribute constraints (id must be positive)
 *   - bulk delete shape (ids must be an array)
 *
 * Each row below sets `invalidOnly: [ <param-name> ]` so the framework asserts
 * the exact param that fails — guarding against a refactor that silently
 * loosens a rule.
 *
 * @group Slack
 * @group APIv2
 */
class SlackRegistrationAPITest extends EndpointIntegrationTestCase
{
    /**
     * @dataProvider dataProviderForTestCreate
     */
    public function testCreateInvalidParams(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('SlackConfigAPI.yaml', null, true);
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(SlackRegistrationAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'create', $testCaseParams);
    }

    public function dataProviderForTestCreate(): array
    {
        return $this->getTestCases('SlackRegistrationAPITestCases.yaml', 'Create');
    }

    /**
     * @dataProvider dataProviderForTestUpdate
     */
    public function testUpdateInvalidParamsAndEmptyBody(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('SlackConfigAPI.yaml', null, true);
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(SlackRegistrationAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'update', $testCaseParams);
    }

    public function dataProviderForTestUpdate(): array
    {
        return $this->getTestCases('SlackRegistrationAPITestCases.yaml', 'Update');
    }

    /**
     * @dataProvider dataProviderForTestGetOne
     */
    public function testGetOneInvalidParams(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('SlackConfigAPI.yaml', null, true);
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(SlackRegistrationAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getOne', $testCaseParams);
    }

    public function dataProviderForTestGetOne(): array
    {
        return $this->getTestCases('SlackRegistrationAPITestCases.yaml', 'GetOne');
    }

    /**
     * @dataProvider dataProviderForTestDelete
     */
    public function testDeleteInvalidParams(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('SlackConfigAPI.yaml', null, true);
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(SlackRegistrationAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'delete', $testCaseParams);
    }

    public function dataProviderForTestDelete(): array
    {
        return $this->getTestCases('SlackRegistrationAPITestCases.yaml', 'Delete');
    }
}
