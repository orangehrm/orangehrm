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
use OrangeHRM\Entity\JobSpecificationAttachment;
use OrangeHRM\Entity\JobTitle;
use OrangeHRM\ORM\Doctrine;

class JobTitleDao
{
    /**
     * @param string $sortField
     * @param string $sortOrder
     * @param bool $activeOnly
     * @param null $limit
     * @param null $offset
     * @param false $count
     * @return int|mixed|string
     * @throws DaoException
     */
    public function getJobTitleList(
        $sortField = 'jt.jobTitleName',
        $sortOrder = 'ASC',
        $activeOnly = true,
        $limit = null,
        $offset = null,
        $count = false
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
     * @param $toBeDeletedJobTitleIds
     * @return int|mixed|string
     * @throws DaoException
     */
    public function deleteJobTitle($toBeDeletedJobTitleIds)
    {
        try {
            $q = Doctrine::getEntityManager()->createQueryBuilder();
            $q->update(JobTitle::class, 'jt')
                ->set('jt.isDeleted', JobTitle::DELETED)
                ->add('where', $q->expr()->in('jt.id', $toBeDeletedJobTitleIds));
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param $jobTitleId
     * @return object|null
     * @throws DaoException
     */
    public function getJobTitleById($jobTitleId)
    {
        try {
            return Doctrine::getEntityManager()->getRepository(JobTitle::class)->find(
                $jobTitleId
            );
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param $attachId
     * @return object|null
     * @throws DaoException
     */
    public function getJobSpecAttachmentById($attachId)
    {
        try {
            return Doctrine::getEntityManager()->getRepository(
                JobSpecificationAttachment::class
            )->find(
                $attachId
            );
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
    public function saveJobSpecificationAttachment(JobSpecificationAttachment $jobSpecificationAttachment
    ): JobSpecificationAttachment {
        try {
            Doctrine::getEntityManager()->persist($jobSpecificationAttachment);
            Doctrine::getEntityManager()->flush();
            return $jobSpecificationAttachment;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
}
