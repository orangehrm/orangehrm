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
use OrangeHRM\Entity\PerformanceTracker;
use OrangeHRM\Entity\PerformanceTrackerReviewer;
use OrangeHRM\ORM\QueryBuilderWrapper;
use OrangeHRM\Performance\Dto\EmployeeTrackerSearchFilterParams;

class PerformanceTrackerDao extends BaseDao
{
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

        if (!is_null($employeeTrackerSearchFilterParams->getEmpNumbers())) {
            $qb->andWhere($qb->expr()->in('employee.empNumber', ':empNumbers'))
                ->setParameter('empNumbers', $employeeTrackerSearchFilterParams->getEmpNumbers());
        }

        if ($employeeTrackerSearchFilterParams->getIncludeEmployees(
            ) === EmployeeTrackerSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_CURRENT) {
            $qb->andWhere($qb->expr()->isNull('employee.employeeTerminationRecord'));
        } elseif ($employeeTrackerSearchFilterParams->getIncludeEmployees(
            ) === EmployeeTrackerSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_PAST) {
            $qb->andWhere($qb->expr()->isNotNull('employee.employeeTerminationRecord'));
        }

        if (!is_null($employeeTrackerSearchFilterParams->getNameOrId())) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('employee.firstName', ':nameOrId'),
                    $qb->expr()->like('employee.lastName', ':nameOrId'),
                    $qb->expr()->like('employee.middleName', ':nameOrId'),
                    $qb->expr()->like('employee.employeeId', ':nameOrId'),
                )
            );
            $qb->setParameter('nameOrId', '%' . $employeeTrackerSearchFilterParams->getNameOrId() . '%');
        }

        $qb->andWhere($qb->expr()->isNull('employee.purgedAt'));
        $this->setSortingAndPaginationParams($qb, $employeeTrackerSearchFilterParams);

        return $this->getQueryBuilderWrapper($qb);
    }

    /**
     * @param int $reviewerId
     * @return int[]
     * TODO move to Tracker Reviewer Dao
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
     * @param int $reviewerId
     * @return int[]
     * TODO move to Tracker Reviewer Dao
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
}
