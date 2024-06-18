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

namespace OrangeHRM\Performance\Dao;

use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\PerformanceTracker;
use OrangeHRM\Entity\PerformanceTrackerLog;
use OrangeHRM\Entity\PerformanceTrackerReviewer;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\QueryBuilderWrapper;
use OrangeHRM\Performance\Dto\EmployeeTrackerSearchFilterParams;
use OrangeHRM\Performance\Dto\PerformanceTrackerSearchFilterParams;

class PerformanceTrackerDao extends BaseDao
{
    use DateTimeHelperTrait;

    /**
     * Retrieve PerformanceTrack by performanceTrackId, must make this retrieve domain object
     * @param int $performanceTrackId
     * @return PerformanceTracker|null
     */
    public function getPerformanceTracker(int $performanceTrackId): ?PerformanceTracker
    {
        $performanceTracker = $this->getRepository(PerformanceTracker::class)->findOneBy(['id' => $performanceTrackId ,'status' => 1]);
        if ($performanceTracker instanceof PerformanceTracker) {
            return $performanceTracker;
        }
        return null;
    }

    /**
     * @param int[] $ids
     * @return int[]
     */
    public function getExistingPerformanceTrackerIds(array $ids): array
    {
        $qb = $this->createQueryBuilder(PerformanceTracker::class, 'performanceTracker');

        $qb->select('performanceTracker.id')
            ->andWhere($qb->expr()->in('performanceTracker.id', ':ids'))
            ->andWhere($qb->expr()->eq('performanceTracker.status', ':status'))
            ->setParameter('ids', $ids)
            ->setParameter('status', PerformanceTracker::STATUS_TRACKER_NOT_DELETED);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param PerformanceTrackerSearchFilterParams $performanceTrackerSearchFilterParams
     * @return PerformanceTracker[]
     */
    public function getPerformanceTrackList(PerformanceTrackerSearchFilterParams $performanceTrackerSearchFilterParams): array
    {
        $query = $this->getPerformanceTrackerQueryBuilderWrapper($performanceTrackerSearchFilterParams)->getQueryBuilder();
        return $query->getQuery()->execute();
    }

    /**
     * @param PerformanceTrackerSearchFilterParams $performanceTrackerSearchFilterParams
     * @return int
     */
    public function getPerformanceTrackerCount(PerformanceTrackerSearchFilterParams $performanceTrackerSearchFilterParams): int
    {
        $query = $this->getPerformanceTrackerQueryBuilderWrapper($performanceTrackerSearchFilterParams)->getQueryBuilder();
        return $this->getPaginator($query)->count();
    }

    /**
     * @param PerformanceTracker $performanceTracker
     * @param array $reviewerEmpNumbers
     * @return PerformanceTracker
     * @throws TransactionException
     */
    public function savePerformanceTracker(PerformanceTracker $performanceTracker, array $reviewerEmpNumbers): PerformanceTracker
    {
        $this->beginTransaction();
        try {
            $this->persist($performanceTracker);
            if (count($reviewerEmpNumbers) > 0) {
                $this->savePerformanceTrackerReviewers($reviewerEmpNumbers, $performanceTracker);
            }
            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }
        return $performanceTracker;
    }

    /**
     * @param array $reviewers
     * @param PerformanceTracker $performanceTracker
     * @return void
     */
    private function savePerformanceTrackerReviewers(array $reviewers, PerformanceTracker $performanceTracker)
    {
        foreach ($reviewers as $reviewer) {
            $performanceTrackerReviewer = new PerformanceTrackerReviewer();
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
        if (!is_null($performanceTrackerSearchFilterParams->getEmpNumber())) {
            $q->andWhere('employee.empNumber = :employeeNumber')
                ->setParameter('employeeNumber', $performanceTrackerSearchFilterParams->getEmpNumber());
        }
        $q->andWhere('performanceTracker.status = :status')
            ->setParameter('status', PerformanceTracker::STATUS_TRACKER_NOT_DELETED);
        $this->setSortingAndPaginationParams($q, $performanceTrackerSearchFilterParams);
        $q->addOrderBy('performanceTracker.addedDate', ListSorter::DESCENDING);
        return $this->getQueryBuilderWrapper($q);
    }

    /**
     * @param array $toDeleteIds
     * @return int
     */
    public function deletePerformanceTracker(array $toDeleteIds): int
    {
        $q = $this->createQueryBuilder(PerformanceTracker::class, 'performanceTracker');
        $q->update()
            ->set('performanceTracker.status', PerformanceTracker::STATUS_TRACKER_DELETED)
            ->andWhere($q->expr()->in('performanceTracker.id', ':ids'))
            ->setParameter('ids', $toDeleteIds);
        return $q->getQuery()->execute();
    }

    /**
     * @param int $performanceTrackerId
     * @return PerformanceTrackerReviewer[]
     */
    public function getReviewerListByTrackerId(int $performanceTrackerId): array
    {
        $q = $this->createQueryBuilder(PerformanceTrackerReviewer::class, 'ptr');
        $q->andWhere('ptr.performanceTracker = :performanceTracker')
            ->setParameter('performanceTracker', $performanceTrackerId);
        return $q->getQuery()->execute();
    }

    /**
     * @param PerformanceTracker $performanceTracker
     * @param array $reviewerEmpNumbers
     * @return PerformanceTracker
     */
    public function updatePerformanceTracker(PerformanceTracker $performanceTracker, array $reviewerEmpNumbers): PerformanceTracker
    {
        $existingReviewers = $this->getReviewerListByTrackerId($performanceTracker->getId());
        $newReviewerList = [];
        $deletableReviewerList = [];
        foreach ($existingReviewers as $existingReviewer) {
            $reviewerNumber = $existingReviewer->getReviewer()->getEmpNumber();
            if (!in_array($reviewerNumber, $reviewerEmpNumbers)) {
                $deletableReviewerList[] = $reviewerNumber;
            } else {
                $newReviewerList[] = $reviewerNumber;
            }
        }
        $this->deleteExistingReviewers($performanceTracker->getId(), $deletableReviewerList);
        $reviewerList = array_diff($reviewerEmpNumbers, $newReviewerList);
        $updateReviewerList = [];

        foreach ($reviewerList as $reviewer) {
            $updateReviewerList[] = $reviewer;
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

    /**
     * @param EmployeeTrackerSearchFilterParams $employeeTrackerSearchFilterParams
     * @return PerformanceTracker[]
     */
    public function getEmployeeTrackerList(EmployeeTrackerSearchFilterParams $employeeTrackerSearchFilterParams): array
    {
        $qb = $this->getEmployeeTrackerQueryBuilderWrapper($employeeTrackerSearchFilterParams)->getQueryBuilder();
        return $qb->getQuery()->execute();
    }

    /**
     * @param EmployeeTrackerSearchFilterParams $employeeTrackerSearchFilterParams
     * @return int
     */
    public function getEmployeeTrackerCount(EmployeeTrackerSearchFilterParams $employeeTrackerSearchFilterParams): int
    {
        $qb = $this->getEmployeeTrackerQueryBuilderWrapper($employeeTrackerSearchFilterParams)->getQueryBuilder();
        return $this->getPaginator($qb)->count();
    }

    /**
     * @param EmployeeTrackerSearchFilterParams $employeeTrackerSearchFilterParams
     * @return QueryBuilderWrapper
     */
    private function getEmployeeTrackerQueryBuilderWrapper(
        EmployeeTrackerSearchFilterParams $employeeTrackerSearchFilterParams
    ): QueryBuilderWrapper {
        $qb = $this->createQueryBuilder(PerformanceTracker::class, 'tracker');
        $qb->leftJoin('tracker.employee', 'employee');

        if (!is_null($employeeTrackerSearchFilterParams->getEmpNumber())) {
            $qb->andWhere($qb->expr()->eq('employee.empNumber', ':empNumber'))
                ->setParameter('empNumber', $employeeTrackerSearchFilterParams->getEmpNumber());
        }

        if (!is_null($employeeTrackerSearchFilterParams->getTrackerIds())) {
            $qb->andWhere($qb->expr()->in('tracker.id', ':trackerIds'))
                ->setParameter('trackerIds', $employeeTrackerSearchFilterParams->getTrackerIds());
        }

        if ($employeeTrackerSearchFilterParams->getIncludeEmployees(
        ) === EmployeeTrackerSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_CURRENT) {
            $qb->andWhere($qb->expr()->isNull('employee.employeeTerminationRecord'));
        } elseif ($employeeTrackerSearchFilterParams->getIncludeEmployees(
        ) === EmployeeTrackerSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_PAST) {
            $qb->andWhere($qb->expr()->isNotNull('employee.employeeTerminationRecord'));
        }

        $qb->andWhere($qb->expr()->eq('tracker.status', ':status'))
            ->setParameter('status', PerformanceTracker::STATUS_TRACKER_NOT_DELETED);
        $qb->andWhere($qb->expr()->isNull('employee.purgedAt'));
        $this->setSortingAndPaginationParams($qb, $employeeTrackerSearchFilterParams);
        return $this->getQueryBuilderWrapper($qb);
    }

    /**
     * @return array
     */
    public function getPerformanceTrackerIdList(): array
    {
        $qb = $this->createQueryBuilder(PerformanceTracker::class, 'performanceTracker');
        $qb->select('performanceTracker.id');
        return array_column($qb->getQuery()->getArrayResult(), 'id');
    }

    /**
     * @param int $reviewerId
     * @return int[]
     */
    public function getTrackerIdsByReviewerId(int $reviewerId): array
    {
        $qb = $this->createQueryBuilder(PerformanceTrackerReviewer::class, 'trackerReviewer');
        $qb->andWhere($qb->expr()->eq('trackerReviewer.reviewer', ':empNumber'))
            ->setParameter('empNumber', $reviewerId);

        $trackerReviewList = $qb->getQuery()->execute();

        return array_map(function ($trackerReviewer) {
            /** @var PerformanceTrackerReviewer $trackerReviewer */
            return $trackerReviewer->getPerformanceTracker()->getId();
        }, $trackerReviewList);
    }

    /**
     * @param int $empNumber
     * @return int[]
     */
    public function getTrackerIdsByEmpNumber(int $empNumber): array
    {
        $qb = $this->createQueryBuilder(PerformanceTracker::class, 'performanceTracker');
        $qb->select('performanceTracker.id');
        $qb->andWhere($qb->expr()->eq('performanceTracker.employee', ':empNumber'))
            ->setParameter('empNumber', $empNumber);

        return array_column($qb->getQuery()->getArrayResult(), 'id');
    }

    /**
     * @param int $reviewerId
     * @return int[]
     */
    public function getEmployeeIdsByReviewerId(int $reviewerId): array
    {
        $qb = $this->createQueryBuilder(PerformanceTrackerReviewer::class, 'trackerReviewer');
        $qb->leftJoin('trackerReviewer.performanceTracker', 'tracker');
        $qb->leftJoin('tracker.employee', 'employee');
        $qb->andWhere($qb->expr()->eq('trackerReviewer.reviewer', ':empNumber'))
            ->setParameter('empNumber', $reviewerId);
        $qb->andWhere($qb->expr()->isNull('employee.purgedAt'));

        $trackerReviewList = $qb->getQuery()->execute();

        return array_map(function ($trackerReviewer) {
            /** @var PerformanceTrackerReviewer $trackerReviewer */
            return $trackerReviewer->getPerformanceTracker()->getEmployee()->getEmpNumber();
        }, $trackerReviewList);
    }

    /**
     * @param int|null $empNumber
     * @return bool
     */
    public function isTrackerReviewer(?int $empNumber): bool
    {
        if (is_null($empNumber)) {
            return false;
        }
        $qb = $this->createQueryBuilder(PerformanceTrackerReviewer::class, 'trackerReviewer');
        $qb->andWhere($qb->expr()->eq('trackerReviewer.reviewer', ':empNumber'))
            ->setParameter('empNumber', $empNumber);

        $qb->leftJoin('trackerReviewer.performanceTracker', 'performanceTracker');
        $qb->andWhere($qb->expr()->eq('performanceTracker.status', ':status'))
            ->setParameter('status', PerformanceTracker::STATUS_TRACKER_NOT_DELETED);
        return $this->getPaginator($qb)->count() > 0;
    }

    /**
     * @param int $trackerId
     * @return bool
     */
    public function isTrackerOwnerEditable(int $trackerId): bool
    {
        $q = $this->createQueryBuilder(PerformanceTrackerLog::class, 'ptrLog');
        $q->andWhere('ptrLog.performanceTracker = :trackerId')
            ->setParameter('trackerId', $trackerId)
            ->andWhere('ptrLog.status = :notDeletedStatus')
            ->setParameter('notDeletedStatus', PerformanceTrackerLog::STATUS_NOT_DELETED);
        return $this->getPaginator($q)->count() == 0;
    }
}
