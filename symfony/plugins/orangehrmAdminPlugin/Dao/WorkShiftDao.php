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

use OrangeHRM\Admin\Dto\WorkShiftSearchFilterParams;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\WorkShift;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\Paginator;

class WorkShiftDao extends BaseDao
{

    /**
     * @param int $workShiftId
     * @return WorkShift|null
     */
    public function getWorkShiftById(int $workShiftId): ?WorkShift
    {
        try {
            $workShift = $this->getRepository(WorkShift::class)->find($workShiftId);
            if ($workShift instanceof WorkShift) {
                return $workShift;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param WorkShiftSearchFilterParams $workShiftSearchFilterParams
     * @return array
     * @throws DaoException
     */
    public function getWorkShiftList(WorkShiftSearchFilterParams $workShiftSearchFilterParams): array
    {
        try {
            $paginator = $this->getWorkShiftListPaginator($workShiftSearchFilterParams);
            return $paginator->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param WorkShiftSearchFilterParams $workShiftSearchFilterParams
     * @return Paginator
     */
    public function getWorkShiftListPaginator(WorkShiftSearchFilterParams $workShiftSearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(WorkShift::class, 'ws');
        $this->setSortingAndPaginationParams($q, $workShiftSearchFilterParams);
        return new Paginator($q);
    }


    /**
     * @param WorkShiftSearchFilterParams $workShiftSearchFilterParams
     * @return int
     * @throws DaoException
     */
    public function getWorkShiftCount(WorkShiftSearchFilterParams $workShiftSearchFilterParams): int
    {
        try {
            $paginator = $this->getWorkShiftListPaginator($workShiftSearchFilterParams);
            return $paginator->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getWorkShiftEmployeeListById($workShiftId) {
        // TODO
        try {
            $q = Doctrine_Query :: create()
                    ->from('EmployeeWorkShift')
                    ->where('work_shift_id = ?', $workShiftId);
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function getWorkShiftEmployeeNameListById($workShiftId) {
        // TODO
        try {
            $q = Doctrine_Query :: create()
                    ->select('w.emp_number as empNumber, e.firstName as firstName, e.lastName as lastName, e.middleName as middleName')
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

    public function getWorkShiftEmployeeList() {
        // TODO
        try {
            $q = Doctrine_Query :: create()
                    ->from('EmployeeWorkShift');
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function getWorkShiftEmployeeIdList() {
        // TODO
        try {
            $q = Doctrine_Query :: create()
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

    public function saveEmployeeWorkShiftCollection(Doctrine_Collection $empWorkShiftCollection) {
        // TODO
        try {

            $empWorkShiftCollection->save();

            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }
}
