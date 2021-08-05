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
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Entity\Skill;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Api\EmployeeAllowedSkillAPI;
use OrangeHRM\Pim\Dao\EmployeeSkillDao;
use OrangeHRM\Pim\Service\EmployeeSkillService;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;

/**
 * @group Pim
 * @group APIv2
 */
class EmployeeAllowedSkillAPITest extends EndpointTestCase
{
    public function testDelete(): void
    {
        $api = new EmployeeAllowedSkillAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new EmployeeAllowedSkillAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }

    public function testCreate(): void
    {
        $api = new EmployeeAllowedSkillAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->create();
    }

    public function testGetValidationRuleForCreate(): void
    {
        $api = new EmployeeAllowedSkillAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForCreate();
    }

    public function testGetAll(): void
    {
        $empNumber = 1;
        $employeeSkillDao = $this->getMockBuilder(EmployeeSkillDao::class)
            ->onlyMethods(['getEmployeeAllowedSkills', 'getEmployeeAllowedSkillsCount'])
            ->getMock();

        $skill = new Skill();
        $skill->setId(2);
        $skill->setName('Driving');

        $employeeSkillDao->expects($this->once())
            ->method('getEmployeeAllowedSkills')
            ->will($this->returnValue([$skill]));
        $employeeSkillDao->expects($this->once())
            ->method('getEmployeeAllowedSkillsCount')
            ->will($this->returnValue(1));

        $employeeSkillService = $this->getMockBuilder(EmployeeSkillService::class)
            ->onlyMethods(['getEmployeeSkillDao'])
            ->getMock();

        $employeeSkillService->expects($this->exactly(2))
            ->method('getEmployeeSkillDao')
            ->willReturn($employeeSkillDao);

        /** @var MockObject&EmployeeAllowedSkillAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeAllowedSkillAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ]
            ]
        )->onlyMethods(['getEmployeeSkillService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getEmployeeSkillService')
            ->will($this->returnValue($employeeSkillService));

        $result = $api->getAll();
        $this->assertEquals(
            [
                [
                    "id" => 2,
                    "name" => 'Driving',
                ]
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                "empNumber" => 1,
                "total" => 1
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
        $api = new EmployeeAllowedSkillAPI($this->getRequest());
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
