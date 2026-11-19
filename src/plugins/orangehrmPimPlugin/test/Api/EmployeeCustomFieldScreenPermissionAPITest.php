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

namespace OrangeHRM\Tests\Pim\Api;

use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Api\EmployeeCustomFieldAPI;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;

/**
 * Checks the permission of the PIM screen (e.g. salary_attachment, salary_custom_fields) per employee.
 *
 * @group Pim
 * @group APIv2
 */
class EmployeeCustomFieldScreenPermissionAPITest extends EndpointIntegrationTestCase
{
    /**
     * @dataProvider dataProviderForTestGetOne
     */
    public function testGetOne(TestCaseParams $testCaseParams): void
    {
        $this->runTestCase('getOne', $testCaseParams);
    }

    public function dataProviderForTestGetOne(): array
    {
        return $this->getTestCases('EmployeeCustomFieldScreenPermissionAPITestCases.yaml', 'GetOne');
    }

    private function runTestCase(string $operation, TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('EmployeeScreenPermissionAPI.yml', null, true);
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $api = $this->getApiEndpointMock(EmployeeCustomFieldAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, $operation, $testCaseParams);
    }
}
