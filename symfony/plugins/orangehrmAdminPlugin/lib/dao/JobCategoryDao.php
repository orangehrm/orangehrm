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

use Doctrine\ORM\Tools\Pagination\Paginator;
use OrangeHRM\Entity\JobCategory;
use OrangeHRM\ORM\Doctrine;

class JobCategoryDao
{
    /**
     * @param string $sortField
     * @param string $sortOrder
     * @param null $limit
     * @param null $offset
     * @param false $count
     * @return int|mixed|string
     * @throws DaoException
     */
    public function getJobCategoryList(
        $sortField = 'jc.name',
        $sortOrder = 'ASC',
        $limit = null,
        $offset = null,
        $count = false
    ) {
        $sortField = ($sortField == "") ? 'jc.name' : $sortField;
        $sortOrder = strcasecmp($sortOrder, 'DESC') === 0 ? 'DESC' : 'ASC';

        try {
            $q = Doctrine::getEntityManager()->getRepository(JobCategory::class)->createQueryBuilder('jc');
            $q->addOrderBy($sortField, $sortOrder);
            if (!empty($limit)) {
                $q->setFirstResult($offset)
                    ->setMaxResults($limit);
            }

            if ($count) {
                $paginator = new Paginator($q, true);
                return count($paginator);
            }
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param int $jobCatId
     * @return object|null
     * @throws DaoException
     */
    public function getJobCategoryById(int $jobCatId)
    {
        try {
            return Doctrine::getEntityManager()->getRepository(JobCategory::class)->find($jobCatId);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param JobCategory $jobCategory
     * @return JobCategory
     * @throws DaoException
     */
    public function saveJobCategory(JobCategory $jobCategory): JobCategory
    {
        try {
            Doctrine::getEntityManager()->persist($jobCategory);
            Doctrine::getEntityManager()->flush();
            return $jobCategory;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param array $toBeDeletedJobCategoryIds
     * @return int|mixed|string
     * @throws DaoException
     */
    public function deleteJobCategory(array $toBeDeletedJobCategoryIds)
    {
        try {
            $q = Doctrine::getEntityManager()->createQueryBuilder();
            $q->delete(JobCategory::class, 'jc')
                ->where($q->expr()->in('jc.id', $toBeDeletedJobCategoryIds));
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
}
