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

namespace OrangeHRM\Slack\Service\Resolver;

use DateTime;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Slack\Dto\SlackEmployeeRecipient;

/**
 * Resolves recipients for the "Employees on leave today" notification.
 *
 * Two open items recorded here as design notes — both deliberately out of scope
 * for 5.9 per the June 3 decisions:
 *
 * 1. Long-leave repeat notifications.
 *    A multi-day leave (maternity, extended medical, sabbatical, ...) currently
 *    fires this notification once per day for the full duration — same employee,
 *    same name, every morning for months. Suppression is treated as a
 *    *leave-type configuration* concern, not something this resolver should
 *    solve, because:
 *      - The notion of "this leave type is too long to mention daily" is a
 *        property of the leave type, not of the recipient query.
 *      - Future leave types (Bereavement, Jury Duty, ...) will each want
 *        their own suppression rule, ideally configured by admins on the
 *        leave type itself rather than hardcoded here.
 *    Future ticket: add a `notify_on_each_day` flag (or similar) to leave
 *    type, then AND it into the query below.
 *
 * 2. Non-approved / medical leaves (spec TBD).
 *    There is an unresolved TBD in the spec on whether leaves tagged
 *    "Not-Approved" (e.g. pending-approval medical/sick leaves) should
 *    surface in this notification. Conservative default in 5.9: filter to
 *    APPROVED + TAKEN only — see {@see APPROVED_STATUSES} — because
 *    surfacing pending leaves before the line manager has actioned them
 *    feels privacy-sensitive. Revisit when the spec is settled.
 */
class LeaveTodayResolver implements RecipientResolverInterface
{
    use EntityManagerHelperTrait;

    /**
     * Statuses representing a confirmed absence today. APPROVED + TAKEN only;
     * PENDING / REJECTED / CANCELLED are excluded. The "Not-Approved" /
     * pending-medical question is flagged as the open spec item in the class
     * docblock rather than answered with a code change.
     */
    private const APPROVED_STATUSES = [
        Leave::LEAVE_STATUS_LEAVE_APPROVED,
        Leave::LEAVE_STATUS_LEAVE_TAKEN,
    ];

    /**
     * @param int[] $subunitIds Multi-subunit filter; empty array = include all employees.
     * @return SlackEmployeeRecipient[]
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

        // Pass 1 — dedupe matched leaves by employee, collect each kept row's
        // leave_request_id so we can fetch the request's full date span in one
        // batch query instead of N+1.
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

        // Pass 2 — for every retained leave_request_id, compute (min date, max
        // date) across ALL its leave rows. We surface the full applied period
        // (any status) because that's the span the admin sees on the leave UI;
        // restricting to APPROVED+TAKEN would hide pending days mid-request.
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

            $recipients[] = new SlackEmployeeRecipient(
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
     * Returns `[leaveRequestId => ['start' => DateTime, 'end' => DateTime]]`.
     * Empty input → empty array (no extra query fired).
     *
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
