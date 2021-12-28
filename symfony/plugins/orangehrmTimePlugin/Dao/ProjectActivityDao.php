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
    public function isProjectActivityNameTaken(int $projectId, string $projectActivityName, ?int $projectActivityId = null): bool
    {
        $q = $this->createQueryBuilder(ProjectActivity::class, 'projectActivity');
        $q->andWhere('projectActivity.name = :projectActivityName');
        $q->andWhere('projectActivity.project = :projectId');
        $q->setParameter('projectActivityName', $projectActivityName);
        $q->setParameter('projectId', $projectId);
        if (!is_null($projectActivityId)) {
            $q->andWhere('projectActivity.id != :projectActivityId'); // we need to skip the current project activity Name on update, otherwise count always return 1
            $q->setParameter('projectActivityId', $projectActivityId);
        }
        return $this->getPaginator($q)->count() > 0;
    }

    /**
     * @param int $fromProjectId
     * @param int $toProjectId
     * @return ProjectActivity[]
     */
    public function getDuplicatedActivityIds(int $fromProjectId, int $toProjectId): array
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
            ->select('activity, COUNT(activity.id) AS HIDDEN counter');
        return $q->getQuery()->execute();
    }

    /**
     * @param int $toProjectId
     * @param array $fromProjectActivityIds
     * @return array
     */
    public function saveCopyActivity(int $toProjectId, array $fromProjectActivityIds): array
    {
        // this will get all activities which belongs to $fromProjectActivityIds
        $q = $this->createQueryBuilder(ProjectActivity::class, 'activity');
        $q->andWhere($q->expr()->in('activity.id', ':fromProjectActivityIds'))
            ->setParameter('fromProjectActivityIds', $fromProjectActivityIds);

        /** @var ProjectActivity[] $fromProjectActivities */
        $fromProjectActivities = $q->getQuery()->execute();

        foreach ($fromProjectActivities as $fromProjectActivity) {
            $toProjectActivity = new ProjectActivity();
            $toProjectActivity->getDecorator()->setProjectById($toProjectId);
            $toProjectActivity->setName($fromProjectActivity->getName());

            $this->getEntityManager()->persist($toProjectActivity);
        }
        $this->getEntityManager()->flush();
        return $fromProjectActivityIds;
    }
}
