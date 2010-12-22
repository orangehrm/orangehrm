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


class DayOffDaoTest extends PHPUnit_Framework_TestCase {

    private $workWeekDao ;
    private $testCases;

    protected function setUp() {

        $this->testCases = sfYaml::load(sfConfig::get('sf_plugins_dir') . '/orangehrmCoreLeavePlugin/test/fixtures/WorkWeekDao.yml');
        $this->workWeekDao	=	new WorkWeekDao();

        TestDataService::populate(sfConfig::get('sf_plugins_dir') . '/orangehrmCoreLeavePlugin/test/fixtures/WorkWeekDao.yml');

    }

    /**
     *
     * @cover IsWeekend
     */
    public function testIsWeekend() {

        $result	=	$this->workWeekDao->isWeekend('2010-08-15');
        $this->assertTrue($result);

    }

    /* test whether half day weekend */

    public function testHalfDayIsWeekend() {

        $result	=	$this->workWeekDao->isWeekend('2010-08-14');
        $this->assertFalse($result);

    }

    /* test whether half day is weekend not full day */

    public function testHalfDayIsWeekendNotFullDay() {

        $result	=	$this->workWeekDao->isWeekend('2010-08-14', false);
        $this->assertTrue($result);

    }

    /* test whether weekend not full day*/

    public function testIsWeekendNotFullDay() {

        $result	=	$this->workWeekDao->isWeekend('2010-08-15', false);
        $this->assertFalse($result);

    }

    /* Tests for getWorkWeekList() */

    public function testGetWorkWeekListType() {

        $daysList = $this->workWeekDao->getWorkWeekList();

        foreach ($daysList as $day) {
            $this->assertTrue($day instanceof WorkWeek);
        }

    }

    /* Tests for counts getWorkWeekList*/

    public function testGetWorkWeekListCount() {

        $daysList = $this->workWeekDao->getWorkWeekList();

        $this->assertEquals(7, count($daysList));

    }

    public function testGetWorkWeekListValuesAndOrder() {

        $daysList = $this->workWeekDao->getWorkWeekList();

        $this->assertEquals(1, $daysList[0]->getDay());
        $this->assertEquals(0, $daysList[0]->getLength());

        $this->assertEquals(3, $daysList[2]->getDay());
        $this->assertEquals(8, $daysList[2]->getLength());

        $this->assertEquals(6, $daysList[5]->getDay());
        $this->assertEquals(4, $daysList[5]->getLength());

    }

    /* Tests for saveWorkWeek */

    public function testSaveWorkWeek() {

        $day     = 2;
        $length  = 8;

        $workWeek = TestDataService::fetchObject('WorkWeek', $day);
        $workWeek->setLength($length);

        $this->workWeekDao->saveWorkWeek($workWeek);
        $savedWorkWeek = TestDataService::fetchObject('WorkWeek', $day);
        $this->assertEquals($length, $savedWorkWeek->getLength());

    }

    /**
     * @expectedException DaoException
     */
    public function testSaveWorkWeekException() {

        $workWeek = $this->getMock('WorkWeek', array('save'));
        $workWeek->expects($this->once())
                ->method('save')
                ->will($this->throwException(new Exception()));
        
        $this->workWeekDao->saveWorkWeek($workWeek);

    }

    /* Tests for readWorkWeek */

    public function testReadWorkWeek() {

        $workWeek = $this->workWeekDao->readWorkWeek($this->testCases['WorkWeek'][0]['day']);

        $this->assertTrue($workWeek instanceof WorkWeek);
        $this->assertEquals($this->testCases['WorkWeek'][0]['length'], $workWeek->getLength());

    }

    /* Tests for deleteWorkWeek */

    public function testDeleteWorkWeek() {

        $this->assertTrue($this->workWeekDao->deleteWorkWeek(array(7)));

        $this->assertFalse(TestDataService::fetchObject('WorkWeek', 7) instanceof WorkWeek);

    }

}
