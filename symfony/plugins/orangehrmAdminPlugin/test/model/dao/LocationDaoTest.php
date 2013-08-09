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
 *  @group Admin
 */
class LocationDaoTest extends PHPUnit_Framework_TestCase {
	
	private $locationDao;
	protected $fixture;

	/**
	 * Set up method
	 */
	protected function setUp() {

		$this->locationDao = new LocationDao();
		$this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmAdminPlugin/test/fixtures/LocationDao.yml';
		TestDataService::populate($this->fixture);
	}
	
	public function testGetLocationById(){
		$result = $this->locationDao->getLocationById(1);
		$this->assertTrue($result instanceof Location);
                $this->assertEquals($result->getName(), 'location 1');
	}
	
	public function testGetNumberOfEmplyeesForLocation(){
		$result = $this->locationDao->getNumberOfEmplyeesForLocation(1);
		$this->assertEquals($result, 3);
	}
	
	public function testGetLocationList(){
		$result = $this->locationDao->getLocationList();
		$this->assertEquals(count($result), 3);
	}
		
	public function testSearchLocationsForNullArray() {
		$srchClues = array();
		$result = $this->locationDao->searchLocations($srchClues);
		$this->assertEquals(count($result), 3);
	}

	public function testSearchLocationsForLocationName() {
		$srchClues = array(
		    'name' => 'location 1'
		);
		$result = $this->locationDao->searchLocations($srchClues);
		$this->assertEquals(count($result), 1);
		$this->assertEquals($result[0]->getId(), 1);
	}

	public function testSearchLocationsForCity() {
		$srchClues = array(
		    'city' => 'city 1'
		);
		$result = $this->locationDao->searchLocations($srchClues);
		$this->assertEquals(count($result), 1);
	}

	public function testSearchLocationsForCountry() {
		$srchClues = array(
		    'country' => 'LK'
		);
		$result = $this->locationDao->searchLocations($srchClues);
		$this->assertEquals(count($result), 2);
		$this->assertEquals($result[0]->getId(), 1);
	}
        
	public function testSearchLocationsForCountryArray() {
		$srchClues = array(
		    'country' => array('LK')
		);
		$result = $this->locationDao->searchLocations($srchClues);
		$this->assertEquals(count($result), 2);
		$this->assertEquals($result[0]->getId(), 1);
                
		$srchClues = array(
		    'country' => array('LK', 'US')
		);
		$result = $this->locationDao->searchLocations($srchClues);
		$this->assertEquals(count($result), 3);
		$this->assertEquals($result[0]->getId(), 1);                
	}        
	
	public function testGetSearchLocationListCount() {
		$srchClues = array(
		    'country' => 'LK'
		);
		$result = $this->locationDao->getSearchLocationListCount($srchClues);
		$this->assertEquals($result, 2);
	}
        
        public function testGetLocationIdsForEmployees() {
            $empNumbers = array(1, 2, 3, 4, 5);
            $locationIds = $this->locationDao->getLocationIdsForEmployees($empNumbers);
            $expected = array(1, 2);
            
            sort($locationIds);
            $this->assertEquals($expected, $locationIds);
        }
        
        public function testGetLocationIdsForEmployeesOneEmployee() {
            $empNumbers = array(1);
            $locationIds = $this->locationDao->getLocationIdsForEmployees($empNumbers);
            $expected = array(1);

            $this->assertEquals($expected, $locationIds);
        }     
        
        public function testGetLocationIdsForEmployeesNoEmployees() {
            $empNumbers = array();
            $locationIds = $this->locationDao->getLocationIdsForEmployees($empNumbers);
            $expected = array();

            $this->assertEquals($expected, $locationIds);
        }      
        
        public function testGetLocationIdsForEmployeesEmployeesWithoutLocations() {
            $empNumbers = array(5);
            $locationIds = $this->locationDao->getLocationIdsForEmployees($empNumbers);
            $expected = array();

            $this->assertEquals($expected, $locationIds);
        }        
        
        public function testGetLocationIdsForEmployeesInvalidEmployees() {
            $empNumbers = array(100, 101, 102);
            $locationIds = $this->locationDao->getLocationIdsForEmployees($empNumbers);
            $expected = array();

            $this->assertEquals($expected, $locationIds);
        }          
        
}

