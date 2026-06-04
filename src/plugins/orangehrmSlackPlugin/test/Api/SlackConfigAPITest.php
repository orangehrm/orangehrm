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

use OrangeHRM\Core\Api\V2\Exception\NotImplementedException;
use OrangeHRM\Framework\Services;
use OrangeHRM\Slack\Api\SlackConfigAPI;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;

/**
 * Exercises the SlackConfigAPI HTTP boundary via the OHRM endpoint test rig.
 * Covers:
 *   - GET returns the persisted enable flag + EVENT_TYPES constant array
 *   - PUT toggles the flag in `hs_hr_config` and echoes the updated shape
 *   - PUT param validation: enable must be present and a boolean
 *   - delete()/getValidationRuleForDelete() throw NotImplementedException
 *
 * @group Slack
 * @group APIv2
 */
class SlackConfigAPITest extends EndpointIntegrationTestCase
{
    /**
     * @dataProvider dataProviderForTestGetOne
     */
    public function testGetOne(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('SlackConfigAPI.yaml', null, true);
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(SlackConfigAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getOne', $testCaseParams);
    }

    public function dataProviderForTestGetOne(): array
    {
        return $this->getTestCases('SlackConfigAPITestCases.yaml', 'GetOne');
    }

    /**
     * @dataProvider dataProviderForTestUpdate
     */
    public function testUpdate(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('SlackConfigAPI.yaml', null, true);
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(SlackConfigAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'update', $testCaseParams);
    }

    public function dataProviderForTestUpdate(): array
    {
        return $this->getTestCases('SlackConfigAPITestCases.yaml', 'Update');
    }

    public function testDeleteIsNotImplemented(): void
    {
        $api = new SlackConfigAPI($this->getRequest());
        $this->expectException(NotImplementedException::class);
        $api->delete();
    }

    public function testGetValidationRuleForDeleteIsNotImplemented(): void
    {
        $api = new SlackConfigAPI($this->getRequest());
        $this->expectException(NotImplementedException::class);
        $api->getValidationRuleForDelete();
    }
}
