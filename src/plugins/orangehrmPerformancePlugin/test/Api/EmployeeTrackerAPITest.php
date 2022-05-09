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

namespace OrangeHRM\Tests\Performance\Api;

use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Services;
use OrangeHRM\Performance\Api\EmployeeTrackerAPI;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;
use OrangeHRM\Tests\Util\Mock\MockAuthUser;

/**
 * @group Performance
 * @group APIv2
 */
class EmployeeTrackerAPITest extends EndpointIntegrationTestCase
{
    private const ADMIN_USER_ROLE_ID = 1;
    private const ESS_USER_ROLE_ID = 2;

    /**
     * @dataProvider dataProviderForTestGetAll
     */
    public function testGetAll(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('EmployeeTrackerAPITest.yaml');

        $authUser = $this->getMockBuilder(MockAuthUser::class)
            ->onlyMethods(['getUserId', 'getEmpNumber', 'getUserRoleId'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->method('getUserId')
            ->willReturn($testCaseParams->getUserId());
        $authUser->method('getEmpNumber')
            ->willReturn(
                $this->getEntityReference(
                    User::class,
                    $testCaseParams->getUserId()
                )->getEmployee()->getEmpNumber()
            );
        // In the given fixtures, only user 1 is an Admin
        $authUser->method('getUserRoleId')
            ->willReturn(
                $testCaseParams->getUserId() === 1 ?
                    self::ADMIN_USER_ROLE_ID :
                    self::ESS_USER_ROLE_ID
            );
        $this->createKernelWithMockServices([Services::AUTH_USER => $authUser]);

        $this->registerServices($testCaseParams);
        $api = $this->getApiEndpointMock(EmployeeTrackerAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getAll', $testCaseParams);
    }

    public function dataProviderForTestGetAll(): array
    {
        return $this->getTestCases('EmployeeTrackerAPITestCases.yaml', 'GetAll');
    }

    public function testCreate(): void
    {
        $api = new EmployeeTrackerAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->create();
    }

    public function testGetValidationRuleForCreate(): void
    {
        $api = new EmployeeTrackerAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForCreate();
    }

    public function testDelete(): void
    {
        $api = new EmployeeTrackerAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new EmployeeTrackerAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }
}
