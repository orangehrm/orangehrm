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

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeSkill;
use OrangeHRM\Entity\Skill;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Api\EmployeeSkillAPI;
use OrangeHRM\Pim\Dao\EmployeeSkillDao;
use OrangeHRM\Pim\Service\EmployeeSkillService;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;

/**
 * @group Pim
 * @group APIv2
 */
class EmployeeSkillAPITest extends EndpointTestCase
{
    public function testGetEmployeeSkillService(): void
    {
        $api = new EmployeeSkillAPI($this->getRequest());
        $this->assertTrue($api->getEmployeeSkillService() instanceof EmployeeSkillService);
    }

    public function testGetOne(): void
    {
        $empNumber = 1;
        $employeeSkillDao = $this->getMockBuilder(EmployeeSkillDao::class)
            ->onlyMethods(['getEmployeeSkillById'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $skill = new Skill();
        $skill->setId(1);
        $skill->setName('Driving');
        $skill->setDescription('Driving Skills');
        $employeeSkill = new EmployeeSkill();
        $employeeSkill->setEmployee($employee);
        $employeeSkill->setSkill($skill);
        $employeeSkill->setComments('Comments');
        $employeeSkill->setYearsOfExp(3);

        $employeeSkillDao->expects($this->exactly(1))
            ->method('getEmployeeSkillById')
            ->with(1, 1)
            ->will($this->returnValue($employeeSkill));

        $employeeSkillService = $this->getMockBuilder(EmployeeSkillService::class)
            ->onlyMethods(['getEmployeeSkillDao'])
            ->getMock();

        $employeeSkillService->expects($this->exactly(1))
            ->method('getEmployeeSkillDao')
            ->willReturn($employeeSkillDao);

        /** @var MockObject&EmployeeSkillAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeSkillAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_ID => 1
                ]
            ]
        )->onlyMethods(['getEmployeeSkillService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getEmployeeSkillService')
            ->will($this->returnValue($employeeSkillService));

        $result = $api->getOne();
        $this->assertEquals(
            [
                "yearsOfExperience" => 3,
                "comments" => "Comments",
                "skill" => [
                    "id" => 1,
                    "name" => "Driving",
                    "description" => "Driving Skills"
                ]
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                "empNumber" => 1,
            ],
            $result->getMeta()->all()
        );
    }

    public function testGetValidationRuleForGetOne(): void
    {
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAccessibleEntityIds'])
            ->getMock();
        $userRoleManager->expects($this->exactly(0))
            ->method('getAccessibleEntityIds')
            ->willReturn([1, 2]);

        $authUser = $this->getMockBuilder(\OrangeHRM\Authentication\Auth\User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->once())
            ->method('getEmpNumber')
            ->willReturn(1);
        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::AUTH_USER => $authUser
            ]
        );
        $api = new EmployeeSkillAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetOne();
        $this->assertTrue(
            $this->validate(
                [CommonParams::PARAMETER_EMP_NUMBER => 1, CommonParams::PARAMETER_ID => 1],
                $rules
            )
        );
    }

    public function testUpdate()
    {
        $empNumber = 1;
        $employeeSkillDao = $this->getMockBuilder(EmployeeSkillDao::class)
            ->onlyMethods(['saveEmployeeSkill', 'getEmployeeSkillById'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $skill = new Skill();
        $skill->setId(1);
        $skill->setName('Driving');
        $skill->setDescription('Driving Skills');
        $employeeSkill = new EmployeeSkill();
        $employeeSkill->setEmployee($employee);
        $employeeSkill->setSkill($skill);
        $employeeSkill->setComments('Comments');
        $employeeSkill->setYearsOfExp(3);

        $employeeSkillDao->expects($this->exactly(1))
            ->method('getEmployeeSkillById')
            ->with(1, 1)
            ->willReturn($employeeSkill);

        $employeeSkillDao->expects($this->exactly(1))
            ->method('saveEmployeeSkill')
            ->with($employeeSkill)
            ->will($this->returnValue($employeeSkill));

        $employeeSkillService = $this->getMockBuilder(EmployeeSkillService::class)
            ->onlyMethods(['getEmployeeSkillDao'])
            ->getMock();

        $employeeSkillService->expects($this->exactly(2))
            ->method('getEmployeeSkillDao')
            ->willReturn($employeeSkillDao);

        /** @var MockObject&EmployeeSkillAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeSkillAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_ID => 1
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    EmployeeSkillAPI::PARAMETER_SKILL_ID => 1,
                    EmployeeSkillAPI::PARAMETER_YEARS_OF_EXP => 5,
                    EmployeeSkillAPI::PARAMETER_COMMENTS => "Comment new",
                ]
            ]
        )->onlyMethods(['getEmployeeSkillService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getEmployeeSkillService')
            ->will($this->returnValue($employeeSkillService));

        $result = $api->update();
        $this->assertEquals(
            [
                "yearsOfExperience" => 5,
                "comments" => "Comment new",
                "skill" => [
                    "id" => 1,
                    "name" => "Driving",
                    "description" => "Driving Skills"
                ]
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                "empNumber" => 1,
            ],
            $result->getMeta()->all()
        );
    }

    public function testGetValidationRuleForUpdate(): void
    {
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAccessibleEntityIds'])
            ->getMock();
        $userRoleManager->expects($this->exactly(0))
            ->method('getAccessibleEntityIds')
            ->willReturn([1, 2]);

        $authUser = $this->getMockBuilder(\OrangeHRM\Authentication\Auth\User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->once())
            ->method('getEmpNumber')
            ->willReturn(1);
        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::AUTH_USER => $authUser
            ]
        );
        $api = new EmployeeSkillAPI($this->getRequest());
        $rules = $api->getValidationRuleForUpdate();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    CommonParams::PARAMETER_ID => 1,
                    EmployeeSkillAPI::PARAMETER_COMMENTS => "comment",
                    EmployeeSkillAPI::PARAMETER_YEARS_OF_EXP => 5
                ],
                $rules
            )
        );
    }

    public function testDelete()
    {
        $empNumber = 1;
        $employeeSkillDao = $this->getMockBuilder(EmployeeSkillDao::class)
            ->onlyMethods(['deleteEmployeeSkills'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $skill = new Skill();
        $skill->setId(1);
        $skill->setName('Driving');
        $skill->setDescription('Driving Skills');
        $employeeSkill = new EmployeeSkill();
        $employeeSkill->setEmployee($employee);
        $employeeSkill->setSkill($skill);
        $employeeSkill->setComments('Comments');
        $employeeSkill->setYearsOfExp(3);

        $employeeSkillDao->expects($this->exactly(1))
            ->method('deleteEmployeeSkills')
            ->with(1, [1])
            ->willReturn(1);

        $employeeSkillService = $this->getMockBuilder(EmployeeSkillService::class)
            ->onlyMethods(['getEmployeeSkillDao'])
            ->getMock();

        $employeeSkillService->expects($this->exactly(1))
            ->method('getEmployeeSkillDao')
            ->willReturn($employeeSkillDao);

        /** @var MockObject&EmployeeSkillAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeSkillAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    CommonParams::PARAMETER_IDS => [1],
                ]
            ]
        )->onlyMethods(['getEmployeeSkillService'])
            ->getMock();
        $api->expects($this->exactly(1))
            ->method('getEmployeeSkillService')
            ->will($this->returnValue($employeeSkillService));

        $result = $api->delete();
        $this->assertEquals(
            [
                1
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                "empNumber" => 1,
            ],
            $result->getMeta()->all()
        );
    }

    public function testGetValidationRuleForDelete(): void
    {
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAccessibleEntityIds'])
            ->getMock();
        $userRoleManager->expects($this->exactly(0))
            ->method('getAccessibleEntityIds')
            ->willReturn([1, 2]);

        $authUser = $this->getMockBuilder(\OrangeHRM\Authentication\Auth\User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->once())
            ->method('getEmpNumber')
            ->willReturn(1);
        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::AUTH_USER => $authUser
            ]
        );
        $api = new EmployeeSkillAPI($this->getRequest());
        $rules = $api->getValidationRuleForDelete();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    CommonParams::PARAMETER_IDS => [1],
                ],
                $rules
            )
        );
    }

    public function testCreate()
    {
        $empNumber = 1;
        $employeeSkillDao = $this->getMockBuilder(EmployeeSkillDao::class)
            ->onlyMethods(['saveEmployeeSkill', 'getEmployeeSkillById'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $skill = new Skill();
        $skill->setId(1);
        $skill->setName('Driving');
        $skill->setDescription('Driving Skills');
        $employeeSkill = new EmployeeSkill();
        $employeeSkill->setEmployee($employee);
        $employeeSkill->setSkill($skill);
        $employeeSkill->setComments('Comments');
        $employeeSkill->setYearsOfExp(3);

        $employeeSkillDao->expects($this->exactly(1))
            ->method('getEmployeeSkillById')
            ->with(1, 1)
            ->willReturn($employeeSkill);

        $employeeSkillDao->expects($this->exactly(1))
            ->method('saveEmployeeSkill')
            ->with($employeeSkill)
            ->will($this->returnValue($employeeSkill));

        $employeeSkillService = $this->getMockBuilder(EmployeeSkillService::class)
            ->onlyMethods(['getEmployeeSkillDao'])
            ->getMock();

        $employeeSkillService->expects($this->exactly(2))
            ->method('getEmployeeSkillDao')
            ->willReturn($employeeSkillDao);

        /** @var MockObject&EmployeeSkillAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeSkillAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    EmployeeSkillAPI::PARAMETER_SKILL_ID => 1,
                    EmployeeSkillAPI::PARAMETER_YEARS_OF_EXP => 5,
                    EmployeeSkillAPI::PARAMETER_COMMENTS => "Comment new",
                ]
            ]
        )->onlyMethods(['getEmployeeSkillService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getEmployeeSkillService')
            ->will($this->returnValue($employeeSkillService));

        $result = $api->update();
        $this->assertEquals(
            [
                "yearsOfExperience" => 5,
                "comments" => "Comment new",
                "skill" => [
                    "id" => 1,
                    "name" => "Driving",
                    "description" => "Driving Skills"
                ]
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                "empNumber" => 1,
            ],
            $result->getMeta()->all()
        );
    }

    public function testGetValidationRuleForCreate(): void
    {
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAccessibleEntityIds'])
            ->getMock();
        $userRoleManager->expects($this->exactly(0))
            ->method('getAccessibleEntityIds')
            ->willReturn([1, 2]);

        $authUser = $this->getMockBuilder(\OrangeHRM\Authentication\Auth\User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->once())
            ->method('getEmpNumber')
            ->willReturn(1);
        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::AUTH_USER => $authUser
            ]
        );
        $api = new EmployeeSkillAPI($this->getRequest());
        $rules = $api->getValidationRuleForCreate();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    EmployeeSkillAPI::PARAMETER_SKILL_ID => 1,
                    EmployeeSkillAPI::PARAMETER_COMMENTS => "comment",
                    EmployeeSkillAPI::PARAMETER_YEARS_OF_EXP => 5
                ],
                $rules
            )
        );
    }


    public function testGetAll()
    {
        $empNumber = 1;
        $employeeSkillDao = $this->getMockBuilder(EmployeeSkillDao::class)
            ->onlyMethods(['searchEmployeeSkill', 'getSearchEmployeeSkillsCount'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $skill1 = new Skill();
        $skill1->setId(1);
        $skill1->setName('Driving');
        $skill1->setDescription('Driving Skills');
        $skill2 = new Skill();
        $skill2->setId(2);
        $skill2->setName('Swimming');
        $skill2->setDescription('Swimming Skills');
        $employeeSkill1 = new EmployeeSkill();
        $employeeSkill1->setEmployee($employee);
        $employeeSkill1->setSkill($skill1);
        $employeeSkill1->setComments('Comments');
        $employeeSkill1->setYearsOfExp(3);
        $employeeSkill2 = new EmployeeSkill();
        $employeeSkill2->setEmployee($employee);
        $employeeSkill2->setSkill($skill2);
        $employeeSkill2->setComments('Comments');
        $employeeSkill2->setYearsOfExp(3);

        $employeeSkillDao->expects($this->exactly(1))
            ->method('searchEmployeeSkill')
            ->willReturn([$employeeSkill1, $employeeSkill2]);

        $employeeSkillDao->expects($this->exactly(1))
            ->method('getSearchEmployeeSkillsCount')
            ->willReturn(2);

        $employeeSkillService = $this->getMockBuilder(EmployeeSkillService::class)
            ->onlyMethods(['getEmployeeSkillDao'])
            ->getMock();

        $employeeSkillService->expects($this->exactly(2))
            ->method('getEmployeeSkillDao')
            ->willReturn($employeeSkillDao);

        /** @var MockObject&EmployeeSkillAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeSkillAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    EmployeeSkillAPI::PARAMETER_YEARS_OF_EXP => 3,
                    EmployeeSkillAPI::PARAMETER_COMMENTS => "Comments",
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
                    "yearsOfExperience" => 3,
                    "comments" => "Comments",
                    "skill" => [
                        "id" => 1,
                        "name" => "Driving",
                        "description" => "Driving Skills"
                    ]
                ],
                [
                    "yearsOfExperience" => 3,
                    "comments" => "Comments",
                    "skill" => [
                        "id" => 2,
                        "name" => "Swimming",
                        "description" => "Swimming Skills"
                    ]
                ]
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                "empNumber" => 1,
                "total" => 2
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
        $userRoleManager->expects($this->exactly(0))
            ->method('getAccessibleEntityIds')
            ->willReturn([1, 2]);

        $authUser = $this->getMockBuilder(\OrangeHRM\Authentication\Auth\User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->once())
            ->method('getEmpNumber')
            ->willReturn(1);
        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::AUTH_USER => $authUser
            ]
        );
        $api = new EmployeeSkillAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetAll();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                ],
                $rules
            )
        );
    }
}
