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
use OrangeHRM\Entity\Employee;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Pim\Dto\EmployeeSearchFilterParams;

class EmployeeDao extends BaseDao
{
    /**
     * @param EmployeeSearchFilterParams $employeeSearchParamHolder
     * @return array
     * @throws DaoException
     */
    public function getEmployeeList(EmployeeSearchFilterParams $employeeSearchParamHolder): array
    {
        try {
            $paginator = $this->getEmployeeListPaginator($employeeSearchParamHolder);
            return $paginator->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmployeeSearchFilterParams $employeeSearchParamHolder
     * @return int
     * @throws DaoException
     */
    public function getEmployeeCount(EmployeeSearchFilterParams $employeeSearchParamHolder): int
    {
        try {
            $paginator = $this->getEmployeeListPaginator($employeeSearchParamHolder);
            return $paginator->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmployeeSearchFilterParams $employeeSearchParamHolder
     * @return Paginator
     */
    public function getEmployeeListPaginator(EmployeeSearchFilterParams $employeeSearchParamHolder): Paginator
    {
        $q = $this->createQueryBuilder(Employee::class, 'e');
        $this->setSortingAndPaginationParams($q, $employeeSearchParamHolder);
        if (!$employeeSearchParamHolder->isIncludeTerminated()) {
            $q->andWhere($q->expr()->isNull('e.employeeTerminationRecord'));
        }
        if (!is_null($employeeSearchParamHolder->getName())) {
            $q->andWhere(
                $q->expr()->orX(
                    $q->expr()->like('e.firstName', ':name'),
                    $q->expr()->like('e.lastName', ':name'),
                    $q->expr()->like('e.middleName', ':name'),
                )
            );
            $q->setParameter('name', '%' . $employeeSearchParamHolder->getName() . '%');
        }
        if (!is_null($employeeSearchParamHolder->getNameOrId())) {
            $q->andWhere(
                $q->expr()->orX(
                    $q->expr()->like('e.firstName', ':nameOrId'),
                    $q->expr()->like('e.lastName', ':nameOrId'),
                    $q->expr()->like('e.middleName', ':nameOrId'),
                    $q->expr()->like('e.employeeId', ':nameOrId'),
                )
            );
            $q->setParameter('nameOrId', '%' . $employeeSearchParamHolder->getNameOrId() . '%');
        }

        return $this->getPaginator($q);
    }

    /**
     * @param Employee $employee
     * @return Employee
     * @throws DaoException
     */
    public function saveEmployee(Employee $employee): Employee
    {
        try {
            $this->persist($employee);
            return $employee;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $empNumber
     * @return Employee|null
     * @throws DaoException
     */
    public function getEmployeeByEmpNumber(int $empNumber): ?Employee
    {
        try {
            $employee = $this->getRepository(Employee::class)->find($empNumber);
            if ($employee instanceof Employee) {
                return $employee;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getSubordinateIdListBySupervisorId($supervisorId, $includeChain = false, $supervisorIdStack = array (), $maxDepth = NULL, $depth = 1) {

        try {
            $employeeIdList = array();
            $q = "SELECT h.erep_sub_emp_number
            	FROM hs_hr_emp_reportto h 
            		WHERE (h.erep_sup_emp_number = ?)";


            $pdo = Doctrine_Manager::connection()->getDbh();
            $query = $pdo->prepare($q);
            $query->execute(array($supervisorId));
            $subordinates =  $query->fetchAll();

            foreach ($subordinates as $subordinate) {
                array_push($employeeIdList, $subordinate['erep_sub_emp_number']);

                if ($includeChain || (!is_null($maxDepth) && ($depth < $maxDepth))) {
                    if (!in_array($subordinate['erep_sub_emp_number'], $supervisorIdStack)) {
                        $supervisorIdStack[] = $subordinate['erep_sub_emp_number'];
                        $subordinateIdList = $this->getSubordinateIdListBySupervisorId($subordinate['erep_sub_emp_number'], $includeChain, $supervisorIdStack, $maxDepth, $depth + 1);
                        if (count($subordinateIdList) > 0) {
                            foreach ($subordinateIdList as $id) {
                                array_push($employeeIdList, $id);
                            }
                        }
                    }
                }
            }
            return $employeeIdList;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
