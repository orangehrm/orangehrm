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
 * LeaveRequestDao Test
 * @group Leave 
 */
 class LeaveRequestDaoTest extends PHPUnit_Framework_TestCase{
 	
  	public $leaveRequestDao ;
  	public $leaveType ;
  	public $leavePeriod ;
  	public $employee ;
        private $fixture;
 	
 	protected function setUp() {

            $this->leaveRequestDao = new LeaveRequestDao();
            $this->leaveRequestDao->markApprovedLeaveAsTaken();
            $fixtureFile = sfConfig::get('sf_plugins_dir') . '/orangehrmLeavePlugin/test/fixtures/LeaveRequestDao.yml';
            TestDataService::populate($fixtureFile);
            $this->fixture = sfYaml::load($fixtureFile);
            sfConfig::set('app_items_per_page', 50);
           
    	
    }
    
    /* Tests for fetchLeaveRequest() */

    public function testFetchLeaveRequest() {

        $leaveRequest = $this->leaveRequestDao->fetchLeaveRequest(1);

        $this->assertTrue($leaveRequest instanceof LeaveRequest);

        $this->assertEquals(1, $leaveRequest->getLeaveTypeId());
        $this->assertEquals('Casual', $leaveRequest->getLeaveTypeName());
        $this->assertEquals('2010-08-30', $leaveRequest->getDateApplied());
        $this->assertEquals(1, $leaveRequest->getEmpNumber());

    }

    /* Tests for fetchLeave() */

    public function testFetchLeave() {

        $leaveList = $this->leaveRequestDao->fetchLeave(1);

        foreach ($leaveList as $leave) {
            $this->assertTrue($leave instanceof Leave);
        }

        $this->assertEquals(2, count($leaveList));

        $this->assertEquals(1, $leaveList[0]->getId());
        $this->assertEquals(1, $leaveList[0]->getLeaveTypeId());
        $this->assertEquals(1, $leaveList[0]->getEmpNumber());
        $this->assertEquals('2010-09-01', $leaveList[0]->getDate());
        $this->assertEquals(1, $leaveList[0]->getStatus());

        $this->assertEquals(2, $leaveList[1]->getId());
        $this->assertEquals(1, $leaveList[1]->getLeaveTypeId());
        $this->assertEquals(1, $leaveList[1]->getEmpNumber());
        $this->assertEquals('2010-09-02', $leaveList[1]->getDate());
        $this->assertEquals(1, $leaveList[1]->getStatus());

    }

    /* Tests for getLeaveById() */

    public function testGetLeaveById() {

        $leave = $this->leaveRequestDao->getLeaveById(1);

        $this->assertTrue($leave instanceof Leave);

        $this->assertEquals(1, $leave->getId());
        $this->assertEquals(8, $leave->getLengthHours());
        $this->assertEquals(1, $leave->getLengthDays());
        $this->assertEquals(1, $leave->getLeaveRequestId());
        $this->assertEquals(1, $leave->getLeaveTypeId());
        $this->assertEquals(1, $leave->getEmpNumber());
        $this->assertEquals('2010-09-01', $leave->getDate());
        $this->assertEquals(1, $leave->getStatus());

    }

    /* Tests for getNumOfLeave() */

    public function xtestGetNumOfLeave() {

        $this->assertEquals(8.75, $this->leaveRequestDao->getNumOfLeave(1, 'LTY001'));
        $this->assertNull($this->leaveRequestDao->getNumOfLeave(1, 'LTY100'));

    }

    /* Tests for getLeaveRecordCount() */

    public function xtestGetLeaveRecordCount() {

        $this->assertEquals(35, $this->leaveRequestDao->getLeaveRecordCount());

    }

    /* Tests for getNumOfAvaliableLeave() */

    public function xtestGetNumOfAvaliableLeave() {

        $this->assertEquals(4, $this->leaveRequestDao->getNumOfAvaliableLeave(1, 'LTY002'));
        $this->assertEquals(2, $this->leaveRequestDao->getNumOfAvaliableLeave(2, 'LTY001'));

    }

    /* Tests for getScheduledLeavesSum() */

    public function testGetScheduledLeavesSum() {

        $this->assertEquals(2.75, $this->leaveRequestDao->getScheduledLeavesSum(1, 1, 1));
        $this->assertEquals(1, $this->leaveRequestDao->getScheduledLeavesSum(2, 2, 1));

    }

    /* Tests for getTakenLeaveSum() */

    public function testGetTakenLeaveSum() {
        $this->assertEquals(2, $this->leaveRequestDao->getTakenLeaveSum(5, 2, 1));

    }

    /* Tests for getLeavePeriodOverlapLeaves() */

    public function xtestGetLeavePeriodOverlapLeaves() {

        $leavePeriod = TestDataService::fetchObject('LeavePeriod', 1);
        $leaveList = $this->leaveRequestDao->getLeavePeriodOverlapLeaves($leavePeriod);

        foreach ($leaveList as $leave) {
            $this->assertTrue($leave instanceof Leave);
        }

        /* Because of groupBy only first leave (2011-02-07) is
         * returned instead of both (2011-02-07, 2011-02-08)
         */
        $this->assertEquals(2, count($leaveList));

    }

    /* Tests for getOverlappingLeave() */

    public function testGetOverlappingLeaveMultipleFullDayLeave() {

       $leaveList = $this->leaveRequestDao->getOverlappingLeave('2010-01-01', '2010-12-31', 1);

        foreach ($leaveList as $leave) {
            $this->assertTrue($leave instanceof Leave);
        }

        $this->assertEquals(11, count($leaveList));

        $this->assertEquals(1, $leaveList[0]->getId());
        $this->assertEquals(18, $leaveList[10]->getId());

    }
    
    public function testGetOverlappingLeaveInSameDay1() {

       $leaveList = $this->leaveRequestDao->getOverlappingLeave('2011-01-01', '2011-01-01', 6,'11:00:00','12:00:00' );

        foreach ($leaveList as $leave) {
            $this->assertTrue($leave instanceof Leave);
        }
        $this->assertEquals(1, count($leaveList));
    }
    
    public function testGetOverlappingLeaveInSameDay2() {

       $leaveList = $this->leaveRequestDao->getOverlappingLeave('2011-01-01', '2011-01-01', 6,'10:00:00','11:00:00' );

        foreach ($leaveList as $leave) {
            $this->assertTrue($leave instanceof Leave);
        }
        $this->assertEquals(1, count($leaveList));
    }
    
    public function testGetOverlappingLeaveInSameDay3() {

       $leaveList = $this->leaveRequestDao->getOverlappingLeave('2011-01-01', '2011-01-01', 6,'10:00:00','12:00:00' );

        foreach ($leaveList as $leave) {
            $this->assertTrue($leave instanceof Leave);
        }
        $this->assertEquals(2, count($leaveList));
    }
    
    public function testGetOverlappingLeaveInSameDay4() {

       $leaveList = $this->leaveRequestDao->getOverlappingLeave('2011-01-01', '2011-01-01', 6,'10:00:00','12:00:00' );

        foreach ($leaveList as $leave) {
            $this->assertTrue($leave instanceof Leave);
        }
        $this->assertEquals(2, count($leaveList));
    }
    
    public function testGetOverlappingLeaveInSameDay5() {

       $leaveList = $this->leaveRequestDao->getOverlappingLeave('2011-01-01', '2011-01-01', 6,'12:00:00','13:00:00' );
       $this->assertEquals(0, count($leaveList));
    }
    
    public function testGetOverlappingLeaveInSameDay6() {

       $leaveList = $this->leaveRequestDao->getOverlappingLeave('2011-01-01', '2011-01-01', 6,'09:00:00','10:00:00' );
       $this->assertEquals(0, count($leaveList));
    }
    
    public function testGetOverlappingLeaveInSameDay7() {

       $leaveList = $this->leaveRequestDao->getOverlappingLeave('2011-01-01', '2011-01-01', 6,'15:00:00','16:00:00' );
       $this->assertEquals(0, count($leaveList));
    }
    
    public function testGetOverlappingLeaveInSameDay8() {

       $leaveList = $this->leaveRequestDao->getOverlappingLeave('2011-01-01', '2011-01-01', 6);

        foreach ($leaveList as $leave) {
            $this->assertTrue($leave instanceof Leave);
        }
        $this->assertEquals(3, count($leaveList));
    }
    
    public function testGetOverlappingLeaveInSameDay9() {

       $leaveList = $this->leaveRequestDao->getOverlappingLeave('2011-01-01', '2011-01-01', 6, '10:30:00','10:45:00');

        foreach ($leaveList as $leave) {
            $this->assertTrue($leave instanceof Leave);
        }
        $this->assertEquals(1, count($leaveList));
    }
    
    public function testGetOverlappingLeaveInSameDay10() {

       $leaveList = $this->leaveRequestDao->getOverlappingLeave('2011-01-01', '2011-01-01', 6, '13:30:00','15:00:00');

        foreach ($leaveList as $leave) {
            $this->assertTrue($leave instanceof Leave);
        }
        $this->assertEquals(1, count($leaveList));
    }

    public function testGetOverlappingLeaveInSameDay11() {

       $leaveList = $this->leaveRequestDao->getOverlappingLeave('2011-01-01', '2011-01-01', 6, '09:00:00','10:00:00');
       $this->assertEquals(0, count($leaveList));
    }
    
    public function testGetOverlappingLeaveInSameDay12() {
       
       $leaveList = $this->leaveRequestDao->getOverlappingLeave('2011-01-01', '2011-01-01', 6, '09:00:00','10:30:00');

        foreach ($leaveList as $leave) {
            $this->assertTrue($leave instanceof Leave);
        }
        $this->assertEquals(1, count($leaveList));
    }
    
    public function testGetOverlappingLeaveMultiDayFullNoOverlap() {
       
       $leaveList = $this->leaveRequestDao->getOverlappingLeave('2011-01-05', '2011-01-10', 6);
        $this->assertEquals(0, count($leaveList));
    }    
    
    public function testGetOverlappingLeaveMultiDayFullOverlapWithFullDay() {
       
        $leaveList = $this->leaveRequestDao->getOverlappingLeave('2010-08-01', '2010-08-10', 1);
        $this->assertEquals(1, count($leaveList));

        $dates = array('2010-08-09');
        for ($i = 0; $i < count($leaveList); $i++) {
            $leave = $leaveList[$i];
            $this->assertTrue($leave instanceof Leave);
            $this->assertEquals($dates[$i], $leave->getDate());
        }
    }       
    
    public function testGetOverlappingLeaveMultiDayFullOverlapWithPartialDay() {
       
        $leaveList = $this->leaveRequestDao->getOverlappingLeave('2010-12-30', '2011-01-01', 6);
        $this->assertEquals(3, count($leaveList));

        $dates = array('2011-01-01', '2011-01-01', '2011-01-01');
        for ($i = 0; $i < count($leaveList); $i++) {
            $leave = $leaveList[$i];
            $this->assertTrue($leave instanceof Leave);
            $this->assertEquals($dates[$i], $leave->getDate());
        }
    }     
    
    public function testGetOverlappingLeaveMultiDayPartialStartDayFullDayNonStart() {
       
        $leaveList = $this->leaveRequestDao->getOverlappingLeave('2010-08-01', '2010-08-10', 1, '10:00:00', '13:00:00');
        $this->assertEquals(1, count($leaveList));

        $dates = array('2010-08-09');
        for ($i = 0; $i < count($leaveList); $i++) {
            $leave = $leaveList[$i];
            $this->assertTrue($leave instanceof Leave);
            $this->assertEquals($dates[$i], $leave->getDate());
        }
    }  
    
    public function testGetOverlappingLeaveMultiDayPartialStartDayFullDayNonStartNoMatch() {
       
        $leaveList = $this->leaveRequestDao->getOverlappingLeave('2010-08-01', '2010-08-08', 1, '10:00:00', '13:00:00');
        $this->assertEquals(0, count($leaveList));
    }    
    
    public function testGetOverlappingLeaveMultiDayPartialStartDayPartialDayNonStart() {
       
        $leaveList = $this->leaveRequestDao->getOverlappingLeave('2011-04-01', '2011-04-03', 6, '10:00:00', '13:00:00');
        $this->assertEquals(1, count($leaveList));

        $dates = array('2011-04-02');
        for ($i = 0; $i < count($leaveList); $i++) {
            $leave = $leaveList[$i];
            $this->assertTrue($leave instanceof Leave);
            $this->assertEquals($dates[$i], $leave->getDate());
        }
    }       
    
    public function testGetOverlappingLeaveMultiDayPartialStartDayFullDayEnd() {
       
        $leaveList = $this->leaveRequestDao->getOverlappingLeave('2010-08-01', '2010-08-09', 1, '10:00:00', '13:00:00');
        $this->assertEquals(1, count($leaveList));

        $dates = array('2010-08-09');
        for ($i = 0; $i < count($leaveList); $i++) {
            $leave = $leaveList[$i];
            $this->assertTrue($leave instanceof Leave);
            $this->assertEquals($dates[$i], $leave->getDate());
        }
    }    

    public function testGetOverlappingLeaveMultiDayPartialStartDayPartialDayEnd() {
       
        $leaveList = $this->leaveRequestDao->getOverlappingLeave('2011-03-30', '2011-04-02', 6, '10:00:00', '13:20:00');
        $this->assertEquals(1, count($leaveList));

        $dates = array('2011-04-02');
        for ($i = 0; $i < count($leaveList); $i++) {
            $leave = $leaveList[$i];
            $this->assertTrue($leave instanceof Leave);
            $this->assertEquals($dates[$i], $leave->getDate());
        }
    }    
    
    public function testGetOverlappingLeaveMultiDayPartialStartDayPartialDayStartNoMatch() {
       
        $leaveList = $this->leaveRequestDao->getOverlappingLeave('2011-04-02', '2011-04-06', 6, '10:00:00', '13:00:00');
        $this->assertEquals(0, count($leaveList));
    }    
    
    public function testGetOverlappingLeaveMultiDayPartialStartDayPartialDayStart() {
       
        $leaveList = $this->leaveRequestDao->getOverlappingLeave('2011-04-02', '2011-04-06', 6, '10:00:00', '13:20:00');
        $this->assertEquals(1, count($leaveList));

        $dates = array('2011-04-02');
        for ($i = 0; $i < count($leaveList); $i++) {
            $leave = $leaveList[$i];
            $this->assertTrue($leave instanceof Leave);
            $this->assertEquals($dates[$i], $leave->getDate());
        }
    }     
    
    public function testGetOverlappingLeaveMultiDayPartialStartDayFullDayStart() {
       
        $leaveList = $this->leaveRequestDao->getOverlappingLeave('2010-08-20', '2010-08-25', 1, '10:00:00', '13:20:00');
        $this->assertEquals(1, count($leaveList));

        $dates = array('2010-08-20');
        for ($i = 0; $i < count($leaveList); $i++) {
            $leave = $leaveList[$i];
            $this->assertTrue($leave instanceof Leave);
            $this->assertEquals($dates[$i], $leave->getDate());
        }
    }    
    
    public function testGetOverlappingLeaveMultiDayPartialStartDayEndDay() {
       
        $leaveList = $this->leaveRequestDao->getOverlappingLeave('2011-01-01', '2011-04-02', 6, '14:00:00', '15:20:00', false, 
                '12:00:00', '13:00:00');
        $this->assertEquals(0, count($leaveList));
    }     
    
    public function testGetOverlappingLeaveMultiDayPartialStartDayEndDayStartMatch() {
       
        $leaveList = $this->leaveRequestDao->getOverlappingLeave('2011-01-01', '2011-04-02', 6, '13:50:00', '15:20:00', false, 
                '12:00:00', '13:00:00');
        $this->assertEquals(1, count($leaveList));
        
        $dates = array('2011-01-01');
        for ($i = 0; $i < count($leaveList); $i++) {
            $leave = $leaveList[$i];
            $this->assertTrue($leave instanceof Leave);
            $this->assertEquals($dates[$i], $leave->getDate());
        }        
    }    
    
    public function testGetOverlappingLeaveMultiDayPartialStartDayEndDayEndMatch() {
       
        $leaveList = $this->leaveRequestDao->getOverlappingLeave('2011-01-01', '2011-04-02', 6, '14:00:00', '15:20:00', false, 
                '12:10:00', '13:10:00');
        $this->assertEquals(1, count($leaveList));
        
        $dates = array('2011-04-02');
        for ($i = 0; $i < count($leaveList); $i++) {
            $leave = $leaveList[$i];
            $this->assertTrue($leave instanceof Leave);
            $this->assertEquals($dates[$i], $leave->getDate());
        }        
    }     
    
    public function testGetOverlappingLeaveMultiDayPartialStartDayEndDayBothMatch() {
       
        $leaveList = $this->leaveRequestDao->getOverlappingLeave('2011-01-01', '2011-04-02', 6, '13:50:00', '15:20:00', false, 
                '12:10:00', '13:10:00');
        $this->assertEquals(2, count($leaveList));
        
        $dates = array('2011-01-01', '2011-04-02');
        for ($i = 0; $i < count($leaveList); $i++) {
            $leave = $leaveList[$i];
            $this->assertTrue($leave instanceof Leave);
            $this->assertEquals($dates[$i], $leave->getDate());
        }        
    }    
    
    public function testGetOverlappingLeaveMultiDayPartialAllDaysMatchPartialDayMiddle() {
       
        $leaveList = $this->leaveRequestDao->getOverlappingLeave('2011-03-30', '2011-04-05', 6, '13:50:00', '15:20:00', true);
        $this->assertEquals(1, count($leaveList));
        
        $dates = array('2011-04-02');
        for ($i = 0; $i < count($leaveList); $i++) {
            $leave = $leaveList[$i];
            $this->assertTrue($leave instanceof Leave);
            $this->assertEquals($dates[$i], $leave->getDate());
        }        
    }    
    
    public function testGetOverlappingLeaveMultiDayPartialAllDaysMatchPartialDayStart() {
       
        $leaveList = $this->leaveRequestDao->getOverlappingLeave('2011-04-02', '2011-04-05', 6, '13:50:00', '15:20:00', true);
        $this->assertEquals(1, count($leaveList));
        
        $dates = array('2011-04-02');
        for ($i = 0; $i < count($leaveList); $i++) {
            $leave = $leaveList[$i];
            $this->assertTrue($leave instanceof Leave);
            $this->assertEquals($dates[$i], $leave->getDate());
        }        
    }    
    
    public function testGetOverlappingLeaveMultiDayPartialAllDaysMatchPartialDayEnd() {
       
        $leaveList = $this->leaveRequestDao->getOverlappingLeave('2011-03-25', '2011-04-02', 6, '13:50:00', '15:20:00', true);
        $this->assertEquals(1, count($leaveList));
        
        $dates = array('2011-04-02');
        for ($i = 0; $i < count($leaveList); $i++) {
            $leave = $leaveList[$i];
            $this->assertTrue($leave instanceof Leave);
            $this->assertEquals($dates[$i], $leave->getDate());
        }        
    }    
    
    public function testGetOverlappingLeaveMultiDayPartialAllDaysMatchFullDayMiddle() {
       
        $leaveList = $this->leaveRequestDao->getOverlappingLeave('2010-08-10', '2010-08-13', 1, '13:50:00', '15:20:00', true);
        $this->assertEquals(1, count($leaveList));
        
        $dates = array('2010-08-11');
        for ($i = 0; $i < count($leaveList); $i++) {
            $leave = $leaveList[$i];
            $this->assertTrue($leave instanceof Leave);
            $this->assertEquals($dates[$i], $leave->getDate());
        }        
    }      
    
    public function testGetOverlappingLeaveMultiDayPartialAllDaysMatchFullDayStart() {
       
        $leaveList = $this->leaveRequestDao->getOverlappingLeave('2010-08-20', '2010-08-23', 1, '13:50:00', '15:20:00', true);
        $this->assertEquals(1, count($leaveList));
        
        $dates = array('2010-08-20');
        for ($i = 0; $i < count($leaveList); $i++) {
            $leave = $leaveList[$i];
            $this->assertTrue($leave instanceof Leave);
            $this->assertEquals($dates[$i], $leave->getDate());
        }        
    }   
    
    public function testGetOverlappingLeaveMultiDayPartialAllDaysMatchFullDayEnd() {
       
        $leaveList = $this->leaveRequestDao->getOverlappingLeave('2010-08-01', '2010-08-09', 1, '13:50:00', '15:20:00', true);
        $this->assertEquals(1, count($leaveList));
        
        $dates = array('2010-08-09');
        for ($i = 0; $i < count($leaveList); $i++) {
            $leave = $leaveList[$i];
            $this->assertTrue($leave instanceof Leave);
            $this->assertEquals($dates[$i], $leave->getDate());
        }        
    }  
    
    public function testGetOverlappingLeaveMultiDayPartialEndDayMatchPartialDayEnd() {
       
        $leaveList = $this->leaveRequestDao->getOverlappingLeave('2011-03-25', '2011-04-02', 6, null, null, false, '13:50:00', '15:20:00');
        $this->assertEquals(1, count($leaveList));
        
        $dates = array('2011-04-02');
        for ($i = 0; $i < count($leaveList); $i++) {
            $leave = $leaveList[$i];
            $this->assertTrue($leave instanceof Leave);
            $this->assertEquals($dates[$i], $leave->getDate());
        }       
    }     
    
    public function testGetOverlappingLeaveMultiDayPartialEndDayMatchPartialDayEndNoMatch() {
       
        $leaveList = $this->leaveRequestDao->getOverlappingLeave('2011-03-25', '2011-04-02', 6, null, null, false, '12:50:00', '13:00:00');
        $this->assertEquals(0, count($leaveList));       
    }
    
    public function testGetOverlappingLeaveMultiDayPartialEndDayMatchFullDayEnd() {
       
        $leaveList = $this->leaveRequestDao->getOverlappingLeave('2010-08-01', '2010-08-09', 1, null, null, false, '13:50:00', '15:20:00');
        $this->assertEquals(1, count($leaveList));
        
        $dates = array('2010-08-09');
        for ($i = 0; $i < count($leaveList); $i++) {
            $leave = $leaveList[$i];
            $this->assertTrue($leave instanceof Leave);
            $this->assertEquals($dates[$i], $leave->getDate());
        }       
    } 
    
    public function testGetOverlappingLeaveMultiDayPartialEndDayMatchFullDayStart() {
       
        $leaveList = $this->leaveRequestDao->getOverlappingLeave('2010-08-20', '2010-08-25', 1, null, null, false, '13:50:00', '15:20:00');
        $this->assertEquals(1, count($leaveList));
        
        $dates = array('2010-08-20');
        for ($i = 0; $i < count($leaveList); $i++) {
            $leave = $leaveList[$i];
            $this->assertTrue($leave instanceof Leave);
            $this->assertEquals($dates[$i], $leave->getDate());
        }       
    }     
    
    public function testGetOverlappingLeaveMultiDayPartialEndDayMatchFullDayMiddle() {
       
        $leaveList = $this->leaveRequestDao->getOverlappingLeave('2010-08-03', '2010-08-10', 1, null, null, false, '13:50:00', '15:20:00');
        $this->assertEquals(1, count($leaveList));
        
        $dates = array('2010-08-09');
        for ($i = 0; $i < count($leaveList); $i++) {
            $leave = $leaveList[$i];
            $this->assertTrue($leave instanceof Leave);
            $this->assertEquals($dates[$i], $leave->getDate());
        }       
    }     
    /* Tests for getOverlappingLeave() */
    
    
    public function testGetTotalLeaveDuration (){
         $duration = $this->leaveRequestDao->getTotalLeaveDuration( 1, '2011-01-01');
         $this->assertNull($duration);
    }
    
    public function testGetTotalLeaveDuration1 (){
         $duration = $this->leaveRequestDao->getTotalLeaveDuration( 6, '2011-01-01');
         $this->assertEquals( 3.00 ,$duration);
    }

    /* Common methods */

    private function _getLeaveRequestData() {

        $leaveRequest = new LeaveRequest();
        $leaveRequest->setLeaveTypeId(1);
        $leaveRequest->setDateApplied('2010-09-01');
        $leaveRequest->setEmpNumber(1);
        $leaveRequest->setComments("Testing comment i add");

        $leave1 = new Leave();
        $leave1->setLengthHours(8);
        $leave1->setLengthDays(1);
        $leave1->setDate('2010-12-01');
        $leave1->setStatus(1);

        $leave2 = new Leave();
        $leave2->setLengthHours(6);
        $leave2->setLengthDays(0.75);
        $leave2->setDate('2010-12-02');
        $leave2->setStatus(1);

        return array($leaveRequest, array($leave1, $leave2));

    }
    
    /* Tests for saveLeaveRequest() */

    public function testSaveLeaveRequestNewRequestNoEntitlement() {

        $leaveRequestIds = $this->getLeaveRequestIdsFromDb();
        $leaveIds = $this->getLeaveIdsFromDb();
        
        // These are the leave requests defined in the fixture (LeaveRequestDao.yml
        $expected = range(1,21);
        $this->assertEquals($expected, $leaveRequestIds);
        
        $leaveRequestData = $this->_getLeaveRequestData();
        $request = $leaveRequestData[0];
        $leave = $leaveRequestData[1];

        $leaveRequest = $this->leaveRequestDao->saveLeaveRequest($request, $leave, array());
        $this->assertTrue($leaveRequest instanceof LeaveRequest);
        
        $leaveRequestList = $this->getNewLeaveRequests($leaveRequestIds);
        $this->assertEquals(1, count($leaveRequestList));  
        $leaveRequest = $leaveRequestList[0];
        $this->compareLeaveRequest($request, $leaveRequest);

        $leaveList = $this->getNewLeave($leaveIds);

        $this->assertEquals(count($leave), count($leaveList));
        
        // update leave type, leave request id , emp number in leave requests
        for ($i = 0; $i < count($leave); $i++) {
            $expected = $leave[$i];
            $actual = $leaveList[$i];
            $expected->setLeaveTypeId($request->getLeaveTypeId());
            $expected->setEmpNumber($request->getEmpNumber());
            $expected->setLeaveRequestId($request->getId());
            
            $this->compareLeave($expected, $actual);
        }        
    }
    
    public function testSaveLeaveRequestNewRequestWithEntitlement() {

        $leaveRequestIds = $this->getLeaveRequestIdsFromDb();
        $leaveIds = $this->getLeaveIdsFromDb();
        
        // These are the leave requests defined in the fixture (LeaveRequestDao.yml
        $expected = range(1,21);
        $this->assertEquals($expected, $leaveRequestIds);
        
        $leaveRequestData = $this->_getLeaveRequestData();
        $request = $leaveRequestData[0];
        $leave = $leaveRequestData[1];

        $entitlementAssignmentIds = $this->getEntitlementAssignmentIdsFromDb();
        
        // entitlements to be assigned to leave
        $entitlements = array('current' => array(
            '2010-12-01' => array(1 => 0.4, 2 => 0.6),
            '2010-12-02' => array(1 => 1)
        ));
        
        $leaveRequest = $this->leaveRequestDao->saveLeaveRequest($request, $leave, $entitlements);
        $this->assertTrue($leaveRequest instanceof LeaveRequest);
        
        $leaveRequestList = $this->getNewLeaveRequests($leaveRequestIds);
        $this->assertEquals(1, count($leaveRequestList));  
        $leaveRequest = $leaveRequestList[0];
        $this->compareLeaveRequest($request, $leaveRequest);

        $newEntitlements = $this->getNewEntitlementAssignements($entitlementAssignmentIds);
        $this->assertEquals(3, count($newEntitlements));
        
        $leaveList = $this->getNewLeave($leaveIds);
        
        $this->assertEquals(count($leave), count($leaveList));

        // update leave type, leave request id , emp number in leave requests
        for ($i = 0; $i < count($leave); $i++) {
            $expected = $leave[$i];
            $actual = $leaveList[$i];
            $expected->setLeaveTypeId($request->getLeaveTypeId());
            $expected->setEmpNumber($request->getEmpNumber());
            $expected->setLeaveRequestId($request->getId());
            
            $this->compareLeave($expected, $actual);
            
            //echo "Leave for date: " . $actual->getDate() . ", id: " . $actual->getId() . "\n";
            
            // verify entitlement assignments
            $leaveId = $actual->getId();
            $leaveEntitlements = $entitlements['current'][$expected->getDate()];
            $newEntitlementsForThisLeave = $this->filterEntitlementsForLeave($leaveId, $newEntitlements);
            $this->validateLeaveEntitlementAssignment($leaveId, $leaveEntitlements, $newEntitlementsForThisLeave);
        }                
        
    }    
    
    public function testSaveLeaveRequestNewRequestWithEntitlementChanges() {

        $leaveRequestIds = $this->getLeaveRequestIdsFromDb();
        $leaveIds = $this->getLeaveIdsFromDb();
        
        // These are the leave requests defined in the fixture (LeaveRequestDao.yml
        $expected = range(1,21);
        $this->assertEquals($expected, $leaveRequestIds);
        
        $leaveRequestData = $this->_getLeaveRequestData();
        $request = $leaveRequestData[0];
        $leave = $leaveRequestData[1];

        $entitlementAssignmentIds = $this->getEntitlementAssignmentIdsFromDb();

        $savedEntitlements = $this->getEntitlementsFromDb();
        
        // Verify all entitlements in fixture retrieved.
        $this->assertEquals(4, count($savedEntitlements));
        
        // entitlements to be assigned to leave
        $entitlements = array('current' => array(
                    '2010-12-01' => array(1 => 0.4, 2 => 0.6),
                    '2010-12-02' => array(4 => 1)
                ),
                'change' => array(
                    34 => array(2 => 1, 3 => 0.4, 4 => 1), // new entitlements for leave without any
                    1 => array(1 => 1, 2 => 1, 4 => 0.5), // changes to existing values + new
                    2 => array(4 => 1, 3 => 1, 2 => 1), // no changes to existing, new ones added
                    4 => array() // no entitlements
                )
            );
        
        $leaveRequest = $this->leaveRequestDao->saveLeaveRequest($request, $leave, $entitlements);
        $this->assertTrue($leaveRequest instanceof LeaveRequest);
        
        $leaveRequestList = $this->getNewLeaveRequests($leaveRequestIds);
        $this->assertEquals(1, count($leaveRequestList));  
        $leaveRequest = $leaveRequestList[0];
        $this->compareLeaveRequest($request, $leaveRequest);

        $newEntitlements = $this->getNewEntitlementAssignements($entitlementAssignmentIds);
        $this->assertEquals(12, count($newEntitlements));
        
        $leaveList = $this->getNewLeave($leaveIds);
        
        $this->assertEquals(count($leave), count($leaveList));

        $entitlementUsedDaysChanges = array();
        
        // update leave type, leave request id , emp number in leave requests
        for ($i = 0; $i < count($leave); $i++) {
            $expected = $leave[$i];
            $actual = $leaveList[$i];
            $expected->setLeaveTypeId($request->getLeaveTypeId());
            $expected->setEmpNumber($request->getEmpNumber());
            $expected->setLeaveRequestId($request->getId());
            
            $this->compareLeave($expected, $actual);
            
            //echo "Leave for date: " . $actual->getDate() . ", id: " . $actual->getId() . "\n";
            
            // verify entitlement assignments
            $leaveId = $actual->getId();
            $leaveEntitlements = $entitlements['current'][$expected->getDate()];
            $newEntitlementsForThisLeave = $this->filterEntitlementsForLeave($leaveId, $newEntitlements);
            $this->validateLeaveEntitlementAssignment($leaveId, $leaveEntitlements, $newEntitlementsForThisLeave);      
            
            // update leave entitlement used days
            foreach ($leaveEntitlements as $entitlementId => $length) {
                if (!isset($entitlementUsedDaysChanges[$entitlementId])) {
                    $entitlementUsedDaysChanges[$entitlementId] = $length;
                } else {
                    $entitlementUsedDaysChanges[$entitlementId] += $length;
                }
            }
        }                
        
        // verify entitlement changes
        foreach($entitlements['change'] as $leaveId => $change) {
            $entitlementsForThisLeave = $this->getEntitlementAssignmentsForLeave($leaveId);
            $this->validateLeaveEntitlementAssignment($leaveId, $change, $entitlementsForThisLeave);
        }
        
        // Verify no entitlement has changed - since leave request status is: pending approval
        $savedEntitlementsAfter = $this->getEntitlementsFromDb();       
        $this->assertEquals(count($savedEntitlements), count($savedEntitlementsAfter));
        
        for ($i = 0; $i < count($savedEntitlements); $i++) {
            
            $savedEntitlement = $savedEntitlements[$i];

            if (isset($entitlementUsedDaysChanges[$savedEntitlement['id']])) {
                $savedEntitlement['days_used'] += $entitlementUsedDaysChanges[$savedEntitlement['id']];
            }            
            
            $this->compareEntitlement($savedEntitlement, $savedEntitlementsAfter[$i]);
        }
        
    }        

    public function testSaveLeaveRequestNewRequestWithEntitlementChangesAndTakenLeave() {

        $leaveRequestIds = $this->getLeaveRequestIdsFromDb();
        $leaveIds = $this->getLeaveIdsFromDb();
        
        // These are the leave requests defined in the fixture (LeaveRequestDao.yml
        $expected = range(1,21);
        $this->assertEquals($expected, $leaveRequestIds);
        
        $leaveRequestData = $this->_getLeaveRequestData();
        $request = $leaveRequestData[0];
        $leave = $leaveRequestData[1];
        
        // convert first leave request to taken
        $leave[0]->setStatus(Leave::LEAVE_STATUS_LEAVE_TAKEN);

        $entitlementAssignmentIds = $this->getEntitlementAssignmentIdsFromDb();

        $savedEntitlements = $this->getEntitlementsFromDb();
        
        // Verify all entitlements in fixture retrieved.
        $this->assertEquals(4, count($savedEntitlements));
        
        // entitlements to be assigned to leave
        $entitlements = array('current' => array(
                    '2010-12-01' => array(1 => 0.4, 2 => 0.6),
                    '2010-12-02' => array(4 => 1)
                ),
                'change' => array(
                    34 => array(2 => 1, 3 => 0.4, 4 => 1), // new entitlements for leave without any
                    1 => array(1 => 1, 2 => 1, 4 => 0.5), // changes to existing values + new
                    2 => array(4 => 1, 3 => 1, 2 => 1), // no changes to existing, new ones added
                    4 => array() // no entitlements
                )
            );
        
        $leaveRequest = $this->leaveRequestDao->saveLeaveRequest($request, $leave, $entitlements);
        $this->assertTrue($leaveRequest instanceof LeaveRequest);
        
        $leaveRequestList = $this->getNewLeaveRequests($leaveRequestIds);
        $this->assertEquals(1, count($leaveRequestList));  
        $leaveRequest = $leaveRequestList[0];
        $this->compareLeaveRequest($request, $leaveRequest);

        $newEntitlements = $this->getNewEntitlementAssignements($entitlementAssignmentIds);
        $this->assertEquals(12, count($newEntitlements));
        
        $leaveList = $this->getNewLeave($leaveIds);

        $this->assertEquals(count($leave), count($leaveList));

        $takenLeaveId = null;
       
        $entitlementUsedDaysChanges = array();        
        
        // update leave type, leave request id , emp number in leave requests
        for ($i = 0; $i < count($leave); $i++) {
            $expected = $leave[$i];
            $actual = $leaveList[$i];
            $expected->setLeaveTypeId($request->getLeaveTypeId());
            $expected->setEmpNumber($request->getEmpNumber());
            $expected->setLeaveRequestId($request->getId());
            
            $this->compareLeave($expected, $actual);
            
            //echo "Leave for date: " . $actual->getDate() . ", id: " . $actual->getId() . "\n";
            
            // verify entitlement assignments
            $leaveId = $actual->getId();
            
            if ($i == 1) {
                $takenLeaveId = $leaveId;
            }
            
            $leaveEntitlements = $entitlements['current'][$expected->getDate()];
            $newEntitlementsForThisLeave = $this->filterEntitlementsForLeave($leaveId, $newEntitlements);
            $this->validateLeaveEntitlementAssignment($leaveId, $leaveEntitlements, $newEntitlementsForThisLeave); 
            
        
            // update leave entitlement used days             
            foreach ($leaveEntitlements as $entitlementId => $length) {
                if (!isset($entitlementUsedDaysChanges[$entitlementId])) {
                    $entitlementUsedDaysChanges[$entitlementId] = $length;
                } else {
                    $entitlementUsedDaysChanges[$entitlementId] += $length;
                }
            }               
        }                
        
        // verify entitlement changes
        foreach($entitlements['change'] as $leaveId => $change) {
            $entitlementsForThisLeave = $this->getEntitlementAssignmentsForLeave($leaveId);
            $this->validateLeaveEntitlementAssignment($leaveId, $change, $entitlementsForThisLeave);
        }
        
        // Verify days_used for entitlement for leave is updated
        $this->assertTrue(!is_null($takenLeaveId));
                     
        $savedEntitlementsAfter = $this->getEntitlementsFromDb();       
        $this->assertEquals(count($savedEntitlements), count($savedEntitlementsAfter));
        
        for ($i = 0; $i < count($savedEntitlements); $i++) {
            $saved = $savedEntitlements[$i];
            $after = $savedEntitlementsAfter[$i];
            
                // verify used_days incremented
                $change = $entitlementUsedDaysChanges[$saved['id']];
                $this->assertEquals($saved['days_used'] + $change, $after['days_used']);
                
                // Compare other fields
                $saved['days_used'] = $saved['days_used'] + $change;
                $this->compareEntitlement($saved, $after);
        }
        
    }        
    
    public function testSaveLeaveRequestAbortTransaction() {

        // Get current records
        $leaveRequestIds = $this->getLeaveRequestIdsFromDb();
        $leaveIds = $this->getLeaveIdsFromDb();
        $entitlementAssignmentIds = $this->getEntitlementAssignmentIdsFromDb();
        
        // These are the leave requests defined in the fixture (LeaveRequestDao.yml
        $expected = range(1,21);
        $this->assertEquals($expected, $leaveRequestIds);
        
        $leaveRequestData = $this->_getLeaveRequestData();
        $request = $leaveRequestData[0];
        $leave = $leaveRequestData[1];
        
        // entitlements to be assigned to leave
        $entitlements = array('current' => array(
                    '2010-12-01' => array(1 => 0.4, 2 => 0.6),
                    '2010-12-02' => array(4 => 1)
                ),
                'change' => array(
                    34 => array(2 => 1, 3 => 0.4, 4 => 1), // new entitlements for leave without any
                    1 => array(111 => 1, 2 => 1, 4 => 0.5), // Transaction should abort because of this non-existing
                                                            // entitlement id (111)
                    2 => array(4 => 1, 3 => 1, 2 => 1), // no changes to existing, new ones added
                    4 => array() // no entitlements
                )
            );
        
        try {
            $this->leaveRequestDao->saveLeaveRequest($request, $leave, $entitlements);
            $this->fail("Exception expected");
        } catch (Exception $e) {
            
        }
        
        // verify no new records created.        
        $leaveRequestList = $this->getNewLeaveRequests($leaveRequestIds);
        $this->assertEquals(0, count($leaveRequestList));  
        
        $leaveList = $this->getNewLeave($leaveIds);        
        $this->assertEquals(0, count($leaveList));
        
        $entitlementList = $this->getNewEntitlementAssignements($entitlementAssignmentIds);
        $this->assertEquals(0, count($entitlementList));
        
        // verify old records still exist      
        $leaveRequestList = $this->getLeaveRequests($leaveRequestIds);
        $this->assertEquals(count($leaveRequestIds), count($leaveRequestList));  
        
        $leaveList = $this->getLeave($leaveIds);        
        $this->assertEquals(count($leaveIds), count($leaveList));
        
        $entitlementList = $this->getEntitlementAssignements($entitlementAssignmentIds);
        $this->assertEquals(count($entitlementAssignmentIds), count($entitlementList));        
    }        
    
    /*public function testSaveLeaveRequestUpdateRequest() {

        $leaveRequest = TestDataService::fetchObject('LeaveRequest', 1);
        $leave1 = TestDataService::fetchObject('Leave', 1);
        $leave2 = TestDataService::fetchObject('Leave', 2);

        $leave1->setLeaveStatus(2);
        $leave2->setLeaveStatus(2);

        $this->assertTrue($this->leaveRequestDao->saveLeaveRequest($leaveRequest, array($leave1, $leave2)));

        $leave1 = TestDataService::fetchObject('Leave', 1);
        $leave2 = TestDataService::fetchObject('Leave', 2);

        $this->assertEquals(1, $leave1->getId());
        $this->assertEquals(8, $leave1->getLengthHours());
        $this->assertEquals(1, $leave1->getLengthDays());
        $this->assertEquals(1, $leave1->getLeaveRequestId());
        $this->assertEquals('LTY001', $leave1->getLeaveTypeId());
        $this->assertEquals(1, $leave1->getEmpNumber());
        $this->assertEquals('2010-09-01', $leave1->getDate());
        $this->assertEquals(2, $leave1->getStatus());

        $this->assertEquals(1, $leave1->getId());
        $this->assertEquals(8, $leave1->getLengthHours());
        $this->assertEquals(1, $leave1->getLengthDays());
        $this->assertEquals(1, $leave1->getLeaveRequestId());
        $this->assertEquals('LTY001', $leave1->getLeaveTypeId());
        $this->assertEquals(1, $leave1->getEmpNumber());
        $this->assertEquals('2010-09-02', $leave1->getDate());
        $this->assertEquals(2, $leave1->getStatus());

    }*/


    /* Tests for modifyOverlapLeaveRequest() */

    public function xtestModifyOverlapLeaveRequest() {

        /* Preparing required data */

        $leaveRequest = new LeaveRequest();
        $leaveRequest->setLeavePeriodId(1);
        $leaveRequest->setLeaveTypeId('LTY001');
        $leaveRequest->setLeaveTypeName('Casual');
        $leaveRequest->setDateApplied('2010-12-01');
        $leaveRequest->setEmpNumber(1);

        $leave[0] = new Leave();
        $leave[0]->setLeaveLengthHours(8);
        $leave[0]->setLeaveLengthDays(1);
        $leave[0]->setLeaveDate('2010-12-30');
        $leave[0]->setLeaveStatus(1);

        $leave[1] = new Leave();
        $leave[1]->setLeaveLengthHours(8);
        $leave[1]->setLeaveLengthDays(1);
        $leave[1]->setLeaveDate('2010-12-31');
        $leave[1]->setLeaveStatus(1);

        $leave[2] = new Leave();
        $leave[2]->setLeaveLengthHours(8);
        $leave[2]->setLeaveLengthDays(1);
        $leave[2]->setLeaveDate('2011-01-01');
        $leave[2]->setLeaveStatus(1);

        $leave[3] = new Leave();
        $leave[3]->setLeaveLengthHours(8);
        $leave[3]->setLeaveLengthDays(1);
        $leave[3]->setLeaveDate('2011-01-02');
        $leave[3]->setLeaveStatus(1);

        $leavePeriod = TestDataService::fetchObject('LeavePeriod', 1);

        /* Executing tests */

        /* At use, modifyOverlapLeaveRequest() is called after calling
         * saveLeaveRequest()
         */

        $leaveRequest = $this->leaveRequestDao->saveLeaveRequest($leaveRequest, $leave);
        $this->assertTrue($leaveRequest instanceof LeaveRequest);
                
        $this->assertTrue($this->leaveRequestDao->modifyOverlapLeaveRequest($leaveRequest, $leave, $leavePeriod));

        $leaveRequestList = TestDataService::fetchLastInsertedRecords('LeaveRequest', 2);

        $this->assertEquals(21, $leaveRequestList[0]->getLeaveRequestId());
        $this->assertEquals('LTY001', $leaveRequestList[0]->getLeaveTypeId());
        $this->assertEquals('Casual', $leaveRequestList[0]->getLeaveTypeName());
        $this->assertEquals('2010-12-01', $leaveRequestList[0]->getDateApplied());
        $this->assertEquals(1, $leaveRequestList[0]->getEmpNumber());

        $this->assertEquals(22, $leaveRequestList[1]->getLeaveRequestId());
        $this->assertEquals('LTY001', $leaveRequestList[1]->getLeaveTypeId());
        $this->assertEquals('Casual', $leaveRequestList[1]->getLeaveTypeName());
        $this->assertEquals('2010-12-01', $leaveRequestList[1]->getDateApplied());
        $this->assertEquals(1, $leaveRequestList[1]->getEmpNumber());

        $leaveList = TestDataService::fetchLastInsertedRecords('Leave', 4);

        $this->assertEquals(36, $leaveList[0]->getId());
        $this->assertEquals(8, $leaveList[0]->getLengthHours());
        $this->assertEquals(1, $leaveList[0]->getLengthDays());
        $this->assertEquals(21, $leaveList[0]->getLeaveRequestId());
        $this->assertEquals('LTY001', $leaveList[0]->getLeaveTypeId());
        $this->assertEquals(1, $leaveList[0]->getEmpNumber());
        $this->assertEquals('2010-12-30', $leaveList[0]->getDate());
        $this->assertEquals(1, $leaveList[0]->getStatus());

        $this->assertEquals(37, $leaveList[1]->getId());
        $this->assertEquals(8, $leaveList[1]->getLengthHours());
        $this->assertEquals(1, $leaveList[1]->getLengthDays());
        $this->assertEquals(21, $leaveList[1]->getLeaveRequestId());
        $this->assertEquals('LTY001', $leaveList[1]->getLeaveTypeId());
        $this->assertEquals(1, $leaveList[1]->getEmpNumber());
        $this->assertEquals('2010-12-31', $leaveList[1]->getDate());
        $this->assertEquals(1, $leaveList[1]->getStatus());

        $this->assertEquals(38, $leaveList[2]->getId());
        $this->assertEquals(8, $leaveList[2]->getLengthHours());
        $this->assertEquals(1, $leaveList[2]->getLengthDays());
        $this->assertEquals(22, $leaveList[2]->getLeaveRequestId());
        $this->assertEquals('LTY001', $leaveList[2]->getLeaveTypeId());
        $this->assertEquals(1, $leaveList[2]->getEmpNumber());
        $this->assertEquals('2011-01-01', $leaveList[2]->getDate());
        $this->assertEquals(1, $leaveList[2]->getStatus());

        $this->assertEquals(39, $leaveList[3]->getId());
        $this->assertEquals(8, $leaveList[3]->getLengthHours());
        $this->assertEquals(1, $leaveList[3]->getLengthDays());
        $this->assertEquals(22, $leaveList[3]->getLeaveRequestId());
        $this->assertEquals('LTY001', $leaveList[3]->getLeaveTypeId());
        $this->assertEquals(1, $leaveList[3]->getEmpNumber());
        $this->assertEquals('2011-01-02', $leaveList[3]->getDate());
        $this->assertEquals(1, $leaveList[3]->getStatus());

    }


    /* Tests for searchLeaveRequests() */

    public function testSearchLeaveRequestsAll() {

        $searchParameters = new ParameterStub();
        $dateRange = new DateRangeStub();

        $searchParameters->setParameter('dateRange', $dateRange);
        $searchParameters->setParameter('noOfRecordsPerPage', 50);
        $searchParameters->setParameter('cmbWithTerminated','on');
        
        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters, 1);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(21, count($requestList));
        $this->assertEquals(21, $requestCount);

        
        /* Checking values and order */

        $this->assertEquals(21, $requestList[0]->getId());
        $this->assertEquals(4, $requestList[0]->getLeaveTypeId());
        $this->assertEquals('2011-04-02', $requestList[0]->getDateApplied());
        $this->assertEquals(6, $requestList[0]->getEmpNumber());

        $this->assertEquals(9, $requestList[19]->getId());
        $this->assertEquals(3, $requestList[19]->getLeaveTypeId());
        $this->assertEquals('2010-06-08', $requestList[19]->getDateApplied());
        $this->assertEquals(1, $requestList[19]->getEmpNumber());

    }
    
    public function testSearchLeaveRequestsAllTerminatedEmployee() {

        $searchParameters = new ParameterStub();
        $dateRange = new DateRangeStub();

        $searchParameters->setParameter('dateRange', $dateRange);
        $searchParameters->setParameter('noOfRecordsPerPage', 50);
        $searchParameters->setParameter('cmbWithTerminated','');
        
        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters, 1);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(10, count($requestList));
        $this->assertEquals(10, $requestCount);
    }

    public function testSearchLeaveRequestsDateRange() {

        $searchParameters = new ParameterStub();
        $dateRange = new DateRangeStub();
        $dateRange->setFromDate('2010-09-01');
        $dateRange->setToDate('2010-09-30');

        $searchParameters->setParameter('dateRange', $dateRange);
        $searchParameters->setParameter('noOfRecordsPerPage', 50);
        $searchParameters->setParameter('cmbWithTerminated','on');
        
        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters, 1);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(9, count($requestList));
        $this->assertEquals(9, $requestCount);

        /* Checking values and order */

        $this->assertEquals(8, $requestList[0]->getId());
        $this->assertEquals(1, $requestList[0]->getLeaveTypeId());
        $this->assertEquals('2010-08-08', $requestList[0]->getDateApplied());
        $this->assertEquals(1, $requestList[0]->getEmpNumber());

        $this->assertEquals(1, $requestList[8]->getId());
        $this->assertEquals(1, $requestList[8]->getLeaveTypeId());
        $this->assertEquals('2010-08-30', $requestList[8]->getDateApplied());
        $this->assertEquals(1, $requestList[8]->getEmpNumber());
        
    }

    public function testSearchLeaveRequestsStates() {

        $searchParameters = new ParameterStub();
        $dateRange = new DateRangeStub();
        $dateRange->setFromDate('2010-01-01');
        $dateRange->setToDate('2010-12-31');

        $searchParameters->setParameter('dateRange', $dateRange);
        $searchParameters->setParameter('statuses', array(1, -1, 3));
        $searchParameters->setParameter('noOfRecordsPerPage', 50);
        $searchParameters->setParameter('cmbWithTerminated','on');
        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters, 1);
        
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(11, count($requestList));
        $this->assertEquals(11, $requestCount);

        /* Checking values and order */

        $this->assertEquals(17, $requestList[0]->getId());
        $this->assertEquals(2, $requestList[0]->getLeaveTypeId());
        $this->assertEquals('2010-08-15', $requestList[0]->getDateApplied());
        $this->assertEquals(5, $requestList[0]->getEmpNumber());

        $this->assertEquals(10, $requestList[8]->getId());
        $this->assertEquals(1, $requestList[8]->getLeaveTypeId());
        $this->assertEquals('2010-06-09', $requestList[8]->getDateApplied());
        $this->assertEquals(1, $requestList[8]->getEmpNumber());

    }

    public function testSearchLeaveRequestsEmployeeFilterId() {

        $searchParameters = new ParameterStub();
        $dateRange = new DateRangeStub();

        $searchParameters->setParameter('dateRange', $dateRange);
        $searchParameters->setParameter('employeeFilter', 1);
        $searchParameters->setParameter('noOfRecordsPerPage', 50);
        $searchParameters->setParameter('cmbWithTerminated','on');
        
        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters, 1);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(11, count($requestList));
        $this->assertEquals(11, $requestCount);

        /* Checking values and order */

        $this->assertEquals(8, $requestList[0]->getId());
        $this->assertEquals(1, $requestList[0]->getLeaveTypeId());
        $this->assertEquals('2010-08-08', $requestList[0]->getDateApplied());
        $this->assertEquals(1, $requestList[0]->getEmpNumber());

        $this->assertEquals(9, $requestList[10]->getId());
        $this->assertEquals(3, $requestList[10]->getLeaveTypeId());
        $this->assertEquals('2010-06-08', $requestList[10]->getDateApplied());
        $this->assertEquals(1, $requestList[10]->getEmpNumber());

    }

    public function testSearchLeaveRequestsEmployeeFilterSingleEmployee() {

        $searchParameters = new ParameterStub();
        $dateRange = new DateRangeStub();

        $searchParameters->setParameter('dateRange', $dateRange);
        $searchParameters->setParameter('noOfRecordsPerPage', 50);
        $searchParameters->setParameter('cmbWithTerminated','on');
        
        $employee = new Employee();
        $employee->setEmpNumber(1);
        $searchParameters->setParameter('employeeFilter', $employee);

        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters, 1);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(11, count($requestList));
        $this->assertEquals(11, $requestCount);

        /* Checking values and order */

        $this->assertEquals(8, $requestList[0]->getId());
        $this->assertEquals(1, $requestList[0]->getLeaveTypeId());
        $this->assertEquals('2010-08-08', $requestList[0]->getDateApplied());
        $this->assertEquals(1, $requestList[0]->getEmpNumber());

        $this->assertEquals(9, $requestList[10]->getId());
        $this->assertEquals(3, $requestList[10]->getLeaveTypeId());
        $this->assertEquals('2010-06-08', $requestList[10]->getDateApplied());
        $this->assertEquals(1, $requestList[10]->getEmpNumber());

    }

    public function testSearchLeaveRequestsEmployeeFilterMultipleEmployees() {

        $searchParameters = new ParameterStub();
        $dateRange = new DateRangeStub();

        $searchParameters->setParameter('dateRange', $dateRange);
        $searchParameters->setParameter('noOfRecordsPerPage', 50);
        $searchParameters->setParameter('cmbWithTerminated','on');
        
        $employee1 = new Employee();
        $employee1->setEmpNumber(1);
        $employee2 = new Employee();
        $employee2->setEmpNumber(2);

        $searchParameters->setParameter('employeeFilter', array($employee1, $employee2));

        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters, 1);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(14, count($requestList));
        $this->assertEquals(14, $requestCount);

        /* Checking values and order */

        $this->assertEquals(14, $requestList[0]->getId());
        $this->assertEquals(1, $requestList[0]->getLeaveTypeId());
        $this->assertEquals('2010-08-12', $requestList[0]->getDateApplied());
        $this->assertEquals(2, $requestList[0]->getEmpNumber());

        $this->assertEquals(9, $requestList[13]->getId());
        $this->assertEquals(3, $requestList[13]->getLeaveTypeId());
        $this->assertEquals('2010-06-08', $requestList[13]->getDateApplied());
        $this->assertEquals(1, $requestList[13]->getEmpNumber());

    }

    public function xtestSearchLeaveRequestsLeavePeriod() {

        $searchParameters = new ParameterStub();
        $dateRange = new DateRangeStub();

        $searchParameters->setParameter('dateRange', $dateRange);
        $searchParameters->setParameter('leavePeriod', 1);
        $searchParameters->setParameter('noOfRecordsPerPage', 50);
        $searchParameters->setParameter('cmbWithTerminated','on');
        
        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters, 1);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(18, count($requestList));
        $this->assertEquals(18, $requestCount);

        /* Checking values and order */

        $this->assertEquals(17, $requestList[0]->getId());
        $this->assertEquals(2, $requestList[0]->getLeaveTypeId());
        $this->assertEquals('2010-08-15', $requestList[0]->getDateApplied());
        $this->assertEquals(5, $requestList[0]->getEmpNumber());

        $this->assertEquals(18, $requestList[17]->getId());
        $this->assertEquals(2, $requestList[17]->getLeaveTypeId());
        $this->assertEquals('2010-03-15', $requestList[17]->getDateApplied());
        $this->assertEquals(5, $requestList[17]->getEmpNumber());

    }

    public function testSearchLeaveRequestsLeaveType() {

        $searchParameters = new ParameterStub();
        $dateRange = new DateRangeStub();

        $searchParameters->setParameter('dateRange', $dateRange);
        $searchParameters->setParameter('leaveType', 1);
        $searchParameters->setParameter('noOfRecordsPerPage', 50);
        $searchParameters->setParameter('cmbWithTerminated','on');
        
        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters, 1);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(10, count($requestList));
        $this->assertEquals(10, $requestCount);

        /* Checking values and order */

        $this->assertEquals(19, $requestList[0]->getId());
        $this->assertEquals(1, $requestList[0]->getLeaveTypeId());
        $this->assertEquals('2010-08-20', $requestList[0]->getDateApplied());
        $this->assertEquals(5, $requestList[0]->getEmpNumber());

        $this->assertEquals(10, $requestList[9]->getId());
        $this->assertEquals(1, $requestList[9]->getLeaveTypeId());
        $this->assertEquals('2010-06-09', $requestList[9]->getDateApplied());
        $this->assertEquals(1, $requestList[9]->getEmpNumber());

    }

    /**
     * Test funtion to verify searching for leave requests of an employee
     * in a particular subunit.
     */
    public function testSearchLeaveRequestsByEmployeeSubUnit() {

        $searchParameters = new ParameterObject();
        
        // Employees under engineering, and support (2,5) : employees 2,5,6
        $searchParameters->setParameter('subUnit', 2);
        $searchParameters->setParameter('noOfRecordsPerPage', 50);
        $leaveFixture = $this->fixture['LeaveRequest'];
        $expected = array($leaveFixture[20], $leaveFixture[18], $leaveFixture[19], $leaveFixture[16],
                          $leaveFixture[13], $leaveFixture[12], $leaveFixture[11],
                          $leaveFixture[15], $leaveFixture[17]);
        
        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(count($expected), count($requestList));
        $this->assertEquals(count($expected), $requestCount);

        /* Checking values and order */
        $this->compareLeaveRequests($expected, $requestList);
    }

    /**
     * Test funtion to verify searching for leave requests of an employee
     * in a particular location
     */
    public function testSearchLeaveRequestsByLocation() {

        $searchParameters = new ParameterObject();
        
        // Location 1: employees 1
        $searchParameters->setParameter('locations', array(1));
        
        // need to include terminated since employee 1 is terminated.
        $searchParameters->setParameter('cmbWithTerminated', true);
        $searchParameters->setParameter('noOfRecordsPerPage', 50);
        $leaveFixture = $this->fixture['LeaveRequest'];
        $expected = array($leaveFixture[7], $leaveFixture[6], $leaveFixture[5],
                          $leaveFixture[4], $leaveFixture[3], $leaveFixture[2],
                          $leaveFixture[1], $leaveFixture[0], $leaveFixture[10],
                          $leaveFixture[9], $leaveFixture[8]);
            
        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(count($expected), count($requestList));
        $this->assertEquals(count($expected), $requestCount);

        /* Checking values and order */
        $this->compareLeaveRequests($expected, $requestList);                
    }

    /**
     * Test funtion to verify searching for leave requests of an employee
     * in a multiple locations
     */
    public function testSearchLeaveRequestsByMultipleLocations() {

        $searchParameters = new ParameterObject();
        
        // Location 3,4: employees 5,6
        $searchParameters->setParameter('locations', array(3,4));

        $leaveFixture = $this->fixture['LeaveRequest'];
        $expected = array($leaveFixture[20], $leaveFixture[18], $leaveFixture[19], $leaveFixture[16],
                          $leaveFixture[15], $leaveFixture[17]);
            
        $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters);
        $requestList = $searchResult['list'];
        $requestCount = $searchResult['meta']['record_count'];

        /* Checking type */

        foreach ($requestList as $request) {
            $this->assertTrue($request instanceof LeaveRequest);
        }

        /* Checking count */

        $this->assertEquals(count($expected), count($requestList));
        $this->assertEquals(count($expected), $requestCount);

        /* Checking values and order */
        $this->compareLeaveRequests($expected, $requestList);                
    }

    /**
     * Test funtion to verify searching for leave requests of by
     * Employee Name.
     */
    public function testSearchLeaveRequestsByEmployeeName() {

        $leaveFixture = $this->fixture['LeaveRequest'];
        $ashleyLeave = array($leaveFixture[13], $leaveFixture[12], $leaveFixture[11]);

        $tylorLandonJamesLeave = array($leaveFixture[18], $leaveFixture[16], $leaveFixture[15],
                                       $leaveFixture[14], $leaveFixture[17]);

        $names = array('Ashley Aldis Abel', 'Aldis', 'ldis', 'Aldis', 'Abr');
        $expectedArray = array($ashleyLeave, $ashleyLeave, $ashleyLeave, $ashleyLeave, $tylorLandonJamesLeave);

        for ($i = 0; $i < count($names); $i++) {
            $name = $names[$i];
            $expected = $expectedArray[$i];
            
            $searchParameters = new ParameterObject();
            $searchParameters->setParameter('employeeName', $name);

            $searchResult = $this->leaveRequestDao->searchLeaveRequests($searchParameters);
            $requestList = $searchResult['list'];
            $requestCount = $searchResult['meta']['record_count'];

            /* Checking type */

            foreach ($requestList as $request) {
                $this->assertTrue($request instanceof LeaveRequest);
            }

            /* Checking count */

            $this->assertEquals(count($expected), count($requestList));
            $this->assertEquals(count($expected), $requestCount);

            /* Checking values and order */
            $this->compareLeaveRequests($expected, $requestList);             
        }               
    }
    
    /**
     * Test the readLeave() function
     */
    public function testReadLeave() {

        //
        // Unavailable leave id
        //
        Doctrine_Query::create()
		->delete('*')
		->from('Leave l')
		->where('id = 999');

        $leave = $this->leaveRequestDao->readLeave(999);

        $this->assertFalse($leave, 'should return false for unavailable leave id');

        //
        // Available leave id
        //
        $leaveFixture = $this->fixture['Leave'][1];

        $savedLeave = $this->leaveRequestDao->readLeave($leaveFixture['id']);

        // Compare leave id
        $this->assertEquals($savedLeave->id, $leaveFixture['id'], 'leave id should match');
        
        // Compare other properties
        foreach ($leaveFixture as $property => $value) {
            $this->assertEquals($savedLeave->$property, $value, $property . ' should match ');
        }
    }

    public function testSaveLeave() {

        // Try and save leave with id that exists - should throw error
        $existingLeave = new Leave();
        $existingLeave->fromArray($this->fixture['Leave'][1]);

        try {
            $this->leaveRequestDao->saveLeave($existingLeave);
            $this->fail("Dao exception expected");
        } catch (DaoException $e) {
            // expected
        }

        // Try to save new leave (without id)
        $leaveRequestId = $this->fixture['LeaveRequest'][1]['id'];
        $leave = new Leave();
        $leave->length_hours = 8;
        $leave->length_days = 1;
        $leave->leave_request_id = $leaveRequestId;
        $leave->leave_type_id = $this->fixture['LeaveType'][0]['id'];
        $leave->emp_number = $this->fixture['Employee'][0]['empNumber'];
        $leave->date = '2010-09-09';
        $leave->status = 1;
        $this->leaveRequestDao->saveLeave($leave);

        // Verify id assigned
        $this->assertTrue(!empty($leave->id));


        // Verify saved by retrieving
        $result = Doctrine_Query::create()
                                    ->select()
                                    ->from('Leave l')
                                    ->where('id = ?', $leave->id)
                                    ->execute();
        $this->assertTrue($result->count() == 1);
        $this->assertTrue(is_a($result[0], Leave));

        $origAsArray = $leave->toArray();
        $savedAsArray = $result[0]->toArray();

        $this->assertEquals($origAsArray, $savedAsArray);        
    }

    public function xtestGetEmployeesInSubUnits() {
        
        $this->assertEquals(array(2, 6), $this->getEmployeesInSubUnits(array(2)));
        
        $this->assertEquals(array(1, 2, 3, 4, 5, 6), $this->getEmployeesInSubUnits(array(1,2,3,4,5)));
        
        $this->assertEquals(array(5), $this->getEmployeesInSubUnits(array(5)));
    }
    
    public function testChangeLeaveStatusNoEntitlementChanges() {
        $leaves = $this->getLeave(array(1));
        $this->assertEquals(1, count($leaves));
        $savedEntitlements = $this->getEntitlementsFromDb();        
        
        $leave = $leaves[0];
        $leave->setStatus(Leave::LEAVE_STATUS_LEAVE_CANCELLED);
        $this->leaveRequestDao->changeLeaveStatus($leave, array(), false);
        
        $leavesAfterChange = $this->getLeave(array(1));
        $this->assertEquals(1, count($leavesAfterChange));  
        $leaveAfterChange = $leavesAfterChange[0];
        
        // Verify status changed
        $this->assertEquals(Leave::LEAVE_STATUS_LEAVE_CANCELLED, $leaveAfterChange->getStatus());
        
        // Verify no entitlement changes
        $savedEntitlementsAfter = $this->getEntitlementsFromDb();       
        $this->assertEquals(count($savedEntitlements), count($savedEntitlementsAfter));
        
        for ($i = 0; $i < count($savedEntitlements); $i++) {                       
            $this->compareEntitlement($savedEntitlements[$i], $savedEntitlementsAfter[$i]);
        }        
        
    }         
    
    public function testChangeLeaveStatusNoEntitlementChangesRemoveLinked() {
        $leaveId = 1;
        $leaves = $this->getLeave(array($leaveId));
        $this->assertEquals(1, count($leaves));
        $leave = $leaves[0];
        
        $savedEntitlements = $this->getEntitlementsFromDb();        
        
        $thisLeaveEntitlementAssignments = $this->getEntitlementAssignmentsForLeave($leaveId);        
        $this->assertEquals(2, count($thisLeaveEntitlementAssignments));
        
        $entitlementAssignmentIds = $this->getEntitlementAssignmentIdsFromDb();        

        $leave->setStatus(Leave::LEAVE_STATUS_LEAVE_CANCELLED);     
        $this->leaveRequestDao->changeLeaveStatus($leave, array(), true);

        $leavesAfterChange = $this->getLeave(array(1));
        $this->assertEquals(1, count($leavesAfterChange));  
        $leaveAfterChange = $leavesAfterChange[0];
        
        // Verify status changed
        $this->assertEquals(Leave::LEAVE_STATUS_LEAVE_CANCELLED, $leaveAfterChange->getStatus());
        
        // Verify entitlement links to leave removed
        $thisLeaveEntitlementIdsAfter = $this->getEntitlementAssignmentsForLeave(1);
        $this->assertEquals(0, count($thisLeaveEntitlementIdsAfter));
        
        $entitlementAssignmentIdsAfter = $this->getEntitlementAssignmentIdsFromDb();
        $this->assertEquals(count($entitlementAssignmentIds) - count($thisLeaveEntitlementAssignments), count($entitlementAssignmentIdsAfter));
        
        // Verify entitlement changes
        $savedEntitlementsAfter = $this->getEntitlementsFromDb();     
        
        $this->assertEquals(count($savedEntitlements), count($savedEntitlementsAfter));        
        
        for ($i = 0; $i < count($savedEntitlements); $i++) {
            
            $savedEntitlement = $savedEntitlements[$i];

            foreach ($thisLeaveEntitlementAssignments as $assignment) {
                
                if ($assignment->getEntitlementId() == $savedEntitlement['id']) {
                    $savedEntitlement['days_used'] -= $assignment->getLengthDays();                   
                }
            }

            $this->compareEntitlement($savedEntitlement, $savedEntitlementsAfter[$i]);
        }       
    }   
    
    public function testChangeLeaveStatusEntitlementChangesRemoveLinked() {
        $leaveId = 1;
        $leaves = $this->getLeave(array($leaveId));
        $this->assertEquals(1, count($leaves));
        $leave = $leaves[0];
        
        $savedEntitlements = $this->getEntitlementsFromDb();        
        
        $thisLeaveEntitlementAssignments = $this->getEntitlementAssignmentsForLeave($leaveId);        
        $this->assertEquals(2, count($thisLeaveEntitlementAssignments));
        
        $entitlementAssignmentIds = $this->getEntitlementAssignmentIdsFromDb();        

        // entitlements to be assigned to leave
        $entitlements = array('current' => array(),
                'change' => array(
                    34 => array(2 => 1, 3 => 0.4, 4 => 1), // new entitlements for leave without any
                    2 => array(4 => 1, 3 => 0.5, 2 => 1), // no changes to existing, new ones added
                    4 => array() // additions to existing, new ones
                )
            );
        
        //
        // Before: entitlement id: days: days_used
        // 1: 3: 2.25
        // 2: 6: 3.5
        // 3: 1: 0
        // 4: 5: 3
        // 
        // Leave id: 1 is linked to the following (entitlement id: length_days)
        // 1: 0.5
        // 2: 0.5
        // 
        // Removing links for leave id: 1 results in the following:
        // 
        // entitlement id: days: days_used
        // 1: 3: 1.75
        // 2: 6: 3
        // 3: 1: 0
        // 4: 5: 3
        //
        // Changes in above array: (entitlement id: delta days_used)
        // 
        // 1: 0
        // 2: 2
        // 3: 0.9
        // 4: 2
        //
        // Final result should be:
        //
        // entitlement id: days: days_used
        // 1: 3: 1.75
        // 2: 6: 5
        // 3: 1: 0.9
        // 4: 5: 5
        //
        // Entitlement assignments
        // leave_id: entitlement_id: before: add: after
        // 34 : 2 : 0 : 1 : 1
        // 34 : 3 : 0 : 0.4 : 0.4
        // 34 : 4 : 0 : 1 : 1
        // 2 : 2 : 0 : 1 : 1
        // 2 : 3 : 0 : 0.5 : 0.5
        // 2 : 4 : 1 : 1 : 2

        // 4
        //
        
        
        $leave->setStatus(Leave::LEAVE_STATUS_LEAVE_CANCELLED);     
        

        $this->leaveRequestDao->changeLeaveStatus($leave, $entitlements, true);

        $leavesAfterChange = $this->getLeave(array(1));
        $this->assertEquals(1, count($leavesAfterChange));  
        $leaveAfterChange = $leavesAfterChange[0];
        
        // Verify status changed
        $this->assertEquals(Leave::LEAVE_STATUS_LEAVE_CANCELLED, $leaveAfterChange->getStatus());
        
        // Verify entitlement links to leave removed
        $thisLeaveEntitlementIdsAfter = $this->getEntitlementAssignmentsForLeave(1);
        $this->assertEquals(0, count($thisLeaveEntitlementIdsAfter));
        
        $entitlementAssignmentIdsAfter = $this->getEntitlementAssignmentIdsFromDb();
        
        $newlyInsertedAssignments = 5;
        $this->assertEquals(count($entitlementAssignmentIds) - count($thisLeaveEntitlementAssignments) + $newlyInsertedAssignments, 
                count($entitlementAssignmentIdsAfter));
        
        // Verify entitlement changes
        $savedEntitlementsAfter = $this->getEntitlementsFromDb();     
        
        $this->assertEquals(count($savedEntitlements), count($savedEntitlementsAfter));        
        for ($i = 0; $i < count($savedEntitlements); $i++) {
            
            $savedEntitlement = $savedEntitlements[$i];

            // Apply changes due to removing links for changed leave
            foreach ($thisLeaveEntitlementAssignments as $assignment) {                
                if ($assignment->getEntitlementId() == $savedEntitlement['id']) {
                    $savedEntitlement['days_used'] -= $assignment->getLengthDays();                   
                }
            }
            
            // apply changes due to specified entitlement changes
            foreach ($entitlements['change'] as $change) {
                foreach ($change as $entitlementId => $length) {
                    if ($entitlementId == $savedEntitlement['id']) {
                        $savedEntitlement['days_used'] += $length; 
                    }
                }
            }
            
            //print_r($savedEntitlement['id'] . ': ' . $savedEntitlement['no_of_days'] . ': ' . $savedEntitlement['days_used']); echo "\n";

            $this->compareEntitlement($savedEntitlement, $savedEntitlementsAfter[$i]);
        }
        
        // Verify entitlement assignments to leave
        $expectedEntitlementAssignments = array(34 => array(2 => 1, 3 => 0.4, 4 => 1),
            2 => array(2 => 1, 3 => 0.5, 4 => 2));
        
        foreach ($expectedEntitlementAssignments as $leaveId => $assignments) {
            $actualAssignments = $this->getEntitlementAssignmentsForLeave($leaveId, 'l.entitlement_id ASC');
            $this->assertEquals(count($assignments), count($actualAssignments));
            
            $i = 0;
            foreach ($assignments as $entitlementId => $length) {
                $actualAssignment = $actualAssignments[$i++];
                $this->assertEquals($entitlementId, $actualAssignment->getEntitlementId());
                $this->assertEquals($length, $actualAssignment->getLengthDays());
            }
        }
    }      
    
    
    /**
     * Get Employees under given subunit 
     * @param array $subUnits array of subunit ids
     * 
     * @return array Array of employee numbers.
     */
    protected function getEmployeesInSubUnits(array $subUnits) {
        $empNumbers = array();
        $employees = $this->fixture['Employee'];
        
        foreach($employees as $employee) {
            if (isset($employee['work_station']) &&
                    in_array($employee['work_station'], $subUnits)) {
                $empNumbers[] = $employee['empNumber'];
            }
        }
        
        return $empNumbers;
    }
    
    public function xtestGetLeaveRequestsForEmployees() {
        $this->assertEquals(range(1, 11), 
                $this->getLeaveRequestIds($this->getLeaveRequestsForEmployees(array(1))));
        
        $this->assertEquals(range(1, 14), 
                $this->getLeaveRequestIds($this->getLeaveRequestsForEmployees(array(1, 2))));

        $this->assertEquals(array(20), 
                $this->getLeaveRequestIds($this->getLeaveRequestsForEmployees(array(6))));
        
        $this->assertEquals(range(16, 19),
                $this->getLeaveRequestIds($this->getLeaveRequestsForEmployees(array(5))));        
        
    }
    
    protected function getLeaveRequestsForEmployees($empNumbers) {
        
        $leaveRequests = array();
        $allLeaveRequests = $this->fixture['LeaveRequest'];
        
        foreach($allLeaveRequests as $request) {
            if (in_array($request['empNumber'], $empNumbers)) {
                $leaveRequests[] = $request;
            }
        }
        
        return $leaveRequests;
    }
    
    protected function getLeaveRequestIds($leaveRequests) {
        $ids = array();
        foreach ($leaveRequests as $request) {
            $ids[] = $request['leave_request_id'];
        }
        
        return $ids;
    }
    
    protected function compareLeaveRequests($expected, $requestList) {
        $this->assertEquals(count($expected), count($requestList));

        for ($i = 0; $i < count($expected); $i++) {
            
            $item = $expected[$i];
            $result = $requestList[$i];
            $str = $item['id'] . '->' . $result->getId() . "\n" .
            $item['date_applied'] . '->' . $result->getDateApplied() . "\n" .
            $item['emp_number'] . '->' . $result->getEmpNumber() . "\n" .
            $item['comments'] . '->' . $result->getComments() . "\n\n";
            
            //echo $str;
            
            $this->assertEquals($item['id'], $result->getId());
            $this->assertEquals($item['leave_type_id'], $result->getLeaveTypeId());
            $this->assertEquals($item['date_applied'], $result->getDateApplied());
            $this->assertEquals($item['emp_number'], $result->getEmpNumber());
            $this->assertEquals($item['comments'], $result->getComments());
        }
    }
    
    protected function sortLeaveRequestsByDate($leaveRequests) {
        $this->assertTrue(usort($leaveRequests, array($this, 'compareByDate')));
        return $leaveRequests;
    }
    
    protected function compareByDate($request1, $request2) {
        $date1 = $request1['date_applied'];
        $date2 = $request2['date_applied'];
        
        $time1 = strtotime($date1);
        $time2 = strtotime($date2);
        
        $cmp = 0;
        if ($time1 < $time2) {
            $cmp = -1;
        } else if ($time1 > $time2) {
            $cmp = 1;
        }
        
        return $cmp;
    }

    protected function getLeaveRequestIdsFromDb() {
        $conn = Doctrine_Manager::connection()->getDbh();
        
        $query = "SELECT id from ohrm_leave_request";
        $statement = $conn->query($query);
        $ids = $statement->fetchAll(PDO::FETCH_COLUMN, 0);
        return $ids;        
    }

    protected function getLeaveIdsFromDb() {
        $conn = Doctrine_Manager::connection()->getDbh();
        
        $query = "SELECT id from ohrm_leave";
        $statement = $conn->query($query);
        $ids = $statement->fetchAll(PDO::FETCH_COLUMN, 0);
        return $ids;        
    }
    
    protected function getEntitlementAssignmentIdsFromDb() {
        $conn = Doctrine_Manager::connection()->getDbh();
        
        $query = "SELECT id from ohrm_leave_leave_entitlement";
        $statement = $conn->query($query);
        $ids = $statement->fetchAll(PDO::FETCH_COLUMN, 0);
        return $ids;        
    }    
    
    protected function getEntitlementIdsFromAssignmentIds($assignmentIds) {
        $assignments = $this->getEntitlementAssignements($assignmentIds);
        $entitlementIds = array();
        foreach ($assignments as $assignment) {
            $entitlementIds[] = $assignment->getEntitlementId();
        }
        return $entitlementIds;
    }

    protected function getEntitlementsFromDb() {

        $conn = Doctrine_Manager::connection()->getDbh();
        
        $query = "SELECT * from ohrm_leave_entitlement order by id ASC";
        $statement = $conn->query($query);
        return $statement->fetchAll(PDO::FETCH_ASSOC);

        
//        $q = Doctrine_Query::create()->from('LeaveEntitlement e')
//                ->addOrderBy('e.id ASC');
//
//        return $q->execute();       
    }    

    
    protected function getNewLeaveRequests($existingIds) {
        $q = Doctrine_Query::create()->from('LeaveRequest l')
                ->whereNotIn('l.id', $existingIds)
                ->addOrderBy('l.id ASC');

        return $q->execute();
    }
    
    protected function getLeaveRequests($ids) {
        $q = Doctrine_Query::create()->from('LeaveRequest l')
                ->whereIn('l.id', $ids)
                ->addOrderBy('l.id ASC');

        return $q->execute();
    }

    protected function getNewLeave($existingIds) {
        $q = Doctrine_Query::create()->from('Leave l')
                ->whereNotIn('l.id', $existingIds)
                ->addOrderBy('l.id ASC');

        return $q->execute();        
    }
    
    protected function getLeave($ids) {
        $q = Doctrine_Query::create()->from('Leave l')
                ->whereIn('l.id', $ids)
                ->addOrderBy('l.id ASC');

        return $q->execute();        
    }    
    
    protected function getNewEntitlementAssignements($existingIds) {
        $q = Doctrine_Query::create()->from('LeaveLeaveEntitlement l')
                ->whereNotIn('l.id', $existingIds)
                ->addOrderBy('l.id ASC');

        return $q->execute();         
    }
    
    protected function getEntitlementAssignements($ids) {
        $q = Doctrine_Query::create()->from('LeaveLeaveEntitlement l')
                ->whereIn('l.id', $ids)
                ->addOrderBy('l.id ASC');

        return $q->execute();         
    }    
    
    protected function getEntitlementAssignmentsForLeave($leaveId, $sort = 'l.id ASC') {
        $q = Doctrine_Query::create()->from('LeaveLeaveEntitlement l')
                ->where('l.leave_id = ?', $leaveId)
                ->addOrderBy($sort);

        return $q->execute();                 
    }
    
    protected function compareLeaveRequest(LeaveRequest $expected, LeaveRequest $result) {
        $this->assertTrue($result instanceof LeaveRequest);
        
        $expectedId = $expected->getId();
        
        if (!empty($expectedId)) {
            $this->assertEquals($expectedId, $result->getId());
        } else {
            $leaveRequestId = $result->getId();
            $this->assertTrue(!empty($leaveRequestId));            
        }
        
        $this->assertEquals($expected->getLeaveTypeId(), $result->getLeaveTypeId());
        $this->assertEquals($expected->getDateApplied(), $result->getDateApplied());
        $this->assertEquals($expected->getEmpNumber(), $result->getEmpNumber());
        $this->assertEquals($expected->getComments(), $result->getComments());        
    }
    
    protected function compareLeave(Leave $expected, Leave $result) {
        $this->assertTrue($result instanceof Leave);
        
        $expectedId = $expected->getId();
        
        if (!empty($expectedId)) {
            $this->assertEquals($expectedId, $result->getId());
        } else {
            $leaveId = $result->getId();
            $this->assertTrue(!empty($leaveId));            
        }
                
        $this->assertEquals($expected->getLeaveTypeId(), $result->getLeaveTypeId());
        $this->assertEquals($expected->getDate(), $result->getDate());
        $this->assertEquals($expected->getEmpNumber(), $result->getEmpNumber());
        $this->assertEquals($expected->getComments(), $result->getComments());        
        $this->assertEquals($expected->getLengthHours(), $result->getLengthHours());        
        $this->assertEquals($expected->getLengthDays(), $result->getLengthDays());        
        $this->assertEquals($expected->getStatus(), $result->getStatus());        
        $this->assertEquals($expected->getLeaveRequestId(), $result->getLeaveRequestId());    
    }    
    
    protected function validateLeaveEntitlementAssignment($leaveId, $expectedEntitlements, $newEntitlements) {
        
        $this->assertEquals(count($expectedEntitlements), count($newEntitlements));
        
        $usedEntitlements = array();
        
        foreach($expectedEntitlements as $entitlementId => $length) {
            $found = false;
            
            // echo "Looking at $entitlementId => $length \n";
            foreach($newEntitlements as $new) {
                
                if (!in_array($new->getEntitlementId(), $usedEntitlements)) {
                    $this->assertEquals($leaveId, $new->getLeaveId());

                    // echo "New: "; print_r($new->toArray()); echo "\n";
                    if ($new->getEntitlementId() == $entitlementId) {

                        // echo "Found\n";
                        $found = true;
                        $usedEntitlements[] = $new->getEntitlementId();                    
                        $this->assertEquals($length, $new->getLengthDays());
                        break;
                    }
                }
            }
            
            $this->assertTrue($found);
        }
    }    
    
    protected function filterEntitlementsForLeave($leaveId, $newEntitlements) {
        $filteredEntitlements = array();
        foreach($newEntitlements as $entitlement) {

            if ($entitlement->getLeaveId() == $leaveId) {
                $filteredEntitlements[] = $entitlement;
            }
        }
        return $filteredEntitlements;
    }
    
    protected function compareEntitlement($expected, $actual) {
        $this->assertEquals($expected['id'], $actual['id']);
        $this->assertEquals($expected['emp_number'], $actual['emp_number']);
        $this->assertEquals($expected['no_of_days'], $actual['no_of_days']);
        $this->assertEquals($expected['days_used'], $actual['days_used']);
        $this->assertEquals($expected['leave_type_id'], $actual['leave_type_id']);
        $this->assertEquals($expected['from_date'], $actual['from_date']);
        $this->assertEquals($expected['to_date'], $actual['to_date']);
        $this->assertEquals($expected['credited_date'], $actual['credited_date']);
        $this->assertEquals($expected['note'], $actual['note']);
        $this->assertEquals($expected['entitlement_type'], $actual['entitlement_type']);
        $this->assertEquals($expected['deleted'], $actual['deleted']);
        $this->assertEquals($expected['created_by_id'], $actual['created_by_id']);
        $this->assertEquals($expected['created_by_name'], $actual['created_by_name']);
        
    }    
 }


 class ParameterStub {

     private $dateRange;
     private $statuses;
     private $employeeFilter;
     private $leavePeriod;
     private $leaveType;

     public function setParameter($property, $value) {
         $this->$property = $value;
     }

     public function getParameter($property) {
         return $this->$property;
     }

 }

 class DateRangeStub {

     private $fromDate;
     private $toDate;

     public function setFromDate($fromDate) {
         $this->fromDate = $fromDate;
     }

     public function getFromDate() {
         return $this->fromDate;
     }

     public function setToDate($toDate) {
         $this->toDate = $toDate;
     }

     public function getToDate() {
         return $this->toDate;
     }

 }