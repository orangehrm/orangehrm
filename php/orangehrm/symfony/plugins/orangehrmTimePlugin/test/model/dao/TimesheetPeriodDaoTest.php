<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TimesheetPeriodDaoTest
 *
 * @author orangehrm
 */
class TimesheetPeriodDaoTest extends PHPUnit_Framework_TestCase {

	private $timesheetPeriodDao;

	/**
	 * Set up method
	 */
	protected function setUp() {

		$this->timesheetPeriodDao = new TimesheetPeriodDao();
		$handle = mysql_connect("localhost","root","renukshan");
		$db = mysql_select_db("test_time", $handle);
		$query1 = "DELETE FROM `test_time`.`hs_hr_config` WHERE `hs_hr_config`.`key` = 'timesheet_period_and_start_date'";
		$query2 = "DELETE FROM `test_time`.`hs_hr_config` WHERE `hs_hr_config`.`key` = 'timesheet_period_set'";
		$query3 = "INSERT INTO `hs_hr_config`(`key`, `value`) VALUES('timesheet_period_set', 'Yes')";
		$query4 = "INSERT INTO `hs_hr_config`(`key`, `value`) VALUES('timesheet_period_and_start_date', '<TimesheetPeriod><PeriodType>Weekly</PeriodType><ClassName>WeeklyTimesheetPeriod</ClassName><StartDate>1</StartDate><Heading>Week</Heading></TimesheetPeriod>')";
		mysql_query($query1);
		mysql_query($query2);
		mysql_query($query3);
		mysql_query($query4);
	}

	public function testGetDefinedTimesheetPeriod() {

		$xmlString = $this->timesheetPeriodDao->getDefinedTimesheetPeriod();
		$this->assertEquals('<TimesheetPeriod><PeriodType>Weekly</PeriodType><ClassName>WeeklyTimesheetPeriod</ClassName><StartDate>1</StartDate><Heading>Week</Heading></TimesheetPeriod>', $xmlString);
	}

	public function testIsTimesheetPeriodDefined() {
		$isAllowed = $this->timesheetPeriodDao->isTimesheetPeriodDefined();
		$this->assertEquals("Yes", $isAllowed);
	}

	public function testSetTimesheetPeriod() {
		$temp = $this->timesheetPeriodDao->setTimesheetPeriod();
		$this->assertTrue($temp);
	}

	public function testSetTimesheetPeriodAndStartDate() {
		$xml = "<TimesheetPeriod><PeriodType>Weekly</PeriodType><ClassName>WeeklyTimesheetPeriod</ClassName><StartDate>3</StartDate><Heading>Week</Heading></TimesheetPeriod>";
		$temp = $this->timesheetPeriodDao->setTimesheetPeriodAndStartDate($xml);
		$this->assertTrue($temp);
	}

}

?>
