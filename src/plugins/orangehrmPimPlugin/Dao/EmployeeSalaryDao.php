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

use OrangeHRM\Admin\Dto\EmployeeSalarySearchFilterParams;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\EmpDirectDebit;
use OrangeHRM\Entity\EmployeeSalary;
use OrangeHRM\ORM\Paginator;

class EmployeeSalaryDao extends BaseDao
{
    /**
     * @param EmployeeSalary $employeeSalary
     * @return EmployeeSalary
     */
    public function saveEmployeeSalary(EmployeeSalary $employeeSalary): EmployeeSalary
    {
        $this->persist($employeeSalary);
        return $employeeSalary;
    }

    /**
     * @param EmpDirectDebit $empDirectDebit
     */
    public function deleteEmployeeDirectDebit(EmpDirectDebit $empDirectDebit): void
    {
        $this->remove($empDirectDebit);
    }

    /**
     * @param int $empNumber
     * @param int[] $salaryIds
     * @return int
     */
    public function deleteEmployeeSalaries(int $empNumber, array $salaryIds): int
    {
        $q = $this->createQueryBuilder(EmployeeSalary::class, 'es');
        $q->delete();
        $q->andWhere('es.employee = :empNumber')
                ->setParameter('empNumber', $empNumber);

        $q->andWhere($q->expr()->in('es.id', ':ids'))
                ->setParameter('ids', $salaryIds);

        return $q->getQuery()->execute();
    }

    /**
     * @param int $empNumber
     * @param int $salaryId
     * @return EmployeeSalary|null
     */
    public function getEmployeeSalary(int $empNumber, int $salaryId): ?EmployeeSalary
    {
        $q = $this->createQueryBuilder(EmployeeSalary::class, 'es');
        $q->leftJoin('es.directDebit', 'dd');
        $q->andWhere('es.employee = :empNumber')
                ->setParameter('empNumber', $empNumber);
        $q->andWhere('es.id = :salaryId')
                ->setParameter('salaryId', $salaryId);

        return $this->fetchOne($q);
    }

    /**
     * @param int[] $ids
     * @param int $empNumber
     * @return int[]
     */
    public function getExistingEmployeeSalaryIdsByEmpNumber(array $ids, int $empNumber): array
    {
        $qb = $this->createQueryBuilder(EmployeeSalary::class, 'employeeSalary');

        $qb->select('employeeSalary.id')
            ->andWhere($qb->expr()->in('employeeSalary.id', ':ids'))
            ->andWhere($qb->expr()->eq('employeeSalary.employee', ':empNumber'))
            ->setParameter('ids', $ids)
            ->setParameter('empNumber', $empNumber);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param EmployeeSalarySearchFilterParams $employeeSalarySearchFilterParams
     * @return EmployeeSalary[]
     */
    public function getEmployeeSalaries(EmployeeSalarySearchFilterParams $employeeSalarySearchFilterParams): array
    {
        return $this->getEmployeeSalariesPaginator($employeeSalarySearchFilterParams)
                ->getQuery()
                ->execute();
    }

    /**
     * @param EmployeeSalarySearchFilterParams $employeeSalarySearchFilterParams
     * @return int
     */
    public function getEmployeeSalariesCount(EmployeeSalarySearchFilterParams $employeeSalarySearchFilterParams): int
    {
        return $this->getEmployeeSalariesPaginator($employeeSalarySearchFilterParams)->count();
    }

    /**
     * @param EmployeeSalarySearchFilterParams $employeeSalarySearchFilterParams
     * @return Paginator
     */
    private function getEmployeeSalariesPaginator(
        EmployeeSalarySearchFilterParams $employeeSalarySearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(EmployeeSalary::class, 'es');
        $q->leftJoin('es.directDebit', 'dd');
        $this->setSortingAndPaginationParams($q, $employeeSalarySearchFilterParams);
        if ($employeeSalarySearchFilterParams->getEmpNumber()) {
            $q->andWhere('es.employee = :empNumber')
                ->setParameter('empNumber', $employeeSalarySearchFilterParams->getEmpNumber());
        }
        return $this->getPaginator($q);
    }
}
