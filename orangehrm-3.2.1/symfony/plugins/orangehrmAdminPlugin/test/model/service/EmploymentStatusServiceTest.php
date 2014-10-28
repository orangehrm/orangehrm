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
require_once sfConfig::get('sf_test_dir') . '/util/TestDataService.php';

/**
 * @group Admin
 */
class EmploymentStatusServiceTest extends PHPUnit_Framework_TestCase {
	
	private $empStatService;
	private $fixture;

	/**
	 * Set up method
	 */
	protected function setUp() {
		$this->empStatService = new EmploymentStatusService();
		$this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmAdminPlugin/test/fixtures/EmploymentStatusDao.yml';
		TestDataService::populate($this->fixture);
	}
	
	public function testGetEmploymentStatusList() {

		$empStatusList = TestDataService::loadObjectList('EmploymentStatus', $this->fixture, 'EmploymentStatus');

		$empStatusDao = $this->getMock('EmploymentStatusDao');
		$empStatusDao->expects($this->once())
			->method('getEmploymentStatusList')
			->will($this->returnValue($empStatusList));

		$this->empStatService->setEmploymentStatusDao($empStatusDao);

		$result = $this->empStatService->getEmploymentStatusList();
		$this->assertEquals($result, $empStatusList);
	}
	
	public function testGetEmploymentStatusById() {

		$empStatusList = TestDataService::loadObjectList('EmploymentStatus', $this->fixture, 'EmploymentStatus');

		$empStatusDao = $this->getMock('EmploymentStatusDao');
		$empStatusDao->expects($this->once())
			->method('getEmploymentStatusById')
			->with(1)
			->will($this->returnValue($empStatusList[0]));

		$this->empStatService->setEmploymentStatusDao($empStatusDao);

		$result = $this->empStatService->getEmploymentStatusById(1);
		$this->assertEquals($result, $empStatusList[0]);
	}
}

?>
