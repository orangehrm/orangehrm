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

namespace OrangeHRM\Tests\Admin\Dao;

use OrangeHRM\Admin\Dao\LocationDao;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Location;
use OrangeHRM\Admin\Dto\LocationSearchFilterParams;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Dao
 */
class LocationDaoTest extends TestCase
{
    private LocationDao $locationDao;
    protected string $fixture;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->locationDao = new LocationDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/LocationDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetLocationById(): void
    {
        $result = $this->locationDao->getLocationById(1);
        $this->assertTrue($result instanceof Location);
        $this->assertEquals($result->getName(), 'location 1');
    }

    public function testGetLocationByIdForNonExistingId(): void
    {
        $result = $this->locationDao->getLocationById(1002);
        $this->assertNull($result);
    }

    public function testGetNumberOfEmployeesForLocation(): void
    {
        $result = $this->locationDao->getNumberOfEmployeesForLocation(1);
        $this->assertEquals($result, 3);
    }

    public function testGetLocationList(): void
    {
        $result = $this->locationDao->getLocationsIdList();
        $this->assertEquals(4, count($result));
    }

    public function testSearchLocationsForNullArray(): void
    {
        $locationSearchFilterParams = new LocationSearchFilterParams();
        $result = $this->locationDao->searchLocations($locationSearchFilterParams);
        $this->assertEquals(4, count($result));
    }

    public function testSearchLocationsForLocationName(): void
    {
        $locationSearchFilterParams = new LocationSearchFilterParams();
        $locationSearchFilterParams->setName('location 1');
        $result = $this->locationDao->searchLocations($locationSearchFilterParams);
        $this->assertCount(2, $result);
        $this->assertEquals($result[0]->getId(), 1);
    }

    public function testSearchLocationsForCity(): void
    {
        $locationSearchFilterParams = new LocationSearchFilterParams();
        $locationSearchFilterParams->setCity('city 1');
        $result = $this->locationDao->searchLocations($locationSearchFilterParams);
        $this->assertCount(2, $result);
    }

    public function testSearchLocationsForCountry(): void
    {
        $locationSearchFilterParams = new LocationSearchFilterParams();
        $locationSearchFilterParams->setCountryCode('LK');
        $result = $this->locationDao->searchLocations($locationSearchFilterParams);
        $this->assertEquals(count($result), 2);
        $this->assertEquals($result[0]->getId(), 1);
    }

    public function testGetSearchLocationListCount(): void
    {
        $locationSearchFilterParams = new LocationSearchFilterParams();
        $locationSearchFilterParams->setCountryCode('LK');
        $result = $this->locationDao->getSearchLocationListCount($locationSearchFilterParams);
        $this->assertEquals($result, 2);
    }

    public function testGetLocationIdsForEmployees(): void
    {
        $empNumbers = [1, 2, 3, 4, 5];
        $locationIds = $this->locationDao->getLocationIdsForEmployees($empNumbers);
        $expected = [1, 2];

        sort($locationIds);
        $this->assertEquals($expected, $locationIds);
    }

    public function testGetLocationIdsForEmployeesOneEmployee(): void
    {
        $empNumbers = [1];
        $locationIds = $this->locationDao->getLocationIdsForEmployees($empNumbers);
        $expected = [1];

        $this->assertEquals($expected, $locationIds);
    }

    public function testGetLocationIdsForEmployeesNoEmployees(): void
    {
        $empNumbers = [];
        $locationIds = $this->locationDao->getLocationIdsForEmployees($empNumbers);
        $expected = [];

        $this->assertEquals($expected, $locationIds);
    }

    public function testGetLocationIdsForEmployeesEmployeesWithoutLocations(): void
    {
        $empNumbers = [5];
        $locationIds = $this->locationDao->getLocationIdsForEmployees($empNumbers);
        $expected = [];

        $this->assertEquals($expected, $locationIds);
    }

    public function testGetLocationIdsForEmployeesInvalidEmployees(): void
    {
        $empNumbers = [100, 101, 102];
        $locationIds = $this->locationDao->getLocationIdsForEmployees($empNumbers);
        $expected = [];

        $this->assertEquals($expected, $locationIds);
    }

    public function testGetLocationsByIds(): void
    {
        $this->assertCount(2, $this->locationDao->getLocationsByIds([1, 3]));
    }
}
