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

class LeavePeriodHistoryDaoTest extends PHPUnit_Framework_TestCase {
    
    public $leavePeriodDao;

    protected function setUp() {

        $this->leavePeriodDao = new LeavePeriodDao();
        TestDataService::populate(sfConfig::get('sf_plugins_dir') . '/orangehrmLeavePlugin/test/fixtures/LeavePeriodHistoryDao.yml');

    }
    
    public function testSaveLeavePeriodHistory(){
        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setLeavePeriodStartMonth(1);
        $leavePeriodHistory->setLeavePeriodStartDay(1);
        $leavePeriodHistory->setCreatedAt('2012-01-01');
        
        $result = $this->leavePeriodDao->saveLeavePeriodHistory( $leavePeriodHistory );
        $this->assertEquals(1,$result->getLeavePeriodStartMonth());
        $this->assertEquals(1,$result->getLeavePeriodStartDay());
        $this->assertEquals('2012-01-01',$result->getCreatedAt());
    }
    
    public function testGetCurrentLeavePeriodStartDateAndMonth(){
        $result = $this->leavePeriodDao->getCurrentLeavePeriodStartDateAndMonth( );
        $this->assertEquals(1,$result->getLeavePeriodStartMonth());
        $this->assertEquals(3,$result->getLeavePeriodStartDay());
        $this->assertEquals('2012-01-02',$result->getCreatedAt());
    }
    
    public function testGetLeavePeriodHistoryList(){
        $result = $this->leavePeriodDao->getLeavePeriodHistoryList( );
        $this->assertEquals(1,$result[0]->getLeavePeriodStartMonth());
        $this->assertEquals(4,$result[0]->getLeavePeriodStartDay());
        $this->assertEquals('2012-01-01',$result[0]->getCreatedAt());
        
        $this->assertEquals(1,$result[1]->getLeavePeriodStartMonth());
        $this->assertEquals(1,$result[1]->getLeavePeriodStartDay());
        $this->assertEquals('2012-01-02',$result[1]->getCreatedAt());
        
        $this->assertEquals(1,$result[2]->getLeavePeriodStartMonth());
        $this->assertEquals(2,$result[2]->getLeavePeriodStartDay());
        $this->assertEquals('2012-01-02',$result[2]->getCreatedAt());
        
        $this->assertEquals(4,count($result));
    }
}
