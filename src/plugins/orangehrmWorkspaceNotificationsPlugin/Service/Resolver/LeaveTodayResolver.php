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

namespace OrangeHRM\WorkspaceNotifications\Service\Resolver;

use DateTime;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Leave;
use OrangeHRM\WorkspaceNotifications\Dto\WorkspaceNotificationRecipient;

class LeaveTodayResolver implements RecipientResolverInterface
{
    use EntityManagerHelperTrait;

    private const APPROVED_STATUSES = [
        Leave::LEAVE_STATUS_LEAVE_APPROVED,
        Leave::LEAVE_STATUS_LEAVE_TAKEN,
    ];

    /**
     * @param int[] $subunitIds Multi-subunit filter; empty array = include all employees.
     * @return WorkspaceNotificationRecipient[]
     */
    public function resolve(DateTime $date, array $subunitIds): array
    {
        $qb = $this->createQueryBuilder(Leave::class, 'l')
            ->innerJoin('l.employee', 'e')
            ->addSelect('e')
            ->leftJoin('l.leaveType', 'lt')
            ->addSelect('lt')
            ->leftJoin('e.subDivision', 'sub')
            ->addSelect('sub')
            ->leftJoin('l.leaveRequest', 'lr')
            ->addSelect('lr')
            ->andWhere('l.date = :date')
            ->setParameter('date', $date->format('Y-m-d'))
            ->andWhere('l.status IN (:statuses)')
            ->setParameter('statuses', self::APPROVED_STATUSES)
            ->andWhere('e.employeeTerminationRecord IS NULL')
            ->orderBy('e.firstName', 'ASC')
            ->addOrderBy('e.lastName', 'ASC');

        if (count($subunitIds) > 0) {
            $qb->andWhere('IDENTITY(e.subDivision) IN (:subunitIds)')
                ->setParameter('subunitIds', $subunitIds);
        }

        $seen = [];
        $matched = [];
        $requestIds = [];
        foreach ($qb->getQuery()->getResult() as $leave) {
            /** @var Leave $leave */
            $emp = $leave->getEmployee();
            $empId = $emp->getEmpNumber();
            if (isset($seen[$empId])) {
                continue;
            }
            $seen[$empId] = true;
            $matched[] = $leave;
            if ($leave->getLeaveRequest() !== null) {
                $requestIds[] = (int)$leave->getLeaveRequest()->getId();
            }
        }

        $periodByRequest = $this->fetchPeriodByRequest($requestIds);

        $recipients = [];
        foreach ($matched as $leave) {
            $emp = $leave->getEmployee();
            $requestId = $leave->getLeaveRequest() !== null
                ? (int)$leave->getLeaveRequest()->getId()
                : null;
            $period = $requestId !== null && isset($periodByRequest[$requestId])
                ? $periodByRequest[$requestId]
                : ['start' => $leave->getDate(), 'end' => $leave->getDate()];

            $recipients[] = new WorkspaceNotificationRecipient(
                trim($emp->getFirstName() . ' ' . $emp->getLastName()),
                $emp->getSubDivision() ? $emp->getSubDivision()->getName() : null,
                $leave->getLeaveType() ? $leave->getLeaveType()->getName() : null,
                $period['start'],
                $period['end']
            );
        }
        return $recipients;
    }

    /**
     * @param int[] $requestIds
     * @return array<int, array{start:DateTime, end:DateTime}>
     */
    private function fetchPeriodByRequest(array $requestIds): array
    {
        if (count($requestIds) === 0) {
            return [];
        }

        $rows = $this->createQueryBuilder(Leave::class, 'l')
            ->select('IDENTITY(l.leaveRequest) AS request_id', 'MIN(l.date) AS start_date', 'MAX(l.date) AS end_date')
            ->andWhere('IDENTITY(l.leaveRequest) IN (:ids)')
            ->setParameter('ids', $requestIds)
            ->groupBy('l.leaveRequest')
            ->getQuery()
            ->getArrayResult();

        $byId = [];
        foreach ($rows as $row) {
            $byId[(int)$row['request_id']] = [
                'start' => $row['start_date'] instanceof DateTime
                    ? $row['start_date']
                    : new DateTime((string)$row['start_date']),
                'end' => $row['end_date'] instanceof DateTime
                    ? $row['end_date']
                    : new DateTime((string)$row['end_date']),
            ];
        }
        return $byId;
    }
}
