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
use OrangeHRM\Slack\Api\SlackTestWebhookAPI;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;

/**
 * The "Send test" button on the configuration UI lands here. Because the
 * happy path actually performs an HTTP POST to Slack, comprehensive happy-path
 * coverage lives in SlackNotificationServiceTest (where the provider registry
 * is mocked). This integration test pins the API-level invariants:
 *
 *   - request param validation rules — every invalid-param case fails fast
 *     BEFORE hitting the network. If a future refactor accidentally moves a
 *     check into the body of create(), the param-rule cases here regress.
 *   - getAll / delete / getValidationRuleForGetAll / getValidationRuleForDelete
 *     all throw NotImplementedException — guards against accidentally
 *     enabling a method that the routes / authz rules don't expect.
 *
 * @group Slack
 * @group APIv2
 */
class SlackTestWebhookAPITest extends EndpointIntegrationTestCase
{
    /**
     * @dataProvider dataProviderForTestCreateInvalid
     */
    public function testCreateInvalidParamsFailValidation(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('SlackConfigAPI.yaml', null, true);
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(SlackTestWebhookAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'create', $testCaseParams);
    }

    public function dataProviderForTestCreateInvalid(): array
    {
        return $this->getTestCases('SlackTestWebhookAPITestCases.yaml', 'Create');
    }

    public function testGetAllIsNotImplemented(): void
    {
        $api = new SlackTestWebhookAPI($this->getRequest());
        $this->expectException(NotImplementedException::class);
        $api->getAll();
    }

    public function testGetValidationRuleForGetAllIsNotImplemented(): void
    {
        $api = new SlackTestWebhookAPI($this->getRequest());
        $this->expectException(NotImplementedException::class);
        $api->getValidationRuleForGetAll();
    }

    public function testDeleteIsNotImplemented(): void
    {
        $api = new SlackTestWebhookAPI($this->getRequest());
        $this->expectException(NotImplementedException::class);
        $api->delete();
    }

    public function testGetValidationRuleForDeleteIsNotImplemented(): void
    {
        $api = new SlackTestWebhookAPI($this->getRequest());
        $this->expectException(NotImplementedException::class);
        $api->getValidationRuleForDelete();
    }
}
