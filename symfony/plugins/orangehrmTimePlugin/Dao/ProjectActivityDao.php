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
use OrangeHRM\Entity\TimeSheetItem;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Time\Dto\ProjectActivitySearchFilterParams;
use OrangeHRM\Time\Exception\ProjectServiceException;

class ProjectActivityDao extends BaseDao
{
    /**
     * @param int $projectId
     * @param ProjectActivitySearchFilterParams $projectActivitySearchFilterParams
     * @return Paginator
     */
    public function getProjectActivitiesByProjectId(
        int $projectId,
        ProjectActivitySearchFilterParams $projectActivitySearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(ProjectActivity::class, 'projectActivity');
        $q->andWhere('projectActivity.project = :projectId');
        $q->andWhere('projectActivity.deleted = :status');
        $q->setParameter('projectId', $projectId);
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
        $paginator = $this->getProjectActivitiesByProjectId($projectId, $projectActivitySearchFilterParams);
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
            ['activityId' => $projectActivityId, 'project' => $project]
        );
        return ($projectActivity instanceof ProjectActivity) ? $projectActivity : null;
    }

    /**
     * @param int $activityId
     * @return bool
     */
    public function hasActivityGotTimesheetItems(int $activityId): bool
    {
        $q = $this->createQueryBuilder(TimeSheetItem::class, 'timeSheetItem');
        $q->andWhere('timeSheetItem.projectActivity = :projectId');
        $q->setParameter('projectId', $activityId);
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
            ->where($q->expr()->in('projectActivity.activityId', ':ids'))
            ->setParameter('ids', $toBeDeletedActivityIds);
        return $q->getQuery()->execute();
    }
}
