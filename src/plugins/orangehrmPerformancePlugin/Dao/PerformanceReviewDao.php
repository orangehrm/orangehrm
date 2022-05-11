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
use OrangeHRM\Performance\Dto\ReviewListSearchFilterParams;

class PerformanceReviewDao extends BaseDao
{
    use AuthUserTrait;

    /**
     * @param ReviewListSearchFilterParams $reviewSearchFilterParams
     * @return PerformanceReview[]
     */
    public function getReviewList(ReviewListSearchFilterParams $reviewSearchFilterParams): array
    {
        $qb = $this->getReviewListQueryBuilderWrapper($reviewSearchFilterParams)->getQueryBuilder();
        return $qb->getQuery()->execute();
    }

    /**
     * @param ReviewListSearchFilterParams $reviewSearchFilterParams
     * @return int
     */
    public function getReviewListCount(ReviewListSearchFilterParams $reviewSearchFilterParams): int
    {
        $qb = $this->getReviewListQueryBuilderWrapper($reviewSearchFilterParams)->getQueryBuilder();
        return $this->getPaginator($qb)->count();
    }

    /**
     * @param ReviewListSearchFilterParams $reviewSearchFilterParams
     * @return QueryBuilderWrapper
     */
    private function getReviewListQueryBuilderWrapper(ReviewListSearchFilterParams $reviewSearchFilterParams): QueryBuilderWrapper
    {
        $qb = $this->createQueryBuilder(PerformanceReview::class, 'performanceReview');
        $qb->leftJoin('performanceReview.employee', 'employee');
        $qb->andWhere($qb->expr()->in('performanceReview.id', ':performanceReviewIds'))
            ->setParameter(
                'performanceReviewIds',
                $this->getReviewIdsBySupervisorId($this->getAuthUser()->getEmpNumber())
            );

        if (!is_null($reviewSearchFilterParams->getEmpNumber())) {
            $qb->andWhere($qb->expr()->eq('performanceReview.employee', ':empNumber'))
                ->setParameter('empNumber', $reviewSearchFilterParams->getEmpNumber());
        }

        if (!is_null($reviewSearchFilterParams->getStatusId())) {
            $qb->andWhere($qb->expr()->eq('performanceReview.statusId', ':statusId'))
                ->setParameter('statusId', $reviewSearchFilterParams->getStatusId());
        } else {
            $qb->andWhere($qb->expr()->neq('performanceReview.statusId', ':statusId'))
                ->setParameter('statusId', 1);
        }

        if (!is_null($reviewSearchFilterParams->getFromDate())) {
            $qb->andWhere($qb->expr()->gte('performanceReview.dueDate', ':fromDate'))
                ->setParameter('fromDate', $reviewSearchFilterParams->getFromDate());
        }

        if (!is_null($reviewSearchFilterParams->getToDate())) {
            $qb->andWhere($qb->expr()->lte('performanceReview.dueDate', ':toDate'))
                ->setParameter('toDate', $reviewSearchFilterParams->getToDate());
        }

        if (!is_null($reviewSearchFilterParams->getJobTitleId())) {
            $qb->leftJoin('performanceReview.jobTitle', 'jobTitle');
            $qb->andWhere($qb->expr()->eq('jobTitle.id', ':jobTitleId'))
                ->setParameter('jobTitleId', $reviewSearchFilterParams->getJobTitleId());
        }

        if (!is_null($reviewSearchFilterParams->getSubUnitId())) {
            $qb->leftJoin('performanceReview.department', 'subUnit');
            $qb->andWhere($qb->expr()->eq('subUnit.id', ':subUnitId'))
                ->setParameter('subUnitId', $reviewSearchFilterParams->getSubUnitId());
        }

        $qb->andWhere($qb->expr()->isNull('employee.purgedAt'));

        $this->setSortingAndPaginationParams($qb, $reviewSearchFilterParams);
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
