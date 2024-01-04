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
use OrangeHRM\Dashboard\Dto\EmployeeOnLeaveSearchFilterParams;
use OrangeHRM\Dashboard\Traits\Service\EmployeeOnLeaveServiceTrait;
use OrangeHRM\Entity\Leave;
use OrangeHRM\ORM\Paginator;

class EmployeeOnLeaveDao extends BaseDao
{
    use EmployeeOnLeaveServiceTrait;

    /**
     * @param EmployeeOnLeaveSearchFilterParams $leaveListSearchFilterParams
     * @return array
     */
    public function getEmployeeOnLeaveList(EmployeeOnLeaveSearchFilterParams $leaveListSearchFilterParams): array
    {
        return $this->getLeaveListPaginator($leaveListSearchFilterParams)->getQuery()->execute();
    }

    /**
     * @param EmployeeOnLeaveSearchFilterParams $leaveListSearchFilterParams
     * @return Paginator
     */
    private function getLeaveListPaginator(EmployeeOnLeaveSearchFilterParams $leaveListSearchFilterParams): Paginator
    {
        $q = $this->createQueryBuilder(Leave::class, 'leaveList');
        $q->leftJoin('leaveList.employee', 'employee');
        $q->leftJoin('leaveList.leaveType', 'type');

        $this->setSortingAndPaginationParams($q, $leaveListSearchFilterParams);

        $q->andWhere('type.deleted = :deleted')
            ->setParameter('deleted', false);
        $q->andWhere('leaveList.date = :date')->setParameter('date', $leaveListSearchFilterParams->getDate());
        $q->andWhere(
            $q->expr()->orX(
                $q->expr()->eq('leaveList.status', ':pending'),
                $q->expr()->eq('leaveList.status', ':approved'),
                $q->expr()->eq('leaveList.status', ':taken'),
            )
        )
            ->setParameter('pending', Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL)
            ->setParameter('approved', Leave::LEAVE_STATUS_LEAVE_APPROVED)
            ->setParameter('taken', Leave::LEAVE_STATUS_LEAVE_TAKEN);
        if (!is_null($leaveListSearchFilterParams->getAccessibleEmpNumber())) {
            $q->andWhere($q->expr()->in('leaveList.employee', ':empNumbers'));
            $q->setParameter('empNumbers', $leaveListSearchFilterParams->getAccessibleEmpNumber());
        }

        $q->andWhere($q->expr()->isNull('employee.employeeTerminationRecord'));
        $q->andWhere($q->expr()->isNull('employee.purgedAt'));
        return $this->getPaginator($q);
    }

    /**
     * @param EmployeeOnLeaveSearchFilterParams $leaveListSearchFilterParams
     * @return int
     */
    public function getEmployeeOnLeaveCount(EmployeeOnLeaveSearchFilterParams $leaveListSearchFilterParams): int
    {
        return $this->getLeaveListPaginator($leaveListSearchFilterParams)->count();
    }
}
