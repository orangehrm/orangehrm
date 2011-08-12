<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TimesheetPeriodServiceTest
 *
 * @author orangehrm
 */
class TimesheetPeriodServiceTest extends PHPUnit_Framework_Testcase {

	private $timesheetPeriodService;

	protected function setUp() {
	
		$this->timesheetPeriodService = new TimesheetPeriodService();
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

	public function testGetTimesheetPeriodDao() {

		$this->assertTrue($this->timesheetPeriodService->getTimesheetPeriodDao() instanceof TimesheetPeriodDao);
	}

	public function testSetTimesheetPeriodDao() {

		$timesheetPeriodDao = new TimesheetPeriodDao();
		$this->timesheetPeriodService->setTimesheetPeriodDao($timesheetPeriodDao);

		$this->assertTrue($this->timesheetPeriodService->getTimesheetPeriodDao() instanceof TimesheetPeriodDao);
	}

	public function testGetDefinedTimesheetPeriod() {

		$currentDate = '2011-06-30';
		$key = 'timesheet_period_and_start_date';
		$xmlString = TestDataService::fetchObject('Config', $key);
		$timesheetPeriodDaoMock = $this->getMock('TimesheetPeriodDao', array('getDefinedTimesheetPeriod'));
		$timesheetPeriodDaoMock->expects($this->once())
			->method('getDefinedTimesheetPeriod')
			->will($this->returnValue($xmlString->getValue()));

		$this->timesheetPeriodService->setTimesheetPeriodDao($timesheetPeriodDaoMock);
		$array = $this->timesheetPeriodService->getDefinedTimesheetPeriod($currentDate);
		$this->assertEquals($array[0],'2011-06-27');
		$this->assertEquals($array[4],'2011-07-01');


	}

	public function testIsTimesheetPeriodDefined() {

		$key = 'timesheet_period_set';
		$boolean = TestDataService::fetchObject('Config', $key);
		$timesheetPeriodDaoMock = $this->getMock('TimesheetPeriodDao', array('isTimesheetPeriodDefined'));
		$timesheetPeriodDaoMock->expects($this->once())
			->method('isTimesheetPeriodDefined')
			->will($this->returnValue($boolean));

		$this->timesheetPeriodService->setTimesheetPeriodDao($timesheetPeriodDaoMock);
		$isDefined = $this->timesheetPeriodService->isTimesheetPeriodDefined();
		$this->assertEquals($boolean,$isDefined);

	}

	public function testSetTimesheetPeriod(){

		$startDay='1';
		$xml = '<TimesheetPeriod><PeriodType>Weekly</PeriodType><ClassName>WeeklyTimesheetPeriod</ClassName><StartDate>1</StartDate><Heading>Week</Heading></TimesheetPeriod>';
		$timesheetPeriodDaoMock = $this->getMock('TimesheetPeriodDao', array('setTimesheetPeriod','setTimesheetPeriodAndStartDate'));
		$timesheetPeriodDaoMock->expects($this->once())
			->method('setTimesheetPeriod')
			->will($this->returnValue(true));

		$timesheetPeriodDaoMock->expects($this->once())
			->method('setTimesheetPeriodAndStartDate')
			->with($xml)
			->will($this->returnValue(true));

		$this->timesheetPeriodService->setTimesheetPeriodDao($timesheetPeriodDaoMock);
		$true = $this->timesheetPeriodService->setTimesheetPeriod($startDay);
		$this->assertTrue($true);
		

	}
    
    public function testGetTimesheetHeading(){
        
        
        		
		$key = 'timesheet_period_and_start_date';
		$xmlString = TestDataService::fetchObject('Config', $key);
		$timesheetPeriodDaoMock = $this->getMock('TimesheetPeriodDao', array('getDefinedTimesheetPeriod'));
		$timesheetPeriodDaoMock->expects($this->once())
			->method('getDefinedTimesheetPeriod')
			->will($this->returnValue($xmlString->getValue()));

		$this->timesheetPeriodService->setTimesheetPeriodDao($timesheetPeriodDaoMock);
		$timesheetHeading = $this->timesheetPeriodService->getTimesheetHeading();
        
        $this->assertEquals("Week",(string)$timesheetHeading );
        
    }



   


}

?>
