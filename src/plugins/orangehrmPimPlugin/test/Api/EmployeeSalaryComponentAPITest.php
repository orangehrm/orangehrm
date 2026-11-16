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

use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Api\EmployeeSalaryComponentAPI;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;
use OrangeHRM\Tests\Util\Mock\MockAuthUser;

/**
 * @group Pim
 * @group APIv2
 */
class EmployeeSalaryComponentAPITest extends EndpointIntegrationTestCase
{
    /**
     * @dataProvider dataProviderForTestGetAll
     */
    public function testGetAll(TestCaseParams $testCaseParams): void
    {
        $this->runTestCase('getAll', $testCaseParams);
    }

    public function dataProviderForTestGetAll(): array
    {
        return $this->getTestCases('EmployeeSalaryComponentAPITestCases.yaml', 'GetAll');
    }

    /**
     * @dataProvider dataProviderForTestGetOne
     */
    public function testGetOne(TestCaseParams $testCaseParams): void
    {
        $this->runTestCase('getOne', $testCaseParams);
    }

    public function dataProviderForTestGetOne(): array
    {
        return $this->getTestCases('EmployeeSalaryComponentAPITestCases.yaml', 'GetOne');
    }

    /**
     * @dataProvider dataProviderForTestCreate
     */
    public function testCreate(TestCaseParams $testCaseParams): void
    {
        $this->runTestCase('create', $testCaseParams);
    }

    public function dataProviderForTestCreate(): array
    {
        return $this->getTestCases('EmployeeSalaryComponentAPITestCases.yaml', 'Create');
    }

    /**
     * @dataProvider dataProviderForTestUpdate
     */
    public function testUpdate(TestCaseParams $testCaseParams): void
    {
        $this->runTestCase('update', $testCaseParams);
    }

    public function dataProviderForTestUpdate(): array
    {
        return $this->getTestCases('EmployeeSalaryComponentAPITestCases.yaml', 'Update');
    }

    /**
     * @dataProvider dataProviderForTestDelete
     */
    public function testDelete(TestCaseParams $testCaseParams): void
    {
        $this->runTestCase('delete', $testCaseParams);
    }

    public function dataProviderForTestDelete(): array
    {
        return $this->getTestCases('EmployeeSalaryComponentAPITestCases.yaml', 'Delete');
    }

    private function runTestCase(string $operation, TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('EmployeeSalaryComponentAPI.yml', null, true);
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $api = $this->getApiEndpointMock(EmployeeSalaryComponentAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, $operation, $testCaseParams);
    }

    /**
     * Same as the parent, but also supports a user without an employee record.
     */
    protected function getMockAuthUser(TestCaseParams $testCaseParams): MockAuthUser
    {
        $authUser = $this->getMockBuilder(MockAuthUser::class)
            ->onlyMethods(['getUserId', 'getEmpNumber', 'getUserRoleName'])
            ->disableOriginalConstructor()
            ->getMock();
        /** @var User $user */
        $user = $this->getEntityReference(User::class, $testCaseParams->getUserId());
        $authUser->method('getUserId')
            ->willReturn($testCaseParams->getUserId());
        $authUser->method('getEmpNumber')
            ->willReturn($user->getEmployee() ? $user->getEmployee()->getEmpNumber() : null);
        $authUser->method('getUserRoleName')
            ->willReturn($user->getUserRole()->getName());
        return $authUser;
    }
}
