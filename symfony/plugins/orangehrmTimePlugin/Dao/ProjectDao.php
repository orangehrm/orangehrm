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
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Project;
use OrangeHRM\Entity\ProjectAdmin;

class ProjectDao extends BaseDao
{
    /**
     * @param  Project  $project
     * @return Project
     */
    public function saveProject(Project $project): Project
    {
        $this->persist($project);
        return $project;
    }

    /**
     * @param  ProjectAdmin  $projectAdmin
     * @return ProjectAdmin
     */
    public function saveProjectAdmins(ProjectAdmin $projectAdmin): ProjectAdmin
    {
      $this->persist($projectAdmin);
        return $projectAdmin;
    }

    /**
     * @param  array  $ids
     * @return bool
     */
    public function deleteProjects(array $ids): bool
    {
        $q = $this->createQueryBuilder(Project::class, 'project');
        $q->update()
            ->set('project.isDeleted', ':isDeleted')
            ->setParameter('isDeleted', true)
            ->where($q->expr()->in('project.id', ':ids'))
            ->setParameter('ids', $ids);
        return $q->getQuery()->execute();
    }

    /**
     * @param  int  $id
     * @return Project|null
     */
    public function getProjectById(int $id):?Project
    {
        $project = $this->getRepository(Project::class)->find($id);
        if ($project instanceof Project) {
            return $project;
        }
        return null;
    }
}
