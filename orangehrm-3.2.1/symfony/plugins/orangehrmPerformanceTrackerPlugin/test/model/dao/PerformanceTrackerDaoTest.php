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

/**
 * @group PerformanceTracker
 */
class PerformanceTrackerDaoTest extends PHPUnit_Framework_TestCase {

    private $testCase;
    private $performanceTrackerDao;
    protected $fixture;
    
    /**
     * Set up method
     */
    protected function setUp() {
        $this->performanceTrackerDao = new PerformanceTrackerDao();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmPerformanceTrackerPlugin/test/fixtures/PerformanceTrackerDao.yml';
        TestDataService::populate($this->fixture);
    }

    /**
     * Testing getEmployeeListAsJson
     */
    public function testPerform() {
        $this->assertTrue(true);
    }
    
    /**
     * Testing getPerformanceTrack
     */
    public function testGetPerformanceTrack() {
        $performanceTrackId = 1;
        $result = $this->performanceTrackerDao->getPerformanceTrack($performanceTrackId);
        $this->assertTrue(!empty($result));
        $this->assertEquals(1,$result->getEmpNumber());
    }
    
    public function testGetPerformanceTrackList(){
        $parameters['limit'] = null;
        $result = $this->performanceTrackerDao->getPerformanceTrackList($parameters);
        $this->assertEquals(2, count($result));
    }
    
    public function testGetPerformanceReviewersIdListByTrackId1(){
        $reviewid = 1;
        $result = $this->performanceTrackerDao->getPerformanceReviewersIdListByTrackId($reviewid);
        $this->assertTrue(!empty($result));       
    }
    
    public function testGetPerformanceReviewersIdListByTrackId2(){ 
        $reviewid = 4;
        $result = $this->performanceTrackerDao->getPerformanceReviewersIdListByTrackId($reviewid);
        $this->assertTrue(empty($result));        
    }

    public function testGetPerformanceReviewersIdListByTrackId3(){
       $trackId = 1;
       $result = $this->performanceTrackerDao->getPerformanceReviewersIdListByTrackId($trackId);       
       //only two reviewers assigned
       $this->assertEquals(count($result), 2);
       //reviwer with id 2 and 3 are in the reviewer list.               
       $this->assertTrue(in_array(2, $result));              
       $this->assertTrue(in_array(3, $result));       
    }
    
    public function testGetPerformanceReviewersIdListByTrackIdNull(){
        $result = $this->performanceTrackerDao->getPerformanceReviewersIdListByTrackId(null);
        $this->assertTrue(empty($result));
    }

    public function testGetPerformanceTrackerLog1(){ 
        $trackLogId = 2;
        $result = $this->performanceTrackerDao->getPerformanceTrackerLog($trackLogId);
        $this->assertTrue(!empty($result));        
    }    
    
    public function testGetPerformanceTrackerLog2(){ 
        $trackLogId = 5;
        $result = $this->performanceTrackerDao->getPerformanceTrackerLog($trackLogId);
        $this->assertTrue(empty($result));        
    }
        
    public function testGetPerformanceTrackerLogByEmployeeNumber1(){ 
        $empNumber = 1;
        $result = $this->performanceTrackerDao->getPerformanceTrackerLogByEmployeeNumber($empNumber);
        $this->assertTrue(!empty($result));        
    }
    
        
    public function testGetPerformanceTrackerLogByEmployeeNumber2(){ 
        $empNumber = 5;
        $result = $this->performanceTrackerDao->getPerformanceTrackerLogByEmployeeNumber($empNumber);
        $this->assertTrue(!empty($result));        
    }
    
}
