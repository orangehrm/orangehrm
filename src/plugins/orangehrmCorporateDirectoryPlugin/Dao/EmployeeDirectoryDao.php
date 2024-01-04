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

namespace OrangeHRM\CorporateDirectory\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\CorporateDirectory\Dto\EmployeeDirectorySearchFilterParams;
use OrangeHRM\Entity\Employee;
use OrangeHRM\ORM\QueryBuilderWrapper;

class EmployeeDirectoryDao extends BaseDao
{
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
        $q->leftJoin('employee.locations', 'location');

        $this->setSortingAndPaginationParams($q, $employeeDirectorySearchParamHolder);

        if (!is_null($employeeDirectorySearchParamHolder->getEmpNumbers())) {
            $q->andWhere($q->expr()->in('employee.empNumber', ':empNumbers'))
                ->setParameter('empNumbers', $employeeDirectorySearchParamHolder->getEmpNumbers());
        } elseif (!is_null($employeeDirectorySearchParamHolder->getNameOrId())) {
            $q->andWhere(
                $q->expr()->orX(
                    $q->expr()->like('employee.firstName', ':nameOrId'),
                    $q->expr()->like('employee.lastName', ':nameOrId'),
                    $q->expr()->like('employee.middleName', ':nameOrId'),
                    $q->expr()->like('employee.employeeId', ':nameOrId'),
                )
            );
            $q->setParameter('nameOrId', '%' . $employeeDirectorySearchParamHolder->getNameOrId() . '%');
        } else {
            $q->andWhere($q->expr()->isNull('employee.employeeTerminationRecord'));
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
}
