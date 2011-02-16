<?php
/*
 *
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
 *
*/

class HolidayDaoTest extends PHPUnit_Framework_TestCase {

    private $holidayDao ;

    protected function setUp() {

        $this->holidayDao	=	new HolidayDao();

        TestDataService::populate(sfConfig::get('sf_plugins_dir') . '/orangehrmCoreLeavePlugin/test/fixtures/HolidayDao.yml');
    }

    /* test readHoliday */

    public function testReadHoliday() {

        $holiday = $this->holidayDao->readHoliday(1);

        $this->assertTrue($holiday instanceof Holiday);
        $this->assertEquals(1, $holiday->getRecurring());
        $this->assertEquals("2010-05-27", $holiday->getDate());

    }


    /* test getHolidayList */

    public function testGetHolidayList() {

        $holidayList = $this->holidayDao->getHolidayList(date("Y"));
        foreach($holidayList as $holiday) {

            $this->assertTrue($holiday instanceof Holiday);

        }

    }

    /* test getHolidayList without passing year */

    public function testGetHolidayListWithoutPassingYear() {

        $holidayList = $this->holidayDao->getHolidayList();
        foreach($holidayList as $holiday) {

            $this->assertTrue($holiday instanceof Holiday);

        }
    }

    /* count getHolidayList */

    public function testCountGetHolidayListWithYear() {

        $holidayList = $this->holidayDao->getHolidayList($year="2010");
        $this->assertEquals(4, count($holidayList));

    }

    public function testCountGetHolidayListWithoutYear() {

        $holidayList = $this->holidayDao->getHolidayList();
        $this->assertEquals(2, count($holidayList));

    }

    /* test saveHoliday */

    public function testSaveHoliday() {

        $holiday = TestDataService::fetchObject('Holiday', 1);

        $holiday->setLength(4);
        $holiday->setRecurring(0);
        $holiday->setDate("2010-05-30");

        $this->holidayDao->saveHoliday($holiday);
        $savedHoliday = TestDataService::fetchObject('Holiday', $holiday->getHolidayId());

        $this->assertEquals($holiday->getLength(), $savedHoliday->getLength());
        $this->assertEquals($holiday->getRecurring(), $savedHoliday->getRecurring());
        $this->assertEquals($holiday->getDate(), $savedHoliday->getDate());

    }


    /* test saveHoliday without an Id*/

    public function testSaveHolidayWithNoId() {

        $holiday = new Holiday();
        $holiday->setLength(4);
        $holiday->setDescription("for dummies");

        $this->holidayDao->saveHoliday($holiday);

        $savedHoliday = TestDataService::fetchObject('Holiday', 5);

        $this->assertEquals(5, $savedHoliday->getHolidayId());
        $this->assertEquals(4, $savedHoliday->getLength());
        $this->assertEquals($holiday->getDescription(), $savedHoliday->getDescription());
    }

    /* test deleteHoliday */

    public function testDeleteHoliday() {

        $this->assertTrue($this->holidayDao->deleteHoliday(array(1,2)));
        $holiday = TestDataService::fetchObject('Holiday', 2);

        $this->assertFalse($holiday instanceof Holiday);

    }

    /* test readHolidayByDate */

    public function testReadHolidayByDate() {

        $readHoliday = $this->holidayDao->readHolidayByDate("2010-05-27");
        $this->assertTrue($readHoliday instanceof Holiday);

    }

    /* test getFullHolidayList */

    public function testGetFullHolidayList() {

        $holidayList = $this->holidayDao->getFullHolidayList();
        foreach($holidayList as $holiday) {

            $this->assertTrue($holiday instanceof Holiday);

        }
    }
    

    /* test SearchHolidays */

    public function testSearchHolidays() {
        $holidayList = $this->holidayDao->searchHolidays('2010-01-01', '2010-12-31');
        foreach($holidayList as $holiday) {
            $this->assertTrue($holiday instanceof Holiday);
        }
    }
}