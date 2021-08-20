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

use OrangeHRM\Admin\Service\CountryService;
use OrangeHRM\Authentication\Auth\User;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Core\Helper\ClassHelper;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmpUsTaxExemption;
use OrangeHRM\Entity\Province;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Api\EmpUsTaxExemptionAPI;
use OrangeHRM\Pim\Dao\EmpUsTaxExemptionDao;
use OrangeHRM\Pim\Service\EmpUsTaxExemptionService;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group APIv2
 */
class EmpUsTaxExemptionAPITest extends EndpointTestCase
{
    protected function loadFixtures(): void
    {
        $this->fixture = Config::get(Config::PLUGINS_DIR) .
            '/orangehrmPimPlugin/test/fixtures/EmpUsTaxExemptionDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmpUsTaxExemptionService(): void
    {
        $api = new EmpUsTaxExemptionAPI($this->getRequest());
        $this->assertTrue($api->getEmpUsTaxExemptionService() instanceof EmpUsTaxExemptionService);
    }

    public function testGetOne(): void
    {
        $empNumber = 1;
        $empUsTaxExemptionDao = $this->getMockBuilder(EmpUsTaxExemptionDao::class)
            ->onlyMethods(['getEmployeeTaxExemption'])
            ->getMock();
        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $empUsTaxExemption = new EmpUsTaxExemption();
        $empUsTaxExemption->setFederalStatus('S');
        $empUsTaxExemption->setFederalExemptions(2);
        $empUsTaxExemption->setState('AK');
        $empUsTaxExemption->setStateStatus('S');
        $empUsTaxExemption->setStateExemptions(1);
        $empUsTaxExemption->setUnemploymentState('AK');
        $empUsTaxExemption->setWorkState('AK');
        $empUsTaxExemption->setEmployee($employee);

        $empUsTaxExemptionDao->expects($this->exactly(1))
            ->method('getEmployeeTaxExemption')
            ->with(1)
            ->will($this->returnValue($empUsTaxExemption));

        $empUsTaxExemptionService = $this->getMockBuilder(EmpUsTaxExemptionService::class)
            ->onlyMethods(['getEmpUsTaxExemptionDao'])
            ->getMock();

        $empUsTaxExemptionService->expects($this->exactly(1))
            ->method('getEmpUsTaxExemptionDao')
            ->willReturn($empUsTaxExemptionDao);

        $province = new Province();
        $province->setProvinceCode('AK');
        $province->setProvinceName('Alaska');
        $countryService = $this->getMockBuilder(CountryService::class)
            ->onlyMethods(['getProvinceByProvinceCode'])
            ->getMock();
        $countryService->expects($this->exactly(3))
            ->method('getProvinceByProvinceCode')
            ->with('AK')
            ->willReturn($province);

        $this->createKernelWithMockServices(
            [
                Services::COUNTRY_SERVICE => $countryService,
            ]
        );

        /** @var MockObject&EmpUsTaxExemptionAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmpUsTaxExemptionAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ]
            ]
        )->onlyMethods(['getEmpUsTaxExemptionService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getEmpUsTaxExemptionService')
            ->will($this->returnValue($empUsTaxExemptionService));

        $result = $api->getOne();
        $this->assertEquals(
            [
                "federalStatus" => "S",
                "federalExemptions" => 2,
                "taxState" => [
                    "code" => "AK",
                    "name" => "Alaska",
                ],
                "stateStatus" => "S",
                "stateExemptions" => 1,
                "unemploymentState" => [
                    "code" => "AK",
                    "name" => "Alaska",
                ],
                "workState" => [
                    "code" => "AK",
                    "name" => "Alaska",
                ],
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
        $this->createKernelWithMockServices([Services::CLASS_HELPER => new ClassHelper()]);
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
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

        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['showPimTaxExemptions'])
            ->getMock();
        $configService->expects($this->once())
            ->method('showPimTaxExemptions')
            ->willReturn(true);

        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::AUTH_USER => $authUser,
                Services::CONFIG_SERVICE => $configService,
            ]
        );
        $api = new EmpUsTaxExemptionAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetOne();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    CommonParams::PARAMETER_ID => 0
                ],
                $rules
            )
        );
    }

    public function testDelete()
    {
        $api = new EmpUsTaxExemptionAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new EmpUsTaxExemptionAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testUpdate()
    {
        $this->loadFixtures();

        $empNumber = 1;
        $empUsTaxExemptionDao = $this->getMockBuilder(EmpUsTaxExemptionDao::class)
            ->onlyMethods(['saveEmployeeTaxExemption', 'getEmployeeTaxExemption'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $empUsTaxExemption = new EmpUsTaxExemption();
        $empUsTaxExemption->setFederalStatus('S');
        $empUsTaxExemption->setFederalExemptions(2);
        $empUsTaxExemption->setState('AK');
        $empUsTaxExemption->setStateStatus('S');
        $empUsTaxExemption->setStateExemptions(1);
        $empUsTaxExemption->setUnemploymentState('AK');
        $empUsTaxExemption->setWorkState('AK');
        $empUsTaxExemption->setEmployee($employee);

        $empUsTaxExemptionDao->expects($this->exactly(1))
            ->method('getEmployeeTaxExemption')
            ->with(1)
            ->willReturn($empUsTaxExemption);

        $empUsTaxExemptionDao->expects($this->exactly(1))
            ->method('saveEmployeeTaxExemption')
            ->with($empUsTaxExemption)
            ->will($this->returnValue($empUsTaxExemption));

        $empUsTaxExemptionService = $this->getMockBuilder(EmpUsTaxExemptionService::class)
            ->onlyMethods(['getEmpUsTaxExemptionDao'])
            ->getMock();

        $empUsTaxExemptionService->expects($this->exactly(2))
            ->method('getEmpUsTaxExemptionDao')
            ->willReturn($empUsTaxExemptionDao);

        $province = new Province();
        $province->setProvinceCode('AK');
        $province->setProvinceName('Alaska');
        $countryService = $this->getMockBuilder(CountryService::class)
            ->onlyMethods(['getProvinceByProvinceCode'])
            ->getMock();
        $countryService->expects($this->exactly(3))
            ->method('getProvinceByProvinceCode')
            ->with('AK')
            ->willReturn($province);

        $this->createKernelWithMockServices(
            [
                Services::COUNTRY_SERVICE => $countryService,
            ]
        );

        /** @var MockObject&EmpUsTaxExemptionAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmpUsTaxExemptionAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_ID => 0,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    EmpUsTaxExemptionAPI::PARAMETER_FEDERAL_STATUS => "S",
                    EmpUsTaxExemptionAPI::PARAMETER_FEDERAL_EXEMPTIONS => 2,
                    EmpUsTaxExemptionAPI::PARAMETER_TAX_STATE_CODE => "AK",
                    EmpUsTaxExemptionAPI::PARAMETER_STATE_STATUS => "S",
                    EmpUsTaxExemptionAPI::PARAMETER_STATE_EXEMPTIONS => 1,
                    EmpUsTaxExemptionAPI::PARAMETER_UNEMPLOYMENT_STATE_CODE => "AK",
                    EmpUsTaxExemptionAPI::PARAMETER_WORK_STATE_CODE => "AK",
                ]
            ]
        )->onlyMethods(['getEmpUsTaxExemptionService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getEmpUsTaxExemptionService')
            ->will($this->returnValue($empUsTaxExemptionService));

        $result = $api->update();
        $this->assertEquals(
            [
                "federalStatus" => "S",
                "federalExemptions" => 2,
                "taxState" => [
                    "code" => "AK",
                    "name" => "Alaska",
                ],
                "stateStatus" => "S",
                "stateExemptions" => 1,
                "unemploymentState" => [
                    "code" => "AK",
                    "name" => "Alaska",
                ],
                "workState" => [
                    "code" => "AK",
                    "name" => "Alaska",
                ],
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
        $this->createKernelWithMockServices([Services::CLASS_HELPER => new ClassHelper()]);
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
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
        $province = new Province();
        $province->setProvinceCode('AK');
        $province->setProvinceName('Alaska');
        $countryService = $this->getMockBuilder(CountryService::class)
            ->onlyMethods(['getProvinceByProvinceCode'])
            ->getMock();
        $countryService->expects($this->exactly(3))
            ->method('getProvinceByProvinceCode')
            ->with('AK')
            ->willReturn($province);

        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['showPimTaxExemptions'])
            ->getMock();
        $configService->expects($this->once())
            ->method('showPimTaxExemptions')
            ->willReturn(true);

        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::AUTH_USER => $authUser,
                Services::COUNTRY_SERVICE => $countryService,
                Services::CONFIG_SERVICE => $configService,
            ]
        );
        $api = new EmpUsTaxExemptionAPI($this->getRequest());
        $rules = $api->getValidationRuleForUpdate();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    CommonParams::PARAMETER_ID => 0,
                    EmpUsTaxExemptionAPI::PARAMETER_FEDERAL_STATUS => "S",
                    EmpUsTaxExemptionAPI::PARAMETER_FEDERAL_EXEMPTIONS => 2,
                    EmpUsTaxExemptionAPI::PARAMETER_TAX_STATE_CODE => "AK",
                    EmpUsTaxExemptionAPI::PARAMETER_STATE_STATUS => "S",
                    EmpUsTaxExemptionAPI::PARAMETER_STATE_EXEMPTIONS => 1,
                    EmpUsTaxExemptionAPI::PARAMETER_UNEMPLOYMENT_STATE_CODE => "AK",
                    EmpUsTaxExemptionAPI::PARAMETER_WORK_STATE_CODE => "AK",
                ],
                $rules
            )
        );
    }
}
