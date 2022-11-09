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

namespace OrangeHRM\Tests\Leave\Api;

use DateTime;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\Holiday;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Api\HolidayAPI;
use OrangeHRM\Leave\Dao\HolidayDao;
use OrangeHRM\Leave\Service\HolidayService;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Leave
 * @group APIv2
 */
class HolidayAPITest extends EndpointTestCase
{
    protected function loadFixtures(): void
    {
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmLeavePlugin/test/fixtures/HolidayDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetOne(): void
    {
        $holidayDao = $this->getMockBuilder(HolidayDao::class)
            ->onlyMethods(['getHolidayById'])
            ->getMock();

        $holiday = new Holiday();
        $holiday->setId(1);
        $holiday->setRecurring(true);
        $holiday->setName('Christmas');
        $holiday->setDate(new DateTime('2021-12-25'));
        $holiday->setLength(0);

        $holidayDao->expects($this->exactly(1))
            ->method('getHolidayById')
            ->with(1)
            ->will($this->returnValue($holiday));

        $holidayService = $this->getMockBuilder(HolidayService::class)
            ->onlyMethods(['getHolidayDao'])
            ->getMock();

        $holidayService->expects($this->exactly(1))
            ->method('getHolidayDao')
            ->willReturn($holidayDao);

        $this->createKernelWithMockServices(
            [
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );

        /** @var MockObject&HolidayAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            HolidayAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_ID => 1
                ]
            ]
        )->onlyMethods(['getHolidayService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getHolidayService')
            ->will($this->returnValue($holidayService));

        $result = $api->getOne();
        $this->assertEquals(
            [
                "id" => 1,
                "name" => "Christmas",
                "date" => "2021-12-25",
                "recurring" => true,
                "length" => 0,
                "lengthName" => "Full Day"
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForGetOne(): void
    {
        $api = new HolidayAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetOne();
        $this->assertTrue(
            $this->validate(
                [CommonParams::PARAMETER_ID => 1],
                $rules
            )
        );
    }

    public function testUpdate()
    {
        $holidayDao = $this->getMockBuilder(HolidayDao::class)
            ->onlyMethods(['saveHoliday', 'getHolidayById'])
            ->getMock();

        $holiday = new Holiday();
        $holiday->setId(1);
        $holiday->setRecurring(true);
        $holiday->setName('Christmas');
        $holiday->setDate(new DateTime('2021-12-25'));
        $holiday->setLength(0);

        $holidayDao->expects($this->exactly(1))
            ->method('getHolidayById')
            ->with(1)
            ->willReturn($holiday);

        $holidayDao->expects($this->exactly(1))
            ->method('saveHoliday')
            ->will(
                $this->returnCallback(
                    function (Holiday $holiday) {
                        return $holiday;
                    }
                )
            );

        $holidayService = $this->getMockBuilder(HolidayService::class)
            ->onlyMethods(['getHolidayDao', 'getCache'])
            ->getMock();

        $holidayService->expects($this->exactly(1))
            ->method('getCache');

        $holidayService->expects($this->exactly(2))
            ->method('getHolidayDao')
            ->willReturn($holidayDao);

        $this->createKernelWithMockServices(
            [
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );

        /** @var MockObject&HolidayAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            HolidayAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_ID => 1
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    HolidayAPI::PARAMETER_NAME => 'Christmas',
                    HolidayAPI::PARAMETER_DATE => '2021-12-25',
                    HolidayAPI::PARAMETER_LENGTH => 0,
                    HolidayAPI::PARAMETER_RECURRING => true,
                ]
            ]
        )->onlyMethods(['getHolidayService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getHolidayService')
            ->will($this->returnValue($holidayService));

        $result = $api->update();
        $this->assertEquals(
            [
                "id" => 1,
                "name" => "Christmas",
                "date" => "2021-12-25",
                "recurring" => true,
                "length" => 0,
                "lengthName" => "Full Day"
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForUpdate(): void
    {
        $api = new HolidayAPI($this->getRequest());
        $rules = $api->getValidationRuleForUpdate();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_ID => 1,
                    HolidayAPI::PARAMETER_NAME => 'Christmas',
                    HolidayAPI::PARAMETER_DATE => '2021-12-25',
                    HolidayAPI::PARAMETER_LENGTH => 0,
                    HolidayAPI::PARAMETER_RECURRING => true,
                ],
                $rules
            )
        );
    }

    public function testDelete()
    {
        $holidayDao = $this->getMockBuilder(HolidayDao::class)
            ->onlyMethods(['deleteHolidays'])
            ->getMock();

        $holiday = new Holiday();
        $holiday->setId(1);
        $holiday->setRecurring(true);
        $holiday->setName('Christmas');
        $holiday->setDate(new DateTime('2021-12-25'));
        $holiday->setLength(0);

        $holidayDao->expects($this->exactly(1))
            ->method('deleteHolidays')
            ->with([1])
            ->willReturn(1);

        $holidayService = $this->getMockBuilder(HolidayService::class)
            ->onlyMethods(['getHolidayDao', 'getCache'])
            ->getMock();

        $holidayService->expects($this->exactly(1))
            ->method('getCache');

        $holidayService->expects($this->exactly(1))
            ->method('getHolidayDao')
            ->willReturn($holidayDao);

        /** @var MockObject&HolidayAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            HolidayAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [],
                RequestParams::PARAM_TYPE_BODY => [
                    CommonParams::PARAMETER_IDS => [1],
                ]
            ]
        )->onlyMethods(['getHolidayService'])
            ->getMock();
        $api->expects($this->exactly(1))
            ->method('getHolidayService')
            ->will($this->returnValue($holidayService));

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
        $api = new HolidayAPI($this->getRequest());
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

    public function testCreate()
    {
        $this->loadFixtures();

        $holidayDao = $this->getMockBuilder(HolidayDao::class)
            ->onlyMethods(['saveHoliday', 'getHolidayById'])
            ->getMock();

        $holiday = new Holiday();
        $holiday->setId(1);
        $holiday->setRecurring(true);
        $holiday->setName('Christmas');
        $holiday->setDate(new DateTime('2021-12-25'));
        $holiday->setLength(0);

        $holidayDao->expects($this->never())
            ->method('getHolidayById')
            ->with(1, 1)
            ->willReturn($holiday);

        $holidayDao->expects($this->once())
            ->method('saveHoliday')
            ->will(
                $this->returnCallback(
                    function (Holiday $holiday) {
                        $holiday->setId(1);
                        return $holiday;
                    }
                )
            );

        $holidayService = $this->getMockBuilder(HolidayService::class)
            ->onlyMethods(['getHolidayDao', 'getCache'])
            ->getMock();

        $holidayService->expects($this->once())
            ->method('getHolidayDao')
            ->willReturn($holidayDao);

        $holidayService->expects($this->exactly(1))
            ->method('getCache');

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $holidayService,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );

        /** @var MockObject&HolidayAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            HolidayAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [],
                RequestParams::PARAM_TYPE_BODY => [
                    HolidayAPI::PARAMETER_NAME => 'Christmas',
                    HolidayAPI::PARAMETER_DATE => '2021-12-25',
                    HolidayAPI::PARAMETER_LENGTH => 0,
                    HolidayAPI::PARAMETER_RECURRING => true,
                ]
            ]
        )->onlyMethods(['getHolidayService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getHolidayService')
            ->will($this->returnValue($holidayService));

        $result = $api->create();
        $this->assertEquals(
            [
                "id" => 1,
                "name" => "Christmas",
                "date" => "2021-12-25",
                "recurring" => true,
                "length" => 0,
                "lengthName" => "Full Day"
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForCreate(): void
    {
        $api = new HolidayAPI($this->getRequest());
        $rules = $api->getValidationRuleForCreate();
        $this->assertTrue(
            $this->validate(
                [
                    HolidayAPI::PARAMETER_NAME => 'Christmas',
                    HolidayAPI::PARAMETER_DATE => '2021-12-25',
                    HolidayAPI::PARAMETER_LENGTH => 0,
                    HolidayAPI::PARAMETER_RECURRING => true,
                ],
                $rules
            )
        );
    }

    public function testGetAll()
    {
        $holidayService = $this->getMockBuilder(HolidayService::class)
            ->onlyMethods(['searchHolidays', 'searchHolidaysCount'])
            ->getMock();

        $holiday1 = new Holiday();
        $holiday1->setId(1);
        $holiday1->setRecurring(true);
        $holiday1->setName('Christmas');
        $holiday1->setDate(new DateTime('2021-12-25'));
        $holiday1->setLength(0);

        $holiday2 = new Holiday();
        $holiday2->setId(2);
        $holiday2->setRecurring(false);
        $holiday2->setName('Special');
        $holiday2->setDate(new DateTime('2021-08-21'));
        $holiday2->setLength(4);

        $holidayService->expects($this->exactly(1))
            ->method('searchHolidays')
            ->willReturn([$holiday1, $holiday2]);

        $holidayService->expects($this->exactly(1))
            ->method('searchHolidaysCount')
            ->willReturn(2);


        /** @var MockObject&HolidayAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            HolidayAPI::class,
            [
                RequestParams::PARAM_TYPE_QUERY => [
                    HolidayAPI::FILTER_FROM_DATE => '2021-01-01',
                    HolidayAPI::FILTER_TO_DATE => '2021-12-31',
                ]
            ]
        )->onlyMethods(['getHolidayService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getHolidayService')
            ->will($this->returnValue($holidayService));

        $this->createKernelWithMockServices(
            [
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );
        $result = $api->getAll();
        $this->assertEquals(
            [
                [
                    "id" => 1,
                    "name" => "Christmas",
                    "length" => 0,
                    "recurring" => true,
                    "date" => "2021-12-25",
                    'lengthName' => 'Full Day'
                ],
                [
                    "id" => 2,
                    "name" => "Special",
                    "length" => 4,
                    "recurring" => false,
                    "date" => "2021-08-21",
                    'lengthName' => 'Half Day'
                ]
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForGetAll(): void
    {
        $api = new HolidayAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetAll();
        $this->assertTrue(
            $this->validate(
                [
                    HolidayAPI::FILTER_FROM_DATE => '2021-01-01',
                    HolidayAPI::FILTER_TO_DATE => '2021-12-31',
                ],
                $rules
            )
        );
    }
}
