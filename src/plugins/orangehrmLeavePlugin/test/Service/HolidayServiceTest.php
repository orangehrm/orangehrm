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

namespace OrangeHRM\Tests\Leave\Service;

use DateTime;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Holiday;
use OrangeHRM\Framework\Cache\FilesystemAdapter;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Dao\HolidayDao;
use OrangeHRM\Leave\Dto\HolidaySearchFilterParams;
use OrangeHRM\Leave\Service\HolidayService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Leave
 * @group Service
 */
class HolidayServiceTest extends KernelTestCase
{
    private HolidayService $holidayService;
    private $fixture;

    protected function setUp(): void
    {
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmLeavePlugin/test/fixtures/HolidayService.yml';
        $this->holidayService = new HolidayService();
        $cache = new FilesystemAdapter();
        $cache->clear();
        $this->createKernelWithMockServices([Services::CACHE => $cache]);
    }

    public function testGetSetHolidayDao(): void
    {
        $this->assertTrue($this->holidayService->getHolidayDao() instanceof HolidayDao);
    }

    public function testSaveHoliday(): void
    {
        $holidays = TestDataService::loadObjectList(Holiday::class, $this->fixture, 'Holiday');
        $holiday = $holidays[0];

        $holidayDao = $this->getMockBuilder(HolidayDao::class)
            ->onlyMethods(['saveHoliday'])
            ->getMock();
        $holidayDao->expects($this->once())
            ->method('saveHoliday')
            ->with($holiday)
            ->will($this->returnValue($holiday));

        $holidayService = $this->getMockBuilder(HolidayService::class)
            ->onlyMethods(['getHolidayDao'])
            ->getMock();
        $holidayService->expects($this->once())
            ->method('getHolidayDao')
            ->willReturn($holidayDao);

        $this->assertTrue($holidayService->saveHoliday($holiday) instanceof Holiday);
    }

    public function testSearchHolidaysNoneRecurring(): void
    {
        $fixture = [
            [
                'id' => 1,
                'recurring' => 0,
                'name' => 'Public Holiday',
                'date' => new DateTime('2009-03-22'),
                'length' => 4
            ],
            [
                'id' => 2,
                'recurring' => 0,
                'name' => 'Public Holiday',
                'date' => new DateTime('2010-05-22'),
                'length' => 8
            ],
            [
                'id' => 3,
                'recurring' => 0,
                'name' => 'Public Holiday',
                'date' => new DateTime('2010-05-27'),
                'length' => 4
            ],
            [
                'id' => 4,
                'recurring' => 0,
                'name' => 'Public Holiday',
                'date' => new DateTime('2010-06-27'),
                'length' => 8
            ]
        ];

        $holidays = TestDataService::loadObjectListFromArray(Holiday::class, $fixture);

        $holidayDao = $this->getMockBuilder(HolidayDao::class)
            ->onlyMethods(['searchHolidays'])
            ->getMock();
        $holidayDao->expects($this->once())
            ->method('searchHolidays')
            ->will($this->returnValue($holidays));

        $holidayService = $this->getMockBuilder(HolidayService::class)
            ->onlyMethods(['getHolidayDao'])
            ->getMock();
        $holidayService->expects($this->once())
            ->method('getHolidayDao')
            ->willReturn($holidayDao);

        $holidaySearchFilterParams = new HolidaySearchFilterParams();
        $holidaySearchFilterParams->setFromDate(new DateTime("2009-01-01"));
        $holidaySearchFilterParams->setToDate(new DateTime("2010-12-31"));
        $result = $holidayService->searchHolidays($holidaySearchFilterParams);

        $this->compareHolidays($holidays, $result);
    }

    public function testSearchHolidaysRecurringCurrentYear(): void
    {
        $fixture = [
            [
                'id' => 1,
                'recurring' => 0,
                'name' => 'Public Holiday',
                'date' => new DateTime('2010-03-22'),
                'length' => 4
            ],
            [
                'id' => 2,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2010-04-01'),
                'length' => 8
            ],
            [
                'id' => 3,
                'recurring' => 0,
                'name' => 'Public Holiday',
                'date' => new DateTime('2010-05-27'),
                'length' => 4
            ],
            [
                'id' => 4,
                'recurring' => 0,
                'name' => 'Public Holiday',
                'date' => new DateTime('2010-06-27'),
                'length' => 8
            ]
        ];

        $holidays = TestDataService::loadObjectListFromArray(Holiday::class, $fixture);

        $holidayDao = $this->getMockBuilder(HolidayDao::class)
            ->onlyMethods(['searchHolidays'])
            ->getMock();
        $holidayDao->expects($this->once())
            ->method('searchHolidays')
            ->will($this->returnValue($holidays));

        $holidayService = $this->getMockBuilder(HolidayService::class)
            ->onlyMethods(['getHolidayDao'])
            ->getMock();
        $holidayService->expects($this->once())
            ->method('getHolidayDao')
            ->willReturn($holidayDao);

        $holidaySearchFilterParams = new HolidaySearchFilterParams();
        $holidaySearchFilterParams->setFromDate(new DateTime("2010-01-01"));
        $holidaySearchFilterParams->setToDate(new DateTime("2010-12-31"));
        $result = $holidayService->searchHolidays($holidaySearchFilterParams);

        $this->compareHolidays($holidays, $result);
    }

    public function testSearchHolidaysRecurringOtherYear(): void
    {
        $fixture = [
            [
                'id' => 2,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2008-04-01'),
                'length' => 8
            ],
            [
                'id' => 3,
                'recurring' => 0,
                'name' => 'Public Holiday',
                'date' => new DateTime('2010-05-27'),
                'length' => 4
            ],
            [
                'id' => 4,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2010-06-27'),
                'length' => 8
            ],
            [
                'id' => 5,
                'recurring' => 0,
                'name' => 'Another Holiday',
                'date' => new DateTime('2010-09-13'),
                'length' => 8
            ],
            [
                'id' => 7,
                'recurring' => 1,
                'name' => 'Another Holiday',
                'date' => new DateTime('2012-09-13'),
                'length' => 8
            ]
        ];

        $holidays = TestDataService::loadObjectListFromArray(Holiday::class, $fixture);

        $holidayDao = $this->getMockBuilder(HolidayDao::class)
            ->onlyMethods(['searchHolidays'])
            ->getMock();
        $holidayDao->expects($this->once())
            ->method('searchHolidays')
            ->will($this->returnValue($holidays));

        $holidayService = $this->getMockBuilder(HolidayService::class)
            ->onlyMethods(['getHolidayDao'])
            ->getMock();
        $holidayService->expects($this->once())
            ->method('getHolidayDao')
            ->willReturn($holidayDao);

        $holidaySearchFilterParams = new HolidaySearchFilterParams();
        $holidaySearchFilterParams->setFromDate(new DateTime("2010-01-01"));
        $holidaySearchFilterParams->setToDate(new DateTime("2010-12-31"));
        $result = $holidayService->searchHolidays($holidaySearchFilterParams);

        $expectedArray = [
            [
                'id' => 2,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2010-04-01'),
                'length' => 8
            ],
            [
                'id' => 3,
                'recurring' => 0,
                'name' => 'Public Holiday',
                'date' => new DateTime('2010-05-27'),
                'length' => 4
            ],
            [
                'id' => 4,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2010-06-27'),
                'length' => 8
            ],
            [
                'id' => 5,
                'recurring' => 0,
                'name' => 'Another Holiday',
                'date' => new DateTime('2010-09-13'),
                'length' => 8
            ],
            [
                'id' => 7,
                'recurring' => 1,
                'name' => 'Another Holiday',
                'date' => new DateTime('2010-09-13'),
                'length' => 8
            ]
        ];
        $expected = TestDataService::loadObjectListFromArray(Holiday::class, $expectedArray);

        $this->compareHolidays($expected, $result);
    }

    public function testSearchHolidaysRecurringMultiYear(): void
    {
        $fixture = [
            [
                'id' => 1,
                'recurring' => 0,
                'name' => 'Public Holiday',
                'date' => new DateTime('2008-03-22'),
                'length' => 4
            ],
            [
                'id' => 2,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2008-04-01'),
                'length' => 8
            ],
            [
                'id' => 3,
                'recurring' => 0,
                'name' => 'Public Holiday',
                'date' => new DateTime('2010-05-27'),
                'length' => 4
            ],
            [
                'id' => 4,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2010-06-27'),
                'length' => 8
            ],
            [
                'id' => 5,
                'recurring' => 0,
                'name' => 'Another Holiday',
                'date' => new DateTime('2010-09-13'),
                'length' => 8
            ],
            [
                'id' => 6,
                'recurring' => 0,
                'name' => 'Another Holiday',
                'date' => new DateTime('2011-01-13'),
                'length' => 8
            ],
            [
                'id' => 7,
                'recurring' => 1,
                'name' => 'Another Holiday',
                'date' => new DateTime('2012-09-13'),
                'length' => 8
            ]
        ];

        $holidays = TestDataService::loadObjectListFromArray(Holiday::class, $fixture);

        $holidayDao = $this->getMockBuilder(HolidayDao::class)
            ->onlyMethods(['searchHolidays'])
            ->getMock();
        $holidayDao->expects($this->once())
            ->method('searchHolidays')
            ->will($this->returnValue($holidays));

        $holidayService = $this->getMockBuilder(HolidayService::class)
            ->onlyMethods(['getHolidayDao'])
            ->getMock();
        $holidayService->expects($this->once())
            ->method('getHolidayDao')
            ->willReturn($holidayDao);

        $holidaySearchFilterParams = new HolidaySearchFilterParams();
        $holidaySearchFilterParams->setFromDate(new DateTime("2005-01-01"));
        $holidaySearchFilterParams->setToDate(new DateTime("2013-07-01"));
        $result = $holidayService->searchHolidays($holidaySearchFilterParams);

        $expectedArray = [
            [
                'id' => 2,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2005-04-01'),
                'length' => 8
            ],
            [
                'id' => 4,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2005-06-27'),
                'length' => 8
            ],
            [
                'id' => 7,
                'recurring' => 1,
                'name' => 'Another Holiday',
                'date' => new DateTime('2005-09-13'),
                'length' => 8
            ],
            [
                'id' => 2,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2006-04-01'),
                'length' => 8
            ],
            [
                'id' => 4,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2006-06-27'),
                'length' => 8
            ],
            [
                'id' => 7,
                'recurring' => 1,
                'name' => 'Another Holiday',
                'date' => new DateTime('2006-09-13'),
                'length' => 8
            ],
            [
                'id' => 2,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2007-04-01'),
                'length' => 8
            ],
            [
                'id' => 4,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2007-06-27'),
                'length' => 8
            ],
            [
                'id' => 7,
                'recurring' => 1,
                'name' => 'Another Holiday',
                'date' => new DateTime('2007-09-13'),
                'length' => 8
            ],
            [
                'id' => 1,
                'recurring' => 0,
                'name' => 'Public Holiday',
                'date' => new DateTime('2008-03-22'),
                'length' => 4
            ],
            [
                'id' => 2,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2008-04-01'),
                'length' => 8
            ],
            [
                'id' => 4,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2008-06-27'),
                'length' => 8
            ],
            [
                'id' => 7,
                'recurring' => 1,
                'name' => 'Another Holiday',
                'date' => new DateTime('2008-09-13'),
                'length' => 8
            ],
            [
                'id' => 2,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2009-04-01'),
                'length' => 8
            ],
            [
                'id' => 4,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2009-06-27'),
                'length' => 8
            ],
            [
                'id' => 7,
                'recurring' => 1,
                'name' => 'Another Holiday',
                'date' => new DateTime('2009-09-13'),
                'length' => 8
            ],
            [
                'id' => 2,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2010-04-01'),
                'length' => 8
            ],
            [
                'id' => 3,
                'recurring' => 0,
                'name' => 'Public Holiday',
                'date' => new DateTime('2010-05-27'),
                'length' => 4
            ],
            [
                'id' => 4,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2010-06-27'),
                'length' => 8
            ],
            [
                'id' => 5,
                'recurring' => 0,
                'name' => 'Another Holiday',
                'date' => new DateTime('2010-09-13'),
                'length' => 8
            ],
            [
                'id' => 7,
                'recurring' => 1,
                'name' => 'Another Holiday',
                'date' => new DateTime('2010-09-13'),
                'length' => 8
            ],
            [
                'id' => 6,
                'recurring' => 0,
                'name' => 'Another Holiday',
                'date' => new DateTime('2011-01-13'),
                'length' => 8
            ],
            [
                'id' => 2,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2011-04-01'),
                'length' => 8
            ],
            [
                'id' => 4,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2011-06-27'),
                'length' => 8
            ],
            [
                'id' => 7,
                'recurring' => 1,
                'name' => 'Another Holiday',
                'date' => new DateTime('2011-09-13'),
                'length' => 8
            ],
            [
                'id' => 2,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2012-04-01'),
                'length' => 8
            ],
            [
                'id' => 4,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2012-06-27'),
                'length' => 8
            ],
            [
                'id' => 7,
                'recurring' => 1,
                'name' => 'Another Holiday',
                'date' => new DateTime('2012-09-13'),
                'length' => 8
            ],
            [
                'id' => 2,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2013-04-01'),
                'length' => 8
            ],
            [
                'id' => 4,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2013-06-27'),
                'length' => 8
            ]
        ];

        $expected = TestDataService::loadObjectListFromArray(Holiday::class, $expectedArray);
        $this->compareHolidays($expected, $result);
    }

    public function testSearchHolidaysRecurringPartYear(): void
    {
        $fixture = [
            [
                'id' => 2,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2008-04-01'),
                'length' => 8
            ],
            [
                'id' => 4,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2010-06-27'),
                'length' => 8
            ],
            [
                'id' => 5,
                'recurring' => 0,
                'name' => 'Another Holiday',
                'date' => new DateTime('2010-09-13'),
                'length' => 8
            ],
            [
                'id' => 7,
                'recurring' => 1,
                'name' => 'Another Holiday',
                'date' => new DateTime('2012-09-13'),
                'length' => 8
            ]
        ];

        $holidays = TestDataService::loadObjectListFromArray(Holiday::class, $fixture);

        $holidayDao = $this->getMockBuilder(HolidayDao::class)
            ->onlyMethods(['searchHolidays'])
            ->getMock();
        $holidayDao->expects($this->once())
            ->method('searchHolidays')
            ->will($this->returnValue($holidays));

        $holidayService = $this->getMockBuilder(HolidayService::class)
            ->onlyMethods(['getHolidayDao'])
            ->getMock();
        $holidayService->expects($this->once())
            ->method('getHolidayDao')
            ->willReturn($holidayDao);

        $holidaySearchFilterParams = new HolidaySearchFilterParams();
        $holidaySearchFilterParams->setFromDate(new DateTime("2010-06-11"));
        $holidaySearchFilterParams->setToDate(new DateTime("2010-11-02"));
        $result = $holidayService->searchHolidays($holidaySearchFilterParams);

        $expectedArray = [
            [
                'id' => 4,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2010-06-27'),
                'length' => 8
            ],
            [
                'id' => 5,
                'recurring' => 0,
                'name' => 'Another Holiday',
                'date' => new DateTime('2010-09-13'),
                'length' => 8
            ],
            [
                'id' => 7,
                'recurring' => 1,
                'name' => 'Another Holiday',
                'date' => new DateTime('2010-09-13'),
                'length' => 8
            ]
        ];

        $expected = TestDataService::loadObjectListFromArray(Holiday::class, $expectedArray);
        $this->compareHolidays($expected, $result);
    }

    protected function compareHolidays($expected, $actual): void
    {
        $this->assertEquals(count($expected), count($actual));
        for ($i = 0; $i < count($expected); $i++) {
            $this->compareHoliday($expected[$i], $actual[$i]);
        }
    }

    protected function compareHoliday(Holiday $expected, Holiday $actual): void
    {
        $this->assertEquals($expected->isRecurring(), $actual->isRecurring());
        $this->assertEquals($expected->getName(), $actual->getName());
        $this->assertEquals($expected->getDate(), $actual->getDate());
        $this->assertEquals($expected->getLength(), $actual->getLength());
    }

    public function testSearchHolidaysCheckUniqueness()
    {
        $fixture = [
            [
                'id' => 1,
                'recurring' => 0,
                'name' => 'Public Holiday',
                'date' => new DateTime('2008-03-22'),
                'length' => 4
            ],
            [
                'id' => 2,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2008-04-01'),
                'length' => 8
            ],
            [
                'id' => 3,
                'recurring' => 0,
                'name' => 'Public Holiday',
                'date' => new DateTime('2010-05-27'),
                'length' => 4
            ],
            [
                'id' => 4,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2010-06-27'),
                'length' => 8
            ],
            [
                'id' => 5,
                'recurring' => 0,
                'name' => 'Another Holiday',
                'date' => new DateTime('2010-09-13'),
                'length' => 8
            ],
            [
                'id' => 6,
                'recurring' => 0,
                'name' => 'Another Holiday',
                'date' => new DateTime('2011-01-13'),
                'length' => 8
            ],
            [
                'id' => 7,
                'recurring' => 1,
                'name' => 'Another Holiday',
                'date' => new DateTime('2012-09-13'),
                'length' => 8
            ]
        ];

        $holidays = TestDataService::loadObjectListFromArray(Holiday::class, $fixture);

        $holidayDao = $this->getMockBuilder(HolidayDao::class)
            ->onlyMethods(['searchHolidays'])
            ->getMock();
        $holidayDao->expects($this->once())
            ->method('searchHolidays')
            ->will($this->returnValue($holidays));

        $holidayService = $this->getMockBuilder(HolidayService::class)
            ->onlyMethods(['getHolidayDao'])
            ->getMock();
        $holidayService->expects($this->once())
            ->method('getHolidayDao')
            ->willReturn($holidayDao);

        $expectedArray = [
            [
                'id' => 2,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2005-04-01'),
                'length' => 8
            ],
            [
                'id' => 4,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2005-06-27'),
                'length' => 8
            ],
            [
                'id' => 7,
                'recurring' => 1,
                'name' => 'Another Holiday',
                'date' => new DateTime('2005-09-13'),
                'length' => 8
            ],
            [
                'id' => 2,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2006-04-01'),
                'length' => 8
            ],
            [
                'id' => 4,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2006-06-27'),
                'length' => 8
            ],
            [
                'id' => 7,
                'recurring' => 1,
                'name' => 'Another Holiday',
                'date' => new DateTime('2006-09-13'),
                'length' => 8
            ],
            [
                'id' => 2,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2007-04-01'),
                'length' => 8
            ],
            [
                'id' => 4,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2007-06-27'),
                'length' => 8
            ],
            [
                'id' => 7,
                'recurring' => 1,
                'name' => 'Another Holiday',
                'date' => new DateTime('2007-09-13'),
                'length' => 8
            ],
            [
                'id' => 1,
                'recurring' => 0,
                'name' => 'Public Holiday',
                'date' => new DateTime('2008-03-22'),
                'length' => 4
            ],
            [
                'id' => 2,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2008-04-01'),
                'length' => 8
            ],
            [
                'id' => 4,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2008-06-27'),
                'length' => 8
            ],
            [
                'id' => 7,
                'recurring' => 1,
                'name' => 'Another Holiday',
                'date' => new DateTime('2008-09-13'),
                'length' => 8
            ],
            [
                'id' => 2,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2009-04-01'),
                'length' => 8
            ],
            [
                'id' => 4,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2009-06-27'),
                'length' => 8
            ],
            [
                'id' => 7,
                'recurring' => 1,
                'name' => 'Another Holiday',
                'date' => new DateTime('2009-09-13'),
                'length' => 8
            ],
            [
                'id' => 2,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2010-04-01'),
                'length' => 8
            ],
            [
                'id' => 3,
                'recurring' => 0,
                'name' => 'Public Holiday',
                'date' => new DateTime('2010-05-27'),
                'length' => 4
            ],
            [
                'id' => 4,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2010-06-27'),
                'length' => 8
            ],
            [
                'id' => 5,
                'recurring' => 0,
                'name' => 'Another Holiday',
                'date' => new DateTime('2010-09-13'),
                'length' => 8
            ],
            [
                'id' => 7,
                'recurring' => 1,
                'name' => 'Another Holiday',
                'date' => new DateTime('2010-09-13'),
                'length' => 8
            ],
            [
                'id' => 6,
                'recurring' => 0,
                'name' => 'Another Holiday',
                'date' => new DateTime('2011-01-13'),
                'length' => 8
            ],
            [
                'id' => 2,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2011-04-01'),
                'length' => 8
            ],
            [
                'id' => 4,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2011-06-27'),
                'length' => 8
            ],
            [
                'id' => 7,
                'recurring' => 1,
                'name' => 'Another Holiday',
                'date' => new DateTime('2011-09-13'),
                'length' => 8
            ],
            [
                'id' => 2,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2012-04-01'),
                'length' => 8
            ],
            [
                'id' => 4,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2012-06-27'),
                'length' => 8
            ],
            [
                'id' => 7,
                'recurring' => 1,
                'name' => 'Another Holiday',
                'date' => new DateTime('2012-09-13'),
                'length' => 8
            ],
            [
                'id' => 2,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2013-04-01'),
                'length' => 8
            ],
            [
                'id' => 4,
                'recurring' => 1,
                'name' => 'Public Holiday',
                'date' => new DateTime('2013-06-27'),
                'length' => 8
            ]
        ];

        // check offset = 0, limit = 5
        $holidaySearchFilterParams = new HolidaySearchFilterParams();
        $holidaySearchFilterParams->setFromDate(new DateTime("2005-01-01"));
        $holidaySearchFilterParams->setToDate(new DateTime("2013-07-01"));
        $holidaySearchFilterParams->setLimit(5);
        $holidaySearchFilterParams->setOffset(0);
        $result = $holidayService->searchHolidays($holidaySearchFilterParams);

        $expected = TestDataService::loadObjectListFromArray(Holiday::class, array_slice($expectedArray, 0, 5));
        $this->compareHolidays($expected, $result);

        // check offset = 1, limit = 5
        $holidaySearchFilterParams = new HolidaySearchFilterParams();
        $holidaySearchFilterParams->setFromDate(new DateTime("2005-01-01"));
        $holidaySearchFilterParams->setToDate(new DateTime("2013-07-01"));
        $holidaySearchFilterParams->setLimit(5);
        $holidaySearchFilterParams->setOffset(1);
        $result = $holidayService->searchHolidays($holidaySearchFilterParams);

        $expected = TestDataService::loadObjectListFromArray(Holiday::class, array_slice($expectedArray, 1, 5));
        $this->compareHolidays($expected, $result);

        // check offset = 3, limit = 5
        $holidaySearchFilterParams = new HolidaySearchFilterParams();
        $holidaySearchFilterParams->setFromDate(new DateTime("2005-01-01"));
        $holidaySearchFilterParams->setToDate(new DateTime("2013-07-01"));
        $holidaySearchFilterParams->setLimit(5);
        $holidaySearchFilterParams->setOffset(3);
        $result = $holidayService->searchHolidays($holidaySearchFilterParams);

        $expected = TestDataService::loadObjectListFromArray(Holiday::class, array_slice($expectedArray, 3, 5));
        $this->compareHolidays($expected, $result);

        // check uniqueness (limit = 0)
        $holidaySearchFilterParams = new HolidaySearchFilterParams();
        $holidaySearchFilterParams->setFromDate(new DateTime("2005-01-01"));
        $holidaySearchFilterParams->setToDate(new DateTime("2013-07-01"));
        $holidaySearchFilterParams->setLimit(0);
        $result = $holidayService->searchHolidays($holidaySearchFilterParams);

        $expected = TestDataService::loadObjectListFromArray(Holiday::class, $expectedArray);
        $this->compareHolidays($expected, $result);
    }
}
