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

namespace OrangeHRM\Tests\Leave\Dao;

use DateTime;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Holiday;
use OrangeHRM\Leave\Dao\HolidayDao;
use OrangeHRM\Leave\Dto\HolidaySearchFilterParams;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Leave
 * @group Dao
 */
class HolidayDaoTest extends TestCase
{
    private HolidayDao $holidayDao;
    private string $fixture;

    protected function setUp(): void
    {
        $this->holidayDao = new HolidayDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmLeavePlugin/test/fixtures/HolidayDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetHolidayById(): void
    {
        $holiday = $this->holidayDao->getHolidayById(1);

        $this->assertTrue($holiday instanceof Holiday);
        $this->assertEquals(1, $holiday->isRecurring());
        $this->assertEquals("2010-05-27", $holiday->getDate()->format('Y-m-d'));
    }

    public function testSaveHoliday(): void
    {
        /** @var Holiday $holiday */
        $holiday = TestDataService::fetchObject(Holiday::class, 1);

        $holiday->setLength(4);
        $holiday->setRecurring(false);
        $holiday->setDate(new DateTime("2010-05-30"));

        $this->holidayDao->saveHoliday($holiday);
        /** @var Holiday $savedHoliday */
        $savedHoliday = TestDataService::fetchObject(Holiday::class, $holiday->getId());

        $this->assertEquals($holiday->getLength(), $savedHoliday->getLength());
        $this->assertEquals($holiday->isRecurring(), $savedHoliday->isRecurring());
        $this->assertEquals($holiday->getDate(), $savedHoliday->getDate());
    }

    public function testSaveHolidayWithNoId(): void
    {
        $holiday = new Holiday();
        $holiday->setLength(4);
        $holiday->setName("for dummies");

        $savedHoliday = $this->holidayDao->saveHoliday($holiday);

        $this->assertEquals($holiday->getName(), $savedHoliday->getName());
        $this->assertIsInt($holiday->getId());
    }

    public function testSearchHolidaysWithoutFromToDates(): void
    {
        $holidaySearchFilterParams = new HolidaySearchFilterParams();
        $holidays = $this->holidayDao->searchHolidays($holidaySearchFilterParams);

        $this->assertCount(5, $holidays);
        foreach ($holidays as $holiday) {
            $this->assertTrue($holiday->isRecurring());
        }
    }

    public function testSearchHolidaysWithoutFromToDatesAndExcludeRecurring(): void
    {
        $holidaySearchFilterParams = new HolidaySearchFilterParams();
        $holidaySearchFilterParams->setExcludeRecurring(true);
        $holidays = $this->holidayDao->searchHolidays($holidaySearchFilterParams);

        $this->assertCount(0, $holidays);
    }

    public function testSearchHolidaysWithoutFromDateAndExcludeRecurring(): void
    {
        $holidaySearchFilterParams = new HolidaySearchFilterParams();
        $holidaySearchFilterParams->setToDate(new DateTime('2021-07-25'));
        $holidaySearchFilterParams->setExcludeRecurring(true);
        $holidays = $this->holidayDao->searchHolidays($holidaySearchFilterParams);

        $this->assertCount(0, $holidays);
    }

    public function testSearchHolidaysWithoutToDateAndExcludeRecurring(): void
    {
        $holidaySearchFilterParams = new HolidaySearchFilterParams();
        $holidaySearchFilterParams->setFromDate(new DateTime('2021-07-23'));
        $holidaySearchFilterParams->setExcludeRecurring(true);
        $holidays = $this->holidayDao->searchHolidays($holidaySearchFilterParams);

        $this->assertCount(0, $holidays);
    }

    public function testSearchHolidaysExcludeRecurring(): void
    {
        $holidaySearchFilterParams = new HolidaySearchFilterParams();
        $holidaySearchFilterParams->setFromDate(new DateTime('2010-05-28'));
        $holidaySearchFilterParams->setToDate(new DateTime('2010-08-28'));
        $holidaySearchFilterParams->setExcludeRecurring(true);
        $holidays = $this->holidayDao->searchHolidays($holidaySearchFilterParams);

        $this->assertCount(2, $holidays);
        $this->assertEquals('Fullmoon Day', $holidays[0]->getName());
        $this->assertEquals('Personal Holiday', $holidays[1]->getName());

        $holidaySearchFilterParams->setFromDate(new DateTime('2010-05-28'));
        $holidaySearchFilterParams->setToDate(new DateTime('2010-08-27'));
        $holidaySearchFilterParams->setExcludeRecurring(true);
        $holidays = $this->holidayDao->searchHolidays($holidaySearchFilterParams);
        $this->assertCount(1, $holidays);
        $this->assertEquals('Fullmoon Day', $holidays[0]->getName());

        $holidaySearchFilterParams->setFromDate(new DateTime('2010-08-28'));
        $holidaySearchFilterParams->setToDate(new DateTime('2010-08-28'));
        $holidaySearchFilterParams->setExcludeRecurring(true);
        $holidays = $this->holidayDao->searchHolidays($holidaySearchFilterParams);
        $this->assertCount(1, $holidays);
        $this->assertEquals('Personal Holiday', $holidays[0]->getName());
    }

    public function testSearchHolidays(): void
    {
        $holidaySearchFilterParams = new HolidaySearchFilterParams();
        $holidaySearchFilterParams->setFromDate(new DateTime('2010-05-27'));
        $holidaySearchFilterParams->setToDate(new DateTime('2010-08-28'));
        $holidays = $this->holidayDao->searchHolidays($holidaySearchFilterParams);

        $this->assertCount(7, $holidays);
        $this->assertEquals('Public Holiday', $holidays[0]->getName());
        $this->assertEquals('Fullmoon Day', $holidays[1]->getName());
        $this->assertEquals('Home Holiday', $holidays[2]->getName());
        $this->assertEquals('Personal Holiday', $holidays[3]->getName());
        $this->assertEquals('Sports Day', $holidays[4]->getName());
        $this->assertEquals('Father`s Day', $holidays[5]->getName());
        $this->assertEquals('Christmas Day', $holidays[6]->getName());
    }

    public function testGetHolidayByDate(): void
    {
        $holiday = $this->holidayDao->getHolidayByDate(new DateTime('2010-05-27'));
        $this->assertEquals('Public Holiday', $holiday->getName());

        $holiday = $this->holidayDao->getHolidayByDate(new DateTime('2011-05-27'));
        $this->assertEquals('Public Holiday', $holiday->getName());

        $holiday = $this->holidayDao->getHolidayByDate(new DateTime('2021-05-27'));
        $this->assertEquals('Public Holiday', $holiday->getName());

        $holiday = $this->holidayDao->getHolidayByDate(new DateTime('2000-07-25'));
        $this->assertEquals('Father`s Day', $holiday->getName());

        $holiday = $this->holidayDao->getHolidayByDate(new DateTime('2021-07-25'));
        // Not getting `Constitution Day` since `Father`s Day` added as recurring but same date in fixtures
        $this->assertEquals('Father`s Day', $holiday->getName());
    }

    public function testDeleteHolidays(): void
    {
        $toTobedeletedIds = [1, 2];
        $result = $this->holidayDao->deleteHolidays($toTobedeletedIds);
        $this->assertEquals(2, $result);
    }
}
