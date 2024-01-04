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

namespace OrangeHRM\Time\Service;

use OrangeHRM\Entity\ProjectActivity;
use OrangeHRM\Time\Dao\ProjectActivityDao;
use OrangeHRM\Time\Dao\ProjectDao;
use OrangeHRM\Time\Exception\ProjectServiceException;

class ProjectService
{
    /**
     * @var ProjectDao|null
     */
    private ?ProjectDao $projectDao = null;

    /**
     * @var ProjectActivityDao|null
     */
    protected ?ProjectActivityDao $projectActivityDao = null;

    /**
     * @return ProjectDao
     */
    public function getProjectDao(): ProjectDao
    {
        if (is_null($this->projectDao)) {
            $this->projectDao = new ProjectDao();
        }
        return $this->projectDao;
    }

    /**
     * @return ProjectActivityDao
     */
    public function getProjectActivityDao(): ProjectActivityDao
    {
        if (!$this->projectActivityDao instanceof ProjectActivityDao) {
            $this->projectActivityDao = new ProjectActivityDao();
        }
        return $this->projectActivityDao;
    }

    /**
     * @param int $toProjectId
     * @param int $fromProjectId
     * @param int[] $fromProjectActivityIds
     * @return void
     * @throws ProjectServiceException
     */
    public function validateProjectActivityName(
        int $toProjectId,
        int $fromProjectId,
        array $fromProjectActivityIds
    ): void {
        $fromProjectActivities = $this->getProjectActivityDao()
            ->getProjectActivitiesByActivityIds($fromProjectActivityIds);

        $duplicatedActivities = $this->getProjectActivityDao()
            ->getDuplicatedActivities($fromProjectId, $toProjectId);

        $fetchedFromProjectActivityIds = array_map(
            function (ProjectActivity $projectActivity) {
                return $projectActivity->getId();
            },
            $fromProjectActivities
        );

        if (!empty(array_diff($fromProjectActivityIds, $fetchedFromProjectActivityIds))) {
            throw ProjectServiceException::projectActivityNotFound();
        }

        $duplicatedActivitiesMap = $this->getProjectActivityAsMap($duplicatedActivities);
        foreach ($fromProjectActivities as $fromProjectActivity) {
            if ($fromProjectActivity->getProject()->getId() !== $fromProjectId) {
                throw ProjectServiceException::projectActivityNotFound();
            }

            $name = $fromProjectActivity->getName();
            if (isset($duplicatedActivitiesMap[$name])) {
                throw ProjectServiceException::duplicateProjectActivityNameFound();
            }
        }
    }

    /**
     * @param ProjectActivity[] $projectActivities
     * @return array
     */
    public function getProjectActivityAsMap(array $projectActivities): array
    {
        $projectActivityList = [];
        foreach ($projectActivities as $value) {
            $projectActivityList[$value->getName()] = [
                "id" => $value->getId(),
                "name" => $value->getName(),
            ];
        }
        return $projectActivityList;
    }
}
