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
 * @group Leave 
 */
class LeavePeriodHistoryServiceTest extends PHPUnit_Framework_TestCase {

    private $leavePeriodService;
    private $fixture;

    protected function setUp() {

        $this->leavePeriodService = new LeavePeriodService();
        $leaveEntitlementService = new LeaveEntitlementService();
        $leaveEntitlementService->setLeaveEntitlementStrategy(new FIFOEntitlementConsumptionStrategy());
        $this->leavePeriodService->setLeaveEntitlementService($leaveEntitlementService);
        
        TestDataService::truncateTables(array('LeavePeriodHistory'));
    }
    /**
     * @expectedException ServiceException
     */
    public function testGetGeneratedLeavePeriodListDateIsNotSet(){

        $result = $this->leavePeriodService->getGeneratedLeavePeriodList(null, true);
       
    }
    
    public function testGetGeneratedLeavePeriodListDefineAs2012Jan1st(){
        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setLeavePeriodStartMonth(1);
        $leavePeriodHistory->setLeavePeriodStartDay(1);
        $leavePeriodHistory->setCreatedAt('2010-01-02');
        
        $this->leavePeriodService->saveLeavePeriodHistory( $leavePeriodHistory );
        
        
        $result = $this->leavePeriodService->getGeneratedLeavePeriodList(null, true);
        
        $expected = array(
            array('2010-01-01','2010-12-31'),
            array('2011-01-01','2011-12-31'),
            array('2012-01-01','2012-12-31'),
            array('2013-01-01','2013-12-31'));
        
        // extend range till next year end:
        $now = new DateTime();
        
        $nextYear = $now->format('Y') + 1;
        $this->assertTrue($nextYear > 2012, 'System clock set to past!. Test should be run with system date 2012 or later.');
        
        if ($nextYear > 2013) {
            for ($year = 2014; $year <= $nextYear; $year++) {
                $expected[] = array($year . '-01-01', $year . '-12-31');
            }
        }
        
        $this->assertEquals($expected, $result);
        
        
    }
    
     public function testGetGeneratedLeavePeriodListDefineAs2010Jan1st(){
        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setLeavePeriodStartMonth(1);
        $leavePeriodHistory->setLeavePeriodStartDay(1);
        $leavePeriodHistory->setCreatedAt('2010-01-02');
        
        $this->leavePeriodService->saveLeavePeriodHistory( $leavePeriodHistory );
        
        $expected = array(
            array('2010-01-01','2010-12-31'),
            array('2011-01-01','2011-12-31'),
            array('2012-01-01','2012-12-31'),
            array('2013-01-01','2013-12-31'));
        
        // extend range till next year end:
        $now = new DateTime();
        
        $nextYear = $now->format('Y') + 1;
        $this->assertTrue($nextYear > 2012, 'System clock set to past!. Test should be run with system date 2012 or later.');
        
        if ($nextYear > 2013) {
            for ($year = 2014; $year <= $nextYear; $year++) {
                $expected[] = array($year . '-01-01', $year . '-12-31');
            }
        }
        
        $result = $this->leavePeriodService->getGeneratedLeavePeriodList(null, true);
        $this->assertEquals($expected, $result);
        
        
    }

     /* Fails if run in 2014 */
     public function testGetGeneratedLeavePeriodListForLeapYear(){
        $thisYear = date('Y');
        
        $startYear = $thisYear - 3;
        $nextYear = $thisYear + 1;
        
        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setLeavePeriodStartMonth(3);
        $leavePeriodHistory->setLeavePeriodStartDay(1);
        $leavePeriodHistory->setCreatedAt($startYear . '-01-02');
        
        $this->leavePeriodService->saveLeavePeriodHistory( $leavePeriodHistory );
        
        $expected = array();
        
        for ($year = $startYear - 1; $year <= $nextYear; $year++) {
            $expected[] = array($year . '-03-01', $this->getLastDayInFebruary($year + 1));
        }
        
        $result = $this->leavePeriodService->getGeneratedLeavePeriodList(null, true);
        $this->assertEquals($expected, $result);                
    }
    
    public function testGetGeneratedLeavePeriodListDefineAs2010Jan1stAnd2012Jan1st(){
        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setLeavePeriodStartMonth(1);
        $leavePeriodHistory->setLeavePeriodStartDay(1);
        $leavePeriodHistory->setCreatedAt('2010-10-02');
        
        $this->leavePeriodService->saveLeavePeriodHistory( $leavePeriodHistory );
        
        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setLeavePeriodStartMonth(1);
        $leavePeriodHistory->setLeavePeriodStartDay(1);
        $leavePeriodHistory->setCreatedAt('2011-08-04');
        
        $this->leavePeriodService->saveLeavePeriodHistory( $leavePeriodHistory );
        
        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setLeavePeriodStartMonth(1);
        $leavePeriodHistory->setLeavePeriodStartDay(1);
        $leavePeriodHistory->setCreatedAt('2012-08-02');
        
        $this->leavePeriodService->saveLeavePeriodHistory( $leavePeriodHistory );
        
        
        $result = $this->leavePeriodService->getGeneratedLeavePeriodList(null, true);
        
        $expected = array(array('2010-01-01','2010-12-31'),
                          array('2011-01-01','2011-12-31'),
                          array('2012-01-01','2012-12-31'),
                          array('2013-01-01','2013-12-31'));
        
        // extend range till next year end:
        $now = new DateTime();
        
        $nextYear = $now->format('Y') + 1;
        $this->assertTrue($nextYear > 2012, 'System clock set to past!. Test should be run with system date 2012 or later.');
        
        if ($nextYear > 2013) {
            for ($year = 2014; $year <= $nextYear; $year++) {
                $expected[] = array($year . '-01-01', $year . '-12-31');
            }
        }
        
        $this->assertEquals($expected,$result);        
        
    }
    
    /* Fails if run in 2014 */
     public function testGetGeneratedLeavePeriodListCase1(){
        
        $thisYear = date('Y');
        $expected = array();
        
        $threeYearsAgo = $thisYear - 3;
        $twoYearsAgo = $thisYear - 2;
        $oneYearAgo = $thisYear - 1;
        $nextYear = $thisYear + 1;
        $nextNextYear = $thisYear + 2;
        
        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setLeavePeriodStartMonth(1);
        $leavePeriodHistory->setLeavePeriodStartDay(1);
        $leavePeriodHistory->setCreatedAt($threeYearsAgo . '-10-02');
        $expected[] = array($threeYearsAgo . '-01-01', $threeYearsAgo . '-12-31');
        
        $this->leavePeriodService->saveLeavePeriodHistory( $leavePeriodHistory );
        
        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setLeavePeriodStartMonth(2);
        $leavePeriodHistory->setLeavePeriodStartDay(1);
        $leavePeriodHistory->setCreatedAt($twoYearsAgo . '-08-04');
        $expected[] = array($twoYearsAgo . '-01-01', $oneYearAgo . '-01-31');
        
        $this->leavePeriodService->saveLeavePeriodHistory( $leavePeriodHistory );
        
        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setLeavePeriodStartMonth(3);
        $leavePeriodHistory->setLeavePeriodStartDay(1);
        $leavePeriodHistory->setCreatedAt($oneYearAgo . '-08-02');
        $expected[] = array($oneYearAgo . '-02-01', $this->getLastDayInFebruary($thisYear));
        
        $this->leavePeriodService->saveLeavePeriodHistory( $leavePeriodHistory );
        
        $expected[] = array($thisYear . '-03-01', $this->getLastDayInFebruary($nextYear));
        $expected[] = array($nextYear . '-03-01', $this->getLastDayInFebruary($nextNextYear));
        
        // work around for cached generated leave period list
        $newLeavePeriodService = new LeavePeriodService();
        $newLeavePeriodService->setLeaveEntitlementService($this->leavePeriodService->getLeaveEntitlementService());                
        $result= $newLeavePeriodService->getGeneratedLeavePeriodList(null, true);
        
        $this->assertEquals($expected, $result);
        
        
    }
    
    /**
     * Returns the last day of the given year in yyy-mm-dd format.
     * 
     * @param String $year Full year with 4 digits
     */
    protected function getLastDayInFebruary($year) {
        $lastDay =  date('L', strtotime("$year-01-01")) ? '29' : '28';
        return "{$year}-02-{$lastDay}"; 
    }
    
      public function testGetGeneratedLeavePeriodListCase2(){
        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setLeavePeriodStartMonth(2);
        $leavePeriodHistory->setLeavePeriodStartDay(1);
        $leavePeriodHistory->setCreatedAt('2010-01-01');
        
        $this->leavePeriodService->saveLeavePeriodHistory( $leavePeriodHistory );
        
        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setLeavePeriodStartMonth(1);
        $leavePeriodHistory->setLeavePeriodStartDay(1);
        $leavePeriodHistory->setCreatedAt('2010-01-02');
        
        $this->leavePeriodService->saveLeavePeriodHistory( $leavePeriodHistory );
        
        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setLeavePeriodStartMonth(1);
        $leavePeriodHistory->setLeavePeriodStartDay(2);
        $leavePeriodHistory->setCreatedAt('2010-01-02');
        
        $this->leavePeriodService->saveLeavePeriodHistory( $leavePeriodHistory );
        
        // work around for cached generated leave period list
        $newLeavePeriodService = new LeavePeriodService();
        $newLeavePeriodService->setLeaveEntitlementService($this->leavePeriodService->getLeaveEntitlementService());                
        $result= $newLeavePeriodService->getGeneratedLeavePeriodList(null, true);

        $expected = array(
            array('2009-02-01','2011-01-01'),
            array('2011-01-02','2012-01-01'),
            array('2012-01-02','2013-01-01'),
            array('2013-01-02','2014-01-01'));
        
        // extend range till next year end:
        $now = new DateTime();
        
        $nextYear = $now->format('Y') + 1;
        $this->assertTrue($nextYear > 2012, 'System clock set to past!. Test should be run with system date 2012 or later.');
        
        if ($nextYear > 2013) {
            for ($year = 2014; $year <= $nextYear; $year++) {
                $expected[] = array($year . '-01-02', ($year + 1) . '-01-01');
            }
        }        
        
        $this->assertEquals($expected, $result);
        
        
    }
    
    public function testGetCurrentLeavePeriodByDate( ){
        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setLeavePeriodStartMonth(1);
        $leavePeriodHistory->setLeavePeriodStartDay(1);
        $leavePeriodHistory->setCreatedAt('2010-01-02');
        
        $this->leavePeriodService->saveLeavePeriodHistory( $leavePeriodHistory );
        
        
        $result = $this->leavePeriodService->getCurrentLeavePeriodByDate('2012-01-01', true);
       
        $this->assertEquals(array('2012-01-01','2012-12-31'),$result);
        
         $result = $this->leavePeriodService->getCurrentLeavePeriodByDate('2013-01-04', true);
       
        $this->assertEquals(array('2013-01-01','2013-12-31'),$result);
     
    }
    
    
}
