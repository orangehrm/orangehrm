<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Admin\Service;

use OrangeHRM\Admin\Dao\LocationDao;
use OrangeHRM\Admin\Dto\LocationSearchFilterParams;
use OrangeHRM\Admin\Service\Model\LocationModel;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Location;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class LocationService
{
    use UserRoleManagerTrait;
    use NormalizerServiceTrait;
    use EmployeeServiceTrait;

    /**
     * @var LocationDao|null
     */
    private ?LocationDao $locationDao = null;

    /**
     * @return LocationDao
     */
    public function getLocationDao(): LocationDao
    {
        if (!($this->locationDao instanceof LocationDao)) {
            $this->locationDao = new LocationDao();
        }
        return $this->locationDao;
    }

    /**
     * @param LocationDao $locationDao
     */
    public function setLocationDao(LocationDao $locationDao): void
    {
        $this->locationDao = $locationDao;
    }

    /**
     * Get Location by id
     *
     * @param int $locationId
     *
     * @return Location|null
     */
    public function getLocationById(int $locationId): ?Location
    {
        return $this->getLocationDao()->getLocationById($locationId);
    }

    /**
     * Search location by location name, city and country.
     *
     * @param LocationSearchFilterParams $locationSearchFilterParams
     *
     * @return Location[]
     */
    public function searchLocations(LocationSearchFilterParams $locationSearchFilterParams): array
    {
        return $this->getLocationDao()->searchLocations($locationSearchFilterParams);
    }

    /**
     * Get location count of the search results.
     *
     * @param LocationSearchFilterParams $locationSearchFilterParams
     *
     * @return int
     */
    public function getSearchLocationListCount(LocationSearchFilterParams $locationSearchFilterParams): int
    {
        return $this->getLocationDao()->getSearchLocationListCount($locationSearchFilterParams);
    }

    /**
     * Get total number of employees in a location.
     *
     * @param int $locationId
     *
     * @return int
     */
    public function getNumberOfEmployeesForLocation(int $locationId): int
    {
        return $this->getLocationDao()->getNumberOfEmployeesForLocation($locationId);
    }

    /**
     * Get LocationIds for Employees with the given employee numbers
     *
     * @param int[] $empNumbers Array of employee numbers
     *
     * @return int[] of locationIds of the given employees
     */
    public function getLocationIdsForEmployees(array $empNumbers): array
    {
        return $this->getLocationDao()->getLocationIdsForEmployees($empNumbers);
    }

    /**
     * Returns the accessible location list
     *
     * @param int|null $empNumber
     *
     * @return array
     */
    public function getAccessibleLocationsArray(?int $empNumber = null): array
    {
        $employeeLocationsIds = [];
        if (!is_null($empNumber)) {
            $employee = $this->getEmployeeService()->getEmployeeByEmpNumber($empNumber);
            foreach ($employee->getLocations() as $location) {
                $employeeLocationsIds[] = $location->getId();
            }
        }
        $accessibleLocationIds = $this->getUserRoleManager()->getAccessibleEntityIds(Location::class);
        $accessibleLocationIds = array_unique(array_merge($accessibleLocationIds, $employeeLocationsIds));
        $accessibleLocations = $this->getLocationDao()->getLocationsByIds($accessibleLocationIds);
        return $this->getNormalizerService()->normalizeArray(LocationModel::class, $accessibleLocations);
    }

    /**
     * Save Location in the database
     *
     * @param Location $location
     *
     * @return Location
     */
    public function saveLocation(Location $location): Location
    {
        return $this->getLocationDao()->saveLocation($location);
    }

    /**
     * This will flag the Locations as deleted
     *
     * @param array $ids
     *
     * @return int number of affected rows
     */
    public function deleteLocations(array $ids): int
    {
        return $this->getLocationDao()->deleteLocations($ids);
    }

    /**
     * @return array
     */
    public function getLocationsArray(): array
    {
        $locationSearchFilterParams = new LocationSearchFilterParams();
        $locationSearchFilterParams->setLimit(0);
        $locations = $this->getLocationDao()->searchLocations($locationSearchFilterParams);
        return $this->getNormalizerService()->normalizeArray(LocationModel::class, $locations);
    }
}
