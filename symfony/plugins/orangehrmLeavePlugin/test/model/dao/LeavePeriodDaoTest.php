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

/**
 * Test for LeavePeriodDao class
 * 
 * @group Leave 
 */
class LeavePeriodDaoTest extends PHPUnit_Framework_TestCase {

    public $leavePeriodDao;

    protected function setUp() {

        $this->leavePeriodDao = new LeavePeriodDao();
        TestDataService::populate(sfConfig::get('sf_plugins_dir') . '/orangehrmLeavePlugin/test/fixtures/LeavePeriodDao.yml');

    }

    /* test saveLeavePeriod with Id */

    public function testSaveLeavePeriod() {

        $leavePeriod = TestDataService::fetchObject('LeavePeriod', 1);

        $leavePeriod->setStartDate("2008-01-31");
        $leavePeriod->setEndDate("2009-01-31");

        $this->assertTrue($this->leavePeriodDao->saveLeavePeriod($leavePeriod));
        $savedLeavePeriod = TestDataService::fetchObject('LeavePeriod', 1);

        $this->assertEquals($leavePeriod->getStartDate(), $savedLeavePeriod->getStartDate());
        $this->assertEquals($leavePeriod->getEndDate(), $savedLeavePeriod->getEndDate());

    }

    /* test saveLeavePeriod with no Id */

    public function testSaveLeavePeriodWithNoId() {
        TestDataService::truncateTables(array('LeavePeriod'));
        
        $leavePeriod = new LeavePeriod();

        $leavePeriod->setStartDate("2010-01-31");
        $leavePeriod->setEndDate("2011-01-31");

        $this->assertTrue($this->leavePeriodDao->saveLeavePeriod($leavePeriod));
        $savedLeavePeriod = TestDataService::fetchObject('LeavePeriod', 1);

        $this->assertEquals($leavePeriod->getLeavePeriodId(), $savedLeavePeriod->getLeavePeriodId());
        $this->assertEquals($leavePeriod->getStartDate(), $savedLeavePeriod->getStartDate());
        $this->assertEquals($leavePeriod->getEndDate(), $savedLeavePeriod->getEndDate());

    }

    /* test FilterByTimestamp */

    public function testFilterByTimestamp() {

        $timestamp = strtotime('2008-10-12');
        $leavePeriod = $this->leavePeriodDao->filterByTimestamp($timestamp);

        $this->assertTrue($leavePeriod instanceof LeavePeriod);
        $this->assertEquals('2008-02-01', $leavePeriod->getStartDate());
        $this->assertEquals('2009-01-31', $leavePeriod->getEndDate());

    }

    /* test FilterByTimestamp Returns null */

    public function testFilterByTimestampReturnsNull() {

        $timestamp = strtotime('6000-10-12');
        $leavePeriod = $this->leavePeriodDao->filterByTimestamp($timestamp);

        $this->assertFalse($leavePeriod instanceof LeavePeriod);
        $this->assertTrue(is_null($leavePeriod));

    }

    /* test FindLastLeavePeriod */

    public function testFindLastLeavePeriod() {

        $leavePeriod = TestDataService::fetchObject('LeavePeriod', 2);

        $lastLeavePeriod = $this->leavePeriodDao->findLastLeavePeriod($leavePeriod->getStartDate());
        $this->assertTrue($lastLeavePeriod instanceof LeavePeriod);
        $this->assertEquals(1, $lastLeavePeriod->getLeavePeriodId());

    }

    /* test FindLastLeavePeriod returns null */

    public function testFindLastLeavePeriodReturnsNull() {

        $leavePeriod = TestDataService::fetchObject('LeavePeriod', 1);
        $lastLeavePeriod = $this->leavePeriodDao->findLastLeavePeriod($leavePeriod->getStartDate());

        $this->assertFalse($lastLeavePeriod instanceof LeavePeriod);
        $this->assertTrue(is_null($lastLeavePeriod));

    }

    /* test getLeavePeriodList */

    public function testGetLeavePeriodList() {

        $leavePeriods = $this->leavePeriodDao->getLeavePeriodList();
        foreach($leavePeriods as $leavePeriod) {

            $this->assertTrue($leavePeriod instanceof LeavePeriod);

        }

    }

    /* test getLeavePeriodList Count and Order */

    public function testGetLeavePeriodListCountAndOrder() {

        $leavePeriods = $this->leavePeriodDao->getLeavePeriodList();
        $this->assertEquals(3, count($leavePeriods));

        $this->assertEquals(1, $leavePeriods[0]->getLeavePeriodId());
        $this->assertEquals(2, $leavePeriods[1]->getLeavePeriodId());
        $this->assertEquals(3, $leavePeriods[2]->getLeavePeriodId());

    }

    /* test readLeavePeriod */

    public function testReadLeavePeriod1() {

        $leavePeriod = TestDataService::fetchObject('LeavePeriod', 3);

        $readLeavePeriod = $this->leavePeriodDao->readLeavePeriod(3);

        $this->assertEquals($leavePeriod->getStartDate(), $readLeavePeriod->getStartDate());
        $this->assertEquals($leavePeriod->getEndDate(), $readLeavePeriod->getEndDate());

    }

    /* test SaveLeavePeriod Exception throwing */
    
    public function testSaveLeavePeriodException() {
        $leavePeriod = TestDataService::fetchObject('LeavePeriod', 1);

        $leavePeriod->setStartDate("2008-01-31");
        $leavePeriod->setEndDate("2009-01-31");
        $leavePeriod->setLeavePeriodId("california");
        
        try {
            $this->leavePeriodDao->saveLeavePeriod($leavePeriod);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof DaoException);
        }
    }
}
?>