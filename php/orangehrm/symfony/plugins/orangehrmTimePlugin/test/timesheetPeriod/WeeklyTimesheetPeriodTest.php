<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class WeeklyTimesheetPeriodTest extends PHPUnit_Framework_TestCase {

    private $weeklyTimesheetPeriod;

    protected function setUp() {
        TestDataService::truncateTables(array('Config'));
        TestDataService::populate(sfConfig::get('sf_plugins_dir') . '/orangehrmTimePlugin/test/fixtures/WeeklyTimesheetPeriod.yml');

        $this->weeklyTimesheetPeriod = new WeeklyTimesheetPeriod();
    }

    public function testCalculateDaysInTheTimesheetPeriod() {

        $key = 'timesheet_period_and_start_date';
        $xmlString = TestDataService::fetchObject('Config', $key);

        $xmlString = $xmlString['value'];
        $xmlString = simplexml_load_String($xmlString);
        $currentDate = '2011-04-24';

        $datesArray = $this->weeklyTimesheetPeriod->calculateDaysInTheTimesheetPeriod($currentDate, $xmlString);
        $this->assertEquals($datesArray[0], "2011-04-20");
        $this->assertEquals($datesArray[3], "2011-04-23");
        $this->assertEquals(end($datesArray), "2011-04-26");

        $currentDate = '2012-02-28';

        $datesArray = $this->weeklyTimesheetPeriod->calculateDaysInTheTimesheetPeriod($currentDate, $xmlString);
        $this->assertEquals($datesArray[0], "2012-02-22");
        $this->assertEquals($datesArray[3], "2012-02-25");
        $this->assertEquals(end($datesArray), "2012-02-28");

        $currentDate = '2011-12-29';

        $datesArray = $this->weeklyTimesheetPeriod->calculateDaysInTheTimesheetPeriod($currentDate, $xmlString);
        $this->assertEquals($datesArray[0], "2011-12-28");
        $this->assertEquals($datesArray[3], "2011-12-31");
        $this->assertEquals(end($datesArray), "2012-01-03");
    }

    public function testSetTimesheetPeriodAndStartDate() {

        $startDay = "5";
        $returnedString = $this->weeklyTimesheetPeriod->setTimesheetPeriodAndStartDate($startDay);
        $this->assertEquals("<TimesheetPeriod><PeriodType>Weekly</PeriodType><ClassName>WeeklyTimesheetPeriod</ClassName><StartDate>5</StartDate><Heading>Week</Heading></TimesheetPeriod>", $returnedString);
    }

}
