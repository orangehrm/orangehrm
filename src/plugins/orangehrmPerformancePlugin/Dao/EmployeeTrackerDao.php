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
use OrangeHRM\ORM\QueryBuilderWrapper;
use OrangeHRM\Performance\Dto\EmployeeTrackerSearchFilterParams;

class EmployeeTrackerDao extends BaseDao
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

        if (!is_null($employeeTrackerSearchFilterParams->getEmpNumber())) {
            $qb->andWhere($qb->expr()->eq('employee.empNumber', ':empNumber'))
                ->setParameter('empNumber', $employeeTrackerSearchFilterParams->getEmpNumber());
        }

        if ($employeeTrackerSearchFilterParams->getIncludeEmployees(
            ) === EmployeeTrackerSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_CURRENT) {
            $qb->andWhere($qb->expr()->isNull('employee.employeeTerminationRecord'));
        } elseif ($employeeTrackerSearchFilterParams->getIncludeEmployees(
            ) === EmployeeTrackerSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_PAST) {
            $qb->andWhere($qb->expr()->isNotNull('employee.employeeTerminationRecord'));
        }

        // TODO filter results for ESS reviewers

        $qb->andWhere($qb->expr()->isNull('employee.purgedAt'));
        $this->setSortingAndPaginationParams($qb, $employeeTrackerSearchFilterParams);

        return $this->getQueryBuilderWrapper($qb);
    }
}
