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

use DateTime;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\AttendanceRecord;
use OrangeHRM\ORM\ListSorter;

class EmployeeTimeAtWorkDao extends BaseDao
{
    /**
     * @param int $empNumber
     * @return AttendanceRecord|null
     */
    public function getLatestAttendanceRecordByEmpNumber(int $empNumber): ?AttendanceRecord
    {
        $qb = $this->createQueryBuilder(AttendanceRecord::class, 'attendanceRecord')
            ->andWhere('attendanceRecord.employee = :empNumber')
            ->setParameter('empNumber', $empNumber)
            ->setMaxResults(1)
            ->addOrderBy('attendanceRecord.punchInUtcTime', ListSorter::DESCENDING);
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param int $empNumber
     * @param DateTime $startUTCDateTime
     * @param DateTime $endUTCDateTime
     * @return AttendanceRecord[]
     */
    public function getAttendanceRecordsByEmployeeAndDate(
        int $empNumber,
        DateTime $startUTCDateTime,
        DateTime $endUTCDateTime
    ): array {
        $qb = $this->createQueryBuilder(AttendanceRecord::class, 'attendanceRecord');
        $qb->andWhere('attendanceRecord.employee = :empNumber');
        $qb->setParameter('empNumber', $empNumber);
        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->between(
                    'attendanceRecord.punchInUtcTime',
                    ':start',
                    ':end'
                ),
                $qb->expr()->between(
                    'attendanceRecord.punchOutUtcTime',
                    ':start',
                    ':end'
                ),
                $qb->expr()->andX(
                    $qb->expr()->lte('attendanceRecord.punchInUtcTime', ':start'),
                    $qb->expr()->gte('attendanceRecord.punchOutUtcTime', ':end')
                )
            )
        );
        $qb->setParameter('start', $startUTCDateTime);
        $qb->setParameter('end', $endUTCDateTime);
        return $qb->getQuery()->execute();
    }

    /**
     * @param int $empNumber
     * @return AttendanceRecord|null
     */
    public function getOpenAttendanceRecordByEmpNumber(int $empNumber): ?AttendanceRecord
    {
        $qb = $this->createQueryBuilder(AttendanceRecord::class, 'attendanceRecord');
        $qb->andWhere('attendanceRecord.state = :state');
        $qb->setParameter('state', AttendanceRecord::STATE_PUNCHED_IN);
        $qb->andWhere('attendanceRecord.employee = :empNumber');
        $qb->setParameter('empNumber', $empNumber);
        return $qb->getQuery()->getOneOrNullResult();
    }
}
