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

namespace OrangeHRM\Dashboard\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Dashboard\Dto\ActionableReviewSearchFilterParams;
use OrangeHRM\Entity\CandidateVacancy;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Entity\ReviewerGroup;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\QueryBuilderWrapper;
use OrangeHRM\Performance\Dto\PerformanceReviewSearchFilterParams;

class EmployeeActionSummaryDao extends BaseDao
{
    public const STATE_INTERVIEW_SCHEDULED = 'INTERVIEW SCHEDULED';

    /**
     * @param int[] $candidateIds
     * @return int
     */
    public function getActionableScheduledInterviewCount(array $candidateIds): int
    {
        $qb = $this->createQueryBuilder(CandidateVacancy::class, 'candidateVacancy');
        $qb->andWhere($qb->expr()->in('candidateVacancy.candidate', ':candidateIds'))
            ->setParameter('candidateIds', $candidateIds)
            ->andWhere('candidateVacancy.status = :status')
            ->setParameter('status', self::STATE_INTERVIEW_SCHEDULED);
        return $this->getPaginator($qb)->count();
    }

    /**
     * @param ActionableReviewSearchFilterParams $actionableReviewSearchFilterParams
     * @return int
     */
    public function getActionableReviewCount(
        ActionableReviewSearchFilterParams $actionableReviewSearchFilterParams
    ): int {
        $query = $this->getActionableReviewQueryBuilderWrapper($actionableReviewSearchFilterParams)->getQueryBuilder();
        return $this->getPaginator($query)->count();
    }

    /**
     * @param ActionableReviewSearchFilterParams $actionableReviewSearchFilterParams
     * @return QueryBuilderWrapper
     */
    private function getActionableReviewQueryBuilderWrapper(
        ActionableReviewSearchFilterParams $actionableReviewSearchFilterParams
    ): QueryBuilderWrapper {
        $qb = $this->createQueryBuilder(PerformanceReview::class, 'performanceReview');
        $qb->leftJoin('performanceReview.employee', 'employee');
        $qb->leftJoin('performanceReview.reviewers', 'reviewer');
        $qb->leftJoin('reviewer.employee', 'reviewerEmployee');
        $qb->leftJoin('reviewer.group', 'reviewGroup');
        $qb->leftJoin('performanceReview.jobTitle', 'jobTitle');
        $qb->leftJoin('performanceReview.subunit', 'subunit');

        $qb->andWhere($qb->expr()->eq('reviewGroup.name', ':reviewGroupName'))
            ->setParameter('reviewGroupName', ReviewerGroup::REVIEWER_GROUP_SUPERVISOR);

        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->eq('reviewerEmployee.empNumber', ':supervisorEmpNumber'),
                $qb->expr()->eq('performanceReview.employee', ':empNumber')
            )
        );
        $qb->setParameter(
            'supervisorEmpNumber',
            $actionableReviewSearchFilterParams->getReviewerEmpNumber()
        );
        $qb->setParameter(
            'empNumber',
            $actionableReviewSearchFilterParams->getEmpNumber()
        );
        $qb->andWhere($qb->expr()->in('performanceReview.statusId', ':statusIds'))
            ->setParameter('statusIds', $actionableReviewSearchFilterParams->getActionableStatuses());

        if (is_null($actionableReviewSearchFilterParams->getIncludeEmployees()) ||
            $actionableReviewSearchFilterParams->getIncludeEmployees() ===
            PerformanceReviewSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_CURRENT
        ) {
            $qb->andWhere($qb->expr()->isNull('employee.employeeTerminationRecord'));
        } elseif ($actionableReviewSearchFilterParams->getIncludeEmployees() ===
            PerformanceReviewSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_PAST) {
            $qb->andWhere($qb->expr()->isNotNull('employee.employeeTerminationRecord'));
        }

        $qb->andWhere($qb->expr()->isNull('employee.purgedAt'));

        $this->setSortingAndPaginationParams($qb, $actionableReviewSearchFilterParams);
        $qb->addOrderBy('performanceReview.dueDate', ListSorter::DESCENDING);
        $qb->addOrderBy('employee.lastName');
        return $this->getQueryBuilderWrapper($qb);
    }
}
