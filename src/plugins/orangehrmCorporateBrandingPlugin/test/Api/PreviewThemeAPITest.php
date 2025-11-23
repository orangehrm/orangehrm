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

namespace OrangeHRM\Tests\CorporateBranding\Api;

use OrangeHRM\CorporateBranding\Api\PreviewThemeAPI;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;

class PreviewThemeAPITest extends EndpointIntegrationTestCase
{
    /**
     * @dataProvider dataProviderForTestCreate
     */
    public function testCreate(TestCaseParams $testCaseParams): void
    {
        $this->registerServices($testCaseParams);
        $api = $this->getApiEndpointMock(PreviewThemeAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'create', $testCaseParams);
    }

    /**
     * @return array
     */
    public function dataProviderForTestCreate(): array
    {
        return $this->getTestCases('PreviewThemeAPITestCases.yaml', 'Create');
    }

    public function testDelete(): void
    {
        $api = new PreviewThemeAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new PreviewThemeAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }

    public function testGetAll(): void
    {
        $api = new PreviewThemeAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getAll();
    }

    public function testGetValidationRuleForGetAll(): void
    {
        $api = new PreviewThemeAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForGetAll();
    }
}
