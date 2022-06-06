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

namespace OrangeHRM\CorporateDirectory\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Traits\Service\TextHelperTrait;
use OrangeHRM\CorporateDirectory\Dto\EmployeeDirectorySearchFilterParams;
use OrangeHRM\Entity\Employee;
use OrangeHRM\ORM\QueryBuilderWrapper;

class EmployeeDirectoryDao extends BaseDao
{
    use TextHelperTrait;

    /**
     * @param EmployeeDirectorySearchFilterParams $employeeDirectorySearchParamHolder
     * @return Employee[]
     */
    public function getEmployeeList(EmployeeDirectorySearchFilterParams $employeeDirectorySearchParamHolder): array
    {
        $qb = $this->getEmployeeListQueryBuilderWrapper($employeeDirectorySearchParamHolder)->getQueryBuilder();
        return $qb->getQuery()->execute();
    }

    /**
     * @param EmployeeDirectorySearchFilterParams $employeeDirectorySearchParamHolder
     * @return QueryBuilderWrapper
     */
    protected function getEmployeeListQueryBuilderWrapper(
        EmployeeDirectorySearchFilterParams $employeeDirectorySearchParamHolder
    ): QueryBuilderWrapper {
        $q = $this->createQueryBuilder(Employee::class, 'employee');
        $q->leftJoin('employee.jobTitle', 'jobTitle');
        $q->leftJoin('employee.subDivision', 'subunit');
        $q->leftJoin('employee.locations', 'location');

        $this->setSortingAndPaginationParams($q, $employeeDirectorySearchParamHolder);

        if (is_null($employeeDirectorySearchParamHolder->getIncludeEmployees()) ||
            $employeeDirectorySearchParamHolder->getIncludeEmployees() ===
            EmployeeDirectorySearchFilterParams::INCLUDE_EMPLOYEES_ONLY_CURRENT
        ) {
            $q->andWhere($q->expr()->isNull('employee.employeeTerminationRecord'));
        } elseif (
            $employeeDirectorySearchParamHolder->getIncludeEmployees() ===
            EmployeeDirectorySearchFilterParams::INCLUDE_EMPLOYEES_ONLY_PAST
        ) {
            $q->andWhere($q->expr()->isNotNull('employee.employeeTerminationRecord'));
        }

        if (!is_null($employeeDirectorySearchParamHolder->getName())) {
            $q->andWhere(
                $q->expr()->orX(
                    $q->expr()->like('employee.firstName', ':name'),
                    $q->expr()->like('employee.lastName', ':name'),
                    $q->expr()->like('employee.middleName', ':name'),
                )
            );
            $q->setParameter('name', '%' . $employeeDirectorySearchParamHolder->getName() . '%');
        }

        if (!is_null($employeeDirectorySearchParamHolder->getEmpNumber())) {
            $q->andWhere('employee.empNumber = :empNumber')
                ->setParameter('empNumber', $employeeDirectorySearchParamHolder->getEmpNumber());
        }

        if (!is_null($employeeDirectorySearchParamHolder->getLocationId())) {
            $q->andWhere('location.id = :locationId')
                ->setParameter('locationId', $employeeDirectorySearchParamHolder->getLocationId());
        }

        if (!is_null($employeeDirectorySearchParamHolder->getJobTitleId())) {
            $q->andWhere('jobTitle.id = :jobTitleId')
                ->setParameter('jobTitleId', $employeeDirectorySearchParamHolder->getJobTitleId());
        }

        $q->andWhere($q->expr()->isNull('employee.purgedAt'));

        return $this->getQueryBuilderWrapper($q);
    }

    /**
     * @param EmployeeDirectorySearchFilterParams $employeeDirectorySearchParamHolder
     * @return int[]
     */
    public function getEmpNumbersByFilterParams(EmployeeDirectorySearchFilterParams $employeeDirectorySearchParamHolder
    ): array {
        $employeeDirectorySearchParamHolder->setSortField('employee.empNumber');
        $q = $this->getEmployeeListQueryBuilderWrapper($employeeDirectorySearchParamHolder)->getQueryBuilder();
        $q->select('employee.empNumber');

        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'empNumber');
    }

    /**
     * @param EmployeeDirectorySearchFilterParams $employeeDirectorySearchParamHolder
     * @return int
     */
    public function getEmployeeCount(EmployeeDirectorySearchFilterParams $employeeDirectorySearchParamHolder): int
    {
        $qb = $this->getEmployeeListQueryBuilderWrapper($employeeDirectorySearchParamHolder)->getQueryBuilder();
        return $this->getPaginator($qb)->count();
    }

    /**
     * @param int $empNumber
     * @return Employee|null
     */
    public function getEmployeeByEmpNumber(int $empNumber): ?Employee
    {
        return $this->getRepository(Employee::class)->find($empNumber);
    }
//
//    /**
//     * @param bool $includeTerminated
//     * @return int
//     */
//    public function getNumberOfEmployees(bool $includeTerminated = false): int
//    {
//        $q = $this->createQueryBuilder(Employee::class, 'e');
//
//        if (!$includeTerminated) {
//            $q->andWhere($q->expr()->isNull('e.employeeTerminationRecord'));
//        }
//        return $this->count($q);
//    }
}
