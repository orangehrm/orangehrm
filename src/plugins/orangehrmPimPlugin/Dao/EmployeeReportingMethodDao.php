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

namespace OrangeHRM\Pim\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\ReportTo;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Pim\Dto\EmployeeSubordinateSearchFilterParams;
use OrangeHRM\Pim\Dto\EmployeeSupervisorSearchFilterParams;

class EmployeeReportingMethodDao extends BaseDao
{
    /**
     * @param ReportTo $reportTo
     * @return ReportTo
     */
    public function saveEmployeeReportTo(ReportTo $reportTo): ReportTo
    {
        $this->persist($reportTo);
        return $reportTo;
    }

    /**
     * Search
     *
     * @param EmployeeSupervisorSearchFilterParams $employeeSupervisorSearchFilterParams
     * @return array
     */
    public function searchImmediateEmployeeSupervisors(EmployeeSupervisorSearchFilterParams $employeeSupervisorSearchFilterParams): array
    {
        $paginator = $this->getSearchEmployeeSupervisorPaginator($employeeSupervisorSearchFilterParams);
        return $paginator->getQuery()->execute();
    }

    /**
     * @param EmployeeSupervisorSearchFilterParams $employeeSupervisorSearchFilterParams
     * @return Paginator
     */
    private function getSearchEmployeeSupervisorPaginator(EmployeeSupervisorSearchFilterParams $employeeSupervisorSearchFilterParams): Paginator
    {
        $q = $this->createQueryBuilder(ReportTo::class, 'rt');
        $q->leftJoin('rt.supervisor', 'supervisor')
            ->andWhere('rt.subordinate = :empNumber')
            ->setParameter('empNumber', $employeeSupervisorSearchFilterParams->getEmpNumber());
        $this->setSortingAndPaginationParams($q, $employeeSupervisorSearchFilterParams);

        return $this->getPaginator($q);
    }

    /**
     * @param EmployeeSubordinateSearchFilterParams $employeeSubordinateSearchFilterParams
     * @return Paginator
     */
    private function getSearchEmployeeSubordinatePaginator(EmployeeSubordinateSearchFilterParams $employeeSubordinateSearchFilterParams): Paginator
    {
        $q = $this->createQueryBuilder(ReportTo::class, 'rt');
        $q->leftJoin('rt.subordinate', 'subordinate')
            ->andWhere('rt.supervisor = :empNumber')
            ->setParameter('empNumber', $employeeSubordinateSearchFilterParams->getEmpNumber());
        $this->setSortingAndPaginationParams($q, $employeeSubordinateSearchFilterParams);

        return $this->getPaginator($q);
    }


    /**
     * Get Count of Search Query
     *
     * @param EmployeeSupervisorSearchFilterParams $employeeSupervisorSearchFilterParams
     * @return int
     */
    public function getSearchImmediateEmployeeSupervisorsCount(EmployeeSupervisorSearchFilterParams $employeeSupervisorSearchFilterParams): int
    {
        $paginator = $this->getSearchEmployeeSupervisorPaginator($employeeSupervisorSearchFilterParams);
        return $paginator->count();
    }

    /**
     * @param int $empNumber
     * @param array $toDeleteIds
     * @return int
     */
    public function deleteEmployeeSupervisors(int $empNumber, array $toDeleteIds): int
    {
        $q = $this->createQueryBuilder(ReportTo::class, 'rt');
        $q->delete()
            ->andWhere('rt.subordinate = :empNumber')
            ->setParameter('empNumber', $empNumber)
            ->andWhere($q->expr()->in('rt.supervisor', ':ids'))
            ->setParameter('ids', $toDeleteIds);
        return $q->getQuery()->execute();
    }

    /**
     * @param int $empNumber
     * @param array $toDeleteIds
     * @return int
     */
    public function deleteEmployeeSubordinates(int $empNumber, array $toDeleteIds): int
    {
        $q = $this->createQueryBuilder(ReportTo::class, 'rt');
        $q->delete()
            ->andWhere('rt.supervisor = :empNumber')
            ->setParameter('empNumber', $empNumber)
            ->andWhere($q->expr()->in('rt.subordinate', ':ids'))
            ->setParameter('ids', $toDeleteIds);
        return $q->getQuery()->execute();
    }

    /**
     * Search
     *
     * @param EmployeeSubordinateSearchFilterParams $employeeSubordinateSearchFilterParams
     * @return array
     */
    public function searchEmployeeSubordinates(EmployeeSubordinateSearchFilterParams $employeeSubordinateSearchFilterParams): array
    {
        $paginator = $this->getSearchEmployeeSubordinatePaginator($employeeSubordinateSearchFilterParams);
        return $paginator->getQuery()->execute();
    }

    /**
     * Get Count of Search Query
     *
     * @param EmployeeSubordinateSearchFilterParams $employeeSubordinateSearchFilterParams
     * @return int
     */
    public function getSearchEmployeeSubordinatesCount(EmployeeSubordinateSearchFilterParams $employeeSubordinateSearchFilterParams): int
    {
        $paginator = $this->getSearchEmployeeSubordinatePaginator($employeeSubordinateSearchFilterParams);
        return $paginator->count();
    }

    /**
     * @param int $reportFromEmployeeId
     * @param int $reportToEmployeeId
     * @return ReportTo|null
     */
    public function getEmployeeReportToByEmpNumbers(int $reportFromEmployeeId, int $reportToEmployeeId): ?ReportTo
    {
        $employeeSupervisor = $this->getRepository(ReportTo::class)->findOneBy(
            [
                'supervisor' => $reportToEmployeeId,
                'subordinate' => $reportFromEmployeeId,
            ]
        );
        if ($employeeSupervisor instanceof ReportTo) {
            return $employeeSupervisor;
        }
        return null;
    }

    /**
     * @param int[] $ids
     * @param int $empNumber
     * @return int[]
     */
    public function getExistingSubordinateIdsForEmpNumber(array $ids, int $empNumber): array
    {
        $qb = $this->createQueryBuilder(ReportTo::class, 'reportTo');

        $qb->select('IDENTITY(reportTo.subordinate)')
            ->andWhere($qb->expr()->in('reportTo.subordinate', ':ids'))
            ->andWhere($qb->expr()->eq('reportTo.supervisor', ':empNumber'))
            ->setParameter('ids', $ids)
            ->setParameter('empNumber', $empNumber);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param int[] $ids
     * @param int $empNumber
     * @return int[]
     */
    public function getExistingSupervisorIdsForEmpNumber(array $ids, int $empNumber): array
    {
        $qb = $this->createQueryBuilder(ReportTo::class, 'reportTo');

        $qb->select('IDENTITY(reportTo.supervisor)')
            ->andWhere($qb->expr()->in('reportTo.supervisor', ':ids'))
            ->andWhere($qb->expr()->eq('reportTo.subordinate', ':empNumber'))
            ->setParameter('ids', $ids)
            ->setParameter('empNumber', $empNumber);

        return $qb->getQuery()->getSingleColumnResult();
    }
}
