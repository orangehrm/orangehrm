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
use OrangeHRM\Admin\Service\PayGradeService;
use OrangeHRM\Authentication\Auth\User;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\CurrencyType;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeMembership;
use OrangeHRM\Entity\Membership;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Api\EmployeeMembershipAPI;
use OrangeHRM\Pim\Dao\EmployeeMembershipDao;
use OrangeHRM\Pim\Service\EmployeeMembershipService;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group APIv2
 */
class EmployeeMembershipAPITest extends EndpointTestCase
{
    protected function loadFixtures(): void
    {
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmPimPlugin/test/fixtures/EmployeeMembershipDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmployeeMembershipService(): void
    {
        $api = new EmployeeMembershipAPI($this->getRequest());
        $this->assertTrue($api->getEmployeeMembershipService() instanceof EmployeeMembershipService);
    }

    public function testGetOne(): void
    {
        $empNumber = 1;
        $employeeMembershipDao = $this->getMockBuilder(EmployeeMembershipDao::class)
            ->onlyMethods(['getEmployeeMembershipById'])
            ->getMock();
        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $membership = new Membership();
        $membership->setId(1);
        $membership->setName('membership1');
        $employeeMembership = new EmployeeMembership();
        $employeeMembership->setId(1);
        $employeeMembership->setSubscriptionFee('4');
        $employeeMembership->setSubscriptionPaidBy('Individual');
        $employeeMembership->setSubscriptionCurrency('LKR');
        $employeeMembership->setSubscriptionCommenceDate(new DateTime('2011-05-20'));
        $employeeMembership->setSubscriptionRenewalDate(new DateTime('2011-05-22'));
        $employeeMembership->setMembership($membership);
        $employeeMembership->setEmployee($employee);

        $employeeMembershipDao->expects($this->exactly(1))
            ->method('getEmployeeMembershipById')
            ->with(1, 1)
            ->will($this->returnValue($employeeMembership));

        $employeeMembershipService = $this->getMockBuilder(EmployeeMembershipService::class)
            ->onlyMethods(['getEmployeeMembershipDao'])
            ->getMock();

        $employeeMembershipService->expects($this->exactly(1))
            ->method('getEmployeeMembershipDao')
            ->willReturn($employeeMembershipDao);

        $currency = new CurrencyType();
        $currency->setId('LKR');
        $currency->setName('Sri Lanka Rupee');
        $currencyService = $this->getMockBuilder(PayGradeService::class)
            ->onlyMethods(['getCurrencyById'])
            ->getMock();
        $currencyService->expects($this->once())
            ->method('getCurrencyById')
            ->with('LKR')
            ->willReturn($currency);

        $this->createKernelWithMockServices(
            [
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
                Services::PAY_GRADE_SERVICE => $currencyService,
            ]
        );

        /** @var MockObject&EmployeeMembershipAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeMembershipAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_ID => 1
                ]
            ]
        )->onlyMethods(['getEmployeeMembershipService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getEmployeeMembershipService')
            ->will($this->returnValue($employeeMembershipService));

        $result = $api->getOne();
        $this->assertEquals(
            [
                "id" => 1,
                "membership" => [
                    "id" => 1,
                    "name" => "membership1",
                ],
                "subscriptionFee" => "4",
                "subscriptionPaidBy" => "Individual",
                "currencyType" => [
                    "id" => "LKR",
                    "name" => "Sri Lanka Rupee",
                ],
                "subscriptionCommenceDate" => '2011-05-20',
                "subscriptionRenewalDate" => "2011-05-22",
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
                Services::AUTH_USER => $authUser,
            ]
        );
        $api = new EmployeeMembershipAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetOne();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    CommonParams::PARAMETER_ID => 1
                ],
                $rules
            )
        );
    }

    public function testDelete()
    {
        $empNumber = 1;
        $employeeMembershipDao = $this->getMockBuilder(EmployeeMembershipDao::class)
            ->onlyMethods(['deleteEmployeeMemberships'])
            ->getMock();
        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $membership = new Membership();
        $membership->setId(1);
        $membership->setName('membership1');
        $employeeMembership = new EmployeeMembership();
        $employeeMembership->setId(1);
        $employeeMembership->setSubscriptionFee('4');
        $employeeMembership->setSubscriptionPaidBy('Individual');
        $employeeMembership->setSubscriptionCurrency('LKR');
        $employeeMembership->setSubscriptionCommenceDate(new DateTime('2011-05-20'));
        $employeeMembership->setSubscriptionRenewalDate(new DateTime('2011-05-22'));
        $employeeMembership->setMembership($membership);
        $employeeMembership->setEmployee($employee);

        $employeeMembershipDao->expects($this->exactly(1))
            ->method('deleteEmployeeMemberships')
            ->with(1, [1])
            ->willReturn(1);
        $employeeMembershipService = $this->getMockBuilder(EmployeeMembershipService::class)
            ->onlyMethods(['getEmployeeMembershipDao'])
            ->getMock();
        $employeeMembershipService->expects($this->exactly(1))
            ->method('getEmployeeMembershipDao')
            ->willReturn($employeeMembershipDao);

        /** @var MockObject&EmployeeMembershipAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeMembershipAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    CommonParams::PARAMETER_IDS => [1],
                ]
            ]
        )->onlyMethods(['getEmployeeMembershipService'])
            ->getMock();
        $api->expects($this->exactly(1))
            ->method('getEmployeeMembershipService')
            ->will($this->returnValue($employeeMembershipService));

        $result = $api->delete();
        $this->assertEquals(
            [1],
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
        $api = new EmployeeMembershipAPI($this->getRequest());
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

    public function testUpdate()
    {
        $this->loadFixtures();

        $empNumber = 1;
        $employeeMembershipDao = $this->getMockBuilder(EmployeeMembershipDao::class)
            ->onlyMethods(['saveEmployeeMembership', 'getEmployeeMembershipById'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $membership = new Membership();
        $membership->setId(1);
        $membership->setName('membership1');
        $employeeMembership = new EmployeeMembership();
        $employeeMembership->setId(1);
        $employeeMembership->setSubscriptionFee('4');
        $employeeMembership->setSubscriptionPaidBy('Individual');
        $employeeMembership->setSubscriptionCurrency('LKR');
        $employeeMembership->setSubscriptionCommenceDate(new DateTime('2011-05-20'));
        $employeeMembership->setSubscriptionRenewalDate(new DateTime('2011-05-22'));
        $employeeMembership->setMembership($membership);
        $employeeMembership->setEmployee($employee);

        $employeeMembershipDao->expects($this->exactly(1))
            ->method('getEmployeeMembershipById')
            ->with(1, 1)
            ->willReturn($employeeMembership);

        $employeeMembershipDao->expects($this->exactly(1))
            ->method('saveEmployeeMembership')
            ->will(
                $this->returnCallback(
                    function (EmployeeMembership $employeeMembership) {
                        $employeeMembership->setId(1);
                        return $employeeMembership;
                    }
                )
            );

        $employeeMembershipRecordService = $this->getMockBuilder(EmployeeMembershipService::class)
            ->onlyMethods(['getEmployeeMembershipDao'])
            ->getMock();

        $employeeMembershipRecordService->expects($this->exactly(2))
            ->method('getEmployeeMembershipDao')
            ->willReturn($employeeMembershipDao);

        $currency = new CurrencyType();
        $currency->setId('LKR');
        $currency->setName('Sri Lanka Rupee');
        $currencyService = $this->getMockBuilder(PayGradeService::class)
            ->onlyMethods(['getCurrencyById'])
            ->getMock();
        $currencyService->expects($this->once())
            ->method('getCurrencyById')
            ->with('LKR')
            ->willReturn($currency);

        $this->createKernelWithMockServices(
            [
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
                Services::PAY_GRADE_SERVICE => $currencyService,
            ]
        );

        /** @var MockObject&EmployeeMembershipAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeMembershipAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_ID => 1
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    EmployeeMembershipAPI::PARAMETER_MEMBERSHIP_ID => 1,
                    EmployeeMembershipAPI::PARAMETER_SUBSCRIPTION_PAID_BY => "Individual",
                    EmployeeMembershipAPI::PARAMETER_SUBSCRIPTION_FEE => "4",
                    EmployeeMembershipAPI::PARAMETER_SUBSCRIPTION_CURRENCY => "LKR",
                    EmployeeMembershipAPI::PARAMETER_SUBSCRIPTION_COMMENCE_DATE => '2011-05-20',
                    EmployeeMembershipAPI::PARAMETER_SUBSCRIPTION_RENEWAL_DATE => "2011-05-22",
                ]
            ]
        )->onlyMethods(['getEmployeeMembershipService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getEmployeeMembershipService')
            ->will($this->returnValue($employeeMembershipRecordService));

        $result = $api->update();
        $this->assertEquals(
            [
                "id" => 1,
                "membership" => [
                    "id" => 1,
                    "name" => "membership1",
                ],
                "subscriptionFee" => "4",
                "subscriptionPaidBy" => "Individual",
                "currencyType" => [
                    "id" => "LKR",
                    "name" => "Sri Lanka Rupee",
                ],
                "subscriptionCommenceDate" => '2011-05-20',
                "subscriptionRenewalDate" => "2011-05-22",
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

        $currencyService = $this->getMockBuilder(PayGradeService::class)
            ->onlyMethods(['getCurrencyById'])
            ->getMock();
        $currencyService->expects($this->never())
            ->method('getCurrencyById');

        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::AUTH_USER => $authUser,
                Services::PAY_GRADE_SERVICE => $currencyService,
            ]
        );
        $api = new EmployeeMembershipAPI($this->getRequest());
        $rules = $api->getValidationRuleForUpdate();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    CommonParams::PARAMETER_ID => 1,
                    EmployeeMembershipAPI::PARAMETER_MEMBERSHIP_ID => 1,
                    EmployeeMembershipAPI::PARAMETER_SUBSCRIPTION_PAID_BY => "Individual",
                    EmployeeMembershipAPI::PARAMETER_SUBSCRIPTION_FEE => "4",
                    EmployeeMembershipAPI::PARAMETER_SUBSCRIPTION_COMMENCE_DATE => '2011-05-20',
                    EmployeeMembershipAPI::PARAMETER_SUBSCRIPTION_RENEWAL_DATE => "2011-05-22",
                ],
                $rules
            )
        );
    }

    public function testCreate()
    {
        $this->loadFixtures();
        $empNumber = 1;
        $employeeMembershipDao = $this->getMockBuilder(EmployeeMembershipDao::class)
            ->onlyMethods(['saveEmployeeMembership', 'getEmployeeMembershipById'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $membership = new Membership();
        $membership->setId(1);
        $membership->setName('membership1');
        $employeeMembership = new EmployeeMembership();
        $employeeMembership->setId(1);
        $employeeMembership->setSubscriptionFee('4');
        $employeeMembership->setSubscriptionPaidBy('Individual');
        $employeeMembership->setSubscriptionCurrency('LKR');
        $employeeMembership->setSubscriptionCommenceDate(new DateTime('2011-05-20'));
        $employeeMembership->setSubscriptionRenewalDate(new DateTime('2011-05-22'));
        $employeeMembership->setMembership($membership);
        $employeeMembership->setEmployee($employee);

        $employeeMembershipDao->expects($this->never())
            ->method('getEmployeeMembershipById')
            ->with(1, 1)
            ->willReturn($employeeMembership);

        $employeeMembershipDao->expects($this->exactly(1))
            ->method('saveEmployeeMembership')
            ->will(
                $this->returnCallback(
                    function (EmployeeMembership $employeeMembership) {
                        $employeeMembership->setId(1);
                        return $employeeMembership;
                    }
                )
            );

        $employeeMembershipService = $this->getMockBuilder(EmployeeMembershipService::class)
            ->onlyMethods(['getEmployeeMembershipDao'])
            ->getMock();

        $employeeMembershipService->expects($this->exactly(1))
            ->method('getEmployeeMembershipDao')
            ->willReturn($employeeMembershipDao);

        $currency = new CurrencyType();
        $currency->setId('LKR');
        $currency->setName('Sri Lanka Rupee');
        $currencyService = $this->getMockBuilder(PayGradeService::class)
            ->onlyMethods(['getCurrencyById'])
            ->getMock();
        $currencyService->expects($this->once())
            ->method('getCurrencyById')
            ->with('LKR')
            ->willReturn($currency);

        $this->createKernelWithMockServices(
            [
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
                Services::PAY_GRADE_SERVICE => $currencyService,
            ]
        );

        /** @var MockObject&EmployeeMembershipAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeMembershipAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    EmployeeMembershipAPI::PARAMETER_MEMBERSHIP_ID => 1,
                    EmployeeMembershipAPI::PARAMETER_SUBSCRIPTION_PAID_BY => "Individual",
                    EmployeeMembershipAPI::PARAMETER_SUBSCRIPTION_FEE => "4",
                    EmployeeMembershipAPI::PARAMETER_SUBSCRIPTION_CURRENCY => "LKR",
                    EmployeeMembershipAPI::PARAMETER_SUBSCRIPTION_COMMENCE_DATE => '2011-05-20',
                    EmployeeMembershipAPI::PARAMETER_SUBSCRIPTION_RENEWAL_DATE => "2011-05-22",
                ]
            ]
        )->onlyMethods(['getEmployeeMembershipService'])
            ->getMock();
        $api->expects($this->exactly(1))
            ->method('getEmployeeMembershipService')
            ->will($this->returnValue($employeeMembershipService));

        $result = $api->create();
        $this->assertEquals(
            [
                "id" => 1,
                "membership" => [
                    "id" => 1,
                    "name" => "membership1",
                ],
                "subscriptionFee" => "4",
                "subscriptionPaidBy" => "Individual",
                "currencyType" => [
                    "id" => "LKR",
                    "name" => "Sri Lanka Rupee",
                ],
                "subscriptionCommenceDate" => '2011-05-20',
                "subscriptionRenewalDate" => "2011-05-22",
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

        $currencyService = $this->getMockBuilder(PayGradeService::class)
            ->onlyMethods(['getCurrencyById'])
            ->getMock();
        $currencyService->expects($this->never())
            ->method('getCurrencyById');

        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::AUTH_USER => $authUser,
                Services::PAY_GRADE_SERVICE => $currencyService,
            ]
        );
        $api = new EmployeeMembershipAPI($this->getRequest());
        $rules = $api->getValidationRuleForCreate();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    EmployeeMembershipAPI::PARAMETER_MEMBERSHIP_ID => 1,
                    EmployeeMembershipAPI::PARAMETER_SUBSCRIPTION_PAID_BY => "Individual",
                    EmployeeMembershipAPI::PARAMETER_SUBSCRIPTION_FEE => "4",
                    EmployeeMembershipAPI::PARAMETER_SUBSCRIPTION_COMMENCE_DATE => '2011-05-20',
                    EmployeeMembershipAPI::PARAMETER_SUBSCRIPTION_RENEWAL_DATE => "2011-05-22",
                ],
                $rules
            )
        );
    }

    public function testGetAll()
    {
        $empNumber = 1;
        $employeeMembershipDao = $this->getMockBuilder(EmployeeMembershipDao::class)
            ->onlyMethods(['searchEmployeeMembership', 'getSearchEmployeeMembershipsCount'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $membership = new Membership();
        $membership->setId(1);
        $membership->setName('membership1');

        $employeeMembership1 = new EmployeeMembership();
        $employeeMembership1->setId(1);
        $employeeMembership1->setSubscriptionPaidBy('Individual');
        $employeeMembership1->setSubscriptionFee('4');
        $employeeMembership1->setSubscriptionCurrency('LKR');
        $employeeMembership1->setSubscriptionCommenceDate(new DateTime('2011-05-20'));
        $employeeMembership1->setSubscriptionRenewalDate(new DateTime('2011-05-22'));
        $employeeMembership1->setMembership($membership);
        $employeeMembership1->setEmployee($employee);

        $employeeMembership2 = new EmployeeMembership();
        $employeeMembership2->setId(2);
        $employeeMembership2->setSubscriptionPaidBy('Company');
        $employeeMembership2->setSubscriptionFee('4');
        $employeeMembership2->setSubscriptionCurrency('LKR');
        $employeeMembership2->setSubscriptionCommenceDate(new DateTime('2011-05-20'));
        $employeeMembership2->setSubscriptionRenewalDate(new DateTime('2011-05-22'));
        $employeeMembership2->setMembership($membership);
        $employeeMembership2->setEmployee($employee);

        $employeeMembershipDao->expects($this->exactly(1))
            ->method('searchEmployeeMembership')
            ->willReturn([$employeeMembership1, $employeeMembership2]);

        $employeeMembershipDao->expects($this->exactly(1))
            ->method('getSearchEmployeeMembershipsCount')
            ->willReturn(2);

        $employeeMembershipService = $this->getMockBuilder(EmployeeMembershipService::class)
            ->onlyMethods(['getEmployeeMembershipDao'])
            ->getMock();

        $employeeMembershipService->expects($this->exactly(2))
            ->method('getEmployeeMembershipDao')
            ->willReturn($employeeMembershipDao);

        $currency = new CurrencyType();
        $currency->setId('LKR');
        $currency->setName('Sri Lanka Rupee');
        $currencyService = $this->getMockBuilder(PayGradeService::class)
            ->onlyMethods(['getCurrencyById'])
            ->getMock();
        $currencyService->expects($this->exactly(2))
            ->method('getCurrencyById')
            ->with('LKR')
            ->willReturn($currency);

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $employeeMembershipService,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
                Services::PAY_GRADE_SERVICE => $currencyService,
            ]
        );

        /** @var MockObject&EmployeeMembershipAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeMembershipAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    EmployeeMembershipAPI::PARAMETER_SUBSCRIPTION_FEE => "4",
                    EmployeeMembershipAPI::PARAMETER_SUBSCRIPTION_CURRENCY => "LKR",
                    EmployeeMembershipAPI::PARAMETER_SUBSCRIPTION_COMMENCE_DATE => '2011-05-20',
                    EmployeeMembershipAPI::PARAMETER_SUBSCRIPTION_RENEWAL_DATE => "2011-05-22",
                ]
            ]
        )->onlyMethods(['getEmployeeMembershipService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getEmployeeMembershipService')
            ->will($this->returnValue($employeeMembershipService));

        $result = $api->getAll();
        $this->assertEquals(
            [
                [
                    "id" => 1,
                    "membership" => [
                        "id" => 1,
                        "name" => "membership1",
                    ],
                    "subscriptionFee" => "4",
                    "subscriptionPaidBy" => "Individual",
                    "currencyType" => [
                        "id" => "LKR",
                        "name" => "Sri Lanka Rupee",
                    ],
                    "subscriptionCommenceDate" => '2011-05-20',
                    "subscriptionRenewalDate" => "2011-05-22",
                ],
                [
                    "id" => 2,
                    "membership" => [
                        "id" => 1,
                        "name" => "membership1",
                    ],
                    "subscriptionFee" => "4",
                    "subscriptionPaidBy" => "Company",
                    "currencyType" => [
                        "id" => "LKR",
                        "name" => "Sri Lanka Rupee",
                    ],
                    "subscriptionCommenceDate" => '2011-05-20',
                    "subscriptionRenewalDate" => "2011-05-22",
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
        $api = new EmployeeMembershipAPI($this->getRequest());
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
