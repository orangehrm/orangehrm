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

namespace OrangeHRM\Tests\Pim\Api;

use OrangeHRM\Authentication\Auth\User;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Api\EmployeeAllowedLanguageAPI;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group APIv2
 */
class EmployeeAllowedLanguageAPITest extends EndpointTestCase
{
    protected function loadFixtures(): void
    {
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmPimPlugin/test/fixtures/EmployeeLanguageDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testDelete(): void
    {
        $api = new EmployeeAllowedLanguageAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new EmployeeAllowedLanguageAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }

    public function testCreate(): void
    {
        $api = new EmployeeAllowedLanguageAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->create();
    }

    public function testGetValidationRuleForCreate(): void
    {
        $api = new EmployeeAllowedLanguageAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForCreate();
    }

    public function testGetAll(): void
    {
        $this->loadFixtures();

        /** @var MockObject&EmployeeAllowedLanguageAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeAllowedLanguageAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                ]
            ]
        )->onlyMethods([])
            ->getMock();

        $result = $api->getAll();
        $this->assertEquals(
            [
                [
                    "id" => 3,
                    "name" => 'Dutch',
                    "allowedFluencyIds" => [1, 2, 3]
                ],
                [
                    "id" => 1,
                    "name" => 'English',
                    "allowedFluencyIds" => [1, 3]
                ],
                [
                    "id" => 2,
                    "name" => 'Spanish',
                    "allowedFluencyIds" => [2, 3]
                ]
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                "empNumber" => 1,
                "total" => 3,
            ],
            $result->getMeta()->all()
        );

        /** @var MockObject&EmployeeAllowedLanguageAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeAllowedLanguageAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => 4,
                ]
            ]
        )->onlyMethods([])
            ->getMock();

        $result = $api->getAll();
        $this->assertEquals(
            [
                [
                    "id" => 3,
                    "name" => 'Dutch',
                    "allowedFluencyIds" => [1, 2, 3]
                ],
                [
                    "id" => 2,
                    "name" => 'Spanish',
                    "allowedFluencyIds" => [1, 2, 3]
                ]
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                "empNumber" => 4,
                "total" => 2,
            ],
            $result->getMeta()->all()
        );
    }

    public function testGetValidationRuleForGetAll(): void
    {
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAccessibleEntityIds'])
            ->getMock();
        $userRoleManager->expects($this->exactly(2))
            ->method('getAccessibleEntityIds')
            ->willReturn([1, 2]);

        $authUser = $this->getMockBuilder(User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->exactly(2))
            ->method('getEmpNumber')
            ->willReturn(2);
        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::AUTH_USER => $authUser
            ]
        );
        $api = new EmployeeAllowedLanguageAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetAll();
        $this->assertTrue(
            $this->validate(
                [CommonParams::PARAMETER_EMP_NUMBER => 1],
                $rules
            )
        );

        $this->expectInvalidParamException();
        $this->validate(
            [CommonParams::PARAMETER_EMP_NUMBER => 100],
            $rules
        );
    }
}
