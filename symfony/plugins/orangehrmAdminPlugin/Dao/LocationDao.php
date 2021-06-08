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

namespace OrangeHRM\Admin\Dao;

use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\EmpLocations;
use OrangeHRM\Entity\Location;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Pim\Dto\LocationSearchFilterParams;

class LocationDao extends BaseDao
{
    /**
     * @param int $locationId
     * @return Location|null
     * @throws DaoException
     */
    public function getLocationById(int $locationId): ?Location
    {
        try {
            return $this->getRepository(Location::class)->find($locationId);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param LocationSearchFilterParams $locationSearchFilterParams
     * @return int
     * @throws DaoException
     */
    public function getSearchLocationListCount(LocationSearchFilterParams $locationSearchFilterParams): int
    {
        try {
            return $this->searchLocationsPaginator($locationSearchFilterParams)->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param LocationSearchFilterParams $locationSearchFilterParams
     * @return Location[]
     * @throws DaoException
     */
    public function searchLocations(LocationSearchFilterParams $locationSearchFilterParams): array
    {
        try {
            return $this->searchLocationsPaginator($locationSearchFilterParams)->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param LocationSearchFilterParams $locationSearchFilterParams
     * @return Paginator
     */
    private function searchLocationsPaginator(LocationSearchFilterParams $locationSearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(Location::class, 'location');
        $q->leftJoin('location.country', 'country');
        $this->setSortingAndPaginationParams($q, $locationSearchFilterParams);

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

        return $this->getPaginator($q);
    }

    /**
     * @param int $locationId
     * @return int
     */
    public function getNumberOfEmployeesForLocation(int $locationId): int
    {
        try {
            $q = $this->createQueryBuilder(EmpLocations::class, 'el');
            $q->andWhere('el.location = :locationId')
                ->setParameter('locationId', $locationId);

            return $this->count($q);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @return Location[]
     */
    public function getLocationList(): array
    {
        try {
            $q = $this->createQueryBuilder(Location::class, 'l');
            $q->addOrderBy('l.name', ListSorter::ASCENDING);
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Get LocationIds for Employees with the given employee numbers
     *
     * @param int[] $empNumbers Array of employee numbers
     * @return int[] of locationIds of the given employees
     */
    public function getLocationIdsForEmployees(array $empNumbers): array
    {
        try {
            $locationIds = [];

            if (count($empNumbers) > 0) {
                $q = $this->createQueryBuilder(EmpLocations::class, 'el');
                $q->distinct()
                    ->addGroupBy('el.location');
                $q->andWhere($q->expr()->in('el.employee', ':empNumbers'))
                    ->setParameter('empNumbers', $empNumbers);

                /** @var EmpLocations[] $locations */
                $locations = $q->getQuery()->execute();

                foreach ($locations as $location) {
                    $locationIds[] = $location->getLocation()->getId();
                }
            }

            return $locationIds;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param int[] $ids
     * @return Location[]
     * @throws DaoException
     */
    public function getLocationsByIds(array $ids): array
    {
        try {
            $q = $this->createQueryBuilder(Location::class, 'l');
            $q->andWhere($q->expr()->in('l.id', ':ids'))
                ->setParameter('ids', $ids);
            $q->addOrderBy('l.name', ListSorter::ASCENDING);
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
}
