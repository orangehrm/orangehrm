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

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Project;
use OrangeHRM\Entity\ProjectActivity;
use OrangeHRM\Entity\TimesheetItem;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Time\Dto\ProjectActivitySearchFilterParams;
use OrangeHRM\Time\Exception\ProjectServiceException;

class ProjectActivityDao extends BaseDao
{
    /**
     * @param int $projectId
     * @param ProjectActivitySearchFilterParams $projectActivitySearchFilterParams
     * @return ProjectActivity[]
     */
    public function getProjectActivityListByProjectId(
        int $projectId,
        ProjectActivitySearchFilterParams $projectActivitySearchFilterParams
    ): array {
        $paginator = $this->getProjectActivitiesPaginator($projectId, $projectActivitySearchFilterParams);
        return $paginator->getQuery()->execute();
    }

    /**
     * @param int $projectId
     * @param ProjectActivitySearchFilterParams $projectActivitySearchFilterParams
     * @return Paginator
     */
    protected function getProjectActivitiesPaginator(
        int $projectId,
        ProjectActivitySearchFilterParams $projectActivitySearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(ProjectActivity::class, 'projectActivity');
        $q->andWhere('projectActivity.project = :projectId');
        $q->andWhere('projectActivity.deleted = :status');
        $q->setParameter('projectId', $projectId);

        if (!is_null($projectActivitySearchFilterParams->getProjectActivityName())) {
            $q->andWhere($q->expr()->like('projectActivity.name', ':projectActivityName'))
                ->setParameter(
                    'projectActivityName',
                    '%' . $projectActivitySearchFilterParams->getProjectActivityName() . '%'
                );
        }

        $q->setParameter('status', false);
        $this->setSortingAndPaginationParams($q, $projectActivitySearchFilterParams);
        return $this->getPaginator($q);
    }

    /**
     * @param int $projectId
     * @param ProjectActivitySearchFilterParams $projectActivitySearchFilterParams
     * @return int
     */
    public function getProjectActivityCount(
        int $projectId,
        ProjectActivitySearchFilterParams $projectActivitySearchFilterParams
    ): int {
        $paginator = $this->getProjectActivitiesPaginator($projectId, $projectActivitySearchFilterParams);
        return $paginator->count();
    }

    /**
     * @param ProjectActivity $projectActivity
     * @return ProjectActivity
     */
    public function saveProjectActivity(ProjectActivity $projectActivity): ProjectActivity
    {
        $this->persist($projectActivity);
        return $projectActivity;
    }

    /**
     * @param int $projectId
     * @return Project|null
     */
    public function getProjectById(int $projectId): ?Project
    {
        $project = $this->getRepository(Project::class)->find($projectId);
        return ($project instanceof Project) ? $project : null;
    }

    /**
     * @param int $projectId
     * @param int $projectActivityId
     * @return ProjectActivity|null
     */
    public function getProjectActivityByProjectIdAndProjectActivityId(
        int $projectId,
        int $projectActivityId
    ): ?ProjectActivity {
        $project = $this->getProjectById($projectId);
        $projectActivity = $this->getRepository(ProjectActivity::class)->findOneBy(
            ['id' => $projectActivityId, 'project' => $project]
        );
        return ($projectActivity instanceof ProjectActivity) ? $projectActivity : null;
    }

    /**
     * @param int[] $ids
     * @param int $projectId
     * @return int[]
     */
    public function getExistingProjectActivityIdsForProject(array $ids, int $projectId): array
    {
        $qb = $this->createQueryBuilder(ProjectActivity::class, 'projectActivity');

        $qb->select('projectActivity.id')
            ->andWhere($qb->expr()->in('projectActivity.id', ':ids'))
            ->andWhere($qb->expr()->eq('projectActivity.project', ':projectId'))
            ->andWhere($qb->expr()->eq('projectActivity.deleted', ':deleted'))
            ->setParameter('ids', $ids)
            ->setParameter('projectId', $projectId)
            ->setParameter('deleted', false);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param int $activityId
     * @return bool
     */
    public function hasActivityGotTimesheetItems(int $activityId): bool
    {
        $q = $this->createQueryBuilder(TimesheetItem::class, 'timesheetItem');
        $q->andWhere('timesheetItem.projectActivity = :projectActivityId');
        $q->setParameter('projectActivityId', $activityId);
        $count = $this->getPaginator($q)->count();
        return ($count > 0);
    }

    /**
     * @param int[] $toBeDeletedActivityIds
     * @return int
     * @throws ProjectServiceException
     */
    public function deleteProjectActivities(array $toBeDeletedActivityIds): int
    {
        foreach ($toBeDeletedActivityIds as $toBeDeletedActivityId) {
            if ($this->hasActivityGotTimesheetItems($toBeDeletedActivityId)) {
                throw ProjectServiceException::projectActivityExist();
            }
        }

        $q = $this->createQueryBuilder(ProjectActivity::class, 'projectActivity');
        $q->update()
            ->set('projectActivity.deleted', ':deleted')
            ->setParameter('deleted', true)
            ->where($q->expr()->in('projectActivity.id', ':ids'))
            ->setParameter('ids', $toBeDeletedActivityIds);
        return $q->getQuery()->execute();
    }

    /**
     **this function for validating the project activity name availability. ( true -> project activity name already exist, false - project activity name is not exist )
     * @param int $projectId
     * @param string $projectActivityName
     * @param int|null $projectActivityId
     * @return bool
     */
    public function isProjectActivityNameTaken(
        int $projectId,
        string $projectActivityName,
        ?int $projectActivityId = null
    ): bool {
        $q = $this->createQueryBuilder(ProjectActivity::class, 'projectActivity');
        $q->andWhere('projectActivity.name = :projectActivityName');
        $q->andWhere('projectActivity.project = :projectId');
        $q->setParameter('projectActivityName', $projectActivityName);
        $q->setParameter('projectId', $projectId);
        if (!is_null($projectActivityId)) {
            // we need to skip the current project activity Name on update, otherwise count always return 1
            $q->andWhere('projectActivity.id != :projectActivityId');
            $q->setParameter('projectActivityId', $projectActivityId);
        }
        return $this->getPaginator($q)->count() > 0;
    }

    /**
     * @param int $fromProjectId
     * @param int $toProjectId
     * @return ProjectActivity[]
     */
    public function getDuplicatedActivities(int $fromProjectId, int $toProjectId): array
    {
        $q = $this->createQueryBuilder(ProjectActivity::class, 'activity');
        $q->andWhere(
            $q->expr()->orX(
                $q->expr()->eq('activity.project', ':fromProjectId'),
                $q->expr()->eq('activity.project', ':toProjectId')
            )
        )
            ->setParameter('fromProjectId', $fromProjectId)
            ->setParameter('toProjectId', $toProjectId)
            ->andWhere('activity.deleted = :deleted')
            ->setParameter('deleted', false);
        $q->groupBy('activity.name')
            ->having('counter >= 2')
            ->select('activity.name, COUNT(activity.name) AS HIDDEN counter');

        $duplicatedActivityNames = array_column($q->getQuery()->execute(), 'name');

        return $this->createQueryBuilder(ProjectActivity::class, 'activity')
            ->andWhere($q->expr()->in('activity.name', ':duplicatedActivities'))
            ->andWhere($q->expr()->eq('activity.project', ':projectId'))
            ->setParameter('duplicatedActivities', $duplicatedActivityNames)
            ->setParameter('projectId', $fromProjectId)
            ->getQuery()->execute();
    }

    /**
     * @param int[] $projectActivityIds
     * @return ProjectActivity[]
     */
    public function getProjectActivitiesByActivityIds(array $projectActivityIds): array
    {
        // this will get all activities which belongs to $fromProjectActivityIds
        $q = $this->createQueryBuilder(ProjectActivity::class, 'activity');
        $q->andWhere($q->expr()->in('activity.id', ':projectActivityIds'))
            ->setParameter('projectActivityIds', $projectActivityIds);

        return $q->getQuery()->execute();
    }

    /**
     * @param int $toProjectId
     * @param array $fromProjectActivityIds
     * @return void
     */
    public function copyActivities(int $toProjectId, array $fromProjectActivityIds): void
    {
        $fromProjectActivities = $this->getProjectActivitiesByActivityIds($fromProjectActivityIds);
        foreach ($fromProjectActivities as $fromProjectActivity) {
            $toProjectActivity = new ProjectActivity();
            $toProjectActivity->getDecorator()->setProjectById($toProjectId);
            $toProjectActivity->setName($fromProjectActivity->getName());

            $this->getEntityManager()->persist($toProjectActivity);
        }
        $this->getEntityManager()->flush();
    }

    /**
     * @param array $projectActivityIdPairs e.g. [[1, 2], [1, 3], [activityId, projectId]]
     * @return int
     */
    public function getActivitiesCountByProjectActivityIdPairs(array $projectActivityIdPairs): int
    {
        $paginator = $this->getActivitiesPaginatorByProjectActivityIdPairs($projectActivityIdPairs);
        return is_null($paginator) ? 0 : $paginator->count();
    }

    /**
     * @param array $projectActivityIdPairs
     * @return Paginator|null
     */
    private function getActivitiesPaginatorByProjectActivityIdPairs(array $projectActivityIdPairs): ?Paginator
    {
        if (empty($projectActivityIdPairs)) {
            return null;
        }
        $qb = $this->createQueryBuilder(ProjectActivity::class, 'activity')
            ->leftJoin('activity.project', 'project');
        foreach ($projectActivityIdPairs as $i => $projectActivityIdPair) {
            $qb->orWhere(
                $qb->expr()->andX(
                    $qb->expr()->eq('activity.id', ":activityId_$i"),
                    $qb->expr()->eq('activity.project', ":projectId_$i"),
                    $qb->expr()->eq('activity.deleted', ":deleted_$i"),
                    $qb->expr()->eq('project.deleted', ":deleted_$i")
                )
            )->setParameter("activityId_$i", $projectActivityIdPair[0])
                ->setParameter("projectId_$i", $projectActivityIdPair[1])
                ->setParameter("deleted_$i", false);
        }
        return $this->getPaginator($qb);
    }
}
