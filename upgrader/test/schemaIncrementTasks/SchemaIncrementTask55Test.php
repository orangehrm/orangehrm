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
 * Test case for SchemaIncrementTask55
 *
 */
class SchemaIncrementTask55Test extends PHPUnit_Framework_TestCase {

    protected $schema;
    
    /**
     * Set up method
     */
    protected function setUp() {
        $this->schema = new SchemaIncrementTask55();
    }

    public function testGetLeavePeriodHistoryRecordsOnePeriodDefault() {
        $periods = array(
            array('leave_period_id' => 1, 'leave_period_start_date' => '2013-01-01', 
                'leave_period_end_date' => '2013-12-31')
        );

        $expected = array(
            array('1', '1', '2013-01-01')
        );

        $history = $this->schema->getLeavePeriodHistoryRecords($periods);
        $this->assertEquals($expected, $history);
    }
    
    public function testGetLeavePeriodHistoryRecordsOnePeriodCustom() {
        $periods = array(
            array('leave_period_id' => 1, 'leave_period_start_date' => '2013-03-07', 
                'leave_period_end_date' => '2014-03-06')
        );

        $expected = array(
            array('7', '3', '2013-03-07')
        );

        $history = $this->schema->getLeavePeriodHistoryRecords($periods);
        $this->assertEquals($expected, $history);
    }    

    public function testGetLeavePeriodHistoryRecordsManyPeriodsDefaultNoChange() {
        $periods = array(
            array('leave_period_id' => 1, 'leave_period_start_date' => '2013-01-01', 
                'leave_period_end_date' => '2013-12-31'),
            array('leave_period_id' => 1, 'leave_period_start_date' => '2014-01-01', 
                'leave_period_end_date' => '2014-12-31'),  
            array('leave_period_id' => 1, 'leave_period_start_date' => '2015-01-01', 
                'leave_period_end_date' => '2015-12-31'),              
        );

        $expected = array(
            array('1', '1', '2013-01-01')
        );

        $history = $this->schema->getLeavePeriodHistoryRecords($periods);
        $this->assertEquals($expected, $history);
    }   
    
    public function testGetLeavePeriodHistoryRecordsManyPeriodsCustomNoChange() {
        $periods = array(
            array('leave_period_id' => 1, 'leave_period_start_date' => '2013-04-21', 
                'leave_period_end_date' => '2014-04-20'),
            array('leave_period_id' => 1, 'leave_period_start_date' => '2014-04-21', 
                'leave_period_end_date' => '2015-04-20'),
            array('leave_period_id' => 1, 'leave_period_start_date' => '2015-04-21', 
                'leave_period_end_date' => '2016-04-20'),            
        );

        $expected = array(
            array('21', '4', '2013-04-21')
        );

        $history = $this->schema->getLeavePeriodHistoryRecords($periods);
        $this->assertEquals($expected, $history);
    }     
    
    public function testGetLeavePeriodHistoryRecordsManyWithOneChange() {
        $periods = array(
            array('leave_period_id' => 1, 'leave_period_start_date' => '2013-01-01', 
                'leave_period_end_date' => '2014-01-31'),
            array('leave_period_id' => 1, 'leave_period_start_date' => '2014-02-01', 
                'leave_period_end_date' => '2015-01-31')          
        );

        $expected = array(
            array('1', '1', '2013-01-01'),
            array('1', '2', '2013-12-30')
        );

        $history = $this->schema->getLeavePeriodHistoryRecords($periods);
        $this->assertEquals($expected, $history);
    }      
}

