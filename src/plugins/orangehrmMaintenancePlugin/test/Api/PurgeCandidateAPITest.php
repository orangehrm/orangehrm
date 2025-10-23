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

namespace OrangeHRM\Tests\Maintenance\Api;

use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Framework\Services;
use OrangeHRM\Maintenance\Api\PurgeCandidateAPI;
use OrangeHRM\Maintenance\Service\PurgeService;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;

class PurgeCandidateAPITest extends EndpointIntegrationTestCase
{
    /**
     * @dataProvider dataProviderForTestDelete
     */
    public function testDelete(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('PurgeCandidateAPITest.yml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);

        $this->registerMockDateTimeHelper($testCaseParams);
        $this->registerServices($testCaseParams);
        $api = $this->getApiEndpointMock(PurgeCandidateAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'delete', $testCaseParams);
    }

    public function dataProviderForTestDelete(): array
    {
        return $this->getTestCases('PurgeCandidateAPITestCases.yml', 'Delete');
    }

    public function testGetPurgeService(): void
    {
        $api = new PurgeCandidateAPI($this->getRequest());

        $this->assertInstanceOf(PurgeService::class, $api->getPurgeService());
    }

    public function testGetValidationRuleForDelete(): void
    {
        $this->populateFixtures('PurgeCandidateAPITest.yml');

        $testCaseParams = new TestCaseParams();
        $testCaseParams->setUserId(1);

        $api = new PurgeCandidateAPI($this->getRequest());
        $validationRules = $api->getValidationRuleForDelete();

        $this->assertInstanceOf(ParamRuleCollection::class, $validationRules);

        $values = [PurgeCandidateAPI::PARAMETER_VACANCY_ID => 2];
        $this->assertTrue($this->validate($values, $validationRules));

        $this->expectInvalidParamException();
        $values = [PurgeCandidateAPI::PARAMETER_VACANCY_ID => -1];
        $this->validate($values, $validationRules);
    }

    /**
     * @dataProvider dataProviderForTestGetAll
     */
    public function testGetAll(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('PurgeCandidateAPITest.yml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);

        $this->registerMockDateTimeHelper($testCaseParams);
        $this->registerServices($testCaseParams);
        $api = $this->getApiEndpointMock(PurgeCandidateAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getAll', $testCaseParams);
    }

    public function dataProviderForTestGetAll(): array
    {
        return $this->getTestCases('PurgeCandidateAPITestCases.yml', 'GetAll');
    }

    public function testGetValidationRuleForGetAll(): void
    {
        $this->populateFixtures('PurgeCandidateAPITest.yml');

        $testCaseParams = new TestCaseParams();
        $testCaseParams->setUserId(1);

        $api = new PurgeCandidateAPI($this->getRequest());
        $validationRules = $api->getValidationRuleForGetAll();

        $this->assertInstanceOf(ParamRuleCollection::class, $validationRules);

        $values = [PurgeCandidateAPI::PARAMETER_VACANCY_ID => 2];
        $this->assertTrue($this->validate($values, $validationRules));

        $this->expectInvalidParamException();
        $values = [PurgeCandidateAPI::PARAMETER_VACANCY_ID => -1];
        $this->validate($values, $validationRules);
    }

    public function testCreate(): void
    {
        $api = new PurgeCandidateAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->create();
    }

    public function testGetValidationRuleForCreate(): void
    {
        $api = new PurgeCandidateAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForCreate();
    }
}
