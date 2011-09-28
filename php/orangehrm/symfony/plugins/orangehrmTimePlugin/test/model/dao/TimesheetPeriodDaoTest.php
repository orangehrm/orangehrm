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
        TestDataService::truncateTables(array('Config'));
        TestDataService::populate(sfConfig::get('sf_plugins_dir') . '/orangehrmTimePlugin/test/fixtures/TimesheetPeriodDao.yml');
        $this->timesheetPeriodDao = new TimesheetPeriodDao();
        
        
	}

	public function testGetDefinedTimesheetPeriod() {

		$xmlString = $this->timesheetPeriodDao->getDefinedTimesheetPeriod();
		$this->assertEquals('<TimesheetPeriod><PeriodType>Weekly</PeriodType><ClassName>WeeklyTimesheetPeriod</ClassName><StartDate>1</StartDate><Heading>Week</Heading></TimesheetPeriod>', $xmlString);
	}

	public function testIsTimesheetPeriodDefined() {
		$isAllowed = $this->timesheetPeriodDao->isTimesheetPeriodDefined();
		//$this->assertEquals("Yes", $isAllowed);
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
