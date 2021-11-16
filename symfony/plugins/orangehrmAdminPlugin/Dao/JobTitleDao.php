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
use OrangeHRM\Admin\Dto\JobTitleSearchFilterParams;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\JobSpecificationAttachment;
use OrangeHRM\Entity\JobTitle;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\Paginator;

class JobTitleDao extends BaseDao
{
    /**
     * @param bool $activeOnly
     * @return JobTitle[]
     * @throws DaoException
     */
    public function getJobTitleList(bool $activeOnly = true): array
    {
        try {
            $q = $this->createQueryBuilder(JobTitle::class, 'jt');
            if ($activeOnly == true) {
                $q->andWhere('jt.isDeleted = :isDeleted');
                $q->setParameter('isDeleted', JobTitle::ACTIVE);
            }
            $q->addOrderBy('jt.jobTitleName', ListSorter::ASCENDING);

            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param int $empNumber
     * @return JobTitle[]
     * @throws DaoException
     */
    public function getJobTitlesForEmployee(int $empNumber): array
    {
        try {
            $q = $this->createQueryBuilder(JobTitle::class, 'jt');
            $q->leftJoin('jt.employees', 'e');
            $q->andWhere('jt.isDeleted = :isDeleted')
                ->setParameter('isDeleted', JobTitle::ACTIVE);
            $q->orWhere('e.empNumber = :empNumber')
                ->setParameter('empNumber', $empNumber);

            $q->addOrderBy('jt.jobTitleName', ListSorter::ASCENDING);

            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param JobTitleSearchFilterParams $jobTitleSearchFilterParams
     * @return JobTitle[]
     * @throws DaoException
     */
    public function getJobTitles(JobTitleSearchFilterParams $jobTitleSearchFilterParams): array
    {
        try {
            return $this->getJobTitlesPaginator($jobTitleSearchFilterParams)->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param JobTitleSearchFilterParams $jobTitleSearchFilterParams
     * @return int
     * @throws DaoException
     */
    public function getJobTitlesCount(JobTitleSearchFilterParams $jobTitleSearchFilterParams): int
    {
        try {
            return $this->getJobTitlesPaginator($jobTitleSearchFilterParams)->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param JobTitleSearchFilterParams $jobTitleSearchFilterParams
     * @return Paginator
     */
    private function getJobTitlesPaginator(
        JobTitleSearchFilterParams $jobTitleSearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(JobTitle::class, 'jt');
        $this->setSortingAndPaginationParams($q, $jobTitleSearchFilterParams);
        if ($jobTitleSearchFilterParams->getActiveOnly() == true) {
            $q->andWhere('jt.isDeleted = :isDeleted');
            $q->setParameter('isDeleted', JobTitle::ACTIVE);
        }
        return $this->getPaginator($q);
    }

    /**
     * @param array $toBeDeletedJobTitleIds
     * @return int
     * @throws DaoException
     */
    public function deleteJobTitle(array $toBeDeletedJobTitleIds): int
    {
        try {
            $q = $this->createQueryBuilder(JobTitle::class, 'jt');
            $q->update()
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
            $jobTitle = $this->getRepository(JobTitle::class)->find($jobTitleId);
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
            $jobSpecificationAttachment = $this->getRepository(JobSpecificationAttachment::class)->find($attachId);
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
            $this->persist($jobTitle);
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
            $this->persist($jobSpecificationAttachment);
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
            $this->remove($jobSpecificationAttachment);
            return $jobSpecificationAttachment;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param int $jobTitleId
     * @return JobSpecificationAttachment|null
     * @throws DaoException
     */
    public function getJobSpecificationByJobTitleId(int $jobTitleId): ?JobSpecificationAttachment
    {
        try {
            $q = $this->createQueryBuilder(JobSpecificationAttachment::class, 'js');
            $q->andWhere('js.jobTitle = :jobTitleId')
                ->setParameter('jobTitleId', $jobTitleId);

            return $this->fetchOne($q);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
