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

use OrangeHRM\Admin\Dto\PartialJobSpecificationAttachment;
use OrangeHRM\Admin\Dto\JobTitleSearchFilterParams;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\JobSpecificationAttachment;
use OrangeHRM\Entity\JobTitle;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\Paginator;

class JobTitleDao extends BaseDao
{
    /**
     * @param bool $activeOnly
     * @return JobTitle[]
     */
    public function getJobTitleList(bool $activeOnly = true): array
    {
        $q = $this->createQueryBuilder(JobTitle::class, 'jt');
        if ($activeOnly == true) {
            $q->andWhere('jt.isDeleted = :isDeleted');
            $q->setParameter('isDeleted', JobTitle::ACTIVE);
        }
        $q->addOrderBy('jt.jobTitleName', ListSorter::ASCENDING);

        return $q->getQuery()->execute();
    }

    /**
     * @param int $empNumber
     * @return JobTitle[]
     */
    public function getJobTitlesForEmployee(int $empNumber): array
    {
        $q = $this->createQueryBuilder(JobTitle::class, 'jt');
        $q->leftJoin('jt.employees', 'e');
        $q->andWhere('jt.isDeleted = :isDeleted')
            ->setParameter('isDeleted', JobTitle::ACTIVE);
        $q->orWhere('e.empNumber = :empNumber')
            ->setParameter('empNumber', $empNumber);

        $q->addOrderBy('jt.jobTitleName', ListSorter::ASCENDING);

        return $q->getQuery()->execute();
    }

    /**
     * @param JobTitleSearchFilterParams $jobTitleSearchFilterParams
     * @return JobTitle[]
     */
    public function getJobTitles(JobTitleSearchFilterParams $jobTitleSearchFilterParams): array
    {
        return $this->getJobTitlesPaginator($jobTitleSearchFilterParams)->getQuery()->execute();
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
     * @param JobTitleSearchFilterParams $jobTitleSearchFilterParams
     * @return int
     */
    public function getJobTitlesCount(JobTitleSearchFilterParams $jobTitleSearchFilterParams): int
    {
        return $this->getJobTitlesPaginator($jobTitleSearchFilterParams)->count();
    }

    /**
     * @param array $toBeDeletedJobTitleIds
     * @return int
     */
    public function deleteJobTitle(array $toBeDeletedJobTitleIds): int
    {
        $q = $this->createQueryBuilder(JobTitle::class, 'jt');
        $q->update()
            ->set('jt.isDeleted', ':isDeleted')
            ->setParameter('isDeleted', JobTitle::DELETED)
            ->where($q->expr()->in('jt.id', ':ids'))
            ->setParameter('ids', $toBeDeletedJobTitleIds);
        return $q->getQuery()->execute();
    }

    /**
     * @param int $jobTitleId
     * @return JobTitle|null
     */
    public function getJobTitleById(int $jobTitleId): ?JobTitle
    {
        $jobTitle = $this->getRepository(JobTitle::class)->find($jobTitleId);
        if ($jobTitle instanceof JobTitle) {
            return $jobTitle;
        }
        return null;
    }

    /**
     * @param int[] $ids
     * @return int[]
     */
    public function getExistingJobTitleIds(array $ids): array
    {
        $qb = $this->createQueryBuilder(JobTitle::class, 'jobTitle');
        $qb->select('jobTitle.id')
            ->andWhere($qb->expr()->in('jobTitle.id', ':ids'))
            ->andWhere($qb->expr()->eq('jobTitle.isDeleted', ':deleted'))
            ->setParameter('ids', $ids)
            ->setParameter('deleted', false);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param int $attachId
     * @return JobSpecificationAttachment|null
     */
    public function getJobSpecAttachmentById(int $attachId): ?JobSpecificationAttachment
    {
        $jobSpecificationAttachment = $this->getRepository(JobSpecificationAttachment::class)->find($attachId);
        if ($jobSpecificationAttachment instanceof JobSpecificationAttachment) {
            return $jobSpecificationAttachment;
        }
        return null;
    }

    /**
     * @param JobTitle $jobTitle
     * @return JobTitle
     */
    public function saveJobTitle(JobTitle $jobTitle): JobTitle
    {
        $this->persist($jobTitle);
        return $jobTitle;
    }

    /**
     * @param JobSpecificationAttachment $jobSpecificationAttachment
     * @return JobSpecificationAttachment
     */
    public function saveJobSpecificationAttachment(
        JobSpecificationAttachment $jobSpecificationAttachment
    ): JobSpecificationAttachment {
        $this->persist($jobSpecificationAttachment);
        return $jobSpecificationAttachment;
    }

    /**
     * @param JobSpecificationAttachment $jobSpecificationAttachment
     * @return JobSpecificationAttachment
     */
    public function deleteJobSpecificationAttachment(
        JobSpecificationAttachment $jobSpecificationAttachment
    ): JobSpecificationAttachment {
        $this->remove($jobSpecificationAttachment);
        return $jobSpecificationAttachment;
    }

    /**
     * @param int $jobTitleId
     * @return PartialJobSpecificationAttachment|null
     */
    public function getJobSpecificationByJobTitleId(int $jobTitleId): ?PartialJobSpecificationAttachment
    {
        $select = 'NEW ' . PartialJobSpecificationAttachment::class
            . "(js.id,js.fileName,js.fileType,js.fileSize,IDENTITY(js.jobTitle))";
        $q = $this->createQueryBuilder(JobSpecificationAttachment::class, 'js');
        $q->select($select);
        $q->andWhere('js.jobTitle = :jobTitleId')
            ->setParameter('jobTitleId', $jobTitleId);
        return $q->getQuery()->getOneOrNullResult();
    }
}
