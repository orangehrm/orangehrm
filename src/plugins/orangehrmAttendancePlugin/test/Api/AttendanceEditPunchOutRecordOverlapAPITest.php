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

use OrangeHRM\Attendance\Api\AttendanceEditPunchOutRecordOverlapAPI;
use OrangeHRM\Config\Config;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Attendance
 * @group APIv2
 */
class AttendanceEditPunchOutRecordOverlapAPITest extends EndpointIntegrationTestCase
{
    public static function setUpBeforeClass(): void
    {
        TestDataService::populate(
            Config::get(Config::PLUGINS_DIR) .
            '/orangehrmAttendancePlugin/test/fixtures/EditAttendancePunchOutOverlap.yaml',
            true
        );
    }

    /**
     * @dataProvider dataProviderForTestGetOne
     */
    public function testGetOne(TestCaseParams $testCaseParams): void
    {
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(AttendanceEditPunchOutRecordOverlapAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getOne', $testCaseParams);
    }

    public function dataProviderForTestGetOne(): array
    {
        return $this->getTestCases('EditAttendancePunchOutOverlapTestCase.yaml', 'GetOne');
    }

    public function testUpdate(): void
    {
        $api = new AttendanceEditPunchOutRecordOverlapAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->update();
    }

    public function testGetValidationRuleForUpdate(): void
    {
        $api = new AttendanceEditPunchOutRecordOverlapAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForUpdate();
    }

    public function testDelete(): void
    {
        $api = new AttendanceEditPunchOutRecordOverlapAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new AttendanceEditPunchOutRecordOverlapAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }
}
