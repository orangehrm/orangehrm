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

namespace OrangeHRM\Admin\Dao;

use OrangeHRM\Admin\Dto\LocationSearchFilterParams;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\EmpLocations;
use OrangeHRM\Entity\Location;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\Paginator;

class LocationDao extends BaseDao
{
    /**
     * Returns the Location having the given id (or null, if not exist)
     *
     * @param int $locationId
     *
     * @return Location|null
     */
    public function getLocationById(int $locationId): ?Location
    {
        return $this->getRepository(Location::class)->find($locationId);
    }

    /**
     * @param int[] $ids
     * @return int[]
     */
    public function getExistingLocationIds(array $ids): array
    {
        $qb = $this->createQueryBuilder(Location::class, 'location');
        $qb->select('location.id')
            ->andWhere($qb->expr()->in('location.id', ':ids'))
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * Returns the count of Locations that matches the given search filters
     *
     * @param LocationSearchFilterParams $locationSearchFilterParams
     *
     * @return int
     */
    public function getSearchLocationListCount(LocationSearchFilterParams $locationSearchFilterParams): int
    {
        $locationSearchFilterParams->setSortField(null);
        return $this->searchLocationsPaginator($locationSearchFilterParams)->count();
    }

    /**
     * Searches the Locations matching the given search filters
     *
     * @param LocationSearchFilterParams $locationSearchFilterParams
     *
     * @return Location[]
     */
    public function searchLocations(LocationSearchFilterParams $locationSearchFilterParams): array
    {
        return $this->searchLocationsPaginator($locationSearchFilterParams)->getQuery()->execute();
    }

    /**
     *
     * Set up the query with the paginator to search the locations using the given filters
     *
     * @param LocationSearchFilterParams $locationSearchFilterParams
     *
     * @return Paginator
     */
    private function searchLocationsPaginator(
        LocationSearchFilterParams $locationSearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(Location::class, 'location');
        $q->leftJoin('location.country', 'country');
        $isSortedByEmpCount = $locationSearchFilterParams->getSortField() === 'noOfEmployees';
        if ($isSortedByEmpCount) {
            $q->leftJoin('location.employees', 'employees');
            $q->addSelect('COUNT(employees.empNumber) AS HIDDEN noOfEmployees');
            $q->addOrderBy(
                'noOfEmployees',
                $locationSearchFilterParams->getSortOrder()
            );
            $q->addGroupBy('location.id');
            $this->setPaginationParams($q, $locationSearchFilterParams);
        } else {
            $this->setSortingAndPaginationParams($q, $locationSearchFilterParams);
        }

        if (!empty($locationSearchFilterParams->getName())) {
            $q->andWhere($q->expr()->like('location.name', ':name'))
                ->setParameter('name', '%' . $locationSearchFilterParams->getName() . '%');
        }

        if (!empty($locationSearchFilterParams->getCity())) {
            $q->andWhere($q->expr()->like('location.city', ':city'))
                ->setParameter('city', '%' . $locationSearchFilterParams->getCity() . '%');
        }

        if (!empty($locationSearchFilterParams->getCountryCode())) {
            $q->andWhere('country.countryCode = :countryCode')
                ->setParameter('countryCode', $locationSearchFilterParams->getCountryCode());
        }

        // get predictable sorting
        $q->addOrderBy('location.id');

        return $this->getPaginator($q);
    }

    /**
     * Returns the number of employees in the given location
     *
     * @param int $locationId
     *
     * @return int
     */
    public function getNumberOfEmployeesForLocation(int $locationId): int
    {
        $q = $this->createQueryBuilder(EmpLocations::class, 'el');
        $q->andWhere('el.location = :locationId')
            ->setParameter('locationId', $locationId);

        return $this->count($q);
    }

    /**
     * @return int[]
     */
    public function getLocationsIdList(): array
    {
        $q = $this->createQueryBuilder(Location::class, 'l');
        $q->select('l.id');
        $q->addOrderBy('l.name', ListSorter::ASCENDING);

        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
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
        $locationIds = [];

        if (!empty($empNumbers)) {
            $q = $this->createQueryBuilder(EmpLocations::class, 'empLocation');
            $q->select('IDENTITY(empLocation.location) as locationId')
                ->distinct()
                ->addGroupBy('locationId');
            $q->andWhere($q->expr()->in('empLocation.employee', ':empNumbers'))
                ->setParameter('empNumbers', $empNumbers);

            /** @var EmpLocations[] $locations */
            $locations = $q->getQuery()->execute();

            $locationIds = array_column($locations, 'locationId');
        }

        return $locationIds;
    }

    /**
     * Returns the Locations having the given ids
     *
     * @param int[] $ids
     *
     * @return Location[]
     */
    public function getLocationsByIds(array $ids): array
    {
        $q = $this->createQueryBuilder(Location::class, 'l');
        $q->andWhere($q->expr()->in('l.id', ':ids'))
            ->setParameter('ids', $ids);
        $q->addOrderBy('l.name', ListSorter::ASCENDING);
        return $q->getQuery()->execute();
    }

    /**
     * Save Location into the database
     *
     * @param Location $location
     *
     * @return Location
     */
    public function saveLocation(Location $location): Location
    {
        $this->persist($location);
        return $location;
    }

    /**
     * Deletes the Locations having the given ids
     *
     * @param int[] $toDeleteIds
     *
     * @return int
     */
    public function deleteLocations(array $toDeleteIds): int
    {
        $q = $this->createQueryBuilder(Location::class, 'l');
        $q->delete()
            ->where($q->expr()->in('l.id', ':ids'))
            ->setParameter('ids', $toDeleteIds);

        return $q->getQuery()->execute();
    }
}
