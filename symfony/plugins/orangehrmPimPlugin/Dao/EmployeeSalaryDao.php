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
use OrangeHRM\Admin\Dto\EmployeeSalarySearchFilterParams;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\EmpDirectDebit;
use OrangeHRM\Entity\EmployeeSalary;
use OrangeHRM\ORM\Paginator;

class EmployeeSalaryDao extends BaseDao
{
    /**
     * @param EmployeeSalary $employeeSalary
     * @return EmployeeSalary
     * @throws DaoException
     */
    public function saveEmployeeSalary(EmployeeSalary $employeeSalary): EmployeeSalary
    {
        try {
            $this->persist($employeeSalary);
            return $employeeSalary;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmpDirectDebit $empDirectDebit
     * @throws DaoException
     */
    public function deleteEmployeeDirectDebit(EmpDirectDebit $empDirectDebit): void
    {
        try {
            $this->remove($empDirectDebit);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $empNumber
     * @param int[] $salaryIds
     * @return int
     * @throws DaoException
     */
    public function deleteEmployeeSalaries(int $empNumber, array $salaryIds): int
    {
        try {
            $q = $this->createQueryBuilder(EmployeeSalary::class, 'es');
            $q->delete();
            $q->andWhere('es.employee = :empNumber')
                ->setParameter('empNumber', $empNumber);

            $q->andWhere($q->expr()->in('es.id', ':ids'))
                ->setParameter('ids', $salaryIds);

            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $empNumber
     * @param int $salaryId
     * @return EmployeeSalary|null
     * @throws DaoException
     */
    public function getEmployeeSalary(int $empNumber, int $salaryId): ?EmployeeSalary
    {
        try {
            $q = $this->createQueryBuilder(EmployeeSalary::class, 'es');
            $q->leftJoin('es.directDebit', 'dd');
            $q->andWhere('es.employee = :empNumber')
                ->setParameter('empNumber', $empNumber);
            $q->andWhere('es.id = :salaryId')
                ->setParameter('salaryId', $salaryId);

            return $this->fetchOne($q);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmployeeSalarySearchFilterParams $employeeSalarySearchFilterParams
     * @return EmployeeSalary[]
     * @throws DaoException
     */
    public function getEmployeeSalaries(EmployeeSalarySearchFilterParams $employeeSalarySearchFilterParams): array
    {
        try {
            return $this->getEmployeeSalariesPaginator($employeeSalarySearchFilterParams)
                ->getQuery()
                ->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmployeeSalarySearchFilterParams $employeeSalarySearchFilterParams
     * @return int
     * @throws DaoException
     */
    public function getEmployeeSalariesCount(EmployeeSalarySearchFilterParams $employeeSalarySearchFilterParams): int
    {
        try {
            return $this->getEmployeeSalariesPaginator($employeeSalarySearchFilterParams)->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
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
