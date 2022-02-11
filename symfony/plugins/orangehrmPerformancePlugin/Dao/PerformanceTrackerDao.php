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

namespace OrangeHRM\Performance\Dao;

use Doctrine;
use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\PerformanceTracker;
use OrangeHRM\Entity\PerformanceTrackerReviewer;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\ORM\QueryBuilderWrapper;
use OrangeHRM\Performance\Dto\PerformanceTrackerSearchFilterParams;

/**
 * Description of PerformanceTrackDao
 *
 */
class PerformanceTrackerDao extends BaseDao
{
    use DateTimeHelperTrait;

    /**
     * Retrieve PerformanceTrack by performanceTrackId, must make this retrieve domain object
     * @param int $performanceTrackId
     * @return PerformanceTracker|null
     */
    public function getPerformanceTrack(int $performanceTrackId): ?PerformanceTracker
    {
        $performanceTracker = $this->getRepository(PerformanceTracker::class)->find($performanceTrackId);
        if ($performanceTracker instanceof PerformanceTracker) {
            return $performanceTracker;
        }
        return null;
    }

    /**
     * @param PerformanceTrackerSearchFilterParams $performanceTrackerSearchFilterParams
     * @return array
     * @throws DaoException
     */
    public function getPerformanceTrackList(PerformanceTrackerSearchFilterParams $performanceTrackerSearchFilterParams): array
    {
        try {
            // return $this->getJobTitlesPaginator($jobTitleSearchFilterParams)->getQuery()->execute();
            $query = $this->getPerformanceTrackerQueryBuilderWrapper($performanceTrackerSearchFilterParams)->getQueryBuilder();
            return $query->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param PerformanceTrackerSearchFilterParams $performanceTrackerSearchFilterParams
     * @return int
     * @throws DaoException
     */
    public function getPerformanceTrackerCount(PerformanceTrackerSearchFilterParams $performanceTrackerSearchFilterParams): int
    {
        try {
            $query = $this->getPerformanceTrackerQueryBuilderWrapper($performanceTrackerSearchFilterParams)->getQueryBuilder();
            return $this->getPaginator($query)->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }


    /**
     * @param PerformanceTracker $performanceTracker
     * @return PerformanceTracker
     * @throws DaoException
     * @throws TransactionException
     */
    public function savePerformanceTracker(PerformanceTracker $performanceTracker, array $reviewers): PerformanceTracker
    {
        $this->beginTransaction();
        try {
            $this->persist($performanceTracker);
            if (count($reviewers) > 0) {
                $this->savePerformanceTrackerReviewers($reviewers, $performanceTracker);
            }
            $this->commitTransaction();
            return $performanceTracker;
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }
    }

    /**
     * @throws Doctrine\ORM\ORMException
     */
    public function savePerformanceTrackerReviewers(array $reviewers, PerformanceTracker $performanceTracker)
    {
        foreach ($reviewers as $reviewer) {
            $performanceTrackerReviewer = new PerformanceTrackerReviewer();
            //$employee = $this->getRepository(Employee::class)->find($reviewer);
            $performanceTrackerReviewer->getDecorator()->setReviewerByEmpNumber($reviewer);
            $performanceTrackerReviewer->setPerformanceTracker($performanceTracker);
            $performanceTrackerReviewer->setAddedDate($this->getDateTimeHelper()->getNow());
            $this->getEntityManager()->persist($performanceTrackerReviewer);
        }
        $this->getEntityManager()->flush();
    }

    /**
     * @param PerformanceTrackerSearchFilterParams $performanceTrackerSearchFilterParams
     * @return QueryBuilderWrapper
     */
    private function getPerformanceTrackerQueryBuilderWrapper(PerformanceTrackerSearchFilterParams $performanceTrackerSearchFilterParams): QueryBuilderWrapper
    {
        $q = $this->createQueryBuilder(PerformanceTracker::class, 'performanceTracker');
        $q->leftJoin('performanceTracker.employee', 'employee');
        $this->setSortingAndPaginationParams($q, $performanceTrackerSearchFilterParams);

        if (!is_null($performanceTrackerSearchFilterParams->getEmpNumber())) {
            $q->andWhere('employee.empNumber = :employeeNumber')
                ->setParameter('employeeNumber', $performanceTrackerSearchFilterParams->getEmpNumber());
        }
        return $this->getQueryBuilderWrapper($q);
    }

    /**
     * @param array $toDeleteIds
     * @return int
     */
    public function deletePerformanceTracker(array $toDeleteIds): int
    {
        $q = $this->createQueryBuilder(PerformanceTracker::class, 'performanceTracker');
        $q->delete()
            ->andWhere($q->expr()->in('performanceTracker.id', ':ids'))
            ->setParameter('ids', $toDeleteIds);
        return $q->getQuery()->execute();
    }

    /**
     * @param int $performanceTrackerId
     * @return array
     */
    public function getReviewerListByTrackerId(int $performanceTrackerId): array
    {
        $q = $this->createQueryBuilder(PerformanceTrackerReviewer::class, 'ptr');
        $q->leftJoin('ptr.reviewer', 'ptrR');
        $q->andWhere('ptr.performanceTracker = :performanceTracker')
            ->setParameter('performanceTracker', $performanceTrackerId);
        return $q->getQuery()->execute();
    }

    /**
     * @param PerformanceTracker $performanceTracker
     * @param array $reviewers
     * @return PerformanceTracker
     * @throws Doctrine\ORM\ORMException
     */
    public function updatePerformanceTracker(PerformanceTracker $performanceTracker, array $reviewers): PerformanceTracker
    {
        $existingReviewers = $this->getReviewerListByTrackerId($performanceTracker->getId());
        $newReviewerList = [];
        $deletableReviewerList = [];
        foreach ($existingReviewers as $existingReviewer) {
            $reviewerNumber = $existingReviewer->getReviewer()->getEmpNumber();
            if (!in_array($reviewerNumber, $reviewers)) {
                array_push($deletableReviewerList, $reviewerNumber);
            } else {
                array_push($newReviewerList, $reviewerNumber);
            }
        }
        $this->deleteExistingReviewers($performanceTracker->getId(), $deletableReviewerList);
        $reviewerList = array_diff($reviewers, $newReviewerList);
        $updateReviewerList = [];

        foreach ($reviewerList as $reviewer) {
            array_push($updateReviewerList, $reviewer);
        }
        $this->persist($performanceTracker);
        if (count($updateReviewerList) > 0) {
            $this->savePerformanceTrackerReviewers($updateReviewerList, $performanceTracker);
            return $performanceTracker;

        }
        return $performanceTracker;
    }

    /**
     * @param int $performanceTrackerId
     * @param array $reviewerNumbers
     * @return void
     */
    public function deleteExistingReviewers(int $performanceTrackerId, array $reviewerNumbers): void
    {
        $q = $this->createQueryBuilder(PerformanceTrackerReviewer::class, 'ptr');
        $q->delete()
            ->where('ptr.performanceTracker = :performanceTrackerId')
            ->andWhere($q->expr()->in('ptr.reviewer', ':reviewerNumbers'))
            ->setParameter('performanceTrackerId', $performanceTrackerId)
            ->setParameter('reviewerNumbers', $reviewerNumbers)
            ->getQuery()
            ->execute();
    }

}
