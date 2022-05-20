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

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\PerformanceTracker;
use OrangeHRM\Entity\PerformanceTrackerReviewer;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\ORM\QueryBuilderWrapper;
use OrangeHRM\Performance\Dto\PerformanceTrackerReviewerSearchFilterParams;
use OrangeHRM\Performance\Dto\PerformanceTrackerSearchFilterParams;
use PHPUnit\Exception;

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
        $performanceTracker = $this->getRepository(PerformanceTracker::class)->findOneBy(['id' => $performanceTrackId ,'status' => 1]);
        if ($performanceTracker instanceof PerformanceTracker) {
            return $performanceTracker;
        }
        return null;
    }

    /**
     * @param PerformanceTrackerSearchFilterParams $performanceTrackerSearchFilterParams
     * @return array
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
     * @param array $reviewers
     * @return PerformanceTracker
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
        }catch (Exception $e){
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
        $this->setSortingAndPaginationParams($q, $performanceTrackerSearchFilterParams);

        if (!is_null($performanceTrackerSearchFilterParams->getEmpNumber())) {
            $q->andWhere('employee.empNumber = :employeeNumber')
                ->setParameter('employeeNumber', $performanceTrackerSearchFilterParams->getEmpNumber());
        }
        $q->andWhere('performanceTracker.status = :status')
            ->setParameter('status', PerformanceTracker::STATUS_NOT_DELETED);
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

    public function getReviewerList(PerformanceTrackerReviewerSearchFilterParams $performanceTrackerReviewerSearchFilterParams)
    {
        $q = $this->createQueryBuilder(Employee::class, 'employee');
        $q->select();
        $this->setSortingAndPaginationParams($q, $performanceTrackerReviewerSearchFilterParams);
        if (!is_null($performanceTrackerReviewerSearchFilterParams->getTrackerEmpNumber())) {
            $q->andWhere('employee.empNumber != :excludeEmployee')
                ->setParameter('excludeEmployee', $performanceTrackerReviewerSearchFilterParams->getTrackerEmpNumber());
        }
        if (!is_null($performanceTrackerReviewerSearchFilterParams->getNameOrId())) {
            $q->andWhere(
                $q->expr()->orX(
                    $q->expr()->like('employee.firstName', ':nameOrId'),
                    $q->expr()->like('employee.lastName', ':nameOrId'),
                    $q->expr()->like('employee.middleName', ':nameOrId'),
                    $q->expr()->like('employee.employeeId', ':nameOrId'),
                )
            );
            $q->setParameter('nameOrId', '%' . $performanceTrackerReviewerSearchFilterParams->getNameOrId() . '%');
        }
        $q->andWhere($q->expr()->isNull('employee.employeeTerminationRecord'));
        return $q->getQuery()->execute();
    }
}
