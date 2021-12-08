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
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Time\Dto\ProjectSearchFilterParams;

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
     * @param  int[]  $ids
     * @return int
     */
    public function deleteProjects(array $ids): int
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
    public function getProjectById(int $id): ?Project
    {
        $qb = $this->createQueryBuilder(Project::class, 'project');
        $qb->andWhere('project.id = :id')->setParameter('id', $id);
        $qb->andWhere('project.isDeleted = :isDeleted')->setParameter('isDeleted', false);
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param  ProjectSearchFilterParams  $projectSearchFilterParamHolder
     * @return Project[]
     */
    public function getProjects(ProjectSearchFilterParams $projectSearchFilterParamHolder): array
    {
        $qb = $this->getProjectsPaginator($projectSearchFilterParamHolder);
        return $qb->getQuery()->execute();
    }

    /**
     * @param  ProjectSearchFilterParams  $projectSearchFilterParamHolder
     * @return Paginator
     */
    protected function getProjectsPaginator(ProjectSearchFilterParams $projectSearchFilterParamHolder): Paginator
    {
        $qb = $this->createQueryBuilder(Project::class, 'project');
        $qb->leftJoin('project.customer', 'customer');
        $qb->leftJoin('project.projectAdmins', 'projectAdmin');

        $this->setSortingAndPaginationParams($qb, $projectSearchFilterParamHolder);

        if (!is_null($projectSearchFilterParamHolder->getProjectId())) {
            $qb->andWhere('project.id = :projectId')->setParameter(
                'projectId',
                $projectSearchFilterParamHolder->getProjectId()
            );
        }
        if (!is_null($projectSearchFilterParamHolder->getCustomerId())) {
            $qb->andWhere('customer.id = :customerId')->setParameter(
                'customerId',
                $projectSearchFilterParamHolder->getCustomerId()
            );
        }
        if (!is_null($projectSearchFilterParamHolder->getEmpNumber())) {
            $qb->andWhere('projectAdmin.empNumber = :empNumber')->setParameter(
                'empNumber',
                $projectSearchFilterParamHolder->getEmpNumber()
            );
        }
        $qb->andWhere('project.isDeleted = :isDeleted')->setParameter(
            'isDeleted',
            false
        );
        return $this->getPaginator($qb);
    }

    /**
     * @param  ProjectSearchFilterParams  $projectSearchFilterParamHolder
     * @return int
     */
    public function getProjectsCount(ProjectSearchFilterParams $projectSearchFilterParamHolder): int
    {
        return $this->getProjectsPaginator($projectSearchFilterParamHolder)->count();
    }

    /**
     * @param  string  $projectName
     * @param  int|null  $projectId
     * @return bool
     */
    public function isProjectNameTaken(string $projectName, ?int $projectId = null): bool
    {
        $q = $this->createQueryBuilder(Project::class, 'project');
        $q->andWhere('project.name = :projectName');
        $q->setParameter('projectName', $projectName);
        if (!is_null($projectId)) {
            $q->andWhere('project.id != :projectId');
            $q->setParameter('projectId', $projectId);
        }
        return $this->getPaginator($q)->count() === 0;
    }
}
