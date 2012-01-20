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
 * Description of TimesheetPeriodDaoTest
 *
 * @group Time
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
