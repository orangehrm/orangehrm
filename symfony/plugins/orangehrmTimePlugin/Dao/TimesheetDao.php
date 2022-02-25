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

use DateTime;
use LogicException;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Timesheet;
use OrangeHRM\Entity\TimesheetActionLog;
use OrangeHRM\Entity\TimesheetItem;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\ORM\QueryBuilderWrapper;
use OrangeHRM\Time\Dto\DefaultTimesheetSearchFilterParams;
use OrangeHRM\Time\Dto\EmployeeReportsSearchFilterParams;
use OrangeHRM\Time\Dto\EmployeeTimesheetListSearchFilterParams;
use OrangeHRM\Time\Dto\TimesheetActionLogSearchFilterParams;
use OrangeHRM\Time\Dto\TimesheetSearchFilterParams;
use OrangeHRM\Time\Traits\Service\TimesheetServiceTrait;

class TimesheetDao extends BaseDao
{
    use TimesheetServiceTrait;

    /**
     * @param int $timesheetId
     * @return Timesheet|null
     */
    public function getTimesheetById(int $timesheetId): ?Timesheet
    {
        return $this->getRepository(Timesheet::class)->find($timesheetId);
    }

    /**
     * @param Timesheet $timesheet
     * @return Timesheet
     */
    public function saveTimesheet(Timesheet $timesheet): Timesheet
    {
        $this->persist($timesheet);
        return $timesheet;
    }

    /**
     * @param int $timesheetId
     * @param int $timesheetItemId
     * @return TimesheetItem|null
     */
    public function getTimesheetItemByTimesheetIdAndTimesheetItemId(
        int $timesheetId,
        int $timesheetItemId
    ): ?TimesheetItem {
        $timesheetItem = $this->getRepository(TimesheetItem::class)
            ->findOneBy(['id' => $timesheetItemId, 'timesheet' => $timesheetId]);
        return ($timesheetItem instanceof TimesheetItem) ? $timesheetItem : null;
    }

    /**
     * @param int $timesheetId
     * @return TimesheetItem[]
     */
    public function getTimesheetItemsByTimesheetId(int $timesheetId): array
    {
        $q = $this->createQueryBuilder(TimesheetItem::class, 'timesheetItem')
            ->andWhere('IDENTITY(timesheetItem.timesheet) = :timesheetId')
            ->setParameter('timesheetId', $timesheetId);

        return $q->getQuery()->execute();
    }

    /**
     * @param TimesheetItem $timesheetItem
     * @return TimesheetItem
     */
    public function saveTimesheetItem(TimesheetItem $timesheetItem): TimesheetItem
    {
        $this->persist($timesheetItem);
        return $timesheetItem;
    }

    /**
     * @param TimesheetActionLog $timesheetActionLog
     * @return TimesheetActionLog
     */
    public function saveTimesheetActionLog(TimesheetActionLog $timesheetActionLog): TimesheetActionLog
    {
        $this->persist($timesheetActionLog);
        return $timesheetActionLog;
    }

    /**
     * @param DateTime $date
     * @param int|null $employeeNumber
     * @return bool
     */
    public function hasTimesheetForStartDate(int $employeeNumber, DateTime $date): bool
    {
        $q = $this->createQueryBuilder(Timesheet::class, 'timesheet');
        $q->andWhere('timesheet.startDate = :date');
        $q->andWhere('timesheet.employee = :employeeNumber');
        $q->setParameter('date', $date);
        $q->setParameter('employeeNumber', $employeeNumber);

        return $this->getPaginator($q)->count() > 0;
    }

    /**
     * @param int $timesheetId
     * @param TimesheetActionLogSearchFilterParams $timesheetActionLogParamHolder
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
     * @param int $timesheetId
     * @param TimesheetActionLogSearchFilterParams $timesheetActionLogParamHolder
     * @return Paginator
     */
    protected function getTimesheetActionLogsPaginator(
        int $timesheetId,
        TimesheetActionLogSearchFilterParams $timesheetActionLogParamHolder
    ): Paginator {
        $qb = $this->createQueryBuilder(TimesheetActionLog::class, 'timesheetActionLog');
        $qb->leftJoin('timesheetActionLog.timesheet', 'timesheet');

        $this->setSortingAndPaginationParams($qb, $timesheetActionLogParamHolder);

        $qb->andWhere('timesheet.id = :timesheetId')
            ->setParameter('timesheetId', $timesheetId);

        return $this->getPaginator($qb);
    }

    /**
     * @param $timesheetId
     * @param TimesheetActionLogSearchFilterParams $timesheetActionLogParamHolder
     * @return int
     */
    public function getTimesheetActionLogsCount(
        $timesheetId,
        TimesheetActionLogSearchFilterParams $timesheetActionLogParamHolder
    ): int {
        return $this->getTimesheetActionLogsPaginator($timesheetId, $timesheetActionLogParamHolder)->count();
    }

    /**
     * @param TimesheetSearchFilterParams $timesheetParamHolder
     * @return array
     */
    public function getTimesheetByStartAndEndDate(
        TimesheetSearchFilterParams $timesheetParamHolder
    ): array {
        $qb = $this->getTimesheetPaginator(
            $timesheetParamHolder,
        );
        return $qb->getQuery()->execute();
    }

    /**
     * @param TimesheetSearchFilterParams $timesheetParamHolder
     * @return Paginator
     */
    private function getTimesheetPaginator(
        TimesheetSearchFilterParams $timesheetParamHolder
    ): Paginator {
        $qb = $this->createQueryBuilder(Timesheet::class, 'timesheet');

        $this->setSortingAndPaginationParams($qb, $timesheetParamHolder);
        if (!is_null($timesheetParamHolder->getToDate() && !is_null($timesheetParamHolder->getFromDate()))) {
            $qb->andWhere(
                $qb->expr()->between(
                    'timesheet.startDate',
                    ':startDate',
                    ':endDate'
                )
            )
                ->setParameter('startDate', $timesheetParamHolder->getFromDate())
                ->setParameter('endDate', $timesheetParamHolder->getToDate());
        }

        $qb->andWhere('timesheet.employee = :empNumber')
            ->setParameter('empNumber', $timesheetParamHolder->getEmpNumber());

        return $this->getPaginator($qb);
    }

    /**
     * @param TimesheetSearchFilterParams $timesheetParamHolder
     * @return int
     */
    public function getTimesheetCount(TimesheetSearchFilterParams $timesheetParamHolder): int
    {
        return $this->getTimesheetPaginator($timesheetParamHolder)->count();
    }

    /**
     * @param int $timesheetId
     * @param array $rows e.g. array(['projectId' => 1, 'activityId' => 2], ['projectId' => 1, 'activityId' => 3])
     * @return int
     */
    public function deleteTimesheetRows(int $timesheetId, array $rows): int
    {
        if (empty($rows)) {
            return 0;
        }
        $q = $this->createQueryBuilder(TimesheetItem::class, 'ti')
            ->delete();
        foreach ($rows as $i => $row) {
            if (!(isset($row['projectId']) && isset($row['activityId']))) {
                throw new LogicException('`projectId` & `activityId` required attributes');
            }
            $timesheetIdParamKey = 'timesheetId_' . $i;
            $projectIdParamKey = 'projectId_' . $i;
            $activityIdParamKey = 'activityId_' . $i;
            $q->orWhere(
                $q->expr()->andX(
                    $q->expr()->eq('ti.timesheet', ':' . $timesheetIdParamKey),
                    $q->expr()->eq('ti.project', ':' . $projectIdParamKey),
                    $q->expr()->eq('ti.projectActivity', ':' . $activityIdParamKey)
                )
            );
            $q->setParameter($timesheetIdParamKey, $timesheetId)
                ->setParameter($projectIdParamKey, $row['projectId'])
                ->setParameter($activityIdParamKey, $row['activityId']);
        }

        return $q->getQuery()->execute();
    }

    /**
     * @param array<string, TimesheetItem> $timesheetItems
     */
    public function saveAndUpdateTimesheetItems(array $timesheetItems): void
    {
        $q = $this->createQueryBuilder(TimesheetItem::class, 'ti');

        $timesheetRowKeys = [];
        foreach (array_values($timesheetItems) as $i => $timesheetItem) {
            $timesheetIdParamKey = 'timesheetId_' . $i;
            $projectIdParamKey = 'projectId_' . $i;
            $activityIdParamKey = 'activityId_' . $i;

            /** @var TimesheetItem $timesheetItem */
            $timesheetId = $timesheetItem->getTimesheet()->getId();
            $projectId = $timesheetItem->getProject()->getId();
            $activityId = $timesheetItem->getProjectActivity()->getId();
            if ($timesheetItem->getProjectActivity()->getProject()->getId() !== $projectId) {
                throw new LogicException(
                    "The project activity (id: $activityId) not belongs to provided project (id: $projectId)"
                );
            }
            $timesheetRowKey = $timesheetId . '_' . $projectId . '_' . $activityId;
            if (isset($timesheetRowKeys[$timesheetRowKey])) {
                continue;
            }
            $timesheetRowKeys[$timesheetRowKey] = [$timesheetId, $projectId, $activityId];

            // Executing where clause only depend on `timesheet_id`, `project_id`, `activity_id`,
            // No point of adding `date` also
            $q->orWhere(
                $q->expr()->andX(
                    $q->expr()->eq('ti.timesheet', ':' . $timesheetIdParamKey),
                    $q->expr()->eq('ti.project', ':' . $projectIdParamKey),
                    $q->expr()->eq('ti.projectActivity', ':' . $activityIdParamKey),
                )
            );
            $q->setParameter($timesheetIdParamKey, $timesheetId)
                ->setParameter($projectIdParamKey, $projectId)
                ->setParameter($activityIdParamKey, $activityId);
        }

        /** @var array<string, TimesheetItem> $updatableTimesheetItems */
        $updatableTimesheetItems = [];
        foreach ($q->getQuery()->execute() as $updatableTimesheetItem) {
            $itemKey = $this->getTimesheetService()->generateTimesheetItemKey(
                $updatableTimesheetItem->getTimesheet()->getId(),
                $updatableTimesheetItem->getProject()->getId(),
                $updatableTimesheetItem->getProjectActivity()->getId(),
                $updatableTimesheetItem->getDate()
            );
            $updatableTimesheetItems[$itemKey] = $updatableTimesheetItem;
        }

        foreach ($timesheetItems as $key => $timesheetItem) {
            if (isset($updatableTimesheetItems[$key])) {
                $updatableTimesheetItems[$key]->setDuration($timesheetItem->getDuration());
                // update
                $this->getEntityManager()->persist($updatableTimesheetItems[$key]);
                continue;
            }
            // create
            $this->getEntityManager()->persist($timesheetItem);
        }
        $this->getEntityManager()->flush();
    }

    /**
     * @param int $timesheetId
     * @param int $activityId
     * @param int $projectId
     * @return bool
     */
    public function isDuplicateTimesheetItem(
        int $timesheetId,
        int $activityId,
        int $projectId
    ): bool {
        $qb = $this->createQueryBuilder(TimesheetItem::class, 'timesheetItem');
        $qb->andWhere('timesheetItem.timesheet = :timesheetId');
        $qb->setParameter('timesheetId', $timesheetId);
        $qb->andWhere('timesheetItem.project = :projectId');
        $qb->setParameter('projectId', $projectId);
        $qb->andwhere('timesheetItem.projectActivity = :activityId');
        $qb->setParameter('activityId', $activityId);

        return $this->getPaginator($qb)->count() > 0;
    }

    /**
     * @param EmployeeTimesheetListSearchFilterParams $employeeTimesheetActionSearchFilterParams
     * @return Timesheet[]
     */
    public function getEmployeeTimesheetList(
        EmployeeTimesheetListSearchFilterParams $employeeTimesheetActionSearchFilterParams
    ): array {
        $paginator = $this->getEmployeeTimesheetPaginator($employeeTimesheetActionSearchFilterParams);
        return $paginator->getQuery()->execute();
    }

    /**
     * @param EmployeeTimesheetListSearchFilterParams $employeeTimesheetActionSearchFilterParams
     * @return Paginator
     */
    public function getEmployeeTimesheetPaginator(
        EmployeeTimesheetListSearchFilterParams $employeeTimesheetActionSearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(Timesheet::class, 'timesheet');
        $q->leftJoin('timesheet.employee', 'employee');

        if (!is_null($employeeTimesheetActionSearchFilterParams->getEmployeeNumbers())) {
            $q->andWhere($q->expr()->in('timesheet.employee', ':empNumbers'))
                ->setParameter('empNumbers', $employeeTimesheetActionSearchFilterParams->getEmployeeNumbers());
        }

        if (!empty($employeeTimesheetActionSearchFilterParams->getActionableStatesList())) {
            $q->andWhere($q->expr()->in('timesheet.state', ':states'))
                ->setParameter('states', $employeeTimesheetActionSearchFilterParams->getActionableStatesList());
        }

        $this->setSortingAndPaginationParams($q, $employeeTimesheetActionSearchFilterParams);
        $q->addOrderBy('employee.lastName');
        return $this->getPaginator($q);
    }

    /**
     * @param EmployeeTimesheetListSearchFilterParams $employeeTimesheetActionSearchFilterParams
     * @return int
     */
    public function getEmployeeTimesheetListCount(
        EmployeeTimesheetListSearchFilterParams $employeeTimesheetActionSearchFilterParams
    ): int {
        $paginator = $this->getEmployeeTimesheetPaginator($employeeTimesheetActionSearchFilterParams);
        return $paginator->count();
    }

    /**
     * @param DefaultTimesheetSearchFilterParams $defaultTimesheetSearchFilterParams
     * @return Timesheet|null
     */
    public function getDefaultTimesheet(
        DefaultTimesheetSearchFilterParams $defaultTimesheetSearchFilterParams
    ): ?Timesheet {
        $qb = $this->createQueryBuilder(Timesheet::class, 'timesheet');
        $qb->andWhere('timesheet.employee = :empNumber');
        $qb->setParameter('empNumber', $defaultTimesheetSearchFilterParams->getEmpNumber());
        if (!is_null($defaultTimesheetSearchFilterParams->getFromDate()) && !is_null(
            $defaultTimesheetSearchFilterParams->getToDate()
        )) {
            $qb->andWhere('timesheet.startDate = :fromDate');
            $qb->setParameter('fromDate', $defaultTimesheetSearchFilterParams->getFromDate());
            $qb->andWhere('timesheet.endDate = :toDate');
            $qb->setParameter('toDate', $defaultTimesheetSearchFilterParams->getToDate());
        } else {
            $qb->orderBy('timesheet.startDate', ListSorter::DESCENDING);
            $qb->setMaxResults(1);
        }
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param  EmployeeReportsSearchFilterParams  $filterParams
     * @return array
     */
    public function getTimesheetItemsForEmployeeReport(
        EmployeeReportsSearchFilterParams $filterParams
    ): array {
        return $this->getTimesheetItemsPaginatorForEmployeeReport($filterParams)->getQuery()->execute();
    }

    /**
     * @param  EmployeeReportsSearchFilterParams  $filterParams
     * @return int
     */
    public function getTimesheetItemsCountForEmployeeReport(EmployeeReportsSearchFilterParams $filterParams): int
    {
        return $this->getTimesheetItemsPaginatorForEmployeeReport($filterParams)->count();
    }

    /**
     * @param  EmployeeReportsSearchFilterParams  $filterParams
     * @return Paginator
     */
    private function getTimesheetItemsPaginatorForEmployeeReport(
        EmployeeReportsSearchFilterParams $filterParams
    ): Paginator {
        $qb = $this->getTimesheetItemsForEmployeeReportQueryBuilderWrapper($filterParams)->getQueryBuilder();
        $qb->addSelect('COALESCE(SUM(timesheetItem.duration),0) AS totalDurationByGroup');
        $qb->addGroupBy('timesheetItem.project');
        $qb->addGroupBy('timesheetItem.projectActivity');
        return $this->getPaginator($qb);
    }

    /**
     * @param  EmployeeReportsSearchFilterParams  $filterParams
     * @return QueryBuilderWrapper
     */
    private function getTimesheetItemsForEmployeeReportQueryBuilderWrapper(
        EmployeeReportsSearchFilterParams $filterParams
    ): QueryBuilderWrapper {
        $q = $this->createQueryBuilder(TimesheetItem::class, 'timesheetItem');
        $q->leftJoin('timesheetItem.timesheet', 'timesheet');
        $q->leftJoin('timesheetItem.projectActivity', 'projectActivity');
        $q->leftJoin('timesheetItem.project', 'project');
        $q->leftJoin('project.customer', 'customer');
        $q->andWhere('timesheetItem.employee = :empNumber');
        $q->setParameter('empNumber', $filterParams->getEmpNumber());
        $this->setSortingAndPaginationParams($q, $filterParams);

        if (!is_null($filterParams->getProjectId())) {
            $q->andWhere('timesheetItem.project = :projectId');
            $q->setParameter('projectId', $filterParams->getProjectId());
        }

        if (!is_null($filterParams->getActivityId())) {
            $q->andWhere('timesheetItem.projectActivity = :activityId');
            $q->setParameter('activityId', $filterParams->getActivityId());
        }

        //Timesheet items after fromDate (including fromDate) and Timesheet items before toDate (including toDate)
        if (!is_null($filterParams->getFromDate()) && !is_null($filterParams->getToDate())) {
            $q->andWhere($q->expr()->between('timesheetItem.date', ':fromDate', ':toDate'));
            $q->setParameter('fromDate', $filterParams->getFromDate());
            $q->setParameter('toDate', $filterParams->getToDate());
        }
        //Timesheet items after fromDate (including fromDate)
        elseif (!is_null($filterParams->getFromDate())) {
            $q->andWhere($q->expr()->gte('timesheetItem.date', ':fromDate'));
            $q->setParameter('fromDate', $filterParams->getFromDate());
        }

        //Timesheet items before toDate (including toDate)
        elseif (!is_null($filterParams->getToDate())) {
            $q->andWhere($q->expr()->lte('timesheetItem.date', ':toDate'));
            $q->setParameter('toDate', $filterParams->getToDate());
        }

        if ($filterParams->getIncludeTimesheets(
            ) === EmployeeReportsSearchFilterParams::INCLUDE_TIMESHEETS_APPROVED_ONLY) {
            $q->andWhere('timesheet.state = :state');
            $q->setParameter('state', EmployeeReportsSearchFilterParams::TIMESHEET_APPROVED_STATE);
        }
        //else: neither fromDate nor toDate is available

        $q->addOrderBy('project.name', ListSorter::ASCENDING);
        $q->addOrderBy('projectActivity.name', ListSorter::ASCENDING);

        return $this->getQueryBuilderWrapper($q);
    }

    /**
     * @param  EmployeeReportsSearchFilterParams  $filterParams
     * @return int
     */
    public function getTotalDurationForEmployeeReport(EmployeeReportsSearchFilterParams $filterParams): int
    {
        $qb = $this->getTimesheetItemsForEmployeeReportQueryBuilderWrapper($filterParams)->getQueryBuilder();
        //COALESCE usage => if timesheetItem.duration == null, it will be converted to 0
        $qb->select('COALESCE(SUM(timesheetItem.duration),0) AS totalDuration');
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param int $timesheetId
     * @param int $projectId
     * @param int $activityId
     * @param DateTime $date
     * @return TimesheetItem | null
     */
    public function getTimesheetItemByProjectIdAndTimesheetIdAndActivityIdAndDate(
        int $timesheetId,
        int $projectId,
        int $activityId,
        DateTime $date
    ): ?TimesheetItem {
        return $this->getRepository(TimesheetItem::class)->findOneBy([
            'timesheet' => $timesheetId,
            'project' => $projectId,
            'projectActivity' => $activityId,
            'date' => $date
        ]);
    }
}
