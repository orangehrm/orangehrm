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

class LeaveTodayResolver implements RecipientResolverInterface
{
    use EntityManagerHelperTrait;

    private const APPROVED_STATUSES = [
        Leave::LEAVE_STATUS_LEAVE_APPROVED,
        Leave::LEAVE_STATUS_LEAVE_TAKEN,
    ];

    /**
     * @return SlackEmployeeRecipient[]
     */
    public function resolve(DateTime $date, ?int $subunitId): array
    {
        $qb = $this->createQueryBuilder(Leave::class, 'l')
            ->innerJoin('l.employee', 'e')
            ->addSelect('e')
            ->leftJoin('l.leaveType', 'lt')
            ->addSelect('lt')
            ->leftJoin('e.subDivision', 'sub')
            ->addSelect('sub')
            ->andWhere('l.date = :date')
            ->setParameter('date', $date->format('Y-m-d'))
            ->andWhere('l.status IN (:statuses)')
            ->setParameter('statuses', self::APPROVED_STATUSES)
            ->andWhere('e.employeeTerminationRecord IS NULL')
            ->orderBy('e.firstName', 'ASC')
            ->addOrderBy('e.lastName', 'ASC');

        if ($subunitId !== null) {
            $qb->andWhere('IDENTITY(e.subDivision) = :subunitId')
                ->setParameter('subunitId', $subunitId);
        }

        $seen = [];
        $recipients = [];
        foreach ($qb->getQuery()->getResult() as $leave) {
            /** @var Leave $leave */
            $emp = $leave->getEmployee();
            $empId = $emp->getEmpNumber();
            if (isset($seen[$empId])) {
                continue;
            }
            $seen[$empId] = true;

            $recipients[] = new SlackEmployeeRecipient(
                trim($emp->getFirstName() . ' ' . $emp->getLastName()),
                $emp->getSubDivision() ? $emp->getSubDivision()->getName() : null,
                $leave->getLeaveType() ? $leave->getLeaveType()->getName() : null
            );
        }
        return $recipients;
    }
}
