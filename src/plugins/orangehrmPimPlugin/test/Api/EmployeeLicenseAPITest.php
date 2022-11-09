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
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeLicense;
use OrangeHRM\Entity\License;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Api\EmployeeLicenseAPI;
use OrangeHRM\Pim\Dao\EmployeeLicenseDao;
use OrangeHRM\Pim\Service\EmployeeLicenseService;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;

/**
 * @group Pim
 * @group APIv2
 */
class EmployeeLicenseAPITest extends EndpointTestCase
{
    public function testGetEmployeeLicenseService(): void
    {
        $api = new EmployeeLicenseAPI($this->getRequest());
        $this->assertTrue($api->getEmployeeLicenseService() instanceof EmployeeLicenseService);
    }

    public function testGetOne(): void
    {
        $empNumber = 1;
        $employeeLicenseDao = $this->getMockBuilder(EmployeeLicenseDao::class)
            ->onlyMethods(['getEmployeeLicense'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $license = new License();
        $license->setId(1);
        $license->setName('CIMA');

        $employeeLicense = new EmployeeLicense();
        $employeeLicense->setEmployee($employee);
        $employeeLicense->setLicense($license);
        $employeeLicense->setLicenseNo('02');
        $employeeLicense->setLicenseIssuedDate(new DateTime('2019-05-19'));
        $employeeLicense->setLicenseExpiryDate(new DateTime('2020-05-19'));

        $employeeLicenseDao->expects($this->exactly(1))
            ->method('getEmployeeLicense')
            ->with(1, 1)
            ->will($this->returnValue($employeeLicense));

        $employeeLicenseService = $this->getMockBuilder(EmployeeLicenseService::class)
            ->onlyMethods(['getEmployeeLicenseDao'])
            ->getMock();

        $employeeLicenseService->expects($this->exactly(1))
            ->method('getEmployeeLicenseDao')
            ->willReturn($employeeLicenseDao);

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $employeeLicenseService,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );

        /** @var MockObject&EmployeeLicenseAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeLicenseAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_ID => 1
                ]
            ]
        )->onlyMethods(['getEmployeeLicenseService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getEmployeeLicenseService')
            ->will($this->returnValue($employeeLicenseService));

        $result = $api->getOne();
        $this->assertEquals(
            [
                'licenseNo' => '02',
                'issuedDate' => '2019-05-19',
                'expiryDate' => '2020-05-19',
                "license" => [
                    "id" => 1,
                    "name" => "CIMA"
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
        $api = new EmployeeLicenseAPI($this->getRequest());
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
        $employeeLicenseDao = $this->getMockBuilder(EmployeeLicenseDao::class)
            ->onlyMethods(['saveEmployeeLicense', 'getEmployeeLicense'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $license = new License();
        $license->setId(1);
        $license->setName('CIMA');
        $employeeLicense = new EmployeeLicense();
        $employeeLicense->setEmployee($employee);
        $employeeLicense->setLicense($license);
        $employeeLicense->setLicenseNo('02');
        $employeeLicense->setLicenseIssuedDate(new DateTime('2019-05-19'));
        $employeeLicense->setLicenseExpiryDate(new DateTime('2020-05-19'));

        $employeeLicenseDao->expects($this->exactly(1))
            ->method('getEmployeeLicense')
            ->with(1, 1)
            ->willReturn($employeeLicense);

        $employeeLicenseDao->expects($this->exactly(1))
            ->method('saveEmployeeLicense')
            ->with($employeeLicense)
            ->will($this->returnValue($employeeLicense));

        $employeeLicenseService = $this->getMockBuilder(EmployeeLicenseService::class)
            ->onlyMethods(['getEmployeeLicenseDao'])
            ->getMock();

        $employeeLicenseService->expects($this->exactly(2))
            ->method('getEmployeeLicenseDao')
            ->willReturn($employeeLicenseDao);

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $employeeLicenseService,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );

        /** @var MockObject&EmployeeLicenseAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeLicenseAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_ID => 1
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    EmployeeLicenseAPI::PARAMETER_LICENSE_ID => 1,
                    EmployeeLicenseAPI::PARAMETER_LICENSE_NO => '05',
                    EmployeeLicenseAPI::PARAMETER_LICENSE_ISSUED_DATE => '2019-05-19',
                    EmployeeLicenseAPI::PARAMETER_LICENSE_EXPIRED_DATE => '2020-05-19',
                ]
            ]
        )->onlyMethods(['getEmployeeLicenseService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getEmployeeLicenseService')
            ->will($this->returnValue($employeeLicenseService));

        $result = $api->update();
        $this->assertEquals(
            [
                'licenseNo' => '05',
                'issuedDate' => '2019-05-19',
                'expiryDate' => '2020-05-19',
                "license" => [
                    "id" => 1,
                    "name" => "CIMA"
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
        $api = new EmployeeLicenseAPI($this->getRequest());
        $rules = $api->getValidationRuleForUpdate();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    CommonParams::PARAMETER_ID => 1,
                    EmployeeLicenseAPI::PARAMETER_LICENSE_NO => '02',
                    EmployeeLicenseAPI::PARAMETER_LICENSE_ISSUED_DATE => '2019-05-19',
                    EmployeeLicenseAPI::PARAMETER_LICENSE_EXPIRED_DATE => '2020-05-19',
                ],
                $rules
            )
        );
    }

    public function testDelete()
    {
        $empNumber = 1;
        $employeeLicenseDao = $this->getMockBuilder(EmployeeLicenseDao::class)
            ->onlyMethods(['deleteEmployeeLicenses'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $license = new License();
        $license->setId(1);
        $license->setName('CIMA');
        $employeeLicense = new EmployeeLicense();
        $employeeLicense->setEmployee($employee);
        $employeeLicense->setLicense($license);
        $employeeLicense->setLicenseNo('02');
        $employeeLicense->setLicenseIssuedDate(new DateTime('2019-05-19'));
        $employeeLicense->setLicenseExpiryDate(new DateTime('2020-05-19'));

        $employeeLicenseDao->expects($this->exactly(1))
            ->method('deleteEmployeeLicenses')
            ->with(1, [1])
            ->willReturn(1);

        $employeeLicenseService = $this->getMockBuilder(EmployeeLicenseService::class)
            ->onlyMethods(['getEmployeeLicenseDao'])
            ->getMock();

        $employeeLicenseService->expects($this->exactly(1))
            ->method('getEmployeeLicenseDao')
            ->willReturn($employeeLicenseDao);

        /** @var MockObject&EmployeeLicenseAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeLicenseAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    CommonParams::PARAMETER_IDS => [1],
                ]
            ]
        )->onlyMethods(['getEmployeeLicenseService'])
            ->getMock();
        $api->expects($this->exactly(1))
            ->method('getEmployeeLicenseService')
            ->will($this->returnValue($employeeLicenseService));

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
        $api = new EmployeeLicenseAPI($this->getRequest());
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
        $employeeLicenseDao = $this->getMockBuilder(EmployeeLicenseDao::class)
            ->onlyMethods(['saveEmployeeLicense', 'getEmployeeLicense'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $License = new License();
        $License->setId(1);
        $License->setName('CIMA');
        $employeeLicense = new EmployeeLicense();
        $employeeLicense->setEmployee($employee);
        $employeeLicense->setLicense($License);
        $employeeLicense->setLicenseNo('02');
        $employeeLicense->setLicenseIssuedDate(new DateTime('2019-05-19'));
        $employeeLicense->setLicenseExpiryDate(new DateTime('2020-05-19'));

        $employeeLicenseDao->expects($this->exactly(1))
            ->method('getEmployeeLicense')
            ->with(1, 1)
            ->willReturn($employeeLicense);

        $employeeLicenseDao->expects($this->exactly(1))
            ->method('saveEmployeeLicense')
            ->with($employeeLicense)
            ->will($this->returnValue($employeeLicense));

        $employeeLicenseService = $this->getMockBuilder(EmployeeLicenseService::class)
            ->onlyMethods(['getEmployeeLicenseDao'])
            ->getMock();

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $employeeLicenseService,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );

        $employeeLicenseService->expects($this->exactly(2))
            ->method('getEmployeeLicenseDao')
            ->willReturn($employeeLicenseDao);

        /** @var MockObject&EmployeeLicenseAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeLicenseAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    EmployeeLicenseAPI::PARAMETER_LICENSE_ID => 1,
                    EmployeeLicenseAPI::PARAMETER_LICENSE_NO => '05',
                    EmployeeLicenseAPI::PARAMETER_LICENSE_ISSUED_DATE => '2019-07-19',
                    EmployeeLicenseAPI::PARAMETER_LICENSE_EXPIRED_DATE => '2020-07-19',
                ]
            ]
        )->onlyMethods(['getEmployeeLicenseService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getEmployeeLicenseService')
            ->will($this->returnValue($employeeLicenseService));

        $result = $api->update();
        $this->assertEquals(
            [
                'licenseNo' => '05',
                'issuedDate' => '2019-07-19',
                'expiryDate' => '2020-07-19',
                "license" => [
                    "id" => 1,
                    "name" => "CIMA"
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
        $api = new EmployeeLicenseAPI($this->getRequest());
        $rules = $api->getValidationRuleForCreate();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    EmployeeLicenseAPI::PARAMETER_LICENSE_ID => 1,
                    EmployeeLicenseAPI::PARAMETER_LICENSE_NO => '05',
                    EmployeeLicenseAPI::PARAMETER_LICENSE_ISSUED_DATE => '2019-07-19',
                    EmployeeLicenseAPI::PARAMETER_LICENSE_EXPIRED_DATE => '2020-07-19',
                ],
                $rules
            )
        );
    }

    public function testGetAll()
    {
        $empNumber = 1;
        $employeeLicenseDao = $this->getMockBuilder(EmployeeLicenseDao::class)
            ->onlyMethods(['searchEmployeeLicense', 'getSearchEmployeeLicensesCount'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $license1 = new License();
        $license1->setId(1);
        $license1->setName('CIMA');
        $license2 = new License();
        $license2->setId(2);
        $license2->setName('CCNA');
        $employeeLicense1 = new EmployeeLicense();
        $employeeLicense1->setEmployee($employee);
        $employeeLicense1->setLicense($license1);
        $employeeLicense1->setLicenseNo('02');
        $employeeLicense1->setLicenseIssuedDate(new DateTime('2019-05-19'));
        $employeeLicense1->setLicenseExpiryDate(new DateTime('2020-05-19'));

        $employeeLicense2 = new EmployeeLicense();
        $employeeLicense2->setEmployee($employee);
        $employeeLicense2->setLicense($license2);
        $employeeLicense2->setLicenseNo('02');
        $employeeLicense2->setLicenseIssuedDate(new DateTime('2019-05-19'));
        $employeeLicense2->setLicenseExpiryDate(new DateTime('2020-05-19'));

        $employeeLicenseDao->expects($this->exactly(1))
            ->method('searchEmployeeLicense')
            ->willReturn([$employeeLicense1, $employeeLicense2]);

        $employeeLicenseDao->expects($this->exactly(1))
            ->method('getSearchEmployeeLicensesCount')
            ->willReturn(2);

        $employeeLicenseService = $this->getMockBuilder(EmployeeLicenseService::class)
            ->onlyMethods(['getEmployeeLicenseDao'])
            ->getMock();

        $employeeLicenseService->expects($this->exactly(2))
            ->method('getEmployeeLicenseDao')
            ->willReturn($employeeLicenseDao);

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $employeeLicenseService,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );

        /** @var MockObject&EmployeeLicenseAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeLicenseAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    EmployeeLicenseAPI::PARAMETER_LICENSE_NO => '02',
                    EmployeeLicenseAPI::PARAMETER_LICENSE_ISSUED_DATE => '2019-05-19',
                    EmployeeLicenseAPI::PARAMETER_LICENSE_EXPIRED_DATE => '2020-05-19',
                ]
            ]
        )->onlyMethods(['getEmployeeLicenseService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getEmployeeLicenseService')
            ->will($this->returnValue($employeeLicenseService));

        $result = $api->getAll();
        $this->assertEquals(
            [
                [
                    'licenseNo' => '02',
                    'issuedDate' => '2019-05-19',
                    'expiryDate' => '2020-05-19',
                    "license" => [
                        "id" => 1,
                        "name" => "CIMA"
                    ]
                ],
                [
                    'licenseNo' => '02',
                    'issuedDate' => '2019-05-19',
                    'expiryDate' => '2020-05-19',
                    "license" => [
                        "id" => 2,
                        "name" => "CCNA"
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
        $api = new EmployeeLicenseAPI($this->getRequest());
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
