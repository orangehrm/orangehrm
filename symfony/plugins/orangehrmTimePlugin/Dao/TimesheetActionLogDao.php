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

namespace OrangeHRM\Time\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\TimesheetActionLog;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Time\Dto\TimesheetActionLogSearchFilterParams;

class TimesheetActionLogDao extends BaseDao
{
    /**
     * @param  int  $timesheetId
     * @param  TimesheetActionLogSearchFilterParams  $timesheetActionLogParamHolder
     * @return TimesheetActionLog[]
     */
    public function getTimesheetActionLogs(
        int $timesheetId,
        TimesheetActionLogSearchFilterParams $timesheetActionLogParamHolder
    ): array {
        $qb = $this->getTimesheetActionLogsPaginator($timesheetId, $timesheetActionLogParamHolder);
        return $qb->getQuery()->execute();
    }

    /**
     * @param  int  $timesheetId
     * @param  TimesheetActionLogSearchFilterParams  $timesheetActionLogParamHolder
     * @return Paginator
     */
    protected function getTimesheetActionLogsPaginator(
        int $timesheetId,
        TimesheetActionLogSearchFilterParams $timesheetActionLogParamHolder
    ): Paginator {
        $qb = $this->createQueryBuilder(TimesheetActionLog::class, 'timesheetActionLog');
        $qb->leftJoin('timesheetActionLog.performedUser', 'performedUser');
        $qb->leftJoin('timesheetActionLog.timesheet', 'timesheet');

        $this->setSortingAndPaginationParams($qb, $timesheetActionLogParamHolder);

        if (!is_null($timesheetActionLogParamHolder->getAction())) {
            $qb->andWhere('timesheetActionLog.action = :action')
                ->setParameter('action', $timesheetActionLogParamHolder->getAction());
        }
        if (!is_null($timesheetActionLogParamHolder->getDateTime())) {
            $qb->andWhere('timesheetActionLog.dateTime = :dateTime')
                ->setParameter('dateTime', $timesheetActionLogParamHolder->getDateTime());
        }
        if (!is_null($timesheetActionLogParamHolder->getUserId())) {
            $qb->andWhere('performedUser.id = :performedUserId')
                ->setParameter('performedUserId', $timesheetActionLogParamHolder->getUserId());
        }
        $qb->andWhere('timesheet.id = :timesheetId')
            ->setParameter('timesheetId', $timesheetId);

        return $this->getPaginator($qb);
    }

    /**
     * @param $timesheetId
     * @param  TimesheetActionLogSearchFilterParams  $timesheetActionLogParamHolder
     * @return int
     */
    public function getTimesheetActionLogsCount(
        $timesheetId,
        TimesheetActionLogSearchFilterParams $timesheetActionLogParamHolder
    ): int {
        return $this->getTimesheetActionLogsPaginator($timesheetId, $timesheetActionLogParamHolder)->count();
    }
}
