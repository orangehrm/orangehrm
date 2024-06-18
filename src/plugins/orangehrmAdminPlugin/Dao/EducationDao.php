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

use OrangeHRM\Admin\Dto\QualificationEducationSearchFilterParams;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Education;
use OrangeHRM\ORM\Paginator;

class EducationDao extends BaseDao
{
    /**
     * @param Education $education
     * @return Education
     */
    public function saveEducation(Education $education): Education
    {
        $this->persist($education);
        return $education;
    }

    /**
     * get education names according to the Id
     * @param int $id
     * @return Education|null
     */
    public function getEducationById(int $id): ?Education
    {
        $education = $this->getRepository(Education::class)->find($id);
        if ($education instanceof Education) {
            return $education;
        }
        return null;
    }

    /**
     * @param int[] $ids
     * @return int[]
     */
    public function getExistingEducationIds(array $ids): array
    {
        $qb = $this->createQueryBuilder(Education::class, 'education');
        $qb->select('education.id')
            ->andWhere($qb->expr()->in('education.id', ':ids'))
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param string $name
     * @return Education|null
     */
    public function getEducationByName(string $name): ?Education
    {
        $query = $this->createQueryBuilder(Education::class, 'e');
        $trimmed = trim($name, ' ');
        $query->andWhere('e.name = :name');
        $query->setParameter('name', $trimmed);
        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * @param QualificationEducationSearchFilterParams $educationSearchFilterParams
     * @return array
     */
    public function getEducationList(QualificationEducationSearchFilterParams $educationSearchFilterParams): array
    {
        $paginator = $this->getEducationListPaginator($educationSearchFilterParams);
        return $paginator->getQuery()->execute();
    }

    /**
     * @param QualificationEducationSearchFilterParams $educationSearchFilterParams
     * @return Paginator
     */
    public function getEducationListPaginator(
        QualificationEducationSearchFilterParams $educationSearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(Education::class, 'e');
        $this->setSortingAndPaginationParams($q, $educationSearchFilterParams);
        return new Paginator($q);
    }

    /**
     * @param QualificationEducationSearchFilterParams $educationSearchFilterParams
     * @return int
     */
    public function getEducationCount(QualificationEducationSearchFilterParams $educationSearchFilterParams): int
    {
        $paginator = $this->getEducationListPaginator($educationSearchFilterParams);
        return $paginator->count();
    }

    /**
     * Soft Delete Education field
     * @param array $toDeleteIds
     * @return int
     */
    public function deleteEducations(array $toDeleteIds): int
    {
        $q = $this->createQueryBuilder(Education::class, 'e');
        $q->delete()
            ->where($q->expr()->in('e.id', ':ids'))
            ->setParameter('ids', $toDeleteIds);
        return $q->getQuery()->execute();
    }

    /**
     * @param string $educationName
     * @return bool
     */
    public function isExistingEducationName(string $educationName): bool
    {
        $q = $this->createQueryBuilder(Education::class, 'e');
        $trimmed = trim($educationName, ' ');
        $q->where('e.name = :name');
        $q->setParameter('name', $trimmed);
        $count = $this->count($q);
        if ($count > 0) {
            return true;
        }
        return false;
    }
}
