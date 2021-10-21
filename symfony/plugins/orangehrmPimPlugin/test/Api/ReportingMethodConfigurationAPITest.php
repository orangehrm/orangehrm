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

use OrangeHRM\Pim\Api\ReportingMethodConfigurationAPI;
use OrangeHRM\Pim\Dao\ReportingMethodConfigurationDao;
use OrangeHRM\Pim\Service\ReportingMethodConfigurationService;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Entity\ReportingMethod;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;

/**
 * @group Pim
 * @group APIv2
 */
class ReportingMethodConfigurationAPITest extends EndpointTestCase
{
    public function getReportingMethodService(): void
    {
        $api = new ReportingMethodConfigurationAPI($this->getRequest());
        $this->assertTrue($api->getReportingMethodService() instanceof ReportingMethodConfigurationService);
    }

    public function testGetOne(): void
    {
        $this->createKernel();
        $reportingMethodDao = $this->getMockBuilder(ReportingMethodConfigurationDao::class)
            ->onlyMethods(['getReportingMethodById'])
            ->getMock();

        $reportingMethod = new ReportingMethod();
        $reportingMethod->setId(1);
        $reportingMethod->setName('Indirect');

        $reportingMethodDao->expects($this->exactly(1))
            ->method('getReportingMethodById')
            ->with(1)
            ->will($this->returnValue($reportingMethod));
        $reportingMethodService = $this->getMockBuilder(ReportingMethodConfigurationService::class)
            ->onlyMethods(['getReportingMethodDao'])
            ->getMock();
        $reportingMethodService->expects($this->exactly(1))
            ->method('getReportingMethodDao')
            ->willReturn($reportingMethodDao);

        /** @var MockObject&ReportingMethodConfigurationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            ReportingMethodConfigurationAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_ID => 1
                ]
            ]
        )->onlyMethods(['getReportingMethodService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getReportingMethodService')
            ->will($this->returnValue($reportingMethodService));

        $result = $api->getOne();
        $this->assertEquals(
            [
                "id" => 1,
                "name" => "Indirect"
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForGetOne(): void
    {
        $api = new ReportingMethodConfigurationAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetOne();
        $this->assertTrue(
            $this->validate(
                [CommonParams::PARAMETER_ID => 1],
                $rules
            )
        );
    }

    public function testDelete()
    {
        $reportingMethodDao = $this->getMockBuilder(ReportingMethodConfigurationDao::class)
            ->onlyMethods(['deleteReportingMethods'])
            ->getMock();

        $reportingMethod = new ReportingMethod();
        $reportingMethod->setId(1);
        $reportingMethod->setName('Indirect');

        $reportingMethodDao->expects($this->exactly(1))
            ->method('deleteReportingMethods')
            ->with([1])
            ->willReturn(1);
        $reportingMethodService = $this->getMockBuilder(ReportingMethodConfigurationService::class)
            ->onlyMethods(['getReportingMethodDao'])
            ->getMock();
        $reportingMethodService->expects($this->exactly(1))
            ->method('getReportingMethodDao')
            ->willReturn($reportingMethodDao);

        /** @var MockObject&ReportingMethodConfigurationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            ReportingMethodConfigurationAPI::class,
            [

                RequestParams::PARAM_TYPE_BODY => [
                    CommonParams::PARAMETER_IDS => [1],
                ]
            ]
        )->onlyMethods(['getReportingMethodService'])
            ->getMock();
        $api->expects($this->exactly(1))
            ->method('getReportingMethodService')
            ->will($this->returnValue($reportingMethodService));

        $result = $api->delete();
        $this->assertEquals(
            [
                1
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForDelete(): void
    {
        $reportingMethodService = $this->getMockBuilder(ReportingMethodConfigurationService::class)
            ->onlyMethods(['getReportingMethodIdsInUse'])
            ->getMock();
        $reportingMethodService->expects($this->once())
            ->method('getReportingMethodIdsInUse')
            ->willReturn([2]);
        /** @var MockObject&ReportingMethodConfigurationAPI $api */
        $api = $this->getApiEndpointMockBuilder(ReportingMethodConfigurationAPI::class)
            ->onlyMethods(['getReportingMethodService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getReportingMethodService')
            ->willReturn($reportingMethodService);
        $rules = $api->getValidationRuleForDelete();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_IDS => [1],
                ],
                $rules
            )
        );
    }

    public function testUpdate()
    {
        $reportingMethodDao = $this->getMockBuilder(ReportingMethodConfigurationDao::class)
            ->onlyMethods(['saveReportingMethod', 'getReportingMethodById'])
            ->getMock();

        $reportingMethod = new ReportingMethod();
        $reportingMethod->setId(1);
        $reportingMethod->setName('Direct');

        $reportingMethodDao->expects($this->exactly(1))
            ->method('getReportingMethodById')
            ->with(1)
            ->willReturn($reportingMethod);
        $reportingMethodDao->expects($this->exactly(1))
            ->method('saveReportingMethod')
            ->with($reportingMethod)
            ->will($this->returnValue($reportingMethod));
        $reportingMethodService = $this->getMockBuilder(ReportingMethodConfigurationService::class)
            ->onlyMethods(['getReportingMethodDao'])
            ->getMock();
        $reportingMethodService->expects($this->exactly(2))
            ->method('getReportingMethodDao')
            ->willReturn($reportingMethodDao);

        /** @var MockObject&ReportingMethodConfigurationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            ReportingMethodConfigurationAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_ID => 1
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    ReportingMethodConfigurationAPI::PARAMETER_NAME => 'Indirect',
                ]
            ]
        )->onlyMethods(['getReportingMethodService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getReportingMethodService')
            ->will($this->returnValue($reportingMethodService));

        $result = $api->update();
        $this->assertEquals(
            [
                "id" => 1,
                "name" => "Indirect"
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForUpdate(): void
    {
        $api = new ReportingMethodConfigurationAPI($this->getRequest());
        $rules = $api->getValidationRuleForUpdate();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_ID => 1,
                    ReportingMethodConfigurationAPI::PARAMETER_NAME => 'Indirect',
                ],
                $rules
            )
        );
    }

    public function testCreate()
    {
        $reportingMethodDao = $this->getMockBuilder(ReportingMethodConfigurationDao::class)
            ->onlyMethods(['saveReportingMethod'])
            ->getMock();
        $reportingMethodDao->expects($this->once())
            ->method('saveReportingMethod')
            ->will(
                $this->returnCallback(
                    function (ReportingMethod $reportingMethod) {
                        $reportingMethod->setId(1);
                        return $reportingMethod;
                    }
                )
            );

        $reportingMethodService = $this->getMockBuilder(ReportingMethodConfigurationService::class)
            ->onlyMethods(['getReportingMethodDao'])
            ->getMock();
        $reportingMethodService->expects($this->once())
            ->method('getReportingMethodDao')
            ->willReturn($reportingMethodDao);

        /** @var MockObject&ReportingMethodConfigurationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            ReportingMethodConfigurationAPI::class,
            [
                RequestParams::PARAM_TYPE_BODY => [
                    ReportingMethodConfigurationAPI::PARAMETER_NAME => 'Direct',
                ]
            ]
        )->onlyMethods(['getReportingMethodService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getReportingMethodService')
            ->will($this->returnValue($reportingMethodService));

        $result = $api->create();
        $this->assertEquals(
            [
                "id" => 1,
                "name" => 'Direct'
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForCreate(): void
    {
        $api = new ReportingMethodConfigurationAPI($this->getRequest());
        $rules = $api->getValidationRuleForCreate();
        $this->assertTrue(
            $this->validate(
                [
                    ReportingMethodConfigurationAPI::PARAMETER_NAME => 'Direct',
                ],
                $rules
            )
        );
    }

    public function testGetAll()
    {
        $reportingMethodDao = $this->getMockBuilder(ReportingMethodConfigurationDao::class)
            ->onlyMethods(['getReportingMethodList', 'getReportingMethodCount'])
            ->getMock();

        $reportingMethod1 = new ReportingMethod();
        $reportingMethod1->setId(1);
        $reportingMethod1->setName('Indirect');
        $reportingMethod2 = new ReportingMethod();
        $reportingMethod2->setId(2);
        $reportingMethod2->setName('Direct');

        $reportingMethodDao->expects($this->exactly(1))
            ->method('getReportingMethodList')
            ->willReturn([$reportingMethod1, $reportingMethod2]);
        $reportingMethodDao->expects($this->exactly(1))
            ->method('getReportingMethodCount')
            ->willReturn(2);
        $reportingMethodService = $this->getMockBuilder(ReportingMethodConfigurationService::class)
            ->onlyMethods(['getReportingMethodDao'])
            ->getMock();
        $reportingMethodService->expects($this->exactly(2))
            ->method('getReportingMethodDao')
            ->willReturn($reportingMethodDao);

        /** @var MockObject&ReportingMethodConfigurationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            ReportingMethodConfigurationAPI::class,
            [
                RequestParams::PARAM_TYPE_BODY => [
                    ReportingMethodConfigurationAPI::PARAMETER_NAME,
                ]
            ]
        )->onlyMethods(['getReportingMethodService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getReportingMethodService')
            ->will($this->returnValue($reportingMethodService));

        $result = $api->getAll();
        $this->assertEquals(
            [
                [
                    "id" => 1,
                    "name" => "Indirect"
                ],
                [
                    "id" => 2,
                    "name" => "Direct"
                ]
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                "total" => 2
            ],
            $result->getMeta()->all()
        );
    }

    public function testGetValidationRuleForGetAll(): void
    {
        $api = new ReportingMethodConfigurationAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetAll();
        $this->assertTrue(
            $this->validate(
                [],
                $rules
            )
        );
    }
}
