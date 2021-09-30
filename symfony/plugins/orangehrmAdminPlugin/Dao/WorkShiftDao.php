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

namespace OrangeHRM\Admin\Dao;

use Exception;
use OrangeHRM\Admin\Dto\WorkShiftSearchFilterParams;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeWorkShift;
use OrangeHRM\Entity\WorkShift;
use OrangeHRM\ORM\Paginator;

class WorkShiftDao extends BaseDao
{
    /**
     * @param int $workShiftId
     * @return WorkShift|null
     */
    public function getWorkShiftById(int $workShiftId): ?WorkShift
    {
        $workShift = $this->getRepository(WorkShift::class)->find($workShiftId);
        if ($workShift instanceof WorkShift) {
            return $workShift;
        }
        return null;
    }

    /**
     * @param WorkShiftSearchFilterParams $workShiftSearchFilterParams
     * @return array
     */
    public function getWorkShiftList(WorkShiftSearchFilterParams $workShiftSearchFilterParams): array
    {
        $paginator = $this->getWorkShiftListPaginator($workShiftSearchFilterParams);
        return $paginator->getQuery()->execute();
    }

    /**
     * @param WorkShiftSearchFilterParams $workShiftSearchFilterParams
     * @return Paginator
     */
    public function getWorkShiftListPaginator(WorkShiftSearchFilterParams $workShiftSearchFilterParams): Paginator
    {
        $q = $this->createQueryBuilder(WorkShift::class, 'ws');
        $this->setSortingAndPaginationParams($q, $workShiftSearchFilterParams);
        return new Paginator($q);
    }

    /**
     * @param WorkShiftSearchFilterParams $workShiftSearchFilterParams
     * @return int
     */
    public function getWorkShiftCount(WorkShiftSearchFilterParams $workShiftSearchFilterParams): int
    {
        $paginator = $this->getWorkShiftListPaginator($workShiftSearchFilterParams);
        return $paginator->count();
    }

    public function getWorkShiftEmployeeListById($workShiftId)
    {
        // TODO
        try {
            $q = Doctrine_Query:: create()
                ->from('EmployeeWorkShift')
                ->where('work_shift_id = ?', $workShiftId);
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function getWorkShiftEmployeeNameListById($workShiftId)
    {
        // TODO
        try {
            $q = Doctrine_Query:: create()
                ->select(
                    'w.emp_number as empNumber, e.firstName as firstName, e.lastName as lastName, e.middleName as middleName'
                )
                ->from('EmployeeWorkShift w')
                ->leftJoin('w.Employee e')
                ->where('work_shift_id = ?', $workShiftId);

            $employeeNames = $q->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

            return $employeeNames;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function getWorkShiftEmployeeList()
    {
        // TODO
        try {
            $q = Doctrine_Query:: create()
                ->from('EmployeeWorkShift');
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function getWorkShiftEmployeeIdList()
    {
        // TODO
        try {
            $q = Doctrine_Query:: create()
                ->select('emp_number')
                ->from('EmployeeWorkShift');

            $employeeIds = $q->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

            if (is_string($employeeIds)) {
                $employeeIds = array($employeeIds);
            }

            return $employeeIds;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function saveEmployeeWorkShiftCollection(Doctrine_Collection $empWorkShiftCollection)
    {
        // TODO
        try {
            $empWorkShiftCollection->save();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param WorkShift $workShift
     * @param array $empNumber
     * @return WorkShift
     */
    public function saveWorkShift(WorkShift $workShift, array $empNumber): WorkShift
    {
        $this->persist($workShift);
        if (sizeof($empNumber) > 0) {
            // this function will invoke only if the array have some values
            $this->saveEmployeeWorkShift($empNumber, $workShift);
            return $workShift;
        }
        return $workShift;
    }

    /**
     * @param array $empNumber
     * @param WorkShift $workShift
     */
    public function saveEmployeeWorkShift(array $empNumber, WorkShift $workShift): void
    {
        foreach ($empNumber as $empNo) {
            $employeeWorkShift = new EmployeeWorkShift();
            $employee = $this->getRepository(Employee::class)->find($empNo);
            $employeeWorkShift->setWorkShift($workShift);
            $employeeWorkShift->setEmployee($employee);
            $this->persist($employeeWorkShift);
        }
    }

    /**
     * @param WorkShift $workShift
     * @param array $empNumber
     * @return WorkShift
     */
    public function updateWorkShift(WorkShift $workShift, array $empNumber): WorkShift
    {
        $existingEmployees = $this->getEmployeeListByWorkShiftId($workShift->getId());
        $idList = array();
        foreach ($existingEmployees as $x => $existingEmployee) {
            $id = $existingEmployee->getEmpNumber();
            if (!in_array($id, $empNumber)) {
                $this->deleteExistingEmployees($workShift->getId(), $id);
            } else {
                array_push($idList, $id);
            }
        }
        $employeeList = array_diff($empNumber, $idList);
        $newEmployeeList = array();
        foreach ($employeeList as $employee) {
            array_push($newEmployeeList, $employee);
        }
        $this->persist($workShift);
        if (sizeof($newEmployeeList) > 0) {
            $this->saveEmployeeWorkShift($newEmployeeList, $workShift);
            return $workShift;
        }
        return $workShift;
    }

    /**
     * @param $workShiftId
     * @return Employee[]
     */
    public function getEmployeeListByWorkShiftId($workShiftId): array
    {
        $q = $this->createQueryBuilder(Employee::class, 'e');
        $q->leftJoin('e.employeeWorkShift', 'ew');
        $q->andWhere('ew.workShift = :workShift')
            ->setParameter('workShift', $workShiftId);
        return $q->getQuery()->execute();
    }

    /**
     * @param $workShiftId
     * @param $empNumber
     */
    public function deleteExistingEmployees($workShiftId, $empNumber): void
    {
        $this->createQueryBuilder(EmployeeWorkShift::class, 'ews')
            ->delete()
            ->where('ews.workShift = :workShiftId')
            ->andWhere('ews.employee = :employeeId')
            ->setParameter('workShiftId', $workShiftId)
            ->setParameter('employeeId', $empNumber)
            ->getQuery()
            ->execute();
    }

    /**
     * @param array $deletedIds
     * @return int
     */
    public function deleteWorkShifts(array $deletedIds): int
    {
        $q = $this->createQueryBuilder(WorkShift::class, 'ws');
        $q->delete()
            ->where($q->expr()->in('ws.id', ':ids'))
            ->setParameter('ids', $deletedIds);
        return $q->getQuery()->execute();
    }
}
