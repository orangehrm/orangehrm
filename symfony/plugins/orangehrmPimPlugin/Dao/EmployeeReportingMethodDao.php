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

namespace OrangeHRM\Pim\Dao;

use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\EmpDependent;
use OrangeHRM\Entity\EmployeeTerminationRecord;
use OrangeHRM\Entity\ReportingMethod;
use OrangeHRM\Entity\ReportTo;
use OrangeHRM\Entity\TerminationReason;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Pim\Dto\EmployeeDependentSearchFilterParams;
use OrangeHRM\Pim\Dto\EmployeeSupervisorSearchFilterParams;

class EmployeeReportingMethodDao extends BaseDao
{
    /**
     * Search
     *
     * @param EmployeeSupervisorSearchFilterParams $employeeSupervisorSearchFilterParams
     * @return array
     * @throws DaoException
     */
    public function searchImmediateEmployeeSupervisors(EmployeeSupervisorSearchFilterParams $employeeSupervisorSearchFilterParams): array
    {
        try {
            $paginator = $this->getSearchEmployeeSupervisorPaginator($employeeSupervisorSearchFilterParams);
            return $paginator->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmployeeSupervisorSearchFilterParams $employeeSupervisorSearchFilterParams
     * @return Paginator
     */
    private function getSearchEmployeeSupervisorPaginator(EmployeeSupervisorSearchFilterParams $employeeSupervisorSearchFilterParams): Paginator
    {
        $q = $this->createQueryBuilder(ReportTo::class, 'rt');
        $q->leftJoin('rt.reportingMethod', 'rpm')
            ->leftJoin('rt.subordinate', 'sub')
            ->andWhere('sub.empNumber = :empNumber')
            ->setParameter('empNumber', $employeeSupervisorSearchFilterParams->getEmpNumber());
        $this->setSortingAndPaginationParams($q, $employeeSupervisorSearchFilterParams);

        return $this->getPaginator($q);
    }

    /**
     * Get Count of Search Query
     *
     * @param EmployeeSupervisorSearchFilterParams $employeeSupervisorSearchFilterParams
     * @return int
     * @throws DaoException
     */
    public function getSearchImmediateEmployeeSupervisorsCount(EmployeeSupervisorSearchFilterParams $employeeSupervisorSearchFilterParams): int
    {
        try {
            $paginator = $this->getSearchEmployeeSupervisorPaginator($employeeSupervisorSearchFilterParams);
            return $paginator->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
