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

use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\JobSpecificationAttachment;
use OrangeHRM\Entity\JobTitle;
use OrangeHRM\ORM\Doctrine;

class JobTitleDao
{
    /**
     * @param string $sortField
     * @param string $sortOrder
     * @param bool $activeOnly
     * @param int|null $limit
     * @param int|null $offset
     * @param false $count
     * @return int|JobTitle[]
     * @throws DaoException
     */
    public function getJobTitleList(
        string $sortField = 'jt.jobTitleName',
        string $sortOrder = 'ASC',
        bool $activeOnly = true,
        ?int $limit = null,
        ?int $offset = null,
        bool $count = false
    ) {
        $sortField = ($sortField == "") ? 'jt.jobTitleName' : $sortField;
        $sortOrder = strcasecmp($sortOrder, 'DESC') === 0 ? 'DESC' : 'ASC';

        try {
            $q = Doctrine::getEntityManager()->getRepository(
                JobTitle::class
            )->createQueryBuilder(
                'jt'
            );
            if ($activeOnly == true) {
                $q->andWhere('jt.isDeleted = :isDeleted');
                $q->setParameter('isDeleted', JobTitle::ACTIVE);
            }
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
     * @param array $toBeDeletedJobTitleIds
     * @return int
     * @throws DaoException
     */
    public function deleteJobTitle(array $toBeDeletedJobTitleIds): int
    {
        try {
            $q = Doctrine::getEntityManager()->createQueryBuilder();
            $q->update(JobTitle::class, 'jt')
                ->set('jt.isDeleted', ':isDeleted')
                ->setParameter('isDeleted', JobTitle::DELETED)
                ->where($q->expr()->in('jt.id', ':ids'))
                ->setParameter('ids', $toBeDeletedJobTitleIds);
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param int $jobTitleId
     * @return JobTitle|null
     * @throws DaoException
     */
    public function getJobTitleById(int $jobTitleId): ?JobTitle
    {
        try {
            $jobTitle = Doctrine::getEntityManager()->getRepository(JobTitle::class)->find(
                $jobTitleId
            );
            if ($jobTitle instanceof JobTitle) {
                return $jobTitle;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param int $attachId
     * @return JobSpecificationAttachment|null
     * @throws DaoException
     */
    public function getJobSpecAttachmentById(int $attachId): ?JobSpecificationAttachment
    {
        try {
            $jobSpecificationAttachment = Doctrine::getEntityManager()->getRepository(
                JobSpecificationAttachment::class
            )->find($attachId);
            if ($jobSpecificationAttachment instanceof JobSpecificationAttachment) {
                return $jobSpecificationAttachment;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param JobTitle $jobTitle
     * @return JobTitle
     * @throws DaoException
     */
    public function saveJobTitle(JobTitle $jobTitle): JobTitle
    {
        try {
            Doctrine::getEntityManager()->persist($jobTitle);
            Doctrine::getEntityManager()->flush();
            return $jobTitle;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param JobSpecificationAttachment $jobSpecificationAttachment
     * @return JobSpecificationAttachment
     * @throws DaoException
     */
    public function saveJobSpecificationAttachment(
        JobSpecificationAttachment $jobSpecificationAttachment
    ): JobSpecificationAttachment {
        try {
            Doctrine::getEntityManager()->persist($jobSpecificationAttachment);
            Doctrine::getEntityManager()->flush();
            return $jobSpecificationAttachment;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param JobSpecificationAttachment $jobSpecificationAttachment
     * @return JobSpecificationAttachment
     * @throws DaoException
     */
    public function deleteJobSpecificationAttachment(
        JobSpecificationAttachment $jobSpecificationAttachment
    ): JobSpecificationAttachment {
        try {
            Doctrine::getEntityManager()->remove($jobSpecificationAttachment);
            Doctrine::getEntityManager()->flush();
            return $jobSpecificationAttachment;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
}
