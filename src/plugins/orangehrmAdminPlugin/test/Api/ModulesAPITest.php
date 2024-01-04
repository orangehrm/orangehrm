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

namespace OrangeHRM\Tests\Admin\Api;

use OrangeHRM\Admin\Api\ModulesAPI;
use OrangeHRM\Entity\OAuthClient;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;
use OrangeHRM\Tests\Util\Mock\MockAuthUser;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group APIv2
 */
class ModulesAPITest extends EndpointIntegrationTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([OAuthClient::class]);
    }

    /**
     * @dataProvider dataProviderForTestGetAll
     */
    public function testGetAll(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('ModulesAPI.yaml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $api = $this->getApiEndpointMock(ModulesAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getAll', $testCaseParams);
    }

    public function dataProviderForTestGetAll(): array
    {
        return $this->getTestCases('ModulesAPITestCases.yaml', 'GetAll');
    }

    /**
     * @dataProvider dataProviderForTestUpdate
     */
    public function testUpdate(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('ModulesAPI.yaml');
        $authUser = $this->getMockBuilder(MockAuthUser::class)
            ->onlyMethods(['getUserId', 'getEmpNumber','removeAttribute', 'getAttribute'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->atMost(2))
            ->method('removeAttribute')
            ->willReturnMap([
                ['core.menu.side_panel'],
                ['core.menu.top_ribbon_keys']
            ]);
        $authUser->expects($this->atMost(1))
            ->method('getAttribute')
            ->with('core.menu.top_ribbon_keys')
            ->willReturn([]);
        $this->createKernelWithMockServices([Services::AUTH_USER => $authUser]);
        $this->registerServices($testCaseParams);
        $api = $this->getApiEndpointMock(ModulesAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'update', $testCaseParams);
    }

    public function dataProviderForTestUpdate(): array
    {
        return $this->getTestCases('ModulesAPITestCases.yaml', 'Update');
    }

    public function testCreate(): void
    {
        $api = new ModulesAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->create();
    }

    public function testGetValidationRuleForCreate(): void
    {
        $api = new ModulesAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForCreate();
    }

    public function testGetOne(): void
    {
        $api = new ModulesAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getOne();
    }

    public function testGetValidationRuleForGetOne(): void
    {
        $api = new ModulesAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForGetOne();
    }

    public function testDelete(): void
    {
        $api = new ModulesAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new ModulesAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }
}
