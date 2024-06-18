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

use OrangeHRM\Admin\Dto\NationalitySearchFilterParams;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Nationality;
use OrangeHRM\ORM\Paginator;

class NationalityDao extends BaseDao
{
    /**
     * @param Nationality $nationality
     * @return Nationality
     */
    public function saveNationality(Nationality $nationality): Nationality
    {
        $this->persist($nationality);
        return $nationality;
    }

    /**
     * @param NationalitySearchFilterParams $nationalitySearchFilterParams
     * @return array
     */
    public function getNationalityList(NationalitySearchFilterParams $nationalitySearchFilterParams): array
    {
        $paginator = $this->getNationalityListPaginator($nationalitySearchFilterParams);
        return $paginator->getQuery()->execute();
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
     */
    public function getNationalityCount(NationalitySearchFilterParams $nationalitySearchFilterParams): int
    {
        $paginator = $this->getNationalityListPaginator($nationalitySearchFilterParams);
        return $paginator->count();
    }

    /**
     * @param int $id
     * @return Nationality|null
     */
    public function getNationalityById(int $id): ?Nationality
    {
        $nationality = $this->getRepository(Nationality::class)->find($id);
        if ($nationality instanceof Nationality) {
            return $nationality;
        }
        return null;
    }

    /**
     * @param int[] $ids
     * @return int[]
     */
    public function getExistingNationalityIds(array $ids): array
    {
        $qb = $this->createQueryBuilder(Nationality::class, 'nationality');
        $qb->select('nationality.id')
            ->andWhere($qb->expr()->in('nationality.id', ':ids'))
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param string $name
     * @return Nationality|null
     */
    public function getNationalityByName(string $name): ?Nationality
    {
        $query = $this->createQueryBuilder(Nationality::class, 'n');
        $trimmed = trim($name, ' ');
        $query->andWhere('n.name = :name');
        $query->setParameter('name', $trimmed);
        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * @param array $toDeleteIds
     * @return int
     */
    public function deleteNationalities(array $toDeleteIds): int
    {
        $q = $this->createQueryBuilder(Nationality::class, 'n');
        $q->delete();
        $q->where($q->expr()->in('n.id', ':ids'))
            ->setParameter('ids', $toDeleteIds);

        return $q->getQuery()->execute();
    }

    /**
     * @param string $nationalityName
     * @return bool
     */
    public function isExistingNationalityName(string $nationalityName): bool
    {
        $q = $this->createQueryBuilder(Nationality::class, 'n');
        $trimmed = trim($nationalityName, ' ');
        $q->where('n.name = :name');
        $q->setParameter('name', $trimmed);
        $count = $this->count($q);
        if ($count > 0) {
            return true;
        }
        return false;
    }
}
