<?php
/*
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
 * @group Time
 */
class MonthlyTimesheetPeriodTest extends PHPUnit_Framework_TestCase {

    private $monthlyTimesheetPeriod;

    protected function setUp() {
        TestDataService::truncateTables(array('Config'));
        TestDataService::populate(sfConfig::get('sf_plugins_dir') . '/orangehrmTimePlugin/test/fixtures/MonthlyTimesheetPeriod.yml');
        $this->monthlyTimesheetPeriod = new MonthlyTImesheetPeriod();
    }

    public function testCalculateDaysInTheTimesheetPeriod() {

        $key = 'timesheet_period_and_start_date';
        
        $xmlString = TestDataService::getRecords("SELECT value from hs_hr_config WHERE `key` = '" . $key . "'");
        $xmlString = $xmlString[0]['value'];         

        $xmlString = simplexml_load_String($xmlString);


        $currentDate = '2011-04-24';
        $dates = $this->monthlyTimesheetPeriod->calculateDaysInTheTimesheetPeriod($currentDate, $xmlString);
        $this->assertEquals("2011-04-13", $dates[0]);
        $this->assertEquals("2011-05-12", end($dates));
        $this->assertEquals("2011-04-18", $dates[5]);


        $dates = $this->monthlyTimesheetPeriod->calculateDaysInTheTimesheetPeriod("2011-02-18", $xmlString);
        $this->assertEquals("2011-02-13", $dates[0]);
        $this->assertEquals("2011-03-12", end($dates));
        $this->assertEquals("2011-02-23", $dates[10]);

        $dates = $this->monthlyTimesheetPeriod->calculateDaysInTheTimesheetPeriod("2012-02-18", $xmlString);
        $this->assertEquals("2012-02-13", $dates[0]);
        $this->assertEquals("2012-03-12", end($dates));
        $this->assertEquals("2012-02-28", $dates[15]);

        $dates = $this->monthlyTimesheetPeriod->calculateDaysInTheTimesheetPeriod("2012-12-31", $xmlString);
        $this->assertEquals("2012-12-13", $dates[0]);
        $this->assertEquals("2013-01-12", end($dates));
        $this->assertEquals("2013-01-02", $dates[20]);
    }

    public function testSetTimesheetPeriodAndStartDate() {

        $startDay = "12";
        $returnedString = $this->monthlyTimesheetPeriod->setTimesheetPeriodAndStartDate($startDay);
        $this->assertEquals("<TimesheetPeriod><PeriodType>Monthly</PeriodType><ClassName>MonthlyTimesheetPeriod</ClassName><StartDate>12</StartDate><Heading>Month</Heading></TimesheetPeriod>", $returnedString);
    }

    public function testGetDatesOfTheTimesheetPeriod() {
        
        // This is necessary to make timeStampDiff 0 in MonthlyTimesheetPeriod::getDatesOfTheTimesheetPeriod
        // $timeStampDiff = $clientTimeZoneOffset * 3600 - $serverTimezoneOffset;
        $serverTimezoneOffset = ((int) date('Z'));

        sfContext::getInstance()->getUser()->setAttribute('system.timeZoneOffset', $serverTimezoneOffset / 3600);
         
        $startDate = "2011-12-12";
        $endDate = "2011-12-31";
        $returnedDatesArray = $this->monthlyTimesheetPeriod->getDatesOfTheTimesheetPeriod($startDate, $endDate);
        $this->assertEquals($returnedDatesArray[0], "2011-12-12");

        $startDate = "2012-02-20";
        $endDate = "2012-03-15";
        $returnedDatesArray = $this->monthlyTimesheetPeriod->getDatesOfTheTimesheetPeriod($startDate, $endDate);

        $this->assertEquals($returnedDatesArray[0], "2012-02-20");
        $this->assertEquals(end($returnedDatesArray), "2012-03-15");
        $this->assertEquals($returnedDatesArray[9], "2012-02-29");
    }

}
