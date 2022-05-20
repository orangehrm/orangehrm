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
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\ORM\QueryBuilderWrapper;
use OrangeHRM\Performance\Dto\PerformanceReviewSearchFilterParams;

class PerformanceReviewDao extends BaseDao
{
    use DateTimeHelperTrait;

    /**
     * @param PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams
     * @return PerformanceReview[]
     */
    public function getPerformanceReviewList(PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams): array
    {
        $qb = $this->getReviewListQueryBuilderWrapper($performanceReviewSearchFilterParams)->getQueryBuilder();
        return $qb->getQuery()->execute();
    }

    /**
     * @param PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams
     * @return int
     */
    public function getPerformanceReviewCount(PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams): int
    {
        $qb = $this->getReviewListQueryBuilderWrapper($performanceReviewSearchFilterParams)->getQueryBuilder();
        return $this->getPaginator($qb)->count();
    }

    /**
     * @param PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams
     * @return QueryBuilderWrapper
     */
    private function getReviewListQueryBuilderWrapper(PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams): QueryBuilderWrapper
    {
        $qb = $this->createQueryBuilder(PerformanceReview::class, 'performanceReview');
        $qb->leftJoin('performanceReview.employee', 'employee');
        $qb->leftJoin('performanceReview.reviewers', 'reviewer');
        $qb->leftJoin('reviewer.employee', 'reviewerEmployee');
        $qb->leftJoin('reviewer.group', 'reviewGroup');
        $qb->leftJoin('performanceReview.jobTitle', 'jobTitle');
        $qb->leftJoin('performanceReview.department', 'subUnit');

        $qb->andWhere($qb->expr()->eq('reviewGroup.name', ':reviewGroupName'))
            ->setParameter('reviewGroupName', 'Supervisor');

        if (!is_null($performanceReviewSearchFilterParams->getSupervisorId())) {
            $qb->andWhere($qb->expr()->eq('reviewerEmployee.empNumber', ':supervisorEmpNumber'))
                ->setParameter('supervisorEmpNumber', $performanceReviewSearchFilterParams->getSupervisorId());
        }

        if (!is_null($performanceReviewSearchFilterParams->getEmpNumber())) {
            $qb->andWhere($qb->expr()->eq('performanceReview.employee', ':empNumber'))
                ->setParameter('empNumber', $performanceReviewSearchFilterParams->getEmpNumber());
        }

        if (!is_null($performanceReviewSearchFilterParams->getStatusId())) {
            $qb->andWhere($qb->expr()->eq('performanceReview.statusId', ':statusId'))
                ->setParameter('statusId', $performanceReviewSearchFilterParams->getStatusId());
        } elseif ($performanceReviewSearchFilterParams->isExcludeInactiveReviews()) {
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

        if (is_null($performanceReviewSearchFilterParams->getFromDate()) &&
            is_null($performanceReviewSearchFilterParams->getToDate())
        ) {
            $currentYear = $this->getDateTimeHelper()->getNow()->format('Y');
            $qb->andWhere($qb->expr()->gte('performanceReview.dueDate', ':fromDate'))
                ->setParameter('fromDate', "$currentYear-01-01");
            $qb->andWhere($qb->expr()->lte('performanceReview.dueDate', ':toDate'))
                ->setParameter('toDate', "$currentYear-12-31");
        }

        if (!is_null($performanceReviewSearchFilterParams->getJobTitleId())) {
            $qb->andWhere($qb->expr()->eq('jobTitle.id', ':jobTitleId'))
                ->setParameter('jobTitleId', $performanceReviewSearchFilterParams->getJobTitleId());
        }

        if (!is_null($performanceReviewSearchFilterParams->getSubunitId())) {
            $qb->andWhere($qb->expr()->in('subUnit.id', ':subUnitId'))
                ->setParameter('subUnitId', $performanceReviewSearchFilterParams->getSubunitIdChain());
        }

        if (is_null($performanceReviewSearchFilterParams->getIncludeEmployees()) ||
            $performanceReviewSearchFilterParams->getIncludeEmployees() ===
            PerformanceReviewSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_CURRENT
        ) {
            $qb->andWhere($qb->expr()->isNull('employee.employeeTerminationRecord'));
        } elseif ($performanceReviewSearchFilterParams->getIncludeEmployees() ===
            PerformanceReviewSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_PAST) {
            $qb->andWhere($qb->expr()->isNotNull('employee.employeeTerminationRecord'));
        }

        $qb->andWhere($qb->expr()->isNull('employee.purgedAt'));

        $this->setSortingAndPaginationParams($qb, $performanceReviewSearchFilterParams);
        $qb->addOrderBy('performanceReview.dueDate', 'DESC');
        $qb->addOrderBy('employee.lastName');
        return $this->getQueryBuilderWrapper($qb);
    }

    /**
     * @param array $performanceReviewIds
     * @return int
     */
    public function deletePerformanceReviews(array $performanceReviewIds): int
    {
        $qb = $this->createQueryBuilder(PerformanceReview::class, 'performanceReview');
        $qb->delete()
            ->andWhere($qb->expr()->in('performanceReview.id', ':performanceReviewIds'))
            ->setParameter('performanceReviewIds', $performanceReviewIds);
        return $qb->getQuery()->execute();
    }
}
