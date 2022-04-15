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
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeWorkShift;
use OrangeHRM\Entity\WorkShift;
use OrangeHRM\ORM\Exception\TransactionException;
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
        $q = $this->createQueryBuilder(WorkShift::class, 'workShift');
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

    /**
     * @param WorkShift $workShift
     * @param int[] $empNumbers
     * @return WorkShift
     * @throws TransactionException
     */
    public function saveWorkShift(WorkShift $workShift, array $empNumbers): WorkShift
    {
        $this->beginTransaction();
        try {
            $this->persist($workShift);
            if (count($empNumbers) > 0) {
                // this function will invoke only if the array have some values
                $this->saveEmployeeWorkShift($empNumbers, $workShift);
            }
            $this->commitTransaction();
            return $workShift;
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }
    }

    /**
     * @param int[] $empNumbers
     * @param WorkShift $workShift
     * @return void
     */
    public function saveEmployeeWorkShift(array $empNumbers, WorkShift $workShift): void
    {
        foreach ($empNumbers as $empNumber) {
            $employeeWorkShift = new EmployeeWorkShift();
            $employee = $this->getRepository(Employee::class)->find($empNumber);
            $employeeWorkShift->setWorkShift($workShift);
            $employeeWorkShift->setEmployee($employee);
            $this->getEntityManager()->persist($employeeWorkShift);
        }
        $this->getEntityManager()->flush();
    }

    /**
     * @param WorkShift $workShift
     * @param int[] $empNumbers
     * @return WorkShift
     */
    public function updateWorkShift(WorkShift $workShift, array $empNumbers): WorkShift
    {
        $existingEmployees = $this->getEmployeeListByWorkShiftId($workShift->getId());
        $employeeNumberList = [];
        $deletableEmployeeNumberList = [];
        foreach ($existingEmployees as $existingEmployee) {
            $employeeNumber = $existingEmployee->getEmpNumber();
            if (!in_array($employeeNumber, $empNumbers)) {
                // this array is containing the employees that's going to  be deleted.
                array_push($deletableEmployeeNumberList, $employeeNumber);
            } else {
                array_push($employeeNumberList, $employeeNumber);
            }
        }
        $this->deleteExistingEmployees($workShift->getId(), $deletableEmployeeNumberList);
        $employeeList = array_diff($empNumbers, $employeeNumberList);
        $newEmployeeList = [];
        foreach ($employeeList as $employee) {
            array_push($newEmployeeList, $employee);
        }
        $this->persist($workShift);
        if (count($newEmployeeList) > 0) {
            $this->saveEmployeeWorkShift($newEmployeeList, $workShift);
            return $workShift;
        }
        return $workShift;
    }

    /**
     * @param int $workShiftId
     * @return Employee[]
     */
    public function getEmployeeListByWorkShiftId(int $workShiftId): array
    {
        $q = $this->createQueryBuilder(Employee::class, 'e');
        $q->andWhere($q->expr()->isNull('e.purgedAt'));
        $q->leftJoin('e.employeeWorkShift', 'ew');
        $q->andWhere('ew.workShift = :workShift')
            ->setParameter('workShift', $workShiftId);
        return $q->getQuery()->execute();
    }

    /**
     * @param int $workShiftId
     * @param int[] $empNumbers
     * @return void
     */
    public function deleteExistingEmployees(int $workShiftId, array $empNumbers): void
    {
        $q = $this->createQueryBuilder(EmployeeWorkShift::class, 'ews');
        $q->delete()
            ->where('ews.workShift = :workShiftId')
            ->andWhere($q->expr()->in('ews.employee', ':employeeNumbers'))
            ->setParameter('workShiftId', $workShiftId)
            ->setParameter('employeeNumbers', $empNumbers)
            ->getQuery()
            ->execute();
    }

    /**
     * @param int[] $deletedIds
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
