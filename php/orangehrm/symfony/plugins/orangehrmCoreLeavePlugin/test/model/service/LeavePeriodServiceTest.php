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
 * Leave period service test
 */
class LeavePeriodServiceTest extends PHPUnit_Framework_TestCase {

    private $leavePeriodService;
   private $fixture;

    protected function setUp() {

        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmCoreLeavePlugin/test/fixtures/LeavePeriodService.yml';
        $this->leavePeriodService = new LeavePeriodService();
      
    }

   /* Test get list of months */

    public function testGetListOfMonths() {
      
        $expected = array ('January','February','March','April','May','June','July','August','September',
            'October', 'November', 'December');
      
      $result = $this->leavePeriodService->getListOfMonths();
      $this->assertEquals($expected, $result);
      
    }

    
    /* Test get list of dates, given the month */
   
    public function testGetListOfDates() {
      
        /* Checking for days with 31 days */
        $expected = range(1, 31);
        $result = $this->leavePeriodService->getListOfDates(1); // January
        $this->assertEquals($expected, $result, 'Wrong date range fetched for January');
        $result = $this->leavePeriodService->getListOfDates(3); // March
        $this->assertEquals($expected, $result, 'Wrong date range fetched for March');
        $result = $this->leavePeriodService->getListOfDates(5); // May
        $this->assertEquals($expected, $result, 'Wrong date range fetched for May');
        $result = $this->leavePeriodService->getListOfDates(7); // July
        $this->assertEquals($expected, $result, 'Wrong date range fetched for July');
        $result = $this->leavePeriodService->getListOfDates(8); // August
        $this->assertEquals($expected, $result, 'Wrong date range fetched for August');
        $result = $this->leavePeriodService->getListOfDates(10); // October
        $this->assertEquals($expected, $result, 'Wrong date range fetched for October');
        $result = $this->leavePeriodService->getListOfDates(12); // December
        $this->assertEquals($expected, $result, 'Wrong date range fetched for December');

        /* Checking for days with 30 days */
        $expected = range(1, 30);
        $result = $this->leavePeriodService->getListOfDates(4); // April
        $this->assertEquals($expected, $result, 'Wrong date range fetched for April');
        $result = $this->leavePeriodService->getListOfDates(6); // June
        $this->assertEquals($expected, $result, 'Wrong date range fetched for June');
        $result = $this->leavePeriodService->getListOfDates(9); // September
        $this->assertEquals($expected, $result, 'Wrong date range fetched for September');
        $result = $this->leavePeriodService->getListOfDates(11); // November
        $this->assertEquals($expected, $result, 'Wrong date range fetched for November');

        /* Checking for February; Should return maximum 29 days */
        $expected = range(1, 29);
        $result = $this->leavePeriodService->getListOfDates(2);
        $this->assertEquals($expected, $result, 'Wrong date range fetched for February');

        /* Checking for February; Should return maximum 28 days if $isLeapYear parameter is false */
        $expected = range(1, 29);
        $result = $this->leavePeriodService->getListOfDates(2, true);
        $this->assertEquals($expected, $result, 'Wrong date range fetched for February for leap years');

        $expected = range(1, 28);
        $result = $this->leavePeriodService->getListOfDates(2, false);
        $this->assertEquals($expected, $result, 'Wrong date range fetched for February non leap years');



        /* Checking for invalid month values */
        try {
            $this->leavePeriodService->getListOfDates(-1);
            $this->fail('getListOfDates() should not accept invalid month values');
        } catch (Exception $e) {
            $this->assertTrue($e instanceof LeaveServiceException);
            $this->assertEquals('Invalid value passed for month in LeavePeriodService::getListOfDates()', $e->getMessage());
        }

        try {
            $this->leavePeriodService->getListOfDates(13);
            $this->fail('getListOfDates() should not accept invalid month values');
        } catch (Exception $e) {
            $this->assertTrue($e instanceof LeaveServiceException);
            $this->assertEquals('Invalid value passed for month in LeavePeriodService::getListOfDates()', $e->getMessage());
        }
        /* Checking for non numeric values */
        try {
            $this->leavePeriodService->getListOfDates('abcd asdf');
            $this->fail('getListOfDates() should not accept non-numeric values');
        } catch (Exception $e) {
            $this->assertTrue($e instanceof LeaveServiceException);
            $this->assertEquals('Invalid value passed for month in LeavePeriodService::getListOfDates()', $e->getMessage());
        }

    }

    
    /* Test for calcualating end date of the leave period, when given the start date */
   
    public function testCalculateEndDate() {

        $result = $this->leavePeriodService->calculateEndDate(1, 1, null, 'F d');
        $this->assertEquals('December 31', $result);

        $result = $this->leavePeriodService->calculateEndDate(1, 1, 1999, 'F d');
        $this->assertEquals('December 31', $result);

        /* Test for leap years */
        $result = $this->leavePeriodService->calculateEndDate(1, 1, 2004, 'F d');
        $this->assertEquals('December 31', $result);

        $result = $this->leavePeriodService->calculateEndDate(2, 28, 2004, 'F d');
        $this->assertEquals('February 27', $result);

        $result = $this->leavePeriodService->calculateEndDate(2, 29, 2004, 'F d');
        $this->assertEquals('February 28', $result);

        /* Test for format */
        $result = $this->leavePeriodService->calculateEndDate(1, 1, 1999, 'F d');
        $this->assertEquals('December 31', $result);

        $result = $this->leavePeriodService->calculateEndDate(1, 1, 1999, 'Y-m-d');
        $this->assertEquals('1999-12-31', $result);

        $result = $this->leavePeriodService->calculateEndDate(1, 1, 1999, 'm/d/Y');
        $this->assertEquals('12/31/1999', $result);

        $result = $this->leavePeriodService->calculateEndDate(1, 1, 1999, 'd.m.Y');
        $this->assertEquals('31.12.1999', $result);

        /* Test for days other than Ja1 1st
         * (End date should always in next year) */
        
        $currentYear = date('Y');
        $nextYear = date('Y')+1;
        
        $result = $this->leavePeriodService->calculateEndDate(12, 22);
        $this->assertEquals("$nextYear-12-21", $result);
        
        $result = $this->leavePeriodService->calculateEndDate(12, 22, $currentYear);
        $this->assertEquals("{$nextYear}-12-21", $result);

        /* Test for Ja1 1st
         * (End date should be same year Dec 31) */

        $currentYear = (int) date('Y');

        $result = $this->leavePeriodService->calculateEndDate(01, 01);
        $this->assertEquals("$currentYear-12-31", $result);

        $result = $this->leavePeriodService->calculateEndDate(01, 01, $currentYear);
        $this->assertEquals("$currentYear-12-31", $result);
      
    }

    
    /* Test for calculating start date given the month, date and year */
   
    public function testCalculateStartDate() {
      
        $currentYear = (int) date('Y'); // TODO: Remove this dependancy on getting the system date by using a mock
        $previousYear = $currentYear - 1;

        $result = $this->leavePeriodService->calculateStartDate(1, 1);
        $this->assertEquals("{$currentYear}-01-01", $result);

        $result = $this->leavePeriodService->calculateStartDate(8, 1, 2006);
        $this->assertEquals("2006-08-01", $result);

        $currentYear = date('Y');

        $result = $this->leavePeriodService->calculateStartDate(05, 12);
        $exprected = $currentYear . "-05-12";
        $this->assertEquals($exprected, $result);

        $result = $this->leavePeriodService->calculateStartDate(05, 12, $currentYear);
        $exprected = $currentYear . "-05-12";
        $this->assertEquals($exprected, $result);
      
    }

    /* Test for savng leave period */

    public function testSaveLeavePeriod() {

        $leavePeriods  = TestDataService::loadObjectList('LeavePeriod', $this->fixture, 'LeavePeriod');
        $leavePeriod   = $leavePeriods[0];

        $leavePeriodDao = $this->getMock('LeavePeriodDao', array('saveLeavePeriod'));
        $leavePeriodDao->expects($this->once())
                     ->method('saveLeavePeriod')
                     ->with($leavePeriod)
                     ->will($this->returnValue(true));

        $this->leavePeriodService->setLeavePeriodDao($leavePeriodDao);

        $this->assertTrue($this->leavePeriodService->saveLeavePeriod($leavePeriod));

    }
    
    /* test GetLeavePeriod */

    public function testGetLeavePeriod() {

        $leavePeriods  = TestDataService::loadObjectList('LeavePeriod', $this->fixture, 'LeavePeriod');
        $leavePeriod   = $leavePeriods[0];
        $timestamp     = strtotime('2008-02-01');

        $leavePeriodDao = $this->getMock('LeavePeriodDao', array('filterByTimestamp'));
        $leavePeriodDao->expects($this->once())
                         ->method('filterByTimestamp')
                         ->with($timestamp)
                         ->will($this->returnValue($leavePeriod));

        $this->leavePeriodService->setLeavePeriodDao($leavePeriodDao);

        $returnedLeavePeriod = $this->leavePeriodService->getLeavePeriod($timestamp);

        $this->assertTrue($returnedLeavePeriod instanceof LeavePeriod);
        $this->assertEquals($leavePeriod, $returnedLeavePeriod);

    }
    
    /* test getNextLeavePeriodByCurrentEndDate */

    public function testGetNextLeavePeriodByCurrentEndDate() {

        $leavePeriods  = TestDataService::loadObjectList('LeavePeriod', $this->fixture, 'LeavePeriod');
        $timestamp     = strtotime('+2 day', strtotime('2009-01-31'));

        $leavePeriodDao = $this->getMock('LeavePeriodDao', array('filterByTimestamp'));
        $leavePeriodDao->expects($this->once())
                        ->method('filterByTimestamp')
                        ->will($this->returnValue($leavePeriods[1]));

        $this->leavePeriodService->setLeavePeriodDao($leavePeriodDao);

        $returnedLeavePeriod = $this->leavePeriodService->getNextLeavePeriodByCurrentEndDate("2009-01-31");

        $this->assertTrue($returnedLeavePeriod instanceof LeavePeriod);
        $this->assertEquals($leavePeriods[1], $returnedLeavePeriod);

    }

    /* test getNextLeavePeriodByCurrentEndDate returns null */

    public function testGetNextLeavePeriodByCurrentEndDateReturnsNull() {

        $leavePeriods  = TestDataService::loadObjectList('LeavePeriod', $this->fixture, 'LeavePeriod');
        $timestamp     = strtotime('5500-11-12');

        $leavePeriodDao = $this->getMock('LeavePeriodDao', array('filterByTimestamp'));
        $leavePeriodDao->expects($this->once())
                        ->method('filterByTimestamp')
                        ->will($this->returnValue(null));

        $this->leavePeriodService->setLeavePeriodDao($leavePeriodDao);

        $returnedLeavePeriod = $this->leavePeriodService->getNextLeavePeriodByCurrentEndDate("5500-11-12");

        $this->assertFalse($returnedLeavePeriod instanceof LeavePeriod);
        $this->assertTrue(is_null($returnedLeavePeriod));

    }
    
    
    /* Test for adjustCurrentLeavePeriod */

    public function testAdjustCurrentLeavePeriod() {

        $leavePeriods  = TestDataService::loadObjectList('LeavePeriod', $this->fixture, 'LeavePeriod');
        $leavePeriodDao = $this->getMock('LeavePeriodDao', array('filterByTimestamp', 'saveLeavePeriod', 'readLeavePeriod'));

        $leavePeriodDao->expects($this->any())
                        ->method('filterByTimestamp')
                        ->will($this->returnValue($leavePeriods[1]));

        $leavePeriodDao->expects($this->any())
                        ->method('readLeavePeriod')
                        ->will($this->returnValue($leavePeriods[1]));

        $leavePeriodDao->expects($this->any())
                        ->method('saveLeavePeriod')
                        ->will($this->returnValue(true));

        $this->leavePeriodService->setLeavePeriodDao($leavePeriodDao);

        $this->assertTrue($this->leavePeriodService->adjustCurrentLeavePeriod(date("Y") . '-01-30'));

    }

    /* test createNextLeavePeriod */

    public function testCreateNextLeavePeriodAlreadyCreated() {

        $leavePeriods  = TestDataService::loadObjectList('LeavePeriod', $this->fixture, 'LeavePeriod');

        $leavePeriodDao = $this->getMock('LeavePeriodDao', array('findLastLeavePeriod', 'filterByTimestamp'));
        $leavePeriodDao->expects($this->once())
                        ->method('findLastLeavePeriod')
                        ->with('2010-01-30')
                        ->will($this->returnValue($leavePeriods[1]));

        $timestamp = strtotime('+2 day', strtotime('2010-01-31'));
        $leavePeriodDao->expects($this->once())
                        ->method('filterByTimestamp')
                        ->with($timestamp)
                        ->will($this->returnValue($leavePeriods[2]));

        $this->leavePeriodService->setLeavePeriodDao($leavePeriodDao);
        $leavePeriod = $this->leavePeriodService->createNextLeavePeriod("2010-01-30");

        $this->assertTrue($leavePeriod instanceof LeavePeriod);
        $this->assertEquals($leavePeriods[2]->getStartDate(), $leavePeriod->getStartDate());
        $this->assertEquals($leavePeriods[2]->getEndDate(), $leavePeriod->getEndDate());

    }

    /* test createNextLeavePeriod not already created */

    public function testCreateNextLeavePeriodNotAlreadyCreated() {

        $paramLeavePeriodStartDate = ParameterService::getParameter('leavePeriodStartDate');
        ParameterService::setParameter('leavePeriodStartDate', '02-01');
        ParameterService::setParameter('isLeavePeriodStartOnFeb29th', "No");
        ParameterService::setParameter('nonLeapYearLeavePeriodStartDate', "");

        $leavePeriods  = TestDataService::loadObjectList('LeavePeriod', $this->fixture, 'LeavePeriod');

        $leavePeriodDao = $this->getMock('LeavePeriodDao', array('findLastLeavePeriod', 'filterByTimestamp', 'saveLeavePeriod'));
        $leavePeriodDao->expects($this->once())
                        ->method('findLastLeavePeriod')
                        ->with('2010-01-30')
                        ->will($this->returnValue($leavePeriods[1]));

        $timestamp = strtotime('+2 day', strtotime('2010-01-31'));
        $leavePeriodDao->expects($this->once())
                        ->method('filterByTimestamp')
                        ->with($timestamp)
                        ->will($this->returnValue(null));

        $leavePeriodDao->expects($this->once())
                        ->method('saveLeavePeriod')
                        ->will($this->returnValue(true));

        $this->leavePeriodService->setLeavePeriodDao($leavePeriodDao);
        $leavePeriod = $this->leavePeriodService->createNextLeavePeriod("2010-01-30");

        $this->assertTrue($leavePeriod instanceof LeavePeriod);
        $this->assertEquals($leavePeriods[2]->getStartDate(), $leavePeriod->getStartDate());
        $this->assertEquals($leavePeriods[2]->getEndDate(), $leavePeriod->getEndDate());

        ParameterService::setParameter('leavePeriodStartDate', $paramLeavePeriodStartDate);

    }

    /* test createNextLeavePeriod returns null */

    public function testCreateNextLeavePeriodReturnsNull() {

        $leavePeriodDao = $this->getMock('LeavePeriodDao', array('findLastLeavePeriod'));
        $leavePeriodDao->expects($this->once())
                        ->method('findLastLeavePeriod')
                        ->with('1000-10-10')
                        ->will($this->returnValue(null));

        $this->leavePeriodService->setLeavePeriodDao($leavePeriodDao);
        $leavePeriod = $this->leavePeriodService->createNextLeavePeriod('1000-10-10');

        $this->assertFalse($leavePeriod instanceof LeavePeriod);
        $this->assertTrue(is_null($leavePeriod));

    }

    /* test getLeavePeriodList */
   
    public function testGetLeavePeriodList() {

        $leavePeriods  = TestDataService::loadObjectList('LeavePeriod', $this->fixture, 'LeavePeriod');

        $leavePeriodDao = $this->getMock('LeavePeriodDao', array('getLeavePeriodList'));

        $leavePeriodDao->expects($this->once())
                        ->method('getLeavePeriodList')
                        ->will($this->returnValue($leavePeriods));

        $this->leavePeriodService->setLeavePeriodDao($leavePeriodDao);

        $list = $this->leavePeriodService->getLeavePeriodList();
        $this->assertEquals(4, count($list));

        foreach($list as $leavePeriod) {
            $this->assertTrue($leavePeriod instanceof LeavePeriod);
        }

    }

    /* test isWithinNextLeavePeriod */
   
    public function testIsWithinNextLeavePeriod() {

        $leavePeriods  = TestDataService::loadObjectList('LeavePeriod', $this->fixture, 'LeavePeriod');

        $leavePeriodDao = $this->getMock('LeavePeriodDao', array('filterByTimestamp'));

        $leavePeriodDao->expects($this->any())
                        ->method('filterByTimestamp')
                        ->will($this->returnValue($leavePeriods[1]));

        $timestamp = strtotime(date("Y") . "-05-11");
        $results = array(true, false);

        $this->leavePeriodService->setLeavePeriodDao($leavePeriodDao);
        $result = $this->leavePeriodService->isWithinNextLeavePeriod($timestamp);

        $this->assertTrue(in_array($result, $results));

    }

    /* test eager loading of LeavePeriodDao */
    public function testEagerLoadingLeavePeriodDao() {
        $leavePeriodDao = $this->leavePeriodService->getLeavePeriodDao();
        $this->assertTrue($leavePeriodDao instanceof LeavePeriodDao);
    }

    /* test getCurrentLeavePeriod */
    
    public function testGetCurrentLeavePeriod1() {
        //mocking LeavePeriodDao
        $leavePeriod = new LeavePeriod();
        $leavePeriod->setStartDate("2010-01-01");
        $leavePeriod->setEndDate("2010-12-31");
        $leavePeriod->setLeavePeriodId(1);
        $leavePeriodDao = $this->getMock('LeavePeriodDao', array('filterByTimestamp'));
        $leavePeriodDao->expects($this->once())
                ->method('filterByTimestamp')
                ->will($this->returnValue($leavePeriod));
        $this->leavePeriodService->setLeavePeriodDao($leavePeriodDao);

        $retrievedLeavePeriod = $this->leavePeriodService->getCurrentLeavePeriod();
        $this->assertTrue($retrievedLeavePeriod instanceof LeavePeriod);
        $this->assertEquals($retrievedLeavePeriod, $leavePeriod);
    }

    /* test getCurrentLeavePeriod */

    public function testGetCurrentLeavePeriod2() {
        //mocking LeavePeriodDao
        $lastLeavePeriod = new LeavePeriod();
        $lastLeavePeriod->setStartDate("2010-01-01");
        $lastLeavePeriod->setEndDate("2010-12-31");
        $lastLeavePeriod->setLeavePeriodId(1);

        $leavePeriodDao = $this->getMock('LeavePeriodDao', array('filterByTimestamp', 'findLastLeavePeriod', 'saveLeavePeriod'));
        $leavePeriodDao->expects($this->any())
                ->method('filterByTimestamp')
                ->will($this->returnValue(null));

        $leavePeriodDao->expects($this->once())
                ->method('findLastLeavePeriod')
                ->will($this->returnValue($lastLeavePeriod));

        $leavePeriodDao->expects($this->once())
                ->method('saveLeavePeriod')
                ->will($this->returnValue(true));

        $this->leavePeriodService->setLeavePeriodDao($leavePeriodDao);

        $retrievedLeavePeriod = $this->leavePeriodService->getCurrentLeavePeriod();
        $this->assertTrue($retrievedLeavePeriod instanceof LeavePeriod);
    }
}
?>
