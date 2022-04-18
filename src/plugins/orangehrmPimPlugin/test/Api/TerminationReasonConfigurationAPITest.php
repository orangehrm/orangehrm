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

use OrangeHRM\Pim\Api\TerminationReasonConfigurationAPI;
use OrangeHRM\Pim\Dao\TerminationReasonConfigurationDao;
use OrangeHRM\Pim\Service\TerminationReasonConfigurationService;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Entity\TerminationReason;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;

/**
 * @group Pim
 * @group APIv2
 */
class TerminationReasonConfigurationAPITest extends EndpointTestCase
{
    public function testGetTerminationReasonConfigurationService(): void
    {
        $api = new TerminationReasonConfigurationAPI($this->getRequest());
        $this->assertTrue($api->getTerminationReasonConfigurationService() instanceof TerminationReasonConfigurationService);
    }

    public function testGetOne(): void
    {
        $terminationReasonDao = $this->getMockBuilder(TerminationReasonConfigurationDao::class)
            ->onlyMethods(['getTerminationReasonById'])
            ->getMock();

        $terminationReason = new TerminationReason();
        $terminationReason->setId(1);
        $terminationReason->setName('Resigned');

        $terminationReasonDao->expects($this->exactly(1))
            ->method('getTerminationReasonById')
            ->with(1)
            ->will($this->returnValue($terminationReason));
        $terminationReasonService = $this->getMockBuilder(TerminationReasonConfigurationService::class)
            ->onlyMethods(['getTerminationReasonDao'])
            ->getMock();
        $terminationReasonService->expects($this->exactly(1))
            ->method('getTerminationReasonDao')
            ->willReturn($terminationReasonDao);

        /** @var MockObject&TerminationReasonConfigurationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            TerminationReasonConfigurationAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_ID => 1
                ]
            ]
        )->onlyMethods(['getTerminationReasonConfigurationService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getTerminationReasonConfigurationService')
            ->will($this->returnValue($terminationReasonService));

        $result = $api->getOne();
        $this->assertEquals(
            [
                "id" => 1,
                "name" => "Resigned"
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForGetOne(): void
    {
        $api = new TerminationReasonConfigurationAPI($this->getRequest());
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
        $terminationReasonDao = $this->getMockBuilder(TerminationReasonConfigurationDao::class)
            ->onlyMethods(['deleteTerminationReasons'])
            ->getMock();

        $terminationReason = new TerminationReason();
        $terminationReason->setId(1);
        $terminationReason->setName('Resigned');

        $terminationReasonDao->expects($this->exactly(1))
            ->method('deleteTerminationReasons')
            ->with([1])
            ->willReturn(1);
        $terminationReasonService = $this->getMockBuilder(TerminationReasonConfigurationService::class)
            ->onlyMethods(['getTerminationReasonDao'])
            ->getMock();
        $terminationReasonService->expects($this->exactly(1))
            ->method('getTerminationReasonDao')
            ->willReturn($terminationReasonDao);

        /** @var MockObject&TerminationReasonConfigurationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            TerminationReasonConfigurationAPI::class,
            [

                RequestParams::PARAM_TYPE_BODY => [
                    CommonParams::PARAMETER_IDS => [1],
                ]
            ]
        )->onlyMethods(['getTerminationReasonConfigurationService'])
            ->getMock();
        $api->expects($this->exactly(1))
            ->method('getTerminationReasonConfigurationService')
            ->will($this->returnValue($terminationReasonService));

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
        $terminationReasonService = $this->getMockBuilder(TerminationReasonConfigurationService::class)
            ->onlyMethods(['getReasonIdsInUse'])
            ->getMock();
        $terminationReasonService->expects($this->once())
            ->method('getReasonIdsInUse')
            ->willReturn([2]);

        /** @var MockObject&TerminationReasonConfigurationAPI $api */
        $api = $this->getApiEndpointMockBuilder(TerminationReasonConfigurationAPI::class)
            ->onlyMethods(['getTerminationReasonConfigurationService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getTerminationReasonConfigurationService')
            ->willReturn($terminationReasonService);
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
        $terminationReasonDao = $this->getMockBuilder(TerminationReasonConfigurationDao::class)
            ->onlyMethods(['saveTerminationReason', 'getTerminationReasonById'])
            ->getMock();

        $terminationReason = new TerminationReason();
        $terminationReason->setId(1);
        $terminationReason->setName('Dismissed');

        $terminationReasonDao->expects($this->exactly(1))
            ->method('getTerminationReasonById')
            ->with(1)
            ->willReturn($terminationReason);
        $terminationReasonDao->expects($this->exactly(1))
            ->method('saveTerminationReason')
            ->with($terminationReason)
            ->will($this->returnValue($terminationReason));
        $terminationReasonService = $this->getMockBuilder(TerminationReasonConfigurationService::class)
            ->onlyMethods(['getTerminationReasonDao'])
            ->getMock();
        $terminationReasonService->expects($this->exactly(2))
            ->method('getTerminationReasonDao')
            ->willReturn($terminationReasonDao);

        /** @var MockObject&TerminationReasonConfigurationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            TerminationReasonConfigurationAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_ID => 1
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    TerminationReasonConfigurationAPI::PARAMETER_NAME => 'Resigned',
                ]
            ]
        )->onlyMethods(['getTerminationReasonConfigurationService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getTerminationReasonConfigurationService')
            ->will($this->returnValue($terminationReasonService));

        $result = $api->update();
        $this->assertEquals(
            [
                "id" => 1,
                "name" => "Resigned"
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForUpdate(): void
    {
        $api = new TerminationReasonConfigurationAPI($this->getRequest());
        $rules = $api->getValidationRuleForUpdate();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_ID => 1,
                    TerminationReasonConfigurationAPI::PARAMETER_NAME => 'Resigned',
                ],
                $rules
            )
        );
    }

    public function testCreate()
    {
        $terminationReasonDao = $this->getMockBuilder(TerminationReasonConfigurationDao::class)
            ->onlyMethods(['saveTerminationReason'])
            ->getMock();
        $terminationReasonDao->expects($this->once())
            ->method('saveTerminationReason')
            ->will(
                $this->returnCallback(
                    function (TerminationReason $terminationReason) {
                        $terminationReason->setId(1);
                        return $terminationReason;
                    }
                )
            );

        $terminationReasonService = $this->getMockBuilder(TerminationReasonConfigurationService::class)
            ->onlyMethods(['getTerminationReasonDao'])
            ->getMock();
        $terminationReasonService->expects($this->once())
            ->method('getTerminationReasonDao')
            ->willReturn($terminationReasonDao);

        /** @var MockObject&TerminationReasonConfigurationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            TerminationReasonConfigurationAPI::class,
            [
                RequestParams::PARAM_TYPE_BODY => [
                    TerminationReasonConfigurationAPI::PARAMETER_NAME => 'Dismissed',
                ]
            ]
        )->onlyMethods(['getTerminationReasonConfigurationService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getTerminationReasonConfigurationService')
            ->will($this->returnValue($terminationReasonService));

        $result = $api->create();
        $this->assertEquals(
            [
                "id" => 1,
                "name" => 'Dismissed'
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForCreate(): void
    {
        $api = new TerminationReasonConfigurationAPI($this->getRequest());
        $rules = $api->getValidationRuleForCreate();
        $this->assertTrue(
            $this->validate(
                [
                    TerminationReasonConfigurationAPI::PARAMETER_NAME => 'Dismissed',
                ],
                $rules
            )
        );
    }

    public function testGetAll()
    {
        $terminationReasonDao = $this->getMockBuilder(TerminationReasonConfigurationDao::class)
            ->onlyMethods(['getTerminationReasonList', 'getTerminationReasonCount'])
            ->getMock();

        $terminationReason1 = new TerminationReason();
        $terminationReason1->setId(1);
        $terminationReason1->setName('Resigned');
        $terminationReason2 = new TerminationReason();
        $terminationReason2->setId(2);
        $terminationReason2->setName('Dismissed');

        $terminationReasonDao->expects($this->exactly(1))
            ->method('getTerminationReasonList')
            ->willReturn([$terminationReason1, $terminationReason2]);
        $terminationReasonDao->expects($this->exactly(1))
            ->method('getTerminationReasonCount')
            ->willReturn(2);
        $terminationReasonService = $this->getMockBuilder(TerminationReasonConfigurationService::class)
            ->onlyMethods(['getTerminationReasonDao'])
            ->getMock();
        $terminationReasonService->expects($this->exactly(2))
            ->method('getTerminationReasonDao')
            ->willReturn($terminationReasonDao);

        /** @var MockObject&TerminationReasonConfigurationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            TerminationReasonConfigurationAPI::class,
            [
                RequestParams::PARAM_TYPE_BODY => [
                    TerminationReasonConfigurationAPI::PARAMETER_NAME,
                ]
            ]
        )->onlyMethods(['getTerminationReasonConfigurationService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getTerminationReasonConfigurationService')
            ->will($this->returnValue($terminationReasonService));

        $result = $api->getAll();
        $this->assertEquals(
            [
                [
                    "id" => 1,
                    "name" => "Resigned"
                ],
                [
                    "id" => 2,
                    "name" => "Dismissed"
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
        $api = new TerminationReasonConfigurationAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetAll();
        $this->assertTrue(
            $this->validate(
                [],
                $rules
            )
        );
    }
}
