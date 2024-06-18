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

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\JobCategory;

class JobCategoryDao extends BaseDao
{
    /**
     * @param string $sortField
     * @param string $sortOrder
     * @param int|null $limit
     * @param int|null $offset
     * @param false $count
     * @return int|JobCategory[]
     */
    public function getJobCategoryList(
        string $sortField = 'jc.name',
        string $sortOrder = 'ASC',
        ?int $limit = null,
        ?int $offset = null,
        bool $count = false
    ) {
        $sortField = ($sortField == "") ? 'jc.name' : $sortField;
        $sortOrder = strcasecmp($sortOrder, 'DESC') === 0 ? 'DESC' : 'ASC';

        $q = $this->createQueryBuilder(JobCategory::class, 'jc');
        $q->addOrderBy($sortField, $sortOrder);
        if (!empty($limit)) {
            $q->setFirstResult($offset)
                ->setMaxResults($limit);
        }

        if ($count) {
            return $this->count($q);
        }
        return $q->getQuery()->execute();
    }

    /**
     * @param int $jobCatId
     * @return JobCategory|null
     */
    public function getJobCategoryById(int $jobCatId): ?JobCategory
    {
        $jobCategory = $this->getRepository(JobCategory::class)->find($jobCatId);
        if ($jobCategory instanceof JobCategory) {
            return $jobCategory;
        }
        return null;
    }

    /**
     * @param int[] $ids
     * @return int[]
     */
    public function getExistingJobCategoryIds(array $ids): array
    {
        $qb = $this->createQueryBuilder(JobCategory::class, 'jobCategory');
        $qb->select('jobCategory.id')
            ->andWhere($qb->expr()->in('jobCategory.id', ':ids'))
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param JobCategory $jobCategory
     * @return JobCategory
     */
    public function saveJobCategory(JobCategory $jobCategory): JobCategory
    {
        $this->persist($jobCategory);
        return $jobCategory;
    }

    /**
     * @param array $toBeDeletedJobCategoryIds
     * @return int
     */
    public function deleteJobCategory(array $toBeDeletedJobCategoryIds): int
    {
        $q = $this->createQueryBuilder(JobCategory::class, 'jc');
        $q->delete()
            ->where($q->expr()->in('jc.id', ':ids'))
            ->setParameter('ids', $toBeDeletedJobCategoryIds);
        return $q->getQuery()->execute();
    }
}
