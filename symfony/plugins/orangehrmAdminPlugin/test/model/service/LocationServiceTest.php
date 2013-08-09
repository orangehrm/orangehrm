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
class LocationServiceTest extends PHPUnit_Framework_TestCase {
	
	private $locationService;
	private $fixture;

	/**
	 * Set up method
	 */
	protected function setUp() {
		$this->locationService = new LocationService();
		$this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmAdminPlugin/test/fixtures/LocationDao.yml';
		TestDataService::populate($this->fixture);
	}
	
	public function testgetLocationById() {

		$locationList = TestDataService::loadObjectList('Location', $this->fixture, 'Location');

		$locationDao = $this->getMock('LocationDao');
		$locationDao->expects($this->once())
			->method('getLocationById')
			->with(1)
			->will($this->returnValue($locationList[0]));

		$this->locationService->setLocationDao($locationDao);

		$result = $this->locationService->getLocationById(1);
		$this->assertEquals($result,$locationList[0]);
	}
	
	public function testSearchLocations() {

		$locationList = TestDataService::loadObjectList('Location', $this->fixture, 'Location');
		$srchClues = array(
		    'name' => 'location 1'
		);
		
		$locationDao = $this->getMock('LocationDao');
		$locationDao->expects($this->once())
			->method('searchLocations')
			->with($srchClues)
			->will($this->returnValue($locationList[0]));

		$this->locationService->setLocationDao($locationDao);

		$result = $this->locationService->searchLocations($srchClues);
		$this->assertEquals($result,$locationList[0]);
	}
	
	public function testGetSearchLocationListCount() {

		$locationList = TestDataService::loadObjectList('Location', $this->fixture, 'Location');
		$srchClues = array(
		    'name' => 'location 1'
		);
		
		$locationDao = $this->getMock('LocationDao');
		$locationDao->expects($this->once())
			->method('getSearchLocationListCount')
			->with($srchClues)
			->will($this->returnValue(1));

		$this->locationService->setLocationDao($locationDao);

		$result = $this->locationService->getSearchLocationListCount($srchClues);
		$this->assertEquals($result,1);
	}
	
	public function testGetNumberOfEmplyeesForLocation() {

		$locationList = TestDataService::loadObjectList('Location', $this->fixture, 'Location');

		$locationDao = $this->getMock('LocationDao');
		$locationDao->expects($this->once())
			->method('getNumberOfEmplyeesForLocation')
			->with(1)
			->will($this->returnValue(2));

		$this->locationService->setLocationDao($locationDao);

		$result = $this->locationService->getNumberOfEmplyeesForLocation(1);
		$this->assertEquals($result,2);
	}
	
	public function testGetLocationList() {

		$locationList = TestDataService::loadObjectList('Location', $this->fixture, 'Location');

		$locationDao = $this->getMock('LocationDao');
		$locationDao->expects($this->once())
			->method('getLocationList')
			->will($this->returnValue($locationList));

		$this->locationService->setLocationDao($locationDao);

		$result = $this->locationService->getLocationList();
		$this->assertEquals($result,$locationList);
	}
        
	public function testGetLocationIdsForEmployees() {

		$empNumbers = array(2, 34, 1, 20);
                $locationIds = array(2, 3, 1);

		$locationDao = $this->getMock('LocationDao');
		$locationDao->expects($this->once())
			->method('getLocationIdsForEmployees')
                        ->with($empNumbers)
			->will($this->returnValue($locationIds));

		$this->locationService->setLocationDao($locationDao);

		$result = $this->locationService->getLocationIdsForEmployees($empNumbers);
		$this->assertEquals($locationIds, $result);
	}        
}
