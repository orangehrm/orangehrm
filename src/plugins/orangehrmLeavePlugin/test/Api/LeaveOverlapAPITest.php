<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Tests\Leave\Api;

use OrangeHRM\Authentication\Auth\User;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Api\LeaveCommonParams;
use OrangeHRM\Leave\Api\LeaveOverlapAPI;
use OrangeHRM\Leave\Dto\LeaveDuration;
use OrangeHRM\Leave\Dto\LeaveParameterObject;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Leave
 * @group APIv2
 */
class LeaveOverlapAPITest extends EndpointIntegrationTestCase
{
    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmLeavePlugin/test/fixtures/LeaveBalanceAPI.yml';
        TestDataService::populate($fixture);
    }

    public function testDelete(): void
    {
        $api = new LeaveOverlapAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new LeaveOverlapAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }

    public function testCreate(): void
    {
        $api = new LeaveOverlapAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->create();
    }

    public function testGetValidationRuleForCreate(): void
    {
        $api = new LeaveOverlapAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForCreate();
    }

    public function testGetValidationRuleForGetAll(): void
    {
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAccessibleEntityIds'])
            ->getMock();
        $userRoleManager->expects($this->once())
            ->method('getAccessibleEntityIds')
            ->willReturn([1, 2]);

        $authUser = $this->getMockBuilder(User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->exactly(2))
            ->method('getEmpNumber')
            ->willReturn(1);

        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::AUTH_USER => $authUser,
            ]
        );
        $queryParams = [
            LeaveCommonParams::PARAMETER_FROM_DATE => '2011-01-01',
            LeaveCommonParams::PARAMETER_TO_DATE => '2011-01-02',
        ];
        $api = new LeaveOverlapAPI($this->getRequest($queryParams));
        $rules = $api->getValidationRuleForGetAll();
        $this->assertTrue($this->validate($queryParams, $rules));

        $queryParams = [
            CommonParams::PARAMETER_EMP_NUMBER => 1,
            LeaveCommonParams::PARAMETER_FROM_DATE => '2011-01-01',
            LeaveCommonParams::PARAMETER_TO_DATE => '2011-01-01',
        ];
        $api = new LeaveOverlapAPI($this->getRequest($queryParams));
        $rules = $api->getValidationRuleForGetAll();
        $this->assertTrue($this->validate($queryParams, $rules));

        $queryParams = [
            LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID => 1,
            LeaveCommonParams::PARAMETER_FROM_DATE => '2011-01-01',
            LeaveCommonParams::PARAMETER_TO_DATE => '2011-01-02',
        ];
        $api = new LeaveOverlapAPI($this->getRequest($queryParams));
        $rules = $api->getValidationRuleForGetAll();
        $this->assertInvalidParamException(
        // invalid param
            fn () => $this->validate($queryParams, $rules),
            [LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID]
        );

        $queryParams = [
            LeaveCommonParams::PARAMETER_COMMENT => 'Test',
            LeaveCommonParams::PARAMETER_FROM_DATE => '2011-01-01',
            LeaveCommonParams::PARAMETER_TO_DATE => '2011-01-02',
        ];
        $api = new LeaveOverlapAPI($this->getRequest($queryParams));
        $rules = $api->getValidationRuleForGetAll();
        $this->assertInvalidParamException(
        // invalid param
            fn () => $this->validate($queryParams, $rules),
            [LeaveCommonParams::PARAMETER_COMMENT]
        );

        $queryParams = [
            CommonParams::PARAMETER_EMP_NUMBER => 3,
            LeaveCommonParams::PARAMETER_FROM_DATE => '2011-01-01',
            LeaveCommonParams::PARAMETER_TO_DATE => '2011-01-02',
        ];
        $api = new LeaveOverlapAPI($this->getRequest($queryParams));
        $rules = $api->getValidationRuleForGetAll();
        $this->assertInvalidParamException(
        // inaccessible employee number
            fn () => $this->validate($queryParams, $rules),
            [CommonParams::PARAMETER_EMP_NUMBER]
        );

        $queryParams = [LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-23'];
        $api = new LeaveOverlapAPI($this->getRequest($queryParams));
        $rules = $api->getValidationRuleForGetAll();
        $this->assertInvalidParamException(
        // if defined only from date, to date also need to define
            fn () => $this->validate($queryParams, $rules),
            [
                LeaveCommonParams::PARAMETER_TO_DATE,
                LeaveCommonParams::PARAMETER_FROM_DATE,
                LeaveCommonParams::PARAMETER_DURATION,
            ]
        );

        $queryParams = [LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-24'];
        $api = new LeaveOverlapAPI($this->getRequest($queryParams));
        $rules = $api->getValidationRuleForGetAll();
        $this->assertInvalidParamException(
        // if defined only to date, from date also need to define
            fn () => $this->validate($queryParams, $rules),
            [LeaveCommonParams::PARAMETER_FROM_DATE]
        );

        $queryParams = [
            LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-25',
            LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-24',
        ];
        $api = new LeaveOverlapAPI($this->getRequest($queryParams));
        $rules = $api->getValidationRuleForGetAll();
        $this->assertInvalidParamException(
        // from date < to date
            fn () => $this->validate($queryParams, $rules),
            [LeaveCommonParams::PARAMETER_FROM_DATE, LeaveCommonParams::PARAMETER_DURATION]
        );

        $queryParams = [
            LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-24',
            LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-24',
            LeaveCommonParams::PARAMETER_DURATION => [
                LeaveCommonParams::PARAMETER_DURATION_TYPE => LeaveDuration::FULL_DAY,
            ],
        ];
        $api = new LeaveOverlapAPI($this->getRequest($queryParams));
        $rules = $api->getValidationRuleForGetAll();
        $this->assertTrue($this->validate($queryParams, $rules));

        $queryParams = [
            LeaveCommonParams::PARAMETER_FROM_DATE => '2021-08-24',
            LeaveCommonParams::PARAMETER_TO_DATE => '2021-08-25',
            LeaveCommonParams::PARAMETER_PARTIAL_OPTION => LeaveParameterObject::PARTIAL_OPTION_ALL,
            LeaveCommonParams::PARAMETER_DURATION => [
                LeaveCommonParams::PARAMETER_DURATION_TYPE => LeaveDuration::HALF_DAY_MORNING,
            ],
        ];
        $api = new LeaveOverlapAPI($this->getRequest($queryParams));
        $rules = $api->getValidationRuleForGetAll();
        $this->assertTrue($this->validate($queryParams, $rules));
    }

    /**
     * @dataProvider dataProviderForTestGetAll
     */
    public function testGetAll(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('LeaveOverlapAPITest.yaml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);

        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(LeaveOverlapAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getAll', $testCaseParams);
    }

    /**
     * @return array
     */
    public function dataProviderForTestGetAll(): array
    {
        return $this->getTestCases('LeaveOverlapAPITestCases.yaml', 'GetAll');
    }
}
