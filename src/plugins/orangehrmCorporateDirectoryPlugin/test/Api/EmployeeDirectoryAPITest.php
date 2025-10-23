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

namespace OrangeHRM\Tests\CorporateDirectory\Api;

use Exception;
use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Api\V2\Exception\NotImplementedException;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\Serializer\NormalizeException;
use OrangeHRM\CorporateDirectory\Api\EmployeeDirectoryAPI;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;

/**
 * @group Directory
 * @group APIv2
 */
class EmployeeDirectoryAPITest extends EndpointIntegrationTestCase
{
    /**
     * @dataProvider dataProviderForTestGetOne
     * @throws Exception
     */
    public function testGetOne(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('EmployeeDirectoryAPITest.yml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);

        $this->registerServices($testCaseParams);
        $api = $this->getApiEndpointMock(EmployeeDirectoryAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getOne', $testCaseParams);
    }

    /**
     * @throws Exception
     */
    public function dataProviderForTestGetOne(): array
    {
        return $this->getTestCases('EmployeeDirectoryAPITestCases.yml', 'GetOne');
    }

    /**
     * @dataProvider dataProviderForTestGetAll
     * @throws Exception
     */
    public function testGetAll(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('EmployeeDirectoryAPITest.yml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);

        $this->registerServices($testCaseParams);
        $api = $this->getApiEndpointMock(EmployeeDirectoryAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getAll', $testCaseParams);
    }

    /**
     * @throws Exception
     */
    public function dataProviderForTestGetAll(): array
    {
        return $this->getTestCases('EmployeeDirectoryAPITestCases.yml', 'GetAll');
    }

    /**
     * @throws NormalizeException
     * @throws NotImplementedException
     * @throws RecordNotFoundException
     * @throws ForbiddenException
     */
    public function testCreate(): void
    {
        $api = new EmployeeDirectoryAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->create();
    }

    /**
     * @throws NotImplementedException
     */
    public function testGetValidationRuleForCreate(): void
    {
        $api = new EmployeeDirectoryAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForCreate();
    }

    /**
     * @throws NormalizeException
     * @throws NotImplementedException
     * @throws RecordNotFoundException
     * @throws ForbiddenException
     */
    public function testUpdate(): void
    {
        $api = new EmployeeDirectoryAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->update();
    }

    /**
     * @throws NotImplementedException
     */
    public function testGetValidationRuleForUpdate(): void
    {
        $api = new EmployeeDirectoryAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForUpdate();
    }

    /**
     * @throws NormalizeException
     * @throws NotImplementedException
     * @throws RecordNotFoundException
     * @throws ForbiddenException
     */
    public function testDelete(): void
    {
        $api = new EmployeeDirectoryAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    /**
     * @throws NotImplementedException
     */
    public function testGetValidationRuleForDelete(): void
    {
        $api = new EmployeeDirectoryAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }
}
