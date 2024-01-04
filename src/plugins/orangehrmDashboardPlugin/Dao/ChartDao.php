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

namespace OrangeHRM\Dashboard\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Dashboard\Dto\LocationEmployeeCount;
use OrangeHRM\Dashboard\Dto\SubunitCountPair;
use OrangeHRM\Entity\EmpLocations;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Subunit;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\QueryBuilderWrapper;
use OrangeHRM\Pim\Dto\EmployeeSearchFilterParams;

class ChartDao extends BaseDao
{
    /**
     * @return SubunitCountPair[]
     */
    public function getEmployeeDistributionBySubunit(): array
    {
        $q = $this->createQueryBuilder(Subunit::class, 'subunit');
        $q->andWhere('subunit.level = :level');
        $q->setParameter('level', 1);

        $subunits = $q->getQuery()->execute();

        $employeeSearchFilterParams = new EmployeeSearchFilterParams();

        $employeeCount = [];
        foreach ($subunits as $subunit) {
            $employeeSearchFilterParams->setSubunitId($subunit->getId());
            $count = $this->getEmployeeCount($employeeSearchFilterParams);

            if ($count > 0) {
                $employeeCount[] = new SubunitCountPair($subunit, $count);
            }
        }

        return $employeeCount;
    }

    /**
     * @return int
     */
    public function getUnassignedEmployeeCount(): int
    {
        $employeeSearchFilterParams = new EmployeeSearchFilterParams();
        return $this->getEmployeeCount($employeeSearchFilterParams);
    }

    /**
     * @param EmployeeSearchFilterParams $employeeSearchFilterParams
     * @return int
     */
    private function getEmployeeCount(
        EmployeeSearchFilterParams $employeeSearchFilterParams
    ): int {
        $qb = $this->getEmployeeDistributionQueryBuilderWrapper(
            $employeeSearchFilterParams
        )->getQueryBuilder();
        return $this->getPaginator($qb)->count();
    }

    /**
     * @param EmployeeSearchFilterParams $employeeSearchFilterParams
     * @return QueryBuilderWrapper
     */
    private function getEmployeeDistributionQueryBuilderWrapper(EmployeeSearchFilterParams $employeeSearchFilterParams): QueryBuilderWrapper
    {
        $q = $this->createQueryBuilder(Employee::class, 'employee');
        $q->leftJoin('employee.subDivision', 'subunit');

        if (!is_null($employeeSearchFilterParams->getSubunitId())) {
            $q->andWhere($q->expr()->in('subunit.id', ':subunitId'))
                ->setParameter(
                    'subunitId',
                    $employeeSearchFilterParams->getSubunitIdChain()
                );
        }

        if (is_null($employeeSearchFilterParams->getSubunitId())) {
            $q->andWhere($q->expr()->isNull('employee.subDivision'));
        }

        $q->andWhere($q->expr()->isNull('employee.employeeTerminationRecord'));
        $q->andWhere($q->expr()->isNull('employee.purgedAt'));

        return $this->getQueryBuilderWrapper($q);
    }

    /**
     * @return int
     */
    public function getTotalActiveEmployeeCount(): int
    {
        $q = $this->createQueryBuilder(Employee::class, 'employee');
        $q->andWhere($q->expr()->isNull('employee.employeeTerminationRecord'));
        $q->andWhere($q->expr()->isNull('employee.purgedAt'));

        return $this->count($q);
    }

    /**
     * @return LocationEmployeeCount[]
     */
    public function getEmployeeDistributionByLocation(): array
    {
        $select = 'NEW ' . LocationEmployeeCount::class .
            '(location.id, location.name, COUNT(employee.empNumber))';
        $q = $this->createQueryBuilder(EmpLocations::class, 'el')
            ->leftJoin('el.employee', 'employee')
            ->leftJoin('el.location', 'location')
            ->select($select);
        $q->andWhere($q->expr()->isNull('employee.employeeTerminationRecord'));
        $q->andWhere($q->expr()->isNull('employee.purgedAt'));
        $q->addGroupBy('location.id');
        $q->addOrderBy('COUNT(employee.empNumber)', ListSorter::DESCENDING);
        $q->addOrderBy('location.name', ListSorter::ASCENDING);

        return $q->getQuery()->getResult();
    }
}
