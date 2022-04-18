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

namespace OrangeHRM\Tests\Admin\Api;

use OrangeHRM\Admin\Api\NationalityAPI;
use OrangeHRM\Admin\Dao\NationalityDao;
use OrangeHRM\Admin\Service\NationalityService;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Entity\Nationality;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;

/**
 * @group Admin
 * @group APIv2
 */
class NationalityAPITest extends EndpointTestCase
{
    public function testGetNationalityService(): void
    {
        $api = new NationalityAPI($this->getRequest());
        $this->assertTrue($api->getNationalityService() instanceof NationalityService);
    }

    public function testGetOne(): void
    {
        $nationalityDao = $this->getMockBuilder(NationalityDao::class)
            ->onlyMethods(['getNationalityById'])
            ->getMock();

        $nationality = new Nationality();
        $nationality->setId(1);
        $nationality->setName('Sri Lankan');

        $nationalityDao->expects($this->exactly(1))
            ->method('getNationalityById')
            ->with(1)
            ->will($this->returnValue($nationality));
        $nationalityService = $this->getMockBuilder(NationalityService::class)
            ->onlyMethods(['getNationalityDao'])
            ->getMock();
        $nationalityService->expects($this->exactly(1))
            ->method('getNationalityDao')
            ->willReturn($nationalityDao);

        /** @var MockObject&NationalityAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            NationalityAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_ID => 1
                ]
            ]
        )->onlyMethods(['getNationalityService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getNationalityService')
            ->will($this->returnValue($nationalityService));

        $result = $api->getOne();
        $this->assertEquals(
            [
                "id" => 1,
                "name" => "Sri Lankan"
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForGetOne(): void
    {
        $api = new NationalityAPI($this->getRequest());
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
        $nationalityDao = $this->getMockBuilder(NationalityDao::class)
            ->onlyMethods(['deleteNationalities'])
            ->getMock();

        $nationality = new Nationality();
        $nationality->setId(1);
        $nationality->setName('Sri Lankan');

        $nationalityDao->expects($this->exactly(1))
            ->method('deleteNationalities')
            ->with([1])
            ->willReturn(1);
        $nationalityService = $this->getMockBuilder(NationalityService::class)
            ->onlyMethods(['getNationalityDao'])
            ->getMock();
        $nationalityService->expects($this->exactly(1))
            ->method('getNationalityDao')
            ->willReturn($nationalityDao);

        /** @var MockObject&NationalityAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            NationalityAPI::class,
            [

                RequestParams::PARAM_TYPE_BODY => [
                    CommonParams::PARAMETER_IDS => [1],
                ]
            ]
        )->onlyMethods(['getNationalityService'])
            ->getMock();
        $api->expects($this->exactly(1))
            ->method('getNationalityService')
            ->will($this->returnValue($nationalityService));

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
        $api = new NationalityAPI($this->getRequest());
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
        $nationalityDao = $this->getMockBuilder(NationalityDao::class)
            ->onlyMethods(['saveNationality', 'getNationalityById'])
            ->getMock();

        $nationality = new Nationality();
        $nationality->setId(1);
        $nationality->setName('India');

        $nationalityDao->expects($this->exactly(1))
            ->method('getNationalityById')
            ->with(1)
            ->willReturn($nationality);
        $nationalityDao->expects($this->exactly(1))
            ->method('saveNationality')
            ->with($nationality)
            ->will($this->returnValue($nationality));
        $nationalityService = $this->getMockBuilder(NationalityService::class)
            ->onlyMethods(['getNationalityDao'])
            ->getMock();
        $nationalityService->expects($this->exactly(2))
            ->method('getNationalityDao')
            ->willReturn($nationalityDao);

        /** @var MockObject&NationalityAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            NationalityAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_ID => 1
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    NationalityAPI::PARAMETER_NAME => 'sri lankan',
                ]
            ]
        )->onlyMethods(['getNationalityService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getNationalityService')
            ->will($this->returnValue($nationalityService));

        $result = $api->update();
        $this->assertEquals(
            [
                "id" => 1,
                "name" => "sri lankan"
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForUpdate(): void
    {
        $api = new NationalityAPI($this->getRequest());
        $rules = $api->getValidationRuleForUpdate();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_ID => 1,
                    NationalityAPI::PARAMETER_NAME => 'Sri Lankan',
                ],
                $rules
            )
        );
    }

    public function testCreate()
    {
        $nationalityDao = $this->getMockBuilder(NationalityDao::class)
            ->onlyMethods(['saveNationality'])
            ->getMock();
        $nationalityDao->expects($this->once())
            ->method('saveNationality')
            ->will(
                $this->returnCallback(
                    function (Nationality $nationality) {
                        $nationality->setId(1);
                        return $nationality;
                    }
                )
            );

        $nationalityService = $this->getMockBuilder(NationalityService::class)
            ->onlyMethods(['getNationalityDao'])
            ->getMock();
        $nationalityService->expects($this->once())
            ->method('getNationalityDao')
            ->willReturn($nationalityDao);

        /** @var MockObject&NationalityAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            NationalityAPI::class,
            [
                RequestParams::PARAM_TYPE_BODY => [
                    NationalityAPI::PARAMETER_NAME => 'India',
                ]
            ]
        )->onlyMethods(['getNationalityService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getNationalityService')
            ->will($this->returnValue($nationalityService));

        $result = $api->create();
        $this->assertEquals(
            [
                "id" => 1,
                "name" => 'India'
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForCreate(): void
    {
        $api = new NationalityAPI($this->getRequest());
        $rules = $api->getValidationRuleForCreate();
        $this->assertTrue(
            $this->validate(
                [
                    NationalityAPI::PARAMETER_NAME => 'india',
                ],
                $rules
            )
        );
    }

    public function testGetAll()
    {
        $nationalityDao = $this->getMockBuilder(NationalityDao::class)
            ->onlyMethods(['getNationalityList', 'getNationalityCount'])
            ->getMock();

        $nationality1 = new Nationality();
        $nationality1->setId(1);
        $nationality1->setName('Sri Lankan');
        $nationality2 = new Nationality();
        $nationality2->setId(2);
        $nationality2->setName('Indian');

        $nationalityDao->expects($this->exactly(1))
            ->method('getNationalityList')
            ->willReturn([$nationality1, $nationality2]);
        $nationalityDao->expects($this->exactly(1))
            ->method('getNationalityCount')
            ->willReturn(2);
        $nationalityService = $this->getMockBuilder(NationalityService::class)
            ->onlyMethods(['getNationalityDao'])
            ->getMock();
        $nationalityService->expects($this->exactly(2))
            ->method('getNationalityDao')
            ->willReturn($nationalityDao);

        /** @var MockObject&NationalityAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            NationalityAPI::class,
            [
                RequestParams::PARAM_TYPE_BODY => [
                    NationalityAPI::PARAMETER_NAME,
                ]
            ]
        )->onlyMethods(['getNationalityService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getNationalityService')
            ->will($this->returnValue($nationalityService));

        $result = $api->getAll();
        $this->assertEquals(
            [
                [
                    "id" => 1,
                    "name" => "Sri Lankan"
                ],
                [
                    "id" => 2,
                    "name" => "Indian"
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
        $api = new NationalityAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetAll();
        $this->assertTrue(
            $this->validate(
                [],
                $rules
            )
        );
    }
}
