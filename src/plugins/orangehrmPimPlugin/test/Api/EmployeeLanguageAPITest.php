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
use OrangeHRM\Core\Api\V2\Exception\InvalidParamException;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeLanguage;
use OrangeHRM\Entity\Language;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Api\EmployeeLanguageAPI;
use OrangeHRM\Pim\Dao\EmployeeLanguageDao;
use OrangeHRM\Pim\Service\EmployeeLanguageService;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group APIv2
 */
class EmployeeLanguageAPITest extends EndpointTestCase
{
    protected function loadFixtures(): void
    {
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmPimPlugin/test/fixtures/EmployeeLanguageDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmployeeLanguageService(): void
    {
        $api = new EmployeeLanguageAPI($this->getRequest());
        $this->assertTrue($api->getEmployeeLanguageService() instanceof EmployeeLanguageService);
    }

    public function testGetOne(): void
    {
        $empNumber = 1;
        $employeeLanguageDao = $this->getMockBuilder(EmployeeLanguageDao::class)
            ->onlyMethods(['getEmployeeLanguage'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $language = new Language();
        $language->setId(1);
        $language->setName('English');
        $employeeLanguage = new EmployeeLanguage();
        $employeeLanguage->setEmployee($employee);
        $employeeLanguage->setLanguage($language);
        $employeeLanguage->setComment('Comments');
        $employeeLanguage->setFluency(3);
        $employeeLanguage->setCompetency(2);

        $map = [
            [1, 1, 3, $employeeLanguage],
            [1, 1, 1, null],
        ];

        $employeeLanguageDao->expects($this->exactly(2))
            ->method('getEmployeeLanguage')
            ->will($this->returnValueMap($map));

        $employeeLanguageService = $this->getMockBuilder(EmployeeLanguageService::class)
            ->onlyMethods(['getEmployeeLanguageDao'])
            ->getMock();

        $employeeLanguageService->expects($this->exactly(2))
            ->method('getEmployeeLanguageDao')
            ->willReturn($employeeLanguageDao);

        /** @var MockObject&EmployeeLanguageAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeLanguageAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    EmployeeLanguageAPI::PARAMETER_LANGUAGE_ID => 1,
                    EmployeeLanguageAPI::PARAMETER_FLUENCY_ID => 3,
                ]
            ]
        )->onlyMethods(['getEmployeeLanguageService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getEmployeeLanguageService')
            ->will($this->returnValue($employeeLanguageService));

        $result = $api->getOne();
        $this->assertEquals(
            [
                "language" => [
                    'id' => 1,
                    'name' => 'English',
                ],
                "fluency" => [
                    'id' => 3,
                    'name' => 'Reading',
                ],
                "competency" => [
                    'id' => 2,
                    'name' => 'Basic',
                ],
                "comment" => "Comments",
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                "empNumber" => 1,
            ],
            $result->getMeta()->all()
        );

        /** @var MockObject&EmployeeLanguageAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeLanguageAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    EmployeeLanguageAPI::PARAMETER_LANGUAGE_ID => 1,
                    EmployeeLanguageAPI::PARAMETER_FLUENCY_ID => 1,
                ]
            ]
        )->onlyMethods(['getEmployeeLanguageService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getEmployeeLanguageService')
            ->will($this->returnValue($employeeLanguageService));
        $this->expectRecordNotFoundException();
        $api->getOne();
    }

    public function testGetValidationRuleForGetOne(): void
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
        $api = new EmployeeLanguageAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetOne();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    EmployeeLanguageAPI::PARAMETER_LANGUAGE_ID => 1,
                    EmployeeLanguageAPI::PARAMETER_FLUENCY_ID => 1
                ],
                $rules
            )
        );

        $this->expectInvalidParamException();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    EmployeeLanguageAPI::PARAMETER_LANGUAGE_ID => 1,
                    EmployeeLanguageAPI::PARAMETER_FLUENCY_ID => 4
                ],
                $rules
            )
        );
    }

    public function testGetAll(): void
    {
        $empNumber = 1;
        $employeeLanguageDao = $this->getMockBuilder(EmployeeLanguageDao::class)
            ->onlyMethods(['getEmployeeLanguages', 'getEmployeeLanguagesCount'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $language = new Language();
        $language->setId(1);
        $language->setName('English');
        $employeeLanguage = new EmployeeLanguage();
        $employeeLanguage->setEmployee($employee);
        $employeeLanguage->setLanguage($language);
        $employeeLanguage->setComment('Comments');
        $employeeLanguage->setFluency(3);
        $employeeLanguage->setCompetency(2);

        $employeeLanguageDao->expects($this->exactly(1))
            ->method('getEmployeeLanguages')
            ->will($this->returnValue([$employeeLanguage]));
        $employeeLanguageDao->expects($this->exactly(1))
            ->method('getEmployeeLanguagesCount')
            ->will($this->returnValue(1));

        $employeeLanguageService = $this->getMockBuilder(EmployeeLanguageService::class)
            ->onlyMethods(['getEmployeeLanguageDao'])
            ->getMock();

        $employeeLanguageService->expects($this->exactly(2))
            ->method('getEmployeeLanguageDao')
            ->willReturn($employeeLanguageDao);

        /** @var MockObject&EmployeeLanguageAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeLanguageAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ]
            ]
        )->onlyMethods(['getEmployeeLanguageService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getEmployeeLanguageService')
            ->will($this->returnValue($employeeLanguageService));

        $result = $api->getAll();
        $this->assertEquals(
            [
                [
                    "language" => [
                        'id' => 1,
                        'name' => 'English',
                    ],
                    "fluency" => [
                        'id' => 3,
                        'name' => 'Reading',
                    ],
                    "competency" => [
                        'id' => 2,
                        'name' => 'Basic',
                    ],
                    "comment" => "Comments",
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
        $api = new EmployeeLanguageAPI($this->getRequest());
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

    public function testCreate(): void
    {
        $this->loadFixtures();

        $empNumber = 1;
        $employeeLanguageDao = $this->getMockBuilder(EmployeeLanguageDao::class)
            ->onlyMethods(['getEmployeeLanguage', 'saveEmployeeLanguage'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $language = new Language();
        $language->setId(1);
        $language->setName('English');
        $employeeLanguage = new EmployeeLanguage();
        $employeeLanguage->setEmployee($employee);
        $employeeLanguage->setLanguage($language);
        $employeeLanguage->setComment('Comments');
        $employeeLanguage->setFluency(3);
        $employeeLanguage->setCompetency(2);

        $map = [
            [1, 1, 3, $employeeLanguage],
            [1, 1, 1, null],
        ];

        $employeeLanguageDao->expects($this->exactly(2))
            ->method('getEmployeeLanguage')
            ->will($this->returnValueMap($map));
        $employeeLanguageDao->expects($this->exactly(1))
            ->method('saveEmployeeLanguage')
            ->will(
                $this->returnCallback(
                    function (EmployeeLanguage $employeeLanguage) {
                        return $employeeLanguage;
                    }
                )
            );

        $employeeLanguageService = $this->getMockBuilder(EmployeeLanguageService::class)
            ->onlyMethods(['getEmployeeLanguageDao'])
            ->getMock();

        $employeeLanguageService->expects($this->exactly(3))
            ->method('getEmployeeLanguageDao')
            ->willReturn($employeeLanguageDao);

        /** @var MockObject&EmployeeLanguageAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeLanguageAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    EmployeeLanguageAPI::PARAMETER_LANGUAGE_ID => 1,
                    EmployeeLanguageAPI::PARAMETER_FLUENCY_ID => 1,
                    EmployeeLanguageAPI::PARAMETER_COMPETENCY_ID => 1,
                    EmployeeLanguageAPI::PARAMETER_COMMENT => 'Test Comment',
                ]
            ]
        )->onlyMethods(['getEmployeeLanguageService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getEmployeeLanguageService')
            ->will($this->returnValue($employeeLanguageService));

        $result = $api->create();
        $this->assertEquals(
            [
                "language" => [
                    'id' => 1,
                    'name' => 'English',
                ],
                "fluency" => [
                    'id' => 1,
                    'name' => 'Writing',
                ],
                "competency" => [
                    'id' => 1,
                    'name' => 'Poor',
                ],
                "comment" => "Test Comment",
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                "empNumber" => 1,
            ],
            $result->getMeta()->all()
        );

        /** @var MockObject&EmployeeLanguageAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeLanguageAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    EmployeeLanguageAPI::PARAMETER_LANGUAGE_ID => 1,
                    EmployeeLanguageAPI::PARAMETER_FLUENCY_ID => 3,
                    EmployeeLanguageAPI::PARAMETER_COMPETENCY_ID => 1,
                    EmployeeLanguageAPI::PARAMETER_COMMENT => 'Test Comment',
                ]
            ]
        )->onlyMethods(['getEmployeeLanguageService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getEmployeeLanguageService')
            ->will($this->returnValue($employeeLanguageService));

        $this->expectBadRequestException();
        $api->create();
    }

    public function testGetValidationRuleForCreate(): void
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
        $api = new EmployeeLanguageAPI($this->getRequest());
        $rules = $api->getValidationRuleForCreate();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    EmployeeLanguageAPI::PARAMETER_LANGUAGE_ID => 1,
                    EmployeeLanguageAPI::PARAMETER_FLUENCY_ID => 3,
                    EmployeeLanguageAPI::PARAMETER_COMPETENCY_ID => 1,
                    EmployeeLanguageAPI::PARAMETER_COMMENT => str_repeat('අදහස්', 20),
                ],
                $rules
            )
        );

        try {
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    EmployeeLanguageAPI::PARAMETER_LANGUAGE_ID => 1,
                    EmployeeLanguageAPI::PARAMETER_FLUENCY_ID => 4,
                    EmployeeLanguageAPI::PARAMETER_COMPETENCY_ID => 5,
                    EmployeeLanguageAPI::PARAMETER_COMMENT => str_repeat('Comments', 13),
                ],
                $rules
            );
        } catch (InvalidParamException $e) {
            $this->assertCount(3, $e->getErrorBag());
            $paramKeys = array_keys($e->getErrorBag());
            $this->assertEquals(EmployeeLanguageAPI::PARAMETER_FLUENCY_ID, $paramKeys[0]);
            $this->assertEquals(EmployeeLanguageAPI::PARAMETER_COMPETENCY_ID, $paramKeys[1]);
            $this->assertEquals(EmployeeLanguageAPI::PARAMETER_COMMENT, $paramKeys[2]);
        }
    }

    public function testUpdate(): void
    {
        $this->loadFixtures();

        $empNumber = 1;
        $employeeLanguageDao = $this->getMockBuilder(EmployeeLanguageDao::class)
            ->onlyMethods(['getEmployeeLanguage', 'saveEmployeeLanguage'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $language = new Language();
        $language->setId(1);
        $language->setName('English');
        $employeeLanguage = new EmployeeLanguage();
        $employeeLanguage->setEmployee($employee);
        $employeeLanguage->setLanguage($language);
        $employeeLanguage->setComment('Comments');
        $employeeLanguage->setFluency(2);
        $employeeLanguage->setCompetency(2);

        $map = [
            [1, 1, 2, $employeeLanguage],
            [1, 1, 1, null],
        ];

        $employeeLanguageDao->expects($this->exactly(2))
            ->method('getEmployeeLanguage')
            ->will($this->returnValueMap($map));
        $employeeLanguageDao->expects($this->exactly(1))
            ->method('saveEmployeeLanguage')
            ->will(
                $this->returnCallback(
                    function (EmployeeLanguage $employeeLanguage) {
                        return $employeeLanguage;
                    }
                )
            );

        $employeeLanguageService = $this->getMockBuilder(EmployeeLanguageService::class)
            ->onlyMethods(['getEmployeeLanguageDao'])
            ->getMock();

        $employeeLanguageService->expects($this->exactly(3))
            ->method('getEmployeeLanguageDao')
            ->willReturn($employeeLanguageDao);

        /** @var MockObject&EmployeeLanguageAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeLanguageAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    EmployeeLanguageAPI::PARAMETER_LANGUAGE_ID => 1,
                    EmployeeLanguageAPI::PARAMETER_FLUENCY_ID => 2,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    EmployeeLanguageAPI::PARAMETER_COMPETENCY_ID => 3,
                    EmployeeLanguageAPI::PARAMETER_COMMENT => 'Edited Comment',
                ]
            ]
        )->onlyMethods(['getEmployeeLanguageService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getEmployeeLanguageService')
            ->will($this->returnValue($employeeLanguageService));

        $result = $api->update();
        $this->assertEquals(
            [
                "language" => [
                    'id' => 1,
                    'name' => 'English',
                ],
                "fluency" => [
                    'id' => 2,
                    'name' => 'Speaking',
                ],
                "competency" => [
                    'id' => 3,
                    'name' => 'Good',
                ],
                "comment" => "Edited Comment",
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                "empNumber" => 1,
            ],
            $result->getMeta()->all()
        );

        /** @var MockObject&EmployeeLanguageAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeLanguageAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    EmployeeLanguageAPI::PARAMETER_LANGUAGE_ID => 1,
                    EmployeeLanguageAPI::PARAMETER_FLUENCY_ID => 1,
                    EmployeeLanguageAPI::PARAMETER_COMPETENCY_ID => 1,
                    EmployeeLanguageAPI::PARAMETER_COMMENT => 'Test Comment',
                ]
            ]
        )->onlyMethods(['getEmployeeLanguageService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getEmployeeLanguageService')
            ->will($this->returnValue($employeeLanguageService));

        $this->expectRecordNotFoundException();
        $api->update();
    }

    public function testGetValidationRuleForUpdate(): void
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
        $authUser->expects($this->once())
            ->method('getEmpNumber')
            ->willReturn(2);
        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::AUTH_USER => $authUser
            ]
        );
        $api = new EmployeeLanguageAPI($this->getRequest());
        $rules = $api->getValidationRuleForUpdate();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    EmployeeLanguageAPI::PARAMETER_LANGUAGE_ID => 1,
                    EmployeeLanguageAPI::PARAMETER_FLUENCY_ID => 3,
                    EmployeeLanguageAPI::PARAMETER_COMPETENCY_ID => 1,
                    EmployeeLanguageAPI::PARAMETER_COMMENT => '',
                ],
                $rules
            )
        );
    }

    public function testDelete(): void
    {
        $this->loadFixtures();

        $empNumber = 1;
        $employeeLanguageDao = $this->getMockBuilder(EmployeeLanguageDao::class)
            ->onlyMethods(['deleteEmployeeLanguages'])
            ->getMock();

        $employeeLanguageDao->expects($this->once())
            ->method('deleteEmployeeLanguages')
            ->willReturn(1);

        $employeeLanguageService = $this->getMockBuilder(EmployeeLanguageService::class)
            ->onlyMethods(['getEmployeeLanguageDao'])
            ->getMock();

        $employeeLanguageService->expects($this->once())
            ->method('getEmployeeLanguageDao')
            ->willReturn($employeeLanguageDao);

        /** @var MockObject&EmployeeLanguageAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeLanguageAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    CommonParams::PARAMETER_IDS => [
                        ['languageId' => 1, 'fluencyId' => 1]
                    ],
                ]
            ]
        )->onlyMethods(['getEmployeeLanguageService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getEmployeeLanguageService')
            ->will($this->returnValue($employeeLanguageService));

        $result = $api->delete();
        $this->assertEquals(
            [['languageId' => 1, 'fluencyId' => 1]],
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
        $userRoleManager->expects($this->exactly(3))
            ->method('getAccessibleEntityIds')
            ->willReturn([1, 2]);

        $authUser = $this->getMockBuilder(User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->exactly(3))
            ->method('getEmpNumber')
            ->willReturn(2);
        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::AUTH_USER => $authUser
            ]
        );
        $api = new EmployeeLanguageAPI($this->getRequest());
        $rules = $api->getValidationRuleForDelete();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    CommonParams::PARAMETER_IDS => [['languageId' => 1, 'fluencyId' => 1]],
                ],
                $rules
            )
        );

        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    CommonParams::PARAMETER_IDS => [
                        ['languageId' => 1, 'fluencyId' => 1],
                        ['languageId' => 1, 'fluencyId' => 2],
                        ['languageId' => 1, 'fluencyId' => 3],
                        ['languageId' => 2, 'fluencyId' => 1],
                    ],
                ],
                $rules
            )
        );

        $this->expectInvalidParamException();
        $this->validate(
            [
                CommonParams::PARAMETER_EMP_NUMBER => 1,
                CommonParams::PARAMETER_IDS => [['languageId' => 1]],
            ],
            $rules
        );
    }
}
