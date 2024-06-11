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

namespace OrangeHRM\Time\Dao;

use Doctrine\ORM\QueryBuilder;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Project;
use OrangeHRM\Entity\ProjectActivity;
use OrangeHRM\Entity\ProjectAdmin;
use OrangeHRM\Entity\TimesheetItem;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\ORM\QueryBuilderWrapper;
use OrangeHRM\Time\Dto\ProjectActivityDetailedReportSearchFilterParams;
use OrangeHRM\Time\Dto\ProjectReportSearchFilterParams;
use OrangeHRM\Time\Dto\ProjectSearchFilterParams;

class ProjectDao extends BaseDao
{
    /**
     * @param Project $project
     * @return Project
     */
    public function saveProject(Project $project): Project
    {
        $this->persist($project);
        return $project;
    }

    /**
     * @param int[] $ids
     * @return int
     */
    public function deleteProjects(array $ids): int
    {
        $q = $this->createQueryBuilder(Project::class, 'project');
        $q->update()
            ->set('project.deleted', ':deleted')
            ->setParameter('deleted', true)
            ->where($q->expr()->in('project.id', ':ids'))
            ->setParameter('ids', $ids);
        return $q->getQuery()->execute();
    }

    /**
     * @param int $id
     * @return Project|null
     */
    public function getProjectById(int $id): ?Project
    {
        $qb = $this->createQueryBuilder(Project::class, 'project');
        $qb->andWhere('project.id = :id')->setParameter('id', $id);
        $qb->andWhere('project.deleted = :deleted')->setParameter('deleted', false);
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param int[] $ids
     * @return int[]
     */
    public function getExistingProjectIds(array $ids): array
    {
        $qb = $this->createQueryBuilder(Project::class, 'project');

        $qb->select('project.id')
            ->andWhere($qb->expr()->in('project.id', ':ids'))
            ->andWhere($qb->expr()->eq('project.deleted', ':deleted'))
            ->setParameter('ids', $ids)
            ->setParameter('deleted', false);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param ProjectSearchFilterParams $projectSearchFilterParamHolder
     * @return Project[]
     */
    public function getProjects(ProjectSearchFilterParams $projectSearchFilterParamHolder): array
    {
        $qb = $this->getProjectsPaginator($projectSearchFilterParamHolder);
        return $qb->getQuery()->execute();
    }

    /**
     * @param ProjectSearchFilterParams $projectSearchFilterParamHolder
     * @return Paginator
     */
    protected function getProjectsPaginator(ProjectSearchFilterParams $projectSearchFilterParamHolder): Paginator
    {
        $qb = $this->createQueryBuilder(Project::class, 'project');
        $qb->leftJoin('project.customer', 'customer');
        $qb->leftJoin('project.projectAdmins', 'projectAdmin');

        $this->setSortingAndPaginationParams($qb, $projectSearchFilterParamHolder);

        if (!is_null($projectSearchFilterParamHolder->getProjectIds())) {
            $qb->andWhere($qb->expr()->in('project.id', ':projectIds'))
                ->setParameter('projectIds', $projectSearchFilterParamHolder->getProjectIds());
        }
        if (!is_null($projectSearchFilterParamHolder->getCustomerId())) {
            $qb->andWhere('customer.id = :customerId')
                ->setParameter('customerId', $projectSearchFilterParamHolder->getCustomerId());
        }
        if (!is_null($projectSearchFilterParamHolder->getEmpNumber())) {
            $qb->andWhere('projectAdmin.empNumber = :empNumber')
                ->setParameter('empNumber', $projectSearchFilterParamHolder->getEmpNumber());
        }
        if (!is_null($projectSearchFilterParamHolder->getCustomerOrProjectName())) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('project.name', ':customerOrProjectName'),
                    $qb->expr()->like('customer.name', ':customerOrProjectName'),
                )
            );
            $qb->setParameter(
                'customerOrProjectName',
                '%' . $projectSearchFilterParamHolder->getCustomerOrProjectName() . '%'
            );
        }
        if (!empty($projectSearchFilterParamHolder->getName())) {
            $qb->andWhere($qb->expr()->like('project.name', ':projectName'))
                ->setParameter('projectName', '%' . $projectSearchFilterParamHolder->getName() . '%');
        }
        if (!empty($projectSearchFilterParamHolder->getExcludeProjectIds())) {
            $qb->andWhere($qb->expr()->notIn('project.id', ':excludeProjectIds'))
                ->setParameter('excludeProjectIds', $projectSearchFilterParamHolder->getExcludeProjectIds());
        }

        $qb->andWhere('project.deleted = :deleted')
            ->setParameter('deleted', false);
        return $this->getPaginator($qb);
    }

    /**
     * @param ProjectSearchFilterParams $projectSearchFilterParamHolder
     * @return int
     */
    public function getProjectsCount(ProjectSearchFilterParams $projectSearchFilterParamHolder): int
    {
        return $this->getProjectsPaginator($projectSearchFilterParamHolder)->count();
    }

    /**
     * @param string $projectName
     * @param int|null $projectId
     * @return bool
     */
    public function isProjectNameTaken(string $projectName, int $customerId, ?int $projectId = null): bool
    {
        $q = $this->createQueryBuilder(Project::class, 'project');
        $q->andWhere('project.name = :projectName');
        $q->setParameter('projectName', $projectName);
        $q->andWhere('project.deleted = :deleted');
        $q->setParameter('deleted', false);
        $q->andWhere('project.customer = :customerId');
        $q->setParameter('customerId', $customerId);
        if (!is_null($projectId)) {
            $q->andWhere('project.id != :projectId');
            $q->setParameter('projectId', $projectId);
        }
        return $this->getPaginator($q)->count() === 0;
    }

    /**
     * @param int|null $empNumber
     * @return bool
     */
    public function isProjectAdmin(?int $empNumber): bool
    {
        if (is_null($empNumber)) {
            return false;
        }
        $q = $this->createQueryBuilder(ProjectAdmin::class, 'projectAdmin')
            ->leftJoin('projectAdmin.project', 'project')
            ->andWhere('projectAdmin.employee = :empNumber')
            ->setParameter('empNumber', $empNumber)
            ->andWhere('project.deleted = :deleted')
            ->setParameter('deleted', false);
        return $this->getPaginator($q)->count() > 0;
    }

    /**
     * @param bool $includeDeleted
     * @return int[]
     */
    public function getProjectIdList(bool $includeDeleted = false): array
    {
        $q = $this->createQueryBuilder(Project::class, 'project');
        $q->select('project.id');
        if (!$includeDeleted) {
            $q->andWhere('project.deleted = :deleted')
                ->setParameter('deleted', false);
        }
        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }

    /**
     * @param int $projectAdminEmpNumber
     * @param bool $includeDeleted
     * @return int[]
     */
    public function getProjectIdListForProjectAdmin(int $projectAdminEmpNumber, bool $includeDeleted = false): array
    {
        $q = $this->createQueryBuilder(Project::class, 'project')
            ->select('project.id')
            ->innerJoin('project.projectAdmins', 'projectAdmin')
            ->andWhere('projectAdmin.empNumber = :projectAdminEmpNumber')
            ->setParameter('projectAdminEmpNumber', $projectAdminEmpNumber);
        if (!$includeDeleted) {
            $q->andWhere('project.deleted = :deleted')
                ->setParameter('deleted', false);
        }
        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }

    /**
     * @param int $projectId
     * @return bool
     */
    public function hasTimesheetItemsForProject(int $projectId): bool
    {
        $qb = $this->createQueryBuilder(TimesheetItem::class, 'timesheetItem');
        $qb->andWhere('timesheetItem.project = :projectId');
        $qb->setParameter('projectId', $projectId);
        return $this->getPaginator($qb)->count() > 0;
    }

    /**
     * @param ProjectReportSearchFilterParams $projectReportSearchFilterParams
     * @return array
     */
    public function getProjectReportCriteriaList(
        ProjectReportSearchFilterParams $projectReportSearchFilterParams
    ): array {
        return $this->getProjectReportCriteriaPaginator($projectReportSearchFilterParams)->getQuery()->execute();
    }

    /**
     * @param ProjectReportSearchFilterParams $projectReportSearchFilterParams
     * @return int
     */
    public function getProjectReportCriteriaListCount(
        ProjectReportSearchFilterParams $projectReportSearchFilterParams
    ): int {
        return $this->getProjectReportCriteriaPaginator($projectReportSearchFilterParams)->count();
    }

    /**
     * @param ProjectReportSearchFilterParams $projectReportSearchFilterParams
     * @return Paginator
     */
    private function getProjectReportCriteriaPaginator(
        ProjectReportSearchFilterParams $projectReportSearchFilterParams
    ): Paginator {
        $q = $this->getProjectReportQueryBuilderWrapper($projectReportSearchFilterParams)->getQueryBuilder();
        $q->select(
            'projectActivity.id AS activityId,
            projectActivity.name, 
            projectActivity.deleted AS deleted, 
            SUM(COALESCE(timesheetItem.duration, 0)) AS totalDuration'
        );
        $q->groupBy('projectActivity.id');

        return $this->getPaginator($q);
    }

    /**
     * @param ProjectReportSearchFilterParams $projectReportSearchFilterParams
     * @return int
     */
    public function getTotalDurationForProjectReport(
        ProjectReportSearchFilterParams $projectReportSearchFilterParams
    ): int {
        $q = $this->getProjectReportQueryBuilderWrapper($projectReportSearchFilterParams)->getQueryBuilder();
        $q->select('SUM(COALESCE(timesheetItem.duration, 0)) AS totalDuration');
        return $q->getQuery()->getSingleScalarResult() === null ? 0 : $q->getQuery()->getSingleScalarResult();
    }

    /**
     * @param ProjectReportSearchFilterParams $projectReportSearchFilterParams
     * @return QueryBuilderWrapper
     */
    private function getProjectReportQueryBuilderWrapper(
        ProjectReportSearchFilterParams $projectReportSearchFilterParams
    ): QueryBuilderWrapper {
        $q = $this->createQueryBuilder(ProjectActivity::class, 'projectActivity');

        $q->leftJoin('projectActivity.project', 'project');
        $q->leftJoin('projectActivity.timesheetItems', 'timesheetItem');
        $q->leftJoin('timesheetItem.timesheet', 'timesheet');

        $this->setSortingAndPaginationParams($q, $projectReportSearchFilterParams);

        if (!is_null($projectReportSearchFilterParams->getProjectId())) {
            $q->andWhere('projectActivity.project = :projectId');
            $q->setParameter('projectId', $projectReportSearchFilterParams->getProjectId());
        }

        if (!is_null($projectReportSearchFilterParams->getFromDate())) {
            $q->andWhere(
                $q->expr()->orX(
                    $q->expr()->isNull('timesheet.id'),
                    $q->expr()->gte('timesheetItem.date', ':fromDate')
                )
            )
                ->setParameter('fromDate', $projectReportSearchFilterParams->getFromDate());
        }

        if (!is_null($projectReportSearchFilterParams->getToDate())) {
            $q->andWhere(
                $q->expr()->orX(
                    $q->expr()->isNull('timesheet.id'),
                    $q->expr()->lte('timesheetItem.date', ':toDate')
                )
            )
                ->setParameter('toDate', $projectReportSearchFilterParams->getToDate());
        }

        if ($projectReportSearchFilterParams->getIncludeApproveTimesheet(
        ) === ProjectReportSearchFilterParams::INCLUDE_TIMESHEET_ONLY_APPROVED) {
            $q->andWhere('timesheet.state = :state');
            $q->setParameter('state', ProjectReportSearchFilterParams::TIMESHEET_STATE_APPROVED);
        }

        return $this->getQueryBuilderWrapper($q);
    }

    /**
     * @param ProjectReportSearchFilterParams $projectReportSearchFilterParams
     * @param QueryBuilder $q
     * @return QueryBuilderWrapper
     */
    private function getCommonQueryBuilderWrapper(
        ProjectReportSearchFilterParams $projectReportSearchFilterParams,
        QueryBuilder $q
    ): QueryBuilderWrapper {
        if (!is_null($projectReportSearchFilterParams->getFromDate()) && !is_null(
            $projectReportSearchFilterParams->getToDate()
        )) {
            $q->andWhere($q->expr()->between('timesheetItem.date', ':fromDate', ':toDate'))
                ->setParameter('fromDate', $projectReportSearchFilterParams->getFromDate())
                ->setParameter('toDate', $projectReportSearchFilterParams->getToDate());
        } elseif (!is_null($projectReportSearchFilterParams->getFromDate())) {
            $q->andWhere($q->expr()->gte('timesheetItem.date', ':fromDate'))
                ->setParameter('fromDate', $projectReportSearchFilterParams->getFromDate());
        } elseif (!is_null($projectReportSearchFilterParams->getToDate())) {
            $q->andWhere($q->expr()->lte('timesheetItem.date', ':toDate'))
                ->setParameter('toDate', $projectReportSearchFilterParams->getToDate());
        }

        if ($projectReportSearchFilterParams->getIncludeApproveTimesheet(
        ) === ProjectReportSearchFilterParams::INCLUDE_TIMESHEET_ONLY_APPROVED) {
            $q->andWhere('timesheet.state = :state');
            $q->setParameter('state', ProjectReportSearchFilterParams::TIMESHEET_STATE_APPROVED);
        }

        return $this->getQueryBuilderWrapper($q);
    }

    /**
     * @param ProjectActivityDetailedReportSearchFilterParams $projectActivityDetailedReportSearchFilterParams
     * @return array
     */
    public function getProjectActivityDetailedReportCriteriaList(
        ProjectActivityDetailedReportSearchFilterParams $projectActivityDetailedReportSearchFilterParams
    ): array {
        return $this->getProjectActivityReportCriteriaPaginator(
            $projectActivityDetailedReportSearchFilterParams
        )->getQuery()->execute();
    }

    /**
     * @param ProjectActivityDetailedReportSearchFilterParams $projectActivityDetailedReportSearchFilterParams
     * @return int
     */
    public function getProjectReportActivityDetailedCriteriaListCount(
        ProjectActivityDetailedReportSearchFilterParams $projectActivityDetailedReportSearchFilterParams
    ): int {
        return $this->getProjectActivityReportCriteriaPaginator(
            $projectActivityDetailedReportSearchFilterParams
        )->count();
    }

    /**
     * @param ProjectActivityDetailedReportSearchFilterParams $projectActivityDetailedReportSearchFilterParams
     * @return Paginator
     */
    private function getProjectActivityReportCriteriaPaginator(
        ProjectActivityDetailedReportSearchFilterParams $projectActivityDetailedReportSearchFilterParams
    ): Paginator {
        $q = $this->getProjectActivityDetailedReportQueryBuilderWrapper(
            $projectActivityDetailedReportSearchFilterParams
        )->getQueryBuilder();
        $q->select(
            'CONCAT(employee.firstName, \' \', employee.lastName) AS fullName,
            IDENTITY(employee.employeeTerminationRecord) AS terminationId,
            SUM(COALESCE(timesheetItem.duration, 0)) AS totalDuration'
        );

        $q->groupBy('employee.empNumber');

        return $this->getPaginator($q);
    }

    /**
     * @param ProjectActivityDetailedReportSearchFilterParams $projectActivityDetailedReportSearchFilterParams
     * @return QueryBuilderWrapper
     */
    private function getProjectActivityDetailedReportQueryBuilderWrapper(
        ProjectActivityDetailedReportSearchFilterParams $projectActivityDetailedReportSearchFilterParams
    ): QueryBuilderWrapper {
        $q = $this->createQueryBuilder(TimesheetItem::class, 'timesheetItem');
        $q->leftJoin('timesheetItem.projectActivity', 'projectActivity');
        $q->leftJoin('timesheetItem.employee', 'employee');
        $q->leftJoin('timesheetItem.timesheet', 'timesheet');
        $this->setSortingAndPaginationParams($q, $projectActivityDetailedReportSearchFilterParams);

        if (!is_null($projectActivityDetailedReportSearchFilterParams->getProjectId())) {
            $q->andWhere('timesheetItem.project = :projectId');
            $q->setParameter('projectId', $projectActivityDetailedReportSearchFilterParams->getProjectId());
        }

        if (!is_null($projectActivityDetailedReportSearchFilterParams->getProjectActivityId())) {
            $q->andWhere('timesheetItem.projectActivity = :projectActivityId');
            $q->setParameter(
                'projectActivityId',
                $projectActivityDetailedReportSearchFilterParams->getProjectActivityId()
            );
        }

        // Run time polymorphism (Upper casting) child as parent
        return $this->getCommonQueryBuilderWrapper($projectActivityDetailedReportSearchFilterParams, $q);
    }

    /**
     * @param ProjectActivityDetailedReportSearchFilterParams $projectActivityDetailedReportSearchFilterParams
     * @return int
     */
    public function getTotalDurationForProjectActivityDetailedReport(
        ProjectActivityDetailedReportSearchFilterParams $projectActivityDetailedReportSearchFilterParams
    ): int {
        $q = $this->getProjectActivityDetailedReportQueryBuilderWrapper(
            $projectActivityDetailedReportSearchFilterParams
        )->getQueryBuilder();
        $q->select('SUM(COALESCE(timesheetItem.duration, 0)) AS totalDuration');
        return $q->getQuery()->getSingleScalarResult() === null ? 0 : $q->getQuery()->getSingleScalarResult();
    }

    /**
     * @param int|null $projectAdminEmpNumber
     * @return int[]
     */
    public function getAccessibleEmpNumbersForProjectAdmin(?int $projectAdminEmpNumber): array
    {
        if (is_null($projectAdminEmpNumber)) {
            return [];
        }
        $q = $this->createQueryBuilder(ProjectAdmin::class, 'projectAdmin');
        $q->andWhere(
            $q->expr()->in(
                'projectAdmin.project',
                $this->createQueryBuilder(Project::class, 'project')
                    ->select('project.id')
                    ->innerJoin('project.projectAdmins', 'admin')
                    ->andWhere('admin.empNumber = :projectAdminEmpNumber')
                    ->andWhere('project.deleted = :deleted')
                    ->getDQL()
            )
        )
            ->setParameter('projectAdminEmpNumber', $projectAdminEmpNumber)
            ->setParameter('deleted', false)
            ->select('IDENTITY(projectAdmin.employee) AS empNumber')
            ->distinct();

        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'empNumber');
    }

    /**
     * @return int[]
     */
    public function getUnselectableProjectIds(): array
    {
        $qb = $this->createQueryBuilder(TimesheetItem::class, 'timesheetItem');
        $qb->leftJoin('timesheetItem.project', 'project');
        $qb->select('project.id');
        $qb->addGroupBy('project.id');
        $result = $qb->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }

    /**
     * @return int[]
     */
    public function getActivityIdsOfProjectInTimesheetItems($projectId): array
    {
        $qb = $this->createQueryBuilder(TimesheetItem::class, 'timesheetItem');
        $qb->leftJoin('timesheetItem.projectActivity', 'projectActivity');
        $qb->select('projectActivity.id');
        $qb->andWhere('timesheetItem.project = :projectId');
        $qb->setParameter('projectId', $projectId);
        $qb->addGroupBy('projectActivity.id');
        $result = $qb->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }
}
