<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class WeeklyTimesheetPeriodTest extends PHPUnit_Framework_TestCase {

    private $weeklyTimesheetPeriod;

    protected function setUp() {
        TestDataService::populate(sfConfig::get('sf_plugins_dir') . '/orangehrmTimePlugin/test/fixtures/WeeklyTimesheetPeriod.yml');

        $this->weeklyTimesheetPeriod = new WeeklyTimesheetPeriod();
    }

    public function testCalculateDaysInTheTimesheetPeriod() {

        $key = 'timesheet_period_and_start_date';
        $xmlString = TestDataService::getRecords("SELECT value from hs_hr_config WHERE `key` = '" . $key . "'");

        $xmlString = $xmlString[0]['value'];        
        $xmlString = simplexml_load_String($xmlString);
        
        $currentDate = '2011-04-24';
        
        // This is necessary to make timeStampDiff 0 in MonthlyTimesheetPeriod::getDatesOfTheTimesheetPeriod
        // $timeStampDiff = $clientTimeZoneOffset * 3600 - $serverTimezoneOffset;
        $userObj = new User();
        $serverTimezoneOffset = ((int) date('Z'));
        $userObj->setUserTimeZoneOffset($serverTimezoneOffset / 3600);
        sfContext::getInstance()->getUser()->setAttribute('user', $userObj);        

        $datesArray = $this->weeklyTimesheetPeriod->calculateDaysInTheTimesheetPeriod($currentDate, $xmlString);
        $this->assertEquals($datesArray[0], "2011-04-18 00:00");
        $this->assertEquals($datesArray[3], "2011-04-21 00:00");
        $this->assertEquals(end($datesArray), "2011-04-24 00:00");

        $currentDate = '2012-02-28';

        $datesArray = $this->weeklyTimesheetPeriod->calculateDaysInTheTimesheetPeriod($currentDate, $xmlString);
        $this->assertEquals($datesArray[0], "2012-02-27 00:00");
        $this->assertEquals($datesArray[3], "2012-03-01 00:00");
        $this->assertEquals(end($datesArray), "2012-03-04 00:00");

        $currentDate = '2011-12-29';

        $datesArray = $this->weeklyTimesheetPeriod->calculateDaysInTheTimesheetPeriod($currentDate, $xmlString);
        $this->assertEquals($datesArray[0], "2011-12-26 00:00");
        $this->assertEquals($datesArray[3], "2011-12-29 00:00");
        $this->assertEquals(end($datesArray), "2012-01-01 00:00");
    }

    public function testSetTimesheetPeriodAndStartDate() {

        $startDay = "5";
        $returnedString = $this->weeklyTimesheetPeriod->setTimesheetPeriodAndStartDate($startDay);
        $this->assertEquals("<TimesheetPeriod><PeriodType>Weekly</PeriodType><ClassName>WeeklyTimesheetPeriod</ClassName><StartDate>5</StartDate><Heading>Week</Heading></TimesheetPeriod>", $returnedString);
    }

}
