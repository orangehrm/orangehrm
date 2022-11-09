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

use DateTime;
use OrangeHRM\Authentication\Auth\User;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\Education;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeEducation;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Api\EmployeeEducationAPI;
use OrangeHRM\Pim\Dao\EmployeeEducationDao;
use OrangeHRM\Pim\Service\EmployeeEducationService;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group APIv2
 */
class EmployeeEducationAPITest extends EndpointTestCase
{
    protected function loadFixtures(): void
    {
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmPimPlugin/test/fixtures/EmployeeEducationDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmployeeEducationService(): void
    {
        $api = new EmployeeEducationAPI($this->getRequest());
        $this->assertTrue($api->getEmployeeEducationService() instanceof EmployeeEducationService);
    }

    public function testGetOne(): void
    {
        $empNumber = 1;
        $employeeEducationDao = $this->getMockBuilder(EmployeeEducationDao::class)
            ->onlyMethods(['getEmployeeEducationById'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $education = new Education();
        $education->setId(1);
        $education->setName('BSc');
        $employeeEducation = new EmployeeEducation();
        $employeeEducation->setId(1);
        $employeeEducation->setInstitute('UoP');
        $employeeEducation->setMajor('CE');
        $employeeEducation->setYear(2020);
        $employeeEducation->setScore('First Class');
        $employeeEducation->setStartDate(new DateTime('2017-01-01'));
        $employeeEducation->setEndDate(new DateTime('2020-12-31'));
        $employeeEducation->setEducation($education);
        $employeeEducation->setEmployee($employee);

        $employeeEducationDao->expects($this->exactly(1))
            ->method('getEmployeeEducationById')
            ->with(1, 1)
            ->will($this->returnValue($employeeEducation));

        $employeeEducationService = $this->getMockBuilder(EmployeeEducationService::class)
            ->onlyMethods(['getEmployeeEducationDao'])
            ->getMock();

        $employeeEducationService->expects($this->exactly(1))
            ->method('getEmployeeEducationDao')
            ->willReturn($employeeEducationDao);

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $employeeEducationService,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );

        /** @var MockObject&EmployeeEducationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeEducationAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_ID => 1
                ]
            ]
        )->onlyMethods(['getEmployeeEducationService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getEmployeeEducationService')
            ->will($this->returnValue($employeeEducationService));

        $result = $api->getOne();
        $this->assertEquals(
            [
                "id" => 1,
                "institute" => "UoP",
                "major" => "CE",
                "year" => 2020,
                "score" => "First Class",
                "startDate" => '2017-01-01',
                "endDate" => '2020-12-31',
                "education" => [
                    "id" => 1,
                    "name" => "BSc"
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

        $authUser = $this->getMockBuilder(User::class)
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
        $api = new EmployeeEducationAPI($this->getRequest());
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
        $employeeEducationDao = $this->getMockBuilder(EmployeeEducationDao::class)
            ->onlyMethods(['saveEmployeeEducation', 'getEmployeeEducationById'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber(1);
        $education = new Education();
        $education->setId(1);
        $education->setName('BSc');
        $employeeEducation = new EmployeeEducation();
        $employeeEducation->setId(1);
        $employeeEducation->setInstitute('UoP');
        $employeeEducation->setMajor('CE');
        $employeeEducation->setYear(2020);
        $employeeEducation->setScore('First Class');
        $employeeEducation->setStartDate(new DateTime('2017-01-01'));
        $employeeEducation->setEndDate(new DateTime('2020-12-31'));
        $employeeEducation->setEducation($education);
        $employeeEducation->setEmployee($employee);

        $employeeEducationDao->expects($this->exactly(1))
            ->method('getEmployeeEducationById')
            ->with(1, 1)
            ->willReturn($employeeEducation);

        $employeeEducationDao->expects($this->exactly(1))
            ->method('saveEmployeeEducation')
            ->will(
                $this->returnCallback(
                    function (EmployeeEducation $employeeEducation) {
                        return $employeeEducation;
                    }
                )
            );

        $employeeEducationService = $this->getMockBuilder(EmployeeEducationService::class)
            ->onlyMethods(['getEmployeeEducationDao'])
            ->getMock();

        $employeeEducationService->expects($this->exactly(2))
            ->method('getEmployeeEducationDao')
            ->willReturn($employeeEducationDao);

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $employeeEducationService,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );

        /** @var MockObject&EmployeeEducationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeEducationAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_ID => 1
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    EmployeeEducationAPI::PARAMETER_EDUCATION_ID => 1,
                    EmployeeEducationAPI::PARAMETER_INSTITUTE => 'UoP',
                    EmployeeEducationAPI::PARAMETER_MAJOR => 'CE',
                    EmployeeEducationAPI::PARAMETER_SCORE => 'First Class',
                    EmployeeEducationAPI::PARAMETER_YEAR => 2020,
                    EmployeeEducationAPI::PARAMETER_START_DATE => '2017-01-01',
                    EmployeeEducationAPI::PARAMETER_END_DATE => '2020-12-31',
                ]
            ]
        )->onlyMethods(['getEmployeeEducationService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getEmployeeEducationService')
            ->will($this->returnValue($employeeEducationService));

        $result = $api->update();
        $this->assertEquals(
            [
                "id" => 1,
                "institute" => "UoP",
                "major" => "CE",
                "year" => 2020,
                "score" => "First Class",
                "startDate" => '2017-01-01',
                "endDate" => '2020-12-31',
                "education" => [
                    "id" => 1,
                    "name" => "BSc"
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

        $authUser = $this->getMockBuilder(User::class)
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
        $api = new EmployeeEducationAPI($this->getRequest());
        $rules = $api->getValidationRuleForUpdate();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    CommonParams::PARAMETER_ID => 1,
                    EmployeeEducationAPI::PARAMETER_INSTITUTE => 'UoM',
                    EmployeeEducationAPI::PARAMETER_MAJOR => 'CSE',
                    EmployeeEducationAPI::PARAMETER_SCORE => 'First Class',
                    EmployeeEducationAPI::PARAMETER_YEAR => 2020,
                    EmployeeEducationAPI::PARAMETER_START_DATE => '2017-01-01',
                    EmployeeEducationAPI::PARAMETER_END_DATE => '2020-12-31',
                ],
                $rules
            )
        );
    }

    public function testDelete()
    {
        $empNumber = 1;
        $employeeEducationDao = $this->getMockBuilder(EmployeeEducationDao::class)
            ->onlyMethods(['deleteEmployeeEducations'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $education = new Education();
        $education->setId(1);
        $education->setName('BSc');
        $employeeEducation = new EmployeeEducation();
        $employeeEducation->setId(1);
        $employeeEducation->setInstitute('UoP');
        $employeeEducation->setMajor('CE');
        $employeeEducation->setYear(2020);
        $employeeEducation->setScore('First Class');
        $employeeEducation->setStartDate(new DateTime('2017-01-01'));
        $employeeEducation->setEndDate(new DateTime('2020-12-31'));
        $employeeEducation->setEducation($education);
        $employeeEducation->setEmployee($employee);

        $employeeEducationDao->expects($this->exactly(1))
            ->method('deleteEmployeeEducations')
            ->with(1, [1])
            ->willReturn(1);

        $employeeEducationService = $this->getMockBuilder(EmployeeEducationService::class)
            ->onlyMethods(['getEmployeeEducationDao'])
            ->getMock();

        $employeeEducationService->expects($this->exactly(1))
            ->method('getEmployeeEducationDao')
            ->willReturn($employeeEducationDao);

        /** @var MockObject&EmployeeEducationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeEducationAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    CommonParams::PARAMETER_IDS => [1],
                ]
            ]
        )->onlyMethods(['getEmployeeEducationService'])
            ->getMock();
        $api->expects($this->exactly(1))
            ->method('getEmployeeEducationService')
            ->will($this->returnValue($employeeEducationService));

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

        $authUser = $this->getMockBuilder(User::class)
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
        $api = new EmployeeEducationAPI($this->getRequest());
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
        $this->loadFixtures();

        $empNumber = 1;
        $employeeEducationDao = $this->getMockBuilder(EmployeeEducationDao::class)
            ->onlyMethods(['saveEmployeeEducation', 'getEmployeeEducationById'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $education = new Education();
        $education->setId(1);
        $education->setName('BSc');

        $employeeEducation = new EmployeeEducation();
        $employeeEducation->setId(1);
        $employeeEducation->setInstitute('UoP');
        $employeeEducation->setMajor('CE');
        $employeeEducation->setYear(2020);
        $employeeEducation->setScore('First Class');
        $employeeEducation->setStartDate(new DateTime('2017-01-01'));
        $employeeEducation->setEndDate(new DateTime('2020-12-31'));
        $employeeEducation->setEducation($education);
        $employeeEducation->setEmployee($employee);

        $employeeEducationDao->expects($this->never())
            ->method('getEmployeeEducationById')
            ->with(1, 1)
            ->willReturn($employeeEducation);

        $employeeEducationDao->expects($this->once())
            ->method('saveEmployeeEducation')
            ->will(
                $this->returnCallback(
                    function (EmployeeEducation $employeeEducation) {
                        $employeeEducation->setId(1);
                        return $employeeEducation;
                    }
                )
            );

        $employeeEducationService = $this->getMockBuilder(EmployeeEducationService::class)
            ->onlyMethods(['getEmployeeEducationDao'])
            ->getMock();

        $employeeEducationService->expects($this->once())
            ->method('getEmployeeEducationDao')
            ->willReturn($employeeEducationDao);

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $employeeEducationService,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );

        /** @var MockObject&EmployeeEducationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeEducationAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    EmployeeEducationAPI::PARAMETER_EDUCATION_ID => 1,
                    EmployeeEducationAPI::PARAMETER_INSTITUTE => 'UoP',
                    EmployeeEducationAPI::PARAMETER_MAJOR => 'CE',
                    EmployeeEducationAPI::PARAMETER_SCORE => 'First Class',
                    EmployeeEducationAPI::PARAMETER_YEAR => 2020,
                    EmployeeEducationAPI::PARAMETER_START_DATE => '2017-01-01',
                    EmployeeEducationAPI::PARAMETER_END_DATE => '2020-12-31',
                ]
            ]
        )->onlyMethods(['getEmployeeEducationService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getEmployeeEducationService')
            ->will($this->returnValue($employeeEducationService));

        $result = $api->create();
        $this->assertEquals(
            [
                "id" => 1,
                "institute" => "UoP",
                "major" => "CE",
                "year" => 2020,
                "score" => "First Class",
                "startDate" => '2017-01-01',
                "endDate" => '2020-12-31',
                "education" => [
                    "id" => 1,
                    "name" => "BSc"
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

        $authUser = $this->getMockBuilder(User::class)
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
        $api = new EmployeeEducationAPI($this->getRequest());
        $rules = $api->getValidationRuleForCreate();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    EmployeeEducationAPI::PARAMETER_EDUCATION_ID => 1,
                    EmployeeEducationAPI::PARAMETER_INSTITUTE => 'UoM',
                    EmployeeEducationAPI::PARAMETER_MAJOR => 'CSE',
                    EmployeeEducationAPI::PARAMETER_SCORE => 'First Class',
                    EmployeeEducationAPI::PARAMETER_YEAR => 2020,
                    EmployeeEducationAPI::PARAMETER_START_DATE => '2017-01-01',
                    EmployeeEducationAPI::PARAMETER_END_DATE => '2020-12-31',
                ],
                $rules
            )
        );
    }


    public function testGetAll()
    {
        $empNumber = 1;
        $employeeEducationDao = $this->getMockBuilder(EmployeeEducationDao::class)
            ->onlyMethods(['searchEmployeeEducation', 'getSearchEmployeeEducationsCount'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $education1 = new Education();
        $education1->setId(1);
        $education1->setName('BSc');

        $education2 = new Education();
        $education2->setId(2);
        $education2->setName('MSc');

        $employeeEducation1 = new EmployeeEducation();
        $employeeEducation1->setId(1);
        $employeeEducation1->setInstitute('UoP');
        $employeeEducation1->setMajor('CE');
        $employeeEducation1->setYear(2020);
        $employeeEducation1->setScore('First Class');
        $employeeEducation1->setStartDate(new DateTime('2017-01-01'));
        $employeeEducation1->setEndDate(new DateTime('2020-12-31'));
        $employeeEducation1->setEducation($education1);
        $employeeEducation1->setEmployee($employee);

        $employeeEducation2 = new EmployeeEducation();
        $employeeEducation2->setId(2);
        $employeeEducation2->setInstitute('UoP');
        $employeeEducation2->setMajor('CE');
        $employeeEducation2->setYear(2020);
        $employeeEducation2->setScore('First Class');
        $employeeEducation2->setStartDate(new DateTime('2017-01-01'));
        $employeeEducation2->setEndDate(new DateTime('2020-12-31'));
        $employeeEducation2->setEducation($education2);
        $employeeEducation2->setEmployee($employee);

        $employeeEducationDao->expects($this->exactly(1))
            ->method('searchEmployeeEducation')
            ->willReturn([$employeeEducation1, $employeeEducation2]);

        $employeeEducationDao->expects($this->exactly(1))
            ->method('getSearchEmployeeEducationsCount')
            ->willReturn(2);

        $employeeEducationService = $this->getMockBuilder(EmployeeEducationService::class)
            ->onlyMethods(['getEmployeeEducationDao'])
            ->getMock();

        $employeeEducationService->expects($this->exactly(2))
            ->method('getEmployeeEducationDao')
            ->willReturn($employeeEducationDao);

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $employeeEducationService,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );

        /** @var MockObject&EmployeeEducationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeEducationAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ]
            ]
        )->onlyMethods(['getEmployeeEducationService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getEmployeeEducationService')
            ->will($this->returnValue($employeeEducationService));

        $result = $api->getAll();
        $this->assertEquals(
            [
                [
                    "id" => 1,
                    "institute" => "UoP",
                    "major" => "CE",
                    "year" => 2020,
                    "score" => "First Class",
                    "startDate" => '2017-01-01',
                    "endDate" => '2020-12-31',
                    "education" => [
                        "id" => 1,
                        "name" => "BSc"
                    ]
                ],
                [
                    "id" => 2,
                    "institute" => "UoP",
                    "major" => "CE",
                    "year" => 2020,
                    "score" => "First Class",
                    "startDate" => '2017-01-01',
                    "endDate" => '2020-12-31',
                    "education" => [
                        "id" => 2,
                        "name" => "MSc"
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

        $authUser = $this->getMockBuilder(User::class)
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
        $api = new EmployeeEducationAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetAll();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1
                ],
                $rules
            )
        );
    }
}
