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
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Entity\Reviewer;
use OrangeHRM\ORM\QueryBuilderWrapper;
use OrangeHRM\Performance\Dto\PerformanceReviewSearchFilterParams;

class PerformanceReviewDao extends BaseDao
{
    use AuthUserTrait;

    /**
     * @param PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams
     * @return PerformanceReview[]
     */
    public function getPerformanceReviewList(PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams): array
    {
        $qb = $this->getPerformanceReviewQueryBuilderWrapper($performanceReviewSearchFilterParams)->getQueryBuilder();
        return $qb->getQuery()->execute();
    }

    /**
     * @param PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams
     * @return int
     */
    public function getPerformanceReviewCount(PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams): int
    {
        $qb = $this->getPerformanceReviewQueryBuilderWrapper($performanceReviewSearchFilterParams)->getQueryBuilder();
        return $this->getPaginator($qb)->count();
    }

    /**
     * @param PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams
     * @return QueryBuilderWrapper
     */
    private function getPerformanceReviewQueryBuilderWrapper(PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams): QueryBuilderWrapper
    {
        $qb = $this->createQueryBuilder(PerformanceReview::class, 'performanceReview');
        $qb->leftJoin('performanceReview.employee', 'employee');
        $qb->andWhere($qb->expr()->in('performanceReview.id', ':performanceReviewIds'))
            ->setParameter(
                'performanceReviewIds',
                $this->getReviewIdsBySupervisorId($this->getAuthUser()->getEmpNumber())
            );

        if (!is_null($performanceReviewSearchFilterParams->getEmpNumber())) {
            $qb->andWhere($qb->expr()->eq('performanceReview.employee', ':empNumber'))
                ->setParameter('empNumber', $performanceReviewSearchFilterParams->getEmpNumber());
        }

        if (!is_null($performanceReviewSearchFilterParams->getStatusId())) {
            $qb->andWhere($qb->expr()->eq('performanceReview.statusId', ':statusId'))
                ->setParameter('statusId', $performanceReviewSearchFilterParams->getStatusId());
        } else {
            $qb->andWhere($qb->expr()->neq('performanceReview.statusId', ':statusId'))
                ->setParameter('statusId', 1);
        }

        if (!is_null($performanceReviewSearchFilterParams->getFromDate())) {
            $qb->andWhere($qb->expr()->gte('performanceReview.dueDate', ':fromDate'))
                ->setParameter('fromDate', $performanceReviewSearchFilterParams->getFromDate());
        }

        if (!is_null($performanceReviewSearchFilterParams->getToDate())) {
            $qb->andWhere($qb->expr()->lte('performanceReview.dueDate', ':toDate'))
                ->setParameter('toDate', $performanceReviewSearchFilterParams->getToDate());
        }

        if (!is_null($performanceReviewSearchFilterParams->getJobTitleId())) {
            $qb->leftJoin('performanceReview.jobTitle', 'jobTitle');
            $qb->andWhere($qb->expr()->eq('jobTitle.id', ':jobTitleId'))
                ->setParameter('jobTitleId', $performanceReviewSearchFilterParams->getJobTitleId());
        }

        if (!is_null($performanceReviewSearchFilterParams->getSubUnitId())) {
            $qb->leftJoin('performanceReview.department', 'subUnit');
            $qb->andWhere($qb->expr()->eq('subUnit.id', ':subUnitId'))
                ->setParameter('subUnitId', $performanceReviewSearchFilterParams->getSubUnitId());
        }

        $qb->andWhere($qb->expr()->isNull('employee.purgedAt'));

        $this->setSortingAndPaginationParams($qb, $performanceReviewSearchFilterParams);
        $qb->addOrderBy('performanceReview.dueDate');
        $qb->addOrderBy('employee.firstName');
        return $this->getQueryBuilderWrapper($qb);
    }

    /**
     * TODO move to reviewer Dao
     * @param int $supervisorId
     * @return array
     */
    public function getReviewIdsBySupervisorId(int $supervisorId): array
    {
        $qb = $this->createQueryBuilder(Reviewer::class, 'reviewer');
        $qb->leftJoin('reviewer.group', 'reviewerGroup');
        $qb->andWhere($qb->expr()->eq('reviewerGroup.name', ':groupName'))
            ->setParameter('groupName', 'Supervisor');
        $qb->andWhere($qb->expr()->eq('reviewer.employee', ':supervisorId'))
            ->setParameter('supervisorId', $supervisorId);

        $reviewerArray = $qb->getQuery()->execute();

        return array_map(function ($reviewer) {
            /** @var Reviewer $reviewer */
            return $reviewer->getReview()->getId();
        }, $reviewerArray);
    }
}
