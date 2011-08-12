<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class WeeklyTimesheetPeriodTest extends PHPUnit_Framework_TestCase {

    private $weeklyTimesheetPeriod;

    protected function setUp() {

        $this->weeklyTimesheetPeriod = new WeeklyTimesheetPeriod();
        $handle = mysql_connect("localhost", "root", "renukshan");
        $db = mysql_select_db("test_time", $handle);
        $query1 = "DELETE FROM `test_time`.`hs_hr_config` WHERE `hs_hr_config`.`key` = 'timesheet_period_and_start_date'";
        $query2 = "DELETE FROM `test_time`.`hs_hr_config` WHERE `hs_hr_config`.`key` = 'timesheet_period_set'";
        $query3 = "INSERT INTO `hs_hr_config`(`key`, `value`) VALUES('timesheet_period_set', 'Yes')";
        $query4 = "INSERT INTO `hs_hr_config`(`key`, `value`) VALUES('timesheet_period_and_start_date', '<TimesheetPeriod><PeriodType>Weekly</PeriodType><ClassName>WeeklyTimesheetPeriod</ClassName><StartDate>3</StartDate><Heading>Week</Heading></TimesheetPeriod>')";
        mysql_query($query1);
        mysql_query($query2);
        mysql_query($query3);
        mysql_query($query4);
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
        
        $startDay="5";
        $returnedString = $this->weeklyTimesheetPeriod->setTimesheetPeriodAndStartDate($startDay);
        $this->assertEquals("<TimesheetPeriod><PeriodType>Weekly</PeriodType><ClassName>WeeklyTimesheetPeriod</ClassName><StartDate>5</StartDate><Heading>Week</Heading></TimesheetPeriod>",$returnedString);
    }

}
