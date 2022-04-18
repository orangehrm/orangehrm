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

namespace OrangeHRM\Tests\Admin\Service;

use OrangeHRM\Admin\Dao\LocationDao;
use OrangeHRM\Admin\Service\LocationService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Core\Service\NormalizerService;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Location;
use OrangeHRM\Admin\Dto\LocationSearchFilterParams;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Service
 */
class LocationServiceTest extends KernelTestCase
{
    use ServiceContainerTrait;
    private LocationService $locationService;
    private string $fixture;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->locationService = new LocationService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/LocationDao.yml';
        TestDataService::populate($this->fixture);

        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
                                ->disableOriginalConstructor()
                                ->onlyMethods(['getAccessibleEntityIds'])
                                ->getMock();
        $userRoleManager->expects($this->any())
                        ->method('getAccessibleEntityIds')
                        ->willReturn([1, 2, 3]);

        $normalizerService = $this->getMockBuilder(NormalizerService::class)
                                  ->getMock();

        $employeeService = $this->getMockBuilder(EmployeeService::class)
                                ->onlyMethods(['getEmployeeByEmpNumber'])
                                ->getMock();
        $employeeService->expects($this->any())
                        ->method('getEmployeeByEmpNumber')
                        ->willReturn(new Employee());

        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::NORMALIZER_SERVICE => $normalizerService,
                Services::EMPLOYEE_SERVICE => $employeeService,
            ]
        );
    }

    public function testGetLocationById(): void
    {
        $locationList = TestDataService::loadObjectList(Location::class, $this->fixture, 'Location');

        $locationDao = $this->getMockBuilder(LocationDao::class)->getMock();
        $locationDao->expects($this->once())
            ->method('getLocationById')
            ->with(1)
            ->will($this->returnValue($locationList[0]));

        $this->locationService->setLocationDao($locationDao);

        $result = $this->locationService->getLocationById(1);
        $this->assertEquals($result, $locationList[0]);
    }

    public function testSearchLocations(): void
    {
        $locationList = TestDataService::loadObjectList(Location::class, $this->fixture, 'Location');
        $locationSearchFilterParams = new LocationSearchFilterParams();
        $locationSearchFilterParams->setName('location 1');

        $locationDao = $this->getMockBuilder(LocationDao::class)->getMock();
        $locationDao->expects($this->once())
            ->method('searchLocations')
            ->with($locationSearchFilterParams)
            ->will($this->returnValue($locationList));

        $this->locationService->setLocationDao($locationDao);

        $result = $this->locationService->searchLocations($locationSearchFilterParams);
        $this->assertEquals($result, $locationList);
    }

    public function testGetSearchLocationListCount(): void
    {
        $locationSearchFilterParams = new LocationSearchFilterParams();
        $locationSearchFilterParams->setName('location 1');

        $locationDao = $this->getMockBuilder(LocationDao::class)->getMock();
        $locationDao->expects($this->once())
            ->method('getSearchLocationListCount')
            ->with($locationSearchFilterParams)
            ->will($this->returnValue(1));

        $this->locationService->setLocationDao($locationDao);

        $result = $this->locationService->getSearchLocationListCount($locationSearchFilterParams);
        $this->assertEquals($result, 1);
    }

    public function testGetNumberOfEmployeesForLocation(): void
    {
        $locationDao = $this->getMockBuilder(LocationDao::class)->getMock();
        $locationDao->expects($this->once())
            ->method('getNumberOfEmployeesForLocation')
            ->with(1)
            ->will($this->returnValue(2));

        $this->locationService->setLocationDao($locationDao);

        $result = $this->locationService->getNumberOfEmployeesForLocation(1);
        $this->assertEquals($result, 2);
    }

    public function testGetLocationIdsForEmployees(): void
    {
        $empNumbers = [2, 34, 1, 20];
        $locationIds = [2, 3, 1];

        $locationDao = $this->getMockBuilder(LocationDao::class)->getMock();
        $locationDao->expects($this->once())
            ->method('getLocationIdsForEmployees')
            ->with($empNumbers)
            ->will($this->returnValue($locationIds));

        $this->locationService->setLocationDao($locationDao);

        $result = $this->locationService->getLocationIdsForEmployees($empNumbers);
        $this->assertEquals($locationIds, $result);
    }

    public function testGetAccessibleLocationsArray()
    {
        $locations = $this->locationService->getAccessibleLocationsArray();
        $this->assertCount(0, $locations);
        $locations = $this->locationService->getAccessibleLocationsArray(1);
        $this->assertCount(0, $locations);
    }
}
