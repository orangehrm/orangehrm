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
use OrangeHRM\Admin\Dto\NationalitySearchFilterParams;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Nationality;
use OrangeHRM\ORM\Paginator;

class NationalityDao extends BaseDao
{
    /**
     * @param Nationality $nationality
     * @return Nationality
     * @throws DaoException
     */
    public function saveNationality(Nationality $nationality): Nationality
    {
        try {
            $this->persist($nationality);
            return $nationality;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param NationalitySearchFilterParams $nationalitySearchFilterParams
     * @return array
     * @throws DaoException
     */
    public function getNationalityList(NationalitySearchFilterParams $nationalitySearchFilterParams): array
    {
        try {
            $paginator = $this->getNationalityListPaginator($nationalitySearchFilterParams);
            return $paginator->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param NationalitySearchFilterParams $nationalitySearchFilterParams
     * @return Paginator
     */
    public function getNationalityListPaginator(
        NationalitySearchFilterParams $nationalitySearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(Nationality::class, 'n');
        $this->setSortingAndPaginationParams($q, $nationalitySearchFilterParams);
        return new Paginator($q);
    }

    /**
     * @param NationalitySearchFilterParams $nationalitySearchFilterParams
     * @return int
     * @throws DaoException
     */
    public function getNationalityCount(NationalitySearchFilterParams $nationalitySearchFilterParams): int
    {
        try {
            $paginator = $this->getNationalityListPaginator($nationalitySearchFilterParams);
            return $paginator->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $id
     * @return Nationality|null
     * @throws DaoException
     */
    public function getNationalityById(int $id): ?Nationality
    {
        try {
            $nationality = $this->getRepository(Nationality::class)->find($id);
            if ($nationality instanceof Nationality) {
                return $nationality;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $name
     * @return Nationality|null
     * @throws DaoException
     */
    public function getNationalityByName(string $name): ?Nationality
    {
        try {
            $query = $this->createQueryBuilder(Nationality::class, 'n');
            $trimmed = trim($name, ' ');
            $query->andWhere('n.name = :name');
            $query->setParameter('name', $trimmed);
            return $query->getQuery()->getOneOrNullResult();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param array $toDeleteIds
     * @return int
     * @throws DaoException
     */
    public function deleteNationalities(array $toDeleteIds): int
    {
        try {
            $q = $this->createQueryBuilder(Nationality::class, 'n');
            $q->delete();
            $q->where($q->expr()->in('n.id', ':ids'))
                ->setParameter('ids', $toDeleteIds);

            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $nationalityName
     * @return bool
     * @throws DaoException
     */
    public function isExistingNationalityName(string $nationalityName): bool
    {
        try {
            $q = $this->createQueryBuilder(Nationality::class, 'n');
            $trimmed = trim($nationalityName, ' ');
            $q->where('n.name = :name');
            $q->setParameter('name', $trimmed);
            $count = $this->count($q);
            if ($count > 0) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
