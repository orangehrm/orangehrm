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

namespace OrangeHRM\Tests\Attendance\Api;

use OrangeHRM\Attendance\Api\TimezonesAPI;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;

class TimezonesAPITest extends EndpointIntegrationTestCase
{
    /**
     * @dataProvider dataProviderForTestGetAll
     */
    public function testGetAll(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('TimezonesAPI.yaml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(TimezonesAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getAll', $testCaseParams);
    }

    public function dataProviderForTestGetAll(): array
    {
        $phpVersion = phpversion();
        $testCases = $this->getTestCases('TimezonesAPITestCases.yaml', 'GetAll');
        if (version_compare($phpVersion, '7.4.29', '>=')) {
            $testCase = &$testCases["Get Timezones by filter(ESS) - tok"][0];
            $modifiedTimeZone = [
                "name" => "Antarctica/Vostok",
                "label" => "+07:00",
                "offset" => "7.0"
            ];
            $timezones = $testCase->getResultData();
            foreach ($timezones as $key => $timezone) {
                if ($timezone['name'] === "Antarctica/Vostok") {
                    $timezones[$key] = $modifiedTimeZone;
                    break;
                }
            }
            $testCase->setResultData($timezones);
            return $testCases;
        } else {
            return $this->getTestCases('TimezonesAPITestCases.yaml', 'GetAll');
        }
    }

    public function testDelete(): void
    {
        $api = new TimezonesAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new TimezonesAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }

    public function testCreate(): void
    {
        $api = new TimezonesAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->create();
    }

    public function testGetValidationRuleForCreate(): void
    {
        $api = new TimezonesAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForCreate();
    }
}
